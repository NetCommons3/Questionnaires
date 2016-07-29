<?php
/**
 * QuestionnaireAnswerSummary::saveAnswerStatus()のテスト
 *
 * @property QuestionnaireAnswerSummary $QuestionnaireAnswerSummary
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
 * QuestionnaireAnswerSummary::saveAnswerStatus()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireAnswerSummary
 */
class SaveAnswerStatusTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'saveAnswerStatus';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';

		$model = $this->_modelName;
		$mailQueueMock = $this->getMock('MailQueueBehavior',
			['setAddEmbedTagValue', 'afterSave']);
		$mailQueueMock->expects($this->any())
			->method('setAddEmbedTagValue')
			->will($this->returnValue(true));
		$mailQueueMock->expects($this->any())
			->method('afterSave')
			->will($this->returnValue(true));

		// ClassRegistoryを使ってモックを登録。
		// まずremoveObjectしないとaddObjectできないのでremoveObjectする
		ClassRegistry::removeObject('MailQueueBehavior');
		// addObjectでUploadBehaviorでMockが使われる
		ClassRegistry::addObject('MailQueueBehavior', $mailQueueMock);

		// このloadではモックがロードされる
		$this->$model->Behaviors->load('MailQueue');
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
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @param int $status status
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data, $status) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$questionnaire = array(
			'Questionnaire' => array(
				'title' => 'test Questionnaire'
			)
		);

		//テスト実行
		$result = $this->$model->$method($questionnaire, $data, $status);
		$this->assertNotEmpty($result);

		//idのチェック
		if (isset($data[$this->$model->alias]['id'])) {
			$id = $data[$this->$model->alias]['id'];
		} else {
			$id = $this->$model->getLastInsertID();
		}

		//登録データ取得
		$actual = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $id
			),
		));
		if (Hash::check($data, $this->$model->alias . '.{n}.id')) {
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		} else {
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created_user');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
			$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		}
		$actual = $actual[0];

		$data[$this->$model->alias]['answer_status'] = $status;
		$expected = Hash::remove($data, $this->$model->alias . '.created');
		$expected = Hash::remove($expected, $this->$model->alias . '.created_user');
		$expected = Hash::remove($expected, $this->$model->alias . '.modified');
		$expected = Hash::remove($expected, $this->$model->alias . '.modified_user');

		$actual = Hash::remove($actual, $this->$model->alias . '.answer_time');
		$actual = Hash::remove($actual, $this->$model->alias . '.session_value');
		$expected = Hash::remove($expected, $this->$model->alias . '.answer_time');

		$this->assertEquals($expected, $actual);
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
		return array(
			array($this->__getSummary(2), QuestionnairesComponent::ACTION_ACT),
		);
	}
/**
 * SaveのValidationErrorテスト
 *
 * @param array $data 登録データ
 * @param int $status status
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnValidationError
 * @return void
 */
	public function testSaveOnValidationError($data, $status, $mockModel, $mockMethod = 'validates') {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$questionnaire = array(
			'Questionnaire' => array(
				'title' => 'test Questionnaire'
			)
		);
		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);
		$result = $this->$model->$method($questionnaire, $data, $status);
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
		return array(
			array($this->__getSummary(2), QuestionnairesComponent::ACTION_ACT, 'Questionnaires.QuestionnaireAnswerSummary'),
		);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param array $data 登録データ
 * @param int $status status
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($data, $status, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		$questionnaire = array(
			'Questionnaire' => array(
				'title' => 'test Questionnaire'
			)
		);

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($questionnaire, $data, $status);
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
	public function dataProviderSaveOnExceptionError() {
		return array(
			array($this->__getSummary(2), QuestionnairesComponent::ACTION_ACT, 'Questionnaires.QuestionnaireAnswerSummary', 'save'),
		);
	}

}
