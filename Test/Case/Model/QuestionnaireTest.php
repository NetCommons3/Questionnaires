<?php
/**
 * Questionnaire Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for Questionnaire Test Case
 */
class QuestionnaireTest extends QuestionnaireTestBase {

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
 * afterFind method
 *
 * @return void
 */
	public function testafterFind() {
		$this->setUp();

		$primary = false;

		$results = $this->Questionnaire->find('all');
		$results[0]['Questionnaire']['all_answer_count'] = 0;

		$result = $this->Questionnaire->afterFind($results, $primary);

		$this->assertEquals($result[0]['Questionnaire']['all_answer_count'], 1);

		$this->tearDown();
	}

/**
 * getQuestionnairesList method
 *
 * @return void
 */
	public function testgetQuestionnairesList() {
		//初期処理
		$this->setUp();

		//データ設定
		$conditions = array();
		//array(
		//'Block.id' => 1);

		$sessionId = 1;
		$userId = 1;
		$filter = array();
		$sort = 'modified DESC';
		$offset = 0;
		$limit = QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE;

		//処理実行
		$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));
		$result = $this->Questionnaire->getQuestionnairesList($conditions, $sessionId, $userId, $filter, $sort, $offset, $limit);

		//テスト実施
		$this->assertEquals($result[0]['Questionnaire']['id'], 1);

		//終了処理
		$this->tearDown();
	}

/**
 * getDefaultQuestionnaire method
 *
 * @return void
 */
	public function testgetDefaultQuestionnaire() {
		//初期処理
		$this->setUp();

		//期待値の生成
		$addData = 'add';
		$expected = array();
		$expected['Questionnaire'] = Hash::merge(
			array(
			'title' => '',
			'key' => '',
			'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT,
			'is_total_show' => QuestionnairesComponent::EXPRESSION_SHOW),
			$addData);
		$expected['QuestionnairePage'] = array(
			array(
				'page_title' => __d('questionnaires', 'First Page'),
				'page_sequence' => 0,
				'origin_id' => 0,
			));

		//処理実行
		$result = $this->Questionnaire->getDefaultQuestionnaire($addData);

		//テスト実施
		$this->_assertArray(
			$expected['Questionnaire'],
			$result['Questionnaire']);
		$this->assertEquals(
			$expected['QuestionnairePage'][0]['page_title'], $result['QuestionnairePage'][0]['page_title']);

		//終了処理
		$this->tearDown();
	}

/**
 * saveQuestionnaire method
 *
 * @return void
 */
	public function testsaveQuestionnaire() {
		//初期処理
		$this->setUp();

		//データ設定
		$questionnaire = array();
		$data = $this->Questionnaire->find('all');

		$data[0]['Questionnaire']['id'] = 2;
		$data[0]['Questionnaire']['title'] = 'test2';
		$data[0]['Questionnaire']['key'] = 'key2';
		$data[0]['Questionnaire']['status'] = 3;
		$data[0]['Frame']['id'] = 1;
		$data[0]['QuestionnairePage'][0]['id'] = 2;
		unset($data[0]['QuestionnairePage'][0]['QuestionnaireQuestion']);

		$questionnaire = Hash::merge( array(
			'Comment' => array(
			'comment' => '')),
			$data[0]);

		//処理実行
		$result = $this->Questionnaire->saveQuestionnaire($questionnaire);

		//テスト実施
		$result = $this->Questionnaire->findByTitle('test2');

		$this->assertEquals(
			$questionnaire['Questionnaire']['key'], $result['Questionnaire']['key']);

		//終了処理
		$this->tearDown();
	}

/**
 * deleteQuestionnaire method
 *
 * @return void
 */
	public function testdeleteQuestionnaire() {
		//初期処理
		$this->setUp();

		//データ設定
		$data = array();
		$data['Questionnaire']['origin_id'] = 1;
		$data['Questionnaire']['key'] = '41ef6012e7574886c9a52fb598f8c5f8';

		//処理実行
		$result = $this->Questionnaire->deleteQuestionnaire($data);

		//テスト実施
		$this->assertEquals($result, true );
		$result = $this->Questionnaire->findByKey('41ef6012e7574886c9a52fb598f8c5f8');
		$expected = array();
		$this->assertEquals( $result, $expected );

		//終了処理
		$this->tearDown();
	}
}
