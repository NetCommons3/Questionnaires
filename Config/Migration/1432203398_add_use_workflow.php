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
class AddUseWorkflow extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_use_workflow';

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
					'use_workflow' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'after' => 'block_key'),
				),
				'questionnaire_frame_display_questionnaires' => array(
					'frame_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'id'),
					'indexes' => array(
						'fk_questionnaire_frame_display_questionnaires_questionnaire_idx' => array('column' => 'frame_key', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'questionnaire_frame_display_questionnaires' => array('questionnaire_frame_setting_id', 'indexes' => array('fk_questionnaire_frame_display_questionnaires_questionnaire_idx')),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'drop_field' => array(
				'questionnaire_blocks_settings' => array('use_workflow'),
				'questionnaire_frame_display_questionnaires' => array('frame_key', 'indexes' => array('fk_questionnaire_frame_display_questionnaires_questionnaire_idx')),
			),
			'create_field' => array(
				'questionnaire_frame_display_questionnaires' => array(
					'questionnaire_frame_setting_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_frame_display_questionnaires_questionnaire_idx' => array(),
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