<?php
/**
 * QuestionnairePage Validation Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnairePage Test Case
 */
class QuestionnairePageValidationTest extends QuestionnaireTestBase {

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
		$this->QuestionnairePage->set($data);
		$result = $this->QuestionnairePage->validates();

		//戻り値チェック
		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		//validationErrorsチェック
		$this->assertEquals($this->QuestionnairePage->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 * @return void
 */
	public function testValidate1() {
		$field = 'origin_id';
		$data = array(
			'id' => 1,
			'origin_id' => 'a',
			'is_active' => 1,
		);
		$expected = array(
			$field => array(__('numeric')));
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testValidate2() {
		$field = 'is_active';

		$data = array(
			'id' => 1,
			'origin_id' => 1,
			'is_active' => 5,

		);
		$expected = array(
			$field => array(__('boolean')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testValidate3() {
		$field = 'is_latest';

		$data = array(
			'id' => 1,
			'origin_id' => 1,
			'is_latest' => 5,

		);
		$expected = array(
			$field => array(__('boolean')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testValidate4() {
		$field = 'questionnaire_id';

		$data = array(
			'id' => 1,
			'origin_id' => 1,
			'questionnaire_id' => 'a',

		);
		$expected = array(
			$field => array(__('numeric')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testValidate5() {
		$field = 'page_sequence';

		$data = array(
			'id' => 1,
			'origin_id' => 1,
			'page_sequence' => 'a',

		);
		$expected = array(
			$field => array(__('numeric')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testValidate6() {
		$field = 'is_auto_translated';

		$data = array(
			'id' => 1,
			'origin_id' => 1,
			'is_auto_translated' => 3,

		);
		$expected = array(
			$field => array(__('boolean')));

		$this->__assertValidationError($field, $data, $expected);
	}

}