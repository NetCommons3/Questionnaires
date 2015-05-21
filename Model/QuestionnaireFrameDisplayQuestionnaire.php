<?php
/**
 * QuestionnaireFrameDisplayQuestionnaire Model
 *
 * @property QuestionnaireFrameSetting $QuestionnaireFrameSetting
 * @property Questionnaire $Questionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireFrameDisplayQuestionnaire Model
 */
class QuestionnaireFrameDisplayQuestionnaire extends QuestionnairesAppModel {

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
		'frame_key' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
		'Frame' => array(
			'className' => 'Frame',
			'foreignKey' => 'frame_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Questionnaire' => array(
			'className' => 'Questionnaire',
			'foreignKey' => 'questionnaire_origin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * saveFrameDisplayQuestionnaire
 * this function is called when you create new questionnaire
 *
 * @param int $frameId frame id
 * @param int $questionnaireId questionnaire id
 * @return bool
 */
	public function saveFrameDisplayQuestionnaire($frameId, $questionnaireId) {
		$frame = $this->Frame->find('first', array(
			'conditions' => array(
				'Frame.id' => $frameId
			)
		));
		if (!$frame) {
			return false;
		}
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array(
				'Questionnaire.id' => $questionnaireId
			)
		));
		if (!$questionnaire) {
			return false;
		}

		$saveData = array(
			'frame_key' => $frame['Frame']['key'],
			'questionnaire_origin_id' => $questionnaire['Questionnaire']['origin_id']);
		if ($this->find('first', array(
			'conditions' => $saveData
		))) {
			// あるならもう作らない
			return true;
		}
		if (!$this->save($saveData)) {
			return false;
		}
		return true;
	}
}
