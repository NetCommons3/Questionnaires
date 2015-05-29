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
		'Questionnaire' => array(
			'className' => 'Questionnaire',
			'foreignKey' => 'questionnaire_origin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CreatedUser' => array(
			'className' => 'Users.UserAttributesUser',
			'foreignKey' => false,
			'conditions' => array(
				'QuestionnaireAnswerSummaryCsv.created_user = CreatedUser.user_id',
				'CreatedUser.key' => 'nickname'
			),
			'fields' => array('CreatedUser.key', 'CreatedUser.value'),
			'order' => ''
		),
		'ModifiedUser' => array(
			'className' => 'Users.UserAttributesUser',
			'foreignKey' => false,
			'conditions' => array(
				'QuestionnaireAnswerSummaryCsv.modified_user = ModifiedUser.user_id',
				'ModifiedUser.key' => 'nickname'
			),
			'fields' => array('ModifiedUser.key', 'ModifiedUser.value'),
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuestionnaireAnswer' => array(
			'className' => 'QuestionnaireAnswer',
			'foreignKey' => 'questionnaire_answer_summary_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

/**
 * getAnswerSummaryCsv 
 *
 * @param array $questionnaire questionnaire data
 * @param int $limit record limit
 * @param int $offset offset
 * @return array
 */
	public function getAnswerSummaryCsv($questionnaire, $limit, $offset) {
		// 指定されたアンケートの回答データをＣｓｖに出力しやすい行形式で返す
		$retArray = array();

		// $offset == 0 のときのみヘッダ行を出す
		if ($offset == 0) {
			$retArray[] = $this->__putHeader($questionnaire);
		}

		// $questionnaireにはページデータ、質問データが入っていることを前提とする

		// アンケートのorigin_idを取得
		$originId = $questionnaire['Questionnaire']['origin_id'];

		// origin_idに一致するsummaryを取得（テストじゃない、完了している）
		$summaries = $this->find('all', array(
			'conditions' => array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
				'questionnaire_origin_id' => $originId,
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
			$answers = $summary['QuestionnaireAnswer'];
			$retArray[] = $this->__getRows($questionnaire, $summary, $answers);
		}

		return $retArray;
	}

/**
 * __putHeader 
 *
 * @param array $questionnaire questionnaire data
 * @return array
 */
	private function __putHeader($questionnaire) {
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
		return $cols;
	}

/**
 * __getRow 
 *
 * @param array $questionnaire questionnaire data
 * @param array $summary answer summary
 * @param array $answers answer data
 * @return array
 */
	private function __getRows($questionnaire, $summary, $answers) {
		// ページ、質問のループから、取り出すべき質問のIDを順番に取り出す
		// question loop
		// 返却用配列にquestionのIDにマッチするAnswerを配列要素として追加、Answerがないときは空文字
		// なお選択肢系のものはchoice_idが回答にくっついているのでそれを削除する
		// MatrixのものはMatrixの行数分返却行の列を加える
		// その他の選択肢の場合は、入力されたその他のテキストを入れる

		$cols = array();

		$cols[] = ($questionnaire['Questionnaire']['is_anonymity']) ? __d('questionnaires', 'Anonymity') : $summary['CreatedUser']['value'];
		$cols[] = $summary['QuestionnaireAnswerSummaryCsv']['modified'];
		$cols[] = $summary['QuestionnaireAnswerSummaryCsv']['answer_number'];

		foreach ($questionnaire['QuestionnairePage'] as $page) {
			foreach ($page['QuestionnaireQuestion'] as $question) {
				if (QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
					foreach ($question['QuestionnaireChoice'] as $choice) {
						if ($choice['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
							$cols[] = $this->__getMatrixAns($question, $choice, $answers);
						}
					}
				} else {
					$cols[] = $this->__getAns($question, $answers);
				}
			}
		}
		return $cols;
	}

/**
 * __getAns
 *
 * @param array $question question data
 * @param array $answers answer data
 * @return string
 */
	private function __getAns($question, $answers) {
		$retAns = '';
		// 回答配列データの中から、現在指定された質問に該当するものを取り出す
		$ans = Hash::extract($answers, '{n}[questionnaire_question_origin_id=' . $question['origin_id'] . ']');
		// 回答が存在するとき処理
		if ($ans) {
			$ans = $ans[0];
			// 単純入力タイプのときは回答の値をそのまま返す
			if (QuestionnairesComponent::isOnlyInputType($question['question_type'])) {
				$retAns = $ans['answer_value'];
			} elseif (QuestionnairesComponent::isSelectionInputType($question['question_type'])) {
				// 選択肢タイプのときは、回答データに選択肢IDとか、複数選ばれていたりとかあるので加工が必要
				// 回答を取り出す
				// choice_id と choice_valueに分けられた回答選択肢配列を得る
				$dividedAnsArray = QuestionnairesComponent::getChoiceValueFromAnswerForSelection($ans['answer_value']);
				// 選択されていた数分処理
				foreach ($dividedAnsArray as $dividedAns) {
					// idから判断して、その他が選ばれていた場合、other_answer_valueを入れる
					$choice = Hash::extract($question['QuestionnaireChoice'], '{n}[origin_id=' . $dividedAns[0] . ']');
					if ($choice) {
						if ($choice[0]['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
							$retAns .= $ans['other_answer_value'];
						} else {
							$retAns .= $dividedAns[1];
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
 * __getMatrixAns
 *
 * @param array $question question data
 * @param array $choice question choice data
 * @param array $answers answer data
 * @return string
 */
	private function __getMatrixAns($question, $choice, $answers) {
		$retAns = '';
		// 回答配列データの中から、現在指定された質問に該当するものを取り出す
		// マトリクスタイプのときは複数存在する（行数分）
		$anss = Hash::extract($answers, '{n}[questionnaire_question_origin_id=' . $question['origin_id'] . ']');
		if (empty($anss)) {
			return $retAns;
		}
		// その中かから現在指定された選択肢行に該当するものを取り出す
		$ans = Hash::extract($anss, '{n}[matrix_choice_id=' . $choice['origin_id'] . ']');
		// 回答が存在するとき処理
		if ($ans) {
			$ans = $ans[0];
			// 選択肢タイプのときは、回答データに選択肢IDとか、複数選ばれていたりとかあるので加工が必要
			// 回答を取り出す
			// choice_id と choice_valueに分けられた回答選択肢配列を得る
			$dividedAnsArray = QuestionnairesComponent::getChoiceValueFromAnswerForSelection($ans['answer_value']);

			// 回答値の先頭に行側の値を入れる。
			// idから判断して、その他が選ばれていた場合、other_answer_valueを入れる
			if ($choice['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				$retAns = $ans['other_answer_value'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER;
			} else {
				$retAns = $choice['choice_label'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER;
			}
			// 選択されていた数分処理
			foreach ($dividedAnsArray as $dividedAns) {
				if (isset($dividedAns[1])) {
					$retAns .= $dividedAns[1];
				} else {
					$retAns .= '';
				}
				$retAns .= QuestionnairesComponent::ANSWER_DELIMITER;
			}
			$retAns = trim($retAns, QuestionnairesComponent::ANSWER_DELIMITER);
		}
		return $retAns;
	}

}
