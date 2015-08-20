<?php
/**
 * QuestionnaireAnswerSummaryGet Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAnswerTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireAnswerSummaryGet Test Case
 */
class QuestionnaireAnswerSummaryGetTest extends QuestionnaireAnswerTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * getNowSummaryOfThisUser method
 * (userId指定)
 * @return void
 */
	public function testgetNowSummaryOfThisUser1() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaireId = 1;
		$userId = 1;
		$sessionId = 1;

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->getNowSummaryOfThisUser($questionnaireId, $userId, $sessionId);

		// テスト実施
		$this->assertEquals($result[0]['QuestionnaireAnswerSummary']['id'], 1);

		//終了処理
		$this->tearDown();
	}

/**
 * getNowSummaryOfThisUser method
 *  (userId未指定)
 * @return void
 */
	public function testgetNowSummaryOfThisUser2() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaireId = 1;
		$userId = 0;
		$sessionId = 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.';

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->getNowSummaryOfThisUser($questionnaireId, $userId, $sessionId);

		// テスト実施
		$this->assertEquals($result[0]['QuestionnaireAnswerSummary']['id'], 1);

		//終了処理
		$this->tearDown();
	}

/**
 * getProgressiveSummaryOfThisUser method
 *
 * @return void
 */
	public function testgetProgressiveSummaryOfThisUser() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaireId = 2;
		$userId = 2;
		$sessionId = '2';

		$this->QuestionnaireAnswerSummary->create();
		$this->QuestionnaireAnswerSummary->save(array(
				'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_TEST,
				'answer_number' => 2,
				'questionnaire_origin_id' => 2,
				'session_value' => $sessionId,
				'user_id' => $userId,
			));
		$this->QuestionnaireAnswerSummary->save();

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->getProgressiveSummaryOfThisUser( $questionnaireId, $userId, $sessionId );

		// テスト実施
		$this->assertEquals($result['QuestionnaireAnswerSummary']['id'], 3);

		//終了処理
		$this->tearDown();
	}

/**
 * getProgressiveAnswerOfThisSummary method
 * summaryがempty
 * @return void
 */
	public function testgetProgressiveAnswerOfThisSummary1() {
		//初期処理
		$this->setUp();

		//データの生成
		$summary = '';


		$result = $this->QuestionnaireAnswerSummary->getProgressiveAnswerOfThisSummary( $summary );

		$answers = array();
		// テスト実施
		$this->assertEquals($result, $answers);

		//終了処理
		$this->tearDown();
	}

/**
 * getProgressiveAnswerOfThisSummary method
 * summaryがemptyではない
 * @return void
 */
	public function testgetProgressiveAnswerOfThisSummary2() {
		//初期処理
		$this->setUp();

		//データの生成
		$summary = array();
		$summary['QuestionnaireAnswerSummary']['id'] = 1;

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->getProgressiveAnswerOfThisSummary( $summary );

		// テスト実施
		$this->assertEquals($result[1][0]['id'], 1);

		//終了処理
		$this->tearDown();
	}

/**
 *  forceGetAnswerSummary method
 *
 * @return void
 */
	public function testforceGetAnswerSummary() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaire = array();
		$questionnaire['Questionnaire']['total_show_timing'] = QuestionnairesComponent::USES_USE;
		$questionnaire['Questionnaire']['origin_id'] = 1;
		$questionnaire['Questionnaire']['status'] = QuestionnairesComponent::TEST_ANSWER_STATUS_TEST;

		$userId = 1;
		$sessionId = 'aaa';

		$conditions = array(
			'questionnaire_origin_id' => 1,
			'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
			'session_value' => $sessionId,
			'user_id' => $userId
		);

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->forceGetAnswerSummary( $questionnaire, $userId, $sessionId, $conditions );

		// テスト実施
		$this->assertEquals($result['QuestionnaireAnswerSummary']['id'], 3 );

		//終了処理
		$this->tearDown();
	}

}
