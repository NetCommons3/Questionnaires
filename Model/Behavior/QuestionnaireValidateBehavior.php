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
class QuestionnaireValidateBehavior extends ModelBehavior {

/**
 * Checks if is_period is on, required start_period, end_period
 *
 * @param object &$model use model
 * @param array $check check data array
 * @return bool
 */
	public function requireTimes(&$model, $check) {
		if ($model->data['Questionnaire']['is_period'] == QuestionnairesComponent::USES_USE) {
			if (empty($model->data['Questionnaire']['start_period']) && empty($model->data['Questionnaire']['end_time'])) {
				return false;
			}
		}
		return true;
	}

/**
 * Checks if is_key_pass_use is on, required key_phrase
 *
 * @param object &$model use model
 * @param array $check check data array
 * @return bool
 */
	public function requireKeyPhrase(&$model, $check) {
		if ($model->data['Questionnaire']['is_key_pass_use'] == QuestionnairesComponent::USES_USE) {
			if (empty($model->data['Questionnaire']['key_phrase'])) {
				return false;
			}
		}
		return true;
	}

/**
 * Checks datetime null or datetime
 *
 * @param object &$model use model
 * @param array $check check data array
 * @return bool
 */
	public function checkDateTime(&$model, $check) {
		foreach ($check as $val) {
			if (empty($val)) {
				continue;
			}
			$ret = Validation::datetime($val);
			if (!$ret) {
				return false;
			}
		}
		return true;
	}

/**
 * Used to compare 2 datetime values.
 *
 * @param object &$model use model
 * @param string|array $check datetime string
 * @param string $operator Can be either a word or operand
 *	is greater >, is less <, greater or equal >=
 *	less or equal <=, is less <, equal to ==, not equal !=
 * @param string $compare compare datetime string
 * @return bool Success
 */
	public function checkDateComp(&$model, $check, $operator, $compare) {
		// 比較対象がないので比較する必要なし
		if (empty($model->data['Questionnaire'][$compare])) {
			return true;
		}

		$check2 = strtotime($model->data['Questionnaire'][$compare]);
		foreach ($check as $val) {
			if (empty($val)) {
				continue;
			}
			$check1 = strtotime($val);
			$ret = Validation::comparison($check1, $operator, $check2);
			if (!$ret) {
				return false;
			}
		}
		return true;
	}

/**
 * checkRelationshipQuestionType
 * can not set result display ON when type of question is in text type or text-area type or date type
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	public function checkRelationshipQuestionType(&$model, $check) {
		// $check には array('is_result_display' => '入力値') が入る
		if ($model->data['QuestionnaireQuestion']['question_type'] == QuestionnairesComponent::TYPE_TEXT
			|| $model->data['QuestionnaireQuestion']['question_type'] == QuestionnairesComponent::TYPE_TEXT_AREA
			|| $model->data['QuestionnaireQuestion']['question_type'] == QuestionnairesComponent::TYPE_DATE_AND_TIME
		) {
			if ($check == QuestionnairesComponent::EXPRESSION_SHOW) {
				return false;
			}
		}
		return true;
	}

/**
 * checkMinMax
 * min and max is require both value
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	public function checkMinMax(&$model, $check) {
		// 最大値、最小値はテキストで「数値型」の場合と、日付け型の「日」「日時」の場合のみ設定可能
		if (empty($model->data['QuestionnaireQuestion']['min']) && empty($model->data['QuestionnaireQuestion']['max'])) {
			return true;
		}

		if (!$this->__checkMinMaxNumeric($model, $check)) {
			return false;
		}
		if (!$this->__checkMinMaxDate($model, $check)) {
			return false;
		}
		if (!$this->__checkMinMaxDateTime($model, $check)) {
			return false;
		}
		if (!empty($model->data['QuestionnaireQuestion']['min']) && !empty($model->data['QuestionnaireQuestion']['max'])) {
			return true;
		}

		return false;
	}

/**
 * __checkMinMaxNumeric
 * min and max is require both value
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	private function __checkMinMaxNumeric(&$model, $check) {
		if ($model->data['QuestionnaireQuestion']['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_NUMERIC) {
			if (!Validation::numeric($model->data['QuestionnaireQuestion']['min'])) {
				return false;
			}
			if (!Validation::numeric($model->data['QuestionnaireQuestion']['max'])) {
				return false;
			}
		}
		return true;
	}

/**
 * __checkMinMaxDate
 * min and max is require both value
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	private function __checkMinMaxDate(&$model, $check) {
		if ($model->data['QuestionnaireQuestion']['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE) {
			if (!Validation::date($model->data['QuestionnaireQuestion']['min'])) {
				return false;
			}
			if (!Validation::date($model->data['QuestionnaireQuestion']['max'])) {
				return false;
			}
		}
		return true;
	}

/**
 * __checkMinMaxDateTime
 * min and max is require both value
 *
 * @param object &$model use model
 * @param bool $check post data
 * @return bool
 */
	private function __checkMinMaxDateTime(&$model, $check) {
		if ($model->data['QuestionnaireQuestion']['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE_TIME) {
			if (!Validation::datetime($model->data['QuestionnaireQuestion']['min'])) {
				return false;
			}
			if (!Validation::datetime($model->data['QuestionnaireQuestion']['max'])) {
				return false;
			}
		}
		return true;
	}

}