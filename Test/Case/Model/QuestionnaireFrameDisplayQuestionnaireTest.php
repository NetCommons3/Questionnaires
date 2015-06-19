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
class QuestionnaireFrameDisplayQuestionnaireTest extends QuestionnaireTestBase {

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
 * saveFrameDisplayQuestionnaire method
 *
 * @return void
 */
	public function testsaveFrameDisplayQuestionnaire() {
		$this->setUp();

		$frameId = 1;
		$questionnaireId = 1;

		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveFrameDisplayQuestionnaire($frameId, $questionnaireId );

		$this->assertTrue($result);

		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 *
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForList() {
		$this->setUp();

		$frameKey = "aaa";

		$displayQs = array(1, 2);

		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForList($frameKey, $displayQs);

		$this->assertTrue($result);

		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForSingle() {
		$this->setUp();

		$frameKey = "";
		$displayQuestionnaire = 1;

		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForSingle($frameKey, $displayQuestionnaire);

		$this->assertFalse($result);

		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForSingle2() {
		$this->setUp();

		$frameKey = "aaa";
		$displayQuestionnaire = 1;

		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForSingle($frameKey, $displayQuestionnaire);

		$this->assertTrue($result);

		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForList method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaireForList() {
		$this->setUp();

		$frameKey = "aaa";
		$displayQs = array(1, 2);

		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaireForList($frameKey, $displayQs);

		$this->assertTrue($result);

		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaireForSingle() {
		$this->setUp();

		$frameKey = "frame_1";
		$displayQs = 1;

		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaireForSingle($frameKey, $displayQs);

		$this->assertTrue($result);

		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaire() {
		$this->setUp();

		$data = array(
			'frame_key' => 'frame_1',
			'questionnaire_origin_id' => 1,
		);

		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaire($data);

		$this->assertTrue($result);

		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testdeleteDisplayQuestionnaire() {
		$this->setUp();

		$data = array(
			'frame_key' => 'frame_1',
			'questionnaire_origin_id' => 1,
		);

		$result = $this->QuestionnaireFrameDisplayQuestionnaire->deleteDisplayQuestionnaire($data);

		$this->assertTrue($result);

		$this->tearDown();
	}

}
