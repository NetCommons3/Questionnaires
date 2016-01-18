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
 * @package NetCommons\Bbses\Test\Case\Controller\QuestionnaireEditController
 */
class QuestionnaireEditControllerViewTest extends WorkflowControllerEditTest {

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
	protected $_controller = 'questionnaire_edit';

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
				'public_type' => 0,
				'is_key_pass_use' => 0,
				'total_show_timing' => 0,
			),
			'QuesitonnairePage' => array(
				'page_title' => __d('questionnaires', 'First Page'),
				'page_sequence' => 0,
				'route_number' => 0,
				'QuestionnaireQuestion' => array(
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
						'choice_sequence' => 0,
						'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
						'choice_label' => __d('questionnaires', 'new choice') . '1',
						'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
						'graph_color' => '#FF0000',
						'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END
					)
				)
			),
			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test'
			),
		);

		if ($questionnaireKey) {
			$getQuestionnaire = $this->Questionnaire->find('first', array(
				'conditions' => array(
					'key' => $questionnaireKey,
					'language_id' => 2,
					'is_latest' => 1,
				),
			));
			$data = Hash::marge($data, $getQuestionnaire);
		}
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
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'edit_question', 'key' => 'questionnaire_42'),
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
		//--他人の記事
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'edit_question', 'key' => 'questionnaire_42'),
			'assert' => null,
			'exception' => 'BadRequestException'
		);
		//--自分の記事(一度も公開していない)
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'edit_question', 'key' => 'questionnaire_44'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//--自分の記事(一度も公開していない)
		$results[2] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'edit_question', 'key' => 'questionnaire_44'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('questionnaires', 'Add Question')),
		);
		//新規作成
		$results[3] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'action' => 'edit_question', 's_id' => 'testSession'),
			'assert' => array('method' => 'assertNotEmpty'),
		);

		//$results[3] = Hash::merge($results[2], array(
		//	'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'delete', 'value' => null),
		//));

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
		$results = array();

		//編集権限あり
		//--コンテンツあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'questionnaire_42'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		/*array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'delete', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_IN_DRAFT, 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_APPROVED, 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[BbsArticle][id]', 'value' => $data['BbsArticle']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[BbsArticle][key]', 'value' => $data['BbsArticle']['key']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[BbsArticle][title]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'textarea', 'name' => 'data[BbsArticle][content]', 'value' => null),
		)));
		//--コンテンツなし
		$results[count($results)] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => null, 'return' => 'viewFile'
		);*/

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
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => 'questionnaire_2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		/*array_push($results, Hash::merge($results[0], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => 'bbs_article_2'),
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => null),
		)));
		array_push($results, Hash::merge($results[0], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'delete', 'value' => null),
		)));*/

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
		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'questionnaire_44'),
				'exception' => 'ForbiddenException'
			),
			/*//作成権限のみ
			//--他人の記事
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
				'exception' => 'BadRequestException'
			),
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
				'exception' => 'BadRequestException', 'return' => 'json'
			),
			//--自分の記事(一度も公開していない)
			array(
				'data' => $this->__getData(Role::ROOM_ROLE_KEY_GENERAL_USER, 'bbs_article_4'), 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'bbs_article_4'),
			),
			//編集権限あり
			//--コンテンツあり
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
			),
			//フレームID指定なしテスト
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => $data['BbsArticle']['key']),
			),*/
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
		// この情報をセッション記載
		// セッションモック設定

		$result = array(
			'data' => $data,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 's_id' => 'testSession'),
		);

		return array(
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'Questionnaire.title',
					'value' => '',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('questionnaires', 'Title'))
				)
			)),
			/*Hash::merge($result, array(
				'validationError' => array(
					'field' => 'BbsArticle.content',
					'value' => '',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Content'))
				)
			)),*/
		);
	}
}