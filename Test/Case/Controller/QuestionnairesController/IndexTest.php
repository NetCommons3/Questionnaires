<?php
/**
 * QuestionnairesController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnairesController', 'Questionnaires.Controller');
App::uses('WorkflowControllerIndexTest', 'Workflow.TestSuite');

/**
 * QuestionnairesController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Controller\QuestionnairesController
 */
class QuestionnairesControllerIndexTest extends WorkflowControllerIndexTest {

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
		'plugin.workflow.workflow_comment',
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
	protected $_controller = 'questionnaires';

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __getData() {
		$frameId = '6';
		$blockId = '2';

		$data = array(
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
			),
		);

		return $data;
	}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->Questionnaire->Behaviors->unload('AuthorizationKey');
		$this->QuestionnaireFrameSetting = ClassRegistry::init('Questionnaires.QuestionnaireFrameSetting');
		$this->QuestionnaireAnswerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary');
	}

/**
 * indexアクションのテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndex() {
		$data = $this->__getData();
		$results = array();
		//ソート、表示件数指定なし
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// ログイン無しでテストモードは０になる　　画面には"no questionnaire"のテキストが現れていること
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'answer_status' => 'test'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('questionnaires', 'no questionnaire')),
		);
		// ログイン無しで未回答は全て出てくる　画面には何らかの表示が現れていること
		$results[2] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'answer_status' => 'unanswered'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// ログイン無しで回答済は０になる　画面には"no questionnaire"のテキストが現れていること
		$results[3] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'answer_status' => 'answered'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('questionnaires', 'no questionnaire')),
		);
		// 表示件数を増やすことでFixtureデータをすべて表示させる
		// そのうえで試験を行う
		// 未ログインでも公開中なら見える
		$results[4] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_2', 'limit' => null)),
		);
		// 未ログインの場合は一時保存が見えない
		$results[5] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_42', 'limit' => null)),
		);
		// 未ログインの場合は未来公開が見えない
		$results[6] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_18', 'limit' => null)),
		);
		// 未ログインの場合は過去公開は見えない。。。Workflowの共通処理に任せることになるので編集権限なければ出ない
		$results[7] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_24', 'limit' => null)),
		);

		//チェック
		//--追加ボタンチェック(なし)
		$results[8] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => false, 'url' => array('controller' => 'questionnaire_add')),
		);

		return $results;
	}

/**
 * indexアクションのテスト(編集権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByEditable() {
		$data = $this->__getData();
		$results = array();

		//編集権限あり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--追加ボタンチェック
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('controller' => 'questionnaire_add'),
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_add')),
		)));
		//フレームあり(コンテンツなし)テスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null),
			'assert' => array('method' => 'assertContains', 'expected' => __d('questionnaires', 'no questionnaire'))
		)));
		//記事なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '6'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('questionnaires', 'no questionnaire'))
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
		)));
		// 公開後に一時保存変更してる見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_2', 'limit' => null))
		)));
		// 公開中なら見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_6', 'limit' => null))
		)));
		// 一時保存も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_42', 'limit' => null))
		)));
		// 未来も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_18', 'limit' => null))
		)));
		// 過去も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_24', 'limit' => null))
		)));

		return $results;
	}

/**
 * indexアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByCreatable() {
		$data = $this->__getData();
		$results = array();

		//作成権限あり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--追加ボタンチェック
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'controller' => 'questionnaire_add'),
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => true, 'url' => array()),
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		)));
		// 公開中なら見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_2', 'limit' => null))
		)));
		// 自分のなら承認待ちも見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_28', 'limit' => null))
		)));
		// 自分のなら差し戻しも見える #5
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_10', 'limit' => null))
		)));
		// 自分のなら一時保存も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_38', 'limit' => null))
		)));
		// 自分のなら未来も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_18', 'limit' => null))
		)));
		// 自分のなら過去も見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_24', 'limit' => null))
		)));

		// 他人の承認待ちも見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_26', 'limit' => null))
		)));
		// 他人の差し戻しも見えない #10
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_30', 'limit' => null))
		)));
		// 他人の一時保存も見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'test_mode', 'linkExist' => false, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_36', 'limit' => null))
		)));
		// 他人の未来 集計表示ありだが未来なので見えない
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => false, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_16', 'limit' => null))
		)));
		// 他人の過去 発行済みで集計表示ありなので見える
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('action' => 'index', 'limit' => 50),
			'assert' => array('method' => 'assertActionLink', 'action' => 'view', 'linkExist' => true, 'url' => array('controller' => 'questionnaire_answers', 'key' => 'questionnaire_22', 'limit' => null))
		)));

		return $results;
	}

/**
 * ページネーションDataProvider
 *
 * ### 戻り値
 *  - page: ページ番号
 *  - isFirst: 最初のページかどうか
 *  - isLast: 最後のページかどうか
 *
 * @return array
 */
	public function dataProviderPaginator() {
		//$page, $isFirst, $isLast
		$data = array(
			array(1, true, false),
			array(3, false, false),
			array(23, false, true),
		);
		return $data;
	}

/**
 * index()のページネーションテスト
 *
 * @param int $page ページ番号
 * @param bool $isFirst 最初のページかどうか
 * @param bool $isLast 最後のページかどうか
 * @dataProvider dataProviderPaginator
 * @return void
 */
	public function testIndexPaginator($page, $isFirst, $isLast) {
		TestAuthGeneral::login($this);

		//テスト実施
		$frameId = '6';
		$blockId = '2';
		$url = array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
			$blockId,
			'frame_id' => $frameId,
			'limit' => '1',
		);
		if (! $isFirst) {
			$url['page'] = $page;
		}
		$result = $this->_testNcAction($url, array('method' => 'get'));

		//チェック
		$this->assertRegExp(
			'/' . preg_quote('<ul class="pagination">', '/') . '/', $result
		);
		if ($isFirst) {
			$this->assertNotRegExp('/<li><a.*?rel="first".*?<\/a><\/li>/', $result);
		} else {
			$this->assertRegExp('/<li><a.*?rel="first".*?<\/a><\/li>/', $result);
		}
		$this->assertRegExp('/<li class="active"><a>' . $page . '<\/a><\/li>/', $result);
		if ($isLast) {
			$this->assertNotRegExp('/<li><a.*?rel="last".*?<\/a><\/li>/', $result);
		} else {
			$this->assertRegExp('/<li><a.*?rel="last".*?<\/a><\/li>/', $result);
		}

		TestAuthGeneral::logout($this);
	}

/**
 * indexアクションのExceptionErrorテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexExceptionError
 * @return void
 * @throws InternalErrorException
 */
	public function testIndexExceptionError($urlOptions, $assert, $exception = null, $return = 'view') {
		$this->generate(
			'Questionnaires.Questionnaires',
			[
				'components' => [
					'Paginator'
				]
			]
		);

		//Exception
		$this->controller->Components->Paginator
			->expects($this->once())
			->method('paginate')
			->will($this->returnCallback(function () {
				throw new InternalErrorException();
			}));

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);
	}

/**
 * indexアクションのExceptionErrorテスト用DataProvider
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexExceptionError() {
		$data = $this->__getData();

		return array(
			array(
				'urlOptions' => array('frame_id' => $data['Frame']['id']),
				'assert' => array('method' => 'assertNotEmpty'),
				'exception' => 'InternalErrorException',
			),
		);
	}

}