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

	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
					'type' => 'LEFT',
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
	public function saveQuestionnaireBlocksSetting($data) {
		$this->loadModels([
			'BlockRolePermission' => 'Blocks.BlockRolePermission',
		]);
		// まだ実行部かいていません FUJI
		return true;
	}

/**
 * validate validateQuestionnaireSetting
 *
 * @param array $data received post data
 * @return bool True on success, false on validation errors
 */
	public function validateQuestionnaireBlocksSetting($data) {
		$this->set($data);
		$this->validates();
		if ($this->validationErrors) {
			return false;
		}
		return true;
	}
}
