<?php
/**
 * QuestionnaireAnswerValidationMatrix Test Case
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
class QuestionnaireAnswerValidationMatrixTest extends QuestionnaireAnswerTestBase {

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
 * validate method
 *  validate試験'question_type'が「マトリクス」の場合（値チェック、「その他」入力チェック）
 * @return void  （異常値が入力されていた場合エラー）
 */

	public function testcheckAnswerValue6() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'answer_value' => 'aaa',
			'other_answer_value' => '',
			'matrix_answer_values' => array( 1 => 'その他'),
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		$question = array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => '',
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '',
				'max' => '',
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
				'id' => 1,
				'origin_id' => 1,
				'choice_sequence' => 1,
				'matrix_type' => 0, //QuestionnairesComponent::MATRIX_TYPE_COLUMN,
				'choice_label' => 'その他',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
				'choice_value' => 'その他',
				'questionnaire_question_id' => 2,
			),
		));

		$answers = array();

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);

		// テスト実施
		//validationErrorsチェック'選択が不正です'
		$expected = array(
				'answer_value' => array(sprintf(__d('questionnaires', 'Invalid choice'))
				));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'question_type'が「マトリクス」の場合（値チェック、「その他」入力チェック）
 * @return void  （回答の行番号が不正の場合エラー）
 */

	public function testcheckAnswerValue7() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 3, //存在しない（エラー）
			'answer_value' => '1',
			'other_answer_value' => '',
			'matrix_answer_values' => array( 1 => 'その他'),
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		$question = array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => '',
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '',
				'max' => '',
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
				'id' => 1,
				'origin_id' => 1,
				'choice_sequence' => 1,
				'matrix_type' => 0, //QuestionnairesComponent::MATRIX_TYPE_COLUMN,
				'choice_label' => 'その他',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
				'choice_value' => 'その他',
				'questionnaire_question_id' => 2,
			),
		));

		$answers = array();

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);

		// テスト実施
		//validationErrorsチェック'選択が不正です'
		$expected = array(
				'answer_value' => array(sprintf(__d('questionnaires', 'Invalid choice'))
				));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'question_type'が「マトリクス」の場合（値チェック、「その他」入力チェック）
 * @return void  （その他を指定時に'other_answer_value'が空欄の場合エラー）
 */

	public function testcheckAnswerValue8() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'answer_value' => '|1:その他',
			'other_answer_value' => '',
			'matrix_answer_values' => array( 1 => 'その他'),
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		$question = array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => '',
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '',
				'max' => '',
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
				'id' => 1,
				'origin_id' => 1,
				'choice_sequence' => 1,
				'matrix_type' => 0, //QuestionnairesComponent::MATRIX_TYPE_COLUMN,
				'choice_label' => 'その他',
				'other_choice_type' => 1, //QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
				'choice_value' => 'その他',
				'questionnaire_question_id' => 2,
			),
		));

		$answers = array();

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);

		// テスト実施
		$this->assertFalse($result);

		//validationErrorsチェック'その他を選択する場合は、内容を入力してください。'
		$expected = array(
				'answer_value' => array(
					__d('questionnaires', 'Please enter something in other item')
				));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'question_type'が「マトリクス」の場合）
 * @return void  （全行無回答ではなく、回答していない行がある場合エラー）
 */

	public function testcheckAnswerValue9() {
		//初期処理
		$this->setUp();
		//データ生成
		$data = array(
			'id' => 2,
			'matrix_choice_id' => '1',
			'answer_value' => '1',
			'other_answer_value' => '',
			'matrix_answer_values' => array(
				1 => '新規選択肢1', 2 => '' ),
			'questionnaire_answer_summary_id' => 2,
			'questionnaire_question_origin_id' => 2,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33');
		$question = array(
				'id' => 2,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 2,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 0,
				'question_type_option' => '',
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '',
				'max' => '',
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
				'id' => 1,
				'origin_id' => 1,
				'choice_sequence' => 0,
				'matrix_type' => 0, //QuestionnairesComponent::MATRIX_TYPE_COLUMN,
				'choice_label' => '新規選択肢1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
				'choice_value' => '新規選択肢1',
				'questionnaire_question_id' => 2, ),
			1 => array(
				'id' => 2,
				'origin_id' => 2,
				'choice_sequence' => 1,
				'matrix_type' => 0, //QuestionnairesComponent::MATRIX_TYPE_COLUMN,
				'choice_label' => 'その他',
				'other_choice_type' => 1,
				'choice_value' => 'その他',
				'questionnaire_question_id' => 2, ),
			2 => array(
				'id' => 3,
				'origin_id' => 3,
				'choice_sequence' => 2,
				'matrix_type' => 1, //QuestionnairesComponent::MATRIX_TYPE_COLUMN,
				'choice_label' => '新規選択肢（列）1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
				'choice_value' => '新規選択肢（列）1',
				'questionnaire_question_id' => 2, ), ));
		$answers = array(
		0 => array(
			'questionnaire_question_origin_id' => 1,
			'matrix_choice_id' => 1,
			'id' => '',
			'answer_value' => '|3:新規選択肢（列）1'),
		1 => array(
			'questionnaire_question_origin_id' => 1,
			'matrix_choice_id' => 2,
			'id' => '',
			'answer_value' => ''),
		2 => array(
			'questionnaire_question_origin_id' => 1,
			'matrix_choice_id' => 2,
			'answer_value' => ''));
		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);
		// テスト実施
		$this->assertFalse($result);
		//validationErrorsチェック'回答するときは全ての行に対して回答をしてください。'
		$expected = array(
				'answer_value' => array( __d('questionnaires', 'Please answer about all rows.')));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

}
