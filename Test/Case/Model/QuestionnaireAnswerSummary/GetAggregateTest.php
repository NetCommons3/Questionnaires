<?php
/**
 * QuestionnaireAnswerSummary::getAggregate()のテスト
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
 * QuestionnaireAnswerSummary::getAggregate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireAnswerSummary
 */
class GetAggregateTest extends NetCommonsGetTest {

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
	protected $_methodName = 'getAggregate';

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
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerSingleChoice');
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerMultipleChoice');
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerSingleList');
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerMatrixSingleChoice');
		$this->QuestionnaireAnswer->Behaviors->unload('QuestionnaireAnswerMatrixMultipleChoice');
		// ダミーデータを入れます
		$summary = array(
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-29 00:00:00',
			'questionnaire_key' => 'questionnaire_4',
		);
		for ($i = 0; $i < 100; $i++) {
			$this->QuestionnaireAnswerSummary->create();
			$this->QuestionnaireAnswerSummary->save($summary);
			$id = $this->QuestionnaireAnswerSummary->getLastInsertID();
			// single choice
			$this->_insertAnswer($id, 'qKey_3', '|choice_4:choice label1');
			// multi choice
			if ($i % 2 == 0) {
				$this->_insertAnswer($id, 'qKey_5', '|choice_7:choice label4|choice_8:choice label5');
			} else {
				$this->_insertAnswer($id, 'qKey_5', '|choice_7:choice label4');
			}
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
		}
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
			'matrix_choice_key' => $cKey
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
 * getAggregate
 *
 * @param int $questionnaireId questionnaire id
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetAggregate($questionnaireId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$questionnaire = $this->_getQuestionnaire($questionnaireId);

		//テスト実行
		$result = $this->$model->$method($questionnaire);
		$result = Hash::flatten($result);
		$result = array_intersect_ukey($result, $result,
			function ($key, $key2) {
				// $keyと$key2は同じものが来てるので意味はないのだが
				// 判定文で両方使わないとphpmdに叱られるので致し方なく
				if (strpos($key, 'aggregate_total') !== false ||
					(strpos($key, 'QuestionnaireChoice') !== false && strpos($key, 'key') !== false)) {
					return 0;
				} else {
					// この処理にいみはない
					if (strpos($key2, 'QuestionnaireChoice')) {
						return 1;
					}
					return -1;
				}
			});
		$result = Hash::expand($result);

		//チェック
		$this->assertEquals($result, $expected);
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
		//$expect = $this->_getQuestionnaire(4);
		$expect = array(
			'qKey_3' => array(
				'QuestionnaireChoice' => array(
					array(
						'key' => 'choice_4',
						'aggregate_total' => array(
							'aggregate_not_matrix' => 100
						)
					),
					array(
						'key' => 'choice_5',
						'aggregate_total' => array(
							'aggregate_not_matrix' => 0
						)
					),
					array(
						'key' => 'choice_6',
						'aggregate_total' => array(
							'aggregate_not_matrix' => 0
						)
					),
				)
			),
			'qKey_5' => array(
				'QuestionnaireChoice' => array(
					array(
						'key' => 'choice_7',
						'aggregate_total' => array(
							'aggregate_not_matrix' => 100
						)
					),
					array(
						'key' => 'choice_8',
						'aggregate_total' => array(
							'aggregate_not_matrix' => 50
						)
					),
				)
			),
			'qKey_9' => array(
				'QuestionnaireChoice' => array(
					array(
						'key' => 'choice_9',
						'aggregate_total' => array(
							'choice_11' => 0,
							'choice_12' => 100,
						)
					),
					array(
						'key' => 'choice_10',
						'aggregate_total' => array(
							'choice_11' => 100,
							'choice_12' => 0,
						)
					),
					array(
						'key' => 'choice_11',
					),
					array(
						'key' => 'choice_12',
					),
				)
			),
			'qKey_11' => array(
				'QuestionnaireChoice' => array(
					array(
						'key' => 'choice_13',
						'aggregate_total' => array(
							'choice_15' => 50,
							'choice_16' => 100,
						)
					),
					array(
						'key' => 'choice_14',
						'aggregate_total' => array(
							'choice_15' => 100,
							'choice_16' => 50,
						)
					),
					array(
						'key' => 'choice_15',
					),
					array(
						'key' => 'choice_16',
					),
				)
			),
		);
		return array(
			array(4, $expect),
		);
	}
}
