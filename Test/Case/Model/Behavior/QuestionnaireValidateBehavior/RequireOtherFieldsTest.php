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
class RequireOtherFieldsTest extends NetCommonsModelTestCase {

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
 * test requireOtherFields
 *
 * @param array $data Postされたデータ
 * @param array $check validateとして渡されるチェックデータ
 * @param mix $requireValue $checkの値として期待する値（この値のときにValidate処理を行う
 * @param array $others 連動して値を要求するその他フィールド名
 * @param string $ope その他フィールドに対して値をチェックするときの判断方法
 * @param bool $expected 期待値
 * @dataProvider dataProviderRequireOtherFields
 * @return void
 */
	public function testRequireOtherFields($data, $check, $requireValue, $others, $ope, $expected) {
		$this->TestQuestionnaireModel->create();
		$this->TestQuestionnaireModel->set($data);
		$result = $this->TestQuestionnaireModel->requireOtherFields($check, $requireValue, $others, $ope);
		$this->assertEqual($result, $expected);
	}

/**
 * dataProviderRequireOtherFields
 *
 * testRequireOtherFieldsのデータプロバイダ
 * @return array
 */
	public function dataProviderRequireOtherFields() {
		$data1 = array(
			'Questionnaire' => array(
				'answer_timing' => 1,
				'answer_start_period' => '2016-03-03 00:00:00',
				'answer_end_period' => null
			)
		);
		$data2 = Hash::insert($data1, 'Questionnaire.answer_end_period', '2016-03-03 00:00:00');
		$data3 = array(
			'Questionnaire' => array(
				'is_key_pass_use' => 1,
			),
			'AuthorizationKey' => array(
				'authorization_key' => 'aaaa'
			)
		);
		$data4 = Hash::insert($data3, 'AuthorizationKey.authorization_key', '');
		$data5 = Hash::insert($data3, 'Questionnaire.is_image_authentication', '0');
		$data6 = Hash::insert($data3, 'Questionnaire.is_image_authentication', '1');
		return array(
			array($data1, array('answer_timing' => 0), 1, array('Questionnaire.answer_start_period', 'Questionnaire.answer_end_period'), 'OR', true),
			array($data1, array('answer_timing' => 1), 1, array('Questionnaire.answer_start_period', 'Questionnaire.answer_end_period'), 'OR', true),
			array($data2, array('answer_timing' => 1), 1, array('Questionnaire.answer_start_period', 'Questionnaire.answer_end_period'), 'AND', true),
			array($data3, array('is_key_pass_use' => 1), 1, array('AuthorizationKey.authorization_key'), 'AND', true),
			array($data4, array('is_key_pass_use' => 1), 1, array('AuthorizationKey.authorization_key'), 'AND', false),
			array($data5, array('is_key_pass_use' => 1), 1, array('Questionnaire.is_image_authentication'), 'XOR', true),
			array($data6, array('is_key_pass_use' => 1), 1, array('Questionnaire.is_image_authentication'), 'XOR', false),
		);
	}
}
