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
class ChangePublicType extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_public_type';

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
				'questionnaires' => array(
					'answer_timing' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false, 'after' => 'sub_title'),
					'answer_start_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'answer_timing'),
					'answer_end_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'answer_start_period'),
				),
			),
			'drop_field' => array(
				'questionnaires' => array('public_type', 'publish_start', 'publish_end', 'key_phrase'),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'drop_field' => array(
				'questionnaires' => array('answer_timing', 'answer_start_period', 'answer_end_period'),
			),
			'create_field' => array(
				'questionnaires' => array(
					'public_type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 4, 'unsigned' => false),
					'publish_start' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'key_phrase' => array('type' => 'string', 'null' => true, 'default' => 'NetCommons', 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'キーフレーズ', 'charset' => 'utf8'),
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
