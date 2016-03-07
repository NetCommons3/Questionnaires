<?php
/**
 * QuestionnaireAnswerSummaryCsv::getAnswerSummaryCsv()のテスト
 *
 * @property QuestionnaireAnswerSummaryCsv $QuestionnaireAnswerSummaryCsv
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
 * QuestionnaireAnswerSummaryCsv::getAnswerSummaryCsv()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireAnswerSummaryCsv
 */
class GetAnswerSummaryCsvTest extends NetCommonsGetTest {

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
	protected $_modelName = 'QuestionnaireAnswerSummaryCsv';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getAnswerSummaryCsv';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->QuestionnaireAnswerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary');
		$this->QuestionnaireAnswer = ClassRegistry::init('Questionnaires.QuestionnaireAnswer');
		$this->QuestionnaireAnswerSummary->deleteAll(array('answer_status' => 2));
		$this->QuestionnaireAnswer->deleteAll(array('NOT' => array('questionnaire_answer_summary_id' => null)));
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerSingleChoice');
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerMultipleChoice');
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerSingleList');
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerMatrixSingleChoice');
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerMatrixMultipleChoice');
		// ダミーデータを入れます
		$summaryData = array(
			array('questionnaire_4', ''),
			array('questionnaire_4', ''),
			array('questionnaire_4', 1),
			array('questionnaire_12', ''),
			array('questionnaire_12', 1),
			array('questionnaire_22', ''),
		);
		for ($i = 0; $i < 6; $i++) {
			$questionnaireKey = $summaryData[$i][0];
			$id = $this->_insertAnswerSummary($questionnaireKey, $summaryData[$i][1]);

			if ($questionnaireKey == 'questionnaire_4') {
				// single choice
				$this->_insertAnswer($id, 'qKey_3', '|choice_4:choice label1');
				// multi choice
				if ($i % 2 == 0) {
					$this->_insertAnswer($id, 'qKey_5', '|choice_7:choice label4|choice_8:choice label5');
				} else {
					$this->_insertAnswer($id, 'qKey_5', '|choice_7:choice label4');
				}
				// text
				$this->_insertAnswer($id, 'qKey_7', 'テキストの回答ですよ');
				// matrix single
				$this->_insertAnswer($id, 'qKey_9', '|choice_12:choice label12', 'choice_9');
				$this->_insertAnswer($id, 'qKey_9', '|choice_11:choice label11', 'choice_10');
				// matrix multi
				if ($i % 2 == 0) {
					$this->_insertAnswer($id, 'qKey_11', '|choice_16:choice label16', 'choice_13');
					$this->_insertAnswer($id, 'qKey_11', '|choice_15:choice label15', 'choice_14');
				} else {
					$this->_insertAnswer($id, 'qKey_11', '|choice_15:choice label15|choice_16:choice label16', 'choice_13');
					$this->_insertAnswer($id, 'qKey_11', '|choice_15:choice label15|choice_16:choice label16', 'choice_14');
				}
				// date
				$this->_insertAnswer($id, 'qKey_13', '2016-03-01');
			} elseif ($questionnaireKey == 'questionnaire_12') {
				$this->_insertAnswer($id, 'qKey_27', '|choice_27:choice label27');
			} elseif ($questionnaireKey == 'questionnaire_22') {
				$this->_insertAnswer($id, 'qKey_41', '|choice_35:choice label35', 'choice_33');
				$this->_insertAnswer($id, 'qKey_41', '|choice_36:choice label36', 'choice_34');
			}
		}
		$this->QuestionnaireAnswer->Behaviors->load('QuestionnaireAnswerSingleChoice');
		$this->QuestionnaireAnswer->Behaviors->load('QuestionnaireAnswerMultipleChoice');
		$this->QuestionnaireAnswer->Behaviors->load('QuestionnaireAnswerSingleList');
		$this->QuestionnaireAnswer->Behaviors->load('QuestionnaireAnswerMatrixSingleChoice');
		$this->QuestionnaireAnswer->Behaviors->load('QuestionnaireAnswerMatrixMultipleChoice');
	}

/**
 * _insertAnswerSummary
 *
 * @param int $questionnaireKey アンケートKey
 *
 * @return int summary id
 */
	protected function _insertAnswerSummary($questionnaireKey, $userId) {
		$summary = array(
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-29 00:00:00',
			'questionnaire_key' => $questionnaireKey,
			'user_id' => $userId,
			'created_user' => $userId
		);
		$this->QuestionnaireAnswerSummary->create();
		$this->QuestionnaireAnswerSummary->save($summary);
		$id = $this->QuestionnaireAnswerSummary->getLastInsertID();
		return $id;
	}
/**
 * _insertAnswer
 *
 * @param int $summaryId サマリID
 * @param sring $qKey Question key
 * @param string $value answer value
 * @param string $cKey choice key
 *
 * @return void
 */
	protected function _insertAnswer($summaryId, $qKey, $value, $cKey = '') {
		$answer = array(
			'answer_value' => $value,
			'questionnaire_answer_summary_id' => $summaryId,
			'questionnaire_question_key' => $qKey,
			'matrix_choice_key' => $cKey,
			'other_answer_value' => 'その他の回答',
		);
		$this->QuestionnaireAnswer->create();
		$this->QuestionnaireAnswer->save($answer, false);
	}
/**
 * _getQuestionnaire
 *
 * @param int $id 質問ID
 * @return array
 */
	protected function _getQuestionnaire($id) {
		$fixtureQuestionnaire = new QuestionnaireFixture();
		$fixturePage = new QuestionnairePageFixture();
		$fixtureQuestion = new QuestionnaireQuestionFixture();
		$fixtureChoice = new QuestionnaireChoiceFixture();

		$data = array();
		$rec = Hash::extract($fixtureQuestionnaire->records, '{n}[id=' . $id . ']');
		$data['Questionnaire'] = $rec[0];

		$rec = Hash::extract($fixturePage->records, '{n}[questionnaire_id=' . $data['Questionnaire']['id'] . ']');
		$rec = Hash::extract($rec, '{n}[language_id=2]');
		$data['QuestionnairePage'] = $rec;

		foreach ($data['QuestionnairePage'] as &$page) {
			$pageId = $page['id'];

			$rec = Hash::extract($fixtureQuestion->records, '{n}[questionnaire_page_id=' . $pageId . ']');
			$rec = Hash::extract($rec, '{n}[language_id=2]');
			$page['QuestionnaireQuestion'] = $rec;
			$questionId = $rec[0]['id'];

			$rec = Hash::extract($fixtureChoice->records, '{n}[questionnaire_question_id=' . $questionId . ']');
			if ($rec) {
				$page['QuestionnaireQuestion'][0]['QuestionnaireChoice'] = $rec;
			}
		}
		return $data;
	}

/**
 * getAnswerSummaryCsv
 *
 * @param int $questionnaireId questionnaire id
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetAnswerSummaryCsv($questionnaireId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$questionnaire = $this->_getQuestionnaire($questionnaireId);

		//テスト実行
		$result = $this->$model->$method($questionnaire, 1000, 0);
		$result = Hash::remove($result, '{n}.1');

		$expected = Hash::remove($expected, '{n}.1');
		//チェック
		$this->assertEquals($expected, $result);
	}
/**
 * getDefaultChoiceのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			array(
				__d('questionnaires', 'Respondent'), __d('questionnaires', 'Answer Date'), __d('questionnaires', 'Number'),
				'1-1. Question_1',
				'2-1. Question_2',
				'3-1. Question_3',
				'4-1-1. Question_4:choice label9',
				'4-1-2. Question_4:choice label10',
				'5-1-1. Question_5:choice label13',
				'5-1-2. Question_5:choice label14',
				'6-1. Question_6',
				'7-1. Question_7',
			),	// header
			array(
				'Guest', '2016-03-01 01:01:01', '1',
				'choice label1',
				'choice label4|その他の回答',
				'テキストの回答ですよ',
				'choice label12',
				'choice label11',
				'choice label16',
				'choice label15',
				'2016-03-01',
				''
			),	// data1
			array(
				'Guest', '2016-03-01 01:01:01', '1',
				'choice label1',
				'choice label4',
				'テキストの回答ですよ',
				'choice label12',
				'choice label11',
				'choice label15|choice label16',
				'choice label15|choice label16',
				'2016-03-01',
				''
			),	// data2
			array(
				'System Administrator', '2016-03-01 01:01:01', '1',
				'choice label1',
				'choice label4|その他の回答',
				'テキストの回答ですよ',
				'choice label12',
				'choice label11',
				'choice label16',
				'choice label15',
				'2016-03-01',
				''
			),	// data3
		);
		$expect2 = array(
			array(
				__d('questionnaires', 'Respondent'), __d('questionnaires', 'Answer Date'), __d('questionnaires', 'Number'),
				'1-1. Question_1',
			),	// header
		);
		$expect3 = array(
			array(
				__d('questionnaires', 'Respondent'), __d('questionnaires', 'Answer Date'), __d('questionnaires', 'Number'),
				'1-1. Question_1',
				'2-1. Question_1',
				'3-1. Question_1',
			),	// header
			array(
				__d('questionnaires', 'Anonymity'), '2016-03-01 01:01:01', '1',
				'choice label27',
				'',
				'',
			),	// data2
			array(
				__d('questionnaires', 'Anonymity'), '2016-03-01 01:01:01', '1',
				'choice label27',
				'',
				'',
			),	// data2
		);
		$expect4 = array(
			array(
				__d('questionnaires', 'Respondent'), __d('questionnaires', 'Answer Date'), __d('questionnaires', 'Number'),
				'1-1-1. Question_1:choice label33',
				'1-1-2. Question_1:choice label34',
			),	// header
			array(
				'Guest', '2016-03-01 01:01:01', '1',
				'choice label35',
				'その他の回答:choice label36',
			),	// data2
		);
		return array(
			array('4', $expect),
			array('2', $expect2),
			array('12', $expect3),
			array('22', $expect4),
		);
	}

}