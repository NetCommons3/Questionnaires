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
		'CreatedUser' => array(
			'className' => 'Users.UserAttributesUser',
			'foreignKey' => false,
			'conditions' => array(
				'Questionnaire.created_user = CreatedUser.user_id',
				'CreatedUser.key' => 'nickname'
			),
			'fields' => array('CreatedUser.key', 'CreatedUser.value'),
			'order' => ''
		),
		'ModifiedUser' => array(
			'className' => 'Users.UserAttributesUser',
			'foreignKey' => false,
			'conditions' => array(
				'Questionnaire.modified_user = ModifiedUser.user_id',
				'ModifiedUser.key' => 'nickname'
			),
			'fields' => array('ModifiedUser.key', 'ModifiedUser.value'),
			'order' => ''
		)
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
			'order' => '',
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
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => true,
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
				'requireTime' => array(
					'rule' => 'requireTimes',
					'message' => __d('questionnaires', 'if you set the answer period, please set start time or end time or both time.')
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
				'requireKeyPhrase' => array(
					'rule' => 'requireKeyPhrase',
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
		$this->loadModels([
			'QuestionnairePage' => 'Questionnaires.QuestionnairePage',
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
			'Comment' => 'Comments.Comment',
		]);

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
					'questionnaire_origin_id' => $val['Questionnaire']['origin_id']
				),
				'recursive' => -1
			));
		}
		return $results;
	}

/**
 * _findGetQListWithAnsCnt
 * custom "find" function called from pagination
 *
 * @param string $state find status before find or after find
 * @param array $query find query
 * @param array $results found records
 * @access protected
 * @return array
 */
	protected function _findGetQListWithAnsCnt($state, $query, $results = array()) {
		if ($state == 'before') {
			//$this->unbindModel(
			//	array('hasMany' => array('QuestionnairePage'))
			//);
			return $query;
		} elseif ($state == 'after') {
			return $results;
		}
		return $results;
	}

/**
 * Removes 'fields' key from count query on custom finds when it is an array,
 * as it will completely break the Model::_findCount() call
 *
 * @param string $state Either "before" or "after"
 * @param array $query find query
 * @param array $results found records
 * @return int The number of records found, or false
 * @access protected
 * @see Model::find()
 */
	protected function _findCount($state, $query, $results = array()) {
		if ($state === 'before') {
			if (isset($query['type']) && isset($this->findMethods[$query['type']])) {
				$query = $this->{'_find' . ucfirst($query['type'])}('before', $query);
				if (!empty($query['fields']) && is_array($query['fields'])) {
					if (!preg_match('/^count/i', current($query['fields']))) {
						unset($query['fields']);
					}
				}
			}
		}
		return parent::_findCount($state, $query, $results);
	}

/**
 * paginate paginate method override function
 *
 * @param null $conditions conditions
 * @param null $fields find fields
 * @param null $order order
 * @param null $limit limit
 * @param int $page page number
 * @param null $recursive query recursive nest
 * @param array $extra extra data
 * @return mixed
 */
	public function paginate($conditions = null, $fields = null, $order = null, $limit = null, $page = 1, $recursive = null, $extra = array()) {
		if (!empty($conditions)) {
			$params['conditions'] = $conditions;
		}
		if (!empty($fields)) {
			$params['fields'] = $fields;
		}
		if (!empty($order)) {
			$params['order'] = $order;
		}
		if (!empty($limit)) {
			$params['limit'] = $limit;
		}
		if (!empty($page)) {
			$params['page'] = $page;
		}
		if (!empty($recursive)) {
			$params['recursive'] = $recursive;
		}

		if (!empty($extra['type']) && $extra['type'] == 'getQListWithAnsCnt') {
			//カスタム設定
			$subQuery = $this->_getQuestionnairesCommonForAnswer($extra['sessionId'], $extra['userId']);

			$params['joins'] =	$subQuery;
			$params['fields'] = array(
				'Block.*',
				'Questionnaire.*',
				'CreatedUser.*',
				'ModifiedUser.*',
				'CountAnswerSummary.*'
			);
			return $this->find('getQListWithAnsCnt', $params);
		} else {
			//普通のpagination設定
			return $this->find('all', $params);
		}
	}

/**
 * paginate paginateCount method override function
 *
 * @param null $conditions get conditions
 * @param int $recursive recursive nest
 * @param array $extra extra data
 * @return int
 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		if (!empty($conditions)) {
			$params['conditions'] = $conditions;
		}
		if (!empty($recursive)) {
			$params['recursive'] = $recursive;
		}

		if (!empty($extra['type']) && $extra['type'] == 'getQListWithAnsCnt') {
			//カスタム設定
			$subQuery = $this->_getQuestionnairesCommonForAnswer($extra['sessionId'], $extra['userId']);
			$params['joins'] = $subQuery;
		}
		return $this->find('count', $params);
	}

/**
 * geQuestionnairesList
 * get questionnaires by specified block id and specified user id limited number
 *
 * @param array $viewVars netCommons variable parameters
 * @param string $sessionId Session id
 * @param int $userId User id （if not specified, null)
 * @param array $filter Narrowing conditions currently envisioned answer status , editing status
 * @param array $sort Sort conditions
 * @param int $offset offset of select
 * @param int $limit limit number of select
 * @return array
 */
	public function getQuestionnairesList($viewVars, $sessionId, $userId, $filter, $sort = 'modified DESC', $offset = 0, $limit = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE) {
		$conditions = array_merge($filter, array(
			'block_id' => $viewVars['blockId'],
		));
		if (!$viewVars['contentEditable']) {
			$conditions['is_active'] = true;
			$conditions['start_period <'] = date('Y-m-d h:i:s');
		} else {
			$conditions['is_latest'] = true;
		}

		$subQuery = $this->_getQuestionnairesCommonForAnswer($sessionId, $userId);

		$this->unbindModel(
			array('hasMany' => array('QuestionnairePage'))
		);
		$list = $this->find('all', array(
			'fields' => array(
				'Block.*',
				'Questionnaire.*',
				'CreatedUser.*',
				'ModifiedUser.*',
				'CountAnswerSummary.*'
			),
			'joins' => $subQuery,
			'conditions' => $conditions,
			'order' => 'Questionnaire.modified DESC',
			'limit' => $limit,
			'offset' => $offset
		));
		return $list;
	}

/**
 * getDefaultQuestionnaire
 * get default data of questionnaires
 *
 * @return array
 */
	public function getDefaultQuestionnaire() {
		$this->loadModels([
			'QuestionnairePage' => 'Questionnaires.QuestionnairePage',
		]);
		$questionnaire = array(
			'Questionnaire' => array(
				'title' => '',
				'key' => '',
				'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
				'is_total_show' => QuestionnairesComponent::EXPRESSION_SHOW),
		);

		if (!isset($questionnaire['QuestionnairePage'][0])) {
			$questionnaire['QuestionnairePage'][0] = $this->QuestionnairePage->getDefaultPage($questionnaire);
		}
		return $questionnaire;
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
		]);

		//トランザクションBegin
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		try {
			$status = $questionnaire['Questionnaire']['status'];

			// アンケートデータの準備
			$saveQuestionnaire = $this->_setupSaveData($questionnaire['Questionnaire'], $status);
			// まだブロックが存在しない状態でのアンケート作成であった場合
			//ブロックの登録をまず行う
			if (empty($saveQuestionnaire['block_id'])) {
				$block = $this->Block->saveByFrameId($questionnaire['Frame']['id'], false);
				$block['Block']['plugin_key'] = 'questionnaires';
				$this->Block->save($block);
				$saveQuestionnaire['block_id'] = $block['Block']['id'];
			}

			// 編集ステータスとコメントの関係性からのチェック
			if (!$this->Comment->validateByStatus($questionnaire, array('caller' => $this->name))) {
				$this->validationErrors = Hash::merge($this->validationErrors, $this->Comment->validationErrors);
				return false;
			}

			$this->create();
			if (! $this->save($saveQuestionnaire)) {
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

			// ブロックの後始末が必要
			$this->QuestionnaireFrameSetting->cleanUpBlock($data['Frame']['id'], $data['Block']['id']);

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
