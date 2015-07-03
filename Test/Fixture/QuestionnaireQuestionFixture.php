<?php
/**
 * QuestionnaireQuestionFixture
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for QuestionnaireQuestionFixture
 */
class QuestionnaireQuestionFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'origin_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '公開中データか否か'),
		'is_latest' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '最新編集データであるか否か'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'question_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '質問表示順'),
		'question_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '質問文', 'charset' => 'utf8'),
		'question_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'comment' => '質問タイプ | 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
'),
		'description' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '質問の補足説明|入力エリアの下部に補足的に表示される', 'charset' => 'utf8'),
		'is_require' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '回答必須フラグ | 0:不要 | 1:必須'),
		'question_type_option' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '1: 数値 | 2:日付(未実装) | 3:時刻(未実装) | 4:メール(未実装) | 5:URL(未実装) | 6:電話番号(未実装) | HTML５チェックで将来的に実装されそうなものに順次対応'),
		'is_choice_random' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 質問タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない'),
		'is_skip' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'アンケート回答のスキップ有無  0:スキップ 無し  1:スキップ有り'),
		'is_range' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '範囲設定しているか否か'),
		'min' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => '最小値　question_typeがテキストで数値タイプのときのみ有効 ', 'charset' => 'utf8'),
		'max' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => '最大値　question_typeがテキストで数値タイプのときのみ有効 ', 'charset' => 'utf8'),
		'is_result_display' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '集計結果表示をするか否か | 0:しない | 1:する'),
		'result_display_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '表示形式デファイン値が｜区切りで保存される | 0:棒グラフ（マトリクスのときは自動的に積み上げ棒グラフ） | 1:円グラフ | 2:表
'),
		'is_auto_translated' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'questionnaire_page_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),

		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_question_questionnaire_page1_idx' => array('column' => 'questionnaire_page_id', 'unique' => 0),
			'origin_id' => array('column' => 'origin_id', 'unique' => 0),
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
			'key' => 'qqq',
			'language_id' => 0,
			'origin_id' => 1,
			'is_active' => 1,
			'is_latest' => 1,
			'status' => 1,
			'question_sequence' => 1,
			'question_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'question_type' => 1,
			'description' => 'Lorem ipsum dolor sit amet',
			'is_require' => 1,
			'question_type_option' => 1,
			'is_choice_random' => 1,
			'is_skip' => 1,
			'min' => 'Lorem ipsum dolor sit amet',
			'max' => 'Lorem ipsum dolor sit amet',
			'is_result_display' => 1,
			'result_display_type' => 1,
			'is_auto_translated' => 1,
			'questionnaire_page_id' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:39:20',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:39:20'
		),
	);

}
