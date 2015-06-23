<?php
/**
 * QuestionnaireFrameDisplayQuestionnaire Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireFrameDisplayQuestionnaire Test Case
 */
class QuestionnaireFrameDisplayQuestionnaireValidationTest extends QuestionnaireTestBase {

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
		//初期設定
		$this->setUp();

		//validate処理実行
		$this->QuestionnaireFrameDisplayQuestionnaire->set($data);
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validates();

		//戻り値チェック
		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		//validationErrorsチェック
		$this->assertEquals($this->QuestionnaireFrameDisplayQuestionnaire->validationErrors, $expected);

		//処理終了
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'frame_key'が空白の場合（エラー）
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
 * validate method
 *  validate試験'questionnaire_origin_id'が数字以外(エラー)
 * @return void
 */
	public function testValidate2() {
		$field = 'questionnaire_origin_id';

		//データ生成
		$data = array(
			'frame_key' => 'aaa',
			'questionnaire_origin_id' => 'a',
		);

		//期待値
		$expected = array(
			$field => array(__('numeric')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

}
