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
				'questionnaire_choices' => array(
					'skip_page_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'questionnairesのskip_flagがスキップ有りの時、スキップ先のページ', 'after' => 'questionnaire_question_id'),
				),
				'questionnaires' => array(
					'skip_flag' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'comment' => 'アンケート回答のスキップ有無  0:スキップ無し  1:スキップ有り', 'after' => 'questionnaire_status'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'questionnaire_choices' => array('skip_page_sequence'),
				'questionnaires' => array('skip_flag'),
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
