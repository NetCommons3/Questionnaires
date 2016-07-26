<?php
/**
 * QuestionnaireValidateBehavior Test Case
 *
 * @property Questionnaire $Questionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
/**
 * QuestionnaireValidateBehavior Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaire\Test\Case\Model
 */
class CheckDateTimeTest extends NetCommonsModelTestCase {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.block_setting_for_questionnaire',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		NetCommonsControllerTestCase::loadTestPlugin($this, 'Questionnaires', 'TestQuestionnaires');
		$this->TestQuestionnaireModel = ClassRegistry::init('TestQuestionnaires.TestQuestionnaireModel');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TestQuestionnaireModel);
		parent::tearDown();
	}

/**
 * test testCheckDateTime
 *
 * @param array $data Postされたデータ
 * @param array $check validateとして渡されるチェックデータ
 * @param bool $expected 期待値
 * @dataProvider dataProviderCheckDateTime
 * @return void
 */
	public function testCheckDateTime($data, $check, $expected) {
		$this->TestQuestionnaireModel->create();
		$this->TestQuestionnaireModel->set($data);
		$result = $this->TestQuestionnaireModel->checkDateTime($check);
		$this->assertEqual($result, $expected);
	}
/**
 * dataProviderCheckDateTime
 *
 * testCheckDateTimeのデータプロバイダ
 * @return array
 */
	public function dataProviderCheckDateTime() {
		$data = array(
			'Questionnaire' => array(
				'answer_start_period' => '2006-06-06 00:12:00'
			)
		);
		return array(
			array($data, array('answer_start_period' => '2006-06-06 00:12:00'), true),
			array($data, array('answer_start_period' => ''), true),
			array($data, array('answer_start_period' => '4567-34-90 44:44:44'), false)
		);
	}
}

