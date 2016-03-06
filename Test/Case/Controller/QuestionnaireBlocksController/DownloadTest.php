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
class QuestionnaireBlocksControllerDownloadTest extends NetCommonsControllerTestCase {

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

		//ログイン
		TestAuthGeneral::login($this);

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestQuestionnaires');
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestFiles');

		//テストコントローラ生成
		$this->generateNc('TestQuestionnaires.TestQuestionnaireBlocks');
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
 * download()のテスト
 *
 * @return void
 */
	public function testDownload() {
		//テスト実施
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'questionnaire_2',
			'frame_id' => $frameId
		);
		$this->_testPostAction('post', array(
				'AuthorizationKey' => array(
					'authorization_key' => 'ABC'
				)
			),
		$url);
		//チェック
		$this->assertTextEquals('questionnaire_2.zip', $this->controller->returnValue[0]);
		$this->assertTextEquals('questionnaire_2.csv', $this->controller->returnValue[1]);
		$this->assertTextEquals('ABC', $this->controller->returnValue[2]);
	}

/**
 * download()のgetテスト
 *
 * @return void
 */
	public function testIndexNoneFrameBlock() {
		//テスト実施
		// フレーム、ブロック指定なし
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'download',
			'key' => 'questionnaire_2',
		);

		$this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url, 'ForbiddenException');
	}

/**
 * download()の不正アンケート指定テスト
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
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'questionnaire_4',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('questionnaires', 'Designation of the questionnaire does not exist.'));
		$result = $this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url);
		//$flash = CakeSession::read('Message.flash');
		$this->assertEmpty($result);
	}
/**
 * download()の圧縮パスワードなし指定テスト
 *
 * @return void
 */
	public function testNoPassword() {
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'questionnaire_2',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('questionnaires', 'Setting of password is required always to download answers.'));
		$result = $this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => ''
			)
		), $url);
		$this->assertEmpty($result);
	}
/**
 * download()のファイル作成異常テスト
 *
 * @return void
 */
	public function testException() {
		$mock = $this->getMockForModel('Questionnaires.QuestionnaireAnswerSummaryCsv', array('getAnswerSummaryCsv'));
		$mock->expects($this->once())
			->method('getAnswerSummaryCsv')
			->will($this->throwException(new Exception));
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'questionnaire_2',
			'frame_id' => $frameId
		);
		$this->controller->Session->expects($this->once())
			->method('setFlash')
			->with(__d('questionnaires', 'download error'));
		$this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url);
	}

/**
 * download()の大量試験テスト
 *
 * @return void
 */
	public function testDownloadBigData() {
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => 'test_questionnaires',
			'controller' => 'test_questionnaire_blocks',
			'action' => 'download',
			'block_id' => $blockId,
			'key' => 'questionnaire_12',
			'frame_id' => $frameId
		);
		$this->_testPostAction('post', array(
			'AuthorizationKey' => array(
				'authorization_key' => 'ABC'
			)
		), $url);
		$this->assertEqual(count($this->controller->returnValue[3]), 3);	// header line + 2 records
	}
}