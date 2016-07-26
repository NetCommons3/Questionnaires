<?php
/**
 * Questionnaires::afterFrameSave()のテスト
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
 * Questionnaires::afterFrameSave()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\Questionnaires
 */
class QuestionnaireAfterFrameSaveTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'afterFrameSave';

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
 * テストDataの取得
 *
 * @param string $frameId frame id
 * @param string $blockId block id
 * @param string $roomId room id
 * @return array
 */
	private function __getData($frameId, $blockId, $roomId) {
		$data = array();
		$data['Frame']['id'] = $frameId;
		$data['Frame']['block_id'] = $blockId;
		$data['Frame']['language_id'] = 2;
		$data['Frame']['room_id'] = $roomId;
		$data['Frame']['plugin_key'] = 'questionnaires';

		return $data;
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertNotEmpty($result);

		//登録データ取得
		$actual = $this->Frame->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $data['Frame']['id']),
		));
		$actualBlockId = $actual['Frame']['block_id'];
		// block_idが設定されていて
		$this->assertNotEmpty($actualBlockId);

		$block = $this->Block->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $actualBlockId),
		));
		$this->assertNotEmpty($block);

		//そのブロックはあんけーとのもので
		$this->assertTextEquals($block['Block']['plugin_key'], 'questionnaires');

		$actualBlockKey = $block['Block']['key'];
		// アンケートのフレーム設定情報もできていること
		$setting = $this->QuestionnaireSetting->find('first', array(
			'recursive' => -1,
			'conditions' => array('block_key' => $actualBlockKey),
		));
		$this->assertNotEmpty($setting);
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		return array(
			array($this->__getData(6, 2, 1)), //
			array($this->__getData(14, null, 1)), //
			array($this->__getData(16, null, 4)), //
		);
	}

}
