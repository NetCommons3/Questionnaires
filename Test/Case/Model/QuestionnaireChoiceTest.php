<?php
/**
 * QuestionnaireChoice Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireChoice Test Case
 */
class QuestionnaireChoiceTest extends QuestionnaireTestBase {

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
		$this->QuestionnaireChoice->set($data);
		$result = $this->QuestionnaireChoice->validates();

		//戻り値チェック
		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		//validationErrorsチェック
		$this->assertEquals($this->QuestionnaireChoice->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'choice_label'が空白の場合(エラー)
 * @return void
 */
	public function testbeforeValidate1() {
		$field = 'choice_label';

		//データ生成
		$data = array(
			'choice_label' => '',
			'questionnaire_question_id' => 1,
			'other_choice_type' => 1,
			'choice_sequence' => 1,
			'graph_color' => '#666666',
			'is_auto_translated' => 0,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__d('questionnaires', 'Please input choice text.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'questionnaire_question_id'が数字以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate2() {
		$field = 'questionnaire_question_id';

		//データ生成
		$data = array(
			'choice_label' => 'aaaa',
			'questionnaire_question_id' => 'a',
			'other_choice_type' => 1,
			'choice_sequence' => 1,
			'graph_color' => '#666666',
			'is_auto_translated' => 0,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);
		//期待値  app/app/Locale/jpn/LC_MESSAGES/default.po msgid "numeric"  msgstr "数値"
		$expected = array(
				$field => array(__('numeric')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'other_choice_type'が数字以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate3() {
		$field = 'other_choice_type';

		//データ生成
		$data = array(
			'choice_label' => 'aaaa',
			'questionnaire_question_id' => '1',
			'other_choice_type' => 'b',
			'choice_sequence' => 1,
			'graph_color' => '#666666',
			'is_auto_translated' => 0,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);
		//期待値  app/app/Locale/jpn/LC_MESSAGES/default.po msgid "numeric"  msgstr "数値"
		$expected = array(
				$field => array(__('numeric')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}
/**
 * validate method
 *  validate試験'choice_sequence'が空の場合(エラー)
 * @return void
 */
	public function testbeforeValidate4() {
		$field = 'choice_sequence';

		//データ生成
		$data = array(
			'choice_label' => 'aaaa',
			'questionnaire_question_id' => '1',
			'other_choice_type' => '1',
			'choice_sequence' => '',
			'graph_color' => '#666666',
			'is_auto_translated' => 0,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);
		//期待値  app/app/Locale/jpn/LC_MESSAGES/default.po msgid "numeric"  msgstr "数値"
		$expected = array(
					$field => array(__('numeric')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'graph_color'がフォーマット不正の場合(エラー)
 * @return void
 */
	public function testbeforeValidate5() {
		$field = 'graph_color';

		//データ生成
		$data = array(
			'choice_label' => 'aaaa',
			'questionnaire_question_id' => '1',
			'other_choice_type' => '1',
			'choice_sequence' => 1,
			'graph_color' => '#666',
			'is_auto_translated' => 0,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);
		//期待値
		$expected = array(
					$field => array(__d('questionnaires', 'First character is "#". And input the hexadecimal numbers by six digits.')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * validate method
 *  validate試験'is_auto_translated'がフォーマット不正の場合(エラー)
 * @return void
 */
	public function testbeforeValidate6() {
		$field = 'is_auto_translated';

		//データ生成
			$data = array(
				'choice_label' => 'aaaa',
				'questionnaire_question_id' => '1',
				'other_choice_type' => '1',
				'choice_sequence' => 1,
				'graph_color' => '#666666',
				'is_auto_translated' => 2,
				'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			);
		//期待値
		$expected = array(
					$field => array(__('boolean')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * getDefaultChoice method
 *
 * @return void
 */
	public function testgetDefaultChoice() {
		//初期処理
		$this->setUp();

		// 期待値の生成
		$expected = array(
			'choice_sequence' => 0,
			'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
			'choice_label' => __d('questionnaires', 'new choice') . '1',
			'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
			'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END,
			'graph_color' => QuestionnairesComponent::$defaultGraphColors[0]
		);

		// 処理実行
		$result = $this->QuestionnaireChoice->getDefaultChoice();

		// テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}

/**
 * saveQuestionnaireChoice method
 *
 * @return void
 */
	public function testsaveQuestionnaireChoice() {
		//初期処理
		$this->setUp();

		//データの生成
		$data = array(
			array(
				'choice_sequence' => 5,
				'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
				'choice_label' => __d('questionnaires', 'new choice') . '1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED
		));

		$questionId = 1;
		$status = NetCommonsBlockComponent::STATUS_IN_DRAFT;

		// 処理実行
		$this->QuestionnaireChoice->saveQuestionnaireChoice($questionId, $status, $data);

		//成否のデータ取得
		$status = array(
					'conditions' => array('choice_sequence' => 5));
		$result = $this->QuestionnaireChoice->find('first', $status);

		// テスト実施
		//$this->_assertArray($data[0], $result['QuestionnaireChoice']);
		$this->assertEquals($data[0]['choice_sequence'], $result['QuestionnaireChoice']['choice_sequence']);
		$this->assertEquals($data[0]['matrix_type'], $result['QuestionnaireChoice']['matrix_type']);
		$this->assertEquals($data[0]['choice_label'], $result['QuestionnaireChoice']['choice_label']);
		$this->assertEquals($data[0]['other_choice_type'], $result['QuestionnaireChoice']['other_choice_type']);

		//終了処理
		$this->tearDown();
	}
}