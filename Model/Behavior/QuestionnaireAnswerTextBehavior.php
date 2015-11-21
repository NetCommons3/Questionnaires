<?php
/**
 * QuestionnaireValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAnswerBehavior', 'Questionnaires.Model/Behavior');

/**
 * Text Behavior
 *
 * @package  Questionnaires\Questionnaires\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class QuestionnaireAnswerTextBehavior extends QuestionnaireAnswerBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = QuestionnairesComponent::TYPE_TEXT;

/**
 * answerMaxLength 回答がアンケートが許す最大長を超えていないかの確認
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 回答データに対応する質問
 * @param int $max 最大長
 * @return bool
 */
	public function answerMaxLength(&$model, $data, $question, $max) {
		if ($question['question_type'] != $this->_myType) {
			return true;
		}
		return Validation::maxLength($data['answer_value'], $max);
	}

/**
 * answerValidation 回答内容の正当性
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 回答データに対応する質問
 * @param array $allAnswers 入力された回答すべて
 * @return bool
 */
	public function answerValidation(&$model, $data, $question, $allAnswers) {
		if ($question['question_type'] != $this->_myType) {
			return true;
		}
		$ret = true;

		// 数値型回答を望まれている場合
		if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_NUMERIC) {
			if (!Validation::numeric($data['answer_value'])) {
				$ret = false;
				$model->validationErrors['answer_value'][] = __d('questionnaires', 'Number required');
			}
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				if (!Validation::range($data['answer_value'], intval($question['min']), intval($question['max']))) {
					$ret = false;
					$model->validationErrors['answer_value'][] = sprintf(__d('questionnaires', 'Please enter the answer between %s and %s.', $question['min'], $question['max']));
				}
			}
		} else {
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				if (!Validation::minLength($data['answer_value'], intval($question['min'])) || !Validation::maxLength($data['answer_value'], intval($question['max']))) {
					$ret = false;
					$model->validationErrors['answer_value'][] = sprintf(__d('questionnaires', 'Please enter the answer between %s letters and %s letters.', $question['min'], $question['max']));
				}
			}
		}
		return $ret;
	}
}