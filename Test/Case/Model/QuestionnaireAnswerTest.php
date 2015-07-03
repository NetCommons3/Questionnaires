<?php
/**
 * QuestionnaireAnswer Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireAnswer Test Case
 */
class QuestionnaireAnswerTest extends QuestionnaireTestBase {

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
 * afterFind method
 *
 * @return void
 */
	public function testafterFind() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
		0 => array(
		'QuestionnaireAnswer' => array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'answer_value' => '|1:test1',
			'other_answer_value' => '',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		)));

		// 処理実行
		$this->QuestionnaireAnswer->set($data);
		$results = $this->QuestionnaireAnswer->afterFind($data);

		// テスト実施（'answer_value'の値[id:value|id:value....]の形が配列で設定される）
		$this->assertEquals($results[0]['QuestionnaireAnswer']['answer_values'][1], 'test1');

		//終了処理
		$this->tearDown();
	}

/**
 * beforeSave method
 *
 * @return void
 */
	public function testbeforeSave() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'multi_answer_values' => '|1:新規選択肢1',
			'answer_value' => '',
			'other_answer_value' => '',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->beforeSave();

		$this->assertTrue($result);
		$this->assertEquals($this->QuestionnaireAnswer->data['QuestionnaireAnswer']['answer_value'], $data['multi_answer_values']);

		//終了処理
		$this->tearDown();
	}

/**
 * getAnswerCount method
 *
 * @return void
 */
	public function testgetAnswerCount() {
		//初期処理
		$this->setUp();

		//データ生成
		$conditions = array(
			'QuestionnaireAnswer.questionnaire_question_origin_id' => 1);

		// 処理実行
		$this->QuestionnaireAnswer->unbindModel(array('belongsTo' => array('QuestionnaireAnswerSummary')));
		//↑エラーのため：PDOException: SQLSTATE[42000]: Syntax error or access violation: 1066 Not unique table/alias: 'QuestionnaireAnswerSummary'
		$result = $this->QuestionnaireAnswer->getAnswerCount($conditions);

		// テスト実施
		$this->assertEquals($result, 1);

		//終了処理
		$this->tearDown();
	}

/**
 * saveAnswer method
 *
 * @return void
 */
	public function testsaveAnswer() {
		//初期処理
		$this->setUp();

		//データ生成

		// 処理実行
		//PENDING $this->QuestionnaireAnswer->saveAnswer($question, $userId, $sessionId, $data, $errors);

		//終了処理
		$this->tearDown();
	}

}
