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
class AddHistoryLogic extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_history_logic';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'questionnaire_blocks_settings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
					'block_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'fk_questionnaire_blocks_settings_blocks1_idx' => array('column' => 'block_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'questionnaire_answer_summaries' => array(
					'questionnaire_origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'after' => 'answer_time'),
					'indexes' => array(
						'fk_questionnaire_answer_summaries_questionnaires1_idx' => array('column' => 'questionnaire_origin_id', 'unique' => 0),
					),
				),
				'questionnaire_answers' => array(
					'questionnaire_question_origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'after' => 'questionnaire_answer_summary_id'),
					'indexes' => array(
						'fk_questionnaire_answers_questionnaire_questions1_idx' => array('column' => 'questionnaire_question_origin_id', 'unique' => 0),
					),
				),
				'questionnaire_choices' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | ', 'after' => 'id'),
					'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = root_id)である場合、現時点での公開されているレコードのIDが入る', 'after' => 'origin_id'),
					'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'after' => 'active_id'),
					'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'skip_page_sequence'),
					'indexes' => array(
						'origin_id' => array('column' => 'origin_id', 'unique' => 0),
						'active_id' => array('column' => 'active_id', 'unique' => 0),
						'latest_id' => array('column' => 'latest_id', 'unique' => 0),
					),
				),
				'questionnaire_frame_display_questionnaires' => array(
					'questionnaire_origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'after' => 'questionnaire_frame_setting_id'),
					'indexes' => array(
						'questionnaire_origin_id' => array('column' => 'questionnaire_origin_id', 'unique' => 0),
					),
				),
				'questionnaire_pages' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | ', 'after' => 'id'),
					'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = root_id)である場合、現時点での公開されているレコードのIDが入る', 'after' => 'origin_id'),
					'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'after' => 'active_id'),
					'questionnaire_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'after' => 'latest_id'),
					'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'page_sequence'),
					'indexes' => array(
						'origin_id' => array('column' => 'origin_id', 'unique' => 0),
						'active_id' => array('column' => 'active_id', 'unique' => 0),
						'latest_id' => array('column' => 'latest_id', 'unique' => 0),
						'fk_questionnaire_pages_questionnaires1_idx' => array('column' => 'questionnaire_id', 'unique' => 0),
					),
				),
				'questionnaire_questions' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | ', 'after' => 'id'),
					'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = root_id)である場合、現時点での公開されているレコードのIDが入る', 'after' => 'origin_id'),
					'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'after' => 'active_id'),
					'is_require' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '回答必須フラグ | 0:不要 | 1:必須', 'after' => 'description'),
					'is_choice_random' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 質問タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない', 'after' => 'question_type_option'),
					'is_skip' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'アンケート回答のスキップ有無  0:スキップ 無し  1:スキップ有り', 'after' => 'is_choice_random'),
					'is_result_display' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '集計結果表示をするか否か | 0:しない | 1:する', 'after' => 'max'),
					'is_auto_translated' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'result_display_type'),
					'indexes' => array(
						'active_id' => array('column' => 'active_id', 'unique' => 0),
						'origin_id' => array('column' => 'origin_id', 'unique' => 0),
						'latest_id' => array('column' => 'latest_id', 'unique' => 0),
					),
				),
				'questionnaires' => array(
					'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | ', 'after' => 'id'),
					'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = origin_id)である場合、現時点での公開されているレコードのIDが入る', 'after' => 'origin_id'),
					'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'after' => 'active_id'),
					'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => 'public status, 1: public, 2: public pending, 3: draft during 4: remand | 公開状況  1:公開中、2:公開申請中、3:下書き中、4:差し戻し |', 'after' => 'block_id'),
					'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケートタイトル', 'charset' => 'utf8', 'after' => 'status'),
					'sub_title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケートサブタイトル', 'charset' => 'utf8', 'after' => 'title'),
					'is_period' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '期間設定フラグ | 0:期間設定なし| 1:期間設定あり', 'after' => 'sub_title'),
					'start_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アンケート開始日時 | 画面表示時、ここがNULLの場合はDefaultで現在日時が設定される', 'after' => 'is_period'),
					'end_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アンケート回答締切日時| 画面表示時、ここがNULLの場合はDefaultで開始日時＋1Monthが設定される', 'after' => 'start_period'),
					'is_no_member_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '非会員の回答を許可するか | 0:許可しない | 1:許可する', 'after' => 'end_period'),
					'is_anonymity' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '会員回答であっても匿名扱いとするか否か | 0:非匿名 | 1:匿名', 'after' => 'is_no_member_allow'),
					'is_key_pass_use' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'キーフレーズによる回答ガードを設けるか | 0:キーフレーズガードは用いない | 1:キーフレーズガードを用いる', 'after' => 'is_anonymity'),
					'key_phrase' => array('type' => 'string', 'null' => true, 'default' => 'NetCommons', 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'キーフレーズ', 'charset' => 'utf8', 'after' => 'is_key_pass_use'),
					'is_repeate_allow' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '繰り返し回答を許可するか | 0:許可しない | 1:許可する', 'after' => 'key_phrase'),
					'is_total_show' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '集計結果を表示するか否か | 0:表示しない | 1:表示する', 'after' => 'is_repeate_allow'),
					'total_show_timing' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 4, 'comment' => '集計結果を表示するタイミング | 0:アンケート回答後、すぐ | 1:期間設定', 'after' => 'is_total_show'),
					'total_show_start_peirod' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '集計結果表示開始日時 total_show_timingが1のとき有効 画面表示時、NULLの場合、自動的に回答締切日時が設定される（回答締切がない場合は現在日時）', 'after' => 'total_show_timing'),
					'total_comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '集計表示ページの先頭に書くメッセージコメント', 'charset' => 'utf8', 'after' => 'total_show_start_peirod'),
					'is_image_authentication' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'SPAMガード項目を表示するか否か | 0:表示しない | 1:表示する', 'after' => 'total_comment'),
					'thanks_content' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート最後に表示するお礼メッセージ', 'charset' => 'utf8', 'after' => 'is_image_authentication'),
					'is_open_mail_send' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'アンケート開始メールを送信するか(現在未使用) | 0:しない | 1:する', 'after' => 'thanks_content'),
					'open_mail_subject' => array('type' => 'string', 'null' => true, 'default' => 'Questionnaire to you has arrived', 'collate' => 'utf8_general_ci', 'comment' => 'アンケート開始メールタイトル(現在未使用)', 'charset' => 'utf8', 'after' => 'is_open_mail_send'),
					'open_mail_body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート開始通知メール本文(現在未使用)', 'charset' => 'utf8', 'after' => 'open_mail_subject'),
					'is_answer_mail_send' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'アンケート回答時に編集者、編集長にメールで知らせるか否か | 0:知らせない| 1:知らせる
', 'after' => 'open_mail_body'),
					'is_page_random' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'ページ表示順序ランダム化（※将来機能）
選択肢分岐機能との兼ね合いを考えなくてはならないため、現時点での機能盛り込みは見送る', 'after' => 'is_answer_mail_send'),
					'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'is_page_random'),
					'indexes' => array(
						'origin_id' => array('column' => 'origin_id', 'unique' => 0),
						'active_id' => array('column' => 'active_id', 'unique' => 0),
						'latest_id' => array('column' => 'latest_id', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'questionnaire_answer_summaries' => array('questionnaire_id', 'indexes' => array('fk_questionnaire_answer_summaries_questionnaires1_idx')),
				'questionnaire_answers' => array('questionnaire_question_id', 'indexes' => array('fk_questionnaire_answer_questionnaire_question1_idx', 'fk_questionnaire_answer_questionnaire_choice1_idx')),
				'questionnaire_frame_display_questionnaires' => array('questionnaire_id', 'indexes' => array('fk_questionnaire_frame_display_questionnaires_questionnaire_idx1')),
				'questionnaire_pages' => array('questionnaire_entity_id', 'indexes' => array('fk_questionnaire_page_questionnnaire1_idx')),
				'questionnaire_questions' => array('require_flag', 'choice_random_flag', 'skip_flag', 'result_display_flag'),
				'questionnaires' => array('questionnaire_status'),
			),
			'alter_field' => array(
				'questionnaire_answer_summaries' => array(
					'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート回答した時のセッション値を保存します。', 'charset' => 'utf8'),
					'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'ログイン後、アンケートに回答した人のusersテーブルのid。未ログインの場合NULL'),
				),
				'questionnaire_answers' => array(
					'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '回答した文字列を設定する
選択肢、リストなどの選ぶだけの場合は、選択肢のid値:ラベルを入れる

選択肢タイプで「その他」を選んだ場合は、入力されたテキストは、ここではなく、other_answer_valueに入れる。

複数選択肢
これらの場合は、(id値):(ラベル)を|つなぎで並べる。
', 'charset' => 'utf8'),
					'other_answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択しタイプで「その他」を選んだ場合、入力されたテキストはここに入る。', 'charset' => 'utf8'),
				),
				'questionnaire_choices' => array(
					'matrix_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => 'マトリックスタイプの場合の行列区分 | 0:行 | 1:列'),
					'other_choice_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'comment' => 'その他欄か否か、また、その他欄の入力エリアタイプ | 0:その他欄でない | 1:テキストタイプを伴ったその他欄 | 2:テキストエリアタイプを伴ったその他欄

'),
					'choice_label' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '\'選択肢ラベル\'', 'charset' => 'utf8'),
					'choice_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択肢の値　デフォルトでidと同じ値が入る（将来、選択肢の値を任意に設定して重みアンケができるよう）', 'charset' => 'utf8'),
					'skip_page_sequence' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'questionnairesのskip_flagがスキップ有りの時、スキップ先のページ'),
				),
				'questionnaire_pages' => array(
					'page_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'ページ表示順'),
				),
				'questionnaire_questions' => array(
					'question_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '質問表示順'),
					'question_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'comment' => '質問タイプ | 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
'),
				),
			),
			'drop_table' => array(
				'questionnaire_entities', 'questionnaire_i18n'
			),
		),
		'down' => array(
			'drop_table' => array(
			),
			'drop_field' => array(
				'questionnaire_answer_summaries' => array('questionnaire_origin_id', 'indexes' => array('fk_questionnaire_answer_summaries_questionnaires1_idx')),
				'questionnaire_answers' => array('questionnaire_question_origin_id', 'indexes' => array('fk_questionnaire_answers_questionnaire_questions1_idx')),
				'questionnaire_choices' => array('origin_id', 'active_id', 'latest_id', 'is_auto_translated', 'indexes' => array('origin_id', 'active_id', 'latest_id')),
				'questionnaire_frame_display_questionnaires' => array('questionnaire_origin_id', 'indexes' => array('questionnaire_origin_id')),
				'questionnaire_pages' => array('origin_id', 'active_id', 'latest_id', 'questionnaire_id', 'is_auto_translated', 'indexes' => array('origin_id', 'active_id', 'latest_id', 'fk_questionnaire_pages_questionnaires1_idx')),
				'questionnaire_questions' => array('origin_id', 'active_id', 'latest_id', 'is_require', 'is_choice_random', 'is_skip', 'is_result_display', 'is_auto_translated', 'indexes' => array('active_id', 'origin_id', 'latest_id')),
				'questionnaires' => array('origin_id', 'active_id', 'latest_id', 'status', 'title', 'sub_title', 'is_period', 'start_period', 'end_period', 'is_no_member_allow', 'is_anonymity', 'is_key_pass_use', 'key_phrase', 'is_repeate_allow', 'is_total_show', 'total_show_timing', 'total_show_start_peirod', 'total_comment', 'is_image_authentication', 'thanks_content', 'is_open_mail_send', 'open_mail_subject', 'open_mail_body', 'is_answer_mail_send', 'is_page_random', 'is_auto_translated', 'indexes' => array('origin_id', 'active_id', 'latest_id')),
			),
			'create_field' => array(
				'questionnaire_answer_summaries' => array(
					'questionnaire_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_answer_summaries_questionnaires1_idx' => array(),
					),
				),
				'questionnaire_answers' => array(
					'questionnaire_question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_answer_questionnaire_question1_idx' => array('column' => 'questionnaire_question_id', 'unique' => 0),
						'fk_questionnaire_answer_questionnaire_choice1_idx' => array('column' => 'matrix_choice_id', 'unique' => 0),
					),
				),
				'questionnaire_frame_display_questionnaires' => array(
					'questionnaire_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_frame_display_questionnaires_questionnaire_idx1' => array('column' => 'questionnaire_id', 'unique' => 0),
					),
				),
				'questionnaire_pages' => array(
					'questionnaire_entity_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'fk_questionnaire_page_questionnnaire1_idx' => array('column' => 'questionnaire_entity_id', 'unique' => 0),
					),
				),
				'questionnaire_questions' => array(
					'require_flag' => array('type' => 'boolean', 'null' => true, 'default' => null, 'comment' => '回答必須フラグ | 0:不要 | 1:必須'),
					'choice_random_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 質問タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない'),
					'skip_flag' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'result_display_flag' => array('type' => 'boolean', 'null' => true, 'default' => null, 'comment' => '集計結果表示をするか否か | 0:しない | 1:する'),
				),
				'questionnaires' => array(
					'questionnaire_status' => array('type' => 'integer', 'null' => true, 'default' => null),
				),
			),
			'alter_field' => array(
				'questionnaire_answer_summaries' => array(
					'session_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'user_id' => array('type' => 'integer', 'null' => true, 'default' => null),
				),
				'questionnaire_answers' => array(
					'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '回答した文字列を設定する
選択肢、リストなどの選ぶだけの場合は、選択肢のラベルを入れる
選択肢タイプで「その他」を選んだ場合に限り、その他欄に入力されたテキストを設定する

複数選択肢
これらの場合は、改行コードでテキストをつないで１つにまとめて設定する
', 'charset' => 'utf8'),
					'other_answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'questionnaire_choices' => array(
					'matrix_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'comment' => 'マトリックスタイプの場合の行列区分 | 0:行 | 1:列'),
					'other_choice_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'comment' => 'その他欄か否か、また、その他欄の入力エリアタイプ | 0:その他欄でない | 1:テキストタイプを伴ったその他欄 | 2:テキストエリアタイプを伴ったその他欄

'),
					'choice_label' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'choice_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択肢ラベル', 'charset' => 'utf8'),
					'skip_page_sequence' => array('type' => 'integer', 'null' => true, 'default' => null),
				),
				'questionnaire_pages' => array(
					'page_sequence' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'ページ表示順'),
				),
				'questionnaire_questions' => array(
					'question_sequence' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '質問表示順'),
					'question_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '質問タイプ | 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻
'),
				),
			),
			'create_table' => array(
				'questionnaire_entities' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
					'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => 'public status, 1: public, 2: public pending, 3: draft during 4: remand | 公開状況  1:公開中、2:公開申請中、3:下書き中、4:差し戻し |'),
					'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケートタイトル', 'charset' => 'utf8'),
					'sub_title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケートサブタイトル', 'charset' => 'utf8'),
					'period_flag' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'start_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アンケート開始日時 | 画面表示時、ここがNULLの場合はDefaultで現在日時が設定される'),
					'end_period' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アンケート回答締切日時| 画面表示時、ここがNULLの場合はDefaultで開始日時＋1Monthが設定される'),
					'no_member_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '非会員の回答を許可するか | 0:許可しない | 1:許可する'),
					'anonymity_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '会員回答であっても匿名扱いとするか否か | 0:非匿名 | 1:匿名'),
					'key_pass_use_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'キーフレーズによる回答ガードを設けるか | 0:キーフレーズガードは用いない | 1:キーフレーズガードを用いる'),
					'key_phrase' => array('type' => 'string', 'null' => true, 'default' => 'NetCommons', 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'キーフレーズ', 'charset' => 'utf8'),
					'repeate_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '繰り返し回答を許可するか | 0:許可しない | 1:許可する'),
					'total_show_flag' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '集計結果を表示するか否か | 0:表示しない | 1:表示する'),
					'total_show_timing' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 4, 'comment' => '集計結果を表示するタイミング | 0:アンケート回答後、すぐ | 1:期間設定'),
					'total_show_start_peirod' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '集計結果表示開始日時 total_show_timingが1のとき有効 画面表示時、NULLの場合、自動的に回答締切日時が設定される（回答締切がない場合は現在日時）'),
					'total_comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '集計表示ページの先頭に書くメッセージコメント', 'charset' => 'utf8'),
					'image_authentication_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'SPAMガード項目を表示するか否か | 0:表示しない | 1:表示する'),
					'thanks_content' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート最後に表示するお礼メッセージ', 'charset' => 'utf8'),
					'open_mail_send_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'アンケート開始メールを送信するか(現在未使用) | 0:しない | 1:する'),
					'open_mail_subject' => array('type' => 'string', 'null' => true, 'default' => 'Questionnaire to you has arrived', 'collate' => 'utf8_general_ci', 'comment' => 'アンケート開始メールタイトル(現在未使用)', 'charset' => 'utf8'),
					'open_mail_body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アンケート開始通知メール本文(現在未使用)', 'charset' => 'utf8'),
					'answer_mail_send_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'アンケート回答時に編集者、編集長にメールで知らせるか否か | 0:知らせない| 1:知らせる
'),
					'page_random_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'ページ表示順序ランダム化（※将来機能）
選択肢分岐機能との兼ね合いを考えなくてはならないため、現時点での機能盛り込みは見送る'),
					'questionnaire_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'fk_questionnaire_histories_questionnaires1_idx' => array('column' => 'questionnaire_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'questionnaire_i18n' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
					'locale' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 6, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'index'),
					'field' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'content' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'locale' => array('column' => 'locale', 'unique' => 0),
						'model' => array('column' => 'model', 'unique' => 0),
						'row_id' => array('column' => 'foreign_key', 'unique' => 0),
						'field' => array('column' => 'field', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
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
