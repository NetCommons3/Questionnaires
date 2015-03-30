<?php
class Questionnaires extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'questionnaires';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'questionnaire_entities' => array(
					'period_flag' => array('type' => 'boolean', 'null' => true, 'default' => null, 'comment' => '期間設定フラグ | 0:期間設定なし| 1:期間設定あり', 'after' => 'sub_title'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'questionnaire_entities' => array('period_flag'),
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
