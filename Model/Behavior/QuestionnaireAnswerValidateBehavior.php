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

App::uses('ModelBehavior', 'Model');

/**
 * QuestionnaireValidate Behavior
 *
 * @package  Questionnaires\Questionnaires\Model\Befavior
 * @author Allcreator <info@allcreator.net>
 */
class QuestionnaireAnswerValidateBehavior extends ModelBehavior {

/**
 * checkTextAnswerValue 入力回答の正当性をチェックする
 *
 * @param object &$model use model
 * @param array &$data Postされた回答データ
 * @param array &$question 回答データに対応する質問
 * @param array &$answers all answer data of this question (for matrix)
 * @return array error messages
 */
	public function checkTextAnswerValue(&$model, &$data, &$question, &$answers) {
		$errors = array();
		$errors = array_merge($errors,
			$model->QuestionnaireAnswerValidation->checkNumericType($question, $data));

		$errors = array_merge($errors,
			$model->QuestionnaireAnswerValidation->checkRange($question, $data));

		if (!Validation::maxLength($data, QuestionnairesComponent::QUESTIONNAIRE_MAX_ANSWER_LENGTH)) {
			$errors = array_merge($errors,
				sprintf(__d('questionnaires', 'the answer is too long. Please enter under %d letters.', QuestionnairesComponent::QUESTIONNAIRE_MAX_ANSWER_LENGTH)));
		}
		return $errors;
	}
/**
 * checkTextAreaAnswerValue 入力回答の正当性をチェックする
 *
 * @param object &$model use model
 * @param array &$data Postされた回答データ
 * @param array &$question 回答データに対応する質問
 * @param array &$answers all answer data of this question (for matrix)
 * @return array error messages
 */
	public function checkTextAreaAnswerValue(&$model, &$data, &$question, &$answers) {
		$errors = array();
		if (!Validation::maxLength($data, QuestionnairesComponent::QUESTIONNAIRE_MAX_ANSWER_LENGTH)) {
			$errors = array_merge($errors,
				sprintf(__d('questionnaires', 'the answer is too long. Please enter under %d letters.', QuestionnairesComponent::QUESTIONNAIRE_MAX_ANSWER_LENGTH)));
		}
		return $errors;
	}
/**
 * checkSelectionAnswerValue 入力回答の正当性をチェックする
 *
 * @param object &$model use model
 * @param array &$data Postされた回答データ
 * @param array &$question 回答データに対応する質問
 * @param array &$answers all answer data of this question (for matrix)
 * @return array error messages
 */
	public function checkSelectionAnswerValue(&$model, &$data, &$question, &$answers) {
		$errors = array();
		if (isset($model->data['QuestionnaireAnswer']['answer_values'])) {
			$list = Hash::combine($question['QuestionnaireChoice'], '{n}.id', '{n}.origin_id');
			$choiceIds = array_keys($model->data['QuestionnaireAnswer']['answer_values']);
			foreach ($choiceIds as $choiceId) {
				$errors = array_merge($errors,
					$model->QuestionnaireAnswerValidation->checkAnswerInList($question, $choiceId, $list));
				$errors = array_merge($errors,
					$model->QuestionnaireAnswerValidation->checkOtherAnswer($question, $data, $choiceId, $model->data['QuestionnaireAnswer']));
			}
		}
		return $errors;
	}
/**
 * checkMatrixSelectionListAnswerValue 入力回答の正当性をチェックする
 *
 * @param object &$model use model
 * @param array &$data Postされた回答データ
 * @param array &$question 回答データに対応する質問
 * @param array &$answers all answer data of this question (for matrix)
 * @return array error messages
 */
	public function checkMatrixSelectionListAnswerValue(&$model, &$data, &$question, &$answers) {
		$errors = array();
		if (isset($model->data['QuestionnaireAnswer']['matrix_answer_values'])) {
			$list = Hash::combine($question['QuestionnaireChoice'], '{n}.id', '{n}.origin_id');
			$errors = array_merge($errors,
				$model->QuestionnaireAnswerValidation->checkMatrixAnswerInList($question, $model->data['QuestionnaireAnswer']['matrix_answer_values'], $list));
			$errors = array_merge($errors,
				$model->QuestionnaireAnswerValidation->checkMatrixOtherAnswer($question, $model->data['QuestionnaireAnswer']['matrix_answer_values'], $model->data['QuestionnaireAnswer']));
			$errors = array_merge($errors,
				$model->QuestionnaireAnswerValidation->checkMatrixAnswerFill($question, $model->data['QuestionnaireAnswer']['matrix_answer_values'], $answers));
		}
		return $errors;
	}
/**
 * checkDateAndTimeAnswerValue 入力回答の正当性をチェックする
 *
 * @param object &$model use model
 * @param array &$data Postされた回答データ
 * @param array &$question 回答データに対応する質問
 * @param array &$answers all answer data of this question (for matrix)
 * @return array error messages
 */
	public function checkDateAndTimeAnswerValue(&$model, &$data, &$question, &$answers) {
		$errors = array();
		$errors = array_merge($errors,
			$model->QuestionnaireAnswerValidation->checkDatetimeType($question, $data));
		$errors = array_merge($errors,
			$model->QuestionnaireAnswerValidation->checkDateRange($question, $data));
		return $errors;
	}
}