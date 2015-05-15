<?php
/**
 * QuestionnaireFrameDisplayQuestionnaire Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireFrameDisplayQuestionnaire', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireFrameDisplayQuestionnaire Test Case
 */
class QuestionnaireFrameDisplayQuestionnaireTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.user',
		'plugin.questionnaires.role',
		'plugin.questionnaires.group',
		'plugin.questionnaires.room',
		'plugin.questionnaires.space',
		'plugin.questionnaires.box',
		'plugin.questionnaires.top_page',
		'plugin.questionnaires.block',
		'plugin.questionnaires.page',
		'plugin.questionnaires.language',
		'plugin.questionnaires.groups_language',
		'plugin.questionnaires.groups_user',
		'plugin.questionnaires.user_attribute',
		'plugin.questionnaires.user_attributes_user',
		'plugin.questionnaires.user_select_attribute',
		'plugin.questionnaires.user_select_attributes_user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->QuestionnaireFrameDisplayQuestionnaire = ClassRegistry::init('Questionnaires.QuestionnaireFrameDisplayQuestionnaire');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->QuestionnaireFrameDisplayQuestionnaire);

		parent::tearDown();
	}

}
