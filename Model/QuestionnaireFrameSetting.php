<?php
/**
 * QuestionnaireFrameSetting Model
 *
 * @property Frame $Frame
 * @property QuestionnaireFrameDisplayQuestionnaire $QuestionnaireFrameDisplayQuestionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireFrameSetting Model
 */
class QuestionnaireFrameSetting extends QuestionnairesAppModel {

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
		'frame_id' => array(
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
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuestionnaireFrameDisplayQuestionnaire' => array(
			'className' => 'QuestionnaireFrameDisplayQuestionnaire',
			'foreignKey' => 'questionnaire_frame_setting_id',
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
 * getFrameSetting 指定されたframe_keyの設定要件を取り出す
 *
 * @param string $frameKey フレームkey
 * @return array ... displayNum sortField srotDirection
 */
	public function getQuestionnaireFrameSetting($frameKey) {
		$frameSetting = $this->find('first', array(
			'conditions' => array(
				'frame_key' => $frameKey
			),
			'recursive' => -1
		));

		if (!$frameSetting) {
			$displayNum = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE;
			$sort = 'modified';
			$dir = 'DESC';
		} else {
			$setting = $frameSetting['QuestionnaireFrameSetting'];
			$displayNum = $setting['display_num_per_page'];
			if ($setting['sort_type'] == QuestionnairesComponent::QUESTIONNAIRE_SORT_MODIFIED) {
				$sort = 'modified';
				$dir = 'DESC';
			} elseif ($setting['sort_type'] == QuestionnairesComponent::QUESTIONNAIRE_SORT_CREATED) {
				$sort = 'created';
				$dir = 'DESC';
			} elseif ($setting['sort_type'] == QuestionnairesComponent::QUESTIONNAIRE_SORT_TITLE) {
				$sort = 'title';
				$dir = 'ASC';
			} elseif ($setting['sort_type'] == QuestionnairesComponent::QUESTIONNAIRE_SORT_END) {
				$sort = 'end_period';
				$dir = 'ASC';
			}
		}
		return array($displayNum, $sort, $dir);
	}
}
