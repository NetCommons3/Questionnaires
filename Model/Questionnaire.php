<?php
/**
 * Questionnaire Model
 *
 * @property QuestionnairePage $QuestionnairePage
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
		'NetCommons.Publishable',
		'NetCommons.OriginalKey',
		'NetCommons.Trackable',
		'Questionnaires.QuestionnaireValidate',
	);

/**
 * Custom find methods
 *
 * @var array
 */
	public $findMethods = array(
		'getQListWithAnsCnt' => true
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
			'className' => 'QuestionnairePage',
			'foreignKey' => 'questionnaire_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('page_sequence' => 'ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
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
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'is_auto_translated' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'title' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('questionnaires', 'Title')),
					'required' => true
				),
			),
			'is_period' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', array('start_period', 'end_period'), 'OR'),
					'message' => __d('questionnaires', 'if you set the period, please set time.')
				)
			),
			'start_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('questionnaires', 'Invalid datetime format.')
				)
			),
			'end_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('questionnaires', 'Invalid datetime format.')
				),
				'checkDateComp' => array(
					'rule' => array('checkDateComp', '>=', 'start_period'),
					'message' => __d('questionnaires', 'start period must be smaller than end period')
				)
			),
			'total_show_timing' => array(
				'inList' => array(
					'rule' => array('inList', array(QuestionnairesComponent::USES_USE, QuestionnairesComponent::USES_NOT_USE)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', array('total_show_start_period'), 'AND'),
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
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', array('key_phrase'), 'AND'),
					'message' => __d('questionnaires', 'if you set the use key phrase period, please set key phrase text.')
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
			),
			'is_answer_mail_send' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));

		return parent::beforeValidate($options);
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
		$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage', true);
		$this->QuestionnaireAnswerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary', true);

		foreach ($results as &$val) {
			// これらの場合はcount か deleteか
			if (!isset($val['Questionnaire'])
				|| (count($val['Questionnaire']) == 1 && isset($val['Questionnaire']['id']))) {
				continue;
			}

			$val['Questionnaire']['period_range_stat'] = $this->getPeriodStatus(
				isset($val['Questionnaire']['is_period']) ? $val['Questionnaire']['is_period'] : false,
				$val['Questionnaire']['start_period'],
				$val['Questionnaire']['end_period']);

			//
			// ページ配下の質問データも取り出す
			// かつ、ページ数、質問数もカウントする
			$val['Questionnaire']['page_count'] = 0;
			$val['Questionnaire']['question_count'] = 0;
			$this->QuestionnairePage->setPageToQuestionnaire($val);

			$val['Questionnaire']['all_answer_count'] = $this->QuestionnaireAnswerSummary->find('count', array(
				'conditions' => array(
					'questionnaire_origin_id' => $val['Questionnaire']['origin_id'],
					'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM
				),
				'recursive' => -1
			));
		}
		return $results;
	}

/**
 * geQuestionnairesList
 * get questionnaires by specified block id and specified user id limited number
 *
 * @param array $conditions find condition
 * @param string $sessionId Session id
 * @param int $userId User id （if not specified, null)
 * @param array $filter Narrowing conditions currently envisioned answer status , editing status
 * @param array $sort Sort conditions
 * @param int $offset offset of select
 * @param int $limit limit number of select
 * @return array
 */
	public function getQuestionnairesList($conditions, $sessionId, $userId, $filter, $sort = 'modified DESC', $offset = 0, $limit = QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE) {
		$subQuery = $this->getQuestionnairesCommonForAnswer($sessionId, $userId);
		$list = $this->find('all', array(
			'fields' => array(
				'Block.*',
				'Questionnaire.*',
				'TrackableCreator.*',
				'TrackableUpdater.*',
				'CountAnswerSummary.*'
			),
			'recursive' => 0,
			'joins' => $subQuery,
			'conditions' => $conditions,
			'order' => 'Questionnaire.modified DESC',
			'limit' => $limit,
			'offset' => $offset
		));
		return $list;
	}

/**
 * get index sql condition method
 *
 * @param int $blockId block id
 * @param int $userId login user id
 * @param array $permissions ( viewVars )
 * @param datetime $currentDateTime date time
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getCondition($blockId, $userId, $permissions, $currentDateTime, $addConditions = array()) {
		$conditions = $this->getConditionForAnswer($blockId, $userId, $permissions, $currentDateTime, $addConditions);
		$conditions['NOT'] = array('QuestionnaireFrameDisplayQuestionnaires.id' => null);
		$conditions['QuestionnaireFrameDisplayQuestionnaires.frame_key'] = $permissions['frameKey'];

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * get index sql condition method
 *
 * @param int $blockId block id
 * @param int $userId login user id
 * @param array $permissions ( viewVars )
 * @param datetime $currentDateTime date time
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getConditionForAnswer($blockId, $userId, $permissions, $currentDateTime, $addConditions = array()) {
		$conditions = array(
			'block_id' => $blockId,
		);
		if (!$permissions['contentEditable']) {
			$conditions['is_active'] = true;
			$conditions['OR'] = array(
				'start_period <' => $currentDateTime,
				'is_period' => false,
			);
		} else {
			$conditions['is_latest'] = true;
		}
		if ($permissions['roomRoleKey'] == NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY) {
			$conditions['is_no_member_allow'] = QuestionnairesComponent::PERMISSION_PERMIT;
		}

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * get questionnaire for result display sql condition method
 *
 * @param int $blockId block id
 * @param int $userId login user id
 * @param array $permissions ( viewVars )
 * @param datetime $currentDateTime date time
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getConditionForResult($blockId, $userId, $permissions, $currentDateTime, $addConditions = array()) {
		$conditions = array(
			'block_id' => $blockId,
			'is_total_show' => QuestionnairesComponent::EXPRESSION_SHOW,

		);
		if (!$permissions['contentEditable']) {
			$conditions['is_active'] = true;
			$conditions['OR'] = array(
				'total_show_timing' => QuestionnairesComponent::USES_NOT_USE,
				'total_show_start_period <' => $currentDateTime,
			);
		} else {
			$conditions['is_latest'] = true;
		}
		if ($permissions['roomRoleKey'] == NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY) {
			$conditions['is_no_member_allow'] = QuestionnairesComponent::PERMISSION_PERMIT;
		}
		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * getDefaultQuestionnaire
 * get default data of questionnaires
 *
 * @param array $addData add data to Default data
 * @return array
 */
	public function getDefaultQuestionnaire($addData) {
		$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage', true);
		$questionnaire = array();
		$questionnaire['Questionnaire'] = Hash::merge(
			array(
				'title' => '',
				'key' => '',
				'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
				'is_total_show' => QuestionnairesComponent::EXPRESSION_SHOW,
				'is_period' => QuestionnairesComponent::USES_NOT_USE,
				'is_key_pass_use' => QuestionnairesComponent::USES_NOT_USE,
				'total_show_timing' => QuestionnairesComponent::USES_NOT_USE,
			),
			$addData);

		if (!isset($questionnaire['QuestionnairePage'][0])) {
			$questionnaire['QuestionnairePage'][0] = $this->QuestionnairePage->getDefaultPage($questionnaire);
		}
		return $questionnaire;
	}

/**
 * getQuestionnaireCloneById 指定されたIDにのアンケートデータのクローンを取得する
 *
 * @param int $questionnaireId アンケートID(編集なのでoriginではなくRAWなIDのほう
 * @return array
 */
	public function getQuestionnaireCloneById($questionnaireId) {
		$questionnaire = $this->find('first', array(
			'conditions' => array('Questionnaire.id' => $questionnaireId),
		));

		if (!$questionnaire) {
			return $this->getDefaultQuestionnaire(array('title' => ''));
		}
		// ID値のみクリア
		$this->__clearQuestionnaireId($questionnaire);

		return $questionnaire;
	}

/**
 * __clearQuestionnaireId アンケートデータからＩＤのみをクリアする
 *
 * @param array &$questionnaire アンケートデータ
 * @return void
 */
	private function __clearQuestionnaireId(&$questionnaire) {
		foreach ($questionnaire as $qKey => $q) {
			if (is_array($q)) {
				$this->__clearQuestionnaireId($questionnaire[$qKey]);
			} elseif (preg_match('/(.*?)id$/', $qKey) ||
				preg_match('/^key$/', $qKey) ||
				preg_match('/^created(.*?)/', $qKey) ||
				preg_match('/^modified(.*?)/', $qKey)) {
				unset($questionnaire[$qKey]);
			}
		}
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
		$this->loadModels([
			'QuestionnairePage' => 'Questionnaires.QuestionnairePage',
			'Block' => 'Blocks.Block',
			'Comment' => 'Comments.Comment',
			'QuestionnaireFrameDisplayQuestionnaire' => 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
		]);

		//トランザクションBegin
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		try {
			$status = $questionnaire['Questionnaire']['status'];

			// アンケートデータの準備
			$saveQuestionnaire = $this->_setupSaveData($questionnaire['Questionnaire'], $status);

			// 編集ステータスとコメントの関係性からのチェック
			if (!$this->Comment->validateByStatus($questionnaire, array('caller' => $this->name))) {
				$this->validationErrors = Hash::merge($this->validationErrors, $this->Comment->validationErrors);
				return false;
			}

			$this->create();
			$saveQuestionnaire = $this->save($saveQuestionnaire);
			if (!$saveQuestionnaire) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$questionnaireId = $this->id;

			$this->QuestionnairePage->saveQuestionnairePage($questionnaireId, $status, $questionnaire['QuestionnairePage']);

			//コメントの登録
			if ($this->Comment->data) {
				if (! $this->Comment->save(null, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}

			// フレーム内表示対象アンケートに登録する
			if (!$this->QuestionnaireFrameDisplayQuestionnaire->saveFrameDisplayQuestionnaire($questionnaire['Frame']['id'], $questionnaireId)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$this->QuestionnaireAnswerSummary->deleteTestAnswerSummary($saveQuestionnaire['Questionnaire']['origin_id'], $status);

			$dataSource->commit();
		} catch (Exception $ex) {
			$dataSource->rollback();
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
			'Comment' => 'Comments.Comment',
			'QuestionnaireFrameSetting' => 'Questionnaires.QuestionnaireFrameSetting',
			'QuestionnaireFrameDisplayQuestionnaire' => 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
		]);
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			// アンケート質問データ削除
			if (! $this->deleteAll(array(
					'Questionnaire.origin_id' => $data['Questionnaire']['origin_id']), true, false)) {
				// @codeCoverageIgnoreStart
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				// @codeCoverageIgnoreEnd
			}
			// 編集コメント削除
			$this->Comment->deleteByContentKey($data['Questionnaire']['key']);

			// アンケート表示設定削除
			if (! $this->QuestionnaireFrameDisplayQuestionnaire->deleteAll(array(
				'questionnaire_origin_id' => $data['Questionnaire']['origin_id']), true, false)) {
				// @codeCoverageIgnoreStart
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				// @codeCoverageIgnoreEnd
			}
			// アンケート回答削除
			if (! $this->QuestionnaireAnswerSummary->deleteAll(array(
				'questionnaire_origin_id' => $data['Questionnaire']['origin_id']), true, false)) {
				// @codeCoverageIgnoreStart
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				// @codeCoverageIgnoreEnd
			}
			$dataSource->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$dataSource->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}

}
