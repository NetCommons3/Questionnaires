<?php
/**
 * QuestionnaireAnswerFixture
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for QuestionnaireAnswerFixture
 */
class QuestionnaireAnswerFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'matrix_choice_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index', 'comment' => 'マトリクスの質問の場合のみ、選択肢IDを設定する | 選択肢IDはマトリクスの行側の選択肢のIDを入れる'),
		'choice_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '選択肢系列の質問に対する回答の場合、選んだ選択肢のIDを設定する | 複数選択の場合は、選択された選択肢のIDを｜でつないで設定する'),
		'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '回答した文字列を設定する
選択肢、リストなどの選ぶだけの場合は、選択肢のラベルを入れる
選択肢タイプで「その他」を選んだ場合に限り、その他欄に入力されたテキストを設定する

複数選択肢
これらの場合は、改行コードでテキストをつないで１つにまとめて設定する
', 'charset' => 'utf8'),
		'questionnaire_answer_summary_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'questionnaire_question_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_answer_questionnaire_answer_summary1_idx' => array('column' => 'questionnaire_answer_summary_id', 'unique' => 0),
			'fk_questionnaire_answer_questionnaire_question1_idx' => array('column' => 'questionnaire_question_id', 'unique' => 0),
			'fk_questionnaire_answer_questionnaire_choice1_idx' => array('column' => 'matrix_choice_id', 'unique' => 0)
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
			'matrix_choice_id' => 1,
			'choice_id' => 1,
			'answer_value' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_id' => 1,
			'created_user' => 1,
			'created' => '2015-02-03 06:11:55',
			'modified_user' => 1,
			'modified' => '2015-02-03 06:11:55'
		),
	);

}
