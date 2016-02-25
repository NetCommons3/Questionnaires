<?php
/**
 * QuestionnaireFrameSetting::saveFrameSetting()のテスト
 *
 * @property QuestionnaireFrameSetting $QuestionnaireFrameSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireFrameSetting::saveFrameSetting()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireFrameSetting
 */
class SaveFrameSettingTest extends NetCommonsSaveTest {

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
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireFrameSetting';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveFrameSettings';

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
 * @param string $key pageKey
 * @return array
 */
	protected function _getData($displayType = QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
		$data = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => $displayType,
				'display_num_per_page' => '10',
				'sort_type' => '0',
				'frame_key' => 'frame_3',
			),
			'List' => array(
				'QuestionnaireFrameDisplayQuestionnaire' => array(
					array('is_display' => '0', 'questionnaire_key' => 'questionnaire_2'),
					array('is_display' => '1', 'questionnaire_key' => 'questionnaire_4'),
					array('is_display' => '1', 'questionnaire_key' => 'questionnaire_6')
				)
			),
			'Single' => array(
				'QuestionnaireFrameDisplayQuestionnaire' => array(
					'questionnaire_key' => 'questionnaire_2',
				)
			)
		);
		return $data;
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
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_SINGLE)),
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_LIST)),
		);
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
			array(
				$this->_getData(QuestionnairesComponent::DISPLAY_TYPE_SINGLE),
				'Questionnaires.QuestionnaireFrameSetting',
				'save'),
		);
	}
/**
 * SaveのValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *
 * @return void
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->_getData(QuestionnairesComponent::DISPLAY_TYPE_SINGLE);
		return array(
			array($data, 'Questionnaires.QuestionnaireFrameSetting'),
			array($data, 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire'),
		);
	}
}
