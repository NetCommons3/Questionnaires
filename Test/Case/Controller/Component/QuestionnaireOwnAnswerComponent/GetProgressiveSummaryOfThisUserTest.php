<?php
/**
 * QuestionnaireOwnAnswerComponent::getProgressiveSummaryOfThisUser()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuestionnaireOwnAnswerComponent::getProgressiveSummaryOfThisUser()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Controller\Component\QuestionnaireOwnAnswerComponent
 */
class QuestionnaireOwnAnswerComponentGetProgressiveSummaryOfThisUserTest
	extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.questionnaire_setting',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'questionnaires';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestQuestionnaires');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * getProgressiveSummaryOfThisUser()のテスト
 *
 * @return void
 */
	public function testGetProgressiveSummaryOfThisUser() {
		//テストコントローラ生成
		$this->generateNc('TestQuestionnaires.TestQuestionnairesOwnAnswerComponent');

		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_EDITOR);

		//事前チェック
		//$this->assertEmpty($this->controller->Questionnaires->controller);

		//テスト実行
		$this->_testGetAction(
			'/test_questionnaires/test_questionnaire_own_answer_component/index',
			array('method' => 'assertNotEmpty'),
			null,
			'view');

		$result = $this->controller->QuestionnairesOwnAnswer->getProgressiveSummaryOfThisUser(
			'questionnaire_6');

		//チェック
		$this->assertEqual($result['QuestionnaireAnswerSummary']['id'], 5);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * getProgressiveSummaryOfThisUser()のテスト
 *
 * @return void
 */
	public function testGetProgressiveSummaryOfThisUserNoLoginNoAnswer() {
		//テストコントローラ生成
		$this->generateNc('TestQuestionnaires.TestQuestionnairesOwnAnswerComponent');

		//テスト実行
		$this->_testGetAction(
			'/test_questionnaires/test_questionnaire_own_answer_component/index',
			array('method' => 'assertNotEmpty'),
			null,
			'view');

		$result = $this->controller->QuestionnairesOwnAnswer->getProgressiveSummaryOfThisUser(
			'questionnaire_6');

		//チェック
		$this->assertFalse($result);
	}

/**
 * getProgressiveSummaryOfThisUser()のテスト
 *
 * @return void
 */
	public function testGetProgressiveSummaryOfThisUserNoLogin() {
		//テストコントローラ生成
		$this->generateNc('TestQuestionnaires.TestQuestionnairesOwnAnswerComponent');

		$this->controller->Session->expects($this->any())
			->method('read')
			->will(
				$this->returnValueMap([
					['Questionnaires.progressiveSummary.questionnaire_12', 12]
			]));

		//テスト実行
		$this->_testGetAction(
			'/test_questionnaires/test_questionnaire_own_answer_component/index',
			array('method' => 'assertNotEmpty'),
			null,
			'view');

		$result = $this->controller->QuestionnairesOwnAnswer->getProgressiveSummaryOfThisUser(
			'questionnaire_12');

		//チェック
		$this->assertFalse($result);
	}

}
