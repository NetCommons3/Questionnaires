<?php
/**
 * QuestionnaireChoiceFixture
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for QuestionnaireChoiceFixture
 */
class QuestionnaireChoiceFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'origin_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードの元となったレコードのID | このレコード自身が最初に作られたものである場合、idと同じ | '),
		'active_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index', 'comment' => 'このレコードがオリジナルレコード(id = root_id)である場合、現時点での公開されているレコードのIDが入る'),
		'latest_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'questionnaire_question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'matrix_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => 'マトリックスタイプの場合の行列区分 | 0:行 | 1:列'),
		'other_choice_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'comment' => 'その他欄か否か、また、その他欄の入力エリアタイプ | 0:その他欄でない | 1:テキストタイプを伴ったその他欄 | 2:テキストエリアタイプを伴ったその他欄

'),
		'choice_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '選択肢並び順'),
		'choice_label' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '\'選択肢ラベル\'', 'charset' => 'utf8'),
		'choice_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択肢の値　デフォルトでidと同じ値が入る（将来、選択肢の値を任意に設定して重みアンケができるよう）', 'charset' => 'utf8'),
		'graph_color' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 16, 'collate' => 'utf8_general_ci', 'comment' => 'グラフ描画時の色', 'charset' => 'utf8'),
		'skip_page_sequence' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'questionnairesのskip_flagがスキップ有りの時、スキップ先のページ'),
		'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_choice_questionnaire_question1_idx' => array('column' => 'questionnaire_question_id', 'unique' => 0),
			'origin_id' => array('column' => 'origin_id', 'unique' => 0),
			'active_id' => array('column' => 'active_id', 'unique' => 0),
			'latest_id' => array('column' => 'latest_id', 'unique' => 0)
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
			'origin_id' => 1,
			'active_id' => 1,
			'latest_id' => 1,
			'questionnaire_question_id' => 1,
			'matrix_type' => 1,
			'other_choice_type' => 1,
			'choice_sequence' => 1,
			'choice_label' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'choice_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'graph_color' => 'Lorem ipsum do',
			'skip_page_sequence' => 1,
			'is_auto_translated' => 1,
			'created_user' => 1,
			'created' => '2015-04-13 06:40:01',
			'modified_user' => 1,
			'modified' => '2015-04-13 06:40:01'
		),
	);

}
