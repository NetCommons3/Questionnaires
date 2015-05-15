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
		'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
		'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = root_id)である場合、現時点での公開されているレコードのIDが入る'),
		'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
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
			'active_id' => array('column' => 'active_id', 'unique' => 0),
			'latest_id' => array('column' => 'latest_id', 'unique' => 0),
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
			'origin_id' => 1,
			'active_id' => 1,
			'latest_id' => 1,
			'questionnaire_id' => 1,
			'page_title' => 'Lorem ipsum dolor sit amet',
			'page_sequence' => 1,
			'is_auto_translated' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:38:28',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:38:28'
		),
	);

}
