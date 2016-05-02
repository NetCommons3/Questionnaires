<?php
/**
 * QuestionnaireAnswer::saveAnswer()のテスト
 *
 * @property QuestionnaireAnswer $QuestionnaireAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');
App::uses('QuestionnaireFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnairePageFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireQuestionFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireChoiceFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireAnswerSummaryFixture', 'Questionnaires.Test/Fixture');

/**
 * QuestionnaireAnswer::saveAnswer()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireFrameDisplayQuestionnaire
 */
class SaveAnswerTest extends NetCommonsModelTestCase {

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
	protected $_modelName = 'QuestionnaireAnswer';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveAnswer';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
		$this->QuestionnaireAnswerSummary = ClassRegistry::init(Inflector::camelize($this->plugin) . '.QuestionnaireAnswerSummary');
	}

/**
 * テストDataの取得
 *
 * @param int $pageSeq page sequence
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @param array $getAnswerData answer data get function
 * @return array
 */
	private function __getData($pageSeq, $qKey, $summaryId, $getAnswerData) {
		$answerData = $this->$getAnswerData($qKey, $summaryId);
		$data = array(
			'Frame' => array('id' => 6),
			'Block' => array('id' => 2),
			'QuestionnairePage' => array('page_sequence' => $pageSeq),
			'QuestionnaireAnswer' => array(
				$qKey => $answerData
			),
		);
		return $data;
	}
/**
 * テストQuestionnaireDataの取得
 *
 * @param int $id questionnaire id
 * @return array
 */
	private function __getQuestionnaire($id) {
		$data = array();
		$fixtureQuestionnaire = new QuestionnaireFixture();
		$rec = Hash::extract($fixtureQuestionnaire->records, '{n}[id=' . $id . ']');
		$data['Questionnaire'] = $rec[0];

		$fixturePage = new QuestionnairePageFixture();
		$rec = Hash::extract($fixturePage->records, '{n}[questionnaire_id=' . $data['Questionnaire']['id'] . ']');
		$rec = Hash::extract($rec, '{n}[language_id=2]');
		$data['QuestionnairePage'] = $rec;

		$fixtureQuestion = new QuestionnaireQuestionFixture();
		$fixtureChoice = new QuestionnaireChoiceFixture();

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
 * テストSummaryDataの取得
 *
 * @param int $id summary id
 * @return array
 */
	private function __getSummary($id) {
		$data = array();
		$fixture = new QuestionnaireAnswerSummaryFixture();
		$rec = Hash::extract($fixture->records, '{n}[id=' . $id . ']');
		$data['QuestionnaireAnswerSummary'] = $rec[0];
		return $data;
	}
/**
 * テスト択一選択回答の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getSingleSelect($qKey, $summaryId) {
		return array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'answer_value' => '|choice_2:choice label1',
				'questionnaire_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
	}
/**
 * テスト複数選択回答の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getMultipleSelect($qKey, $summaryId) {
		return array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'answer_value' => array('|choice_7:choice label4', '|choice_8:choice label5'),
				'questionnaire_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => 'so no ta value!'
			)
		);
	}
/**
 * テストテキスト回答の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getText($qKey, $summaryId) {
		return array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'answer_value' => 'Test Answer!!',
				'questionnaire_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
	}
/**
 * テスト日付回答の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getDate($qKey, $summaryId) {
		return array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'answer_value' => '2016-02-25',
				'questionnaire_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
	}
/**
 * テスト日時回答の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getDateTime($qKey, $summaryId) {
		return array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'answer_value' => '2016-02-25 02:02:02',
				'questionnaire_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
	}
/**
 * テストマトリクス択一選択回答の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getMatrix($qKey, $summaryId) {
		return array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'questionnaire_question_key' => $qKey,
				'matrix_choice_key' => 'choice_9',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => '|choice_11:choice label11',
			),
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'questionnaire_question_key' => $qKey,
				'matrix_choice_key' => 'choice_10',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => '|choice_12:choice label12',
			)
		);
	}
/**
 * テストマトリクス複数選択回答の取得
 *
 * @param string $qKey question key
 * @param int $summaryId summary id
 * @return array
 */
	protected function _getMultipleMatrix($qKey, $summaryId) {
		return array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'questionnaire_question_key' => $qKey,
				'matrix_choice_key' => 'choice_13',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => array('|choice_15:choice label15', '|choice_16:choice label16'),
			),
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'questionnaire_question_key' => $qKey,
				'matrix_choice_key' => 'choice_14',
				'id' => '',
				'other_answer_value' => '',
				'answer_value' => array('|choice_15:choice label15', '|choice_16:choice label16'),
			)
		);
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @param int $questionnaireId questionnaire id
 * @param int $summaryId summary id
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data, $questionnaireId, $summaryId) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$questionnaire = $this->__getQuestionnaire($questionnaireId);
		$summary = $this->__getSummary($summaryId);

		//チェック用データ取得
		if (isset($data[$this->$model->alias]['id'])) {
			$before = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
		}

		//テスト実行
		$result = $this->$model->$method($data, $questionnaire, $summary);
		$this->assertNotEmpty($result);

		//idのチェック
		if (isset($data[$this->$model->alias]['id'])) {
			$id = $data[$this->$model->alias]['id'];
		} else {
			$id = $this->$model->getLastInsertID();
		}

		//登録データ取得
		$actual = $this->$model->find('all', array(
			'recursive' => 0,
			'conditions' => array(
				'QuestionnaireAnswer.questionnaire_answer_summary_id' => $summaryId,
				'QuestionnaireQuestion.language_id' => 2,
			),
		));
		$actual = Hash::remove($actual, '{n}.QuestionnaireQuestion');
		$actual = Hash::remove($actual, '{n}.QuestionnaireChoice');
		$actual = Hash::remove($actual, '{n}.QuestionnaireAnswerSummary');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.answer_values');//これは予備情報なので
		if (Hash::check($data, $this->$model->alias . '.{n}.id')) {
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		} else {
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created_user');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');

			$before[$this->$model->alias] = array();
		}

		$qKeys = array_keys($data[$this->$model->alias]);
		$qKey = $qKeys[0];
		$expected = $data['QuestionnaireAnswer'][$qKey];
		$check = array();
		foreach ($actual as $index => $actualElement) {
			// 新規作成の要望の場合はIDチェックはしない
			if (empty($expected[$index]['id'])) {
				$expected[$index]['id'] = $actualElement['QuestionnaireAnswer']['id'];
			}
			$check[] = $actualElement['QuestionnaireAnswer'];
		}

		$this->assertEquals($expected, $check);
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
		// pageSeq, question key, summaryId, getAnswerFunctionName, questionnaireId, summaryId
		return array(
			array($this->__getData(0, 'qKey_1', 3, '_getSingleSelect'), 2, 3),
			array($this->__getData(1, 'qKey_5', 4, '_getMultipleSelect'), 4, 4),
			array($this->__getData(2, 'qKey_7', 4, '_getText'), 4, 4),
			array($this->__getData(3, 'qKey_9', 4, '_getMatrix'), 4, 4),
			array($this->__getData(4, 'qKey_11', 4, '_getMultipleMatrix'), 4, 4),
			array($this->__getData(5, 'qKey_13', 4, '_getDate'), 4, 4),
			array($this->__getData(6, 'qKey_15', 4, '_getDateTime'), 4, 4),
			array($this->__getData(0, 'qKey_17', 5, '_getText'), 6, 5),
		);
	}

/**
 * SaveのValidationErrorテスト
 *
 * @param array $data 登録データ
 * @param int $questionnaireId questionnaire id
 * @param int $summaryId summary id
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnValidationError
 * @return void
 */
	public function testSaveOnValidationError($data, $questionnaireId, $summaryId, $mockModel, $mockMethod = 'validates') {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$questionnaire = $this->__getQuestionnaire($questionnaireId);
		$summary = $this->__getSummary($summaryId);

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);
		$result = $this->$model->$method($data, $questionnaire, $summary);
		$this->assertFalse($result);
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
		$data = $this->__getData(0, 'qKey_1', 3, '_getSingleSelect');
		return array(
			array($data, 2, 3, 'Questionnaires.QuestionnaireAnswer', 'saveMany'),
		);
	}
/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ
 *
 * @return void
 */
	public function dataProviderValidationError() {
		$options = array(
			'pageIndex' => 0,
			'maxPageIndex' => 0,
		);
		return array(
			array($this->__getDataWithQuestion(), $options, 'page_sequence', '2',
				__d('questionnaires', 'page sequence is illegal.')),
			array($this->__getData(), $options, 'page_sequence', '0',
				__d('questionnaires', 'please set at least one question.')),
		);
	}
/**
 * ターゲット質問取り出しのExceptionErrorテスト
 *
 * @return void
 */
	public function testSaveOnExceptionError2() {
		//$data,  $questionnaireId, $summaryId
		$model = $this->_modelName;
		$method = $this->_methodName;
		$data = $this->__getData(0, 'qKey_1', 3, '_getSingleSelect');
		$questionnaire = $this->__getQuestionnaire(2);
		$summary = $this->__getSummary(3);

		$questionnaire = Hash::remove($questionnaire, 'QuestionnairePage.{n}.QuestionnaireQuestion');
		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($data, $questionnaire, $summary);
	}
}
