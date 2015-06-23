<?php
/**
 * QuestionnairePageFixture
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for QuestionnairePageFixture
 */
class QuestionnairePageFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開中データか否か'),
		'is_latest' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '最新編集データであるか否か'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'questionnaire_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'page_title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ページ名', 'charset' => 'utf8'),
		'page_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'ページ表示順'),
		'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'origin_id' => array('column' => 'origin_id', 'unique' => 0),
			'fk_questionnaire_pages_questionnaires1_idx' => array('column' => 'questionnaire_id', 'unique' => 0)
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
			'key' => 'page1',
			'language_id' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'status' => 1,
			'questionnaire_id' => 1,
			'page_title' => 'test',
			'page_sequence' => 0,
			'is_auto_translated' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:38:28',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:38:28'
		),
		array(
			'id' => 2,
			'key' => 'page2',
			'language_id' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'status' => 1,
			'questionnaire_id' => 2,
			'page_title' => 'test2',
			'page_sequence' => 0,
			'is_auto_translated' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:38:28',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:38:28'
		),
		array(
			'id' => 3,
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
		),
	);

}
