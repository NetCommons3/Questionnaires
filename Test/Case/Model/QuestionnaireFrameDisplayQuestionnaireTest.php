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

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndex() {
	}
}
