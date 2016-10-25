<?php
/**
 * Questionnaire::saveQuestionnaire()のテスト
 *
 * @property Questionnaire $Questionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('WorkflowSaveTest', 'Workflow.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');
App::uses('QuestionnaireFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnairePageFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireQuestionFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireChoiceFixture', 'Questionnaires.Test/Fixture');

/**
 * Questionnaire::saveQuestionnaire()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\Questionnaire
 */
class QuestionnaireSaveQuestionnaireTest extends WorkflowSaveTest {

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
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
		'plugin.questionnaires.block_setting_for_questionnaire',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.workflow.workflow_comment',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'Questionnaire';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveQuestionnaire';

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
		Current::$current['Plugin']['key'] = 'questionnaires';
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

		//新着のビヘイビアをモックに差し替え
		$this->$model->Behaviors->unload('Topics');
		$topicsMock = $this->getMock('TopicsBehavior', ['setTopicValue', 'afterSave']);
		$topicsMock->expects($this->any())
			->method('setTopicValue')
			->will($this->returnValue(true));
		$topicsMock->expects($this->any())
			->method('afterSave')
			->will($this->returnValue(true));

		ClassRegistry::removeObject('TopicsBehavior');
		ClassRegistry::addObject('TopicsBehavior', $topicsMock);
		$this->$model->Behaviors->load('Topics');
	}

/**
 * テストDataの取得
 *
 * @param string $id questionnaireId
 * @param string $status
 * @return array
 */
	private function __getData($id = 2, $status = '1') {
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
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return array 登録後のデータ
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//チェック用データ取得
		if (isset($data[$this->$model->alias]['id'])) {
			$before = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
			$saveData = Hash::remove($data, $this->$model->alias . '.id');
		} else {
			$saveData = $data;
		}

		//テスト実行
		$result = $this->$model->$method($saveData);
		$this->assertNotEmpty($result);
		$lastInsertId = $this->$model->getLastInsertID();

		//登録データ取得
		$latest = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $lastInsertId),
		));

		$actual = $latest;

		//前のレコードのis_latestのチェック
		if (isset($before)) {
			$after = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
			$this->assertFalse($after[$this->$model->alias]['is_latest']);
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');
		} else {
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created_user');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');

			$data[$this->$model->alias]['key'] = OriginalKeyBehavior::generateKey($this->$model->name, $this->$model->useDbConfig);
			$before[$this->$model->alias] = array();
		}
		// afterFindでDBテーブル構造以外のものがくっついてくるので
		$actual = Hash::remove($actual, 'QuestionnairePage');

		$expected[$this->$model->alias] = Hash::merge(
			$before[$this->$model->alias],
			$data[$this->$model->alias],
			array(
				'id' => $lastInsertId,
				'is_active' => true,
				'is_latest' => true
			)
		);
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified');
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified_user');

		$this->assertEquals($expected, $actual);

		return $latest;
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
		$data = $this->__getData();
		return array(
			array($data), //編集
		);
	}

/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderSaveOnExceptionError() {
		$data = $this->__getData();
		return array(
			array($data, 'Questionnaires.Questionnaire', 'save'),
			array($data, 'Questionnaires.QuestionnairePage', 'saveQuestionnairePage'),
			array($data, 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire', 'saveDisplayQuestionnaire'),
		);
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
		$data = $this->__getData();
		return array(
			array($data, 'Questionnaires.Questionnaire'),
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
			'title' => '',
		);
		return array(
			array($this->__getData(), $options, 'title', '',
				__d('net_commons', 'Invalid request.')),
		);
	}
}
