<?php
/**
 * QuestionnaireQuestionFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'question_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '質問表示順'),
		'question_value' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'question_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '質問タイプ | 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_require' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '回答必須フラグ | 0:不要 | 1:必須'),
		'question_type_option' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '1: 数値 | 2:日付(未実装) | 3:時刻(未実装) | 4:メール(未実装) | 5:URL(未実装) | 6:電話番号(未実装) | HTML５チェックで将来的に実装されそうなものに順次対応'),
		'is_choice_random' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '選択肢表示順序ランダム化 | 質問タイプが1:択一選択 2:複数選択 6:マトリクス（択一） 7:マトリクス（複数） のとき有効 ただし、６，７については行がランダムになるだけで列はランダム化されない'),
		'is_skip' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'アンケート回答のスキップ有無  0:スキップ 無し  1:スキップ有り'),
		'is_jump' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'アンケート回答の分岐'),
		'is_range' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '範囲設定しているか否か'),
		'min' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => '最小値　question_typeがテキストで数値タイプのときのみ有効 ', 'charset' => 'utf8'),
		'max' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => '最大値　question_typeがテキストで数値タイプのときのみ有効 ', 'charset' => 'utf8'),
		'is_result_display' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '集計結果表示をするか否か | 0:しない | 1:する'),
		'result_display_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => '表示形式デファイン値が｜区切りで保存される | 0:棒グラフ（マトリクスのときは自動的に積み上げ棒グラフ） | 1:円グラフ | 2:表
'),
		'questionnaire_page_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_question_questionnaire_page1_idx' => array('column' => 'questionnaire_page_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array();

/**
 * Records
 *
 * @var array
 */
	public $overwrite = array(
		4 => array('is_skip' => 1),
		6 => array(
			'question_type' => '2',
		),
		8 => array(
			'question_type' => '3',
			'is_range' => 1,
			'min' => '5',
			'max' => '15'
		),
		10 => array(
			'question_type' => '5',
		),
		12 => array(
			'question_type' => '6',
		),
		14 => array(
			'question_type' => '7',
			'question_type_option' => '2',
			'is_range' => 1,
			'min' => '2016-01-01 00:00:00',
			'max' => '2016-12-31 00:00:00'
		),
		16 => array(
			'question_type' => '7',
			'question_type_option' => '7',
		),
		18 => array(
			'question_type' => '4',
		),
	);

/**
 * insert question id
 *
 * @var array
 */
	public $questionId = 1;

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		$pId = 1;
		$sevenPages = [3];
		$threePages = [7, 11, 35, 39, 43];
		for ($questionnaireId = 1; $questionnaireId <= 50; $questionnaireId = $questionnaireId + 2) {
			$repeat = 1;
			if (in_array($questionnaireId, $threePages)) {
				$repeat = 3;
			} elseif (in_array($questionnaireId, $sevenPages)) {
				$repeat = 7;
			}
			for ($ii = 0; $ii < $repeat; $ii++) {
				$this->records[] = $this->getQuestions($pId, $questionnaireId, 1, $ii);
				$this->records[] = $this->getQuestions($pId + 1, $questionnaireId + 1, 2, $ii);
				$pId = $pId + 2;
			}
		}
		parent::init();
	}
/**
 * question the fixture.
 *
 * @param int $pId page id
 * @param int $questionnaireId questionnaire id
 * @param int $langId language id
 * @param int $pageSeq page sequence
 * @return array
 */
	public function getQuestions($pId, $questionnaireId, $langId, $pageSeq) {
		$ret = array(
			'id' => $this->questionId,
			'key' => 'qKey_' . strval(($this->questionId % 2) ? $this->questionId : $this->questionId - 1),
			'language_id' => $langId,
			'question_sequence' => 0,
			'question_value' => 'Question_1',
			'question_type' => 1, // 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
			'description' => 'It is question description',
			'is_require' => 0,
			'question_type_option' => 0, // '1: 数値 | 2:日付(未実装) | 3:時刻(未実装) | 4:メール(未実装) | 5:URL(未実装) | 6:電話番号(未実装) | HTML５チェックで将来的に実装されそうなものに順次対応'),
			'is_choice_random' => 0,
			'is_skip' => 0,
			'is_jump' => 0,
			'is_range' => 0,
			'min' => null,
			'max' => null,
			'is_result_display' => 1,
			'result_display_type' => 0,
			'questionnaire_page_id' => $pId,
			'created_user' => $this->getCreatedUser($questionnaireId),
			'created' => '2016-01-05 09:00:00',
			'modified_user' => $this->getCreatedUser($questionnaireId),
			'modified' => '2016-01-05 09:00:00',
		);
		if ($this->questionId == 4) {
			$ret['is_choice_random'] = true;
		}

		if (isset($this->overwrite[$this->questionId])) {
			$ret = Hash::merge($ret, $this->overwrite[$this->questionId]);
		}

		$this->questionId++;

		return $ret;
	}
/**
 * createor the fixture.
 *
 * @param int $qId questionnaire id
 * @return int
 */
	public function getCreatedUser($qId) {
		$admin = array(1, 2, 3, 4, 13, 14, 19, 20, 33, 34, 39, 40, 45, 46);
		$chief = array(5, 6, 7, 8, 15, 16, 21, 22, 25, 26, 29, 30, 35, 36, 41, 42, 47, 48);
		if (in_array($qId, $admin)) {
			return 1;
		}
		if (in_array($qId, $chief)) {
			return 3;
		}
		return 4;
	}

}
