<?php
/**
 * QuestionnaireChoiceFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'matrix_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'マトリックスタイプの場合の行列区分 | 0:行 | 1:列'),
		'other_choice_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => 'その他欄か否か、また、その他欄の入力エリアタイプ | 0:その他欄でない | 1:テキストタイプを伴ったその他欄 | 2:テキストエリアタイプを伴ったその他欄

'),
		'choice_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '選択肢並び順'),
		'choice_label' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '\'選択肢ラベル\'', 'charset' => 'utf8'),
		'choice_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択肢の値　デフォルトでidと同じ値が入る（将来、選択肢の値を任意に設定して重みアンケができるよう）', 'charset' => 'utf8'),
		'skip_page_sequence' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'questionnairesのskip_flagがスキップ有りの時、スキップ先のページ'),
		'jump_route_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'questionnaire_questionsのis_jumpが有りのとき、分岐先のルート'),
		'graph_color' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 16, 'collate' => 'utf8_general_ci', 'comment' => 'グラフ描画時の色', 'charset' => 'utf8'),
		'questionnaire_question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_choice_questionnaire_question1_idx' => array('column' => 'questionnaire_question_id', 'unique' => 0)
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
			'id' => '2',
			'key' => 'choice_2',
			'language_id' => '2',
			'matrix_type' => '0',
			'other_choice_type' => '0',
			'choice_sequence' => 0,
			'choice_label' => 'choice label1',
			'choice_value' => 'choice label1',
			'skip_page_sequence' => null,
			'graph_color' => '#ff0000',
			'questionnaire_question_id' => '2',
		),
	);
}
