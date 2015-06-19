<?php
/**
 * QuestionnairePage Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnairePage Test Case
 */
class QuestionnairePageTest extends QuestionnaireTestBase {

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
 * getDefaultPage method
 *
 * @return void
 */
	public function testgetDefaultPage() {
		//初期処理
		$this->setUp();

		//データの生成
		$expected['page_title'] = __d('questionnaires', 'First Page');
		$expected['page_sequence'] = 0;
		$expected['origin_id'] = 0;
		$expected['QuestionnaireQuestion'][0] = array(
			'question_sequence' => 0,
			'question_value' => __d('questionnaires', 'New Question') . '1',
			'question_type' => QuestionnairesComponent::TYPE_SELECTION,
			'is_result_display' => QuestionnairesComponent::EXPRESSION_SHOW,
			'result_display_type' => QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART,
			'QuestionnaireChoice' => array(
				array(
					'choice_sequence' => 0,
					'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
					'choice_label' => __d('questionnaires', 'new choice') . '1',
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED
				)
			)
		);

		//処理実行
		$result = $this->QuestionnairePage->getDefaultPage();

		//テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}

/**
 * setPageToQuestionnaire method
 *
 * @return void
 */
	public function testsetPageToQuestionnaire() {
		//初期処理
		$this->setUp();

		//データの生成
		$data = array(
		'QuestionnairePage' => array(
			array(
				'id' => 2,
				'origin_id' => 1,
				'active_id' => 1,
				'latest_id' => 1,
				'questionnaire_id' => 2,
				'page_title' => 'TEST1',
				'page_sequence' => 1,
				'is_auto_translated' => 1,
				'created_user' => 1,
				'created' => '2015-04-13 06:38:28',
				'modified_user' => 1,
				'modified' => '2015-04-13 06:38:28'
			)
		),
		'Questionnaire' => array(
			'page_count' => 1
		)
		);

		// 処理実行
		$this->QuestionnairePage->setPageToQuestionnaire($data);
		$expected = 2;

		//テスト実施（'page_count'が２に更新）
		$this->assertEquals($data['Questionnaire']['page_count'], $expected);

		$this->tearDown();
	}

/**
 * saveQuestionnairePage method
 *
 * @return void
 */
	public function testsaveQuestionnairePage() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaireId = 1;
		$status = NetCommonsBlockComponent::STATUS_IN_DRAFT;

		$questionnairePage = array(
			'QuestionnairePage' => array(
				array(
					'id' => 2,
					'key' => '41ef6012e7574886c9a52fb598f8c5f8',
					'language_id' => 1,
					'origin_id' => 2,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 3,
					'questionnaire_id' => 1,
					'page_title' => 'TEST1',
					'page_sequence' => 2,
					'is_auto_translated' => 1,
					'created_user' => 1,
					'created' => '2015-04-13 06:38:28',
					'modified_user' => 1,
					'modified' => '2015-04-13 06:38:28'
				),
			)
		);

		//処理実行
		$this->QuestionnairePage->saveQuestionnairePage($questionnaireId, $status, $questionnairePage['QuestionnairePage']);

		//テスト実施
		$result = $this->QuestionnairePage->findByPageTitle('TEST1');
		$this->assertInternalType('array', $result['QuestionnairePage']);

		//終了処理
		$this->tearDown();
	}

}
