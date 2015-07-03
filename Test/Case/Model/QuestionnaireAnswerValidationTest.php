<?php
/**
 * QuestionnaireAnswerValidation Test Case
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
class QuestionnaireAnswerValidationTest extends QuestionnaireTestBase {

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
		$this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->validates();

		//戻り値チェック
		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		//validationErrorsチェック
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'questionnaire_answer_summary_id'が数字以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate1() {
		$field = 'questionnaire_answer_summary_id';

		//データ生成
		$data = array(
			'questionnaire_answer_summary_id' => 'a',
		);

		//期待値
		$expected = array(
				$field => array(__('numeric')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'questionnaire_question_origin_id'が数字以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate2() {
		$field = 'questionnaire_question_origin_id';

		//データ生成
		$data = array(
			'questionnaire_question_origin_id' => 'a',
		);

		//期待値
		$expected = array(
				$field => array(__('numeric')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'answer_value'必須回答が入力されていない場合エラー
 * @return void
 */
	public function testcheckAnswerValue() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'answer_value' => '',
			'other_answer_value' => '',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		$question = array(
				'id' => 2,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => 1,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => 1,
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '2',
				'max' => '3',
				'is_result_display' => 1,
				'result_display_type' => 1,
				'is_auto_translated' => 1,
				'questionnaire_page_id' => 1,
				'created_user' => 1,
				'created' => '2015-04-13 06:39:20',
				'modified_user' => 1,
				'modified' => '2015-04-13 06:39:20',
				'question_count' => 0,
		'QuestionnaireChoice' =>
			array(
				'choice_sequence' => 5,
				'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
				'choice_label' => __d('questionnaires', 'new choice') . '1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED
			)
		);

		$answers = array();

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);

		// テスト実施
		$this->assertFalse($result);

		//validationErrorsチェック
		$expected = array(
				'answer_value' => array(__d('questionnaires', 'Input required')));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'question_type'が「1行テキスト」(数字タイプ)の場合（範囲外チェック）
 * @return void （範囲外エラー）
 */
	public function testcheckAnswerValue2() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'answer_value' => '12a',
			'other_answer_value' => '',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		$question = array(
				'id' => 2,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => QuestionnairesComponent::TYPE_TEXT,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => 1,
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '2',
				'max' => '3',
				'is_result_display' => 1,
				'result_display_type' => 1,
				'is_auto_translated' => 1,
				'questionnaire_page_id' => 1,
				'created_user' => 1,
				'created' => '2015-04-13 06:39:20',
				'modified_user' => 1,
				'modified' => '2015-04-13 06:39:20',
				'question_count' => 0,
		'QuestionnaireChoice' =>
			array(
				'choice_sequence' => 5,
				'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
				'choice_label' => __d('questionnaires', 'new choice') . '1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED
			)
		);

		$answers = array();

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);

		// テスト実施
		$this->assertFalse($result);

		//validationErrorsチェック
		$type = 'number';
		$expected = array(
				'answer_value' => array(
				__d('questionnaires', 'Number required'),
				sprintf(__d('questionnaires', 'Please enter the %s between %s and %s.', $type, $question['min'], $question['max'])))
				);
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'question_type'が「日付回答」の場合（フォーマットチェック、範囲チェック）
 * @return void  （'question_type_option'が日付でフォーマット不正、範囲外でエラー）
 */
	public function testcheckAnswerValueDate() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'answer_value' => '123',
			'other_answer_value' => '',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		$question = array(
				'id' => 2,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => QuestionnairesComponent::TYPE_DATE_AND_TIME,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => QuestionnairesComponent::TYPE_OPTION_DATE,
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '2015-04-15',
				'max' => '2015-06-30',
				'is_result_display' => 1,
				'result_display_type' => 1,
				'is_auto_translated' => 1,
				'questionnaire_page_id' => 1,
				'created_user' => 1,
				'created' => '2015-04-13 06:39:20',
				'modified_user' => 1,
				'modified' => '2015-04-13 06:39:20',
				'question_count' => 0,
		'QuestionnaireChoice' =>
			array(
				'choice_sequence' => 5,
				'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
				'choice_label' => __d('questionnaires', 'new choice') . '1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED
			)
		);

		$answers = array();

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->validates();
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);

		// テスト実施
		$this->assertFalse($result);

		//validationErrorsチェック
		$type = 'date';
		$expected = array(
				'answer_value' => array(
					sprintf(__d('questionnaires', 'Please enter a valid date in YY-MM-DD format.')),
					sprintf(__d('questionnaires', 'Please enter the %s between %s and %s.', $type, $question['min'], $question['max']))
				));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'question_type'が「日付回答」の場合（フォーマットチェック）
 * @return void  （'question_type_option'が時間でフォーマット不正でエラー）
 */
	public function testcheckAnswerValueTime() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'answer_value' => '123',
			'other_answer_value' => '',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		$question = array(
				'id' => 2,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => QuestionnairesComponent::TYPE_DATE_AND_TIME,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => QuestionnairesComponent::TYPE_OPTION_TIME,
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '2015-04-15',
				'max' => '2015-06-30',
				'is_result_display' => 1,
				'result_display_type' => 1,
				'is_auto_translated' => 1,
				'questionnaire_page_id' => 1,
				'created_user' => 1,
				'created' => '2015-04-13 06:39:20',
				'modified_user' => 1,
				'modified' => '2015-04-13 06:39:20',
				'question_count' => 0,
		'QuestionnaireChoice' =>
			array(
				'choice_sequence' => 5,
				'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
				'choice_label' => __d('questionnaires', 'new choice') . '1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED
			)
		);

		$answers = array();

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->validates();
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);

		// テスト実施
		$this->assertFalse($result);

		//validationErrorsチェック
		$expected = array(
				'answer_value' => array(
					sprintf(__d('questionnaires', 'Please enter the time.'))
				));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'question_type'が「日付時間」の場合（フォーマットチェック）
 * @return void  （'question_type_option'が日付時間でフォーマット不正でエラー）
 */
	public function testcheckAnswerValueDateTime() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array();
		$data = array(
			'id' => 1,
			'matrix_choice_id' => 1,
			'answer_value' => '123',
			'other_answer_value' => '',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_origin_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:42:33',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:42:33'
		);

		$question = array(
				'id' => 2,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => QuestionnairesComponent::TYPE_DATE_AND_TIME,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => QuestionnairesComponent::TYPE_OPTION_DATE_TIME,
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '2015-04-15',
				'max' => '2015-06-30',
				'is_result_display' => 1,
				'result_display_type' => 1,
				'is_auto_translated' => 1,
				'questionnaire_page_id' => 1,
				'created_user' => 1,
				'created' => '2015-04-13 06:39:20',
				'modified_user' => 1,
				'modified' => '2015-04-13 06:39:20',
				'question_count' => 0,
		'QuestionnaireChoice' =>
			array(
				'choice_sequence' => 5,
				'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
				'choice_label' => __d('questionnaires', 'new choice') . '1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED
			)
		);

		$answers = array();

		// 処理実行
		$result = $this->QuestionnaireAnswer->set($data);
		$result = $this->QuestionnaireAnswer->validates();
		$result = $this->QuestionnaireAnswer->checkAnswerValue($data, $question, $answers);

		// テスト実施
		$this->assertFalse($result);

		//validationErrorsチェック
		$type = 'date';
		$expected = array(
				'answer_value' => array(
					sprintf(__d('questionnaires', 'Please enter a valid date and time.')),
					sprintf(__d('questionnaires', 'Please enter the %s between %s and %s.', $type, $question['min'], $question['max']))
				));
		$this->assertEquals($this->QuestionnaireAnswer->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

}
