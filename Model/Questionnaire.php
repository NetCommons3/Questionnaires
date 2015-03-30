<?php
/**
 * Questionnaires Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for Questionnaire Model
 */
class Questionnaire extends QuestionnairesAppModel
{

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'block_id' => array(
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
		'QuestionnaireAnswerSummary' => array(
			'className' => 'QuestionnaireAnswerSummary',
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
		'QuestionnaireEntity' => array(
			'className' => 'QuestionnaireEntity',
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
		)
	);

	/**
	 * getQuestionnairesCountForAnswer 指定されたroom、指定されたuser_idのアンケートデータ件数を取得する
	 * @param int $roomId ルームID
	 * @param int $userId ユーザID （指定しない場合は null)
	 * @param string $answer_status 回答ステータス
	 * @return int
	 */
	public function getQuestionnairesCountForAnswer($roomId, $contentEditable, $roomRoleKey, $userId, $sessionId, $answer_status)
	{

		$conds = $this->_getQuestionnairesCommonForAnswer($roomId, $contentEditable, $roomRoleKey, $userId, $sessionId, $answer_status);

		$totalCount = $this->find('count', array(
			'fields' => array(
				'Block.*',
				'Questionnaire.*',
				'CreatedUser.*',
				'ModifiedUser.*',
				'LatestEntity.*',
				'LatestAnswerSummary.*'
			),
			'conditions' => array_merge($conds['cond'], array('Block.room_id' => $roomId, 'Questionnaire.questionnaire_status' => QuestionnairesComponent::STATUS_STARTED)),
			'joins' => $conds['subQueryArray']
		));

		//$this->log("totalCount[".$totalCount."]",QUESTIONNAIRE_DEBUG);

		return $totalCount;
	}

	/**
	 * getQuestionnairesForAnswer 指定されたroom、指定されたuser_idのアンケートデータを取得する
	 * @param int $roomId ルームID
	 * @param int $userId ユーザID （指定しない場合は null)
	 * @param string $answer_status 回答ステータス
	 * @param int $offset SELECTのOFFSET
	 * @param int $limit SELECTのlimit
	 * @return array
	 */
	public function getQuestionnairesForAnswer($roomId, $contentEditable, $roomRoleKey, $userId, $sessionId, $answer_status, $offset = 0, $limit = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE)
	{

		$conds = $this->_getQuestionnairesCommonForAnswer($roomId, $contentEditable, $roomRoleKey, $userId, $sessionId, $answer_status);

		$questionnaires = $this->find('all', array(
			'fields' => array(
				'Block.*',
				'Questionnaire.*',
				'CreatedUser.*',
				'ModifiedUser.*',
				'LatestEntity.*',
				'LatestAnswerSummary.*'
			),
			'conditions' => array_merge($conds['cond'], array('Block.room_id' => $roomId, 'Questionnaire.questionnaire_status' => QuestionnairesComponent::STATUS_STARTED)),
			'order' => array('LatestEntity.modified DESC'),
			'offset' => $offset,
			'limit' => $limit,
			'joins' => $conds['subQueryArray']
		));

		//$this->log(print_r($questionnaires, true),QUESTIONNAIRE_DEBUG);
		$this->__replenishmentQuestionnaire($questionnaires, 'LatestEntity');

		return $questionnaires;
	}

	/**
	 * getQuestionnairesCommonForAnswer 指定されたroom、指定されたuser_idのアンケートデータ
	 *                                  の件数またはデータを取得する
	 * @param int $roomId ルームID
	 * @param int $userId ユーザID （指定しない場合は null)
	 * @param string $answer_status 回答ステータス
	 * @return array
	 */
	private function _getQuestionnairesCommonForAnswer($roomId, $contentEditable, $roomRoleKey, $userId, $sessionId, $answer_status)
	{
		$dbo = $this->getDataSource();
		$QuestionnaireEntity = Classregistry::init('Questionnaires.QuestionnaireEntity');
		$entityLatestQuery = $dbo->buildStatement(
			array(
				'fields' => array('questionnaire_id', 'max(id) AS entity_id'),
				'table' => $dbo->fullTableName($QuestionnaireEntity),
				'alias' => 'EntityLatest',
				'limit' => null,
				'offset' => null,
				'conditions' => ($contentEditable) ? null : array('status' => NetCommonsBlockComponent::STATUS_PUBLISHED),
				'joins' => array(),
				'group' => array('questionnaire_id')
			),
			$QuestionnaireEntity
		);
		$entitySubQuery = $dbo->buildStatement(
			array(
				'fields' => array('Entity.*'),
				'table' => $dbo->fullTableName($QuestionnaireEntity),
				'alias' => 'Entity',
				'limit' => null,
				'offset' => null,
				'foreignKey' => false,
				'joins' => array(
					array('type' => 'inner',
						'table' => '(' . $entityLatestQuery . ') AS Latest',
						'conditions' => 'Entity.questionnaire_id = Latest.questionnaire_id AND Entity.id = Latest.entity_id'
					),
				),
			),
			$QuestionnaireEntity
		);

		/**** SQL 誤り ここでEntityを取ってはいけない！
		 * $entityLatestQuery = $dbo->buildStatement(
		 * array(
		 * 'fields' => array('questionnaire_id', 'max(id) AS entity_id',
		 * 'status','no_member_flag','repeate_flag', 'start_period', 'end_period', 'title','sub_title','total_show_flag','modified'),
		 * 'table' => $dbo->fullTableName($QuestionnaireEntity),
		 * 'alias' => 'MaxEntity',
		 * 'limit' => null,
		 * 'offset' => null,
		 * 'conditions' => null,
		 * 'joins' => array(),
		 * 'group' => array('questionnaire_id')
		 * ),
		 * $QuestionnaireEntity
		 * );
		 ****/
		//回答済の定義：下記条件にマッチするquestionnaire_answer_summariesが１件以上のケース
		//
		//questionnaire_answer_summaries#user_id = user_id(会員idが一致) &&
		//questionnaire_answer_summaries#answer_status = 1(回答完了) &&
		//questionnaire_answer_summaries#test_status = 0(本番回答) &&
		//
		//questionnaire_entities#no_member_flag = 0(非会員の回答を許可しない) &&
		//questionnaire_entities#repeate_flag = 0(繰り返し回答を許可しない)
		//
		//
		// (注1）
		//questionnaire_entities#anonymity_flag について。会員の場合、非匿名,匿名に
		//関係なくuser_idで内部的に人物特定ができる＝繰り返し回答数をチェックできるので、
		//repeate_flagの条件のみをつかい、anonymity_flagは使わない。
		//
		//（注2）
		//questionnaire_answer_summaries#answer_number(回答回数)は使わない.
		//回答完了フラグ1で1回以上と判断できるから。
		//
		//未回答の定義：　回答済が０件のケース

		$QuestionnaireAnswerSummary = Classregistry::init('Questionnaires.QuestionnaireAnswerSummary');
		$answeredSummaryQuery = $dbo->buildStatement(
			array(
				'fields' => array('questionnaire_id', 'max(id) AS answer_summary_id'),
				'table' => $dbo->fullTableName($QuestionnaireAnswerSummary),
				'alias' => 'MaxAnswerSummary',
				'limit' => null,
				'offset' => null,
				'conditions' => array(
					'user_id' => $userId,
					'answer_status' => QuestionnairesComponent::ACTION_ACT,
					'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
					'session_value' => $sessionId
				),
				'joins' => array(),
				'group' => array('questionnaire_id')
			),
			$QuestionnaireAnswerSummary
		);

		$subQueryArray = array(
			array('type' => 'left',
				'table' => '(' . $entitySubQuery . ') AS LatestEntity',
				'conditions' => 'Questionnaire.id = LatestEntity.questionnaire_id'
			),
			array('type' => 'left',
				'table' => '(' . $answeredSummaryQuery . ') AS LatestAnswerSummary',
				'conditions' => 'Questionnaire.id = LatestAnswerSummary.questionnaire_id'
			)
		);

		$cond = array();
		if (!$contentEditable) {
			$cond['LatestEntity.status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}
		if ($roomRoleKey == NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY) {
			$cond['LatestEntity.no_member_flag'] = QuestionnairesComponent::PERMISSION_PERMIT;
		}
		switch ($answer_status) {
			case QUESTIONNAIRE_ANSEWER_TEST:
				if ($contentEditable) {
					$cond['NOT'] = array('LatestEntity.status' => NetCommonsBlockComponent::STATUS_PUBLISHED);
				}
				break;
			case QUESTIONNAIRE_ANSEWER_UNANSERERED:
				$cond['LatestEntity.status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
				$cond['LatestAnswerSummary.answer_summary_id'] = null;
				break;
			case QUESTIONNAIRE_ANSEWER_ANSWERED:
				$cond['LatestEntity.status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
				$cond['NOT'] = array('LatestAnswerSummary.answer_summary_id' => null);
				break;
		}

		/*****************************************************************FUJIWARA下記判定までの詳細条件は不要と判断した
		 * //部分条件. 回答済
		 * $answered_cond = array(
		 * 'AND' => array(
		 * 'NOT' => array('LatestAnswerSummary.answer_summary_id' => null),
		 * 'LatestEntity.no_member_flag = ?' => QuestionnairesComponent::PERMISSION_NOT_PERMIT,
		 * 'LatestEntity.repeate_flag = ?' => QuestionnairesComponent::PERMISSION_NOT_PERMIT
		 * )
		 * );
		 * //部分条件. テスト
		 * //テスト。つまり、questionnaire_entitiesのstatusが、
		 * //2:公開申請中、3:下書き中、差し戻し中のいずれか。
		 * $test_cond =  array(
		 * 'AND' => array(
		 * 'Questionnaire.questionnaire_status != ?' => QuestionnairesComponent::STATUS_STARTED,
		 * 'LatestEntity.status in (?,?,?)'=> array(
		 * NetCommonsBlockComponent::STATUS_APPROVED,
		 * NetCommonsBlockComponent::STATUS_IN_DRAFT,
		 * NetCommonsBlockComponent::STATUS_DISAPPROVED
		 * )
		 * )
		 * );
		 *
		 * switch ($answer_status) {
		 * case QUESTIONNAIRE_ANSEWER_TEST:
		 * $cond =  $test_cond;
		 * break;
		 * default: //全表示.つまり、未回答＋回答済＋テスト
		 * $cond =  array(
		 * 'OR' => array(
		 * array(
		 * 'Questionnaire.questionnaire_status = ?' => QuestionnairesComponent::STATUS_STARTED,
		 * 'LatestEntity.status = ?' => NetCommonsBlockComponent::STATUS_PUBLISHED
		 * ),
		 * 'AND' => array(
		 * 'Questionnaire.questionnaire_status != ?' => QuestionnairesComponent::STATUS_STARTED,
		 * 'LatestEntity.status in (?,?,?)'=> array(
		 * NetCommonsBlockComponent::STATUS_APPROVED,
		 * NetCommonsBlockComponent::STATUS_IN_DRAFT,
		 * NetCommonsBlockComponent::STATUS_DISAPPROVED
		 * )
		 * )
		 * )
		 * );
		 *
		 * break;
		 * }
		 ******************************************************************************************/
		return array(
			'subQueryArray' => $subQueryArray,
			'cond' => $cond
		);
	}

	/**
	 * getQuestionnairesCount 指定されたroomに含まれるアンケートデータ総数を取得する
	 * @param int $roomId ルームID
	 * @param int $contentEditable 編集可能状態
	 * @param string $filter ステータスフィルタ
	 * @return int
	 */
	public function getQuestionnairesCount($roomId, $contentEditable, $filter)
	{

		$subQueryArray = $this->__getQuestionnaireSubQueries($contentEditable);

		// 編集権限の有無により緊急停止ステータスをも含めてみるかどうかを区別する
		if ($contentEditable) {
			$baseCondition = array('Block.room_id' => $roomId);
		} else {
			$baseCondition = array('Block.room_id' => $roomId, 'Questionnaire.questionnaire_status' => QuestionnairesComponent::STATUS_STARTED);
		}
		$totalCount = $this->find('count',
			array(
				'conditions' => array_merge($baseCondition, $filter),
				'joins' => $subQueryArray
			)
		);
		return $totalCount;
	}

	/**
	 * getQuestionnaires 指定されたroomに含まれるアンケートデータ一覧を取得する
	 * @param int $roomId ルームID
	 * @param int $contentEditable 編集可能状態
	 * @param string $filter ステータスフィルタ
	 * @param int $offset オフセット
	 * @param int $limit 取得件数
	 * @return array
	 */
	public function getQuestionnaires($roomId, $contentEditable, $filter = array(), $offset = 0, $limit = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE)
	{

		$subQueryArray = $this->__getQuestionnaireSubQueries($contentEditable);

		if ($contentEditable) {
			$baseCondition = array('Block.room_id' => $roomId);
		} else {
			$baseCondition = array('Block.room_id' => $roomId, 'Questionnaire.questionnaire_status' => QuestionnairesComponent::STATUS_STARTED);
		}

		$questionnaires = $this->find('all', array(
			'fields' => array(
				'Block.*',
				'Questionnaire.*',
				'Entity.*',
				'CreatedUser.*',
				'ModifiedUser.*',
				'AnswerSummary.answer_count'),
			'conditions' => array_merge($baseCondition, $filter),
			'order' => array('Questionnaire.created DESC'),
			'limit' => $limit,
			'offset' => $offset,
			'joins' => $subQueryArray,
		));

		$this->__replenishmentQuestionnaire($questionnaires, 'Entity');

		return $questionnaires;
	}

	/**
	 * saveQuestionnaire アンケートデータセットを登録する
	 * @param array $questionnaire データ
	 * @return boolean
	 */
	public function saveQuestionnaire(&$questionnaire)
	{

		$QuestionnaireEntity = Classregistry::init('Questionnaires.QuestionnaireEntity');
		$QuestionnairePage = Classregistry::init('Questionnaires.QuestionnairePage');
		$QuestionnaireQuestion = Classregistry::init('Questionnaires.QuestionnaireQuestion');
		$QuestionnaireChoice = Classregistry::init('Questionnaires.QuestionnaireChoice');

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		try {
			if (empty($questionnaire['Questionnaire']['id'])) {
				$this->create();
				$ret = $this->save($questionnaire['Questionnaire']);
				if (!$ret) {
					$this->log(print_r($this->validationErrors, true), 'debug');
				}
				$questionnaire_id = $this->id;
				$questionnaire['Questionnaire'] = $ret;
			} else {
				$questionnaire_id = $questionnaire['Questionnaire']['id'];
			}
			$entity = $questionnaire['QuestionnaireEntity'];
			$entity['questionnaire_id'] = $questionnaire_id;
			$entity = Hash::remove($entity, 'id');
			$entity = Hash::remove($entity, 'modified_user');
			$entity = Hash::remove($entity, 'modified');
			$QuestionnaireEntity->create();
			$ret = $QuestionnaireEntity->save($entity);
			if (!$ret) {
				$this->log(print_r($QuestionnaireEntity->validationErrors, true), 'debug');
			}
			$entity_id = $QuestionnaireEntity->id;
			$questionnaire['QuestionnaireEntity'] = $ret;


			$pages = $questionnaire['QuestionnairePage'];
			foreach ($pages as &$p) {
				$p['questionnaire_entity_id'] = $entity_id;
				$p = Hash::remove($p, 'id');
				$p = Hash::remove($p, 'modified_user');
				$p = Hash::remove($p, 'modified');
				$questions = isset($p['QuestionnaireQuestion']) ? $p['QuestionnaireQuestion'] : null;
				$p = Hash::remove($p, 'QuestionnaireQuestion');
				$QuestionnairePage->create();
				$ret = $QuestionnairePage->save($p);
				if (!$ret) {
					$this->log(print_r($QuestionnairePage->validationErrors, true), 'debug');
				}
				$page_id = $QuestionnairePage->id;
				$p = $ret;

				if ($questions) {
					foreach ($questions as &$q) {

						$q['questionnaire_page_id'] = $page_id;
						$q = Hash::remove($q, 'id');
						$q = Hash::remove($q, 'modified_user');
						$q = Hash::remove($q, 'modified');
						$choices = isset($q['QuestionnaireChoice']) ? $q['QuestionnaireChoice'] : null;
						$q = Hash::remove($q, 'QuestionnaireChoice');

						$QuestionnaireQuestion->create();
						$ret = $QuestionnaireQuestion->save($q);
						if (!$ret) {
							$this->log(print_r($QuestionnaireQuestion->validationErrors, true), 'debug');
						}
						$question_id = $QuestionnaireQuestion->id;
						$q = $ret;

						if ($choices) {
							foreach ($choices as &$c) {
								$c['questionnaire_question_id'] = $question_id;
								$c = Hash::remove($c, 'id');
								$c = Hash::remove($c, 'modified_user');
								$c = Hash::remove($c, 'modified');
								$QuestionnaireChoice->create();
								if ($ret = $QuestionnaireChoice->save($c)) {
									$c = $ret;
								} else {
									$this->log(print_r($QuestionnaireChoice->validationErrors, true), 'debug');
								}
							}
						}
					}
				}
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
	 * deleteQuestionnaire 指定されたIDのアンケートデータセットを削除する
	 * @param int $id アンケートID
	 * @return boolean
	 */
	public function deleteQuestionnaire($id)
	{
		$this->delete($id, true);
	}

	/**
	 * isAbleToDisplayAggrigatedData 指定されたIDを集計表示していいいかどうか？
	 * @param int $questionnaireId アンケートID
	 * @param boolean contentEditable 編集権限
	 * @param string roomRoleKey 現ルームにおいてのRole
	 * @param string sessionId 現在のセッションID
	 * @return boolean
	 */
	public function isAbleToDisplayAggrigatedData($questionnaireId, $contentEditable, $roomRoleKey, $userId, $sessionId)
	{
		//(A)
		$entity = $this->QuestionnaireEntity->getQuestionnaireEntityById($questionnaireId, $contentEditable);

		//集計結果を表示するか否か.. これは公開云々より重要
		if ($entity['QuestionnaireEntity']['total_show_flag'] != QuestionnairesComponent::EXPRESSION_SHOW) {
			//集計結果を表示しない
			return false;
		}

		if ($entity['QuestionnaireEntity']['status'] != NetCommonsBlockComponent::STATUS_PUBLISHED) {
			//公開状態じゃない。つまり、承認待ちか編集中など、
			if (!$contentEditable) {
				//編集権限ない
				//=>当然、集計表示しちゃため。
				return false;
			} else {
				//編集権限ある
				//=>未公開かつ編集権限ある場合、本人か管理者が、テストでアンケート回答したケースがこれ。
				//  テストの時は、アンケート回答後の集計結果も確認できるべき、と仕様整理しました。
				//  当然、集計表示を表示するタイミングも関係なく、集計表示してよい。
				return true;
			}
		}

		//ここ以降は、公開中

		if ($entity['Questionnaire']['questionnaire_status'] == QuestionnairesComponent::STATUS_STOPPED) {
			//停止中です。.停止中のアンケートは集計表示しない。
			return false;
		}

		if ($entity['QuestionnaireEntity']['total_show_timing'] != QuestionnairesComponent::USES_USE) {
			//集計結果を表示するタイミング＝アンケート回答後、すぐ。
			//
			//つまり、本人回答があるかどうがか表示有無の判断基準

			//本人回答を取り出す。
			App::import('Model', 'Questionnaires.QuestionnaireAnswerSummary');
			$QuestionnaireAnswerSummary = new QuestionnaireAnswerSummary;
			$summaries = $QuestionnaireAnswerSummary->getNowSummaryOfThisUser($questionnaireId, $userId, $sessionId);
			if(count($summaries) > 0){
				//本人による「回答」データあり
				return true;
			} else {
				//本人による「回答」データなし
				return false;
			}
		}

		//ここにくるのは、集計結果を表示するタイミング＝期間設定のケース
		//
		// つまり、期間内外で判断。
		if (!$this->__isAfterTotalShowStartPeirod($entity['QuestionnaireEntity'])) {
			//表示期間外
			return false;
		}
		
		//表示期間内
		return true;
	}

	/**
	 * isDuringTest テスト中かどうか。公開中の否定状態＝テスト中とするsimple判断関数。編集権限ありなし関係なし。
	 * @param int $id アンケートID
	 * @return boolean
	 */
	public function isDuringTest($questionnaireId,$contentEditable)
	{
		$entity = $this->QuestionnaireEntity->getQuestionnaireEntityById($questionnaireId, $contentEditable);
		if ($entity['QuestionnaireEntity']['status'] == NetCommonsBlockComponent::STATUS_PUBLISHED) {
			//公開中のみ、テスト中でない、とする。
			return false;
		}
		//公開中以外は、テスト中とする。
		return true;
	}
	/**
	 * isAbleToAnswer 指定されたIDに回答できるかどうか
	 * @param int $id アンケートID
	 * @param boolean contentEditable 編集権限
	 * @param string roomRoleKey 現ルームにおいてのRole
	 * @param string sessionId 現在のセッションID
	 * @return boolean
	 */
	public function isAbleToAnswer($questionnaireId, $contentEditable, $roomRoleKey, $userId, $sessionId)
	{
		$this->loadModels([
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
		]);

		// 指定のアンケートの状態と回答者の権限を照らし合わせてガードをかける
		// 編集権限を持っていない場合
		//   公開状態にない
		//   期間外
		//   停止中
		//   繰り返し回答
		//   会員以外には許してないのに未ログインである

		// 編集権限を持っている場合
		//   公開状態にない場合はALL_OK
		//
		// 　公開状態になっている場合は
		//   期間外
		//   停止中
		//   繰り返し回答
		//   会員以外には許してないのに未ログインである


		// 公開状態が「公開」になっている場合は編集権限の有無にかかわらず共通だ
		// なのでまずは公開状態だけを確認する

		$entity = $this->QuestionnaireEntity->getQuestionnaireEntityById($questionnaireId, $contentEditable);
		if ($entity['QuestionnaireEntity']['status'] != NetCommonsBlockComponent::STATUS_PUBLISHED) {
			if (!$contentEditable) {
				return false;
			} else {
				return true;
			}
		}

		// 停止中
		if ($entity['Questionnaire']['questionnaire_status'] == QuestionnairesComponent::STATUS_STOPPED) {
			return false;
		}
		// 期間外
		$ret = $this->__replenishmentPeriod($entity['QuestionnaireEntity']);
		if (!$entity['QuestionnaireEntity']['questionPeriodFlag']) {
			return false;
		}
		// 会員以外には許していないのに未ログイン
		if ($entity['QuestionnaireEntity']['no_member_flag'] == QuestionnairesComponent::PERMISSION_NOT_PERMIT) {
			if ($roomRoleKey == NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY) {
				return false;
			}
		}
		// 繰り返し回答を許していないのにすでに回答済みか
		if ($entity['QuestionnaireEntity']['repeate_flag'] == QuestionnairesComponent::PERMISSION_NOT_PERMIT) {
			$summary = $this->QuestionnaireAnswerSummary->getNowSummaryOfThisUser($questionnaireId, $userId, $sessionId);
			if ($summary) {
				return false;
			}
		}

		return true;
	}

	/**
	 * __getQuestionnaireSubQueries  サブクエリ取得
	 * @param array $questionnaires アンケートデータ配列
	 * @return bool
	 */
	private function __getQuestionnaireSubQueries($contentEditable)
	{
		$dbo = $this->getDataSource();
		$QuestionnaireAnswerSummary = Classregistry::init('Questionnaires.QuestionnaireAnswerSummary');
		$QuestionnaireEntity = Classregistry::init('Questionnaires.QuestionnaireEntity');

		$summaryAnswerSubQuery = $dbo->buildStatement(
			array(
				'fields' => array('questionnaire_id', 'COUNT(questionnaire_id) AS answer_count'),
				'table' => $dbo->fullTableName($QuestionnaireAnswerSummary),
				'alias' => 'AnswerSummary',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array('answer_status' => 0),
				'group' => array('questionnaire_id')
			),
			$QuestionnaireAnswerSummary
		);
		$entityLatestQuery = $dbo->buildStatement(
			array(
				'fields' => array('questionnaire_id', 'max(id) AS entity_id'),
				'table' => $dbo->fullTableName($QuestionnaireEntity),
				'alias' => 'EntityLatest',
				'limit' => null,
				'offset' => null,
				'conditions' => ($contentEditable) ? null : array('status' => NetCommonsBlockComponent::STATUS_PUBLISHED),
				'joins' => array(),
				'group' => array('questionnaire_id')
			),
			$QuestionnaireEntity
		);

		$entitySubQuery = $dbo->buildStatement(
			array(
				'fields' => array('Entity.*'),
				'table' => $dbo->fullTableName($QuestionnaireEntity),
				'alias' => 'Entity',
				'limit' => null,
				'offset' => null,
				'foreignKey' => false,
				'joins' => array(
					array('type' => 'inner',
						'table' => '(' . $entityLatestQuery . ') AS Latest',
						'conditions' => 'Entity.questionnaire_id = Latest.questionnaire_id AND Entity.id = Latest.entity_id'
					),
				),
			),
			$QuestionnaireEntity
		);
		return array(
			array('type' => 'left',
				'table' => '(' . $summaryAnswerSubQuery . ') AS AnswerSummary',
				'conditions' => 'Questionnaire.id = AnswerSummary.questionnaire_id'
			),
			array('type' => 'left',
				'table' => '(' . $entitySubQuery . ') AS Entity',
				'conditions' => 'Questionnaire.id = Entity.questionnaire_id'
			));
	}

	/**
	 * __replenishmentQuestionnaire  アンケートデータ情報の補完
	 *                               配列中の期間状況を見て期間状態を設定
	 * @param array $questionnaires アンケートデータ配列
	 * @return bool
	 */
	private function __replenishmentQuestionnaire(&$questionnaires, $entityName = 'QuestionnaireEntity')
	{
		if (!is_array($questionnaires)) {
			return;
		}
		foreach ($questionnaires as $firstIndex => &$q) {
			if (isset($q[$entityName])) {
				$ret = $this->__replenishmentPeriod($q[$entityName]);
				if ($q['Questionnaire']['questionnaire_status'] != QuestionnairesComponent::STATUS_STOPPED) {
					$q['Questionnaire']['questionnaire_status'] = $ret;
				}
			}
		}
		return true;
	}

	/**
	 * __replenishmentPeriod  期間フラグ補てん
	 * @param array $questionnaireEntity アンケートエンティティデータ配列
	 * @return bool
	 */
	private function __replenishmentPeriod(&$entity)	{
		$nowTime = time();
		$entity['questionPeriodFlag'] = true;
		if (isset($entity['start_period'])) {
			if ($nowTime < strtotime($entity['start_period'])) {
				$entity['questionPeriodFlag'] = false;
				return QuestionnairesComponent::STATUS_NOT_START;
			}
		}
		if (isset($entity['end_period'])) {
			if ($nowTime > strtotime($entity['end_period'])) {
				$entity['questionPeriodFlag'] = false;
			}
		}
		return QuestionnairesComponent::STATUS_STARTED;
	}

	/**
	 *　isAfterTotalShowStartPeirod 集計表示期間後かどうか
	 * @param array $questionnaireEntity アンケートエンティティデータ配列
	 * @return bool
	 */
	private function __isAfterTotalShowStartPeirod(&$entity) {
		$nowTime = time();
		if (isset($entity['total_show_start_peirod'])) {
			if ($nowTime > strtotime($entity['total_show_start_peirod'])) {
				return true; //集計表示期間後
			} else {
				return false; //集計表示期間前
			}
		}
		return true; //暫定：集計表示期間後
	}
}
