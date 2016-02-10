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
 * @package NetCommons\Questionnaires\Test\Case\Controller\QuestionnaireAsnwerController
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
		$this->controller->Session->expects($this->any())
			->method('check')
			->will(
				$this->returnValueMap([
					['Questionnaire.auth_ok.questionnaire_10', true]
			]));
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
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		);
		$results[1] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'linkExist' => false, 'action' => 'edit', 'url' => array('controller' => 'questionnaire_edit')),
		));
		$results[2] = Hash::merge($results[0], array( // 存在しない
			'urlOptions' => array('key' => 'questionnaire_999'),
			'assert' => null,
			'exception' => 'BadRequestException',
		));
		$results[3] = Hash::merge($results[0], array( // 未公開
			'urlOptions' => array('key' => 'questionnaire_36'),
			'assert' => null,
			'exception' => 'BadRequestException',
		));
		$results[4] = Hash::merge($results[0], array( // 非会員NG
			'urlOptions' => array('key' => 'questionnaire_6'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('questionnaires', 'you will not be able to answer this questionnaire.')),
		));
		$results[5] = Hash::merge($results[0], array( // 未来
			'urlOptions' => array('key' => 'questionnaire_14'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('questionnaires', 'you will not be able to answer this questionnaire.')),
		));
		$results[6] = Hash::merge($results[0], array( // 過去
			'urlOptions' => array('key' => 'questionnaire_20'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('questionnaires', 'you will not be able to answer this questionnaire.')),
		));

		// test mode 画面へ
		$results[7] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'test_mode', 'key' => 'questionnaire_2'),
			'assert' => array('method' => 'assertTextNotContains', 'expected' => __d('questionnaires', 'Test Mode')),
		);
		// thanks画面 回答が終わっていない画面は見られない
		$results[8] = Hash::merge($results[0], array( // 未公開
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'thanks', 'key' => 'questionnaire_2'),
			'assert' => array('method' => 'assertNotEmpty'),
			'expected' => 'BadRequestException',
			'return' => 'json'
		));
		// 繰り返しなしのテストは非会員では厳しいので省略
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
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		);
		// 自分が書いた＆未公開
		$results[1] = Hash::merge($results[0], array(
			'urlOptions' => array('key' => 'questionnaire_38'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 人が書いた＆未公開
		$results[2] = Hash::merge($results[0], array( // 未公開
			'urlOptions' => array('key' => 'questionnaire_36'),
			'assert' => null,
			'expected' => 'BadRequestException',
		));
		// 非会員NG みれる
		$results[4] = Hash::merge($results[0], array( // 非会員NG
			'urlOptions' => array('key' => 'questionnaire_6'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 人が書いた未来
		$results[5] = Hash::merge($results[0], array( // 未来
			'urlOptions' => array('key' => 'questionnaire_14'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('questionnaires', 'you will not be able to answer this questionnaire.')),
		));
		// 自分が書いた未来
		$results[6] = Hash::merge($results[0], array( // 未来
			'urlOptions' => array('key' => 'questionnaire_18'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 繰り返し回答NGで未回答
		$results[7] = Hash::merge($results[0], array(
			'urlOptions' => array('key' => 'questionnaire_12'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 回答してないのに確認画面は見られない
		$results[8] = Hash::merge($results[0], array(
			'urlOptions' => array('action' => 'confirm', 'key' => 'questionnaire_12'),
			'assert' => array('method' => 'assertNotEmpty'),
			'expected' => 'BadRequestException',
			'return' => 'json'
		));
		// 人が書いた過去 省略
		// 自分が書いた過去 省略
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
		// 繰り返しNGで回答ずみ
		$results[1] = Hash::merge($results[0], array(
			'urlOptions' => array('key' => 'questionnaire_12'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('questionnaires', 'you will not be able to answer this questionnaire.')),
		));

		$results[2] = Hash::merge($results[0], array(	//画像認証
			'urlOptions' => array('key' => 'questionnaire_8'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'next_', 'value' => null),
		));
		// 回答が終わっているアンケートは見られる
		$results[3] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'thanks', 'key' => 'questionnaire_12'),
			'assert' => array(
				'method' => 'assertActionLink',
				'linkExist' => true,
				'action' => 'view', 'url' => array('frame_id' => '6', 'block_id' => '2', 'controller' => 'questionnaire_answer_summaries', 'key' => 'questionnaire_12')),
		);
		return $results;
	}

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
 * viewアクション　編集長は何でも見ることができるので
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
		$results[1] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'test_mode', 'key' => 'questionnaire_2'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('questionnaires', 'Test Mode')),
		);
		// 確認前までの状態になっていたらconfirmアンケートは見られる
		$results[2] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'action' => 'confirm', 'key' => 'questionnaire_12'),
			'assert' => array('method' => 'assertInput', 'type' => 'submit', 'name' => 'confirm_questionnaire', 'value' => null),
		);
		// shuffl
		$results[3] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'questionnaire_4'),
			'assert' => array('method' => 'assertTextContains', 'expected' => __d('questionnaires', 'Test Mode')),
		);
		return $results;
	}

/**
 * viewアクション　シャッフルされた選択肢を取り出すためだけの試験
 *
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function testGetShuffle() {
		$controller = $this->generate('Questionnaires.QuestionnaireAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'Questionnaires.Questionnaires',
				'Questionnaires.QuestionnairesOwnAnswer',
				'AuthorizationKeys.AuthorizationKey',
				'VisualCaptcha.VisualCaptcha'
			)
		));
		//テスト実施
		$controller->Session->expects($this->any())
			->method('check')
			->will($this->returnValue(true));

		$url = array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'view',
			'frame_id' => 6,
			'block_id' => 2,
			'key' => 'questionnaire_4'
		);
		$assert = array('method' => 'assertTextContains', 'expected' => __d('questionnaires', 'Test Mode'));

		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);
		$this->_testGetAction($url, $assert, null, 'view');
		//ログアウト
		TestAuthGeneral::logout($this);
	}

}
