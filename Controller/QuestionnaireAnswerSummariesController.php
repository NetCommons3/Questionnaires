<?php
/**
 * QuestionnaireAnswerSummaries Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * QuestionnaireAnswerSummariesController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireAnswerSummariesController extends QuestionnairesAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Questionnaires.Questionnaire',
		'Questionnaires.QuestionnairePage',
		'Questionnaires.QuestionnaireQuestion',
		'Questionnaires.QuestionnaireChoice',
		'Questionnaires.QuestionnaireAnswerSummary',
		'Questionnaires.QuestionnaireAnswer',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission',
		'Questionnaires.Questionnaires',
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'Workflow.Workflow',
	);

/**
 * target questionnaire data
 *
 */
	private $__questionnaire = null;

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		// ゲストアクセスOKのアクションを設定
		$this->Auth->allow('view');

		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		if (isset($this->params['pass'][QuestionnairesComponent::QUESTIONNAIRE_KEY_PASS_INDEX])) {
			$questionnaireKey = $this->params['pass'][QuestionnairesComponent::QUESTIONNAIRE_KEY_PASS_INDEX];
		} else {
			$this->setAction('throwBadRequest');
			return;
		}
		// キーで指定されたアンケートデータを取り出しておく
		$conditions = $this->Questionnaire->getBaseCondition(
			array('Questionnaire.key' => $questionnaireKey)
		);

		$this->__questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => $conditions,
		));
		if (! $this->__questionnaire) {
			$this->setAction('throwBadRequest');
			return;
		}
	}

/**
 * result method
 *
 * @throws ForbiddenException
 * @return void
 */
	public function view() {
		$questionnaire = $this->__questionnaire;

		$questions = array();
		foreach ($questionnaire['QuestionnairePage'] as $page) {	//このアンケートのページ毎
			foreach ($page['QuestionnaireQuestion'] as $q) {		//このページ中の質問毎
				//各質問＋選択子($q)情報を、 $questions[(questionnaire_key値)]に格納していく。
				$questions[$q['key']] = $q;
			}
		}

		$userId = $this->Auth->user('id');	//ユーザidを取り出す。

		//集計表示していいかどうかの判断

		if (!$this->QuestionnaireAnswerSummary->isAbleToDisplayAggrigatedData(
			$questionnaire,
			$userId,
			$this->Session->id())) {
			throw new ForbiddenException(__d('net_commons', 'Permission denied'));
		}
		//集計処理を行います。
		$this->__aggrigateAnswer($questionnaire, $questions);

		//ニックネームとuser_idを取り出す.　QuestionnaireControllerに合わせる。
		$username = CakeSession::read('Auth.User.username');
		$username = empty($username) ? __d('questionnaires', 'annonymous person') : $username;
		//$user_id = CakeSession::read('Auth.User.id');
		//$user_id = empty($user_id) ? null : $user_id;

		//画面用データをセットする。
		$this->set('questionnaireId', $this->_getQuestionnaireKey($this->__questionnaire));
		$this->set('questionnaire', $questionnaire);
		$this->set('questions', $questions);
		$this->set('jsQuestionnaire', $this->camelizeKeyRecursive($questionnaire));
		$this->set('jsQuestions', $this->camelizeKeyRecursive($questions));

		//集計結果を、Viewに渡し、表示してもらう。
		//$this->view = 'QuestionnaireQuestions/total';
	}

/**
 * __aggrigateAnswer
 * 集計処理の実施
 *
 * @param array $questionnaire アンケート情報
 * @param array &$questions アンケート質問(集計結果を配列追加して返します)
 * @return void
 */
	private function __aggrigateAnswer($questionnaire, &$questions) {
		// 集計データを集める際の基本条件
		$baseConditions = $this->QuestionnaireAnswerSummary->getResultCondition($questionnaire);

		//質問毎に、まとめあげる.
		//$questionsは、questionnaire_question_keyをキーとし、questionnaire_question配下が代入されている。
		//
		foreach ($questions as &$question) {
			if ($question['is_result_display'] != QuestionnairesComponent::EXPRESSION_SHOW) {
				//集計表示をしない、なので飛ばす
				continue;
			}
			// 戻り値の、この質問の合計回答数を記録しておく。
			// skip ロジックがあるため、単純にsummaryのcountじゃない..
			$questionConditions = $baseConditions + array(
				'QuestionnaireAnswer.questionnaire_question_key' => $question['key'],
			);
			$question['answer_total_cnt'] = $this->QuestionnaireAnswer->getAnswerCount($questionConditions);

			if (QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
				$this->__aggrigateAnswerForMatrix($question, $questionConditions);
			} else {
				$this->__aggrigateAnswerForNotMatrix($question, $questionConditions);
			}
		}
	}

/**
 * __aggrigateAnswerForMatrix
 * matrix aggrigate
 *
 * @param array &$question アンケート質問(集計結果を配列追加して返します)
 * @param array $questionConditions get aggrigate base condition
 * @return void
 */
	private function __aggrigateAnswerForMatrix(&$question, $questionConditions) {
		$rowCnt = 0;
		$cols = Hash::extract($question['QuestionnaireChoice'], '{n}[matrix_type=' . QuestionnairesComponent::MATRIX_TYPE_COLUMN . ']');
		foreach ($question['QuestionnaireChoice'] as &$c) {
			if ($c['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
				foreach ($cols as $col) {
					$conditions = $questionConditions + array(
							'QuestionnaireAnswer.matrix_choice_key' => $c['key'],
							'QuestionnaireAnswer.answer_value LIKE ' => '%' . QuestionnairesComponent::ANSWER_DELIMITER . $col['key'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . '%',
						);
					$cnt = $this->QuestionnaireAnswer->getAnswerCount($conditions);
					$c['aggrigate_total'][$col['key']] = $cnt;
				}
				$rowCnt++;
			}
		}
		$question['answer_total_cnt'] /= $rowCnt;
	}

/**
 * __aggrigateAnswerForNotMatrix
 * not matrix aggrigate
 *
 * @param array &$question アンケート質問(集計結果を配列追加して返します)
 * @param array $questionConditions get aggrigate base condition
 * @return void
 */
	private function __aggrigateAnswerForNotMatrix(&$question, $questionConditions) {
		foreach ($question['QuestionnaireChoice'] as &$c) {
			$conditions = $questionConditions + array(
					'QuestionnaireAnswer.answer_value LIKE ' => '%' . QuestionnairesComponent::ANSWER_DELIMITER . $c['key'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . '%',
				);
			$cnt = $this->QuestionnaireAnswer->getAnswerCount($conditions);
			$c['aggrigate_total']['aggrigate_not_matrix'] = $cnt;
		}
	}

}