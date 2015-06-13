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
class IdToIs extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = '_id_to_is_';

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
					'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開中データか否か', 'after' => 'origin_id'),
					'is_latest' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '最新編集データであるか否か', 'after' => 'is_active'),
				),
				'questionnaire_pages' => array(
					'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開中データか否か', 'after' => 'origin_id'),
					'is_latest' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '最新編集データであるか否か', 'after' => 'is_active'),
				),
				'questionnaire_questions' => array(
					'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開中データか否か', 'after' => 'origin_id'),
					'is_latest' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '最新編集データであるか否か', 'after' => 'is_active'),
				),
				'questionnaires' => array(
					'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開中データか否か', 'after' => 'origin_id'),
					'is_latest' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '最新編集データであるか否か', 'after' => 'is_active'),
				),
			),
			'drop_field' => array(
				'questionnaire_choices' => array('active_id', 'latest_id', 'indexes' => array('active_id', 'latest_id')),
				'questionnaire_pages' => array('active_id', 'latest_id', 'indexes' => array('active_id', 'latest_id')),
				'questionnaire_questions' => array('active_id', 'latest_id', 'indexes' => array('active_id', 'latest_id')),
				'questionnaires' => array('active_id', 'latest_id', 'indexes' => array('active_id', 'latest_id')),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'drop_field' => array(
				'questionnaire_choices' => array('is_active', 'is_latest'),
				'questionnaire_pages' => array('is_active', 'is_latest'),
				'questionnaire_questions' => array('is_active', 'is_latest'),
				'questionnaires' => array('is_active', 'is_latest'),
			),
			'create_field' => array(
				'questionnaire_choices' => array(
					'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = root_id)である場合、現時点での公開されているレコードのIDが入る'),
					'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'active_id' => array('column' => 'active_id', 'unique' => 0),
						'latest_id' => array('column' => 'latest_id', 'unique' => 0),
					),
				),
				'questionnaire_pages' => array(
					'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = root_id)である場合、現時点での公開されているレコードのIDが入る'),
					'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'active_id' => array('column' => 'active_id', 'unique' => 0),
						'latest_id' => array('column' => 'latest_id', 'unique' => 0),
					),
				),
				'questionnaire_questions' => array(
					'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = root_id)である場合、現時点での公開されているレコードのIDが入る'),
					'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'active_id' => array('column' => 'active_id', 'unique' => 0),
						'latest_id' => array('column' => 'latest_id', 'unique' => 0),
					),
				),
				'questionnaires' => array(
					'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = origin_id)である場合、現時点での公開されているレコードのIDが入る'),
					'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'active_id' => array('column' => 'active_id', 'unique' => 0),
						'latest_id' => array('column' => 'latest_id', 'unique' => 0),
					),
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
