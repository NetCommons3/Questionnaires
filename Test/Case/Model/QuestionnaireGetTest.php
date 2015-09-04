<?php
/**
 * Questionnaire GetTest Case
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
class QuestionnaireGetTest extends QuestionnaireTestBase {

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
		$result = $this->Questionnaire->getQuestionnairesList($conditions, $sessionId, $userId, $filter, $sort, $offset, $limit);

		//テスト実施
		$this->assertEquals($result[0]['Questionnaire']['id'], 1);

		//終了処理
		$this->tearDown();
	}

/**
 * getCondition method
 *
 * @return void
 */
	public function testgetCondition() {
		//初期処理
		$this->setUp();

		//データ設定
		$blockId = 1;
		$userId = 1;
		$permissions = array();
		$permissions['contentEditable'] = false;
		$permissions['roomRoleKey'] = NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY;
		$permissions['frameKey'] = 'frame_1';
		$currentDateTime = '2015-06-24 22:22:22';
		$addConditions = array(
			'id' => 1);

		//処理実行
		$result = $this->Questionnaire->getCondition($blockId, $userId, $permissions, $currentDateTime, $addConditions);

		//期待値
		$expect = array(
			'block_id' => 1,
			'is_active' => true,
			'OR' => array(
				'start_period <' => '2015-06-24 22:22:22',
				'is_period' => '',
				),
			'is_no_member_allow' => '1',
			'id' => 1,
			'NOT' => array(
						'QuestionnaireFrameDisplayQuestionnaires.id' => null,
			),
			'QuestionnaireFrameDisplayQuestionnaires.frame_key' => 'frame_1',
		);

		//テスト実施
		$this->assertEquals($result, $expect);

		//終了処理
		$this->tearDown();
	}

/**
 * getConditionForAnswer method
 *
 * @return void
 */
	public function testgetConditionForAnswer() {
		//contentEditableがtrue
		//初期処理
		$this->setUp();

		//データ設定
		$blockId = 1;
		$userId = 1;
		$permissions = array();
		$permissions['contentEditable'] = true;
		$permissions['roomRoleKey'] = NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY;
		$permissions['frameKey'] = 'frame_1';
		$currentDateTime = '2015-06-24 22:22:22';
		$addConditions = array();

		//処理実行
		$result = $this->Questionnaire->getConditionForAnswer($blockId, $userId, $permissions, $currentDateTime, $addConditions);

		//期待値
		$expect = array(
			'block_id' => 1,
			'is_latest' => 1,
			'is_no_member_allow' => 1
		);
		//print_r($result);
		//テスト実施
		$this->assertEquals($result, $expect);

		//終了処理
		$this->tearDown();
	}

/**
 * getConditionForResult method
 *
 * @return void
 */
	public function testgetConditionForResult1() {
		//contentEditableがfalse
		//初期処理
		$this->setUp();

		//データ設定
		$blockId = 1;
		$userId = 1;
		$permissions = array();
		$permissions['contentEditable'] = false;
		$permissions['roomRoleKey'] = NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY;
		$permissions['frameKey'] = 'frame_1';
		$currentDateTime = '2015-06-24 22:22:22';
		$addConditions = array();

		//処理実行
		$result = $this->Questionnaire->getConditionForResult($blockId, $userId, $permissions, $currentDateTime, $addConditions);

		//期待値
		$expect = array(
			'block_id' => 1,
			'is_total_show' => '1',
			'is_active' => true,
			'OR' => array(
						'total_show_timing' => '0',
						'total_show_start_period <' => '2015-06-24 22:22:22',
			),
			'is_no_member_allow' => '1',
		);

		//テスト実施
		$this->assertEquals($result, $expect);

		//終了処理
		$this->tearDown();
	}

/**
 * getConditionForResult method
 *
 * @return void
 */
	public function testgetConditionForResult2() {
		//contentEditableがtrue,addConditionあり
		//初期処理
		$this->setUp();

		//データ設定
		$blockId = 1;
		$userId = 1;
		$permissions = array();
		$permissions['contentEditable'] = true;
		$permissions['roomRoleKey'] = NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY;
		$permissions['frameKey'] = 'frame_1';
		$currentDateTime = '2015-06-24 22:22:22';
		$addConditions = array(
			'id' => 1);

		//処理実行
		$result = $this->Questionnaire->getConditionForResult($blockId, $userId, $permissions, $currentDateTime, $addConditions);

		//期待値
		$expect = array(
			'block_id' => 1,
			'id' => 1,
			'is_total_show' => 1,
			'is_latest' => 1,
			'is_no_member_allow' => 1,
		);
		//print_r($result);
		//テスト実施
		$this->assertEquals($result, $expect);

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
				'is_total_show' => QuestionnairesComponent::EXPRESSION_SHOW,
				'is_period' => QuestionnairesComponent::USES_NOT_USE,
				'is_key_pass_use' => QuestionnairesComponent::USES_NOT_USE,
				'total_show_timing' => QuestionnairesComponent::USES_NOT_USE,),
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
 * getQuestionnaireCloneById method
 *
 * @return void
 */
	public function testgetQuestionnaireCloneById1() {
		//IDに対応するQuestionnaireが存在する
		//初期処理
		$this->setUp();

		//期待値の生成
		$questionnaireId = 1;

		//処理実行
		$result = $this->Questionnaire->getQuestionnaireCloneById($questionnaireId);

		//テスト実施
		//print_r($result);
		$expected = array();
		$expected['Questionnaire']['title'] = 'Lorem ipsum dolor sit amet';
		$this->assertEquals(
			$expected['Questionnaire']['title'], $result['Questionnaire']['title']);

		//終了処理
		$this->tearDown();
	}

/**
 * getQuestionnaireCloneById method
 *
 * @return void
 */
	public function testgetQuestionnaireCloneById2() {
		//IDに対応するQuestionnaireが存在しない
		//初期処理
		$this->setUp();

		//期待値の生成
		$questionnaireId = 10;

		//処理実行
		$result = $this->Questionnaire->getQuestionnaireCloneById($questionnaireId);

		//テスト実施
		//print_r($result);
		$expected = array();
		$expected['Questionnaire']['title'] = '';
		$this->assertEquals(
			$expected['Questionnaire']['title'], $result['Questionnaire']['title']);

		//終了処理
		$this->tearDown();
	}
}
