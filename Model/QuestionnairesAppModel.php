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
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		// この継承クラスたちがValidateロジックを走らせる前に必ずDBを切り替える
		$this->setDataSource('master');
		return parent::beforeValidate($options);
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
			),
			array(
				'table' => 'questionnaire_frame_display_questionnaires',
				'alias' => 'QuestionnaireFrameDisplayQuestionnaires',
				'type' => 'left',
				'conditions' => array(
					'Questionnaire.origin_id = QuestionnaireFrameDisplayQuestionnaires.questionnaire_origin_id'
				)
			),
			array(
				'table' => 'blocks',
				'alias' => 'Block',
				'type' => 'left',
				'conditions' => array(
					'Questionnaire.block_id = Block.id'
				)
			),
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
