<?php
/**
 * QuestionnaireAnswerSummary::getProgressiveSummary()のテスト
 *
 * @property QuestionnaireAnswerSummary $QuestionnaireAnswerSummary
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
 * QuestionnaireAnswerSummary::getProgressiveSummary()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireAnswerSummary
 */
class GetProgressiveSummaryTest extends NetCommonsGetTest {

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
		'plugin.questionnaires.questionnaire_answer',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireAnswerSummary';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getProgressiveSummary';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['User']['id'] = 3;
	}
/**
 * getProgressiveSummary
 *
 * @param string $questionnaireKey questionnaire key
 * @param int $summaryId summary id
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetProgressiveSummary($questionnaireKey, $summaryId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($questionnaireKey, $summaryId);

		//チェック
		if ($result) {
			$this->assertEquals($result[$this->$model->alias]['id'], $expected);
		} else {
			$this->assertEquals($result, $expected);
		}
	}
/**
 * getProgressiveSummaryのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		return array(
			array('questionnaire_2', 3, 3),
			array('questionnaire_12', 1, array()),
			array('questionnaire_12', 2, array())
		);
	}
}
