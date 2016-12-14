<?php
/**
 * QuestionnaireAnswerSummaryFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuestionnaireAnswerSummaryFixture
 */
class QuestionnaireAnswerSummaryFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_12',
			'user_id' => 3,
			'created_user' => 3
		),
		array(
			'id' => 2,
			'answer_status' => '1',	// 確認前
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_12',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 3,
			'answer_status' => '0',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_2',
			'user_id' => 3,
			'created_user' => 3
		),
		array(
			'id' => 4,
			'answer_status' => '0',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_4',
			'user_id' => 3,
			'created_user' => 3
		),
		array(
			'id' => 5,
			'answer_status' => '0',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_6',
			'user_id' => 3,
			'created_user' => 3
		),
		array(
			'id' => 6,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_6',
			'user_id' => 4,
			'created_user' => 4
		),
		array(
			'id' => 7,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_8',
			'user_id' => 4,
			'created_user' => 4
		),
		array(
			'id' => 8,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_20',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 9,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_22',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 10,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_24',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 11,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_4',
			'user_id' => 1,
			'created_user' => 1
		),
		array(
			'id' => 12,
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_12',
			'user_id' => '',
			'created_user' => ''
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Questionnaires') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new QuestionnairesSchema())->tables[Inflector::tableize($this->name)];
		parent::init();
	}

}
