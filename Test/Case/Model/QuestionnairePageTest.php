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
		$this->setUp();

		$expected['page_title'] = __d('questionnaires', 'First Page');
		$expected['page_sequence'] = 0;
		$expected['next_page_sequence'] = 1;
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
					'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
					'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END
				)
			)
		);

		$result = $this->QuestionnairePage->getDefaultPage();

		$this->_assertArray($expected, $result);

		$this->tearDown();
	}

/**
 * setPageToQuestionnaire method
 *
 * @return void
 */
	public function testsetPageToQuestionnaire() {
		$this->setUp();

		$data = array(
			'QuestionnairePage' => array(
				array(
					'id' => 2,
					'origin_id' => 1,
					'active_id' => 1,
					'latest_id' => 1,
					'questionnaire_id' => 2,
					'page_title' => 'TEST1',
					'page_sequence' => 0,
					'is_auto_translated' => 1,
					'created_user' => 1,
					'created' => '2015-04-13 06:38:28',
					'modified_user' => 1,
					'modified' => '2015-04-13 06:38:28'
				),
				array(
					'id' => 3,
					'origin_id' => 3,
					'active_id' => 1,
					'latest_id' => 1,
					'questionnaire_id' => 2,
					'page_title' => 'TEST2',
					'page_sequence' => 1,
					'is_auto_translated' => 1,
					'created_user' => 1,
					'created' => '2015-04-13 06:38:28',
					'modified_user' => 1,
					'modified' => '2015-04-13 06:38:28'
				)
			),
			'Questionnaire' => array(
				'id' => 2,
				'page_count' => 1
			)
		);

		$this->QuestionnairePage->setPageToQuestionnaire($data);
		$expected = 2;

		$this->assertEquals($data['Questionnaire']['page_count'], $expected);

		$this->tearDown();
	}

/**
 * saveQuestionnairePage method
 *
 * @return void
 */
	public function testsaveQuestionnairePage() {
		$this->setUp();

		$questionnaireId = 1;
		$status = NetCommonsBlockComponent::STATUS_IN_DRAFT;

		$questionnairePage = array();
		$questionnairePage['QuestionnairePage'] = array(
		'QuestionnairePage' =>
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
					'modified' => '2015-04-13 06:38:28'));
		$questionnairePage['QuestionnairePage']['QuestionnaireQuestion'] =
			array(
				'QuestionnaireQuestion' => array(
					array(
						'key' => 'testkey',
						'language_id' => 0,
						'origin_id' => 1,
						'is_active' => 1,
						'is_latest' => 1,
						'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
						'question_sequence' => 1,
						'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
						'question_type' => 1,
						'description' => 'Lorem ipsum dolor sit amet',
						'is_require' => 1,
						'question_type_option' => QuestionnairesComponent::TYPE_OPTION_NUMERIC,
						'is_choice_random' => 1,
						'is_skip' => 1,
						'min' => '1',
						'max' => '3',
						'is_result_display' => 1,
						'result_display_type' => 1,
						'is_auto_translated' => 1,
						'questionnaire_page_id' => 2,
						'created_user' => 1,
						'created' => '2015-04-13 06:39:20',
						'modified_user' => 1,
						'modified' => '2015-04-13 06:39:20'
		)));

		$this->QuestionnairePage->saveQuestionnairePage($questionnaireId, $status, $questionnairePage['QuestionnairePage']);

		$result = $this->QuestionnairePage->findByPageTitle('TEST1');
		$this->assertInternalType('array', $result['QuestionnairePage']);

		$this->tearDown();
	}

}
