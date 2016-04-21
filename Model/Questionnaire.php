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
App::uses('NetCommonsUrl', 'NetCommons.Utility');

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
		// 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'Questionnaire.title',
			),
		),
		'Mails.MailQueueDelete',
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
			'QuestionnairePage' => 'Questionnaires.QuestionnairePage',
			'QuestionnaireSetting' =>
				'Questionnaires.QuestionnaireSetting',
			'QuestionnaireFrameDisplayQuestionnaire' =>
				'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
			'QuestionnaireAnswerSummary' =>
				'Questionnaires.QuestionnaireAnswerSummary',
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
		// ウィザード画面中はstatusチェックをしないでほしいので
		// ここに来る前にWorkflowBehaviorでつけられたstatus-validateを削除しておく
		if (Hash::check($options, 'validate') == QuestionnairesComponent::QUESTIONNAIRE_VALIDATE_TYPE) {
			$this->validate = Hash::remove($this->validate, 'status');
		}
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					// Limit validation to 'create' or 'update' operations 新規の時はブロックIDがなかったりするから
					'on' => 'update',
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
					'rule' => array(
						'inList', array(
							QuestionnairesComponent::USES_USE, QuestionnairesComponent::USES_NOT_USE
					)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array(
						'requireOtherFields',
						QuestionnairesComponent::USES_USE,
						array('Questionnaire.answer_start_period', 'Questionnaire.answer_end_period'),
						'OR'
					),
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
					'rule' => array(
						'inList',
						array(QuestionnairesComponent::USES_USE, QuestionnairesComponent::USES_NOT_USE)
					),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array(
						'requireOtherFields',
						QuestionnairesComponent::USES_USE,
						array('Questionnaire.total_show_start_period'),
						'AND'
					),
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
					'rule' => array(
						'requireOtherFields',
						QuestionnairesComponent::USES_USE,
						array('AuthorizationKey.authorization_key'),
						'AND'
					),
					'message' =>
						__d('questionnaires',
							'if you set the use key phrase period, please set key phrase text.')
				),
				'authentication' => array(
					'rule' => array(
						'requireOtherFields',
						QuestionnairesComponent::USES_USE,
						array('Questionnaire.is_image_authentication'),
						'XOR'
					),
					'message' =>
						__d('questionnaires',
							'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			//'is_repeat_allow' => array(
			//	'boolean' => array(
			//		'rule' => array('boolean'),
			//		'message' => __d('net_commons', 'Invalid request.'),
			//	),
			//),
			'is_image_authentication' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'authentication' => array(
					'rule' => array(
						'requireOtherFields',
						QuestionnairesComponent::USES_USE,
						array('Questionnaire.is_key_pass_use'),
						'XOR'
					),
					'message' =>
						__d('questionnaires',
							'Authentication key setting , image authentication , either only one can not be selected.')
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
			$this->validationErrors['pickup_error'] =
				__d('questionnaires', 'please set at least one page.');
		} else {
			// ページデータが存在する場合
			// 配下のページについてバリデート
			$validationErrors = array();
			$maxPageIndex = count($this->data['QuestionnairePage']);
			$options['maxPageIndex'] = $maxPageIndex;
			foreach ($this->data['QuestionnairePage'] as $pageIndex => $page) {
				// それぞれのページのフィールド確認
				$this->QuestionnairePage->create();
				$this->QuestionnairePage->set($page);
				// ページシーケンス番号の正当性を確認するため、現在の配列インデックスを渡す
				$options['pageIndex'] = $pageIndex;
				if (! $this->QuestionnairePage->validates($options)) {
					$validationErrors['QuestionnairePage'][$pageIndex] =
						$this->QuestionnairePage->validationErrors;
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

			$val['Questionnaire']['all_answer_count'] =
				$this->QuestionnaireAnswerSummary->find('count', array(
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
			$this->QuestionnaireSetting->saveBlock($frame);
			// 設定情報も
			$this->QuestionnaireSetting->saveSetting();
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
		$keys = $this->QuestionnaireFrameDisplayQuestionnaire->find(
			'list',
			array(
				'conditions' => array(
					'QuestionnaireFrameDisplayQuestionnaire.frame_key' => Current::read('Frame.key')),
				'fields' => array('QuestionnaireFrameDisplayQuestionnaire.questionnaire_key'),
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

		// 集計結果の表示はアンケート回答が始まっていることが前提
		$totalLimitPreCond = array(
			'Questionnaire.answer_timing' => QuestionnairesComponent::USES_NOT_USE,
			'OR' => array(
				'Questionnaire.answer_timing' => QuestionnairesComponent::USES_USE,
				'OR' => array(
					'Questionnaire.answer_start_period <=' => $nowTime,
					'Questionnaire.answer_start_period' => null,
				)
			)
		);
		$totalLimitCond[] = array('OR' => array(
			'Questionnaire.total_show_start_period <=' => $nowTime,
			'Questionnaire.total_show_start_period' => null,
		));

		$totalTimingCond = array(
			'Questionnaire.is_total_show' => QuestionnairesComponent::USES_USE,
			$totalLimitPreCond,
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
			// is_no_member_allowの値によってis_repeat_allowを決定する
			$questionnaire['Questionnaire']['is_repeat_allow'] = QuestionnairesComponent::USES_NOT_USE;
			if (Hash::get(
					$questionnaire,
					'Questionnaire.is_no_member_allow') == QuestionnairesComponent::USES_USE) {
				$questionnaire['Questionnaire']['is_repeat_allow'] = QuestionnairesComponent::USES_USE;
			}
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

			$this->_sendMail($questionnaire);

			$saveQuestionnaire = $this->save($questionnaire, false);
			if (! $saveQuestionnaire) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$questionnaireId = $this->id;

			// ページ以降のデータを登録
			$questionnaire = Hash::insert(
				$questionnaire,
				'QuestionnairePage.{n}.questionnaire_id',
				$questionnaireId);

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
			$this->QuestionnaireAnswerSummary->deleteTestAnswerSummary(
				$saveQuestionnaire['Questionnaire']['key'],
				$status);

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $questionnaire;
	}
/**
 * _sendMail
 * Send Questionnaire mail
 *
 * @param array $questionnaire questionnaire
 * @return void
 */
	protected function _sendMail($questionnaire) {
		// メールのembed のURL設定を行っておく
		$url = NetCommonsUrl::actionUrl(array(
			'controller' => 'questionnaire_answers',
			'action' => 'view',
			Current::read('Block.id'),
			$questionnaire['Questionnaire']['key'],
			'frame_id' => Current::read('Frame.id'),
		));
		$this->setAddEmbedTagValue('X-URL', $url);
		// 回答期間の設定があるときはリマインダ設定をする
		$netCommonsTime = new NetCommonsTime();
		if ($questionnaire['Questionnaire']['answer_timing'] == QuestionnairesComponent::USES_USE) {
			$sendTimes = array(
				$netCommonsTime->toServerDatetime($questionnaire['Questionnaire']['answer_start_period']),
			);
		} else {
			$sendTimes = array($netCommonsTime->getNowDatetime());
		}
		$this->setSendTimeReminder($sendTimes);
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
			$this->Behaviors->unload('Mails.MailQueue');
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
