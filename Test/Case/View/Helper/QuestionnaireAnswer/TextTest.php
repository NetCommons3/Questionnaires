<?php
/**
 * QuestionnaireAnswerHelper::singleChoice()のテスト
 *
 * @property QuestionnaireAnswerHelper $QuestionnaireAnswerHelper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('View', 'View');
App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnaireAnswerHelper', 'Questionnaires.View/Helper');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * Summary for QuestionnaireAnswerHelper Test Case
 */
class QuestionnaireAnswerHelperTextTest extends NetCommonsCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$this->QuestionnaireAnswer = new QuestionnaireAnswerHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->QuestionnaireAnswer);
		parent::tearDown();
	}

/**
 * _getComElement method
 *
 * @return string
 */
	protected function _getComElement() {
		$ret = <<< EOT
<div class="has-error">
</div>
<input type="hidden" name="data[QuestionnaireAnswer][qKey1][0][questionnaire_answer_summary_id]" id="QuestionnaireAnswerQKey10QuestionnaireAnswerSummaryId"/>
<input type="hidden" name="data[QuestionnaireAnswer][qKey1][0][questionnaire_question_key]" value="qKey1" id="QuestionnaireAnswerQKey10QuestionnaireQuestionKey"/>
<input type="hidden" name="data[QuestionnaireAnswer][qKey1][0][id]" id="QuestionnaireAnswerQKey10Id"/>
<input type="hidden" name="data[QuestionnaireAnswer][qKey1][0][matrix_choice_key]" id="QuestionnaireAnswerQKey10MatrixChoiceKey"/>
EOT;
		return $ret;
	}

/**
 * Test QuestionnaireAnswer->singleText()
 *
 * @return void
 */
	public function testSingleTextNormal() {
		$expected = <<< EOT
		<div class="form-inline">
			<input name="data[QuestionnaireAnswer][qKey1][0][answer_value]" class="form-control" type="text" id="QuestionnaireAnswerQKey10AnswerValue"/>
		</div>
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_TEXT,
			'is_range' => 0,
		);

		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test QuestionnaireAnswer->singleText()
 *
 * @return void
 */
	public function testSingleTextRange() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_TEXT,
			'is_range' => 1,
			'min' => 5,
			'max' => 10,
			'question_type_option' => 0
		);

		$expected = <<< EOT
		<div class="form-inline">
			<input name="data[QuestionnaireAnswer][qKey1][0][answer_value]" class="form-control" type="text" id="QuestionnaireAnswerQKey10AnswerValue"/>
		</div>
		<span class="help-block">
EOT;
		$expected .= sprintf(__d('questionnaires', 'Please enter between %s letters and %s letters'), $question['min'], $question['max']) . '</span>';
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test QuestionnaireAnswer->singleText()
 *
 * @return void
 */
	public function testSingleTextNumericRange() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_TEXT,
			'is_range' => 1,
			'min' => 5,
			'max' => 10,
			'question_type_option' => QuestionnairesComponent::TYPE_OPTION_NUMERIC
		);

		$expected = <<< EOT
		<div class="form-inline">
			<input name="data[QuestionnaireAnswer][qKey1][0][answer_value]" class="form-control" type="text" id="QuestionnaireAnswerQKey10AnswerValue"/>
		</div>
		<span class="help-block">
EOT;
		$expected .= sprintf(__d('questionnaires', 'Please enter a number between %s and %s'), $question['min'], $question['max']) . '</span>';
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

/**
 * Test QuestionnaireAnswer->singleText()
 *
 * @return void
 */
	public function testSingleTextReadonly() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_TEXT,
			'is_range' => 1,
			'min' => 5,
			'max' => 10,
			'question_type_option' => QuestionnairesComponent::TYPE_OPTION_NUMERIC
		);

		$expected = <<< EOT
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$actual = $this->QuestionnaireAnswer->answer($question, true);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

/**
 * Test QuestionnaireAnswer->textArea()
 *
 * @return void
 */
	public function testTextArea() {
		$expected = <<< EOT
			<textarea name="data[QuestionnaireAnswer][qKey1][0][answer_value]" div="form-inline" class="form-control" rows="5" id="QuestionnaireAnswerQKey10AnswerValue"></textarea>
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());

		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_TEXT_AREA,
		);

		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);

		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $this->_getComElement());
		$actual = $this->QuestionnaireAnswer->answer($question, true);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

}