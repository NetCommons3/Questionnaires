<?php
/**
 * QuestionnaireFrameSetting::getQuestionnaireFrameSetting()のテスト
 *
 * @property QuestionnaireFrameSetting $QuestionnaireFrameSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireFrameSetting::getQuestionnaireFrameSetting()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnairePage
 */
class QuestionnaireGetQuestionnaireFrameSettingTest extends NetCommonsGetTest {

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
	protected $_methodName = 'getQuestionnaireFrameSetting';

/**
 * getQuestionnaireFrameSetting
 *
 * @param string $frameKey frameKey フレームキー
 * @param int $sort sort type
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetQuestionnaireFrameSetting($frameKey, $sort, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$condition = array('frame_key' => $frameKey);
		$field = array('sort_type' => $sort);
		$this->$model->updateAll($field, $condition);
		//テスト実行
		$result = $this->$model->$method($frameKey);
		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getQuestionnaireFrameSettingのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect0 = array(
			QuestionnairesComponent::DISPLAY_TYPE_LIST,
			'10',
			'modified',
			'ASC',
		);
		$expect1 = $expect0;
		$expect1[3] = 'DESC';
		$expect2 = $expect0;
		$expect2[2] = 'created';
		$expect3 = $expect0;
		$expect3[2] = 'title';
		$expect4 = $expect0;
		$expect4[2] = 'answer_end_period';
		return array(
			array('frame_3', QuestionnairesComponent::QUESTIONNAIRE_SORT_MODIFIED, $expect1),
			array('frame_3', QuestionnairesComponent::QUESTIONNAIRE_SORT_CREATED, $expect2),
			array('frame_3', QuestionnairesComponent::QUESTIONNAIRE_SORT_TITLE, $expect3),
			array('frame_3', QuestionnairesComponent::QUESTIONNAIRE_SORT_END, $expect4),
			array('frame_99999', null, $expect1),
		);
	}

}
