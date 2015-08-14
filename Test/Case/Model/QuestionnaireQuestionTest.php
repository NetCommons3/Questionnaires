<?php
/**
 * QuestionnaireQuestions Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireQuestions Test Case
 */
class QuestionnaireQuestionTest extends QuestionnaireTestBase {

/**
 * Fixtures
 *
 * @var array
 */
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
 * getDefaultQuestion method
 *
 * @return void
 */
	public function testgetDefaultQuestion() {
		//初期処理
		$this->setUp();

		// 期待値の生成
		$expected = array(
			'question_sequence' => 0,
			'question_value' => __d('questionnaires', 'New Question') . '1',
			'question_type' => QuestionnairesComponent::TYPE_SELECTION,
			'is_result_display' => QuestionnairesComponent::EXPRESSION_SHOW,
			'result_display_type' => QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART,
			'is_require' => QuestionnairesComponent::USES_NOT_USE,
			'is_skip' => QuestionnairesComponent::SKIP_FLAGS_NO_SKIP,
			'is_choice_random' => QuestionnairesComponent::USES_NOT_USE,
			'is_range' => QuestionnairesComponent::USES_NOT_USE,
			);
		$expected['QuestionnaireChoice'][0] = array(
			'choice_sequence' => 0,
			'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
			'choice_label' => __d('questionnaires', 'new choice') . '1',
			'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
			'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END,
			'graph_color' => QuestionnairesComponent::$defaultGraphColors[0]
			);

		// 処理実行
		$result = $this->QuestionnaireQuestion->getDefaultQuestion();

		// テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}
/**
 * setQuestionToPage method
 *
 * @return void
 */
	public function testsetQuestionToPage() {
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaire = array(
			'Questionnaire' => array(
				'id' => 2,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'status' => 1,
				'question_sequence' => 1,
				'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'question_type' => 1,
				'description' => 'Lorem ipsum dolor sit amet',
				'is_require' => 1,
				'question_type_option' => 1,
				'is_choice_random' => 1,
				'is_skip' => 1,
				'min' => '2',
				'max' => '3',
				'is_result_display' => 1,
				'result_display_type' => 1,
				'is_auto_translated' => 1,
				'questionnaire_page_id' => 1,
				'created_user' => 1,
				'created' => '2015-04-13 06:39:20',
				'modified_user' => 1,
				'modified' => '2015-04-13 06:39:20',
				'question_count' => 0,
			),
		);
		$page = array(
			'id' => 1,
			'key' => 'page3',
			'language_id' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'status' => 1,
			'questionnaire_id' => 2,
			'page_title' => 'test3',
			'page_sequence' => 1,
			'is_auto_translated' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:38:28',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:38:28'
		);

		$expected = 1; // 処理内でカウントアップ

		// 処理実行
		$this->QuestionnaireQuestion->setQuestionToPage($questionnaire, $page);

		// テスト実施
		$this->assertEquals($questionnaire['Questionnaire']['question_count'], $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * saveQuestionnaireQuestion method
 *
 * @return void
 */
	public function testsaveQuestionnaireQuestion() {
		//初期処理
		$this->setUp();

		//データの生成
		$pageId = 2;
		$status = NetCommonsBlockComponent::STATUS_IN_DRAFT;

		//データの生成
		$questions = array(
		array(
			'id' => 2,
			'key' => 'testkey',
			'language_id' => 0,
			'origin_id' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'question_sequence' => 1,
			'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'question_type' => '1',
			'description' => 'Lorem ipsum dolor sit amet',
			'is_require' => 1,
			'question_type_option' => QuestionnairesComponent::TYPE_OPTION_NUMERIC,
			'is_choice_random' => 1,
			'is_skip' => 1,
			'min' => 1,
			'max' => 3,
			'is_result_display' => 1,
			'result_display_type' => 1,
			'is_auto_translated' => 1,
			'questionnaire_page_id' => 2,
			'created_user' => 1,
			'created' => '2015-04-13 06:39:20',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:39:20',
		'QuestionnairePage' => array(
			'id' => 2,
			'key' => 'page2',
			'language_id' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'status' => 3,
			'questionnaire_id' => 2,
			'page_title' => 'test2',
			'page_sequence' => 1,
			'is_auto_translated' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:38:28',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:38:28'
		),
		'QuestionnaireChoice' =>
			array(
				'QuestionnaireChoice' => array(
				'choice_sequence' => 5,
				'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
				'choice_label' => __d('questionnaires', 'new choice') . '1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED
		)), ));

		// 処理実行
		$this->QuestionnaireQuestion->saveQuestionnaireQuestion($pageId, $status, $questions);

		// テスト実施
		$result = $this->QuestionnaireQuestion->findByKey('testkey');
		$this->assertEquals($result['QuestionnaireQuestion']['key'], 'testkey');

		$conditions = array(
					'conditions' => array('choice_sequence' => 5));
		$result = $this->QuestionnaireQuestion->QuestionnaireChoice->find('first', $conditions);
		$this->assertEquals($result['QuestionnaireChoice']['choice_label'], __d('questionnaires', 'new choice') . '1');

		//終了処理
		$this->tearDown();
	}
}
