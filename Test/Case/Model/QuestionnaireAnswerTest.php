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

App::uses('QuestionnaireAnswerTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireAnswer Test Case
 */
class QuestionnaireAnswerTest extends QuestionnaireAnswerTestBase {

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
		$expect = $this->QuestionnaireAnswer->find('count');
		$this->assertEquals($result, $expect);

		//終了処理
		$this->tearDown();
	}

/**
 * saveAnswer method
 *
 * @return void validateエラー
 */
	public function testsaveAnswerErr() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array(
			0 => array(
				'questionnaire_question_origin_id' => 1,
				'matrix_choice_id' => 1,
				'id' => '',
				'answer_value' => 'aaa'),
			'questionnaire_question_origin_id' => 1);
		$question = array(
		'Questionnaire' =>	array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				),
		'QuestionnairePage' => array(
			0 =>	array(
					'id' => 1,
					'key' => '41ef6012e7574886c9a52fb598f8c5f8',
					'language_id' => 1,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 3,
					'questionnaire_id' => 1,
					'page_title' => 'TEST1',
					'page_sequence' => 2,
					'is_auto_translated' => 1,
					'created_user' => 1,
					'created' => '2015-04-13 06:38:28',
					'modified_user' => 1,
					'modified' => '2015-04-13 06:38:28',
					'QuestionnaireQuestion' => array(
						0 => array(
						'id' => 1,
						'key' => 'testkey',
						'language_id' => 0,
						'origin_id' => 1,
						'is_active' => 1,
						'is_latest' => 1,
						'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
						'question_sequence' => 1,
						'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
						'question_type' => 1,
						'description' => 'Lorem ipsum dolor sit amet',
						'is_require' => 1,
						'question_type_option' => QuestionnairesComponent::TYPE_OPTION_NUMERIC,
						'is_choice_random' => 1,
						'is_skip' => 1,
						'is_range' => 1,
						'min' => '1',
						'max' => '3',
						'is_result_display' => 1,
						'result_display_type' => 1,
						'is_auto_translated' => 1,
						'questionnaire_page_id' => 1,
						'created_user' => 1,
						'created' => '2015-04-13 06:39:20',
						'modified_user' => 1,
						'modified' => '2015-04-13 06:39:20',
						'QuestionnaireChoice' => array(
							0 => array(
								'choice_sequence' => 5,
								'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
								'choice_label' => __d('questionnaires', 'new choice') . '1',
								'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
								'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END))), ))), );
		$userId = 1;
		$sessionId = 1;
		$errors = array();
		// 処理実行
		$result = $this->QuestionnaireAnswer->saveAnswer($question, $userId, $sessionId, $data, $errors);
		//print_r($this->QuestionnaireAnswer->validationErrors);

		//validationErrors(選択肢が不正です)
		$this->assertFalse($result);
		$expected = sprintf(__d('questionnaires', 'Invalid choice'));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors['answer_value'][0], $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * saveAnswer method
 *
 * @return void
 */
	public function testsaveAnswerOk() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array(
			0 => array(
				'questionnaire_question_origin_id' => 1,
				'matrix_choice_id' => 1,
				'id' => '',
				'created_user' => 1,
				'created' => '2015-04-13 06:42:33',
				'modified_user' => 1,
				'modified' => '2015-04-13 06:42:33',
				'answer_value' => '2'),
			'questionnaire_question_origin_id' => 1);
		$question = array(
		'Questionnaire' =>	array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1, ),
		'QuestionnairePage' => array(
			0 =>	array(
					'id' => 1,
					'key' => '41ef6012e7574886c9a52fb598f8c5f8',
					'language_id' => 1,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 3,
					'questionnaire_id' => 1,
					'page_title' => 'TEST1',
					'page_sequence' => 2,
					'is_auto_translated' => 1,
					'created_user' => 1,
					'created' => '2015-04-13 06:38:28',
					'modified_user' => 1,
					'modified' => '2015-04-13 06:38:28',
					'QuestionnaireQuestion' => array(
						0 => array(
						'id' => 1,
						'key' => 'testkey',
						'language_id' => 0,
						'origin_id' => 1,
						'is_active' => 1,
						'is_latest' => 1,
						'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
						'question_sequence' => 1,
						'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
						'question_type' => QuestionnairesComponent::TYPE_TEXT,
						'description' => 'Lorem ipsum dolor sit amet',
						'is_require' => 0,
						'question_type_option' => QuestionnairesComponent::TYPE_OPTION_NUMERIC,
						'is_choice_random' => 1,
						'is_skip' => 1,
						'is_range' => 1,
						'min' => '1',
						'max' => '3',
						'is_result_display' => 1,
						'result_display_type' => 1,
						'is_auto_translated' => 1,
						'questionnaire_page_id' => 1,
						'created_user' => 1,
						'created' => '2015-04-13 06:39:20',
						'modified_user' => 1,
						'modified' => '2015-04-13 06:39:20',
			), ))), );
		$userId = 1;
		$sessionId = 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.';
		$errors = array();
		// 処理実行
		$result = $this->QuestionnaireAnswer->saveAnswer($question, $userId, $sessionId, $data, $errors);

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}
/**
 * getProgressiveAnswerOfThisSummary method
 * summaryがempty
 * @return void
 */
	public function testgetProgressiveAnswerOfThisSummary1() {
		//初期処理
		$this->setUp();

		//データの生成
		$summary = '';

		$result = $this->QuestionnaireAnswer->getProgressiveAnswerOfThisSummary( $summary );

		$answers = array();
		// テスト実施
		$this->assertEquals($result, $answers);

		//終了処理
		$this->tearDown();
	}

/**
 * getProgressiveAnswerOfThisSummary method
 * summaryがemptyではない
 * @return void
 */
	public function testgetProgressiveAnswerOfThisSummary2() {
		//初期処理
		$this->setUp();

		//データの生成
		$summary = array();
		$summary['QuestionnaireAnswerSummary']['id'] = 1;

		// 処理実行
		$result = $this->QuestionnaireAnswer->getProgressiveAnswerOfThisSummary( $summary );

		// テスト実施
		$this->assertEquals($result[1][0]['id'], 1);

		//終了処理
		$this->tearDown();
	}

}
