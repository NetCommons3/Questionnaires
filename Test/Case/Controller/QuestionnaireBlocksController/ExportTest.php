<?php
/**
 * QuestionnaireBlocksController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireBlocksController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Qustionnaires\Test\Case\Controller\QuestionnaireBlocksController
 */
class QuestionnaireBlocksControllerExportTest extends NetCommonsControllerTestCase {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'questionnaire_blocks';

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
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Edit controller name
 *
 * @var string
 */
	protected $_editController = 'questionnaire_blocks';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestQuestionnaires');
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestFiles');

		//テストコントローラ生成
		$this->generateNc('TestQuestionnaires.TestQuestionnaireBlocks', array('components' => array(
			'Flash' => array('set')
		)));

		//ログイン
		TestAuthGeneral::login($this);

		$this->controller->Questionnaire->Behaviors->unload('Workflow.Workflow');
		$this->controller->Questionnaire->Behaviors->unload('Workflow.WorkflowComment');
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
 * export()のテスト
 *
 * @return void
 */
	public function testExport() {
		//テスト実施
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'export',
			'block_id' => $blockId,
			'key' => 'questionnaire_6',
			'frame_id' => $frameId
		);
		$this->_testNcAction($url);
		//チェック
		$this->assertTextEquals('questionnaire_6.zip', $this->controller->returnValue);
	}

/**
 * export()のgetテスト
 *
 * @return void
 */
	public function testIndexNoneFrameBlock() {
		//テスト実施
		// フレーム、ブロック指定なし
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'export',
			'key' => 'questionnaire_6',
		);

		$this->_testNcAction($url, array(), 'NotFoundException');
	}

/**
 * export()の不正アンケート指定テスト
 *
 * 一度も発行されたことのないアンケートはCSVを入手できない
 * 存在しないアンケート
 *
 * @return void
 */
	public function testNoPublish() {
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'export',
			'block_id' => $blockId,
			'key' => 'questionnaire_4',
			'frame_id' => $frameId
		);
		$this->controller->Flash->expects($this->once())
			->method('set')
			->with(__d('questionnaires', 'Designation of the questionnaire does not exist.'));
		$result = $this->_testNcAction($url);
		$this->assertEmpty($result);
	}
/**
 * export()のファイル作成異常テスト
 *
 * @return void
 */
	public function testException() {
		$mock = $this->getMockForModel('Questionnaires.QuestionnaireExport', array('getExportData'));
		$mock->expects($this->once())
			->method('getExportData')
			->will($this->throwException(new Exception));
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'export',
			'block_id' => $blockId,
			'key' => 'questionnaire_6',
			'frame_id' => $frameId
		);
		$this->controller->Flash->expects($this->once())
			->method('set')
			->with(__d('questionnaires', 'export error'));
		$this->_testNcAction($url);
	}

}
