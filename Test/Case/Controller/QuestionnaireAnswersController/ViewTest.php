<?php
/**
 * QuestionnaireAnswerController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * QuestionnaireAnswerController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Bbses\Test\Case\Controller\BbsArticlesController
 */
class QuestionnaireAnswerControllerViewTest extends WorkflowControllerViewTest {

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
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'questionnaire_answers';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->Questionnaire->Behaviors->unload('AuthorizationKey');
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderView() {
		$results = array();

		//ログインなし
		//--コンテンツあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'questionnaire_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		/*
		$results[1] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		$results[2] = Hash::merge($results[0], array( //コメント（なし）
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => false, 'url' => array()),
		));
		//--コンテンツなし
		$results[3] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => null, 'return' => 'viewFile'
		);
		*/

		return $results;
	}

/**
 * viewアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByCreatable() {
		$results = array();
		//作成権限のみ(一般が書いた記事＆一度公開している)
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'questionnaire_10'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		/*
		$results[1] = Hash::merge($results[0], array( //（承認済み記事は編集不可）
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		//作成権限のみ(一般が書いた質問＆公開前)
		$results[2] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = Hash::merge($results[2], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//作成権限のみ(他人が書いた質問＆公開中)（root_idとparent_idが異なる）
		$results[4] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_9'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[5] = Hash::merge($results[4], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		$results[6] = Hash::merge($results[4], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => false, 'url' => array()),
		));

		//作成権限のみ(他人が書いた質問＆公開中)（root_idとparent_idが同一）
		$results[7] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_8'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[8] = Hash::merge($results[7], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		//--（子記事に'parent_id'あり）
		$results[9] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_7'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[10] = Hash::merge($results[9], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		//作成権限のみ(他人が書いた質問＆公開前)
		$results[11] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_5'),
			'assert' => null,
			'exception' => 'BadRequestException',
		);
		//--コンテンツなし
		$results[12] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => null, 'return' => 'viewFile'
		);
		//--パラメータ不正(keyに該当する質問が存在しない)
		$results[13] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_99'),
			'assert' => null,
			'exception' => 'BadRequestException',
		);
		//--BBSなし
		$results[14] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_xx'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
		);
		$results[15] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_xx'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
			'return' => 'json'
		);
		*/
		return $results;
	}

/**
 * viewアクションのテスト(編集権限、公開権限なし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByEditable() {
		$results = array();

		//編集権限あり（chef_userが書いた記事一度も公開していない）
		//--コンテンツあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'questionnaire_48'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		/*
		//チェック
		//--編集ボタン
		$results[1] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//--コメントボタン
		$results[2] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => false, 'url' => array()),
		));
		//編集権限あり（chef_userが書いた記事公開）
		//--コンテンツあり
		$results[3] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_6'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--編集ボタン
		$results[4] = Hash::merge($results[3], array( //なし(公開すると編集不可)
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		//--コメントボタン
		$results[5] = Hash::merge($results[3], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => true, 'url' => array()),
		));
		//--コンテンツなし
		$results[6] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => null, 'return' => 'viewFile'
		);
		//フレームID指定なしテスト
		$results[7] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[8] = Hash::merge($results[3], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		//根記事が取得できない
		$results[9] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_10'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
		);
		$results[10] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_10'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
			'return' => 'json'
		);
		//親記事が取得できない
		$results[11] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_11'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
		);
		$results[12] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_11'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
			'return' => 'json'
		);
		*/

		return $results;
	}

/**
 * viewアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewError
 * @return void
 */
	/*
	public function testViewError($urlOptions, $assert, $exception = null, $return = 'view') {
		//Exception
		ClassRegistry::removeObject('WorkflowBehavior');
		$workflowBehaviorMock = $this->getMock('WorkflowBehavior', ['canReadWorkflowContent']);
		ClassRegistry::addObject('WorkflowBehavior', $workflowBehaviorMock);
		$this->Questionnaire->Behaviors->unload('Workflow');
		$this->Questionnaire->Behaviors->load('Workflow', $this->Questionnaire->actsAs['Workflow.Workflow']);

		$workflowBehaviorMock
			->expects($this->once())
			->method('canReadWorkflowContent')
			->will($this->returnValue(false));

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'view',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);
	}
	*/
/**
 * viewアクション用DataProvider
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	/*
	public function dataProviderViewError() {
		$results = array();

		// 参照不可のテスト
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'questionnaire_40'),
			'assert' => null,
			'exception' => 'BadRequestException',
		);
		$results[1] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_10'),
			'assert' => null,
			'exception' => 'BadRequestException',
			'return' => 'json'
		);
		return $results;
	}
	*/

/**
 * viewアクション(編集ボタンの確認)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewGetByPublishable
 * @return void
 */
	public function testEditGetByPublishable($urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		$this->testView($urlOptions, $assert, $exception, $return);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * viewアクション(コメントボタンの確認)用DataProvider
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewGetByPublishable() {
		//公開中の記事
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'questionnaire_2'),
			'assert' => null
		);
		/*
		//チェック
		//--編集ボタン
		$results[1] = Hash::merge($results[0], array( //あり
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//--コメントボタン
		$results[2] = Hash::merge($results[0], array( //あり
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => true, 'url' => array()),
		));

		//公開前の記事
		$results[3] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_4'),
			'assert' => null
		);
		//チェック
		//--編集ボタン
		$results[4] = Hash::merge($results[3], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//--コメントボタン
		$results[5] = Hash::merge($results[3], array( //なし
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => false, 'url' => array()),
		));
		//--未承認のコメント（承認ボタン）
		$results[6] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_13'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_PUBLISHED, 'value' => null),
		);
		*/

		return $results;
	}

}
