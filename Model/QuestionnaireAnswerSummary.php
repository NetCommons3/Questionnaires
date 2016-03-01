<?php
/**
 * QuestionnaireAnswerSummary Model
 *
 * @property Questionnaire $Questionnaire
 * @property QuestionnaireAnswer $QuestionnaireAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

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
 * saveAnswerStatus
 * 回答状態を書き換える
 *
 * @param array $summary summary data
 * @param int $status status
 * @throws InternalErrorException
 * @return bool
 */
	public function saveAnswerStatus($summary, $status) {
		$summary['QuestionnaireAnswerSummary']['answer_status'] = $status;
		$summary['QuestionnaireAnswerSummary']['answer_time'] = 1;

		if ($status == QuestionnairesComponent::ACTION_ACT) {
			// サマリの状態を完了にして確定する
			$summary['QuestionnaireAnswerSummary']['answer_time'] = (new NetCommonsTime())->getNowDatetime();
		}
		$this->set($summary);
		if (! $this->validates()) {
			return false;
		}
		$this->begin();
		try {
			if (! $this->save($summary, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}
/**
 * forceGetProgressiveAnswerSummary
 * get answer summary record if there is no summary , then create
 *
 * @param array $questionnaire questionnaire
 * @param int $userId user id
 * @param string $sessionId session id
 * @throws $ex
 * @return array summary
 */
	public function forceGetProgressiveAnswerSummary($questionnaire, $userId, $sessionId) {
		$this->begin();
		try {
			$answerTime = 1;
			if ($userId) {
				$maxTime = $this->find('first', array(
					'fields' => array('MAX(answer_time) AS max_answer_time'),
					'conditions' => array(
						'questionnaire_key' => $questionnaire['Questionnaire']['key'],
						'user_id' => $userId
					)
				));
				if ($maxTime) {
					$answerTime = $maxTime[0]['max_answer_time'] + 1;
				}
			}
			$this->create();
			if (! $this->save(array(
				'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
				'test_status' => ($questionnaire['Questionnaire']['status'] != WorkflowComponent::STATUS_PUBLISHED) ? QuestionnairesComponent::TEST_ANSWER_STATUS_TEST : QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => $answerTime,
				'questionnaire_key' => $questionnaire['Questionnaire']['key'],
				'session_value' => $sessionId,
				'user_id' => $userId,
			))) {
				$this->rollback();
				return false;
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		$summary = $this->findById($this->id);
		return $summary;
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
			if (QuestionnairesComponent::isOnlyInputType($question['question_type'])) {
				continue;
			}
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
