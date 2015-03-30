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
	 * getChoicesByQuestionnaireQuestionId  $B<ALd(BID$B$KBP1~$9$kA*Br;R>pJs$r<hF@$7$F$/$k!#(BQuestionnaireQuestionn
	 * @param $questionniareQuestionId
	 * @param $conditions find$B$NFb!"(Bcondition$B>r7o(B
	 * @param $simple true$B$N>l9g!"=jB0$9$k(BQuestionnaireQuestion$B$N>pJs$r30$9!#(Bdefault$B$O(Bfalse
     * $B$=$l$r;H$$$d$9$$$h$&$KE83+$9$k(B
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
