<?php
/**
 * QuestionnaireBlocksSetting Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireBlocksSetting Model
 */
class QuestionnaireSetting extends QuestionnairesAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'block_key' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Blocks.BlockRolePermission',
	);

/**
 * getSetting
 *
 * @return mix QuestionnaireBlockSetting data
 */
	public function getSetting() {
		$this->Block = ClassRegistry::init('Blocks.Block', true);
		$blockSetting = $this->Block->find('all', array(
			'recursive' => -1,
			'fields' => array(
				$this->Block->alias . '.*',
				$this->alias . '.*',
			),
			'joins' => array(
				array(
					'table' => $this->table,
					'alias' => $this->alias,
					'type' => 'INNER',
					'conditions' => array(
						$this->Block->alias . '.key' . ' = ' . $this->alias . ' .block_key',
					),
				),
			),
			'conditions' => array(
				'Block.id' => Current::read('Block.id')
			),
		));
		if (! $blockSetting) {
			return $blockSetting;
		}
		return $blockSetting[0];
	}
/**
 * Save questionnaire settings
 *
 * @param array $data received post data
 * @return bool True on success, false on failure
 * @throws InternalErrorException
 */
	public function saveQuestionnaireSetting($data) {
		//トランザクションBegin
		$this->begin();

		// idが未設定の場合は、指定されたblock_keyを頼りに既存レコードがないか調査
		$existRecord = $this->find('first', array(
			'recursive' => -1,
			'fields' => 'id',
			'conditions' => array(
				'block_key' => $data['QuestionnaireSetting']['block_key'],
			)
		));
		$data = Hash::merge($existRecord, $data);
		$data = Hash::remove($data, 'QuestionnaireSetting.created_user');
		$data = Hash::remove($data, 'QuestionnaireSetting.created');
		$data = Hash::remove($data, 'QuestionnaireSetting.modified_user');
		$data = Hash::remove($data, 'QuestionnaireSetting.modified');

		//バリデーション
		$this->set($data);
		if (! $this->validates()) {
			$this->rollback();
			return false;
		}

		try {
			if (! $this->save(null, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}
		return true;
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
	public function saveBlock($frame) {
		// すでに結びついている場合はBlockは作らないでよい
		if (! empty($frame['Frame']['block_id'])) {
			return;
		}
		$this->Block = ClassRegistry::init('Blocks.Block', true);
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
	public function saveSetting() {
		// block settingはあるか
		$setting = $this->getSetting();
		if (empty($setting)) {
			// ないときは作る
			$blockSetting = $this->create();
			$blockSetting['QuestionnaireSetting']['block_key'] = Current::read('Block.key');	//$block['Block']['key'];
			$this->saveQuestionnaireSetting($blockSetting);
		}
		return true;
	}
}
