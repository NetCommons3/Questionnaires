<?php
/**
 * QuestionnaireFrameSettingFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuestionnaireFrameSettingFixture
 */
class QuestionnaireFrameSettingFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'display_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '0:単一表示(default)|1:リスト表示'),
		'display_num_per_page' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'comment' => 'リスト表示の場合、１ページ当たりに表示するアンケート件数'),
		'sort_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '表示並び順 0:新着順 1:回答期間順（降順） 2:アンケートステータス順（昇順） 3:タイトル順（昇順）'),
		'frame_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_frame_settings_frames1_idx' => array('column' => 'frame_key', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'display_type' => QuestionnairesComponent::DISPLAY_TYPE_SINGLE,
			'display_num_per_page' => 1,
			'sort_type' => QuestionnairesComponent::QUESTIONNAIRE_SORT_CREATED,
			'frame_key' => 'frame_1',
			'created_user' => 1,
			'created' => '2015-02-03 06:08:23',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:08:23'
		),
		array(
			'id' => 2,
			'display_type' => 1,
			'display_num_per_page' => 1,
			'sort_type' => QuestionnairesComponent::QUESTIONNAIRE_SORT_MODIFIED,
			'frame_key' => 'frame_2',
			'created_user' => 1,
			'created' => '2015-02-03 06:08:23',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:08:23'
		),
		array(
			'id' => 3,
			'display_type' => 1,
			'display_num_per_page' => 1,
			'sort_type' => QuestionnairesComponent::QUESTIONNAIRE_SORT_TITLE,
			'frame_key' => 'frame_3',
			'created_user' => 1,
			'created' => '2015-02-03 06:08:23',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:08:23'
		),
		array(
			'id' => 4,
			'display_type' => 1,
			'display_num_per_page' => 1,
			'sort_type' => QuestionnairesComponent::QUESTIONNAIRE_SORT_END,
			'frame_key' => 'frame_4',
			'created_user' => 1,
			'created' => '2015-02-03 06:08:23',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:08:23'
		),
		array(
			'id' => 10,
			'display_type' => QuestionnairesComponent::DISPLAY_TYPE_SINGLE,
			'display_num_per_page' => 1,
			'sort_type' => QuestionnairesComponent::QUESTIONNAIRE_SORT_CREATED,
			'frame_key' => 'frame_10',
			'created_user' => 1,
			'created' => '2015-02-03 06:08:23',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:08:23'
		),
	);

}
