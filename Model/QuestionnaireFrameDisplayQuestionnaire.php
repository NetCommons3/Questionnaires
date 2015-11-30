<?php
/**
 * QuestionnaireFrameDisplayQuestionnaire Model
 *
 * @property QuestionnaireFrameSetting $QuestionnaireFrameSetting
 * @property Questionnaire $Questionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireFrameDisplayQuestionnaire Model
 */
class QuestionnaireFrameDisplayQuestionnaire extends QuestionnairesAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Frame' => array(
			'className' => 'Frames.Frame',
			'foreignKey' => 'frame_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Questionnaire' => array(
			'className' => 'Questionnaires.Questionnaire',
			'foreignKey' => 'questionnaire_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'questionnaire_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		));
		parent::beforeValidate($options);

		return true;
	}

/**
 * validateFrameDisplayQuestionnaire
 *
 * @param mix $data PostData
 * @return bool
 */
	public function validateFrameDisplayQuestionnaire($data) {
		if ($data['QuestionnaireFrameSetting']['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
			$saveData = Hash::extract($data, 'Single.QuestionnaireFrameDisplayQuestionnaires');
			$this->set($saveData);
			$ret = $this->validates();
		} else {
			$saveData = $data['QuestionnaireFrameDisplayQuestionnaires'];
			$ret = $this->saveAll($saveData, array('validate' => 'only'));
		}
		return $ret;
	}
/**
 * saveFrameDisplayQuestionnaire
 * this function is called when save questionnaire
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveFrameDisplayQuestionnaire($data) {
		//トランザクションは親元のQuestionnaireFrameSettingでやっているので不要
		if ($data['QuestionnaireFrameSetting']['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
			// このフレームに設定されている全てのレコードを消す
			// POSTされたアンケートのレコードのみ作成する
			$ret = $this->saveDisplayQuestionnaireForSingle($data);
		} else {
			// hiddenでPOSTされたレコードについて全て処理する
			// POSTのis_displayが０，１によってdeleteかinsertで処理する
			$ret = $this->saveDisplayQuestionnaireForList($data);
		}
		return $ret;
	}

/**
 * saveDisplayQuestionnaireForList
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveDisplayQuestionnaireForList($data) {
		$frameKey = Current::read('Frame.key');

		foreach ($data['QuestionnaireFrameDisplayQuestionnaires'] as $index => $value) {
			$questionnaireKey = $value['questionnaire_key'];
			$isDisplay = $data['List']['QuestionnaireFrameDisplayQuestionnaires'][$index]['is_display'];
			$saveQs = array(
				'frame_key' => $frameKey,
				'questionnaire_key' => $questionnaireKey
			);
			if ($isDisplay != 0) {
				if (!$this->saveDisplayQuestionnaire($saveQs)) {
					return false;
				}
			} else {
				if (!$this->deleteAll($saveQs, false)) {
					return false;
				}
			}
		}
		if (!$this->updateFrameDefaultAction("''")) {
			return false;
		}
		return true;
	}

/**
 * saveDisplayQuestionnaireForSingle
 *
 * @param mix $data PostData
 * @return bool
 */
	public function saveDisplayQuestionnaireForSingle($data) {
		$frameKey = Current::read('Frame.key');
		$deleteQs = array(
			'frame_key' => $frameKey,
		);
		$this->deleteAll($deleteQs, false);

		$saveData = Hash::extract($data, 'Single.QuestionnaireFrameDisplayQuestionnaires');
		$saveData['frame_key'] = $frameKey;
		if (!$this->saveDisplayQuestionnaire($saveData)) {
			return false;
		}
		$action = "'questionnaires/questionnaire_answers/view/" . Current::read('Block.id') . '/' . $saveData['questionnaire_key'] . "'";
		if (!$this->updateFrameDefaultAction($action)) {
			return false;
		}
		return true;
	}

/**
 * saveDisplayQuestionnaire
 * saveQuestionnaireFrameDisplayQuestionnaire
 *
 * @param array $data save data
 * @return bool
 */
	public function saveDisplayQuestionnaire($data) {
		// 該当データを検索して
		$displayQuestionnaire = $this->find('first', array(
			'conditions' => $data
		));
		if (! empty($displayQuestionnaire)) {
			// あるならもう作らない
			return true;
		}
		$this->create();
		if (!$this->save($data)) {
			return false;
		}
		return true;
	}
/**
 * updateFrameDefaultAction
 * update Frame default_action
 *
 * @param string $action default_action
 * @return bool
 */
	public function updateFrameDefaultAction($action) {
		// frameのdefault_actionを変更しておく
		$this->loadModels([
			'Frame' => 'Frames.Frame',
		]);
		$conditions = array(
			'Frame.key' => Current::read('Frame.key')
		);
		$frameData = array(
			'default_action' => $action
		);
		if (! $this->Frame->updateAll($frameData, $conditions)) {
			return false;
		}
		return true;
	}
}
