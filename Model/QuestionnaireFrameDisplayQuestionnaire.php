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
 * Questionnaire list for check
 *
 * @var array
 */
	public $chkQuestionnaireList = array();

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Questionnaire' => 'Questionnaires.Questionnaire',
		]);
	}
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
		// チェック用のアンケートリストを確保しておく
		$conditions = $this->Questionnaire->getBaseCondition();
		$questionnaires = $this->Questionnaire->find('all', array(
			'conditions' => $conditions,
			'recursive' => -1
		));
		$this->chkQuestionnaireList = Hash::combine(
			$questionnaires, '{n}.Questionnaire.id', '{n}.Questionnaire.key');

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
				'inList' => array(
					'rule' => array('inList', $this->chkQuestionnaireList),
					'message' => __d('net_commons', 'Invalid request.'),
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
		$frameSetting = $data['QuestionnaireFrameSetting'];

		if ($frameSetting['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
			$saveData = Hash::extract($data, 'Single.QuestionnaireFrameDisplayQuestionnaire');
			if (! $saveData) {
				return false;
			}
			$this->set($saveData);
			$ret = $this->validates();
		} else {
			$saveData = Hash::extract($data, 'List.QuestionnaireFrameDisplayQuestionnaire');
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
 * @throws $ex
 */
	public function saveFrameDisplayQuestionnaire($data) {
		if (! $this->validateFrameDisplayQuestionnaire($data)) {
			return false;
		}
		$frameSetting = $data['QuestionnaireFrameSetting'];

		//トランザクションBegin
		$this->begin();
		try {
			if ($frameSetting['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
				// このフレームに設定されている全てのレコードを消す
				// POSTされたアンケートのレコードのみ作成する
				$ret = $this->saveDisplayQuestionnaireForSingle($data);
			} else {
				// hiddenでPOSTされたレコードについて全て処理する
				// POSTのis_displayが０，１によってdeleteかinsertで処理する
				$ret = $this->saveDisplayQuestionnaireForList($data);
			}
			//トランザクションCommit
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $ret;
	}

/**
 * saveDisplayQuestionnaireForList
 *
 * @param mix $data PostData
 * @return bool
 * @throws InternalErrorException
 */
	public function saveDisplayQuestionnaireForList($data) {
		$frameKey = Current::read('Frame.key');

		foreach ($data['List']['QuestionnaireFrameDisplayQuestionnaire'] as $value) {
			$questionnaireKey = $value['questionnaire_key'];
			$isDisplay = $value['is_display'];
			$saveQs = array(
				'frame_key' => $frameKey,
				'questionnaire_key' => $questionnaireKey
			);
			if ($isDisplay != 0) {
				// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
				$this->saveDisplayQuestionnaire($saveQs);
			} else {
				if (! $this->deleteAll($saveQs, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
		}
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->updateFrameDefaultAction("''");

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

		$saveData = Hash::extract($data, 'Single.QuestionnaireFrameDisplayQuestionnaire');
		$saveData['frame_key'] = $frameKey;
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->saveDisplayQuestionnaire($saveData);
		$action = sprintf('\'questionnaire_answers/view/%s/%s\'',
			Current::read('Block.id'),
			$saveData['questionnaire_key']);
		// この関数内部でエラーがあった時は、Exceptionなので戻りは見ない
		$this->updateFrameDefaultAction($action);

		return true;
	}

/**
 * saveDisplayQuestionnaire
 * saveQuestionnaireFrameDisplayQuestionnaire
 *
 * @param array $data save data
 * @return bool
 * @throws InternalErrorException
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
		if (!$this->save($data, false)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return true;
	}
/**
 * updateFrameDefaultAction
 * update Frame default_action
 *
 * @param string $action default_action
 * @return bool
 * @throws InternalErrorException
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
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return true;
	}
}
