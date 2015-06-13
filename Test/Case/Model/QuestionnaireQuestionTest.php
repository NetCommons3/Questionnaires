<?php
/**
 * QuestionnaireQuestions Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireQuestions', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireQuestions Test Case
 */
class QuestionnaireQuestionTest extends CakeTestCase {

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
		$this->QuestionnaireQuestion = ClassRegistry::init('Questionnaires.QuestionnaireQuestions');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->QuestionnaireQuestion);

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
