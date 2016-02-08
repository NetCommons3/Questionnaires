<?php
/**
 * QuestionnaireAnswerSummaryFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'answer_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '回答状態 1ページずつ表示するようなアンケートの場合、途中状態か否か | 0:回答未完了 | 1:回答完了'),
		'test_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'テスト時の回答かどうか 0:本番回答 | 1:テスト時回答'),
		'answer_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '回答回数　ログインして回答している人物の場合に限定して回答回数をカウントする'),
		'answer_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '回答完了の時刻　ページわけされている場合、insert_timeは回答開始時刻となるため、完了時刻を設ける'),
		'questionnaire_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート回答した時のセッション値を保存します。', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ログイン後、アンケートに回答した人のusersテーブルのid。未ログインの場合NULL'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
			'answer_status' => '2',
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_12',
			'user_id' => 3,
		),
		array(
			'id' => 2,
			'answer_status' => '1',	// 確認前
			'test_status' => '0',
			'answer_number' => 1,
			'answer_time' => '2016-02-06 00:00:00',
			'questionnaire_key' => 'questionnaire_12',
			'user_id' => 1,
		)
	);
}
