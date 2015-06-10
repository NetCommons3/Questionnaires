<?php
/**
 * QuestionnaireFrameSetting Model
 *
 * @property Frame $Frame
 * @property QuestionnaireFrameDisplayQuestionnaire $QuestionnaireFrameDisplayQuestionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireFrameSetting Model
 */
class QuestionnaireFrameSetting extends QuestionnairesAppModel {

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
		)
	);

/**
 * getFrameSetting 指定されたframe_keyの設定要件を取り出す
 *
 * @param string $frameKey frame key
 * @return array ... displayNum sortField sortDirection
 */
	public function getQuestionnaireFrameSetting($frameKey) {
		$frameSetting = $this->find('first', array(
			'conditions' => array(
				'frame_key' => $frameKey
			),
			'recursive' => -1
		));

		if (!$frameSetting) {
			$displayType = QuestionnairesComponent::DISPLAY_TYPE_LIST;
			$displayNum = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE;
			$sort = 'modified';
			$dir = 'DESC';
		} else {
			$setting = $frameSetting['QuestionnaireFrameSetting'];
			$displayType = $setting['display_type'];
			$displayNum = $setting['display_num_per_page'];
			if ($setting['sort_type'] == QuestionnairesComponent::QUESTIONNAIRE_SORT_MODIFIED) {
				$sort = 'modified';
				$dir = 'DESC';
			} elseif ($setting['sort_type'] == QuestionnairesComponent::QUESTIONNAIRE_SORT_CREATED) {
				$sort = 'created';
				$dir = 'DESC';
			} elseif ($setting['sort_type'] == QuestionnairesComponent::QUESTIONNAIRE_SORT_TITLE) {
				$sort = 'title';
				$dir = 'ASC';
			} elseif ($setting['sort_type'] == QuestionnairesComponent::QUESTIONNAIRE_SORT_END) {
				$sort = 'end_period';
				$dir = 'ASC';
			}
		}
		return array($displayType, $displayNum, $sort, $dir);
	}

/**
 * getDefaultFrameSetting
 * return default frame setting
 *
 * @return array
 */
	public function getDefaultFrameSetting() {
		$frame = array(
			'QuestionnaireFrameSettings' => array(
				'display_type' => QuestionnairesComponent::DISPLAY_TYPE_LIST,
				'display_num_per_page' => QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
				'sort_type' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
			)
		);
		return $frame;
	}

/**
 * prepareBlock
 *
 * @param int $frameId frame id
 * @return bool
 * @throws InternalErrorException
 */
	public function prepareBlock($frameId) {
		// 指定のフレームにブロックが結びついているときtrue
		// まだ結びついてないときfalse
		// エラー発生時error throw
		$this->loadModels([
			'Block' => 'Blocks.Block',
		]);
		// このルームにすでにアンケートブロックが存在した場合で、
		// かつ、現在フレームにまだブロックが結びついてない場合、
		// すでに存在するブロックと現在フレームを結びつける
		$frame = $this->Frame->find('first', array(
			'conditions' => array(
				'Frame.id' => $frameId
			)
		));
		if (!$frame) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		if (!empty($frame['Frame']['block_id'])) {
			return true;
		}
		$block = $this->Block->find('first', array(
			'conditions' => array(
				'Block.room_id' => $frame['Frame']['room_id'],
				'Block.plugin_key' => 'questionnaires'
			)
		));
		if (empty($block)) {
			return false;
		}
		$frame['Frame']['block_id'] = $block['Block']['id'];
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			$this->Frame->save($frame);
			$dataSource->commit();
		} catch (Exception $ex) {
			$dataSource->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}

/**
 * cleanUpBlock
 *
 * @param int $frameId frame id
 * @param int $blockId block id
 * @return void
 * @throws InternalErrorException
 */
	public function cleanUpBlock($frameId, $blockId) {
		$this->loadModels([
			'Questionnaire' => 'Questionnaires.Questionnaire',
			'QuestionnaireBlocksSetting' => 'Questionnaires.QuestionnaireBlocksSetting',
			'Block' => 'Blocks.Block',
		]);

		// 現在ルームのアンケートコンテンツ残量がいくつかをチェックし、
		// その数が０個になっていたらブロックも消してしまう
		$block = $this->Block->findById($blockId);
		// すでにブロックが存在しない
		if (!$block) {
			return;
		}

		$questionnaireCnt = $this->Questionnaire->find('count', array(
			'conditions' => array(
				'Questionnaire.block_id' => $block['Block']['id']
			)
		));

		// まだアンケートがある
		if ($questionnaireCnt > 0) {
			return;
		}

		// もうアンケートがないなら消してしまう
		// ここでフレームとの関連もクリーンされる
		$this->Block->deleteBlock($block['Block']['key']);

		// questionnaireBlockSettingのレコードも削除する
		$this->QuestionnaireBlocksSetting->deleteAll(array(
			'block_key' => $block['Block']['key']
		));
	}

/**
 * prepareFrameSetting
 *
 * @param string $frameKey frame key
 * @return void
 * @throws Exception
 * @throws InternalErrorException
 */
	public function prepareFrameSetting($frameKey) {
		// フレームセッティング確認
		// まだ該当のフレームセッティングがない場合新たに作成しておく
		$frameSetting = $this->find('first', array(
			'conditions' => array(
				'frame_key' => $frameKey
			)
		));
		if ($frameSetting) {
			return;
		}
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			$frameSetting['frame_key'] = $frameKey;
			$frameSetting['display_type'] = QuestionnairesComponent::DISPLAY_TYPE_LIST;
			$frameSetting['display_num_per_page'] = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE;
			$frameSetting['sort_type'] = QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS;
			$this->save($frameSetting);
			$dataSource->commit();
		} catch (Exception $ex) {
			$dataSource->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
	}

/**
 * saveFrameSettings
 *
 * @param string $frameKey frame key
 * @param array $data save data
 * @return bool
 * @throws InternalErrorException
 */
	public function saveFrameSettings($frameKey, $data) {
		$this->loadModels([
			'QuestionnaireFrameDisplayQuestionnaire' => 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
		]);

		//トランザクションBegin
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			$data['QuestionnairesFrameSetting']['frame_key'] = $frameKey;

			// フレーム設定のバリデート
			$this->set($data['QuestionnairesFrameSetting']);
			if (! $this->validates($data)) {
				return false;
			}

			// フレームに表示するアンケート一覧設定のバリデート
			// 一覧表示タイプと単独表示タイプ
			if ($data['QuestionnaireFrameSetting']['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_LIST) {
				$displayQs = $data['QuestionnaireFrameDisplayQuestionnaires']['List']['questionnaire_origin_id'];
				$ret = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForList($frameKey, $displayQs);
			} else {
				$displayQs = $data['QuestionnaireFrameDisplayQuestionnaires']['Single']['questionnaire_origin_id'];
				$ret = $this->QuestionnaireFrameDisplayQuestionnaire->validateDisplayQuestionnaireForSingle($frameKey, $displayQs);
			}
			if ($ret == false) {
				$this->validationErrors = Hash::merge($this->validationErrors, $this->QuestionnaireFrameDisplayQuestionnaire->validationErrors);
				return false;
			}
			// フレーム設定の登録
			if (! $this->save($data, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			// フレームに表示するアンケート一覧設定の登録
			// 一覧表示タイプと単独表示タイプ
			if ($data['QuestionnaireFrameSetting']['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_LIST) {
				$displayQs = $data['QuestionnaireFrameDisplayQuestionnaires']['List']['questionnaire_origin_id'];
				$ret = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaireForList($frameKey, $displayQs);
			} else {
				$displayQs = $data['QuestionnaireFrameDisplayQuestionnaires']['Single']['questionnaire_origin_id'];
				$ret = $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaireForSingle($frameKey, $displayQs);
			}
			if ($ret == false) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$dataSource->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$dataSource->rollback();
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}
}
