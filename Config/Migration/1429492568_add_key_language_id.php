<?php
class AddKeyLanguageId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_key_language_id';

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
					'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'id'),
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'after' => 'key'),
				),
				'questionnaire_pages' => array(
					'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'id'),
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'after' => 'key'),
				),
				'questionnaire_questions' => array(
					'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'id'),
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'after' => 'key'),
				),
				'questionnaires' => array(
					'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'id'),
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'after' => 'key'),
					'is_repeat_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'key_phrase'),
					'total_show_start_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'total_show_timing'),
				),
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
			'drop_field' => array(
				'questionnaires' => array('is_repeate_allow', 'total_show_start_peirod'),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'drop_field' => array(
				'questionnaire_choices' => array('key', 'language_id'),
				'questionnaire_pages' => array('key', 'language_id'),
				'questionnaire_questions' => array('key', 'language_id'),
				'questionnaires' => array('key', 'language_id', 'is_repeat_allow', 'total_show_start_period'),
			),
			'alter_field' => array(
				'questionnaire_choices' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
				),
				'questionnaire_pages' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
				),
				'questionnaire_questions' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
				),
				'questionnaires' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
				),
			),
			'create_field' => array(
				'questionnaires' => array(
					'is_repeate_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '繰り返し回答を許可するか | 0:許可しない | 1:許可する'),
					'total_show_start_peirod' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '集計結果表示開始日時 total_show_timingが1のとき有効 画面表示時、NULLの場合、自動的に回答締切日時が設定される（回答締切がない場合は現在日時）'),
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
