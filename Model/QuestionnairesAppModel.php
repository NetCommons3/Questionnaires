<?php
/**
 * Questionnaires App Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

class QuestionnairesAppModel extends AppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
		'NetCommons.Publishable'
	);

/**
 * Checks if is_period is on, required start_period, end_period
 *
 * @param array $check check data array
 * @return bool
 */
	public function requireTimes($check) {
		if ($this->data['Questionnaire']['is_period'] == QuestionnairesComponent::USES_USE) {
			if (empty($this->data['Questionnaire']['start_period']) && empty($this->data['Questionnaire']['end_time'])) {
				return false;
			}
		}
		return true;
	}

/**
 * Checks if is_key_pass_use is on, required key_phrase
 *
 * @param array $check check data array
 * @return bool
 */
	public function requireKeyPhrase($check) {
		if ($this->data['Questionnaire']['is_key_pass_use'] == QuestionnairesComponent::USES_USE) {
			if (empty($this->data['Questionnaire']['key_phrase'])) {
				return false;
			}
		}
		return true;
	}

/**
 * Checks datetime null or datetime
 *
 * @param array $check check data array
 * @return bool
 */
	public function checkDateTime($check) {
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
 * @param string|array $check datetime string
 * @param string $operator Can be either a word or operand
 *    is greater >, is less <, greater or equal >=
 *    less or equal <=, is less <, equal to ==, not equal !=
 * @param string $compare compare datetime string
 * @return bool Success
 */
	public function checkDateComp($check, $operator, $compare) {
		// 比較対象がないので比較する必要なし
		if (empty($this->data['Questionnaire'][$compare])) {
			return true;
		}

		$check2 = strtotime($this->data['Questionnaire'][$compare]);
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
 * _getQuestionnairesCommonForAnswer アンケートに対する指定ユーザーの回答が存在するか
 *
 * @param string $sessionId セッションID
 * @param int $userId ユーザID （指定しない場合は null)
 * @return array
 */
	protected function _getQuestionnairesCommonForAnswer($sessionId, $userId) {
		$dbo = $this->getDataSource();
		$this->loadModels([
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
		]);
		if ($userId) {
			$conditions = array(
				'user_id' => $userId,
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
			);
		} else {
			$conditions = array(
				'session_value' => $sessionId,
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
			);
		}
		$answeredSummaryQuery = $dbo->buildStatement(
			array(
				'fields' => array('questionnaire_origin_id', 'count(id) AS answer_summary_count'),
				'table' => $dbo->fullTableName($this->QuestionnaireAnswerSummary),
				'alias' => 'CountAnswerSummary',
				'limit' => null,
				'offset' => null,
				'conditions' => $conditions,
				'joins' => array(),
				'group' => array('questionnaire_origin_id')
			),
			$this->QuestionnaireAnswerSummary
		);
		$subQueryArray = array(
			array('type' => 'left',
				'table' => '(' . $answeredSummaryQuery . ') AS CountAnswerSummary',
				'conditions' => 'Questionnaire.origin_id = CountAnswerSummary.questionnaire_origin_id'
			)
		);
		return $subQueryArray;
	}

/**
 * _setupSaveData 保存データを整える
 *
 * @param array $data アンケートデータ
 * @param int $status 編集ステータス
 * @return void
 */
	protected function _setupSaveData($data, $status) {
		$data = Hash::remove($data, 'id');
		$data = Hash::remove($data, 'modified_user');
		$data = Hash::remove($data, 'modified');
		$data = Hash::remove($data, 'QuestionnaireQuestion');
		$data = Hash::remove($data, 'QuestionnaireChoice');
		$data['status'] = $status; // == NetCommonsBlockComponent::STATUS_PUBLISHED ? true : false;
		if (! isset($data['origin_id']) || $data['origin_id'] == '') {
			$data['origin_id'] = '0';
		}
		$data['is_active'] = false;
		$data['is_latest'] = false;
		$data['language_id'] = 0;
		return $data;
	}

}
