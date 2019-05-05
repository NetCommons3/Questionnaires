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
App::uses('NetCommonsTime', 'NetCommons.Utility');

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
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Questionnaire' => 'Questionnaires.Questionnaire',
			'QuestionnaireAnswer' => 'Questionnaires.QuestionnaireAnswer',
		]);
	}

/**
 * getQuestionnaireForAnswerCsv
 *
 * @param int $questionnaireKey questionnaire key
 * @return array questionnaire data
 */
	public function getQuestionnaireForAnswerCsv($questionnaireKey) {
		// 指定のアンケートデータを取得
		// CSVの取得は公開してちゃんとした回答を得ているアンケートに限定である
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array(
				'Questionnaire.block_id' => Current::read('Block.id'),
				'Questionnaire.key' => $questionnaireKey,
				'Questionnaire.is_active' => true,
				'Questionnaire.language_id' => Current::read('Language.id'),
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
			'fields' => array('QuestionnaireAnswerSummaryCsv.*', 'User.handlename'),
			'conditions' => array(
				'answer_status' => QuestionnairesComponent::ACTION_ACT,
				'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
				'questionnaire_key' => $key,
			),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'LEFT',
					'conditions' => array(
						'QuestionnaireAnswerSummaryCsv.user_id = User.id',
					)
				)
			),
			'limit' => $limit,
			'offset' => $offset,
			'order' => array('QuestionnaireAnswerSummaryCsv.created ASC'),
		));
		if (empty($summaries)) {
			return $retArray;
		}

		// 質問のIDを取得
		$questionIds = [];
		foreach ($questionnaire['QuestionnairePage'] as $QuestionnairePage) {
			foreach ($QuestionnairePage['QuestionnaireQuestion'] as $question) {
				$questionIds[] = $question['id'];
			}
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
				'fields' => array('QuestionnaireAnswer.*', 'QuestionnaireQuestion.*'),
				'conditions' => array(
					'questionnaire_answer_summary_id' => $summary[$this->alias]['id'],
					'QuestionnaireQuestion.id' => $questionIds
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'questionnaire_questions',
						'alias' => 'QuestionnaireQuestion',
						'type' => 'LEFT',
						'conditions' => array(
							'QuestionnaireAnswer.questionnaire_question_key = QuestionnaireQuestion.key',
						)
					)
				)
			));
			//$retArray[] = $this->_getRows($questionnaire, $summary, $answers);
			//回答日時は固定で1なので
			$retArray[] = (new NetCommonsTime())->toUserDatetimeArray(
				$this->_getRows($questionnaire, $summary, $answers),
				array('1')
			);
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
				$pageNumber = $page['page_sequence'] + 1;
				$questionNumber = $question['question_sequence'] + 1;
				if (QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
					$choiceSeq = 1;
					foreach ($question['QuestionnaireChoice'] as $choice) {
						if ($choice['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
							$cols[] = sprintf('%d-%d-%d. %s:%s',
								$pageNumber,
								$questionNumber,
								$choiceSeq++,
								$question['question_value'],
								$choice['choice_label']);
						}
					}
				} else {
					$cols[] = sprintf('%d-%d. %s',
						$pageNumber,
						$questionNumber,
						$question['question_value']);
				}
			}
		}
		return $cols;
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

		$cols[] = $this->_getUserName($questionnaire, $summary);
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
		return $cols;
	}

/**
 * _getUserName
 *
 * @param array $questionnaire questionnaire data
 * @param array $summary answer summary
 * @return string
 */
	protected function _getUserName($questionnaire, $summary) {
		if ($questionnaire['Questionnaire']['is_anonymity']) {
			return __d('questionnaires', 'Anonymity');
		}
		if (empty($summary['User']['handlename'])) {
			return __d('questionnaires', 'Guest');
		}
		return $summary['User']['handlename'];
	}
/**
 * _getAns
 *
 * @param array $question question data
 * @param array $answers answer data
 * @return string
 *
 * 速度改善の修正に伴って発生したため抑制
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
	protected function _getAns($question, $answers) {
		$retAns = '';
		// 回答配列データの中から、現在指定された質問に該当するものを取り出す
		$ans = [];
		foreach ($answers as $answer) {
			if ($answer['QuestionnaireAnswer']['questionnaire_question_key'] === $question['key']) {
				$ans = $answer['QuestionnaireAnswer'];
				break;
			}
		}

		// 回答が存在するとき処理
		if (! $ans) {
			// 通常の処理ではこのような場面はありえない
			// アンケートは空回答であっても回答レコードを作成するからです
			// データレコード異常があった場合のみです
			// ただ、この回答を異常データだからといってオミットすると、サマリの合計数と
			// 合わなくなって集計データが狂ってしまうので空回答だったように装って処理します
			return $retAns;
		}

		// 単純入力タイプのときは回答の値をそのまま返す
		if (QuestionnairesComponent::isOnlyInputType($question['question_type'])) {
			$retAns = $ans['answer_value'];
		} elseif (QuestionnairesComponent::isSelectionInputType($question['question_type'])) {
			// choice_id と choice_valueに分けられた回答選択肢配列を得る
			// 選択されていた数分処理
			foreach ($ans['answer_values'] as $choiceKey => $dividedAns) {
				// idから判断して、その他が選ばれていた場合、other_answer_valueを入れる
				$choice = [];
				foreach ($question['QuestionnaireChoice'] as $item) {
					if ($item['key'] === $choiceKey) {
						$choice = $item;
						break;
					}
				}
				if ($choice) {
					if ($choice['other_choice_type'] !=
						QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
						$retAns .= $ans['other_answer_value'];
					} else {
						$retAns .= $dividedAns;
					}
					$retAns .= QuestionnairesComponent::ANSWER_DELIMITER;
				}
			}
			$retAns = trim($retAns, QuestionnairesComponent::ANSWER_DELIMITER);
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
		$answerArr = [];
		foreach ($answers as $answer) {
			if ($answer['QuestionnaireAnswer']['questionnaire_question_key']
				=== $question['key']) {
				$answerArr[] = $answer['QuestionnaireAnswer'];
			}
		}
		if (empty($answerArr)) {
			// 通常の処理ではこのような場面はありえない
			// アンケートは空回答であっても回答レコードを作成するからです
			// データレコード異常があった場合のみです
			// ただ、この回答を異常データだからといってオミットすると、サマリの合計数と
			// 合わなくなって集計データが狂ってしまうので空回答だったように装って処理します
			return $retAns;
		}
		// その中から現在指定された選択肢行に該当するものを取り出す
		$ans = [];
		foreach ($answerArr as $item) {
			if ($item['matrix_choice_key'] === $choice['key']) {
				$ans = $item;
				break;
			}
		}
		// 回答が存在するとき処理
		if ($ans) {
			// idから判断して、その他が選ばれていた場合、other_answer_valueを入れる
			if ($choice['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				$retAns = $ans['other_answer_value'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER;
			}
			$retAns .= implode(QuestionnairesComponent::ANSWER_DELIMITER, $ans['answer_values']);
		}
		return $retAns;
	}
}
