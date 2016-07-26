<?php
/**
 * QuestionnaireAnswerSummary::forceGetProgressiveAnswerSummary()のテスト
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
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

/**
 * QuestionnaireAnswerSummary::forceGetProgressiveAnswerSummary()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireAnswerSummary
 */
class ForceGetProgressiveAnswerSummaryTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'forceGetProgressiveAnswerSummary';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * getのテスト
 *
 * @param int $questionnaire アンケートデータ
 * @param int $userId user id
 * @param string $sessionId session id
 * @param mix $expected
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($questionnaire, $userId, $sessionId, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($questionnaire, $userId, $sessionId);

		$this->assertEquals($expected['answer_status'], $result[$this->$model->alias]['answer_status']);
		$this->assertEquals($expected['test_status'], $result[$this->$model->alias]['test_status']);
		$this->assertEquals($expected['answer_number'], $result[$this->$model->alias]['answer_number']);
		$this->assertEquals($expected['answer_time'], $result[$this->$model->alias]['answer_time']);
		$this->assertEquals($questionnaire['Questionnaire']['key'], $result[$this->$model->alias]['questionnaire_key']);
		$this->assertEquals($userId, $result[$this->$model->alias]['user_id']);
	}

/**
 * getのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		$questionnaire = array(
			'Questionnaire' => array(
				'key' => 'questionnaire_4',
				'status' => WorkflowComponent::STATUS_PUBLISHED
			)
		);
		$questionnaireDraft = array(
			'Questionnaire' => array(
				'key' => 'questionnaire_4',
				'status' => WorkflowComponent::STATUS_IN_DRAFT
			)
		);
		$questionnaireMulti = array(
			'Questionnaire' => array(
				'key' => 'questionnaire_12',
				'status' => WorkflowComponent::STATUS_PUBLISHED
			)
		);
		return array(
			array($questionnaire, 4, '', array(
				'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => 1,
				'answer_time' => ''
			)),
			array($questionnaireDraft, 4, '', array(
				'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_TEST,
				'answer_number' => 1,
				'answer_time' => ''
			)),
			array($questionnaireMulti, 3, '', array(
				'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
				'answer_number' => 2,
				'answer_time' => ''
			)),
		);
	}
/**
 * SaveのExceptionErrorテスト
 *
 * @param int $questionnaire アンケートデータ
 * @param int $userId user id
 * @param string $sessionId session id
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($questionnaire, $userId, $sessionId, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($questionnaire, $userId, $sessionId);
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
		$questionnaire = array(
			'Questionnaire' => array(
				'key' => 'questionnaire_4',
				'status' => WorkflowComponent::STATUS_PUBLISHED
			)
		);
		return array(
			array($questionnaire, 4, '', 'Questionnaires.QuestionnaireAnswerSummary', 'save'),
		);
	}

}
