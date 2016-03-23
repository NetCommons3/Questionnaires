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
 * @param string $ope require condition AND or OR or XOR
 * @return bool
 */
	public function requireOtherFields(&$model, $check, $requireValue, $others, $ope) {
		$checkPatterns = array(
			'AND' => array('midstream' => array('chk' => true, 'ret' => false), 'end' => array('ret' => true)),
			'OR' => array('midstream' => array('chk' => false, 'ret' => true), 'end' => array('ret' => false)),
			'XOR' => array('midstream' => array('chk' => false, 'ret' => false), 'end' => array('ret' => true)),
		);
		$ope = strtoupper($ope);
		$checkPattern = $checkPatterns[$ope];
		$value = array_values($check);
		$value = $value[0];
		if ($value != $requireValue) {
			return true;
		}
		foreach ($others as $other) {
			$checkData = Hash::get($model->data, $other);
			$otherFieldsName = explode('.', $other);
			// is_系のフィールドの場合、チェックボックスで実装され、OFFでも０という数値が入ってくる
			// そうすると「Blank」判定してほしいのに「ある」と判定されてしまう
			// なのでis_で始まるフィールドのデータの設定を確認するときだけは == falseで判定する
			if (strncmp('is_', $otherFieldsName[count($otherFieldsName) - 1], 3) === 0) {
				$ret = ($checkData == false);
			} else {
				$ret = Validation::blank($checkData);
			}
			if ($ret == $checkPattern['midstream']['chk']) {
				return $checkPattern['midstream']['ret'];
			}
		}
		return $checkPattern['end']['ret'];
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

		if ($check == QuestionnairesComponent::USES_USE) {
			$nowTime = (new NetCommonsTime())->getNowDatetime();
			$nowTime = strtotime($nowTime);
			if ($nowTime < strtotime($startTime)) {
				$ret = QuestionnairesComponent::QUESTIONNAIRE_PERIOD_STAT_BEFORE;
			}
			if ($nowTime > strtotime($endTime)) {
				$ret = QuestionnairesComponent::QUESTIONNAIRE_PERIOD_STAT_END;
			}
		}
		return $ret;
	}
}