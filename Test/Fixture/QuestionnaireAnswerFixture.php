<?php
/**
 * QuestionnaireAnswerFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'matrix_choice_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '回答した文字列を設定する|選択肢、リストなどの選ぶだけの場合は、選択肢のid値:ラベルを入れる|選択肢タイプで「その他」を選んだ場合は、入力されたテキストは、ここではなく、other_answer_valueに入れる。|複数選択肢
これらの場合は、(id値):(ラベル)を|つなぎで並べる。', 'charset' => 'utf8'),
		'other_answer_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '選択しタイプで「その他」を選んだ場合、入力されたテキストはここに入る。', 'charset' => 'utf8'),
		'questionnaire_answer_summary_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'questionnaire_question_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_answer_questionnaire_answer_summary1_idx' => array('column' => 'questionnaire_answer_summary_id', 'unique' => 0)
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
			'answer_value' => 'choice label26',
			'questionnaire_answer_summary_id' => 1,
			'questionnaire_question_key' => 'qKey_25'
		),
		array(
			'id' => 2,
			'answer_value' => 'choice label26',
			'questionnaire_answer_summary_id' => 2,
			'questionnaire_question_key' => 'qKey_25'
		),
		array(
			'id' => 3,
			'answer_value' => '|choice_32:choice label32',
			'questionnaire_answer_summary_id' => 8,
			'questionnaire_question_key' => 'qKey_39'
		),
		array(
			'id' => 4,
			'answer_value' => '|choice_35:choice label35',
			'questionnaire_answer_summary_id' => 9,
			'questionnaire_question_key' => 'qKey_41',
			'matrix_choice_key' => 'choice_33'
		),
		array(
			'id' => 5,
			'answer_value' => '|choice_35:choice label35',
			'questionnaire_answer_summary_id' => 9,
			'questionnaire_question_key' => 'qKey_41',
			'matrix_choice_key' => 'choice_34'
		),
		array(
			'id' => 6,
			'answer_value' => '|choice_39:choice label39',
			'questionnaire_answer_summary_id' => 10,
			'questionnaire_question_key' => 'qKey_43',
			'matrix_choice_key' => 'choice_37'
		),
		array(
			'id' => 7,
			'answer_value' => '|choice_39:choice label39',
			'questionnaire_answer_summary_id' => 10,
			'questionnaire_question_key' => 'qKey_43',
			'matrix_choice_key' => 'choice_38'
		),
		array(
			'id' => 8,
			'answer_value' => '|choice_12:choice label12',
			'questionnaire_answer_summary_id' => 11,
			'questionnaire_question_key' => 'qKey_9',
			'matrix_choice_key' => 'choice_9'
		),
		array(
			'id' => 9,
			'answer_value' => '|choice_12:choice label12',
			'questionnaire_answer_summary_id' => 11,
			'questionnaire_question_key' => 'qKey_9',
			'matrix_choice_key' => 'choice_10'
		),
		array(
			'id' => 10,
			'answer_value' => '|choice_27:choice label27',
			'questionnaire_answer_summary_id' => 12,
			'questionnaire_question_key' => 'qKey_27',
			'matrix_choice_key' => ''
		),
		array(
			'id' => 11,
			'answer_value' => '|choice_29:choice label29',
			'questionnaire_answer_summary_id' => 12,
			'questionnaire_question_key' => 'qKey_29',
			'matrix_choice_key' => ''
		),
		array(
			'id' => 12,
			'answer_value' => '|choice_31:choice label31',
			'questionnaire_answer_summary_id' => 12,
			'questionnaire_question_key' => 'qKey_31',
			'matrix_choice_key' => ''
		),
	);
}
