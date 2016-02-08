<?php
/**
 * QuestionnaireAddController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAddController', 'Questionnaires.Controller');
App::uses('WorkflowControllerAddTest', 'Workflow.TestSuite');

/**
 * FaqQuestionsController Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller\FaqQuestionsController
 */
class QuestionnaireAddControllerAddTest extends WorkflowControllerAddTest {

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
	protected $_controller = 'questionnaire_add';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->Questionnaire->Behaviors->unload('AuthorizationKey');
		$this->ActionQuestionnaireAdd = ClassRegistry::init('Questionnaires.ActionQuestionnaireAdd');
	}

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __getData() {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			//'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
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
			'ActionQuestionnaireAdd' => array(
				'create_option' => 'create',
				'title' => 'New Questionnaire Title',
			),
		);

		return $data;
	}
/**
 * テストDataの取得
 *
 * @return array
 */
	private function __getDataPastReuse() {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			//'save_' . WorkflowComponent::STATUS_IN_DRAFT => null,
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
			'ActionQuestionnaireAdd' => array(
				'create_option' => 'reuse',
				'past_questionnaire_id' => '4',
			),
		);

		return $data;
	}

/**
 * addアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGet() {
		$data = $this->__getData();
		$results = array();

		//ログインなし
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		return $results;
	}

/**
 * addアクションのGETテスト(作成権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderAddGetByCreatable() {
		$data = $this->__getData();
		$results = array();

		//作成権限あり
		$base = 0;
		// 正しいフレームIDとブロックID
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		// フレームIDのhidden-inputがあるか
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		)));
		// ブロックIDのhidden-inputがあるか
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		)));
		// 作成方法選択肢オプションがあるか
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[ActionQuestionnaireAdd][create_option]', 'value' => null),
		)));
		// タイトル入力テキストがあるか
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'text', 'name' => 'data[ActionQuestionnaireAdd][title]', 'value' => null),
		)));
		// 過去再利用の絞込テキスト入力とhiddenがあることを確認する
		// 本当は過去のアンケート一覧が表示されることも確認せねばならないが、それはAngularで展開しているのでphpunitでは確認できないため省略
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'text', 'name' => 'data[ActionQuestionnaireAdd][past_search]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[ActionQuestionnaireAdd][past_questionnaire_id]', 'value' => null),
		)));
		// テンプレートファイル読み込みがあるか
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[ActionQuestionnaireAdd][template_file]', 'value' => null),
		)));

		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => null),
		)));

		return $results;
	}

/**
 * addアクションのPOSTテスト用DataProvider
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
	public function dataProviderAddPost() {
		$data = $this->__getData();

		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
				'exception' => 'ForbiddenException'
			),
			//作成権限あり
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			),
			array(
				'data' => $this->__getDataPastReuse(), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			),
			//フレームID指定なしテスト
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			),
		);
	}

/**
 * addアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderAddValidationError() {
		$data = $this->__getData();
		$result = array(
			'data' => $data,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
		);
		$dataPastReuse = $this->__getDataPastReuse();
		$resultPastReuse = array(
			'data' => $dataPastReuse,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
		);
		//$dataTemplate = $this->__getData();
		//$dataTemplate['ActionQuestionnaireAdd']['create_option'] = 'template';
		//$resultTemplate = array(
		//	'data' => $dataTemplate,
		//	'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
		//);

		return array(
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'ActionQuestionnaireAdd.create_option',
					'value' => null,
					'message' => sprintf(__d('questionnaires', 'Please choose create option.'))
				)
			)),
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'ActionQuestionnaireAdd.title',
					'value' => '',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('questionnaires', 'Title'))
				)
			)),
			Hash::merge($resultPastReuse, array(
				'validationError' => array(
					'field' => 'ActionQuestionnaireAdd.past_questionnaire_id',
					'value' => '',
					'message' => sprintf(__d('questionnaires', 'Please select past questionnaire.'))
				)
			)),
			Hash::merge($resultPastReuse, array(
				'validationError' => array(
					'field' => 'ActionQuestionnaireAdd.past_questionnaire_id',
					'value' => '9999999',
					'message' => sprintf(__d('questionnaires', 'Please select past questionnaire.'))
				)
			)),
			//Hash::merge($resultTemplate, array(
			//	'validationError' => array(
			//		'field' => 'ActionQuestionnaireAdd.template_file',
			//		'value' => null,
			//		'message' => sprintf(__d('questionnaires', 'file upload error.'))
			//	)
			//)),
		);
	}
}
