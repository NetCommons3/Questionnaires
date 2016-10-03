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
class QuestionnaireAnswerHelperMatrixTest extends NetCommonsCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$this->QuestionnaireAnswer = new QuestionnaireAnswerHelper($View);
		$this->NetCommonsHtml = new NetCommonsHtmlHelper($View);
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
 * _getQuestion method
 *
 * @return array
 */
	protected function _getQuestion() {
		$question = array(
			'key' => 'qKey1',
			'question_type' => QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
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
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT,
					'choice_sequence' => 1,
					'choice_label' => '選択肢2',
					'choice_value' => '選択肢2',
				),
				array(
					'key' => 'cKey3',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_COLUMN,
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 2,
					'choice_label' => '列１',
					'choice_value' => '列１',
				),
				array(
					'key' => 'cKey4',
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_COLUMN,
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'choice_sequence' => 3,
					'choice_label' => '列２',
					'choice_value' => '列２',
				)
			)
		);
		return $question;
	}
/**
 * Test QuestionnaireAnswer->matrix()
 *
 * @return void
 */
	public function testSingleMatrix() {
		$question = $this->_getQuestion();
		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertInput('input', 'questionnaire_answer_summary_id', '', $actual);
		$this->assertInput('input', 'questionnaire_question_key', '', $actual);
		$this->assertInput('input', 'matrix_choice_key', '', $actual);
		$this->assertInput('input', 'id', '', $actual);
		$this->assertInput('input', 'answer_value', '', $actual);
	}

/**
 * Test QuestionnaireAnswer->matrix()
 *
 * @return void
 */
	public function testMultipleMatrix() {
		$question = $this->_getQuestion();
		$question['question_type'] = QuestionnairesComponent::TYPE_MATRIX_MULTIPLE;
		$actual = $this->QuestionnaireAnswer->answer($question);
		$actual = preg_replace('/[\n|\r|\t|\s]/', '', $actual);
		$this->assertInput('input', 'questionnaire_answer_summary_id', '', $actual);
		$this->assertInput('input', 'questionnaire_question_key', '', $actual);
		$this->assertInput('input', 'matrix_choice_key', '', $actual);
		$this->assertInput('input', 'id', '', $actual);
		$this->assertInput('input', 'answer_value', '', $actual);
	}

/**
 * Assert input tag
 *
 * @param string $tagType タグタイプ(input or textearea or button)
 * @param string $name inputタグのname属性
 * @param string $value inputタグのvalue値
 * @param string $result Result data
 * @return void
 */
	public function assertInput($tagType, $name, $value, $result) {
		$result = str_replace("\n", '', $result);
		$patternName = '.*?name="data\[QuestionnaireAnswer\].*?\[' . preg_quote($name, '/') . '\]"';

		$this->assertRegExp(
				'/<' . $tagType . $patternName . '.*?>/', $result
		);
	}

}
