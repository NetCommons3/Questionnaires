<?php
/**
 * QuestionnaireQuestions Model
 *
 * @property QuestionnairePage $QuestionnairePage
 * @property QuestionnaireAnswer $QuestionnaireAnswer
 * @property QuestionnaireChoice $QuestionnaireChoice
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireQuestions Model
 */
class QuestionnaireQuestion extends QuestionnairesAppModel {

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
		'questionnaire_page_id' => array(
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
		'QuestionnairePage' => array(
			'className' => 'QuestionnairePage',
			'foreignKey' => 'questionnaire_page_id',
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
			'foreignKey' => 'questionnaire_question_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'QuestionnaireChoice' => array(
			'className' => 'QuestionnaireChoice',
			'foreignKey' => 'questionnaire_question_id',
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
	 * getQuestionsByPageId 指定されたPageIdにマッチするアンケート設問を取得する
	 * @param int $pageId ページId
	 * @return array
	 */
	public function getQuestionsByPageId($pageId) {

		$this->unbindModel(array('hasMany' => array('QuestionnaireAnswer')));
		$questions = $this->find('all', array(
			'conditions' => array('QuestionnaireQuestion.questionnaire_page_id' => $pageId),
			'order' => array('QuestionnaireQuestion.question_sequence ASC')
		));
		return $questions;
	}

}
