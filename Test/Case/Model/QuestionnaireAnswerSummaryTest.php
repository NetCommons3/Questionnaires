<?php
/**
 * QuestionnaireAnswerSummary Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAnswerTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireAnswerSummary Test Case
 */
class QuestionnaireAnswerSummaryTest extends QuestionnaireAnswerTestBase {

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
 * __assertValidationError
 *
 * @param string $field Field name
 * @param array $data Save data
 * @param array $expected Expected value
 * @return void
 */
	private function __assertValidationError($field, $data, $expected) {
		//初期処理
		$this->setUp();

		//validate処理実行
		$this->QuestionnaireAnswerSummary->set($data);
		$result = $this->QuestionnaireAnswerSummary->validates();

		//戻り値チェック
		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		//validationErrorsチェック
		$this->assertEquals($this->QuestionnaireAnswerSummary->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'questionnaire_origin_id'が数字以外の場合(エラー)
 * @return void
 */
	public function testbeforeValidate1() {
		$field = 'questionnaire_origin_id';

		//データ生成
		$data = array(
			'questionnaire_origin_id' => 'a',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
		);

		//期待値
		$expected = array(
				$field => array(__('numeric')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * isAbleToDisplayAggrigatedData method
 * 表示期間外：公開不可の場合
 * @return void
 */
	public function testisAbleToDisplayAggrigatedData1() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaireId = 1;
		$userId = 1;
		$sessionId = '1';

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->isAbleToDisplayAggrigatedData( $questionnaireId, $userId, $sessionId );

		// テスト実施
		$this->assertFalse($result);

		//終了処理
		$this->tearDown();
	}

/**
 * isAbleToDisplayAggrigatedData method
 * 表示期間外：公開可の場合
 * @return void
 */
	public function testisAbleToDisplayAggrigatedData2() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaire = array();
		$questionnaire['Questionnaire']['total_show_timing'] = QuestionnairesComponent::USES_NOT_USE;
		$questionnaire['Questionnaire']['origin_id'] = 1;

		$userId = 1;
		$sessionId = 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.';

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->isAbleToDisplayAggrigatedData( $questionnaire, $userId, $sessionId );

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 * isAbleToDisplayAggrigatedData method
 * 表示期間外：公開可の場合
 * @return void
 */
	public function testisAbleToDisplayAggrigatedData3() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaire = array();
		$questionnaire['Questionnaire']['total_show_timing'] = QuestionnairesComponent::USES_USE;
		$questionnaire['Questionnaire']['origin_id'] = 1;

		$userId = 1;
		$sessionId = 'aaa';

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->isAbleToDisplayAggrigatedData( $questionnaire, $userId, $sessionId );

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 *  deleteTestAnswerSummary method
 *
 * @return void
 */
	public function testdeleteTestAnswerSummary() {
		//初期処理
		$this->setUp();

		//データの生成
		$originId = 1;
		$status = NetCommonsBlockComponent::STATUS_PUBLISHED;

		// 処理実行
		$result = $this->QuestionnaireAnswerSummary->deleteTestAnswerSummary( $originId, $status );

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

}
