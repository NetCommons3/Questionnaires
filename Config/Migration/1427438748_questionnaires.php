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
				'questionnaire_answers' => array(
					'other_answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択しタイプで「その他」を選んだ場合、入力されたテキストはここに入る。', 'charset' => 'utf8', 'after' => 'answer_value'),
				),
				'questionnaire_questions' => array(
					'skip_flag' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => 'アンケート回答のスキップ有無  0:スキップ無し  1:スキップ有り', 'after' => 'choice_random_flag'),
				),
			),
			'drop_field' => array(
				'questionnaire_answers' => array('choice_id'),
				'questionnaires' => array('skip_flag'),
			),
			'alter_field' => array(
				'questionnaire_answers' => array(
					'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '回答した文字列を設定する
選択肢、リストなどの選ぶだけの場合は、選択肢のid値:ラベルを入れる

選択肢タイプで「その他」を選んだ場合は、入力されたテキストは、ここではなく、other_answer_valueに入れる。

複数選択肢
これらの場合は、(id値):(ラベル)を|つなぎで並べる。
', 'charset' => 'utf8'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'questionnaire_answers' => array('other_answer_value'),
				'questionnaire_questions' => array('skip_flag'),
			),
			'create_field' => array(
				'questionnaire_answers' => array(
					'choice_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '選択肢系列の質問に対する回答の場合、選んだ選択肢のIDを設定する | 複数選択の場合は、選択された選択肢のIDを｜でつないで設定する'),
				),
				'questionnaires' => array(
					'skip_flag' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'comment' => 'アンケート回答のスキップ有無  0:スキップ無し  1:スキップ有り'),
				),
			),
			'alter_field' => array(
				'questionnaire_answers' => array(
					'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '回答した文字列を設定する
選択肢、リストなどの選ぶだけの場合は、選択肢のラベルを入れる
選択肢タイプで「その他」を選んだ場合に限り、その他欄に入力されたテキストを設定する

複数選択肢
これらの場合は、改行コードでテキストをつないで１つにまとめて設定する
', 'charset' => 'utf8'),
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
