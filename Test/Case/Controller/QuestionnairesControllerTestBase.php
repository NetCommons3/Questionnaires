<?php
/**
 * QuestionnairesController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireQuestionsController', 'Questionnaires.Controller');
App::uses('QuestionnairesAppController', 'Questionnaires.Controller');
App::uses('QuestionnairesQuestionController', 'Questionnaires.Controller');
App::uses('Questionnaire', 'Questionnaires.Model');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

App::uses('NetCommonsFrameComponent', 'NetCommons.Controller/Component');
App::uses('NetCommonsBlockComponent', 'NetCommons.Controller/Component');
App::uses('NetCommonsRoomRoleComponent', 'NetCommons.Controller/Component');
App::uses('NetCommonsWorkflowComponent', 'NetCommons.Controller/Component');

App::uses('YAControllerTestCase', 'NetCommons.TestSuite');
App::uses('RolesControllerTest', 'Roles.Test/Case/Controller');
App::uses('AuthGeneralControllerTest', 'AuthGeneral.Test/Case/Controller');

/**
 * QuestionnaireController Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Questionnaires\Test\Case\Controller
 */
class QuestionnairesControllerTestBase extends YAControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.categories.category',
		'plugin.comments.comment',
		'plugin.files.file',
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_blocks_setting',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		YACakeTestCase::loadTestPlugin($this, 'NetCommons', 'TestPlugin');

		Configure::write('Config.language', 'ja');
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		Configure::write('Config.language', null);
		CakeSession::write('Auth.User', null);
		unset($this->Questionnaire);
		parent::tearDown();
	}

/**
 * Expect index action
 *
 * @return void
 */
	public function testIndex() {
	}

}
