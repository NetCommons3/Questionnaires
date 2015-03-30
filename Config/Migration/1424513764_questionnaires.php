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
				'questionnaire_answer_summaries' => array(
					'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート回答した時のセッション値を保存します。', 'charset' => 'utf8', 'after' => 'questionnaire_id'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'questionnaire_answer_summaries' => array('session_value'),
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
