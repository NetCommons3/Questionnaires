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
 * Use database config
 *
 * @var string
 */
	public $useDbConfig = 'master';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'questionnaire_origin_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
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
			'className' => 'Questionnaire',
			'foreignKey' => 'questionnaire_origin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
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
			'className' => 'QuestionnaireAnswer',
			'foreignKey' => 'questionnaire_answer_summary_id',
			'dependent' => false,
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
 * @param int $questionnaireId アンケートID
 * @param int $userId ユーザID （指定しない場合は null)
 * @param string $sessionId セッションID
 * @return array
 */
	public function getNowSummaryOfThisUser($questionnaireId, $userId, $sessionId) {
		if ($userId) {
			$conditions = array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'questionnaire_origin_id' => $questionnaireId,
				'user_id' => $userId
			);
		} else {
			$conditions = array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'questionnaire_origin_id' => $questionnaireId,
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
 * @param int $questionnaireId アンケートID
 * @param int $userId ユーザID （指定しない場合は null)
 * @param string $sessionId セッションID
 * @return array
 */
	public function getProgressiveSummaryOfThisUser($questionnaireId, $userId, $sessionId) {
		$conditions = array(
			'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
			'questionnaire_origin_id' => $questionnaireId,
			'user_id' => $userId,
			'session_value' => $sessionId);

		$summary = $this->find('all', array(
			'conditions' => $conditions
		));

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

			$summaries = $this->getNowSummaryOfThisUser($questionnaire['Questionnaire']['origin_id'], $userId, $sessionId);
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
 * forceGetAnswerSummary
 * get answer summary record if there is no summary , then create
 *
 * @param array $questionnaire questionnaire
 * @param int $userId user id
 * @param string $sessionId session id
 * @param array $conditions conditions
 * @return array
 */
	public function forceGetAnswerSummary($questionnaire, $userId, $sessionId, $conditions) {
		$summary = $this->find('first', array(
			'conditions' => $conditions
		));
		// なければ作成
		if (!$summary) {
			$summary = $this->create();
			$this->save(array(
				'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
				'test_status' => ($questionnaire['Questionnaire']['status'] != NetCommonsBlockComponent::STATUS_PUBLISHED) ? QuestionnairesComponent::TEST_ANSWER_STATUS_TEST : QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => 1,
				'questionnaire_origin_id' => $questionnaire['Questionnaire']['origin_id'],
				'session_value' => $sessionId,
				'user_id' => $userId,
			));
			$summary = array();
			$summary['QuestionnaireAnswerSummary']['id'] = $this->id;
		}
		return $summary;
	}
}
