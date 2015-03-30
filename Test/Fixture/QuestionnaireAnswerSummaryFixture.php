<?php
/**
 * QuestionnaireAnswerSummaryFixture
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for QuestionnaireAnswerSummaryFixture
 */
class QuestionnaireAnswerSummaryFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'answer_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '回答状態 1ページずつ表示するようなアンケートの場合、途中状態か否か | 0:回答未完了 | 1:回答完了'),
		'test_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => 'テスト時の回答かどうか 0:本番回答 | 1:テスト時回答'),
		'answer_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '回答回数　ログインして回答している人物の場合に限定して回答回数をカウントする'),
		'answer_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '回答完了の時刻　ページわけされている場合、insert_timeは回答開始時刻となるため、完了時刻を設ける'),
		'questionnaire_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_answer_summaries_questionnaires1_idx' => array('column' => 'questionnaire_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'answer_status' => 1,
			'test_status' => 1,
			'answer_number' => 1,
			'answer_time' => '2015-02-03 06:12:15',
			'questionnaire_id' => 1,
			'created_user' => 1,
			'created' => '2015-02-03 06:12:15',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:12:15'
		),
	);

}
