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
 * Use database config
 *
 * @var string
 */
	public $useDbConfig = 'master';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'questionnaire_answer_summary_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'questionnaire_question_origin_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'answer_value' => array(
			'answerValidation' => array(
				'rule' => array('checkAnswerValue', null),
				'last' => true,
				'message' => ''
			)
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'QuestionnaireChoice' => array(
			'className' => 'QuestionnaireChoice',
			'foreignKey' => 'matrix_choice_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'QuestionnaireAnswerSummary' => array(
			'className' => 'QuestionnaireAnswerSummary',
			'foreignKey' => 'questionnaire_answer_summary_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'QuestionnaireQuestion' => array(
			'className' => 'QuestionnaireQuestion',
			'foreignKey' => 'questionnaire_question_origin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * AfterFind Callback function
 *
 * @param array $results found data records
 * @param bool $primary indicates whether or not the current model was the model that the query originated on or whether or not this model was queried as an association
 * @return mixed
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function afterFind($results, $primary = false) {
		// afterFind 選択肢系の回答の場合、answer_value に　[id:value|id:value....]の形で収まっているので
		// それを使いやすいように展開する
		foreach ($results as &$val) {

			$val['QuestionnaireAnswer']['answer_values'] = array();

			if (isset($val['QuestionnaireAnswer']['answer_value'])) {
				$answers = explode(QuestionnairesComponent::ANSWER_DELIMITER, trim($val['QuestionnaireAnswer']['answer_value'], QuestionnairesComponent::ANSWER_DELIMITER));
				if (!empty($answers)) {
					foreach ($answers as $ans) {
						$idValue = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER, $ans);
						$val['QuestionnaireAnswer']['answer_values'][$idValue[0]] = isset($idValue[1]) ? $idValue[1] : null;
					}
				}
			}
		}
		return $results;
	}

/**
 * beforeSave 選択肢系の回答の場合、answer_value に　[id:value|id:value....]の形で収めなくてはいけない
 * 保存前に整える
 *
 * @param array $options オプション
 * @return bool
 */
	public function beforeSave($options = array()) {
		if (isset($this->data['QuestionnaireAnswer']['multi_answer_values'])) {
			$this->data['QuestionnaireAnswer']['answer_value'] = $this->data['QuestionnaireAnswer']['multi_answer_values'];
		} elseif (isset($this->data['QuestionnaireAnswer']['matrix_answer_values'])) {

		}
		return true;
	}

/**
 * checkAnswerValue 入力回答の正当性をチェックする
 *
 * @param array $data Postされた回答データ
 * @param array $question 回答データに対応する質問
 * @return bool
 */
	public function checkAnswerValue($data, $question) {
		$this->loadModels([
			'QuestionnaireAnswerValidation' => 'Questionnaires.QuestionnaireAnswerValidation',
		]);

		$errors = array();

		$answer = $this->data['QuestionnaireAnswer']['answer_value'];

		$this->__setupAnswerValue($answer, $question);

		//
		// 必須なのに入力されていない
		//
		if ($question['is_require'] == QuestionnairesComponent::REQUIRES_REQUIRE) {
			$errors = array_merge($errors,
				$this->QuestionnaireAnswerValidation->checkRequire($question, $this->data['QuestionnaireAnswer']));
		}

		//
		// １行テキストチェック
		//
		if ($question['question_type'] == QuestionnairesComponent::TYPE_TEXT) {
			$errors = array_merge($errors,
				$this->QuestionnaireAnswerValidation->checkNumericType($question, $answer));
			$errors = array_merge($errors,
				$this->QuestionnaireAnswerValidation->checkRange($question, $answer, 'number'));
		}
		//
		// 日付回答チェック
		//
		if ($question['question_type'] == QuestionnairesComponent::TYPE_DATE_AND_TIME) {
			$errors = array_merge($errors,
				$this->QuestionnaireAnswerValidation->checkDatetimeType($question, $answer));
			$errors = array_merge($errors,
				$this->QuestionnaireAnswerValidation->checkRange($question, $answer, 'date'));
		}

		//
		// 択一選択式
		// リスト
		// 複数選択式
		// 異常値入力されてないか
		// その他がチェックされているのにその他の項目に入力されていないことはないか
		//
		if (isset($this->data['QuestionnaireAnswer']['answer_values'])) {
			$list = Hash::combine($question['QuestionnaireChoice'], '{n}.id', '{n}.origin_id');
			$choiceIds = array_keys($this->data['QuestionnaireAnswer']['answer_values']);
			foreach ($choiceIds as $choiceId) {
				$errors = array_merge($errors,
					$this->QuestionnaireAnswerValidation->checkAnswerInList($question, $choiceId, $list));
				$errors = array_merge($errors,
					$this->QuestionnaireAnswerValidation->checkOtherAnswer($question, $answer, $choiceId, $this->data['QuestionnaireAnswer']));
			}
		}

		// マトリクス
		// マトリクス択一
		// マトリクス複数
		// マトリクスの場合はデフォルト全部の行に回答することを求める
		// 異常値入力されてないか
		// その他がチェックされているのにその他の項目に入力されていないことはないか
		if (isset($this->data['QuestionnaireAnswer']['matrix_answer_values'])) {
			$list = Hash::combine($question['QuestionnaireChoice'], '{n}.id', '{n}.origin_id');
			$errors = array_merge($errors,
				$this->QuestionnaireAnswerValidation->checkMatrixAnswerInList($question, $this->data['QuestionnaireAnswer']['matrix_answer_values'], $list));
			$errors = array_merge($errors,
				$this->QuestionnaireAnswerValidation->checkMatrixOtherAnswer($question, $this->data['QuestionnaireAnswer']['matrix_answer_values'], $this->data['QuestionnaireAnswer']));
		}

		//
		// なんらかのエラーメッセージが設定されていたらfalseリターン
		//
		if (count($errors) > 0) {
			$this->validationErrors['answer_value'] = $errors;
			return false;
		}
		return true;
	}

/**
 * __setupAnswerValue
 * setup Answer for check
 *
 * @param array $answer Postされた回答データ
 * @param array $question 回答データに対応する質問
 * @return void
 */
	private function __setupAnswerValue($answer, $question) {
		// 質問種別によってチェックする内容が異なるので
		if ($question['question_type'] == QuestionnairesComponent::TYPE_SELECTION
			|| $question['question_type'] == QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX) {
			$this->data['QuestionnaireAnswer']['answer_values'] = array();
			$this->__getAnswerValueOfSelect($this->data['QuestionnaireAnswer']['answer_values'], $answer);
		}
		if ($question['question_type'] == QuestionnairesComponent::TYPE_MULTIPLE_SELECTION) {
			$this->data['QuestionnaireAnswer']['answer_values'] = array();
			$this->data['QuestionnaireAnswer']['multi_answer_values'] = '';
			foreach ($answer as $a) {
				$this->__getAnswerValueOfSelect($this->data['QuestionnaireAnswer']['answer_values'], $a);
				$this->data['QuestionnaireAnswer']['multi_answer_values'] .= $a;
			}
		}
		if ($question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST) {
			$this->data['QuestionnaireAnswer']['matrix_answer_values'] = array();
			$this->__getAnswerValueOfSelect($this->data['QuestionnaireAnswer']['matrix_answer_values'][$this->data['QuestionnaireAnswer']['matrix_choice_id']], $answer);
		}
		if ($question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_MULTIPLE) {
			$this->data['QuestionnaireAnswer']['matrix_answer_values'] = array();
			$this->data['QuestionnaireAnswer']['multi_answer_values'] = '';
			$matrixChoiceId = $this->data['QuestionnaireAnswer']['matrix_choice_id'];
			foreach ($answer as $ans) {
				$this->__getAnswerValueOfSelect($this->data['QuestionnaireAnswer']['matrix_answer_values'][$matrixChoiceId], $ans);
				$this->data['QuestionnaireAnswer']['multi_answer_values'] .= $ans;
			}
		}
	}

/**
 * __getAnswerValueOfSelect
 * get answer value for selection question
 *
 * @param array &$data Postされた回答データ
 * @param array $answer 分解された選択肢回答
 * @return void
 */
	private function __getAnswerValueOfSelect(&$data, $answer) {
		$answers = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER, trim($answer, QuestionnairesComponent::ANSWER_DELIMITER));
		$data[$answers[0]] = isset($answers[1]) ? $answers[1] : '';
	}

/**
 * saveAnswer
 * save the answer data
 *
 * @param array $questionnaire questionnaire data
 * @param int $userId user id
 * @param string $sessionId session id
 * @param array $data Postされた回答データ
 * @param array &$errors error messages
 * @throws $ex
 * @return bool
 */
	public function saveAnswer($questionnaire, $userId, $sessionId, $data, &$errors) {
		$errors = array();

		$this->loadModels([
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
		]);

		//トランザクションBegin
		//$dataSource = $this->getDataSource();
		//$dataSource->begin();

		try {
			// 初回回答か再回答かを確認している
			// 初めは「ID」がPOSTに入っているかどうかで判断しようと思っていたが
			// Cakeは簡単にブラウザの「戻る」で前画面を表示させたりするので、
			// POSTのIDは再回答であるにもかかわらず空ってことがありうる
			// なので毎回DBチェックするしかないかと思う I think so.
			// サマリレコード取得
			$summary = $this->QuestionnaireAnswerSummary->forceGetAnswerSummary(
				$questionnaire,
				$userId,
				$sessionId,
				array(
					'questionnaire_origin_id' => $questionnaire['Questionnaire']['origin_id'],
					'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
					'session_value' => $sessionId,
					'user_id' => $userId
				)
			);
			$summaryId = $summary['QuestionnaireAnswerSummary']['id'];

			//
			foreach ($data as $answer) {
				// 質問によってバリデーション動作が変わるので
				$targetQuestion = Hash::extract($questionnaire['QuestionnairePage'], '{n}.QuestionnaireQuestion.{n}[origin_id=' . $answer['questionnaire_question_origin_id'] . ']');
				$this->validator()->getField('answer_value')->setRule(
					'answerValidation',
					array('rule' => array(
						'checkAnswerValue',
						$targetQuestion[0],
						'message' => ''
					)));

				// データ保存
				if ($targetQuestion[0]['question_type'] == QuestionnairesComponent::TYPE_MATRIX_MULTIPLE
					|| $targetQuestion[0]['question_type'] == QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST) {
					foreach ($answer as $ans) {
						if (!$this->__saveAnswer($ans, $summaryId, $summary)) {
							$errors[$ans['questionnaire_question_origin_id']] = $this->validationErrors;
						}
					}
				} else {
					if (!$this->__saveAnswer($answer, $summaryId, $summary)) {
						$errors[$answer['questionnaire_question_origin_id']] = $this->validationErrors;
					}
				}
			}
			//$dataSource->commit();

		} catch (Exception $ex) {
			//$dataSource->rollback();
			CakeLog::error($ex);
			throw $ex;
		}

		if (count($errors) == 0) {
			return true;
		} else {
			return false;
		}
	}

/**
 * __saveAnswer
 * save the answer data
 *
 * @param array $answer answer data data
 * @param int $summaryId summary id
 * @param array $summary summary data
 * @return bool
 */
	private function __saveAnswer($answer, $summaryId, $summary) {
		if (!is_array($answer)) {
			return true;
		}
		$answer['questionnaire_answer_summary_id'] = $summaryId;
		if (isset($summary['QuestionnaireAnswer'])) {
			$past = Hash::extract($summary['QuestionnaireAnswer'], '{n}[matrix_choice_id=' . $answer['matrix_choice_id'] . ']');
		} else {
			$past = false;
		}

		if ($past) {
			$answer['id'] = $past[0]['id'];
		} else {
			$this->create();
		}
		if (!$this->save($answer)) {
			return false;
		}
		return true;
	}
}
