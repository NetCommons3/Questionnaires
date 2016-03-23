<?php
/**
 * TestQuestionnaires Model
 *
 * @property Questionnaire $Questionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');
App::uses('Questionnaire', 'Questionnaires.Model');

/**
 * TestQuestionnaires Model
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\test_app\Plugin\TestQuestionnaires\Model
 */
class TestQuestionnaireModel extends CakeTestModel {

/**
 * table name
 *
 * @var string
 */
	public $useTable = 'questionnaires';

/**
 * name
 *
 * @var string
 */
	public $name = 'TestQuestionnaireModel';

/**
 * alias
 *
 * @var string
 */
	public $alias = 'Questionnaire';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Questionnaires.QuestionnaireValidate'
	);
}
