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
//App::uses('YACakeTestCase', 'NetCommons.TestSuite');


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
						$data[0]['Questionnaire']['is_period'] = 1;
						$data[0]['Questionnaire']['start_period'] = '2020-07-07 10:20:00';
						$data[0]['Questionnaire']['end_period'] = '2021-07-07 10:20:00';
		$data[0]['Frame']['id'] = 1;
		$data[0]['QuestionnairePage'][0]['id'] = 2;
		$data[0]['QuestionnairePage'][0]['page_sequence'] = 0;
		$data[0]['QuestionnairePage'][0]['route_number'] = 0;
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
 * saveQuestionnaire method
 *
 * @return void
 */
	public function testsaveQuestionnaireErr1() {
		//編集ステータスとコメントの関係性からのチェック、エラー
		//初期処理
		$this->setUp();

		//データ設定
		$questionnaire = array();
		$data = $this->Questionnaire->find('all');

		$data[0]['Questionnaire']['id'] = 2;
		$data[0]['Questionnaire']['title'] = 'test2';
		$data[0]['Questionnaire']['key'] = 'key2';
		$data[0]['Questionnaire']['status'] = NetCommonsBlockComponent::STATUS_DISAPPROVED;
		$data[0]['Questionnaire']['is_period'] = 1;
		$data[0]['Questionnaire']['start_period'] = '2020-07-07 10:20:00';
		$data[0]['Questionnaire']['end_period'] = '2021-07-07 10:20:00';
		$data[0]['Frame']['id'] = 1;
		$data[0]['QuestionnairePage'][0]['id'] = 2;
		$data[0]['QuestionnairePage'][0]['page_sequence'] = 0;
		$data[0]['QuestionnairePage'][0]['route_number'] = 0;
		unset($data[0]['QuestionnairePage'][0]['QuestionnaireQuestion']);

		$questionnaire = Hash::merge( array(
			'Comment' => array(
			'comment' => '')),
			$data[0]);

		//処理実行
		$result = $this->Questionnaire->saveQuestionnaire($questionnaire);

		//テスト実施
		$this->assertFalse($result);

		//終了処理
		$this->tearDown();
	}

/**
 * saveQuestionnaire method
 *
 * @return void
 */
	public function testsaveQuestionnaireErr2() {
		//InternalErrorException例外テスト

		$this->setExpectedException('InternalErrorException');

		$questionMock = $this->getMockForModel('Questionnaires.Questionnaire', array('save'));
		$questionMock->expects($this->once())
			->method('save')
			->will($this->returnValue(false));

		//初期処理
		$this->setUp();

		//データ設定
		$questionnaire = array();
		$data = $this->Questionnaire->find('all');

		$data[0]['Questionnaire']['id'] = 3;
		$data[0]['Questionnaire']['title'] = 'test3';
		$data[0]['Questionnaire']['key'] = 'key3';
		$data[0]['Questionnaire']['status'] = 3;
		$data[0]['Questionnaire']['is_period'] = 1;
		$data[0]['Questionnaire']['start_period'] = '2020-07-07 10:20:00';
		$data[0]['Questionnaire']['end_period'] = '2021-07-07 10:20:00';
		$data[0]['Frame']['id'] = 1;
		$data[0]['QuestionnairePage'][0]['id'] = 2;
		$data[0]['QuestionnairePage'][0]['page_sequence'] = 0;
		$data[0]['QuestionnairePage'][0]['route_number'] = 0;
		unset($data[0]['QuestionnairePage'][0]['QuestionnaireQuestion']);

		$questionnaire = Hash::merge( array(
			'Comment' => array(
			'comment' => '')),
			$data[0]);

		//処理実行
		$result = $this->Questionnaire->saveQuestionnaire($questionnaire);
		//テスト実施
		$result = $this->Questionnaire->findByTitle('test3');

		$this->assertFalse($result);//検索結果は見つからない

		//終了処理
		$this->tearDown();
	}

/**
 * saveQuestionnaire method
 *
 * @return void
 */
	public function testsaveQuestionnaireErr3() {
		//InternalErrorException例外テスト
		$this->setExpectedException('InternalErrorException');

		//初期処理
		$this->setUp();

		$commentMock = $this->getMockForModel('Comments.Comment', array('save'));
		$commentMock->expects($this->any())
			->method('save')
			->will($this->returnValue(false));

		//データ設定
		// 一つすでに存在しているデータを取り出して、タイトルを変更した体でUPDATE（実質はINSERT)してみる
		$questionnaire = array();
		$data = $this->Questionnaire->find('all');

		$data[0]['Questionnaire']['title'] = 'testsaveQuestionnaireErr3';
		$data[0]['Questionnaire']['status'] = 3;
		$data[0]['Questionnaire']['is_period'] = 1;
		$data[0]['Questionnaire']['start_period'] = '2020-07-07 10:20:00';
		$data[0]['Questionnaire']['end_period'] = '2021-07-07 10:20:00';
		$data[0]['Frame']['id'] = 1;
		$data[0]['QuestionnairePage'][0]['page_sequence'] = 0;
		$data[0]['QuestionnairePage'][0]['route_number'] = 0;

		$questionnaire = Hash::merge( array(
			'Comment' => array(
				'comment' => 'aaa',
				'data' => array(
					'data' => 'aaa'))),
			$data[0]);

		//$comments = $this->Comment->getComments(null,false);
		//print_r($comments);

		//処理実行
		$this->Questionnaire->saveQuestionnaire($questionnaire);

		//テスト実施
		$result = $this->Questionnaire->findByTitle('testsaveQuestionnaireErr3');

		$this->assertFalse($result);//検索結果は見つからない

		//終了処理
		$this->tearDown();
	}

/**
 * saveQuestionnaire method
 *
 * @return void
 */
	public function testsaveQuestionnaireErr4() {
		//InternalErrorException例外テスト

		$this->setExpectedException('InternalErrorException');

		$questionMock = $this->getMockForModel('Questionnaires.QuestionnaireFrameDisplayQuestionnaire', array('saveFrameDisplayQuestionnaire'));
		$questionMock->expects($this->once())
			->method('saveFrameDisplayQuestionnaire')
			->will($this->returnValue(false));

		//初期処理
		$this->setUp();

		//データ設定
		$questionnaire = array();
		$data = $this->Questionnaire->find('all');

		$data[0]['Questionnaire']['id'] = 3;
		$data[0]['Questionnaire']['title'] = 'test3';
		$data[0]['Questionnaire']['key'] = 'key3';
		$data[0]['Questionnaire']['status'] = 3;
		$data[0]['Questionnaire']['is_period'] = 1;
		$data[0]['Questionnaire']['start_period'] = '2020-07-07 10:20:00';
		$data[0]['Questionnaire']['end_period'] = '2021-07-07 10:20:00';
		$data[0]['Frame']['id'] = 1;
		$data[0]['QuestionnairePage'][0]['id'] = 2;
		$data[0]['QuestionnairePage'][0]['page_sequence'] = 0;
		$data[0]['QuestionnairePage'][0]['route_number'] = 0;
		unset($data[0]['QuestionnairePage'][0]['QuestionnaireQuestion']);

		$questionnaire = Hash::merge( array(
			'Comment' => array(
			'comment' => '')),
			$data[0]);

		//処理実行
		$result = $this->Questionnaire->saveQuestionnaire($questionnaire);
		//テスト実施
		$result = $this->Questionnaire->findByTitle('test3');

		$this->assertFalse($result);//検索結果は見つからない

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

/**
 * deleteQuestionnaire method
 *
 * @return void
 */
	public function testdeleteQuestionnaireErr1() {
		//InternalErrorException例外テスト
		$this->setExpectedException('InternalErrorException');

		$questionMock = $this->getMockForModel('Questionnaires.Questionnaire', array('deleteAll'));
		$questionMock->expects($this->once())
			->method('deleteAll')
			->will($this->returnValue(false));

		//初期処理
		$this->setUp();

		//データ設定
		$data = array();
		$data['Questionnaire']['origin_id'] = 1;
		$data['Questionnaire']['key'] = '41ef6012e7574886c9a52fb598f8c5f8';

		//処理実行
		$result = $this->Questionnaire->deleteQuestionnaire($data);

		//テスト実施
		$this->assertFalse($result);
		$result = $this->Questionnaire->findByKey('41ef6012e7574886c9a52fb598f8c5f8');
		$this->assertEquals( $result['Questionnaire']['origin_id'], 1 );//ロールバックのため削除されない

		//終了処理
		$this->tearDown();
	}
}
