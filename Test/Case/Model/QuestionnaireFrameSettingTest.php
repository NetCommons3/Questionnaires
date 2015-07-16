<?php
/**
 * QuestionnaireFrameSetting Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireFrameSetting Test Case
 */
class QuestionnaireFrameSettingTest extends QuestionnaireTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * __assertValidationError
 *
 * @param string $field Field name
 * @param array $data Save data
 * @param array $expected Expected value
 * @return void
 */
	private function __assertValidationError($field, $data, $expected) {
		//初期処理
		$this->setUp();

		//validate処理実行
		$this->QuestionnaireFrameSetting->set($data);
		$result = $this->QuestionnaireFrameSetting->validates();

		//戻り値チェック
		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		//validationErrorsチェック
		$this->assertEquals($this->QuestionnaireFrameSetting->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'frame_key'が空白の場合(エラー)
 * @return void
 */
	public function testValidate1() {
		$field = 'frame_key';

		//データ生成
		$data = array(
			'frame_key' => '',
			'questionnaire_origin_id' => 1,
		);

		//期待値
		$expected = array(
					$field => array(__('notEmpty')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * prepareBlock method
 *
 * @return void
 */
	public function testprepareBlock() {
		//初期処理
		$this->setUp();

		// frame fixture id = 11 のframeはblock未設定
		$frameId = 11;

		// 処理実行
		$this->QuestionnaireFrameSetting->prepareBlock($frameId);

		// フレーム取り出し
		$frame = $this->Frame->findById($frameId);
		// フレームにブロックIDが設定されているか
		$this->assertInternalType('string', $frame['Frame']['block_id']);

		// ブロック取り出し
		$block = $this->Block->findById($frame['Frame']['block_id']);
		// ブロックは新たに生成されているか
		$this->assertInternalType('array', $block['Block']);

		//終了処理
		$this->tearDown();
	}

/**
 * prepareFrameSetting method
 *
 * @return void
 */
	public function testprepareFrameSetting() {
		//初期処理
		$this->setUp();

		$frameKey = 'frame_10';
		//期待値の生成
		$expected = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => QuestionnairesComponent::DISPLAY_TYPE_LIST,
				'display_num_per_page' => QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
				'sort_type' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
				'frame_key' => $frameKey,
			)
		);

		// 処理実行
		$this->QuestionnaireFrameSetting->prepareFrameSetting($frameKey);

		// テスト実施
		$result = $this->QuestionnaireFrameSetting->findByFrameKey($frameKey);
		$this->_assertArray(
			$expected['QuestionnaireFrameSetting'],
			$result['QuestionnaireFrameSetting'],
			1,
			['id', 'created', 'created_user', 'modified', 'modified_user']
		);

		//終了処理
		$this->tearDown();
	}

/**
 * saveFrameSettings method
 *
 * @return void
 */
	public function testsaveFrameSettings() {
		//初期処理
		$this->setUp();

		$frameKey = 'frame_1';

		//データの生成
		$records = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => 1,
				'display_num_per_page' => 1,
				'sort_type' => 1,
			),
			'QuestionnaireFrameDisplayQuestionnaires' => array(
				'List' => array(
					'questionnaire_origin_id' => array(1),
				)
			)
		);
		// 処理実行
		$this->QuestionnaireFrameSetting->saveFrameSettings($frameKey, $records);

		$result = $this->QuestionnaireFrameSetting->findByFrameKey($frameKey);

		// テスト実施
		$this->_assertArray(
			$records['QuestionnaireFrameSetting'],
			$result['QuestionnaireFrameSetting'],
			1,
			['id', 'frame_key', 'created', 'created_user', 'modified', 'modified_user']
		);

		//終了処理
		$this->tearDown();
	}
}
