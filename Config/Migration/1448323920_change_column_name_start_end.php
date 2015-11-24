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
class ChangeColumnNameStartEnd extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_column_name_start_end';

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
					'public_type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 4, 'unsigned' => false, 'after' => 'sub_title'),
					'publish_start' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'public_type'),
					'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'publish_start'),
				),
			),
			'drop_field' => array(
				'questionnaires' => array('is_period', 'start_period', 'end_period'),
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'drop_field' => array(
				'questionnaires' => array('public_type', 'publish_start', 'publish_end'),
			),
			'create_field' => array(
				'questionnaires' => array(
					'is_period' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '期間設定フラグ | 0:期間設定なし| 1:期間設定あり'),
					'start_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アンケート開始日時 | 画面表示時、ここがNULLの場合はDefaultで現在日時が設定される'),
					'end_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アンケート回答締切日時| 画面表示時、ここがNULLの場合はDefaultで開始日時＋1Monthが設定される'),
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
