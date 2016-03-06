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

}
