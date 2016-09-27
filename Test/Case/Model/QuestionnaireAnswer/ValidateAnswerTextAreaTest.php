<?php
/**
 * QuestionnaireAnswer::validate()のテスト
 *
 * @property QuestionnaireAnswer $QuestionnaireAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAnswerValidateTest', 'Questionnaires.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireAnswer::validate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireAnswer
 */
class ValidateAnswerTextAreaTest extends QuestionnaireAnswerValidateTest {

/**
 * __getData
 *
 * @param string $qKey 質問キー
 * @param int $summaryId サマリID
 * @return array
 */
	private function __getData($qKey, $summaryId) {
		$answerData = array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'answer_value' => 'Test Answer!!',
				'questionnaire_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
		return $answerData;
	}

/**
 * testValidationErrorのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderValidationError() {
		$data = $this->__getData('qKey_17', 5);
		// 通常の質問
		$normalQuestion = Hash::merge($this->_getQuestion(18), array('is_range' => 0, 'is_require' => 0));
		// 解答必須質問
		$requireQuestion = Hash::merge($normalQuestion, array('is_require' => QuestionnairesComponent::REQUIRES_REQUIRE));
		return array(
			array($data, 3, $normalQuestion, 'answer_value', str_repeat('MaxLength', QuestionnairesComponent::QUESTIONNAIRE_MAX_ANSWER_LENGTH),
				__d('questionnaires', 'the answer is too long. Please enter under %d letters.', QuestionnairesComponent::QUESTIONNAIRE_MAX_ANSWER_LENGTH)),
			array($data, 3, $requireQuestion, 'answer_value', '',
				__d('questionnaires', 'Input required')),
		);
	}
}
