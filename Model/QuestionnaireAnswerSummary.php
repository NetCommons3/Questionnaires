<?php
/**
 * QuestionnaireAnswerSummary Model
 *
 * @property Questionnaire $Questionnaire
 * @property User $User
 * @property QuestionnaireAnswer $QuestionnaireAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireAnswerSummary Model
 */
class QuestionnaireAnswerSummary extends QuestionnairesAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'questionnaire_key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Questionnaire' => array(
			'className' => 'Questionnaires.Questionnaire',
			'foreignKey' => 'questionnaire_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuestionnaireAnswer' => array(
			'className' => 'Questionnaires.QuestionnaireAnswer',
			'foreignKey' => 'questionnaire_answer_summary_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

/**
 * getNowSummaryOfThisUser 指定されたアンケートIDと指定ユーザーに合致するアンケート回答を取得する
 *
 * @param int $questionnaireKey アンケートKey
 * @param int $userId ユーザID （指定しない場合は null)
 * @param string $sessionId セッションID
 * @return array
 */
	public function getNowSummaryOfThisUser($questionnaireKey, $userId, $sessionId) {
		if ($userId) {
			$conditions = array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'questionnaire_key' => $questionnaireKey,
				'user_id' => $userId
			);
		} else {
			$conditions = array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'questionnaire_key' => $questionnaireKey,
				'session_value' => $sessionId
			);
		}

		$summary = $this->find('all', array(
			'conditions' => $conditions
		));

		return $summary;
	}

/**
 * getProgressiveSummaryOfThisUser 指定されたアンケートIDと指定ユーザーに合致する現在回答中のアンケート回答を取得する
 *
 * @param int $questionnaireKey アンケートKey
 * @param int $userId ユーザID （指定しない場合は null)
 * @param string $sessionId セッションID
 * @return array
 */
	public function getProgressiveSummaryOfThisUser($questionnaireKey, $userId, $sessionId) {
		$conditions = array(
			'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
			'questionnaire_key' => $questionnaireKey,
			'user_id' => $userId,
			'session_value' => $sessionId);

		$summary = $this->find('first', array(
			'conditions' => $conditions,
		));
		return $summary;
	}
/**
 * forceGetProgressiveAnswerSummary
 * get answer summary record if there is no summary , then create
 *
 * @param array $questionnaire questionnaire
 * @param int $userId user id
 * @param string $sessionId session id
 * @return array
 */
	public function forceGetProgressiveAnswerSummary($questionnaire, $userId, $sessionId) {
		$summary = $this->getProgressiveSummaryOfThisUser($questionnaire['Questionnaire']['key'], $userId, $sessionId);
		// なければ作成
		if (!$summary) {
			$this->create();
			$this->save(array(
				'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
				'test_status' => ($questionnaire['Questionnaire']['status'] != WorkflowComponent::STATUS_PUBLISHED) ? QuestionnairesComponent::TEST_ANSWER_STATUS_TEST : QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => 1,
				'questionnaire_key' => $questionnaire['Questionnaire']['key'],
				'session_value' => $sessionId,
				'user_id' => $userId,
			));
			$summary = array();
			$summary['QuestionnaireAnswerSummary']['id'] = $this->id;
		}
		return $summary;
	}

/**
 * isAbleToDisplayAggrigatedData 指定されたIDを集計表示していいいかどうか？
 *
 * @param int $questionnaire Questionnaire
 * @param int $userId user id
 * @param string $sessionId session id
 * @return bool
 */
	public function isAbleToDisplayAggrigatedData($questionnaire, $userId, $sessionId) {
		if ($questionnaire['Questionnaire']['total_show_timing'] != QuestionnairesComponent::USES_USE) {
			//集計結果を表示するタイミング＝アンケート回答後、すぐ。
			//
			//つまり、本人回答があるかどうがか表示有無の判断基準

			$summaries = $this->getNowSummaryOfThisUser($questionnaire['Questionnaire']['key'], $userId, $sessionId);
			if (count($summaries) > 0) {
				//本人による「回答」データあり
				return true;
			} else {
				//本人による「回答」データなし
				return false;
			}
		}

		//表示期間内
		return true;
	}

/**
 * getResultCondition
 *
 * @param int $questionnaire Questionnaire
 * @return array
 */
	public function getResultCondition($questionnaire) {
		// 指定されたアンケートを集計するときのサマリ側の条件を返す
		$baseConditions = array(
			'QuestionnaireAnswerSummary.answer_status' => QuestionnairesComponent::ACTION_ACT,
			'QuestionnaireAnswerSummary.questionnaire_key' => $questionnaire['Questionnaire']['key']
		);
		//公開時は本番時回答のみ、テスト時(=非公開時)は本番回答＋テスト回答を対象とする。
		if ($questionnaire['Questionnaire']['status'] == WorkflowComponent::STATUS_PUBLISHED) {
			$baseConditions['QuestionnaireAnswerSummary.test_status'] = QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM;
		}
		return $baseConditions;
	}

/**
 * getAggrigates
 * 集計処理の実施
 *
 * @param array $questionnaire アンケート情報
 * @return void
 */
	public function getAggregate($questionnaire) {
		$this->QuestionnaireAnswer = ClassRegistry::init('Questionnaires.QuestionnaireAnswer', true);
		// 質問データのとりまとめ
		//$questionsは、questionnaire_question_keyをキーとし、questionnaire_question配下が代入されている。
		$questions = Hash::combine($questionnaire,
			'QuestionnairePage.{n}.QuestionnaireQuestion.{n}.key',
			'QuestionnairePage.{n}.QuestionnaireQuestion.{n}');

		// 集計データを集める際の基本条件
		$baseConditions = $this->getResultCondition($questionnaire);

		//質問毎に集計
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
				$this->__aggregateAnswerForMatrix($question, $questionConditions);
			} else {
				$this->__aggregateAnswerForNotMatrix($question, $questionConditions);
			}
		}
		return $questions;
	}

/**
 * __aggregateAnswerForMatrix
 * matrix aggregate
 *
 * @param array &$question アンケート質問(集計結果を配列追加して返します)
 * @param array $questionConditions get aggregate base condition
 * @return void
 */
	private function __aggregateAnswerForMatrix(&$question, $questionConditions) {
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
					$c['aggregate_total'][$col['key']] = $cnt;
				}
				$rowCnt++;
			}
		}
		$question['answer_total_cnt'] /= $rowCnt;
	}

/**
 * __aggregateAnswerForNotMatrix
 * not matrix aggregate
 *
 * @param array &$question アンケート質問(集計結果を配列追加して返します)
 * @param array $questionConditions get aggregate base condition
 * @return void
 */
	private function __aggregateAnswerForNotMatrix(&$question, $questionConditions) {
		foreach ($question['QuestionnaireChoice'] as &$c) {
			$conditions = $questionConditions + array(
					'QuestionnaireAnswer.answer_value LIKE ' => '%' . QuestionnairesComponent::ANSWER_DELIMITER . $c['key'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . '%',
				);
			$cnt = $this->QuestionnaireAnswer->getAnswerCount($conditions);
			$c['aggregate_total']['aggregate_not_matrix'] = $cnt;
		}
	}

/**
 * deleteTestAnswerSummary
 * when questionnaire is published, delete test answer summary
 *
 * @param int $key questionnaire key
 * @param int $status publish status
 * @return bool
 */
	public function deleteTestAnswerSummary($key, $status) {
		if ($status != WorkflowComponent::STATUS_PUBLISHED) {
			return true;
		}
		$this->deleteAll(array(
			'questionnaire_key' => $key,
			'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_TEST), true);
		return true;
	}

}
