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
 * Checks if flag is on, required other fields
 *
 * @param object &$model use model
 * @param array $check check data array
 * @param mix $requireValue when check data value equal this value, then require other field
 * @param array $others require data field names
 * @param string $ope require condition AND or OR
 * @return bool
 */
	public function requireOtherFields(&$model, $check, $requireValue, $others, $ope) {
		$value = array_values($check);
		$value = $value[0];
		$ope = strtoupper($ope);
		if ($value != $requireValue) {
			return true;
		}
		foreach ($others as $other) {
			$checkData = Hash::get($model->data, $other);
			$ret = Validation::blank($checkData);
			if ($ope == 'AND') {
				if ($ret == true) {
					return false;
				}
			} elseif ($ope == 'OR') {
				if ($ret == false) {
					return true;
				}
			}
		}
		if ($ope == 'AND') {
			return true;
		}
		return false;
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
			if (Validation::blank($val)) {
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
		if (Validation::blank($model->data['Questionnaire'][$compare])) {
			return true;
		}

		$check2 = strtotime($model->data['Questionnaire'][$compare]);
		foreach ($check as $val) {
			if (Validation::blank($val)) {
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
		if (QuestionnairesComponent::isOnlyInputType($this->data['QuestionnaireQuestion']['question_type'])) {
			if ($check['is_result_display'] == QuestionnairesComponent::EXPRESSION_SHOW) {
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
		// 範囲使わない設定のときはチェックしない
		if ($model->data['QuestionnaireQuestion']['is_range'] == QuestionnairesComponent::USES_NOT_USE) {
			return true;
		}

		// 最大値、最小値はテキストで「数値型」の場合と、日付け型の「日」「日時」の場合のみ設定可能
		if (!$this->__checkMinMaxNumeric($model, $check)) {
			return false;
		}
		if (!$this->__checkMinMaxDate($model, $check)) {
			return false;
		}
		if (!$this->__checkMinMaxDateTime($model, $check)) {
			return false;
		}
		if ($model->data['QuestionnaireQuestion']['min'] >= $model->data['QuestionnaireQuestion']['max']) {
			return false;
		}

		return true;
	}

/**
 * getPeriodStatus
 * get period status now and specified time
 *
 * @param object &$model use model
 * @param bool $check flag data
 * @param string $startTime start time
 * @param string $endTime end time
 * @return int
 */
	public function getPeriodStatus(&$model, $check, $startTime, $endTime) {
		$ret = QuestionnairesComponent::QUESTIONNAIRE_PERIOD_STAT_IN;

		if ($check == WorkflowBehavior::PUBLIC_TYPE_LIMITED) {
			$nowTime = time();
			if ($nowTime < strtotime($startTime)) {
				$ret = QuestionnairesComponent::QUESTIONNAIRE_PERIOD_STAT_BEFORE;
			}
			if ($nowTime > strtotime($endTime)) {
				$ret = QuestionnairesComponent::QUESTIONNAIRE_PERIOD_STAT_END;
			}
		}
		return $ret;
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