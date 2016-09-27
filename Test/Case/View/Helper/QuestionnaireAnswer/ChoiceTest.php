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
App::uses('NetCommonsHtmlHelper', 'NetCommons.View/Helper');
App::uses('QuestionnaireAnswerHelper', 'Questionnaires.View/Helper');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * Summary for QuestionnaireAnswerHelper Test Case
 */
class QuestionnaireAnswerHelperChoiceTest extends NetCommonsCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$this->NetCommonsHtml = new NetCommonsHtmlHelper($View);
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
 * Test QuestionnaireAnswer->singleChoice()
 *
 * @return void
 */
	public function testSingleChoice() {
		$expected = <<< EOT
<input type="hidden" name="data[QuestionnaireAnswer][qKey1][0][answer_value]" value="" id="QuestionnaireAnswerQKey10AnswerValue" />
<div class="radio">
	<label class="control-label">
		<input type="radio" name="data[QuestionnaireAnswer][qKey1][0][answer_value]" id="QuestionnaireAnswerQKey10AnswerValueCKey1選択肢1" value="cKey1:選択肢1" after="&lt;inputname=&quot;data[QuestionnaireAnswer][qKey1][0][other_answer_value]&quot;class=&quot;form-control&quot;type=&quot;text&quot;id=&quot;QuestionnaireAnswerQKey10OtherAnswerValue&quot;/&gt;" />
			選択肢1
	</label>
</div>
<div class="radio">
	<label class="control-label">
		<input type="radio" name="data[QuestionnaireAnswer][qKey1][0][answer_value]" id="QuestionnaireAnswerQKey10AnswerValueCKey2選択肢2" value="cKey2:選択肢2" after="&lt;inputname=&quot;data[QuestionnaireAnswer][qKey1][0][other_answer_value]&quot;class=&quot;form-control&quot;type=&quot;text&quot;id=&quot;QuestionnaireAnswerQKey10OtherAnswerValue&quot;/&gt;" />
			選択肢2
	</label>
</div>
<div class="radio">
	<label class="control-label">
		<input type="radio" name="data[QuestionnaireAnswer][qKey1][0][answer_value]" id="QuestionnaireAnswerQKey10AnswerValueCKey3その他" value="cKey3:その他" after="&lt;inputname=&quot;data[QuestionnaireAnswer][qKey1][0][other_answer_value]&quot;class=&quot;form-control&quot;type=&quot;text&quot;id=&quot;QuestionnaireAnswerQKey10OtherAnswerValue&quot;/&gt;"/>
		その他
		<input name="data[QuestionnaireAnswer][qKey1][0][other_answer_value]" class="form-control" type="text" id="QuestionnaireAnswerQKey10OtherAnswerValue" />
	</label>
</div>
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());
		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_SELECTION,
			'is_choice_horizon' => false,
			'QuestionnaireChoice' => array(
				array(
					'key' => 'cKey1',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 0,
					'choice_label' => '選択肢1',
					'choice_value' => '選択肢1',
				),
				array(
					'key' => 'cKey2',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 1,
					'choice_label' => '選択肢2',
					'choice_value' => '選択肢2',
				),
				array(
					'key' => 'cKey3',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT,
					'choice_sequence' => 2,
					'choice_label' => 'その他',
					'choice_value' => 'その他',
				)
			)
		);

		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);

		$expected = preg_replace('/class="radio/', 'class="radioradio-inline', $expected);
		$question['is_choice_horizon'] = true;
		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}

/**
 * Test QuestionnaireAnswer->multipleChoice()
 *
 * @return void
 */
	public function testMultipleChoice() {
		$expected = <<< EOT
<input type="hidden" name="data[QuestionnaireAnswer][qKey1][0][answer_value]" value="" id="QuestionnaireAnswerQKey10AnswerValue"/>
<div class="checkbox nc-checkbox">
	<input type="checkbox" name="data[QuestionnaireAnswer][qKey1][0][answer_value][]" value="cKey1:選択肢1" id="QuestionnaireAnswerQKey10AnswerValueCKey1選択肢1" />
	<label for="QuestionnaireAnswerQKey10AnswerValueCKey1選択肢1">
		選択肢1
	</label>
</div>
<div class="checkbox nc-checkbox">
	<input type="checkbox" name="data[QuestionnaireAnswer][qKey1][0][answer_value][]" value="cKey2:選択肢2" id="QuestionnaireAnswerQKey10AnswerValueCKey2選択肢2" />
	<label for="QuestionnaireAnswerQKey10AnswerValueCKey2選択肢2">
		選択肢2
	</label>
</div>
<div class="checkbox nc-checkbox">
	<input type="checkbox" name="data[QuestionnaireAnswer][qKey1][0][answer_value][]" value="cKey3:その他" id="QuestionnaireAnswerQKey10AnswerValueCKey3その他" />
	<label for="QuestionnaireAnswerQKey10AnswerValueCKey3その他">
		その他
	</label>
</div>
<div class="checkbox-inline">
<input name="data[QuestionnaireAnswer][qKey1][0][other_answer_value]" class="form-control" type="text" id="QuestionnaireAnswerQKey10OtherAnswerValue"/>
</div>
EOT;
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());
		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_MULTIPLE_SELECTION,
			'is_choice_horizon' => false,
			'QuestionnaireChoice' => array(
				array(
					'key' => 'cKey1',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 0,
					'choice_label' => '選択肢1',
					'choice_value' => '選択肢1',
				),
				array(
					'key' => 'cKey2',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 1,
					'choice_label' => '選択肢2',
					'choice_value' => '選択肢2',
				),
				array(
					'key' => 'cKey3',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT,
					'choice_sequence' => 2,
					'choice_label' => 'その他',
					'choice_value' => 'その他',
				)
			)
		);

		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);

		$expected = preg_replace('/class="checkboxnc-checkbox/', 'class="checkbox-inlinenc-checkbox', $expected);
		$question['is_choice_horizon'] = true;
		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertTextEquals($expected, $actual);
	}
/**
 * Test QuestionnaireAnswer->singleList()
 *
 * @return void
 */
	public function testSingleList() {
		$expected = <<< EOT
<div class="form-inline">
	<select name="data[QuestionnaireAnswer][qKey1][0][answer_value]" class="form-control" id="QuestionnaireAnswerQKey10AnswerValue">
		<option value="">%s</option>
		<option value="|cKey1:選択肢1">選択肢1</option>
		<option value="|cKey2:選択肢2">選択肢2</option>
		<option value="|cKey3:選択肢3">選択肢3</option>
	</select>
</div>
EOT;
		$expected = sprintf($expected, __d('questionnaires', 'Please choose one'));
		$expected = preg_replace('/[\n|\r|\t|\s]/', '', $expected . $this->_getComElement());
		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX,
			'QuestionnaireChoice' => array(
				array(
					'key' => 'cKey1',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'choice_sequence' => 0,
					'choice_label' => '選択肢1',
					'choice_value' => '選択肢1',
				),
				array(
					'key' => 'cKey2',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'choice_sequence' => 1,
					'choice_label' => '選択肢2',
					'choice_value' => '選択肢2',
				),
				array(
					'key' => 'cKey3',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'choice_sequence' => 2,
					'choice_label' => '選択肢3',
					'choice_value' => '選択肢3',
				)
			)
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