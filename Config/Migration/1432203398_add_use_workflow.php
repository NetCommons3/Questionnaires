<?php
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
 * recodes
 *
 * @var array $migration
 */
	public $records = array(
		'Plugin' => array(
			array(
				'language_id' => 2,
				'key' => 'questionnaires',
				'namespace' => 'netcommons/questionnaires',
				'name' => 'QUESTIONNAIRE',
				'type' => 1,
			),
		),

		'PluginsRole' => array(
			array(
				'role_key' => 'room_administrator',
				'plugin_key' => 'questionnaires'
			),
		),

		'PluginsRoom' => array(
			array(
				'room_id' => '1',
				'plugin_key' => 'questionnaires'
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
		if ($direction === 'down') {
			return true;
		}

		foreach ($this->records as $model => $records) {
			if (!$this->updateRecords($model, $records)) {
				return false;
			}
		}
		return true;
	}

/**
 * Update model records
 *
 * @param string $model model name to update
 * @param string $records records to be stored
 * @param string $scope ?
 * @return bool Should process continue
 */
	public function updateRecords($model, $records, $scope = null) {
		$Model = $this->generateModel($model);
		foreach ($records as $record) {
			$Model->create();
			if (!$Model->save($record, false)) {
				return false;
			}
		}

		return true;
	}
}