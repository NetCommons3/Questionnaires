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
				'page_title' => 'kumaTEST1',
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

		$this->QuestionnairePage->setPageToQuestionnaire($data);
		$expected = 2;
		$this->assertEquals($data['Questionnaire']['page_count'], $expected);//
		//		$result = $this->QuestionnairePage->QuestionnaireQuestion->find('all');
		//		$result = $this->QuestionnairePage->find('all');
		$this->tearDown();
	}

/**
 * saveQuestionnairePage method
 *
 * @return void
 */
	public function testsaveQuestionnairePage() {
		$this->setUp();

		$questionnaireId = 2;
		$status = NetCommonsBlockComponent::STATUS_PUBLISHED? true: false;
		$questionnairePages = array(
			'QuestionnairePage' => array(
				array(
					'id' => 2,
					'origin_id' => 1,
					'active_id' => 1,
					'latest_id' => 1,
					'questionnaire_id' => 2,
					'page_title' => 'kumaTEST1',
					'page_sequence' => 1,
					'is_auto_translated' => 1,
					'created_user' => 1,
					'created' => '2015-04-13 06:38:28',
					'modified_user' => 1,
					'modified' => '2015-04-13 06:38:28'
				)
			)
		);
		$this->QuestionnairePage->saveQuestionnairePage($questionnaireId, $status, $questionnairePages);

		//$result = $this->QuestionnairePage->QuestionnaireQuestion->find('all');
		//PENDING		$result = $this->QuestionnairePage->find('all',array(
		//			  'page_title' => 'kumaTEST1'));
		$this->tearDown();
	}

}
