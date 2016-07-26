<?php
/**
 * QuestionnaireSetting::saveSetting()のテスト
 *
 * @property QuestionnaireSetting $QuestionnaireSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireSetting::saveSetting()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireSetting
 */
class QuestionnaireSettingSaveSettingTest extends NetCommonsModelTestCase {

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
		'plugin.questionnaires.block_setting_for_questionnaire',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.authorization_keys.authorization_keys'
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireSetting';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveSetting';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}
/**
 * Saveのテスト 通常の登録
 *
 * @return void
 */
	public function testSave() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Block']['key'] = 'block_1';

		$result = $this->$model->$method();
		$this->assertTrue($result);
	}
/**
 * Saveのテスト Setting登録で何等かのエラー
 *
 * @return void
 */
	public function testSaveError() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		// カレントのブロック情報をなくすとエラーになります
		//Current::$current['Block']['key'] = 'block_1';

		$result = $this->$model->$method();
		$this->assertFalse($result);
	}
/**
 * Saveのテスト 既に登録済み
 *
 * @return void
 */
	public function testSaveTrue() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Block']['id'] = '2';
		Current::$current['Block']['key'] = 'block_1';

		$result = $this->$model->$method();
		$this->assertTrue($result);
	}

}
