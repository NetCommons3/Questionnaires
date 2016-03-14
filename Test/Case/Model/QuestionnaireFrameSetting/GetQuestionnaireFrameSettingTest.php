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
		$db = $this->$model->getDataSource();
		$value = $db->value($sort, 'string');
		$field = array('sort_type' => $value);
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
			'Questionnaire.modified',
			'DESC',
		);
		$expect1 = $expect0;
		//$expect1[2] = 'Questionnaire.modified DESC';
		$expect2 = $expect0;
		$expect2[2] = 'Questionnaire.created';
		$expect2[3] = 'ASC';
		$expect3 = $expect2;
		$expect3[2] = 'Questionnaire.title';
		$expect4 = $expect2;
		$expect4[2] = 'Questionnaire.answer_end_period';
		return array(
			array('frame_3', 'Questionnaire.modified DESC', $expect1),
			array('frame_3', 'Questionnaire.created ASC', $expect2),
			array('frame_3', 'Questionnaire.title ASC', $expect3),
			array('frame_3', 'Questionnaire.answer_end_period ASC', $expect4),
			array('frame_99999', null, $expect0),
		);
	}

}
