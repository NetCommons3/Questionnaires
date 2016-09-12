<?php
/**
 * QuestionEditHelper::getJsPostData()のテスト
 *
 * @property QuestionEditHelper $QuestionEditHelper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('View', 'View');
App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');
App::uses('NetCommonsHtmlHelper', 'NetCommons.View/Helper');
App::uses('QuestionEditHelper', 'Questionnaires.View/Helper');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * Summary for QuestionEditHelper Test Case
 */
class QuestionEditHelperGetJsPostData extends NetCommonsCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$this->NetCommonsHtml = new NetCommonsHtmlHelper($View);
		$this->QuestionEdit = new QuestionEditHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->QuestionEdit);
		parent::tearDown();
	}

/**
 * Test QuestionEdit->getJsPostData()
 *
 * @return void
 */
	public function testGetJsPostData() {
		$questionnaireKey = 'questionnaire_44';
		$ajaxPostUrl = '/questionnaires/questionnaire_edit/edit_question/s_id:33333';
		$expected = [
			'Frame' => ['id' => null],
			'Block' => ['id' => null],
			'Questionnaire' => ['key' => 'questionnaire_44'],
		];
		$actual = $this->QuestionEdit->getJsPostData($questionnaireKey, $ajaxPostUrl);
		$this->assertEqual($expected, $actual);
	}
}