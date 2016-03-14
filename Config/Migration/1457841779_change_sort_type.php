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
class ChangeSortType extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_sort_type';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
			),
			'alter_field' => array(
				'questionnaire_frame_settings' => array(
					'sort_type' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'alter_field' => array(
				'questionnaire_frame_settings' => array(
					'sort_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '表示並び順 0:新着順 1:回答期間順（降順） 2:アンケートステータス順（昇順） 3:タイトル順（昇順）'),
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
