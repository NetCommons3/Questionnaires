<?php
/**
 * QuestionnaireI18n Test Case
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnaireI18n', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireI18n Test Case
 */
class QuestionnaireI18nTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire_i18n'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->QuestionnaireI18n = ClassRegistry::init('Questionnaires.QuestionnaireI18n');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->QuestionnaireI18n);

		parent::tearDown();
	}

}
