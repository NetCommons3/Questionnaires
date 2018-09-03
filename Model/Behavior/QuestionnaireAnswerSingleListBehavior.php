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
class QuestionnaireAnswerSingleListBehavior extends QuestionnaireAnswerBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX;

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
 * choice validate check type
 *
 * @var array
 */
	protected $_choiceValidateType = array(
		QuestionnairesComponent::TYPE_SELECTION,
		QuestionnairesComponent::TYPE_MULTIPLE_SELECTION,
		QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX,
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
		$model->data['QuestionnaireAnswer']['answer_values'] = array();
		if (isset($model->data['QuestionnaireAnswer']['answer_value'])) {
			$this->_decomposeAnswerValue($model->data['QuestionnaireAnswer']['answer_values'],
				$model->data['QuestionnaireAnswer']['answer_value']);
		}
	}
/**
 * answerValidation 回答内容の正当性
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 回答データに対応する質問
 * @param array $allAnswers 入力された回答すべて
 * @return bool
 *
 * 速度改善の修正に伴って発生したため抑制
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
	public function answerChoiceValidation(&$model, $data, $question, $allAnswers) {
		if (! in_array($question['question_type'], $this->_choiceValidateType)) {
			return true;
		}
		if (! isset($model->data['QuestionnaireAnswer']['answer_values'])) {
			return true;
		}
		// 質問に設定されている選択肢を配列にまとめる
		$list = [];
		$checkOtherChoiceArr = [];
		foreach ($question['QuestionnaireChoice'] as $choice) {
			$list[$choice['id']] = $choice['key'];
			$hasOtherChoice = false;
			if ($choice['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				$hasOtherChoice = true;
			}
			$checkOtherChoiceArr[$choice['key']] = $hasOtherChoice;
		}

		$ret = true;
		// 選択された選択肢IDすべてについて調査する
		$choiceKeys = array_keys($model->data['QuestionnaireAnswer']['answer_values']);
		foreach ($choiceKeys as $choiceKey) {
			// 選択されたIDは、ちゃんと用意されている選択肢の中のひとつであるか
			if ($choiceKey != '' && !Validation::inList(strval($choiceKey), $list)) {
				$ret = false;
				$model->validationErrors['answer_value'][] =
					__d('questionnaires', 'Invalid choice');
			}
			// チェックされている選択肢が「その他」の項目である場合は
			if (isset($checkOtherChoiceArr[$choiceKey]) &&
				$checkOtherChoiceArr[$choiceKey]) {
				// 具体的なテキストが書かれていないといけない
				if (empty($model->data['QuestionnaireAnswer']['other_answer_value'])) {
					$ret = false;
					$model->validationErrors['answer_value'][] =
						__d('questionnaires', 'Please enter something, if you chose the other item');
				}
			}
		}
		return $ret;
	}

}
