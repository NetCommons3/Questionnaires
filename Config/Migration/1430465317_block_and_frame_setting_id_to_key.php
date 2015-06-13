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
class BlockAndFrameSettingIdToKey extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'block_and_frame_setting_id_to_key';

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
				'questionnaire_blocks_settings' => array(
					'block_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'id'),
					'indexes' => array(
						'fk_questionnaire_blocks_settings_blocks1_idx' => array('column' => 'block_key', 'unique' => 0),
					),
				),
				'questionnaire_frame_settings' => array(
					'frame_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'sort_type'),
					'indexes' => array(
						'fk_questionnaire_frame_settings_frames1_idx' => array('column' => 'frame_key', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'questionnaire_blocks_settings' => array('block_id', 'indexes' => array('fk_questionnaire_blocks_settings_blocks1_idx')),
				'questionnaire_frame_settings' => array('frame_id', 'indexes' => array('fk_questionnaire_frame_settings_frames1_idx')),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'drop_field' => array(
				'questionnaire_blocks_settings' => array('block_key', 'indexes' => array('fk_questionnaire_blocks_settings_blocks1_idx')),
				'questionnaire_frame_settings' => array('frame_key', 'indexes' => array('fk_questionnaire_frame_settings_frames1_idx')),
			),
			'create_field' => array(
				'questionnaire_blocks_settings' => array(
					'block_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_blocks_settings_blocks1_idx' => array(),
					),
				),
				'questionnaire_frame_settings' => array(
					'frame_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_frame_settings_frames1_idx' => array(),
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
