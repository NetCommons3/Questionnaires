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
 * @package NetCommons\Questionnaires\Test\Case\Controller\QuestionnaireEditController
 */
class QuestionnaireEditControllerEditTest extends WorkflowControllerEditTest {

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
	protected $_controller = 'questionnaire_edit';

/**
 * test Action name
 *
 * @var string
 */
	protected $_myAction = 'edit';

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
			->will($this->returnValueMap([['Questionnaires.questionnaireEdit.' . 'testSession', true]]));
		$this->controller->Session->expects($this->any())
			->method('read')
			->will($this->returnValueMap([['Questionnaires.questionnaireEdit.' . 'testSession', $this->__getData()]]));
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
				'sub_title' => 'EditTestSubTitle',
				'is_total_show' => 0,
				'answer_timing' => '0',
				'answer_start_period' => '2016-08-01 00:00:00',
				'answer_end_period' => '2017-08-01 00:00:00',
				//'is_no_member_allow' => 0,
				'is_key_pass_use' => 0,
				'total_show_timing' => 0,
				'total_show_start_period' => '',
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
			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test'
			),
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
			//'assert' => null,
			//'exception' => 'BadRequestException'
			'assert' => array('method' => 'assertContains', 'expected' => __d('questionnaires', 'not found this questionnaire.')),
		);
		//--自分の記事の編集(一度も公開していない)
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_44'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// 存在してないアンケートを指定
		$results[2] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_99999'),
			//'assert' => null, 'exception' => 'BadRequestException', 'return' => 'json'
			'assert' => array('method' => 'assertContains', 'expected' => __d('questionnaires', 'not found this questionnaire.')),
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
		//--自分の記事の編集(一度も公開していない)
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_44'),
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => '_method', 'value' => 'DELETE'),
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
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][title]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][answer_timing]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][total_show_timing]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][is_no_member_allow]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][is_key_pass_use]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][is_image_authentication]', 'value' => null),
		)));
		//array_push($results, Hash::merge($results[$base], array(
		//	'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][is_repeat_allow]', 'value' => null),
		//)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Questionnaire][is_anonymity]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'textarea', 'name' => 'data[Questionnaire][thanks_content]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_IN_DRAFT, 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_APPROVED, 'value' => null),
		)));

		//--コンテンツなし...編集対象データを指定せずに編集画面へ行くと不正リクエストエラー
		$results[count($results)] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'action' => $this->_myAction, 'key' => null),
			//'assert' => null,
			//'exception' => 'BadRequestException'
			'assert' => array('method' => 'assertContains', 'expected' => __d('questionnaires', 'not found this questionnaire.')),
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
		// いったん公開して、その後の一時保存データに対して編集している
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 'key' => 'questionnaire_2'),
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => '_method', 'value' => 'DELETE'),
		)));

		// セッティングモードからの
		array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => 6, 'q_mode' => 'setting'),
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => '_method', 'value' => 'DELETE'),
		)));
		return $results;
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
		$publishData = $this->__getData('questionnaire_4');
		$publishData = Hash::remove($publishData, 'save_' . WorkflowComponent::STATUS_IN_DRAFT);
		$publishData = Hash::insert($publishData, 'save_' . WorkflowComponent::STATUS_PUBLISHED);
		$publishData = Hash::merge($publishData, array(
			'Questionnaire' => array('status' => WorkflowComponent::STATUS_PUBLISHED)));

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
				'data' => $this->__getData('questionnaire_44'), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
			//--自分の記事(公開)
			array(
				'data' => $this->__getData('questionnaire_12'), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
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
				'data' => $this->__getData('questionnaire_4'), 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
			),
			//--自分の記事(公開)
			array(
				'data' => $publishData,
				'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array(
					'frame_id' => $data['Frame']['id'],
					'block_id' => $data['Block']['id'],
					'action' => $this->_myAction,
					's_id' => 'testSession'),
			),
			//--自分の記事(公開)セッティング画面から
			array(
				'data' => $this->__getData('questionnaire_4'), 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array(
					'frame_id' => $data['Frame']['id'],
					'block_id' => $data['Block']['id'],
					'action' => $this->_myAction,
					'q_mode' => 'setting',
					's_id' => 'testSession'
				),
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
		$dataPeriodOn = Hash::merge($data, array('Questionnaire' => array('answer_timing' => '1')));
		$dataPeriodOn2 = Hash::merge($data, array('Questionnaire' => array('total_show_timing' => '1')));

		$result = array(
			'data' => $data,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
		);
		$resultPeriodOn = array(
			'data' => $dataPeriodOn,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
		);
		$resultPeriodOn2 = array(
			'data' => $dataPeriodOn2,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => $this->_myAction, 's_id' => 'testSession'),
		);

		return array(
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'Questionnaire.title',
					'value' => '',
					'message' => __d('net_commons', 'Please input %s.', __d('questionnaires', 'Title')),
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'Questionnaire.answer_timing',
					'value' => 'aa',
					'message' => __d('net_commons', 'Invalid request.'),
				)
			)),
			Hash::merge($resultPeriodOn, array(
				'validationError' => array(
					'field' => 'Questionnaire.answer_start_period',
					'value' => '2019-08-01 00:00:00',
					'message' => __d('questionnaires', 'start period must be smaller than end period'),
				)
			)),
			Hash::merge($resultPeriodOn, array(
				'validationError' => array(
					'field' => 'Questionnaire.answer_end_period',
					'value' => '2015-08-01 00:00:00',
					'message' => __d('questionnaires', 'start period must be smaller than end period'),
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'Questionnaire.total_show_timing',
					'value' => 'aa',
					'message' => __d('net_commons', 'Invalid request.'),
				)
			)),
			//5
			Hash::merge($resultPeriodOn2, array(
				'validationError' => array(
					'field' => 'Questionnaire.total_show_start_period',
					'value' => '',
					'message' => __d('questionnaires', 'if you set the period, please set time.'),
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'Questionnaire.is_no_member_allow',
					'value' => 'aa',
					'message' => __d('net_commons', 'Invalid request.'),
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'Questionnaire.is_anonymity',
					'value' => 'aa',
					'message' => __d('net_commons', 'Invalid request.'),
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'Questionnaire.is_key_pass_use',
					'value' => 'aa',
					'message' => __d('net_commons', 'Invalid request.'),
				)
			)),
			//Hash::merge($result, array(
			//	'validationError' => array(
			//		'field' => 'Questionnaire.is_repeat_allow',
			//		'value' => 'aa',
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	)
			//)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'Questionnaire.is_image_authentication',
					'value' => 'aa',
					'message' => __d('net_commons', 'Invalid request.'),
				)
			)),
		);
	}
}