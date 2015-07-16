<?php
/**
 * QuestionnaireAnswerSummaryCsv Test Case
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
class QuestionnaireAnswerSummaryCsvTest extends QuestionnaireAnswerTestBase {

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
 *  getAnswerSummary method
 *
 * @return void
 */
	public function testgetAnswerSummary1() {
		// offsetが0,summary有,TEXTタイプ
		//初期処理
		$this->setUp();

		//データの生成
		$questionnaire = array(
			'Questionnaire' => array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => 1,
				'status' => 1,
				'title' => 'testtext',
				'sub_title' => 'subtitle',
				'is_period' => 0,
				'start_period' => '2015-07-07 10:20:00',
				'end_period' => '2015-07-07 10:20:00',
				'is_no_member_allow' => '',
				'is_anonymity' => '',
				'is_key_pass_use' => '',
				'key_phrase' => '',
				'is_repeat_allow' => 1,
				'is_total_show' => 1,
				'total_show_timing' => 0,
				'total_show_start_period' => '2015-07-07 10:20:00',
				'total_comment' => 'this question is......',
				'is_image_authentication' => '',
				'thanks_content' => 'thansk message',
				'is_open_mail_send' => '',
				'open_mail_subject' => 'Questionnaire to you has arrived',
				'open_mail_body' => '',
				'is_answer_mail_send' => '',
				'is_page_random' => '',
				'is_auto_translated' => '',
				'created_user' => 1,
				'created' => '2015-07-07 10:20:03',
				'modified_user' => 1,
				'modified' => '2015-07-07 10:20:03',
				'period_range_stat' => 1,
				'page_count' => 1,
				'question_count' => 1,
				'all_answer_count' => 1,
			),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'testkey',
					'language_id' => 0,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 1,
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'next_page_sequence' => '',
					'is_auto_translated' => '',
					'created_user' => 1,
					'created' => '2015-07-07 10:20:03',
					'modified_user' => 1,
					'modified' => '2015-07-07 10:20:03',
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'language_id' => 0,
							'origin_id' => 1,
							'is_active' => 1,
							'is_latest' => 1,
							'status' => 1,
							'question_sequence' => 0,
							'question_value' => 'aaa',
							'question_type' => QuestionnairesComponent::TYPE_TEXT,
							'description' => 'what day is today?',
						)))), );
		$limit = 0;
		$offset = 0;

		//期待値（ "回答者","回答日","回数"）
		$expect = array();
		$expect[0][] = __d('questionnaires', 'Respondent');
		$expect[0][] = __d('questionnaires', 'Answer Date');
		$expect[0][] = __d('questionnaires', 'Number');
		$expect[0][] = $questionnaire['QuestionnairePage'][0]['page_sequence'] . '-' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_sequence'] . '. ' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_value'];

		// 処理実行
		$result = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv( $questionnaire, $limit, $offset );

		// テスト実施
		//print_r($result);print_r($expect);
		$this->assertEquals($result[0], $expect[0] );

		//終了処理
		$this->tearDown();
	}

/**
 *  getAnswerSummary method
 *
 * @return void
 */
	public function testgetAnswerSummary2() {
		// offsetが0,summary無,Matrixタイプ
		//初期処理
		$this->setUp();
		//データの生成
		$questionnaire = array(
			'Questionnaire' => array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 5,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => 1,
				'status' => 1,
				'title' => 'testtext',
				'sub_title' => 'subtitle',
				'is_period' => 0,
				'start_period' => '2015-07-07 10:20:00',
				'end_period' => '2015-07-07 10:20:00',
				'is_no_member_allow' => '',
				'is_anonymity' => '',
				'is_key_pass_use' => '',
				'key_phrase' => '',
				'is_repeat_allow' => 1,
				'is_total_show' => 1,
				'total_show_timing' => 0,
				'total_show_start_period' => '2015-07-07 10:20:00',
				'total_comment' => 'this question is......',
				'is_image_authentication' => '',
				'thanks_content' => 'thansk message',
				'is_open_mail_send' => '',
				'open_mail_subject' => 'Questionnaire to you has arrived',
				'open_mail_body' => '',
				'is_answer_mail_send' => '',
				'is_page_random' => '',
				'is_auto_translated' => '',
				'created_user' => 1,
				'created' => '2015-07-07 10:20:03',
				'modified_user' => 1,
				'modified' => '2015-07-07 10:20:03',
				'period_range_stat' => 1,
				'page_count' => 1,
				'question_count' => 1,
				'all_answer_count' => 1, ),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'testkey',
					'language_id' => 0,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 1,
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'next_page_sequence' => '',
					'is_auto_translated' => '',
					'created_user' => 1,
					'created' => '2015-07-07 10:20:03',
					'modified_user' => 1,
					'modified' => '2015-07-07 10:20:03',
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'language_id' => 0,
							'origin_id' => 1,
							'is_active' => 1,
							'is_latest' => 1,
							'status' => 1,
							'question_sequence' => 0,
							'question_value' => 'aaa',
							'question_type' => QuestionnairesComponent::TYPE_MATRIX_MULTIPLE,
							'description' => 'what day is today?',
							'QuestionnaireChoice' => array(
								0 => array(
								'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
								'choice_label' => 'choicelabel',
								)))))), );
		$limit = 0;
		$offset = 0;
		//期待値（ "回答者","回答日","回数"）
		$expect = array();
		$expect[0][] = __d('questionnaires', 'Respondent');
		$expect[0][] = __d('questionnaires', 'Answer Date');
		$expect[0][] = __d('questionnaires', 'Number');
		$expect[0][] = $questionnaire['QuestionnairePage'][0]['page_sequence'] . '-' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_sequence'] . '-' . 1 . '. ' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['QuestionnaireChoice'][0]['choice_label'];

		// 処理実行
		$result = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv( $questionnaire, $limit, $offset );

		// テスト実施
		$this->assertEquals($result, $expect );

		//終了処理
		$this->tearDown();
	}
/**
 *  getAnswerSummary method
 *
 * @return void
 */
	public function testgetAnswerSummary3() {
		// offsetが0,summary有,SELECTタイプ,その他回答なし
		//初期処理
		$this->setUp();
		//データの生成
		$questionnaire = array(
			'Questionnaire' => array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => 1,
				'status' => 1,
				'title' => 'testtext',
				'sub_title' => 'subtitle',
				'is_period' => 0,
				'start_period' => '2015-07-07 10:20:00',
				'end_period' => '2015-07-07 10:20:00',
				'is_no_member_allow' => '',
				'is_anonymity' => '',
				'is_key_pass_use' => '',
				'key_phrase' => '',
				'is_repeat_allow' => 1,
				'is_total_show' => 1,
				'total_show_timing' => 0,
				'total_show_start_period' => '2015-07-07 10:20:00',
				'total_comment' => 'this question is......',
				'is_image_authentication' => '',
				'thanks_content' => 'thansk message',
				'is_open_mail_send' => '',
				'open_mail_subject' => 'Questionnaire to you has arrived',
				'open_mail_body' => '',
				'is_answer_mail_send' => '',
				'is_page_random' => '',
				'is_auto_translated' => '',
				'created_user' => 1,
				'created' => '2015-07-07 10:20:03',
				'modified_user' => 1,
				'modified' => '2015-07-07 10:20:03',
				'period_range_stat' => 1,
				'page_count' => 1,
				'question_count' => 1,
				'all_answer_count' => 1, ),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'testkey',
					'language_id' => 0,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 1,
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'next_page_sequence' => '',
					'is_auto_translated' => '',
					'created_user' => 1,
					'created' => '2015-07-07 10:20:03',
					'modified_user' => 1,
					'modified' => '2015-07-07 10:20:03',
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'language_id' => 0,
							'origin_id' => 1,
							'is_active' => 1,
							'is_latest' => 1,
							'status' => 1,
							'question_sequence' => 0,
							'question_value' => 'aaa',
							'question_type' => QuestionnairesComponent::TYPE_SELECTION,
							'description' => 'what day is today?',
							'QuestionnaireChoice' => array(
								0 => array(
									'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
									'choice_label' => 'choicelabel',
									'origin_id' => 1,
									'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
								), ))))), );
		$limit = 0;
		$offset = 0;
		//期待値（ "回答者","回答日","回数"）
		$expect = array();
		$expect[0][] = __d('questionnaires', 'Respondent');
		$expect[0][] = __d('questionnaires', 'Answer Date');
		$expect[0][] = __d('questionnaires', 'Number');
		$expect[0][] = $questionnaire['QuestionnairePage'][0]['page_sequence'] . '-' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_sequence'] . '. ' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_value'];
		// 処理実行
		$result = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv( $questionnaire, $limit, $offset );
		// テスト実施
		$this->assertEquals($result[0], $expect[0] );
		//終了処理
		$this->tearDown();
	}

/**
 *  getAnswerSummary method
 *
 * @return void
 */
	public function testgetAnswerSummary4() {
		// offsetが0,summary有,SELECTタイプ,その他回答あり
		//初期処理
		$this->setUp();
		//データの生成
		$questionnaire = array(
			'Questionnaire' => array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => 1,
				'status' => 1,
				'title' => 'testtext',
				'sub_title' => 'subtitle',
				'is_period' => 0,
				'start_period' => '2015-07-07 10:20:00',
				'end_period' => '2015-07-07 10:20:00',
				'is_no_member_allow' => '',
				'is_anonymity' => '',
				'is_key_pass_use' => '',
				'key_phrase' => '',
				'is_repeat_allow' => 1,
				'is_total_show' => 1,
				'total_show_timing' => 0,
				'total_show_start_period' => '2015-07-07 10:20:00',
				'total_comment' => 'this question is......',
				'is_image_authentication' => '',
				'thanks_content' => 'thansk message',
				'is_open_mail_send' => '',
				'open_mail_subject' => 'Questionnaire to you has arrived',
				'open_mail_body' => '',
				'is_answer_mail_send' => '',
				'is_page_random' => '',
				'is_auto_translated' => '',
				'created_user' => 1,
				'created' => '2015-07-07 10:20:03',
				'modified_user' => 1,
				'modified' => '2015-07-07 10:20:03',
				'period_range_stat' => 1,
				'page_count' => 1,
				'question_count' => 1,
				'all_answer_count' => 1, ),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'testkey',
					'language_id' => 0,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 1,
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'next_page_sequence' => '',
					'is_auto_translated' => '',
					'created_user' => 1,
					'created' => '2015-07-07 10:20:03',
					'modified_user' => 1,
					'modified' => '2015-07-07 10:20:03',
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'language_id' => 0,
							'origin_id' => 1,
							'is_active' => 1,
							'is_latest' => 1,
							'status' => 1,
							'question_sequence' => 0,
							'question_value' => 'aaa',
							'question_type' => QuestionnairesComponent::TYPE_SELECTION,
							'description' => 'what day is today?',
							'QuestionnaireChoice' => array(
								0 => array(
									'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
									'choice_label' => 'choicelabel',
									'origin_id' => 1,
									'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT, ), ))))), );
		$limit = 0;
		$offset = 0;
		//期待値（ "回答者","回答日","回数"）
		$expect = array();
		$expect[0][] = __d('questionnaires', 'Respondent');
		$expect[0][] = __d('questionnaires', 'Answer Date');
		$expect[0][] = __d('questionnaires', 'Number');
		$expect[0][] = $questionnaire['QuestionnairePage'][0]['page_sequence'] . '-' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_sequence'] . '. ' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_value'];
		// 処理実行
		$result = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv( $questionnaire, $limit, $offset );
		// テスト実施
		$this->assertEquals($result[0], $expect[0] );
		//終了処理
		$this->tearDown();
	}

/**
 *  getAnswerSummary method
 *
 * @return void
 */
	public function testgetAnswerSummary5() {
		// offsetが0,summary有,Matrixタイプ
		//初期処理
		$this->setUp();
		//データの生成
		$questionnaire = array(
			'Questionnaire' => array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => 1,
				'status' => 1,
				'title' => 'testtext',
				'sub_title' => 'subtitle',
				'is_period' => 0,
				'start_period' => '2015-07-07 10:20:00',
				'end_period' => '2015-07-07 10:20:00',
				'is_no_member_allow' => '',
				'is_anonymity' => '',
				'is_key_pass_use' => '',
				'key_phrase' => '',
				'is_repeat_allow' => 1,
				'is_total_show' => 1,
				'total_show_timing' => 0,
				'total_show_start_period' => '2015-07-07 10:20:00',
				'total_comment' => 'this question is......',
				'is_image_authentication' => '',
				'thanks_content' => 'thansk message',
				'is_open_mail_send' => '',
				'open_mail_subject' => 'Questionnaire to you has arrived',
				'open_mail_body' => '',
				'is_answer_mail_send' => '',
				'is_page_random' => '',
				'is_auto_translated' => '',
				'created_user' => 1,
				'created' => '2015-07-07 10:20:03',
				'modified_user' => 1,
				'modified' => '2015-07-07 10:20:03',
				'period_range_stat' => 1,
				'page_count' => 1,
				'question_count' => 1,
				'all_answer_count' => 1, ),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'testkey',
					'language_id' => 0,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 1,
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'next_page_sequence' => '',
					'is_auto_translated' => '',
					'created_user' => 1,
					'created' => '2015-07-07 10:20:03',
					'modified_user' => 1,
					'modified' => '2015-07-07 10:20:03',
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'language_id' => 0,
							'origin_id' => 1,
							'is_active' => 1,
							'is_latest' => 1,
							'status' => 1,
							'question_sequence' => 0,
							'question_value' => 'aaa',
							'question_type' => QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
							'description' => 'what day is today?',
							'QuestionnaireChoice' => array(
								0 => array(
									'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
									'choice_label' => 'choicelabel',
									'origin_id' => 1,
									'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT, ), ))))), );
		$limit = 0;
		$offset = 0;
		//期待値（ "回答者","回答日","回数"）
		$expect = array();
		$expect[0][] = __d('questionnaires', 'Respondent');
		$expect[0][] = __d('questionnaires', 'Answer Date');
		$expect[0][] = __d('questionnaires', 'Number');
		$expect[0][] = $questionnaire['QuestionnairePage'][0]['page_sequence'] . '-' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_sequence'] . '-' . 1 . '. ' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['QuestionnaireChoice'][0]['choice_label'];
		// 処理実行
		$result = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv( $questionnaire, $limit, $offset );
		// テスト実施
		$this->assertEquals($result[0], $expect[0] );
		//終了処理
		$this->tearDown();
	}

/**
 *  getAnswerSummary method
 *
 * @return void
 */
	public function testgetAnswerSummary6() {
		// 回答データ配列が0になる,summary有,Matrixタイプ
		//初期処理
		$this->setUp();
		//データの生成
		$questionnaire = array(
			'Questionnaire' => array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => 1,
				'status' => 1,
				'title' => 'testtext',
				'sub_title' => 'subtitle',
				'is_period' => 0,
				'start_period' => '2015-07-07 10:20:00',
				'end_period' => '2015-07-07 10:20:00',
				'is_no_member_allow' => '',
				'is_anonymity' => '',
				'is_key_pass_use' => '',
				'key_phrase' => '',
				'is_repeat_allow' => 1,
				'is_total_show' => 1,
				'total_show_timing' => 0,
				'total_show_start_period' => '2015-07-07 10:20:00',
				'total_comment' => 'this question is......',
				'is_image_authentication' => '',
				'thanks_content' => 'thansk message',
				'is_open_mail_send' => '',
				'open_mail_subject' => 'Questionnaire to you has arrived',
				'open_mail_body' => '',
				'is_answer_mail_send' => '',
				'is_page_random' => '',
				'is_auto_translated' => '',
				'created_user' => 1,
				'created' => '2015-07-07 10:20:03',
				'modified_user' => 1,
				'modified' => '2015-07-07 10:20:03',
				'period_range_stat' => 1,
				'page_count' => 1,
				'question_count' => 1,
				'all_answer_count' => 1, ),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'testkey',
					'language_id' => 0,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 1,
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'next_page_sequence' => '',
					'is_auto_translated' => '',
					'created_user' => 1,
					'created' => '2015-07-07 10:20:03',
					'modified_user' => 1,
					'modified' => '2015-07-07 10:20:03',
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'language_id' => 0,
							'origin_id' => 2,
							'is_active' => 1,
							'is_latest' => 1,
							'status' => 1,
							'question_sequence' => 0,
							'question_value' => 'aaa',
							'question_type' => QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
							'description' => 'what day is today?',
							'QuestionnaireChoice' => array(
								0 => array(
									'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
									'choice_label' => 'choicelabel',
									'origin_id' => 1,
									'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT, ), ))))), );
		$limit = 0;
		$offset = 0;
		//期待値（ "回答者","回答日","回数"）
		$expect = array();
		$expect[0][] = __d('questionnaires', 'Respondent');
		$expect[0][] = __d('questionnaires', 'Answer Date');
		$expect[0][] = __d('questionnaires', 'Number');
		$expect[0][] = $questionnaire['QuestionnairePage'][0]['page_sequence'] . '-' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_sequence'] . '-' . 1 . '. ' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['QuestionnaireChoice'][0]['choice_label'];
		// 処理実行
		$result = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv( $questionnaire, $limit, $offset );
		// テスト実施
		$this->assertEquals($result[0], $expect[0] );
		//終了処理
		$this->tearDown();
	}

/**
 *  getAnswerSummary method
 *
 * @return void
 */
	public function testgetAnswerSummary7() {
		//その他が選ばれている場合,summary有,Matrixタイプ
		//初期処理
		$this->setUp();
		//データの生成
		$questionnaire = array(
			'Questionnaire' => array(
				'id' => 1,
				'key' => 'testkey',
				'language_id' => 0,
				'origin_id' => 1,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => 1,
				'status' => 1,
				'title' => 'testtext',
				'sub_title' => 'subtitle',
				'is_period' => 0,
				'start_period' => '2020-07-07 10:20:00',
				'end_period' => '2021-07-07 10:20:00',
				'is_no_member_allow' => '',
				'is_anonymity' => '',
				'is_key_pass_use' => '',
				'key_phrase' => '',
				'is_repeat_allow' => 1,
				'is_total_show' => 1,
				'total_show_timing' => 0,
				'total_show_start_period' => '2020-07-07 10:20:00',
				'total_comment' => 'this question is......',
				'is_image_authentication' => '',
				'thanks_content' => 'thansk message',
				'is_open_mail_send' => '',
				'open_mail_subject' => 'Questionnaire to you has arrived',
				'open_mail_body' => '',
				'is_answer_mail_send' => '',
				'is_page_random' => '',
				'is_auto_translated' => '',
				'created_user' => 1,
				'created' => '2015-07-07 10:20:03',
				'modified_user' => 1,
				'modified' => '2015-07-07 10:20:03',
				'period_range_stat' => 1,
				'page_count' => 1,
				'question_count' => 1,
				'all_answer_count' => 1, ),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'testkey',
					'language_id' => 0,
					'origin_id' => 1,
					'is_active' => 1,
					'is_latest' => 1,
					'status' => 1,
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'next_page_sequence' => '',
					'is_auto_translated' => '',
					'created_user' => 1,
					'created' => '2015-07-07 10:20:03',
					'modified_user' => 1,
					'modified' => '2015-07-07 10:20:03',
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'language_id' => 0,
							'origin_id' => 1,
							'is_active' => 1,
							'is_latest' => 1,
							'status' => 1,
							'question_sequence' => 0,
							'question_value' => 'aaa',
							'question_type' => QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST,
							'description' => 'what day is today?',
							'QuestionnaireChoice' => array(
								0 => array(
									'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
									'choice_label' => 'choicelabel',
									'origin_id' => 1,
									'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED, ), ))))), );
		$limit = 0;
		$offset = 0;
		//期待値（ "回答者","回答日","回数"）
		$expect = array();
		$expect[0][] = __d('questionnaires', 'Respondent');
		$expect[0][] = __d('questionnaires', 'Answer Date');
		$expect[0][] = __d('questionnaires', 'Number');
		$expect[0][] = $questionnaire['QuestionnairePage'][0]['page_sequence'] . '-' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['question_sequence'] . '-' . 1 . '. ' . $questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'][0]['QuestionnaireChoice'][0]['choice_label'];
		// 処理実行
		$result = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv( $questionnaire, $limit, $offset );
		// テスト実施
		$this->assertEquals($result[0], $expect[0] );
		//終了処理
		$this->tearDown();
	}

}
