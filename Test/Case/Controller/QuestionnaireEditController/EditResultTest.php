<?php
/**
 * QuestionnaireEditController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerEditTest', 'Workflow.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireEditController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionaires\Test\Case\Controller\QuestionnaireEditController
 */
class QuestionnaireEditControllerEditResultTest extends WorkflowControllerEditTest {

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
	protected $_controller = 'questionnaire_edit';

/**
 * test Action name
 *
 * @var string
 */
	protected $_myAction = 'edit_result';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->Questionnaire->Behaviors->unload('AuthorizationKey');

		$errs = array(
			array(
				'field' => 'Questionnaire.is_total_show',
				'value' => 'aa',
			),
			array(
				'field' => 'QuestionnairePage.0.QuestionnaireQuestion.0.is_result_display',
				'value' => 'aa',
			),
			array(
				'field' => 'QuestionnairePage.0.QuestionnaireQuestion.0.result_display_type',
				'value' => 'aa',
			),
			array(
				'field' => 'QuestionnairePage.0.QuestionnaireQuestion.0.QuestionnaireChoice.0.graph_color',
				'value' => 'aa',
			),
		);

		$this->controller->Session->expects($this->any())
			->method('check')
			->will($this->returnValueMap([
				['Questionnaires.questionnaireEdit.' . 'testSession', true],
				['Questionnaires.questionnaireEdit.' . 'testSession_44', true],
				['Questionnaires.questionnaireEdit.' . 'testSession_12', true],
				['Questionnaires.questionnaireEdit.' . 'testSession_4', true],
				['Questionnaires.questionnaireEdit.' . 'testSession_err0', true],
				['Questionnaires.questionnaireEdit.' . 'testSession_err1', true],
				['Questionnaires.questionnaireEdit.' . 'testSession_err2', true],
				['Questionnaires.questionnaireEdit.' . 'testSession_err3', true],
			]));
		$this->controller->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap([
				['Questionnaires.questionnaireEdit.' . 'testSession', $this->__getData('')],
				['Questionnaires.questionnaireEdit.' . 'testSession_44', $this->__getData('questionnaire_44')],
				['Questionnaires.questionnaireEdit.' . 'testSession_12', $this->__getData('questionnaire_12')],
				['Questionnaires.questionnaireEdit.' . 'testSession_4', $this->__getData('questionnaire_4')],
				['Questionnaires.questionnaireEdit.' . 'testSession_err0',
					Hash::insert($this->__getData(), $errs[0]['field'], $errs[0]['value'])],
				['Questionnaires.questionnaireEdit.' . 'testSession_err1',
					Hash::insert($this->__getData(), $errs[1]['field'], $errs[1]['value'])],
				['Questionnaires.questionnaireEdit.' . 'testSession_err2',
					Hash::insert($this->__getData(), $errs[2]['field'], $errs[2]['value'])],
				['Questionnaires.questionnaireEdit.' . 'testSession_err3',
					Hash::insert($this->__getData(), $errs[3]['field'], $errs[3]['value'])],
			]));
	}

/**
 * テストDataの取得
 *
 * @param string $questionnaireKey キー
 * @return array
 */
	private function __getData($questionnaireKey = null) {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '1',
				'plugin_key' => $this->plugin,
			),
			'Questionnaire' => array(
				'key' => $questionnaireKey,
				'status' => WorkflowComponent::STATUS_IN_DRAFT,
				'title' => 'EditTestTitle',
				'title_icon' => 'ok.svg',
				'sub_title' => 'EditTestSubTitle',
				'is_total_show' => 0,
				'answer_timing' => '0',
				'is_key_pass_use' => 0,
				'total_show_timing' => 0,
			),
			'QuestionnairePage' => array(
				array(
					'page_title' => __d('questionnaires', 'First Page'),
					'page_sequence' => 0,
					'route_number' => 0,
					'QuestionnaireQuestion' => array(
						array(
							'question_sequence' => 0,
							'question_value' => __d('questionnaires', 'New Question') . '1',
							'question_type' => QuestionnairesComponent::TYPE_SELECTION,
							'is_require' => QuestionnairesComponent::USES_NOT_USE,
							'is_skip' => QuestionnairesComponent::SKIP_FLAGS_NO_SKIP,
							'is_choice_random' => QuestionnairesComponent::USES_NOT_USE,
							'is_range' => QuestionnairesComponent::USES_NOT_USE,
							'is_result_display' => QuestionnairesComponent::EXPRESSION_SHOW,
							'result_display_type' => QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART,
							'QuestionnaireChoice' => array(
								array(
									'choice_sequence' => 0,
									'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
									'choice_label' => __d('questionnaires', 'new choice') . '1',
									'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
									'graph_color' => '#FF0000',
									'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END
								)
							)
						)
					)
				)
			),
			//'WorkflowComment' => array(
			//	'comment' => 'WorkflowComment save test'
			//),
		);
		return $data;
	}

/**
 * editアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
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
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_42'),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		return $results;
	}

/**
 * editアクションのGETテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByCreatable() {
		$data = $this->__getData();

		$results = array();

		//作成権限のみ
		//--他人の記事の編集
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_42'),
			'assert' => null,
			'exception' => 'BadRequestException'
		);
		//--自分の記事の編集(一度も公開していない)
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_44'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 存在してないアンケートを指定
		$results[2] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_99999'),
			'assert' => null, 'exception' => 'BadRequestException', 'return' => 'json'
		);
		//新規作成
		$results[3] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//--自分の記事の編集(公開すみ)
		$results[4] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_10'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		return $results;
	}

/**
 * editアクションのGETテスト(編集権限、公開権限なし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByEditable() {
		$data = $this->__getData();
		$base = 0;
		$results = array();

		//編集権限あり
		//--コンテンツあり 自分の記事（編集できるコンテンツ
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_42'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		/* 本来はここで表示ページの要素の妥当性をチェックするのだと思われるが
		 * アンケートはAngularでページ要素を展開しているため、NetCommons通常テスト確認メソッドが使えない
		 * ごく一部に限って確認を行うことにする
		 */

		// ページタブ,ページ追加リンク,質問追加ボタン,質問LI、質問種別選択,質問削除ボタン, 選択肢追加ボタン, 選択肢削除ボタン、キャンセルボタン、次へボタンの存在の確認
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][is_total_show]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'textarea', 'name' => 'data[Questionnaire][total_comment]', 'value' => null),
		)));

		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][is_result_display]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][result_display_type]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][graph_color]', 'value' => null),
		)));

		//--コンテンツなし...編集対象データを指定せずに編集画面へ行くと不正リクエストエラー
		$results[count($results)] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'action' => $this->_myAction, 'key' => null),
			'assert' => null,
			'exception' => 'BadRequestException'
		);

		return $results;
	}

/**
 * editアクションのGETテスト(公開権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByPublishable() {
		$data = $this->__getData();
		$results = array();

		//フレームID指定なしテスト
		$results[0] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//フレームID指定なしでも画面の内容がちゃんと表示されていることを確認している
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_2'),
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => null),
		)));

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
		$this->_testPostAction(
			'put', $data, Hash::merge(array('action' => 'edit'), $urlOptions), $exception, $return
		);

		//正常の場合、リダイレクト
		if (! $exception) {
			if ($return != 'json') {
				$header = $this->controller->response->header();
				$this->assertNotEmpty($header['Location']);
			} else {
				$result = json_decode($this->contents, true);
				$this->assertArrayHasKey('code', $result);
			}
		}

		//ログアウト
		if (isset($role)) {
			TestAuthGeneral::logout($this);
		}
	}
/**
 * editアクションのPOSTテスト用DataProvider
 *
 * ### 戻り値
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
		$midstreamData = $data;
		$data['QuestionnairePage'] = array();
		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_44'),
				'exception' => 'ForbiddenException'
			),
			//作成権限のみ
			//--他人の記事
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'questionnaire_40'),
				'exception' => 'BadRequestException'
			),
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'questionnaire_40'),
				'exception' => 'BadRequestException', 'return' => 'json'
			),
			//--自分の記事(一度も公開していない)
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession_44'),
			),
			//--自分の記事(公開)
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession_12'),
			),
			//編集権限あり
			//--新規作成
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
			//フレームID指定なし 新規作成テスト
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
			//--自分の記事(公開)
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession_4'),
			),
			//途中投稿
			array(
				'data' => $midstreamData, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
				'exception' => null, 'return' => 'json'
			),
		);
	}

/**
 * editアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderEditValidationError() {
		$data = $this->__getData();
		$data['QuestionnairePage'] = array();

		$result = array();
		for ($i = 0; $i < 4; $i++) {
			$result[$i] = array(
				'data' => $data,
				'urlOptions' => array(
					'frame_id' => $data['Frame']['id'],
					'block_id' => $data['Block']['id'],
					'action' => $this->_myAction,
					's_id' => 'testSession_err' . $i),
			);
		}

		return array(
			Hash::merge($result[0], array(
				'validationError' => array(
					'field' => 'Questionnaire.is_total_show',
					'value' => 'aa',
					'message' => __d('net_commons', 'Invalid request.'),
				)
			)),
			Hash::merge($result[1], array(
				'validationError' => array(
					'field' => '',
					'value' => '',
					'message' => 'question.errorMessages.isResultDisplay',
				)
			)),
			Hash::merge($result[2], array(
				'validationError' => array(
					'field' => '',
					'value' => '',
					'message' => 'question.errorMessages.resultDisplayType',
				)
			)),
			Hash::merge($result[3], array(
				'validationError' => array(
					'field' => '',
					'value' => '',
					'message' => 'choice.errorMessages.graphColor',
				)
			)),
		);
	}
}