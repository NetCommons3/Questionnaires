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
		4 => array(
			'is_skip' => 1,
			'result_display_type' => 1,
		),
		6 => array(
			'question_type' => '2',
			'question_value' => 'Question_2',
		),
		8 => array(
			'question_type' => '3',
			'question_value' => 'Question_3',
			'is_range' => 1,
			'min' => '5',
			'max' => '15'
		),
		10 => array(
			'question_type' => '5',
			'question_value' => 'Question_4',
			'result_display_type' => 2,
		),
		12 => array(
			'question_type' => '6',
			'question_value' => 'Question_5',
			'result_display_type' => 1,
		),
		14 => array(
			'question_type' => '7',
			'question_value' => 'Question_6',
			'question_type_option' => '2',
			'is_range' => 1,
			'min' => '2016-01-01 00:00:00',
			'max' => '2016-12-31 00:00:00'
		),
		16 => array(
			'question_type' => '7',
			'question_value' => 'Question_7',
			'question_type_option' => '7',
		),
		18 => array(
			'question_type' => '4',
		),
		30 => array(
			'is_result_display' => 0,
		),
		40 => array(
			'question_type' => '8',	// single select list
			'result_display_type' => 2,	// table
		),
		42 => array(
			'question_type' => '5',	// single matrix
			'result_display_type' => 1,	// pie
		),
		44 => array(
			'question_type' => '6',	// multi matrix
			'result_display_type' => 0,	// bar
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
		require_once App::pluginPath('Questionnaires') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new QuestionnairesSchema())->tables[Inflector::tableize($this->name)];

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
			'is_origin' => ($langId == 2),
			'is_translation' => true,
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
