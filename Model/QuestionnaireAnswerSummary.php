<?php
/**
 * QuestionnaireAnswerSummary Model
 *
 * @property Questionnaire $Questionnaire
 * @property QuestionnaireAnswer $QuestionnaireAnswer
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
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
		'questionnaire_id' => array(
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
			'foreignKey' => 'questionnaire_id',
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

	public function getNowSummaryOfThisUser($questionnaireId, $userId, $sessionId) {

		if ($userId) {
			$conditions = array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'questionnaire_id' => $questionnaireId,
				'user_id' => $userId
			);
		}
		else {
			$conditions = array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'questionnaire_id' => $questionnaireId,
				'session_value' => $sessionId
			);
		}

		$summary = $this->find('all', array(
			'conditions' => $conditions
		));

		return $summary;
	}
	public function getProgressiveSummaryOfThisUser($questionnaireId, $userId, $sessionId) {

		$conditions = array(
			'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
			'questionnaire_id' => $questionnaireId,
			'user_id' => $userId,
			'session_value' => $sessionId);

		$summary = $this->find('all', array(
			'conditions' => $conditions
		));

		return $summary;
	}
}
