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
 * Answer Behavior
 *
 * @package  Questionnaires\Questionnaires\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class QuestionnaireAnswerBehavior extends ModelBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = null;

/**
 * this answer type
 * data in database must be changed to array
 *
 * @var int
 */
	protected $_isTypeAnsChgArr = false;

/**
 * this answer type
 * data array must be shift up for post data array in screen
 *
 * @var int
 */
	protected $_isTypeAnsArrShiftUp = false;

/**
 * setup
 *
 * @param Model $Model モデル
 * @param array $settings 設定値
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = $settings;
	}

/**
 * beforeSave is called before a model is saved. Returning false from a beforeSave callback
 * will abort the save operation.
 * 選択肢系の回答の場合、answer_value に　[id:value|id:value....]の形で収めなくてはいけない
 * 保存前に整える
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False if the operation should abort. Any other result will continue.
 * @see Model::save()
 */
	public function beforeSave(Model $model, $options = array()) {
		if (isset($model->data['QuestionnaireAnswer']['multi_answer_values'])) {
			$model->data['QuestionnaireAnswer']['answer_value'] = $model->data['QuestionnaireAnswer']['multi_answer_values'];
		}
		// elseif (isset($this->data['QuestionnaireAnswer']['matrix_answer_values'])) {
		return true;
	}

/**
 * After find callback. Can be used to modify any results returned by find.
 *
 * @param Model $model Model using this behavior
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed An array value will replace the value of $results - any other value will be ignored.
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function afterFind(Model $model, $results, $primary = false) {
		// afterFind 選択肢系の回答の場合、answer_value に　[id:value|id:value....]の形で収まっているので
		// それをデータ入力画面から渡されるデータ形式と同じにする
		foreach ($results as &$val) {
			if (isset($val['QuestionnaireAnswer']['answer_value']) && isset($val['QuestionnaireQuestion']['question_type'])) {
				if ($val['QuestionnaireQuestion']['question_type'] != $this->_myType) {
					continue;
				}
				if ($this->_isTypeAnsChgArr) {
					$val['QuestionnaireAnswer']['answer_values'] = array();
					$answers = explode(QuestionnairesComponent::ANSWER_DELIMITER, trim($val['QuestionnaireAnswer']['answer_value'], QuestionnairesComponent::ANSWER_DELIMITER));
					$val['QuestionnaireAnswer']['answer_values'] = Hash::combine(
						array_map('explode',
							array_fill(0, count($answers), QuestionnairesComponent::ANSWER_VALUE_DELIMITER),
							$answers),
						'{n}.0', '{n}.1');
					$val['QuestionnaireAnswer']['answer_value'] = array_map(array($this, 'setDelimiter'), $answers);

					// array_mapで配列化するのでSingle選択のときはFlatに戻す必要がある
					if ($this->_isTypeAnsArrShiftUp) {
						$val['QuestionnaireAnswer']['answer_value'] = $val['QuestionnaireAnswer']['answer_value'][0];
					}
				}
			}
		}
		return $results;
	}

/**
 * setDelimiter
 *
 * @param string $answer answer data
 * @return string
 */
	public function setDelimiter($answer) {
		return '|' . $answer;
	}

/**
 * _decomposeAnswerValue
 * get decompose answer value by delimiter
 *
 * @param mix &$dst 加工データ
 * @param mix $src 入力データ
 * @return void
 */
	protected function _decomposeAnswerValue(&$dst, $src) {
		// dstがまだ配列型になっていないなら
		if (!is_array($dst)) {
			$dst = array();	// 初期化
		}
		$answers = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER, trim($src, QuestionnairesComponent::ANSWER_DELIMITER));
		$dst[$answers[0]] = isset($answers[1]) ? $answers[1] : '';
	}

/**
 * _setupOtherAnswerValue
 * その他オプションにチェックが入っていないのにその他欄に何か書いてあったら空にする
 *
 * @param Model $model Model using this behavior
 * @param array $question 質問データ
 * @return void
 */
	protected function _setupOtherAnswerValue(Model $model, $question) {
		$choice = Hash::extract($question['QuestionnaireChoice'], '{n}[other_choice_type!=' . QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED . ']');
		if (! $choice) {
			return;
		}
		$key = $choice[0]['key'];
		if (! Hash::check($model->data, 'QuestionnaireAnswer.answer_values.' . $key) &&
			$model->data['QuestionnaireAnswer']['matrix_choice_key'] != $key) {
			$model->data['QuestionnaireAnswer']['other_answer_value'] = '';
		}
	}

/**
 * answerRequire 回答必須の質問の場合回答されているかの確認
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 回答データに対応する質問
 * @return bool
 */
	public function answerRequire(&$model, $data, $question) {
		if ($question['is_require'] != QuestionnairesComponent::REQUIRES_REQUIRE) {
			return true;
		}
		return Validation::notBlank($data['answer_value']);
	}

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
		return true;
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
		return true;
	}
}