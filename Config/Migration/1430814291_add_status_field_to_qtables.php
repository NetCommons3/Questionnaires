<?php
class AddStatusFieldToQtables extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_status_field_to_qtables';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
			),
			'create_field' => array(
				'questionnaire_choices' => array(
					'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'after' => 'is_latest'),
				),
				'questionnaire_pages' => array(
					'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'after' => 'is_latest'),
				),
				'questionnaire_questions' => array(
					'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'after' => 'is_latest'),
				),
			),
			'alter_field' => array(
				'questionnaire_choices' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
				),
				'questionnaire_pages' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
				),
				'questionnaire_questions' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
				),
				'questionnaires' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'drop_field' => array(
				'questionnaire_choices' => array('status'),
				'questionnaire_pages' => array('status'),
				'questionnaire_questions' => array('status'),
			),
			'alter_field' => array(
				'questionnaire_choices' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
				),
				'questionnaire_pages' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
				),
				'questionnaire_questions' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
				),
				'questionnaires' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
