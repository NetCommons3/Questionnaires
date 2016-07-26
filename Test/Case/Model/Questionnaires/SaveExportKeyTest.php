<?php
/**
 * Questionnaires::saveExportKey()のテスト
 *
 * @property Questionnaires $Questionnaires
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
 * Questionnaires::saveExportKey()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\Questionnaires
 */
class QuestionnaireSaveExportKeyTest extends NetCommonsModelTestCase {

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
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.block_setting_for_questionnaire',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'Questionnaire';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveExportKey';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->Frame = ClassRegistry::init('Frames' . '.' . 'Frame');
		$this->Block = ClassRegistry::init('Blocks' . '.' . 'Block');
		$this->QuestionnaireSetting = ClassRegistry::init('Questionnaires' . '.' . 'QuestionnaireSetting');
		$this->QuestionnaireFrameSetting = ClassRegistry::init('Questionnaires' . '.' . 'QuestionnaireFrameSetting');
	}

/**
 * Saveのテスト
 *
 * @return void
 */
	public function testSave() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$questionnaireId = 1;
		//登録データ取得
		$before = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $questionnaireId),
		));

		//テスト実行
		$result = $this->$model->$method($questionnaireId, 'testExportKey');
		$this->assertNotEmpty($result);

		//登録データ取得
		$actual = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $questionnaireId),
		));

		$this->assertNotEquals($before[$model]['export_key'], $actual[$model]['export_key']);
		$this->assertEqual('testExportKey', $actual[$model]['export_key']);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @return void
 */
	public function testSaveOnExceptionError() {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $model, 'saveField');
		$this->setExpectedException('InternalErrorException');
		//テスト実行
		$questionnaireId = 1;
		$this->$model->$method($questionnaireId, 'testExportKey');
	}
}
