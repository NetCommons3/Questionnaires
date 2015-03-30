<?php
/**
 * QuestionnaireAnswer Model
 *
 * @property MatrixChoice $MatrixChoice
 * @property Choice $Choice
 * @property QuestionnaireAnswerSummary $QuestionnaireAnswerSummary
 * @property QuestionnaireQuestion $QuestionnaireQuestions
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
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
		'questionnaire_question_id' => array(
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
		'QuestionnaireAnswerSummary' => array(
			'className' => 'QuestionnaireAnswerSummary',
			'foreignKey' => 'questionnaire_answer_summary_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'QuestionnaireQuestions' => array(
			'className' => 'QuestionnaireQuestions',
			'foreignKey' => 'questionnaire_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * afterFind 選択肢系の回答の場合、answer_value に　[id:value|id:value....]の形で収まっているので
	 * それを使いやすいように展開する
	 *
	 * @return boolean
	 */
	public function afterFind($results, $primary = false) {
		foreach ($results as $key => &$val) {

			$val['QuestionnaireAnswer']['answer_values'] = array();

			if (isset($val['QuestionnaireAnswer']['answer_value'])) {
				$answers = explode(QuestionnairesComponent::ANSWER_DELIMITER, $val['QuestionnaireAnswer']['answer_value']);
				if (!empty($answers)) {
					foreach($answers as $ans) {
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
	 * @return boolean
	 */
	public function beforeSave($options = array()) {
		if (isset($this->data['QuestionnaireAnswer']['multi_answer_values'])) {
			$this->data['QuestionnaireAnswer']['answer_value'] = $this->data['QuestionnaireAnswer']['multi_answer_values'];
		}
		else if (isset($this->data['QuestionnaireAnswer']['matrix_answer_values'])) {

		}
		return true;
	}
/**
 * checkAnswerValue 入力回答の正当性をチェックする
 *
 * @return boolean
 */
	public function checkAnswerValue($data, $question) {

		$this->validationErrors = array();

		$answer = $this->data['QuestionnaireAnswer']['answer_value'];

		// 質問種別によってチェックする内容が異なるので
		switch ($question['question_type']) {
			case QuestionnairesComponent::TYPE_SELECTION:
			case QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX:
				$answers = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER, trim($answer, QuestionnairesComponent::ANSWER_DELIMITER));
				$this->data['QuestionnaireAnswer']['answer_values'] = array();
				$this->data['QuestionnaireAnswer']['answer_values'][$answers[0]] = isset($answers[1]) ? $answers[1] : '';
				break;
			case QuestionnairesComponent::TYPE_MULTIPLE_SELECTION:
			case QuestionnairesComponent::TYPE_MATRIX_MULTIPLE:
				$this->data['QuestionnaireAnswer']['answer_values'] = array();
				$this->data['QuestionnaireAnswer']['multi_answer_values'] = '';
			$this->log(print_r($answer, true), 'debug');
				if (is_array($answer)) {
					foreach ($answer as $a) {
						$answers = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER, trim($a, QuestionnairesComponent::ANSWER_DELIMITER));
						$this->data['QuestionnaireAnswer']['answer_values'][$answers[0]] = isset($answers[1]) ? $answers[1] : '';
						$this->data['QuestionnaireAnswer']['multi_answer_values'] .= $a;
					}
				}
				break;
			case QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST:
				$this->data['QuestionnaireAnswer']['answer_values'] = array();
				$this->data['QuestionnaireAnswer']['matrix_answer_values'] = '';
				if (is_array($answer)) {
					foreach ($answer as $matrix_choice_id => $a) {
						$answers = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER, trim($a, QuestionnairesComponent::ANSWER_DELIMITER));
						$this->data['QuestionnaireAnswer']['answer_values'][$matrix_choice_id][$answers[0]] = isset($answers[1]) ? $answers[1] : '';
						$this->data['QuestionnaireAnswer']['matrix_answer_values'][$matrix_choice_id][$answers[0]] = isset($answers[1]) ? $answers[1] : '';
					}
				}
				break;
		}

		$qId = $question['id'];

		//
		// 必須なのに入力されていない
		//
		if ($question['require_flag'] == QuestionnairesComponent::REQUIRES_REQUIRE
			&& empty($answer)) {
//			$this->validationErrors['answer_value'][$qId] = __d('questionnaires', 'Input required');
			$this->validationErrors['answer_value'][] =__d('questionnaires', 'Input required');
		}

		//
		// １行テキストで数値型
		// また上限下限の設定チェック
		//
		if ($question['question_type'] == QuestionnairesComponent::TYPE_TEXT
			&& $question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_NUMERIC
			&& strlen($answer) > 0 ) {
			$chk = preg_match('/^-?[0-9\.]+$/', (string)$answer) ? true : false;
			if (!$chk) {
				$this->validationErrors['answer_value'][] = __d('questionnaires', 'Number required');
			}
			else {
				if (!is_null($question['min'])) {
					if ((int)$answer < $question['min']) {
						$this->validationErrors['answer_value'][] = sprintf(__d('questionnaires', 'Please enter more than %d'), $question['min']);
					}
				}
			}
		}

		//
		// 択一選択式
		// リスト
		// 異常値入力されてないか
		// その他がチェックされているのにその他の項目に入力されていないことはないか
		//
		if ($question['question_type'] == QuestionnairesComponent::TYPE_SELECTION ||
			$question['question_type'] == QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX ||
			$question['question_type'] == QuestionnairesComponent::TYPE_MULTIPLE_SELECTION) {
			$results = null;
			foreach ($this->data['QuestionnaireAnswer']['answer_values'] as $choice_id=>$val) {
				$results = Hash::extract($question['QuestionnaireChoice'], '{n}[id='.$choice_id.']');
				if (!$results) {
					$this->validationErrors['answer_value'][] = __d('questionnaires', 'Invalid choice');
				}
			}

			if ($results && $results[0]['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				if (!isset($this->data['QuestionnaireAnswer']['other_answer_value']) ||
					empty($this->data['QuestionnaireAnswer']['other_answer_value'])) {
					$this->validationErrors['answer_value'][] = __d('questionnaires', 'Please enter something, if you chose the other item');
				}
			}
			else {
				$this->data['QuestionnaireAnswer']['other_answer_value'] = '';
			}
		}

		//
		// なんらかのエラーメッセージが設定されていたらfalseリターン
		//
		if (isset($this->validationErrors['answer_value'])) {
			return false;
		}
		return true;
	}
}
