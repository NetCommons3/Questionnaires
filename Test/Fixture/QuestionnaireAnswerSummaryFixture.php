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
		'questionnaire_origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート回答した時のセッション値を保存します。', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'ログイン後、アンケートに回答した人のusersテーブルのid。未ログインの場合NULL'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_answer_summaries_questionnaires1_idx' => array('column' => 'questionnaire_origin_id', 'unique' => 0)
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
			'test_status' => 0,
			'answer_number' => 1,
			'answer_time' => '2015-04-13 06:41:13',
			'questionnaire_origin_id' => 1,
			'session_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'user_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:41:13',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:41:13'
		),
	);

}
