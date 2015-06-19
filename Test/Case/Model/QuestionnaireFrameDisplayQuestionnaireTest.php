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
		//��������
		$this->setUp();

		//�f�[�^�̐���
		$frameId = 1;
		$questionnaireId = 1;

		// �������s
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveFrameDisplayQuestionnaire($frameId, $questionnaireId );

		// �e�X�g���{
		$this->assertTrue($result);

		//�I������
		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 *
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForList() {
		//��������
		$this->setUp();

		$frameKey = "aaa";

		$displayQs = array(1, 2);

		// �������s
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForList($frameKey, $displayQs);

		// �e�X�g���{
		$this->assertTrue($result);

		//�I������
		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 * �t���[���L�[��NULL�̏ꍇ,FALSE
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForSingle() {
		//��������
		$this->setUp();

		$frameKey = "";
		$displayQuestionnaire = 1;

		// �������s
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForSingle($frameKey, $displayQuestionnaire);

		// �e�X�g���{
		$this->assertFalse($result);

		//�I������
		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 * �t���[���L�[������̏ꍇ,TRUE
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForSingle2() {
		//��������
		$this->setUp();

		$frameKey = "aaa";
		$displayQuestionnaire = 1;

		// �������s
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForSingle($frameKey, $displayQuestionnaire);

		// �e�X�g���{
		$this->assertTrue($result);

		//�I������
		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForList method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaireForList() {
		//��������
		$this->setUp();

		$frameKey = "aaa";
		$displayQs = array(1, 2);

		// �������s
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaireForList($frameKey, $displayQs);

		//�e�X�g���{
		$this->assertTrue($result);

		//�I������
		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaireForSingle() {
		//��������
		$this->setUp();

		$frameKey = "frame_1";
		$displayQs = 1;

		// �������s
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaireForSingle($frameKey, $displayQs);

		//�e�X�g���{(save����)
		$this->assertTrue($result);

		//�I������
		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaire() {
		//��������
		$this->setUp();

		//�f�[�^����
		$data = array(
			'frame_key' => 'frame_1',
			'questionnaire_origin_id' => 1,
		);

		// �������s
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaire($data);

		//�e�X�g���{(save����)
		$this->assertTrue($result);

		//�I������
		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testdeleteDisplayQuestionnaire() {
		//��������
		$this->setUp();

		//�f�[�^����
		$data = array(
			'frame_key' => 'frame_1',
			'questionnaire_origin_id' => 1,
		);

		// �������s
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->deleteDisplayQuestionnaire($data);

		//�e�X�g���{
		$this->assertTrue($result);

		//�I������
		$this->tearDown();
	}

}
