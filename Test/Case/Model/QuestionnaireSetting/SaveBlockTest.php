<?php
/**
 * QuestionnaireSetting::saveBlock()のテスト
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
 * QuestionnaireSetting::saveBlock()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireSetting
 */
class QuestionnaireSettingSaveBlockTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'saveBlock';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
	}

/**
 * テストDataの取得
 *
 * @param int $roomId room id
 * @param string $key frame key
 * @param int $blockId block id
 * @return array
 */
	protected function _getData($roomId, $key, $blockId) {
		$data = array(
			'Frame' => array(
				'block_id' => $blockId,
				'room_id' => $roomId,
				'plugin_key' => 'questionnaires',
				'language_id' => 2,
				'key' => $key,
			),
		);
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
		$this->assertTrue($result);
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
			array($this->_getData(1, 'frame_3', 6)),
			array($this->_getData(1, 'frame_7', null)),
			array($this->_getData(4, 'frame_8', null)),
		);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param array $data 登録データ
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($data, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($data);
	}
/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderSaveOnExceptionError() {
		return array(
			array($this->_getData(4, 'frame_8', null), 'Blocks.Block', 'save'),
			array($this->_getData(4, 'frame_8', null), 'Frames.Frame', 'save'),
		);
	}
}
