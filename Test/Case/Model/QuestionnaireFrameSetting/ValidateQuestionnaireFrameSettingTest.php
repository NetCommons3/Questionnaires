<?php
/**
 * QuestionnaireFrameSetting::validate()のテスト
 *
 * @property QuestionnaireFrameSetting $QuestionnaireFrameSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireFrameSetting::validate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireFrameSetting
 */
class QuestionnaireValidateFrameSettingTest extends NetCommonsValidateTest {

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
	protected $_methodName = 'validateFrameDisplayQuestionnaire';

/**
 * _getData
 *
 * @param int $displayType 表示形式
 * @return array
 */
	protected function _getData($displayType) {
		$data = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => $displayType,
				'display_num_per_page' => 10,
				'sort_type' => 0,
			),
			'List' => array(
				'QuestionnaireFrameDisplayQuestionnaire' => array(
					array('is_display' => 1, 'questionnaire_key' => 'questionnaire_2'),
					array('is_display' => 1, 'questionnaire_key' => 'questionnaire_4'),
					array('is_display' => 1, 'questionnaire_key' => '')
				)
			),
			'Single' => array(
				'QuestionnaireFrameDisplayQuestionnaire' => array(
					'questionnaire_key' => '',
				)
			)
		);
		return $data;
	}
/**
 * validateFrameDisplayQuestionnaireのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderValidationError() {
		return array(
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_SINGLE), 'display_type', '',
				__d('net_commons', 'Invalid request.')),
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_LIST), 'display_type', '',
				__d('net_commons', 'Invalid request.')),
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_LIST), 'display_num_per_page', 9999999,
				__d('net_commons', 'Invalid request.')),
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_LIST), 'sort_type', 12,
				__d('net_commons', 'Invalid request.')),
		);
	}
}
