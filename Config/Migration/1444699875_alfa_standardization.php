<?php
/**
 * Questionnaires Migration file
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
/**
 * Questionnaires Migration
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Config\Migration
 */
class AlfaStandardization extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alfa_Standardization';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'questionnaire_settings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'block_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'use_workflow' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'fk_questionnaire_blocks_settings_blocks1_idx' => array('column' => 'block_key', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'questionnaire_answer_summaries' => array(
					'questionnaire_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'answer_time'),
				),
				'questionnaire_answers' => array(
					'questionnaire_question_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'questionnaire_answer_summary_id'),
				),
				'questionnaire_frame_display_questionnaires' => array(
					'questionnaire_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'frame_key'),
				),
				'questionnaires' => array(
					'import_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'is_page_random'),
					'export_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'import_key'),
				),
			),
			'drop_field' => array(
				'questionnaire_answer_summaries' => array('questionnaire_origin_id', 'indexes' => array('fk_questionnaire_answer_summaries_questionnaires1_idx')),
				'questionnaire_answers' => array('questionnaire_question_origin_id', 'indexes' => array('fk_questionnaire_answers_questionnaire_questions1_idx')),
				'questionnaire_choices' => array('origin_id', 'is_auto_translated', 'indexes' => array('origin_id')),
				'questionnaire_frame_display_questionnaires' => array('questionnaire_origin_id', 'indexes' => array('questionnaire_origin_id')),
				'questionnaire_pages' => array('origin_id', 'is_auto_translated', 'indexes' => array('origin_id')),
				'questionnaire_questions' => array('origin_id', 'is_auto_translated', 'indexes' => array('origin_id')),
				'questionnaires' => array('origin_id', 'is_auto_translated', 'indexes' => array('origin_id')),
			),
			'drop_table' => array(
				'questionnaire_blocks_settings'
			),
		),
		'down' => array(
			'drop_table' => array(
				'questionnaire_settings'
			),
			'drop_field' => array(
				'questionnaire_answer_summaries' => array('questionnaire_key'),
				'questionnaire_answers' => array('questionnaire_question_key'),
				'questionnaire_frame_display_questionnaires' => array('questionnaire_key'),
				'questionnaires' => array('import_key', 'export_key'),
			),
			'create_field' => array(
				'questionnaire_answer_summaries' => array(
					'questionnaire_origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_answer_summaries_questionnaires1_idx' => array('column' => 'questionnaire_origin_id', 'unique' => 0),
					),
				),
				'questionnaire_answers' => array(
					'questionnaire_question_origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_answers_questionnaire_questions1_idx' => array('column' => 'questionnaire_question_origin_id', 'unique' => 0),
					),
				),
				'questionnaire_choices' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
					'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'indexes' => array(
						'origin_id' => array('column' => 'origin_id', 'unique' => 0),
					),
				),
				'questionnaire_frame_display_questionnaires' => array(
					'questionnaire_origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'indexes' => array(
						'questionnaire_origin_id' => array('column' => 'questionnaire_origin_id', 'unique' => 0),
					),
				),
				'questionnaire_pages' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
					'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'indexes' => array(
						'origin_id' => array('column' => 'origin_id', 'unique' => 0),
					),
				),
				'questionnaire_questions' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
					'is_auto_translated' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
					'indexes' => array(
						'origin_id' => array('column' => 'origin_id', 'unique' => 0),
					),
				),
				'questionnaires' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
					'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'indexes' => array(
						'origin_id' => array('column' => 'origin_id', 'unique' => 0),
					),
				),
			),
			'create_table' => array(
				'questionnaire_blocks_settings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'block_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'use_workflow' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'fk_questionnaire_blocks_settings_blocks1_idx' => array('column' => 'block_key', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
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
