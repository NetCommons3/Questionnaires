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
		'Security',
		'NetCommons.NetCommonsBlock', //Use Questionnaire model
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole',
		'Questionnaires.Questionnaires',
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'NetCommons.BackToPage',
		'NetCommons.Token'
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('result');
	}

/**
 * result method
 *
 * @param int $frameId フレームID
 * @param int $questionnaireId アンケートID
 * @throws NotFoundException
 * @throws ForbiddenException
 * @return void
 */
	public function result($frameId = 0, $questionnaireId = 0) {
		// get conditions for finding specified Questionnaire
		$conditions = $this->__getConditionForResult(array('origin_id' => $questionnaireId));

		// get the specified questionnaire
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => $conditions
		));
		if (!$questionnaire) {
			throw new NotFoundException(__d('questionnaires', 'Invalid questionnaire'));
		}

		$questions = array();
		foreach ($questionnaire['QuestionnairePage'] as $page) {	//このアンケートのページ毎
			foreach ($page['QuestionnaireQuestion'] as $q) {		//このページ中の質問毎
				//各質問＋選択子($q)情報を、 $questions[(questionnaire_question_id値)]に格納していく。
				$questions[$q['origin_id']] = $q;
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
		$this->__aggrigateAnswer($questionnaire, $this->viewVars['contentEditable'], $questions);

		//ニックネームとuser_idを取り出す.　QuestinnaireControllerに合わせる。
		$username = CakeSession::read('Auth.User.username');
		$username = empty($username) ? __d('questionnaires', 'annonymous person') : $username;
		//$user_id = CakeSession::read('Auth.User.id');
		//$user_id = empty($user_id) ? null : $user_id;

		//画面用データをセットする。
		$this->set('frameId', $frameId);								//ng-initのinitilize()引数用
		$this->set('questionnaire', array('name' => $username));	//ng-initのinitilize()引数用
		$this->set('questionnaireId', $questionnaireId);
		$this->set('questionnaire', $questionnaire);
		$this->set('questions', $questions);

		//集計結果を、Viewに渡し、表示してもらう。
		//$this->view = 'QuestionnaireQuestions/total';
	}

/**
 * get questionnaire for result display sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	private function __getConditionForResult($addConditions = array()) {
		$conditions = array(
			'block_id' => $this->viewVars['blockId'],
			'is_total_show' => QuestionnairesComponent::EXPRESSION_SHOW,

		);
		if (!$this->viewVars['contentEditable']) {
			$conditions['is_active'] = true;
			$conditions['OR'] = array(
				'total_show_start_period <' => $this->_getNowTime(),
			);
		} else {
			$conditions['is_latest'] = true;
		}
		if ($this->viewVars['roomRoleKey'] == NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY) {
			$conditions['is_no_member_allow'] = QuestionnairesComponent::PERMISSION_PERMIT;
		}
		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * __aggrigateAnswer
 * 集計処理の実施
 *
 * @param array $questionnaire アンケート情報
 * @param bool $contentEditable 編集可能フラグ
 * @param array &$questions アンケート質問(集計結果を配列追加して返します)
 * @return void
 */
	private function __aggrigateAnswer($questionnaire, $contentEditable, &$questions) {
		//公開時は本番時回答、テスト時(=非公開時)はテスト時回答を対象とする。
		if ($this->_isDuringTest($questionnaire)) {
			$testStatus = QuestionnairesComponent::TEST_ANSWER_STATUS_TEST;
		} else {
			$testStatus = QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM;
		}

		$baseConditions = array(
			'QuestionnaireAnswerSummary.answer_status' => QuestionnairesComponent::ACTION_ACT,
			'QuestionnaireAnswerSummary.test_status' => $testStatus,
			'QuestionnaireAnswerSummary.questionnaire_origin_id' => $questionnaire['Questionnaire']['origin_id']
		);

		//質問毎に、まとめあげる.
		//$questionsは、questionnaire_question_origin_idをキーとし、questionnaire_question配下が代入されている。
		//
		foreach ($questions as &$question) {
			if ($question['is_result_display'] != QuestionnairesComponent::EXPRESSION_SHOW) {
				//集計表示をしない、なので飛ばす
				continue;
			}
			// 戻り値の、この質問の合計回答数を記録しておく。
			// skip ロジックがあるため、単純にsummaryのcountじゃない..
			$questionConditions = $baseConditions + array(
				'QuestionnaireAnswer.questionnaire_question_origin_id' => $question['origin_id'],
			);
			$question['answer_total_cnt'] = $this->QuestionnaireAnswer->getAnswerCount($questionConditions);

			if ($question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST ||
				$question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_MULTIPLE) {
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
							'QuestionnaireAnswer.matrix_choice_id' => $c['origin_id'],
							'QuestionnaireAnswer.answer_value LIKE ' => '%' . QuestionnairesComponent::ANSWER_DELIMITER . $col['origin_id'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . '%',
						);
					$cnt = $this->QuestionnaireAnswer->getAnswerCount($conditions);
					$c['aggrigate_total'][$col['origin_id']] = $cnt;
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
					'QuestionnaireAnswer.answer_value LIKE ' => '%' . QuestionnairesComponent::ANSWER_DELIMITER . $c['origin_id'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . '%',
				);
			$cnt = $this->QuestionnaireAnswer->getAnswerCount($conditions);
			$c['aggrigate_total']['aggrigate_not_matrix'] = $cnt;
		}
	}

}