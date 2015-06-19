<?php
/**
 * Questionnaire Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

//require_once 'Questionnaire.php';

/**
 * Summary for Questionnaire Test Case
 */
class QuestionnaireValidationTest extends QuestionnaireTestBase {

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
		$this->setUp();

		$this->Questionnaire->set($data);
		$result = $this->Questionnaire->validates();

		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		$this->assertEquals($this->Questionnaire->validationErrors, $expected);

		$this->tearDown();
	}

/**
 * validate method
 * @return void
 */
	public function testbeforeValidate1() {
		$field = 'block_id';

		$data = array(
			'title' => 'test1',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'block_id' => 'a',
		);

		$expected = array(
				$field => array(
				__d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}
/**
 * validate method
 * @return void
 */
	public function testbeforeValidate2() {
		$field = 'is_auto_translated';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 3,
			'title' => 'test1',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		$expected = array(
				$field => array(
					__d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testbeforeValidate3() {
		$field = 'title';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => '',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);
		$expected = array(
				$field => array( sprintf( __d('net_commons', 'Please input %s.'), __d('questionnaires', 'Title'))));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testbeforeValidate4() {
		$field = 'is_period';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'is_period' => 'a',
		);
		$expected = array(
				$field => array( __d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testbeforeValidate5() {
		$field = 'start_period';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => 'aaa',
			'end_period' => '',
			'is_period' => 0,
		);
		$expected = array(
				$field => array( __d('questionnaires', 'Invalid datetime format.')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testbeforeValidate6() {
		$field = 'end_period';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => 'aaa',
			'is_period' => 0,
		);
		$expected = array(
				$field => array( __d('questionnaires', 'Invalid datetime format.')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testbeforeValidate7() {
		$field = 'total_show_start_period';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => '2015-06-04 11:45:22',
			'is_period' => 0,
			'total_show_start_period' => 'a',
		);
		$expected = array(
				$field => array( __d('questionnaires', 'Invalid datetime format.')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testbeforeValidate8() {
		$field = 'is_no_member_allow';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => '2015-06-04 11:45:22',
			'is_period' => 0,
			'is_no_member_allow' => 3,
		);
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
		/*
	public function testbeforeValidate9() {
		$field = 'is_anonymity';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => '2015-06-04 11:45:22',
			'is_period' => 0,
			'is_anonymity' => 2,
		);
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}
		*/

/**
 * validate method
 * @return void
 */
		/*
	public function testbeforeValidate10() {
		$field = 'is_key_pass_use';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => '2015-06-04 11:45:22',
			'is_period' => 0,
			'is_key_pass_use' => 2,
		);
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}
		*/

/**
 * validate method
 * @return void
 */
		/*	public function testbeforeValidate11() {
		$field = 'is_repeat_allow';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => '2015-06-04 11:45:22',
			'is_period' => 0,
			'is_repeat_allow' => 2,
		);
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}
		*/
/**
 * validate method
 * @return void
 */
		/*	public function testbeforeValidate12() {
		$field = 'is_image_authentication';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => '2015-06-04 11:45:22',
			'is_period' => 0,
			'is_image_authentication' => 2,
		);
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}
		*/
/**
 * validate method
 * @return void
 */
		/*
	public function testbeforeValidate13() {
		$field = 'is_answer_mail_send';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => '2015-06-04 11:45:22',
			'is_period' => 0,
			'is_answer_mail_send' => 2,
		);
		$expected = array(
				$field => array(__d('net_commons', 'Invalid request.')));

		$this->__assertValidationError($field, $data, $expected);
	}
		*/
}
