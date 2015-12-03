<?php
/**
 * QuestionnaireAnswerSummary Model
 *
 * @property Questionnaire $Questionnaire
 * @property User $User
 * @property QuestionnaireAnswer $QuestionnaireAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireAnswerSummary Model
 */
class QuestionnaireAnswerSummaryCsv extends QuestionnairesAppModel {

/**
 * use table
 *
 * @var array
 */
	public $useTable = 'questionnaire_answer_summaries';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
	);

/**
 * convert encoding
 *
 * @var string
 */
	protected $_toCode = 'SJIS';

/**
 * getQuestionnaireForAnswerCsv
 *
 * @param int $questionnaireKey questionnaire key
 * @return array questionnaire data
 */
	public function getQuestionnaireForAnswerCsv($questionnaireKey) {
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire', true);
		// 指定のアンケートデータを取得
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array(
				'Questionnaire.key' => $questionnaireKey,
				'Questionnaire.is_active' => true,
			),
			'recursive' => -1
		));
		return $questionnaire;
	}

/**
 * getAnswerSummaryCsv 
 *
 * @param array $questionnaire questionnaire data
 * @param int $limit record limit
 * @param int $offset offset
 * @return array
 */
	public function getAnswerSummaryCsv($questionnaire, $limit, $offset) {
		$this->QuestionnaireAnswer = ClassRegistry::init('Questionnaires.QuestionnaireAnswer', true);

		// 指定されたアンケートの回答データをＣｓｖに出力しやすい行形式で返す
		$retArray = array();

		// $offset == 0 のときのみヘッダ行を出す
		if ($offset == 0) {
			$retArray[] = $this->_putHeader($questionnaire);
		}

		// $questionnaireにはページデータ、質問データが入っていることを前提とする

		// アンケートのkeyを取得
		$key = $questionnaire['Questionnaire']['key'];

		// keyに一致するsummaryを取得（テストじゃない、完了している）
		$summaries = $this->find('all', array(
			'conditions' => array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
				'questionnaire_key' => $key,
			),
			'limit' => $limit,
			'offset' => $offset,
			'order' => 'QuestionnaireAnswerSummaryCsv.created',
		));
		if (empty($summaries)) {
			return $retArray;
		}

		// summary loop
		foreach ($summaries as $summary) {
			//$answers = $summary['QuestionnaireAnswer'];
			// 何回もSQLを発行するのは無駄かなと思いつつも
			// QuestionnaireAnswerに回答データの取り扱いしやすい形への整備機能を組み込んであるので、それを利用したかった
			// このクラスからでも利用できないかと試みたが
			// AnswerとQuestionがJOINされた形でFindしないと整備機能が発動しない
			// そうするためにはrecursive=2でないといけないわけだが、recursive=2にするとRoleのFindでSQLエラーになる
			// 仕方ないのでこの形式で処理を行う
			$answers = $this->QuestionnaireAnswer->find('all', array(
				'conditions' => array(
					'questionnaire_answer_summary_id' => $summary[$this->alias]['id']
				),
			));
			$retArray[] = $this->_getRows($questionnaire, $summary, $answers);
		}

		return $retArray;
	}

/**
 * _putHeader
 *
 * @param array $questionnaire questionnaire data
 * @return array
 */
	protected function _putHeader($questionnaire) {
		$cols = array();

		// "回答者","回答日","回数"
		$cols[] = __d('questionnaires', 'Respondent');
		$cols[] = __d('questionnaires', 'Answer Date');
		$cols[] = __d('questionnaires', 'Number');

		foreach ($questionnaire['QuestionnairePage'] as $page) {
			foreach ($page['QuestionnaireQuestion'] as $question) {
				if (QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
					$choiceSeq = 1;
					foreach ($question['QuestionnaireChoice'] as $choice) {
						if ($choice['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
							$cols[] = $page['page_sequence'] . '-' . $question['question_sequence'] . '-' . $choiceSeq++ . '. ' . $choice['choice_label'];
						}
					}
				} else {
					$cols[] = $page['page_sequence'] . '-' . $question['question_sequence'] . '. ' . $question['question_value'];
				}
			}
		}
		return array_map(array($this, 'convertCode'), $cols);
	}

/**
 * _getRow
 *
 * @param array $questionnaire questionnaire data
 * @param array $summary answer summary
 * @param array $answers answer data
 * @return array
 */
	protected function _getRows($questionnaire, $summary, $answers) {
		// ページ、質問のループから、取り出すべき質問のIDを順番に取り出す
		// question loop
		// 返却用配列にquestionのIDにマッチするAnswerを配列要素として追加、Answerがないときは空文字
		// なお選択肢系のものはchoice_idが回答にくっついているのでそれを削除する
		// MatrixのものはMatrixの行数分返却行の列を加える
		// その他の選択肢の場合は、入力されたその他のテキストを入れる

		$cols = array();

		$cols[] = ($questionnaire['Questionnaire']['is_anonymity']) ? __d('questionnaires', 'Anonymity') : $summary['TrackableCreator']['username'];
		$cols[] = $summary['QuestionnaireAnswerSummaryCsv']['modified'];
		$cols[] = $summary['QuestionnaireAnswerSummaryCsv']['answer_number'];

		foreach ($questionnaire['QuestionnairePage'] as $page) {
			foreach ($page['QuestionnaireQuestion'] as $question) {
				if (QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
					foreach ($question['QuestionnaireChoice'] as $choice) {
						if ($choice['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
							$cols[] = $this->_getMatrixAns($question, $choice, $answers);
						}
					}
				} else {
					$cols[] = $this->_getAns($question, $answers);
				}
			}
		}
		return array_map(array($this, 'convertCode'), $cols);
	}

/**
 * _getAns
 *
 * @param array $question question data
 * @param array $answers answer data
 * @return string
 */
	protected function _getAns($question, $answers) {
		$retAns = '';
		// 回答配列データの中から、現在指定された質問に該当するものを取り出す
		$ans = Hash::extract($answers, '{n}.QuestionnaireAnswer[questionnaire_question_key=' . $question['key'] . ']');
		// 回答が存在するとき処理
		if ($ans) {
			$ans = $ans[0];
			// 単純入力タイプのときは回答の値をそのまま返す
			if (QuestionnairesComponent::isOnlyInputType($question['question_type'])) {
				$retAns = $ans['answer_value'];
			} elseif (QuestionnairesComponent::isSelectionInputType($question['question_type'])) {
				// choice_id と choice_valueに分けられた回答選択肢配列を得る
				// 選択されていた数分処理
				foreach ($ans['answer_values'] as $choiceKey => $dividedAns) {
					// idから判断して、その他が選ばれていた場合、other_answer_valueを入れる
					$choice = Hash::extract($question['QuestionnaireChoice'], '{n}[key=' . $choiceKey . ']');
					if ($choice) {
						if ($choice[0]['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
							$retAns .= $ans['other_answer_value'];
						} else {
							$retAns .= $dividedAns;
						}
						$retAns .= QuestionnairesComponent::ANSWER_DELIMITER;
					}
				}
				$retAns = trim($retAns, QuestionnairesComponent::ANSWER_DELIMITER);
			}
		}
		return $retAns;
	}

/**
 * _getMatrixAns
 *
 * @param array $question question data
 * @param array $choice question choice data
 * @param array $answers answer data
 * @return string
 */
	protected function _getMatrixAns($question, $choice, $answers) {
		$retAns = '';
		// 回答配列データの中から、現在指定された質問に該当するものを取り出す
		// マトリクスタイプのときは複数存在する（行数分）
		$anss = Hash::extract($answers, '{n}.QuestionnaireAnswer[questionnaire_question_key=' . $question['key'] . ']');
		if (empty($anss)) {
			return $retAns;
		}
		// その中かから現在指定された選択肢行に該当するものを取り出す
		$ans = Hash::extract($anss, '{n}[matrix_choice_key=' . $choice['key'] . ']');
		// 回答が存在するとき処理
		if ($ans) {
			$ans = $ans[0];
			// idから判断して、その他が選ばれていた場合、other_answer_valueを入れる
			if ($choice['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				$retAns = $ans['other_answer_value'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER;
			}
			$retAns = implode(QuestionnairesComponent::ANSWER_DELIMITER, $ans['answer_values']);
		}
		return $retAns;
	}

/**
 * convertCode
 *
 * @param string $data data
 * @return string
 */
	public function convertCode($data) {
		return mb_convert_encoding($data, $this->_toCode);
	}
}
