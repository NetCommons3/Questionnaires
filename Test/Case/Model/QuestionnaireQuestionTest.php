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
class QuestionnaireQuestionTest extends QuestionnaireTestBase {

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
 *  behavior試験'question_type'がTYPE_TEXT,TYPE_TEXT_AREA,TYPE_DATE_AND_TIMEのとき'is_result_display'が1だとエラー
 * @return void
 */
	public function testbeforeValidate1() {
		$field = 'is_result_display';

		//データ生成
		$data = array(
			'is_result_display' => 1,
			'question_type' => QuestionnairesComponent::TYPE_TEXT,
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
 *  'result_display_type'が定義値以外の場合エラー
 * @return void
 */
	public function testbeforeValidate2() {
		$field = 'result_display_type';

		//データ生成
		$data = array(
			'result_display_type' => 5,
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
 *  'min'チェック('question_type_option'が数値の場合、数値以外でエラー)（behavior）
 * @return void
 */
	public function testbeforeValidate3() {
		$field = 'min';

		//データ生成
		$data = array(
			'min' => 'a',
			//'max' => 5,
			'question_type_option' => QuestionnairesComponent::TYPE_OPTION_NUMERIC,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('questionnaires', 'Please enter both the maximum and minimum values.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  'max'チェック('question_type_option'が日付の場合、日付以外でエラー)（behavior）
 * @return void
 */
	/*
	public function testbeforeValidate4() {
		$field = 'max';

		//データ生成
		$data = array(
			'min' => '2015-04-13 00:00:00',
			'max' => 5,
			'question_type_option' => QuestionnairesComponent::TYPE_OPTION_DATE,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('questionnaires', 'Please enter both the maximum and minimum values.')));

		//テスト実施
		//??		$this->__assertValidationError($field, $data, $expected);
	}
	*/
}
