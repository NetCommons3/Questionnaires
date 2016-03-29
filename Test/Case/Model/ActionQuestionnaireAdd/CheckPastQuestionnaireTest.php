<?php
/**
 * ActionQuestionnaireAdd::checkPastQuestionnaire()のテスト
 *
 * @property ActionQuestionnaireAdd $ActionQuestionnaireAdd
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
 * ActionQuestionnaireAdd::checkPastQuestionnaire()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\ActionQuestionnaireAdd
 */
class CheckPastQuestionnaireTest extends NetCommonsGetTest {

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
	protected $_methodName = 'checkPastQuestionnaire';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->Questionnaire->Behaviors->unload('AuthorizationKey');
		Current::$current['Block']['id'] = 2;
	}

/**
 * testCheckPastQuestionnaire
 *
 * @param array $data POSTデータ
 * @param array $check チェックデータ
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testCheckPastQuestionnaire($data, $check, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->$model->create();
		$this->$model->set($data);
		//テスト実行
		$result = $this->$model->$method($check);
		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * testCheckPastQuestionnaireのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		//$expect = $this->_getQuestionnaire(4);
		return array(
			array(
				array('ActionQuestionnaireAdd' => array(
					'create_option' => 'aaaa'
				)),
				array('past_questionnaire_id' => 'aaa'),
				true
			),
			array(
				array('ActionQuestionnaireAdd' => array(
					'create_option' => 'reuse',
					'past_questionnaire_id' => 'aaa'
				)),
				array('past_questionnaire_id' => 'aaa'),
				false
			),
			array(
				array('ActionQuestionnaireAdd' => array(
					'create_option' => 'reuse',
					'past_questionnaire_id' => '1'
				)),
				array('past_questionnaire_id' => '1'),
				false
			),
			array(
				array('ActionQuestionnaireAdd' => array(
					'create_option' => 'reuse',
					'past_questionnaire_id' => '4'
				)),
				array('past_questionnaire_id' => '4'),
				false
			),
			array(
				array('ActionQuestionnaireAdd' => array(
					'create_option' => 'reuse',
					'past_questionnaire_id' => '6'
				)),
				array('past_questionnaire_id' => '6'),
				true
			),
		);
	}
}
