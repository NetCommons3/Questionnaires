<?php
/**
 * QuestionnaireFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuestionnaireFixture
 */
class QuestionnaireFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array();

/**
 * Record Options
 *
 * @var array
 */
	public $recordsOptions = array(
		array(
			'id' => 1,
			'is_latest' => 0,
			'is_no_member_allow' => true,
		),
		array(
			'id' => 2,
			'is_latest' => 0,
			'is_no_member_allow' => true,
		),
		array(
			'id' => 3,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 4,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 5,
			'is_key_pass_use' => true,
			'is_total_show' => false,
		),
		array(
			'id' => 6,
			'is_key_pass_use' => true,
			'is_total_show' => false,
		),
		array(
			'id' => 7,
			'is_image_authentication' => true,
			'total_show_timing' => '1',
			'total_show_start_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 8,
			'is_image_authentication' => true,
			'total_show_timing' => '1',
			'total_show_start_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 9,
			'is_latest' => 0,
			'is_repeat_allow' => false,
		),
		array(
			'id' => 10,
			'is_latest' => 0,
			'is_repeat_allow' => false,
		),
		array(
			'id' => 11,
			'is_anonymity' => true,
			'is_repeat_allow' => false,
		),
		array(
			'id' => 12,
			'is_anonymity' => true,
			'is_repeat_allow' => false,
		),
		array(
			'id' => 13,
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 14,
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 15,
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 16,
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 17,
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 18,
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 19,
			'answer_timing' => '1',
			'answer_start_period' => '1999-01-01 00:00:00',
			'answer_end_period' => '2000-01-01 00:00:00',
		),
		array(
			'id' => 20,
			'answer_timing' => '1',
			'answer_start_period' => '1999-01-01 00:00:00',
			'answer_end_period' => '2000-01-01 00:00:00',
		),
		array(
			'id' => 21,
			'answer_timing' => '1',
			'answer_start_period' => '1999-01-01 00:00:00',
			'answer_end_period' => '2000-01-01 00:00:00',
		),
		array(
			'id' => 22,
			'answer_timing' => '1',
			'answer_start_period' => '1999-01-01 00:00:00',
			'answer_end_period' => '2000-01-01 00:00:00',
		),
		array(
			'id' => 23,
			'answer_timing' => '1',
			'answer_start_period' => '1999-01-01 00:00:00',
			'answer_end_period' => '2000-01-01 00:00:00',
		),
		array(
			'id' => 24,
			'answer_timing' => '1',
			'answer_start_period' => '1999-01-01 00:00:00',
			'answer_end_period' => '2000-01-01 00:00:00',
		),
		array(
			'id' => 25,
			'is_active' => 0,
			'status' => 2, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 26,
			'is_active' => 0,
			'status' => 2, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 27,
			'is_active' => 0,
			'status' => 2, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 28,
			'is_active' => 0,
			'status' => 2, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 29,
			'is_active' => 0,
			'status' => 4, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 30,
			'is_active' => 0,
			'status' => 4, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 31,
			'key' => 'questionnaire_10',
			'is_active' => 0,
			'is_latest' => 1,
			'status' => 4, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 32,
			'key' => 'questionnaire_10',
			'is_active' => 0,
			'is_latest' => 1,
			'status' => 4, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 33,
			'key' => 'questionnaire_2',
			'is_active' => 0,
			'is_latest' => 1,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 34,
			'key' => 'questionnaire_2',
			'is_active' => 0,
			'is_latest' => 1,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 35,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 36,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 37,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 38,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
		),
		array(
			'id' => 39,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 40,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'title' => 'questionnaire_40',
			'sub_title' => 'questionnaier_40_sub',
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 41,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 42,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 43,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),
		array(
			'id' => 44,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2032-01-01 00:00:00',
			'answer_end_period' => '2033-01-01 00:00:00',
		),

		array(
			'id' => 45,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '1999-01-01 00:00:00',
			'answer_end_period' => '2000-01-01 00:00:00',
		),
		array(
			'id' => 46,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '1999-01-01 00:00:00',
			'answer_end_period' => '2000-01-01 00:00:00',
		),
		array(
			'id' => 47,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2000-01-01 00:00:00',
			'answer_end_period' => '2001-01-01 00:00:00',
		),
		array(
			'id' => 48,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2000-01-01 00:00:00',
			'answer_end_period' => '2001-01-01 00:00:00',
		),
		array(
			'id' => 49,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2000-01-01 00:00:00',
			'answer_end_period' => '2001-01-01 00:00:00',
		),
		array(
			'id' => 50,
			'is_active' => 0,
			'status' => 3, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
			'answer_timing' => '1',
			'answer_start_period' => '2000-01-01 00:00:00',
			'answer_end_period' => '2001-01-01 00:00:00',
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Questionnaires') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new QuestionnairesSchema())->tables[Inflector::tableize($this->name)];

		for ($i = 1; $i <= 50; $i = $i + 2) {
			$this->records[] = array(
				'id' => $i,
				'key' => 'questionnaire_' . strval($i + 1),
				'language_id' => '1',
				'is_origin' => false,
				'is_translation' => true,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => '1',
				'status' => 1, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
				'title' => 'questionnaire_' . strval($i + 1),
				'sub_title' => 'questionnaier_' . strval($i + 1) . '_sub',
				'answer_timing' => '0',
				'answer_start_period' => null,
				'answer_end_period' => null,
				'is_no_member_allow' => false,
				'is_anonymity' => false,
				'is_key_pass_use' => false,
				'is_repeat_allow' => true,
				'is_total_show' => true,
				'total_show_timing' => '0',
				'total_show_start_period' => null,
				'total_comment' => 'It is the result!',
				'is_image_authentication' => false,
				'is_page_random' => false,
				'thanks_content' => 'Thanks for your kindness',
				'is_open_mail_send' => false,
				'open_mail_subject' => '',
				'open_mail_body' => '',
				'is_answer_mail_send' => 0,
				'import_key' => null,
				'export_key' => null,
				'created_user' => $this->getCreatedUser($i),
				'created' => '2016-01-05 09:00:00',
				'modified_user' => $this->getCreatedUser($i),
				'modified' => '2016-01-05 09:00:00',
			);
			$this->records[] = array(
				'id' => $i + 1,
				'key' => 'questionnaire_' . strval($i + 1),
				'language_id' => '2',
				'is_origin' => true,
				'is_translation' => true,
				'is_active' => 1,
				'is_latest' => 1,
				'block_id' => '2',
				'status' => 1, //  1:公開中、2:公開申請中、3:下書き中、4:差し戻し
				'title' => 'questionnaire_' . strval($i + 1),
				'sub_title' => 'questionnaier_' . strval($i + 1) . '_sub',
				'answer_timing' => '0',
				'answer_start_period' => null,
				'answer_end_period' => null,
				'is_no_member_allow' => false,
				'is_anonymity' => false,
				'is_key_pass_use' => false,
				'is_repeat_allow' => true,
				'is_total_show' => true,
				'total_show_timing' => '0',
				'total_show_start_period' => null,
				'total_comment' => '集計結果です!',
				'is_image_authentication' => false,
				'is_page_random' => false,
				'thanks_content' => 'ありがとうございました',
				'is_open_mail_send' => false,
				'open_mail_subject' => '',
				'open_mail_body' => '',
				'is_answer_mail_send' => false,
				'import_key' => null,
				'export_key' => null,
				'created_user' => $this->getCreatedUser($i),
				'created' => '2016-01-05 09:00:00',
				'modified_user' => $this->getCreatedUser($i),
				'modified' => '2016-01-05 09:00:00',
			);
		}
		foreach ($this->records as &$record) {
			$path = '{n}[id=' . $record['id'] . ']';
			$opt = Hash::extract($this->recordsOptions, $path);
			if (! empty($opt)) {
				$record = Hash::merge($record, $opt[0]);
			}
		}
		parent::init();
	}
/**
 * page createor the fixture.
 *
 * @param int $qId questionnaire id
 * @return int
 */
	public function getCreatedUser($qId) {
		$admin = array(1, 2, 3, 4, 13, 14, 19, 20, 33, 34, 39, 40, 45, 46);
		$chief = array(5, 6, 7, 8, 15, 16, 21, 22, 25, 26, 29, 30, 35, 36, 41, 42, 47, 48);
		if (in_array($qId, $admin)) {
			return '1';
		}
		if (in_array($qId, $chief)) {
			return '3';
		}
		return '4';
	}

}
