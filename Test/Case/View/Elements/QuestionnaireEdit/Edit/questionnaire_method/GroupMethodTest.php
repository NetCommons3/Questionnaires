<?php
/**
 * View/Elements/QuestionnaireEdit/Edit/questionnaire_method/group_methodのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * View/Elements/QuestionnaireEdit/Edit/questionnaire_method/group_methodのテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\View\Elements\QuestionnaireEdit\Edit\questionnaire_method\GroupMethod
 */
class QuestionnairesViewElementsQuestionnaireEditEditQuestionnaireMethodGroupMethodTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'questionnaires';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestQuestionnaires');
		//テストコントローラ生成
		$this->generateNc('TestQuestionnaires.TestViewElementsQuestionnaireEditEditQuestionnaireMethodGroupMethod');
	}

/**
 * View/Elements/QuestionnaireEdit/Edit/questionnaire_method/group_methodのテスト
 *
 * @return void
 */
	public function testGroupMethod() {
		//テスト実行
		$this->_testGetAction('/test_questionnaires/test_view_elements_questionnaire_edit_edit_questionnaire_method_group_method/group_method',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/QuestionnaireEdit/Edit/questionnaire_method/group_method', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertInput('hidden', 'data[is_no_member_allow]', '0', $this->view);
		$this->assertInput('hidden', 'data[is_key_pass_use]', '0', $this->view);
		$this->assertInput('hidden', 'data[is_image_authentication]', '0', $this->view);
	}

}
