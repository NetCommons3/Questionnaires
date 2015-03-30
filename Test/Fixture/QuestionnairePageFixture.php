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
		'page_title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ページ名', 'charset' => 'utf8'),
		'page_sequence' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'ページ表示順'),
		'questionnaire_entity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_page_questionnnaire1_idx' => array('column' => 'questionnaire_entity_id', 'unique' => 0)
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
			'page_title' => 'Lorem ipsum dolor sit amet',
			'page_sequence' => 1,
			'questionnaire_entity_id' => 1,
			'created_user' => 1,
			'created' => '2015-02-03 05:56:27',
			'modified_user' => 1,
			'modified' => '2015-02-03 05:56:27'
		),
	);

}
