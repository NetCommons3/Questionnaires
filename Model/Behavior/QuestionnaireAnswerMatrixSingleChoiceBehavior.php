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
 * SingleList Behavior
 *
 * @package  Questionnaires\Questionnaires\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class QuestionnaireAnswerMatrixSingleChoiceBehavior extends QuestionnaireAnswerBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST;

/**
 * this answer type
 * data in database must be changed to array
 *
 * @var int
 */
	protected $_isTypeAnsChgArr = true;

/**
 * this answer type
 * data array must be shift up for post data array in screen
 *
 * @var int
 */
	protected $_isTypeAnsArrShiftUp = true;

/**
 * matrix validate check type
 *
 * @var array
 */
	protected $_matrixValidateType = array(
		QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
		QuestionnairesComponent::TYPE_MATRIX_MULTIPLE,
	);

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		$question = $options['question'];
		if ($question['question_type'] != $this->_myType) {
			return;
		}
		$model->data['QuestionnaireAnswer']['matrix_answer_values'] = array();
		$matrixChoiceId = $model->data['QuestionnaireAnswer']['matrix_choice_key'];

		if (isset($model->data['QuestionnaireAnswer']['answer_value'])) {
			$this->_decomposeAnswerValue(
				$model->data['QuestionnaireAnswer']['answer_values'],
				$model->data['QuestionnaireAnswer']['answer_value']);
			$this->_decomposeAnswerValue(
				$model->data['QuestionnaireAnswer']['matrix_answer_values'][$matrixChoiceId],
				$model->data['QuestionnaireAnswer']['answer_value']);
		}
		$this->_setupOtherAnswerValue($model, $question);
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
	public function answerMatrixValidation(&$model, $data, $question, $allAnswers) {
		if (! in_array($question['question_type'], $this->_matrixValidateType)) {
			return true;
		}
		$ret = true;
		if (isset($model->data['QuestionnaireAnswer']['matrix_answer_values'])) {
			$list = [];
			foreach ($question['QuestionnaireChoice'] as $item) {
				$list[$item['key']] = $item['key'];
			}
			if (! $this->checkMatrixAnswerInList(
				$model,
				$model->data['QuestionnaireAnswer']['matrix_answer_values'],
				$list)) {
				$ret = false;
			}
			if (! $this->checkMatrixOtherAnswer(
				$model,
				$question,
				$model->data['QuestionnaireAnswer']['matrix_answer_values'],
				$model->data['QuestionnaireAnswer'])) {
				$ret = false;
			}
			if (! $this->checkMatrixAnswerFill(
				$model,
				$question,
				$model->data['QuestionnaireAnswer'],
				$allAnswers)) {
				$ret = false;
			}
		}
		return $ret;
	}

/**
 * checkMatrixAnswerInList
 *
 * @param object &$model use model
 * @param string $answers answer value
 * @param array $list choice list ( choice key list)
 * @return bool
 */
	public function checkMatrixAnswerInList(&$model, $answers, $list) {
		$ret = true;
		foreach ($answers as $matrixRowId => $matrixColAns) {
			if (!Validation::inList(strval($matrixRowId), $list)) {
				$ret = false;
				$model->validationErrors['answer_value'][] = __d('questionnaires', 'Invalid choice');
			}
			$choiceIds = array_keys($matrixColAns);
			foreach ($choiceIds as $choiceId) {
				if ($choiceId != '' && !Validation::inList(strval($choiceId), $list)) {
					$ret = false;
					$model->validationErrors['answer_value'][] = __d('questionnaires', 'Invalid choice');
				}
			}
		}
		return $ret;
	}

/**
 * checkMatrixOtherAnswer
 *
 * @param object &$model use model
 * @param array $question question
 * @param string $answers answer value
 * @param string $otherAnswer other answer string
 * @return bool
 */
	public function checkMatrixOtherAnswer(&$model, $question, $answers, $otherAnswer) {
		// このやり方だと、「その他」行がマトリクスにある時は必ず入力しなきゃいけなくなる？
		// 選択肢を何も選択しなかったらAnswerデータが飛んでこないからチェックにかからないか？
		$rowIds = array_keys($answers);

		$checkOtherChoiceArr = [];
		foreach ($question['QuestionnaireChoice'] as $choice) {
			$hasOtherChoice = false;
			if ($choice['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				$hasOtherChoice = true;
			}
			$checkOtherChoiceArr[$choice['key']] = $hasOtherChoice;
		}
		foreach ($rowIds as $matrixRowId) {

			if (isset($checkOtherChoiceArr[$matrixRowId]) &&
				$checkOtherChoiceArr[$matrixRowId]) {
				if (empty($otherAnswer['other_answer_value'])) {
					$model->validationErrors['answer_value'][] =
						__d('questionnaires', 'Please enter something in other item');
					return false;
				}
			}
		}
		return true;
	}

/**
 * checkMatrixAnswerFill
 *
 * @param object &$model use model
 * @param array $question question
 * @param string $answers all row answer value
 * @param array $allAnswers 入力された回答すべて
 * @return array error message
 */
	public function checkMatrixAnswerFill(&$model, $question, $answers, $allAnswers) {
		if ($model->oneTimeValidateFlag) {	// チェック済
			return true;
		}
		// マトリクスの場合は全行回答するか全行回答しないかでないと集計計算が狂うので
		// 全行回答か全行無回答かを確認している
		$answerCount = 0;
		$noAnswerCount = 0;
		$checkAnswer = $allAnswers[$question['key']];
		foreach ($checkAnswer as $ans) {
			if (!isset($ans['id'])) {
				// id すらないのはblackhole対応のためのhidden要素であるので無視
				continue;
			}
			if ($ans['answer_value'] == '') {
				$noAnswerCount++;
			}
			$answerCount++;
		}
		$model->oneTimeValidateFlag = true;	// チェックした
		if ($noAnswerCount > 0 && $noAnswerCount < $answerCount) {
			$model->validationErrors['answer_value'][] =
				__d('questionnaires', 'Please answer about all rows.');
			return false;
		}
		return true;
	}

}
