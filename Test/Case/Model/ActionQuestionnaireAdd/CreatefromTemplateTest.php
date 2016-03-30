<?php
/**
 * ActionQuestionnaireAdd::_createFromTemplate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('TemporaryFolder', 'Files.Utility');
App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * ActionQuestionnaireAdd::_createFromTemplate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\ActionQuestionnaireAdd
 */
class ActionQuestionnaireAddCreateFromTemplateTest extends NetCommonsGetTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.questionnaire_setting',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'ActionQuestionnaireAdd';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getNewQuestionnaire';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestQuestionnaires');
		$this->TestActionQuestionnaireAdd = ClassRegistry::init('TestQuestionnaires.TestActionQuestionnaireAdd');

		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestFiles');

		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->Questionnaire->Behaviors->unload('AuthorizationKey');

		Current::$current['Block']['id'] = 2;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TestActionQuestionnaireAdd);
		parent::tearDown();
	}
/**
 * _createFromTemplate()のテスト
 * Successパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplate() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Questionnaires/Test/Fixture/TemplateTest.zip', $tmpFolder->path . DS . 'TemplateTest.zip');
		$data = array('ActionQuestionnaireAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'TemplateTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionQuestionnaireAdd->create();
		$this->TestActionQuestionnaireAdd->set($data);
		// getNewQuestionnaireを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuestionnaireAdd->getNewQuestionnaire();
		if (isset($this->TestActionQuestionnaireAdd->validationErrors['template_file'])) {
			$this->assertTextEquals($this->TestActionQuestionnaireAdd->validationErrors['template_file'], '');
		}
		$this->assertNotNull($result);
		$this->assertTrue(Hash::check($result, 'Questionnaire[import_key=9f1cd3e7ea0cb15c4d6adbe3cabcdb81a20b339a]'));
		for ($i = 0; $i < 10; $i++) {
			$this->assertTrue(Hash::check($result, 'QuestionnairePage.' . $i));
			$this->assertTrue(Hash::check($result, 'QuestionnairePage.' . $i . '.QuestionnaireQuestion.0'));
		}
	}
/**
 * _createFromTemplate()のテスト
 * ファイルアップロードなしできたNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG1() {
		$data = array('ActionQuestionnaireAdd' => array(
			'create_option' => 'template',
			'template_file' => ''
		));
		$this->TestActionQuestionnaireAdd->create();
		$this->TestActionQuestionnaireAdd->set($data);
		// getNewQuestionnaireを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuestionnaireAdd->getNewQuestionnaire();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * ファイルアップロードエラーが発生したNGパターン
 * 実際には存在しないファイルを指定している
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG2() {
		$data = array('ActionQuestionnaireAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'no_TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => 'no_TemplateTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionQuestionnaireAdd->create();
		$this->TestActionQuestionnaireAdd->set($data);
		// getNewQuestionnaireを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuestionnaireAdd->getNewQuestionnaire();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * Zip形式じゃないZIPファイルが指定されたNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG3() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Questionnaires/Test/Fixture/emptyErrorTemplateTest.zip', $tmpFolder->path . DS . 'emptyErrorTemplateTest.zip');
		$data = array('ActionQuestionnaireAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'emptyErrorTemplateTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionQuestionnaireAdd->create();
		$this->TestActionQuestionnaireAdd->set($data);
		// getNewQuestionnaireを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuestionnaireAdd->getNewQuestionnaire();
		$this->assertNull($result);
	}

/**
 * _createFromTemplate()のテスト
 * fingrPrintが違うNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG4() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Questionnaires/Test/Fixture/fingerPrintErrorTest.zip', $tmpFolder->path . DS . 'fingerPrintErrorTest.zip');
		$data = array('ActionQuestionnaireAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'fingerPrintErrorTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionQuestionnaireAdd->create();
		$this->TestActionQuestionnaireAdd->set($data);
		// getNewQuestionnaireを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuestionnaireAdd->getNewQuestionnaire();
		$this->assertNull($result);
	}
/**
 * _createFromTemplate()のテスト
 * versionが違うNGパターン
 *
 * @param array $data POSTデータ
 * @return void
 */
	public function testCreateFromTemplateNG5() {
		$tmpFolder = new TemporaryFolder();
		copy(APP . 'Plugin/Questionnaires/Test/Fixture/versionErrorTest.zip', $tmpFolder->path . DS . 'versionErrorTest.zip');
		$data = array('ActionQuestionnaireAdd' => array(
			'create_option' => 'template',
			'template_file' => array(
				'name' => 'TemplateTest.zip',
				'type' => 'application/x-zip-compressed',
				'tmp_name' => $tmpFolder->path . DS . 'versionErrorTest.zip',
				'error' => 0,
				'size' => 2218
			)
		));
		$this->TestActionQuestionnaireAdd->create();
		$this->TestActionQuestionnaireAdd->set($data);
		// getNewQuestionnaireを呼ぶことで_createFromTemplateが呼ばれる仕組み
		$result = $this->TestActionQuestionnaireAdd->getNewQuestionnaire();
		$this->assertNull($result);
	}
}
