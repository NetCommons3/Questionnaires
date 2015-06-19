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
class QuestionnaireBehaviorTest extends QuestionnaireTestBase {

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
		$field = 'is_period';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '',
			'end_period' => '',
			'is_period' => 1,
		);
		$expected = array(
				$field => array( __d('questionnaires', 'if you set the answer period, please set start time or end time or both time.')));

		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 * @return void
 */
	public function testbeforeValidate2() {
		$field = 'is_key_pass_use';

		$data = array(
			'block_id' => 1,
			'is_auto_translated' => 1,
			'title' => 'title',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'start_period' => '2015-06-04 11:45:22',
			'end_period' => '2015-06-04 11:45:22',
			'is_period' => 0,
			'is_key_pass_use' => 1,
			'key_phrase' => '',
		);
		$expected = array(
				$field => array(__d('questionnaires', 'if you set the use key phrase period, please set key phrase text.')));

		$this->__assertValidationError($field, $data, $expected);
	}

}
