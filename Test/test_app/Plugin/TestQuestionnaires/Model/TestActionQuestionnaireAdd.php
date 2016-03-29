<?php
/**
 * ActionQuestionnaireAddModel
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ActionQuestionnaireAdd', 'Questionnaires.Model');

/**
 * ActionQuestionnaireAddModel
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Model
 */
class TestActionQuestionnaireAdd extends ActionQuestionnaireAdd {

/**
 * Use table config
 *
 * @var string
 */
	public $useTable = 'questionnaires';

/**
 * Use alias config
 *
 * @var string
 */
	public $alias = 'ActionQuestionnaireAdd';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * getNewQuestionnaire
 *
 * @return void
 * @throws InternalErrorException
 */
	public function getNewQuestionnaire() {
		App::uses('TemporaryUploadFile', 'TestFiles.Utility');
		$this->returnValue = parent::getNewQuestionnaire();
		return $this->returnValue;
	}
}