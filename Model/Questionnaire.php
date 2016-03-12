<?php
/**
 * Questionnaire Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for Questionnaire Model
 */
class Questionnaire extends QuestionnairesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Workflow.Workflow',
		'Workflow.WorkflowComment',
		'AuthorizationKeys.AuthorizationKey',
		'Questionnaires.QuestionnaireValidate',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuestionnairePage' => array(
			'className' => 'Questionnaires.QuestionnairePage',
			'foreignKey' => 'questionnaire_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('page_sequence ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

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
			'Frame' => 'Frames.Frame',
			'QuestionnaireSetting' => 'Questionnaires.QuestionnaireSetting',
			'QuestionnairePage' => 'Questionnaires.QuestionnairePage',
			'QuestionnaireFrameDisplayQuestionnaire' => 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
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
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'on' => 'update', // Limit validation to 'create' or 'update' operations 新規の時はブロックIDがなかったりするから
				)
			),
			'title' => array(
					'rule' => 'notBlank',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('questionnaires', 'Title')),
					'required' => true,
					'allowEmpty' => false,
					'required' => true,
			),
			'answer_timing' => array(
				'publicTypeCheck' => array(
					'rule' => array('inList', array(QuestionnairesComponent::USES_USE, QuestionnairesComponent::USES_NOT_USE)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('Questionnaire.answer_start_period', 'Questionnaire.answer_end_period'), 'OR'),
					'message' => __d('questionnaires', 'if you set the period, please set time.')
				)
			),
			'answer_start_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('questionnaires', 'Invalid datetime format.')
				)
			),
			'answer_end_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('questionnaires', 'Invalid datetime format.')
				),
				'checkDateComp' => array(
					'rule' => array('checkDateComp', '>=', 'answer_start_period'),
					'message' => __d('questionnaires', 'start period must be smaller than end period')
				)
			),
			'is_total_show' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'total_show_timing' => array(
				'inList' => array(
					'rule' => array('inList', array(QuestionnairesComponent::USES_USE, QuestionnairesComponent::USES_NOT_USE)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('Questionnaire.total_show_start_period'), 'AND'),
					'message' => __d('questionnaires', 'if you set the period, please set time.')
				)
			),
			'total_show_start_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('questionnaires', 'Invalid datetime format.')
				)
			),
			'is_no_member_allow' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_anonymity' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_key_pass_use' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFieldsKey' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('AuthorizationKey.authorization_key'), 'AND'),
					'message' => __d('questionnaires', 'if you set the use key phrase period, please set key phrase text.')
				),
				'authentication' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('Questionnaire.is_image_authentication'), 'XOR'),
					'message' => __d('questionnaires', 'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			'is_repeat_allow' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_image_authentication' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'authentication' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('Questionnaire.is_key_pass_use'), 'XOR'),
					'message' => __d('questionnaires', 'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			'is_answer_mail_send' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));

		parent::beforeValidate($options);
		// 最低でも１ページは存在しないとエラー
		if (! isset($this->data['QuestionnairePage'][0])) {
			$this->validationErrors['pickup_error'] = __d('questionnaires', 'please set at least one page.');
		} else {
			// ページデータが存在する場合
			// 配下のページについてバリデート
			$validationErrors = array();
			$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage', true);
			$maxPageIndex = count($this->data['QuestionnairePage']);
			$options['maxPageIndex'] = $maxPageIndex;
			foreach ($this->data['QuestionnairePage'] as $pageIndex => $page) {
				// それぞれのページのフィールド確認
				$this->QuestionnairePage->create();
				$this->QuestionnairePage->set($page);
				// ページシーケンス番号の正当性を確認するため、現在の配列インデックスを渡す
				$options['pageIndex'] = $pageIndex;
				if (! $this->QuestionnairePage->validates($options)) {
					$validationErrors['QuestionnairePage'][$pageIndex] = $this->QuestionnairePage->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}
		// 引き続きアンケート本体のバリデートを実施してもらうためtrueを返す
		return true;
	}

/**
 * AfterFind Callback function
 *
 * @param array $results found data records
 * @param bool $primary indicates whether or not the current model was the model that the query originated on or whether or not this model was queried as an association
 * @return mixed
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function afterFind($results, $primary = false) {
		if ($this->recursive == -1) {
			return $results;
		}
		$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage', true);
		$this->QuestionnaireAnswerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary', true);

		foreach ($results as &$val) {
			// この場合はcount
			if (! isset($val['Questionnaire']['id'])) {
				continue;
			}
			// この場合はdelete
			if (! isset($val['Questionnaire']['key'])) {
				continue;
			}

			$val['Questionnaire']['period_range_stat'] = $this->getPeriodStatus(
				isset($val['Questionnaire']['answer_timing']) ? $val['Questionnaire']['answer_timing'] : false,
				$val['Questionnaire']['answer_start_period'],
				$val['Questionnaire']['answer_end_period']);

			//
			// ページ配下の質問データも取り出す
			// かつ、ページ数、質問数もカウントする
			$val['Questionnaire']['page_count'] = 0;
			$val['Questionnaire']['question_count'] = 0;
			$this->QuestionnairePage->setPageToQuestionnaire($val);

			$val['Questionnaire']['all_answer_count'] = $this->QuestionnaireAnswerSummary->find('count', array(
				'conditions' => array(
					'questionnaire_key' => $val['Questionnaire']['key'],
					'answer_status' => QuestionnairesComponent::ACTION_ACT,
					'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM
				),
				'recursive' => -1
			));
		}
		return $results;
	}

/**
 * After frame save hook
 *
 * このルームにすでにアンケートブロックが存在した場合で、かつ、現在フレームにまだブロックが結びついてない場合、
 * すでに存在するブロックと現在フレームを結びつける
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function afterFrameSave($data) {
		$frame = $data['Frame'];

		$this->begin();

		try {
			$this->_saveBlock($frame);
			// 設定情報も
			$this->_saveSetting();
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		return $data;
	}

/**
 * save block
 *
 * afterFrameSaveやsaveQuestionnaireから呼び出される
 *
 * @param array $frame frame data
 * @return bool
 * @throws InternalErrorException
 */
	protected function _saveBlock($frame) {
		// すでに結びついている場合はBlockは作らないでよい
		if (! empty($frame['Frame']['block_id'])) {
			return;
		}
		// ルームに存在するブロックを探す
		$block = $this->Block->find('first', array(
			'conditions' => array(
				'Block.room_id' => $frame['room_id'],
				'Block.plugin_key' => $frame['plugin_key'],
				'Block.language_id' => $frame['language_id'],
			)
		));
		// まだない場合
		if (empty($block)) {
			// 作成する
			$block = $this->Block->save(array(
				'room_id' => $frame['room_id'],
				'language_id' => $frame['language_id'],
				'plugin_key' => $frame['plugin_key'],
			));
			if (!$block) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			Current::$current['Block'] = $block['Block'];
		}

		$frame['block_id'] = $block['Block']['id'];
		if (!$this->Frame->save($frame)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		Current::$current['Frame']['block_id'] = $block['Block']['id'];
	}
/**
 * save setting
 *
 * afterFrameSaveやsaveQuestionnaireから呼び出される
 *
 * @return bool
 * @throws InternalErrorException
 */
	protected function _saveSetting() {
		// block settingはあるか
		$setting = $this->QuestionnaireSetting->getSetting();
		if (empty($setting)) {
			// ないときは作る
			$blockSetting = $this->QuestionnaireSetting->create();
			$blockSetting['QuestionnaireSetting']['block_key'] = Current::read('Block.key');	//$block['Block']['key'];
			$this->QuestionnaireSetting->saveQuestionnaireSetting($blockSetting);
		}
		return true;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getBaseCondition($addConditions = array()) {
		$conditions = $this->getWorkflowConditions(array(
			'block_id' => Current::read('Block.id'),
		));
		$conditions = array_merge($conditions, $addConditions);
		return $conditions;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getCondition($addConditions = array()) {
		// 基本条件（ワークフロー条件）
		$conditions = $this->getBaseCondition($addConditions);
		// 現在フレームに表示設定されているアンケートか
		$frameDisplay = ClassRegistry::init('Questionnaires.QuestionnaireFrameDisplayQuestionnaires');
		$keys = $frameDisplay->find(
			'list',
			array(
				'conditions' => array('QuestionnaireFrameDisplayQuestionnaires.frame_key' => Current::read('Frame.key')),
				'fields' => array('QuestionnaireFrameDisplayQuestionnaires.questionnaire_key'),
				'recursive' => -1
			)
		);
		$conditions['Questionnaire.key'] = $keys;

		$periodCondition = $this->_getPeriodConditions();
		$conditions[] = $periodCondition;

		if (! Current::read('User.id')) {
			$conditions['is_no_member_allow'] = QuestionnairesComponent::PERMISSION_PERMIT;
		}
		$conditions = Hash::merge($conditions, $addConditions);
		return $conditions;
	}

/**
 * 時限公開のconditionsを返す
 *
 * @return array
 */
	protected function _getPeriodConditions() {
		if (Current::permission('content_editable')) {
			return array();
		}
		$netCommonsTime = new NetCommonsTime();
		$nowTime = $netCommonsTime->getNowDatetime();

		$limitedConditions[] = array('OR' => array(
					'Questionnaire.answer_start_period <=' => $nowTime,
					'Questionnaire.answer_start_period' => null,
		));
		$limitedConditions[] = array(
			'OR' => array(
				'Questionnaire.answer_end_period >=' => $nowTime,
				'Questionnaire.answer_end_period' => null,
		));

		$timingConditions = array(
			'OR' => array(
				'Questionnaire.answer_timing' => QuestionnairesComponent::USES_NOT_USE,
				$limitedConditions,
		));

		$totalLimitCond[] = array('OR' => array(
			'Questionnaire.total_show_start_period <=' => $nowTime,
			'Questionnaire.total_show_start_period' => null,
		));

		$totalTimingCond = array(
			'Questionnaire.is_total_show' => QuestionnairesComponent::USES_USE,
			'OR' => array(
				'Questionnaire.total_show_timing' => QuestionnairesComponent::USES_NOT_USE,
				$totalLimitCond,
		));
		$timingConditions['OR'][] = $totalTimingCond;

		if (Current::permission('content_creatable')) {
			$timingConditions['OR']['Questionnaire.created_user'] = Current::read('User.id');
		}

		return $timingConditions;
	}

/**
 * saveQuestionnaire
 * save Questionnaire data
 *
 * @param array &$questionnaire questionnaire
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuestionnaire(&$questionnaire) {
		// 設定画面を表示する前にこのルームのアンケートブロックがあるか確認
		// 万が一、まだ存在しない場合には作成しておく
		// afterFrameSaveが呼ばれず、また最初に設定画面が開かれもしなかったような状況の想定
		$frame['Frame'] = Current::read('Frame');
		$this->afterFrameSave($frame);

		//トランザクションBegin
		$this->begin();

		try {
			$questionnaire['Questionnaire']['block_id'] = Current::read('Frame.block_id');
			$status = $questionnaire['Questionnaire']['status'];
			$this->create();
			// アンケートは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			// （ちなみにこのカット処理をbeforeSaveで共通でやってしまおうとしたが、
			//   beforeSaveでIDをカットしてもUPDATE動作になってしまっていたのでここに置くことにした)
			$questionnaire = Hash::remove($questionnaire, 'Questionnaire.id');

			$this->set($questionnaire);
			if (!$this->validates()) {
				return false;
			}

			$saveQuestionnaire = $this->save($questionnaire, false);
			if (! $saveQuestionnaire) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$questionnaireId = $this->id;

			// ページ以降のデータを登録
			$questionnaire = Hash::insert($questionnaire, 'QuestionnairePage.{n}.questionnaire_id', $questionnaireId);
			if (! $this->QuestionnairePage->saveQuestionnairePage($questionnaire['QuestionnairePage'])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// フレーム内表示対象アンケートに登録する
			if (! $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaire(array(
				'questionnaire_key' => $saveQuestionnaire['Questionnaire']['key'],
				'frame_key' => Current::read('Frame.key')
			))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// これまでのテスト回答データを消す
			$this->QuestionnaireAnswerSummary->deleteTestAnswerSummary($saveQuestionnaire['Questionnaire']['key'], $status);

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $questionnaire;
	}

/**
 * deleteQuestionnaire
 * Delete the questionnaire data set of specified ID
 *
 * @param array $data post data
 * @throws InternalErrorException
 * @return bool
 */
	public function deleteQuestionnaire($data) {
		$this->loadModels([
			'QuestionnaireFrameDisplayQuestionnaire' => 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
		]);
		$this->begin();
		try {
			// アンケート質問データ削除
			if (! $this->deleteAll(array(
					'Questionnaire.key' => $data['Questionnaire']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//コメントの削除
			$this->deleteCommentsByContentKey($data['Questionnaire']['key']);

			// アンケート表示設定削除
			if (! $this->QuestionnaireFrameDisplayQuestionnaire->deleteAll(array(
				'questionnaire_key' => $data['Questionnaire']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// アンケート回答削除
			if (! $this->QuestionnaireAnswerSummary->deleteAll(array(
				'questionnaire_key' => $data['Questionnaire']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}
/**
 * saveExportKey
 * update export key
 *
 * @param int $questionnaireId id of questionnaire
 * @param string $exportKey exported key ( finger print)
 * @throws InternalErrorException
 * @return bool
 */
	public function saveExportKey($questionnaireId, $exportKey) {
		$this->begin();
		try {
			$this->id = $questionnaireId;
			$this->saveField('export_key', $exportKey);
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}
/**
 * hasPublished method
 *
 * @param array $questionnaire questionnaire data
 * @return int
 */
	public function hasPublished($questionnaire) {
		if (isset($questionnaire['Questionnaire']['key'])) {
			$isPublished = $this->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'is_active' => true,
					'key' => $questionnaire['Questionnaire']['key']
				)
			));
		} else {
			$isPublished = 0;
		}
		return $isPublished;
	}

/**
 * clearQuestionnaireId アンケートデータからＩＤのみをクリアする
 *
 * @param array &$questionnaire アンケートデータ
 * @return void
 */
	public function clearQuestionnaireId(&$questionnaire) {
		foreach ($questionnaire as $qKey => $q) {
			if (is_array($q)) {
				$this->clearQuestionnaireId($questionnaire[$qKey]);
			} elseif (preg_match('/^id$/', $qKey) ||
				preg_match('/^key$/', $qKey) ||
				preg_match('/^created(.*?)/', $qKey) ||
				preg_match('/^modified(.*?)/', $qKey)) {
				unset($questionnaire[$qKey]);
			}
		}
	}
}
