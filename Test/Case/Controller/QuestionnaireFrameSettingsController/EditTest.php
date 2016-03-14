<?php
/**
 * QuestionnaireFrameSettingsController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireFrameSettingsController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Controller
 */
class QuestionnairesFrameSettingsControllerEditTest extends NetCommonsControllerTestCase {

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
	protected $_controller = 'questionnaire_frame_settings';

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
			'QuestionnaireFrameSetting' => array(
				'id' => 1,
				'frame_key' => 'frame_3',
				'display_type' => QuestionnairesComponent::DISPLAY_TYPE_LIST,
				'display_num_per_page' => '10',
				'sort_type' => 'Questionnaire.modified DESC',
			),
		);
		$displayQ = array();
		for ($i = 1; $i <= 50; $i = $i + 2) {
			$displayQ['List']['QuestionnaireFrameDisplayQuestionnaire'][] = array('is_display' => '1', 'questionnaire_key' => 'questionnaire_' . strval($i + 1));
		}
		$displayQ['Single']['QuestionnaireFrameDisplayQuestionnaire']['questionnaire_key'] = 'questionnaire_2';

		$data = Hash::merge($data, $displayQ);
		return $data;
	}

/**
 * editアクションのGETテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditGet
 * @return void
 */
	public function testEditGet($urlOptions, $assert, $exception = null, $return = 'view') {
		//Exception
		if ($exception) {
			$this->setExpectedException($exception);
		}

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);
	}

/**
 * editアクションのGETテスト(ログインなし)用DataProvider
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGet() {
		$data = $this->__getData();
		$results = array();

		//ログインなし
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id']),
			//'assert' => array('method' => 'assertNotEmpty', 'expected' => true)
			'assert' => null,
			'exception' => 'ForbiddenException',
			'return' => array('json')
		);
		return $results;
	}

/**
 * editアクションのGETテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditGetByPublishable
 * @return void
 */
	public function testEditGetByPublishable($urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'edit',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * editアクションのGETテスト(ログインあり)用DataProvider
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByPublishable() {
		$data0 = $this->__getData();
		$results = array();

		//ログインあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data0['Frame']['id']),
			'assert' => null
		);
		$results[1] = array(
			'urlOptions' => array('frame_id' => 15),
			'assert' => null
		);

		return $results;
	}

/**
 * editアクションのPOSTテスト
 *
 * @param array $data POSTデータ
 * @param string $role ロール
 * @param array $urlOptions URLオプション
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditPost
 * @return void
 */
	public function testEditPost($data, $role, $urlOptions, $exception = null, $return = 'view') {
		//ログイン
		if (isset($role)) {
			TestAuthGeneral::login($this, $role);
		}

		//テスト実施
		$this->_testPostAction('put', $data, Hash::merge(array('action' => 'edit'), $urlOptions), $exception, $return);

		//正常の場合、リダイレクト
		if (! $exception) {
			$header = $this->controller->response->header();
			$this->assertNotEmpty($header['Location']);
		}

		//ログアウト
		if (isset($role)) {
			TestAuthGeneral::logout($this);
		}
	}

/**
 * editアクションのPOSTテスト用DataProvider
 *
 * #### 戻り値
 *  - data: 登録データ
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditPost() {
		$data = $this->__getData();

		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id']),
				'exception' => 'ForbiddenException'
			),

			//正常
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id']),

			),
			//フレームID指定なしテスト
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null),

			),

		);
	}

/**
 * editアクションのValidateionErrorテスト
 *
 * @param array $data POSTデータ
 * @param array $urlOptions URLオプション
 * @param string|null $validationError ValidationError
 * @dataProvider dataProviderEditValidationError
 * @return void
 */
	public function testEditValidationError($data, $urlOptions, $validationError = null) {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実施
		$this->_testActionOnValidationError('put', $data, Hash::merge(array('action' => 'edit'), $urlOptions), $validationError);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * editアクションのValidationErrorテスト用DataProvider
 *
 * #### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderEditValidationError() {
		$data = $this->__getData();

		$result = array(
			'data' => $data,
			'urlOptions' => array('frame_id' => $data['Frame']['id']),
		);

		return array(
			//バリデーションエラー
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'QuestionnaireFrameSetting.display_type',
					'value' => 'ssss',
					'message' => __d('net_commons', 'Invalid request.'),
				)
			)),
		);
	}

}
