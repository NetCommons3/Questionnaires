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
class ValidateAnswerMultipleSelectTest extends QuestionnaireAnswerValidateTest {

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
				'answer_value' => array('|choice_7:choice label4', '|choice_8:choice label5'),
				'questionnaire_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => 'so no ta value!'
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
		$data = $this->__getData('qKey_5', 4);
		// 通常の質問
		$normalQuestion = $this->_getQuestion(6);
		// 解答必須質問
		$requireQuestion = Hash::merge($normalQuestion, array('is_require' => QuestionnairesComponent::REQUIRES_REQUIRE));
		// その他回答がある質問
		$otherQuestion = Hash::merge($normalQuestion, array('QuestionnaireChoice' => array(array('other_choice_type' => 1))));
		return array(
			array($data, 3, $normalQuestion, 'answer_value', array('aaa', 'bbb'),
				__d('questionnaires', 'Invalid choice')),
			array($data, 3, $requireQuestion, 'answer_value', array(),
				__d('questionnaires', 'Input required')),
			array($data, 3, $otherQuestion, 'other_answer_value', '',
				__d('questionnaires', 'Please enter something, if you chose the other item')),
		);
	}
}
