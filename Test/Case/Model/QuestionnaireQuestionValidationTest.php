<?php
/**
 * QuestionnaireQuestions Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireQuestions Test Case
 */
class QuestionnaireQuestionValidationTest extends QuestionnaireTestBase {

/**
 * Fixtures
 *
 * @var array
 */
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
		$this->QuestionnaireQuestion->set($data);
		$result = $this->QuestionnaireQuestion->validates();

		//戻り値チェック
		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		//validationErrorsチェック
		$this->assertEquals($this->QuestionnaireQuestion->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'questionnaire_page_id'が数字以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate1() {
		$field = 'questionnaire_page_id';

		//データ生成
		$data = array(
			'questionnaire_page_id' => 'a',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'question_sequence'が数字以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate2() {
		$field = 'question_sequence';

		//データ生成
		$data = array(
			'question_sequence' => 'a',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'question_type'が定義値（1～8）以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate3() {
		$field = 'question_type';

		//データ生成
		$data = array(
			'question_type' => 11,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'question_value'が空の場合(エラー)
 * @return void
 */
	public function testbeforeValidate4() {
		$field = 'question_value';

		//データ生成
		$data = array(
			'question_value' => '',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('questionnaires', 'Please input question text.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'is_require'がboolean以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate5() {
		$field = 'is_require';

		//データ生成
		$data = array(
			'is_require' => 11,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'is_choice_random'がboolean以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate6() {
		$field = 'is_choice_random';

		//データ生成
		$data = array(
			'is_choice_random' => 2,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'is_skip'がboolean以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate7() {
		$field = 'is_skip';

		//データ生成
		$data = array(
			'is_skip' => 2,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'is_result_display'がboolean以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate8() {
		$field = 'is_result_display';

		//データ生成
		$data = array(
			'is_result_display' => 2,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

}
