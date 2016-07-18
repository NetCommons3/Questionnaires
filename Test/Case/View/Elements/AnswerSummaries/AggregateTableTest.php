<?php
/**
 * View/Elements/AnswerSummaries/aggregate_tableのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * View/Elements/AnswerSummaries/aggregate_tableのテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\View\Elements\AnswerSummaries\AgregateTable
 */
class QuestionnairesViewElementsAnswerSummariesAggregateTableTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'questionnaires';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestQuestionnaires');
		//テストコントローラ生成
		$this->generateNc('TestQuestionnaires.TestViewElementsAnswerSummariesAggregatTable');
	}

/**
 * View/Elements/QuestionnaireEdit/Edit/questionnaire_method/group_methodのテスト
 *
 * @return void
 */
	public function testAggregateTable() {
		//テスト実行
		$this->_testGetAction('/test_questionnaires/test_view_elements_answer_summaries_aggreagate_table/aggregate_table',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/AnswerSummaries/aggregate_table', '/') . '/';
		$this->assertRegExp($pattern, $this->view);
	}

}
