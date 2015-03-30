<?php
/**
 * QuestionnaireFrameDisplayQuestionnaireFixture
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
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
		'questionnaire_frame_setting_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'questionnaire_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_frame_display_questionnaires_questionnaire_idx' => array('column' => 'questionnaire_frame_setting_id', 'unique' => 0),
			'fk_questionnaire_frame_display_questionnaires_questionnaire_idx1' => array('column' => 'questionnaire_id', 'unique' => 0)
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
			'questionnaire_frame_setting_id' => 1,
			'questionnaire_id' => 1,
			'created_user' => 1,
			'created' => '2015-02-03 06:09:54',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:09:54'
		),
	);

}
