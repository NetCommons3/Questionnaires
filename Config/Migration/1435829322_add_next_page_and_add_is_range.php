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
class AddNextPageAndAddIsRange extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_next_page_and_add_is_range';

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
			),
			'create_field' => array(
				'questionnaire_pages' => array(
					'next_page_sequence' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '次ページ順番数値', 'after' => 'page_sequence'),
				),
				'questionnaire_questions' => array(
					'is_range' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '範囲設定しているか否か', 'after' => 'is_skip'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'alter_field' => array(
			),
			'drop_field' => array(
				'questionnaire_pages' => array('next_page_sequence'),
				'questionnaire_questions' => array('is_range'),
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
