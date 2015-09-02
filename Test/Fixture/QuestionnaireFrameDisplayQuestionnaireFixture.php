<?php
/**
 * QuestionnaireFrameDisplayQuestionnaireFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuestionnaireFrameDisplayQuestionnaireFixture
 */
class QuestionnaireFrameDisplayQuestionnaireFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'frame_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'questionnaire_origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_frame_display_questionnaires_questionnaire_idx' => array('column' => 'frame_key', 'unique' => 0),
			'questionnaire_origin_id' => array('column' => 'questionnaire_origin_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'frame_key' => 'frame_1',
			'questionnaire_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-02-03 06:09:54',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:09:54'
		),
		array(
			'id' => 3,
			'frame_key' => 'frame_3',
			'questionnaire_origin_id' => 3,
			'created_user' => 1,
			'created' => '2015-02-03 06:09:54',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:09:54'
		),
	);

}
