<?php
/**
 * Questionnaires App Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * Summary for QuestionnaireQuestion Model
 */
class QuestionnairesAppModel extends AppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
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
		// この継承クラスたちがValidateロジックを走らせる前に必ずDBを切り替える
		$this->setDataSource('master');
		return parent::beforeValidate($options);
	}

}
