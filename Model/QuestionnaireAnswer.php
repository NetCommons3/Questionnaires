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
class QuestionnaireAnswer extends QuestionnairesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Questionnaires.QuestionnaireAnswerSingleChoice',
		'Questionnaires.QuestionnaireAnswerMultipleChoice',
		'Questionnaires.QuestionnaireAnswerSingleList',
		'Questionnaires.QuestionnaireAnswerTextArea',
		'Questionnaires.QuestionnaireAnswerText',
		'Questionnaires.QuestionnaireAnswerMatrixSingleChoice',
		'Questionnaires.QuestionnaireAnswerMatrixMultipleChoice',
		'Questionnaires.QuestionnaireAnswerDatetime',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'QuestionnaireChoice' => array(
			'className' => 'Questionnaires.QuestionnaireChoice',
			'foreignKey' => false,
			'conditions' => 'QuestionnaireAnswer.matrix_choice_key=QuestionnaireChoice.key',
			'fields' => '',
			'order' => ''
		),
		'QuestionnaireAnswerSummary' => array(
			'className' => 'Questionnaires.QuestionnaireAnswerSummary',
			'foreignKey' => 'questionnaire_answer_summary_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'QuestionnaireQuestion' => array(
			'className' => 'Questionnaires.QuestionnaireQuestion',
			'foreignKey' => false,
			'conditions' => 'QuestionnaireAnswer.questionnaire_question_key=QuestionnaireQuestion.key',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		// option情報取り出し
		$summaryId = $options['questionnaire_answer_summary_id'];
		$this->data['QuestionnaireAnswer']['questionnaire_answer_summary_id'] = $summaryId;
		$question = $options['question'];
		$allAnswers = $options['allAnswers'];

		// Answerモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'questionnaire_answer_summary_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					'allowEmpty' => true,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'questionnaire_question_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					//'message' => 'Your custom message here',
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'answer_value' => array(
				'answerRequire' => array(
					'rule' => array('answerRequire', $question),
					'message' => __d('questionnaires', 'Input required'),
				),
				'answerMaxLength' => array(
					'rule' => array('answerMaxLength', $question, QuestionnairesComponent::QUESTIONNAIRE_MAX_ANSWER_LENGTH),
					'message' => sprintf(__d('questionnaires', 'the answer is too long. Please enter under %d letters.', QuestionnairesComponent::QUESTIONNAIRE_MAX_ANSWER_LENGTH)),
				),
				'answerChoiceValidation' => array(
					'rule' => array('answerChoiceValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
				'answerTextValidation' => array(
					'rule' => array('answerTextValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
				'answerDatetimeValidation' => array(
					'rule' => array('answerDatetimeValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
				'answerMatrixValidation' => array(
					'rule' => array('answerMatrixValidation', $question, $allAnswers),
					'last' => true,
					'message' => ''
				),
			),
		);
		parent::beforeValidate($options);

		return true;
	}

/**
 * getProgressiveAnswerOfThisSummary
 *
 * @param array $questionnaire questionnaire data
 * @param array $summary questionnaire summary ( one record )
 * @return array
 */
	public function getProgressiveAnswerOfThisSummary($questionnaire, $summary) {
		$answers = array();
		if (empty($summary)) {
			return $answers;
		}
		// 指定のサマリに該当するアンケートの質問ID配列を取得
		$questionIds = Hash::extract($questionnaire, 'QuestionnairePage.{n}.QuestionnaireQuestion.{n}.id');
		$choiceIds = Hash::extract($questionnaire, 'QuestionnairePage.{n}.QuestionnaireQuestion.{n}.QuestionnaireChoice.{n}.id');
		// その質問配列を取得条件に加える（間違った質問が入らないよう）
		$answer = $this->find('all', array(
			'conditions' => array(
				'questionnaire_answer_summary_id' => $summary['QuestionnaireAnswerSummary']['id'],
				'QuestionnaireQuestion.id' => $questionIds,
				'OR' => array(
					array('QuestionnaireChoice.id' => $choiceIds),
					array('QuestionnaireChoice.id' => null),
				)
			),
			'recursive' => 0
		));
		if (!empty($answer)) {
			foreach ($answer as $ans) {
				$answers[$ans['QuestionnaireAnswer']['questionnaire_question_key']][] = $ans['QuestionnaireAnswer'];
			}
		}
		return $answers;
	}
/**
 * getAnswerCount
 * It returns the number of responses in accordance with the conditions
 *
 * @param array $conditions conditions
 * @return int
 */
	public function getAnswerCount($conditions) {
		$cnt = $this->find('count', array(
			'conditions' => $conditions,
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'questionnaire_answer_summaries',
					'alias' => 'QuestionnaireAnswerSummary',
					'type' => 'LEFT',
					'conditions' => array(
						'QuestionnaireAnswerSummary.id = QuestionnaireAnswer.questionnaire_answer_summary_id',
					)
				)
			)
		));
		return $cnt;
	}

/**
 * saveAnswer
 * save the answer data
 *
 * @param array $data Postされた回答データ
 * @param array $questionnaire questionnaire data
 * @param array $summary answer summary data
 * @throws InternalErrorException
 * @return bool
 */
	public function saveAnswer($data, $questionnaire, $summary) {
		//トランザクションBegin
		$this->begin();
		try {
			$summaryId = $summary['QuestionnaireAnswerSummary']['id'];
			// 繰り返しValidationを行うときは、こうやってエラーメッセージを蓄積するところ作らねばならない
			// 仕方ないCakeでModelObjectを使う限りは
			$validationErrors = array();
			foreach ($data['QuestionnaireAnswer'] as $answer) {
				$targetQuestionKey = $answer[0]['questionnaire_question_key'];
				$targetQuestion = Hash::extract($questionnaire['QuestionnairePage'], '{n}.QuestionnaireQuestion.{n}[key=' . $targetQuestionKey . ']');
				if (! $targetQuestion) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				// データ保存
				// Matrixタイプの場合はanswerが配列になっているがsaveでかまわない
				$this->oneTimeValidateFlag = false;	// saveMany中で１回しかValidateしなくてよい関数のためのフラグ
				// Validate、Saveで使用するオプションデータ
				$options = array(
					'questionnaire_answer_summary_id' => $summaryId,
					'question' => $targetQuestion[0],
					'allAnswers' => $data['QuestionnaireAnswer'],
				);

				if (! $this->saveMany($answer, $options)) {
					$validationErrors[$targetQuestionKey] = $this->__errorMessageUnique($targetQuestion[0], Hash::filter($this->validationErrors));
				}
			}
			if (! empty($validationErrors)) {
				$this->validationErrors = Hash::filter($validationErrors);
				$this->rollback();
				return false;
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}
/**
 * __errorMessageUnique
 * マトリクスの同じエラーメッセージをまとめる
 *
 * @param array $question question
 * @param array $errors error message
 * @return array
 */
	private function __errorMessageUnique($question, $errors) {
		if (! QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
			return $errors;
		}
		$ret = array();
		foreach ($errors as $err) {
			if (! in_array($err, $ret)) {
				$ret[] = $err;
			}
		}
		return $ret;
	}
}
