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
			'drop_field' => array(
				'questionnaire_entities' => array('questionnaire_status'),
			),
			'create_field' => array(
				'questionnaires' => array(
					'questionnaire_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => 'アンケート実施状態　0:公開中　1:未実施  2:停止中', 'after' => 'block_id'),
				),
			),
		),
		'down' => array(
			'create_field' => array(
				'questionnaire_entities' => array(
					'questionnaire_status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => 'アンケート実施状態　0:公開中　1:未実施  2:停止中'),
				),
			),
			'drop_field' => array(
				'questionnaires' => array('questionnaire_status'),
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
