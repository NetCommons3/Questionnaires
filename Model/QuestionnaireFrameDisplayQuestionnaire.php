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
	public $validate = array(
		'frame_key' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'questionnaire_origin_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Frame' => array(
			'className' => 'Frame',
			'foreignKey' => 'frame_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Questionnaire' => array(
			'className' => 'Questionnaire',
			'foreignKey' => 'questionnaire_origin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * saveFrameDisplayQuestionnaire
 * this function is called when save questionnaire
 *
 * @param int $frameId frame id
 * @param int $questionnaireId questionnaire id
 * @return bool
 */
	public function saveFrameDisplayQuestionnaire($frameId, $questionnaireId) {
		$frame = $this->Frame->find('first', array(
			'conditions' => array(
				'Frame.id' => $frameId
			)
		));
		if (!$frame) {
			return false;
		}
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array(
				'Questionnaire.id' => $questionnaireId
			)
		));
		if (!$questionnaire) {
			return false;
		}
		$qCount = $this->Questionnaire->find('count', array(
			'conditions' => array(
				'Questionnaire.origin_id' => $questionnaire['Questionnaire']['origin_id']
			)
		));
		if ($qCount == 1) {
			// 新規作成
			$this->loadModels([
				'QuestionnaireFrameSetting' => 'Questionnaires.QuestionnaireFrameSetting',
			]);
			$setting = $this->QuestionnaireFrameSetting->find('first', array(
				'conditions' => array(
					'frame_key' => $frame['Frame']['key'],
				)
			));
			if ($setting['QuestionnaireFrameSetting']['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_LIST) {
				$saveData = array(
					'frame_key' => $frame['Frame']['key'],
					'questionnaire_origin_id' => $questionnaire['Questionnaire']['origin_id']);
				return $this->saveDisplayQuestionnaire($saveData);
			}
		}
		// 編集
		// 既存データの編集時は、現在の表示設定から変更しない
		// 単独表示のときは現在の表示設定から変更しない
		return true;
	}

/**
 * validateDisplayQuestionnaireForList
 *
 * @param string $frameKey frame key
 * @param array $displayQs validate data
 * @return bool
 */
	public function validateDisplayQuestionnaireForList($frameKey, $displayQs) {
		foreach ($displayQs as $displayQuestionnaire) {
			if ($displayQuestionnaire != 0) {
				if (!$this->validateDisplayQuestionnaireForSingle($frameKey, $displayQuestionnaire)) {
					return false;
				}
			}
		}
		return true;
	}

/**
 * validateDisplayQuestionnaireForSingle
 *
 * @param string $frameKey frame key
 * @param array $displayQuestionnaire validate data
 * @return bool
 */
	public function validateDisplayQuestionnaireForSingle($frameKey, $displayQuestionnaire) {
		$this->set(array(
			'frame_key' => $frameKey,
			'questionnaire_origin_id' => $displayQuestionnaire
		));
		if (! $this->validates()) {
			return false;
		}
		return true;
	}

/**
 * saveDisplayQuestionnaireForList
 *
 * @param string $frameKey frame key
 * @param array $displayQs save data
 * @return bool
 */
	public function saveDisplayQuestionnaireForList($frameKey, $displayQs) {
		foreach ($displayQs as $originId => $displayQuestionnaire) {
			$saveQs = array(
				'frame_key' => $frameKey,
				'questionnaire_origin_id' => $originId
			);
			if ($displayQuestionnaire != 0) {
				if (!$this->saveDisplayQuestionnaire($saveQs)) {
					return false;
				}
			} else {
				if (!$this->deleteDisplayQuestionnaire($saveQs)) {
					return false;
				}
			}
		}

		return true;
	}

/**
 * saveDisplayQuestionnaireForSingle
 *
 * @param string $frameKey frame key
 * @param array $displayQs save data
 * @return bool
 */
	public function saveDisplayQuestionnaireForSingle($frameKey, $displayQs) {
		$deleteQs = array(
			'frame_key' => $frameKey,
		);
		$this->deleteDisplayQuestionnaire($deleteQs);

		$saveQs = array(
			'frame_key' => $frameKey,
			'questionnaire_origin_id' => $displayQs,
		);
		if (!$this->saveDisplayQuestionnaire($saveQs)) {
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
 * deleteDisplayQuestionnaire
 * delete record QuestionnaireFrameDisplayQuestionnaire
 *
 * @param array $data delete condition
 * @return bool
 */
	public function deleteDisplayQuestionnaire($data) {
		$displayQuestionnaire = $this->find('all', array(
			'conditions' => $data
		));
		if (! empty($displayQuestionnaire)) {
			foreach ($displayQuestionnaire as $questionnaire) {
				$this->delete($questionnaire['QuestionnaireFrameDisplayQuestionnaire']['id']);
			}
		}
		return true;
	}
}
