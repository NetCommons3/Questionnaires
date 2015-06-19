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
		//初期処理
		$this->setUp();

		//データの生成
		$frameId = 1;
		$questionnaireId = 1;

		// 処理実行
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveFrameDisplayQuestionnaire($frameId, $questionnaireId );

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 *
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForList() {
		//初期処理
		$this->setUp();

		$frameKey = "aaa";

		$displayQs = array(1, 2);

		// 処理実行
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForList($frameKey, $displayQs);

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 * フレームキーがNULLの場合,FALSE
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForSingle() {
		//初期処理
		$this->setUp();

		$frameKey = "";
		$displayQuestionnaire = 1;

		// 処理実行
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForSingle($frameKey, $displayQuestionnaire);

		// テスト実施
		$this->assertFalse($result);

		//終了処理
		$this->tearDown();
	}

/**
 * validateDisplayQuestionnaire method
 * フレームキーが正常の場合,TRUE
 * @return void
 */
	public function testvalidateDisplayQuestionnaireForSingle2() {
		//初期処理
		$this->setUp();

		$frameKey = "aaa";
		$displayQuestionnaire = 1;

		// 処理実行
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForSingle($frameKey, $displayQuestionnaire);

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForList method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaireForList() {
		//初期処理
		$this->setUp();

		$frameKey = "aaa";
		$displayQs = array(1, 2);

		// 処理実行
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaireForList($frameKey, $displayQs);

		//テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaireForSingle() {
		//初期処理
		$this->setUp();

		$frameKey = "frame_1";
		$displayQs = 1;

		// 処理実行
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaireForSingle($frameKey, $displayQs);

		//テスト実施(save成功)
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testsaveDisplayQuestionnaire() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array(
			'frame_key' => 'frame_1',
			'questionnaire_origin_id' => 1,
		);

		// 処理実行
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaire($data);

		//テスト実施(save成功)
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 * saveDisplayQuestionnaireForSingle method
 *
 * @return void
 */
	public function testdeleteDisplayQuestionnaire() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array(
			'frame_key' => 'frame_1',
			'questionnaire_origin_id' => 1,
		);

		// 処理実行
		$result = $this->QuestionnaireFrameDisplayQuestionnaire->deleteDisplayQuestionnaire($data);

		//テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

}
