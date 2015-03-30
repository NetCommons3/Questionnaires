<?php
/**
 * QuestionnaireChoice Model
 *
 * @property QuestionnaireQuestion $QuestionnaireQuestions
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireChoice Model
 */
class QuestionnaireChoice extends QuestionnairesAppModel {

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
		'matrix_type' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'other_choice_type' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'choice_sequence' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'questionnaire_question_id' => array(
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
		'QuestionnaireQuestions' => array(
			'className' => 'QuestionnaireQuestions',
			'foreignKey' => 'questionnaire_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    /**
	 * getChoicesByQuestionnaireQuestionId  質問IDに対応する選択子情報を取得してくる。QuestionnaireQuestionn
	 * @param $questionniareQuestionId
	 * @param $conditions findの内、condition条件
	 * @param $simple trueの場合、所属するQuestionnaireQuestionの情報を外す。defaultはfalse
     * それを使いやすいように展開する
     * @return $choices
     */
	public function getChoicesByQuestionnaireQuestionId($questionniareQuestionId, $conditions, $simple=false)
	{
		if ($simple) {
			$this->unbindModel(array('belongsTo' => array('QuestionnaireQuestions')));
		}
        $choices = $this->find('all', array(
            'conditions' => $conditions,
            'order' => array('QuestionnaireChoice.id ASC'),
		));
		return $choices;
	}
}
