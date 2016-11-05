<?php
/**
 * QuestionnairePage::getNextPage()のテスト
 *
 * @property QuestionnairePage $QuestionnairePage
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');
App::uses('QuestionnaireFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnairePageFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireQuestionFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireChoiceFixture', 'Questionnaires.Test/Fixture');

/**
 * QuestionnairePage::getNextPage()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnairePage
 */
class QuestionnaireGetNextPageTest extends NetCommonsGetTest {

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
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnairePage';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getNextPage';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$model = $this->_modelName;
		$this->$model->Behaviors->unload('AuthorizationKey');
		Current::$current['Frame']['id'] = '6';
		Current::$current['Frame']['key'] = 'frame_3';
		Current::$current['Frame']['room_id'] = '2';
		Current::$current['Frame']['plugin_key'] = 'questionnaires';
		Current::$current['Frame']['language_id'] = '2';
	}

/**
 * テストDataの取得
 *
 * @param string $id questionnaireId
 * @param string $status
 * @return array
 */
	private function __getData($id = 3, $status = '1') {
		$fixtureQuestionnaire = new QuestionnaireFixture();
		$rec = Hash::extract($fixtureQuestionnaire->records, '{n}[id=' . $id . ']');
		$data['Questionnaire'] = $rec[0];
		$data['Questionnaire']['status'] = $status;

		$fixturePage = new QuestionnairePageFixture();
		$rec = Hash::extract($fixturePage->records, '{n}[questionnaire_id=' . $data['Questionnaire']['id'] . ']');
		$data['QuestionnairePage'] = $rec;
		$pageId = $rec[0]['id'];

		$fixtureQuestion = new QuestionnaireQuestionFixture();
		$rec = Hash::extract($fixtureQuestion->records, '{n}[questionnaire_page_id=' . $pageId . ']');
		$data['QuestionnairePage'][0]['QuestionnaireQuestion'] = $rec;
		$questionId = $rec[0]['id'];

		$fixtureChoice = new QuestionnaireChoiceFixture();
		$rec = Hash::extract($fixtureChoice->records, '{n}[questionnaire_question_id=' . $questionId . ']');
		if ($rec) {
			$data['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['QuestionnaireChoice'] = $rec;
		}

		$data['Frame']['id'] = 6;
		return $data;
	}

/**
 * getNextPageのテスト
 *
 * @param array $questionnaire questionnaire
 * @param int $nowPageSeq current page sequence number
 * @param array $nowAnswers now answer
 * @param mix $expected 期待値
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetNextPage($questionnaire, $nowPageSeq, $nowAnswers, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($questionnaire, $nowPageSeq, $nowAnswers);

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getNextPageのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$questionnaire = $this->__getData(4);
		$noPageQuest = Hash::remove($questionnaire, 'QuestionnairePage');
		$errPageSeqQuest = $questionnaire;
		$errPageSeqQuest['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['QuestionnaireChoice'][2]['skip_page_sequence'] = 10;
		$answer1 = array(
			'qKey_3' => array(
				array(
					'answer_value' => '|choice_6:choice label3',
					'questionnaire_question_key' => 'qKey_3'
				)
			)
		);
		$answer2 = array(
			'qKey_3' => array(
				array(
					'answer_value' => '|choice_5:choice label2',
					'questionnaire_question_key' => 'qKey_3'
				)
			)
		);
		return array(
			array($questionnaire, 0, $answer1, 4),
			array($questionnaire, 0, $answer2, false),
			array($noPageQuest, 0, $answer1, false),
			array($errPageSeqQuest, 0, $answer1, false),
		);
	}

}
