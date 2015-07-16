<?php
/**
 * QuestionnaireAnswer Model
 *
 * @property MatrixChoice $MatrixChoice
 * @property QuestionnaireAnswerSummary $QuestionnaireAnswerSummary
 * @property QuestionnaireQuestion $QuestionnaireQuestion
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireAnswer Model
 */
class QuestionnaireAnswerValidation extends QuestionnairesAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = false;

/**
 * checkRequire
 *
 * @param array $question question
 * @param array $answer post answer data
 * @return array error message
 */
	public function checkRequire($question, $answer) {
		$errors = array();
		if ($question['is_require'] == QuestionnairesComponent::REQUIRES_REQUIRE) {
			if (empty($answer['answer_value'])) {
				$errors[] = __d('questionnaires', 'Input required');
			}
		}
		return $errors;
	}
/**
 * checkNumericType
 *
 * @param array $question question
 * @param string $answer answer value
 * @return array error message
 */
	public function checkNumericType($question, $answer) {
		$errors = array();
		// 数値タイプの場合、数値型であるか
		// 下限上限に引っかかってないか
		if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_NUMERIC) {
			$chk = Validation::numeric($answer);
			if (!$chk) {
				$errors[] = __d('questionnaires', 'Number required');
			}
		}
		return $errors;
	}

/**
 * checkDatetimeType
 *
 * @param array $question question
 * @param string $answer answer value
 * @return array error message
 */
	public function checkDatetimeType($question, $answer) {
		$errors = array();
		if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE) {
			if (!Validation::date($answer, 'ymd')) {
				$errors[] = sprintf(__d('questionnaires', 'Please enter a valid date in YY-MM-DD format.'));
			}
		} elseif ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_TIME) {
			if (!Validation::time($answer)) {
				$errors[] = sprintf(__d('questionnaires', 'Please enter the time.'));
			}
		} elseif ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE_TIME) {
			if (!Validation::datetime($answer, 'ymd')) {
				$errors[] = sprintf(__d('questionnaires', 'Please enter a valid date and time.'));
			}
		}
		return $errors;
	}

/**
 * checkRange
 *
 * @param array $question question
 * @param string $answer answer value
 * @return array error message
 */
	public function checkRange($question, $answer) {
		$errors = array();
		// 現在の機能は数値型か文字列型のみ
		if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_NUMERIC) {
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				if (!Validation::range($answer, intval($question['min']), intval($question['max']))) {
					$errors[] = sprintf(__d('questionnaires', 'Please enter the answer between %s and %s.', $question['min'], $question['max']));
				}
			}
		} else {
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				if (!Validation::minLength($answer, intval($question['min'])) || !Validation::maxLength($answer, intval($question['max']))) {
					$errors[] = sprintf(__d('questionnaires', 'Please enter the answer between %s letters and %s letters.', $question['min'], $question['max']));
				}
			}
		}
		return $errors;
	}

/**
 * checkDateRange
 *
 * @param array $question question
 * @param string $answer answer value
 * @param string $type question type
 * @return array error message
 */
	public function checkDateRange($question, $answer, $type) {
		$errors = array();
		if (!empty($question['min']) && !empty($question['max'])) {
			if ($question['question_type_option'] != QuestionnairesComponent::TYPE_OPTION_TIME) {
				if (!Validation::range(strtotime($answer), strtotime($question['min']) - 1, strtotime($question['max']) + 1)) {
					$errors[] = sprintf(__d('questionnaires', 'Please enter the %s between %s and %s.', $type, $question['min'], $question['max']));
				}
			}
		}
		return $errors;
	}

/**
 * checkAnswerInList
 *
 * @param array $question question
 * @param string $answer answer value
 * @param int $list choice list ( choice origin_id list)
 * @return array error message
 */
	public function checkAnswerInList($question, $answer, $list) {
		$errors = array();
		if ($answer != '' && !Validation::inList(strval($answer), $list)) {
			$errors[] = __d('questionnaires', 'Invalid choice');
		}
		return $errors;
	}

/**
 * checkMatrixAnswerInList
 *
 * @param array $question question
 * @param string $answers answer value
 * @param int $list choice list ( choice origin_id list)
 * @return array error message
 */
	public function checkMatrixAnswerInList($question, $answers, $list) {
		$errors = array();
		foreach ($answers as $matrixRowId => $matrixColAns) {
			if (!Validation::inList(strval($matrixRowId), $list)) {
				$errors[] = __d('questionnaires', 'Invalid choice');
			}
			$choiceIds = array_keys($matrixColAns);
			foreach ($choiceIds as $choiceId) {
				if ($choiceId != '' && !Validation::inList(strval($choiceId), $list)) {
					$errors[] = __d('questionnaires', 'Invalid choice');
				}
			}
		}
		return $errors;
	}

/**
 * checkOtherAnswer
 *
 * @param array $question question
 * @param string $answer answer value
 * @param int $choiceId selected choice
 * @param string $otherAnswer other answer string
 * @return array error message
 */
	public function checkOtherAnswer($question, $answer, $choiceId, $otherAnswer) {
		$errors = array();
		$results = Hash::extract($question['QuestionnaireChoice'], '{n}[origin_id=' . $choiceId . ']');
		if ($results && $results[0]['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
			if (empty($otherAnswer['other_answer_value'])) {
				$errors[] = __d('questionnaires', 'Please enter something, if you chose the other item');
			}
		}
		return $errors;
	}

/**
 * checkMatrixOtherAnswer
 *
 * @param array $question question
 * @param string $answers answer value
 * @param string $otherAnswer other answer string
 * @return array error message
 */
	public function checkMatrixOtherAnswer($question, $answers, $otherAnswer) {
		// このやり方だと、「その他」行がマトリクスにある時は必ず入力しなきゃいけなくなる？
		// 選択肢を何も選択しなかったらAnswerデータが飛んでこないからチェックにかからないか？
		$errors = array();
		$rowIds = array_keys($answers);
		foreach ($rowIds as $matrixRowId) {
			$results = Hash::extract($question['QuestionnaireChoice'], '{n}[origin_id=' . $matrixRowId . ']');
			if ($results && $results[0]['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				if (empty($otherAnswer['other_answer_value'])) {
					$errors[] = __d('questionnaires', 'Please enter something in other item');
				}
			}
		}
		return $errors;
	}

/**
 * checkMatrixAnswerFill
 *
 * @param array $question question
 * @param string $answer answer string
 * @param string $answers all row answer value
 * @return array error message
 */
	public function checkMatrixAnswerFill($question, $answer, $answers) {
		// マトリクスの場合は全行回答するか全行回答しないかでないと集計計算が狂うので
		// 全行回答か全行無回答かを確認している
		$errors = array();
		$answerCount = 0;
		$noAnswerCount = 0;
		foreach ($answers as $ans) {
			if (!isset($ans['id'])) {
				// id すらないのはblackhole対応のためのhidden要素であるので無視
				continue;
			}
			if ($ans['answer_value'] == '') {
				$noAnswerCount++;
			}
			$answerCount++;
		}
		if ($noAnswerCount > 0 && $noAnswerCount < $answerCount) {
			$errors[] = __d('questionnaires', 'Please answer about all rows.');
		}
		return $errors;
	}
}
