<?php
/**
 * QuestionnaireAnswer Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Questionnaires Answer Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Questionnaires\View\Helper
 */
class QuestionnaireAnswerHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommonsForm',
		'Form'
	);

/**
 * Answer html create by question type
 *
 * @var array
 */
	protected $_answerFunc = array(
		QuestionnairesComponent::TYPE_SELECTION => 'singleChoice',
		QuestionnairesComponent::TYPE_MULTIPLE_SELECTION => 'multipleChoice',
		QuestionnairesComponent::TYPE_TEXT => 'singleText',
		QuestionnairesComponent::TYPE_TEXT_AREA => 'textArea',
		QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST => 'matrix',
		QuestionnairesComponent::TYPE_MATRIX_MULTIPLE => 'matrix',
		QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX => 'singleList',
		QuestionnairesComponent::TYPE_DATE_AND_TIME => 'dateTimeInput'
	);

/**
 * 回答作成
 *
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 回答HTML
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function answer($question, $readonly = false) {
		// 質問セットをもらう
		// 種別に応じて質問＆回答の要素を作成し返す
		$index = $question['key'];
		$fieldName = 'QuestionnaireAnswer.' . $index . '.0.answer_value';

		$ret = call_user_func_array(
			array($this, $this->_answerFunc[$question['question_type']]),
			array($index, $fieldName, $question, $readonly));

		if (! QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
			$ret .= $this->_error($fieldName);
			$ret .= $this->NetCommonsForm->hidden('QuestionnaireAnswer.' . $index . '.0.questionnaire_answer_summary_id');
			$ret .= $this->NetCommonsForm->hidden('QuestionnaireAnswer.' . $index . '.0.questionnaire_question_key', array('value' => $index));
			$ret .= $this->NetCommonsForm->hidden('QuestionnaireAnswer.' . $index . '.0.id');
			$ret .= $this->NetCommonsForm->hidden('QuestionnaireAnswer.' . $index . '.0.matrix_choice_key', array('value' => null));
		}
		return $ret;
	}
/**
 * 択一選択回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 択一選択肢回答のHTML
 */
	public function singleChoice($index, $fieldName, $question, $readonly) {
		$ret = '';
		$otherAnswerFieldName = 'QuestionnaireAnswer.' . $index . '.0.other_answer_value';

		if (isset($question['QuestionnaireChoice'])) {
			$afterLabel = '</label></div>';
			$choices = Hash::sort($question['QuestionnaireChoice'], '{n}.other_choice_type', 'asc');
			$options = $this->_getChoiceOptionElement($choices);
			if (Hash::extract($question['QuestionnaireChoice'], '{n}[other_choice_type!=' . QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED . ']')) {
				$otherInput = $this->NetCommonsForm->input($otherAnswerFieldName, array(
					'type' => 'text',
					'label' => false,
					'div' => false,
					'disabled' => $readonly,
				));
				$afterLabel = $otherInput . $afterLabel;
			}
			$ret = $this->Form->input($fieldName, array(
				'type' => 'radio',
				'options' => $options,
				'legend' => false,
				'label' => false,
				'before' => '<div class="radio"><label>',
				'separator' => '</label></div><div class="radio"><label>',
				'after' => $afterLabel,
				'disabled' => $readonly,
				'error' => false,
			));
		}
		return $ret;
	}

/**
 * 複数選択回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function multipleChoice($index, $fieldName, $question, $readonly) {
		$ret = '';
		$otherAnswerFieldName = 'QuestionnaireAnswer.' . $index . '.0.other_answer_value';

		if (isset($question['QuestionnaireChoice'])) {
			$afterLabel = '';
			$choices = Hash::sort($question['QuestionnaireChoice'], '{n}.other_choice_type', 'asc');
			$options = $this->_getChoiceOptionElement($choices);

			if (Hash::extract($question['QuestionnaireChoice'], '{n}[other_choice_type!=' . QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED . ']')) {
				$otherInput = $this->NetCommonsForm->input($otherAnswerFieldName, array(
					'type' => 'text',
					'label' => false,
					'div' => false,
					'disabled' => $readonly,
				));
				$afterLabel = '<div class="checkbox-inline">' . $otherInput . '</div>';
			}

			$ret .= $this->NetCommonsForm->input($fieldName, array(
				'type' => 'select',
				'multiple' => 'checkbox',
				'options' => $options,
				'label' => false,
				'div' => false,
				'class' => 'checkbox nc-checkbox',
				'disabled' => $readonly,
				'hiddenField' => !$readonly,
				'error' => false,
			));

			$ret .= $afterLabel;
		}
		return $ret;
	}
/**
 * テキスト回答作成
 *
 * @param string $index  回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question  質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function singleText($index, $fieldName, $question, $readonly) {
		if ($readonly) {
			$ret = $this->value($fieldName);
			return $ret;
		}
		$ret = $this->NetCommonsForm->input($fieldName, array(
			'div' => 'form-inline',
			'type' => 'text',
			'label' => false,
			'error' => false,
			));
		if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
			$ret .= '<span class="help-block">';
			if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_NUMERIC) {
				$ret .= sprintf(__d('questionnaires', 'Please enter a number between %s and %s'), $question['min'], $question['max']);
			} else {
				$ret .= sprintf(__d('questionnaires', 'Please enter between %s letters and %s letters'), $question['min'], $question['max']);
			}
			$ret .= '</span>';
		}
		return $ret;
	}
/**
 * 長文テキスト回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function textArea($index, $fieldName, $question, $readonly) {
		if ($readonly) {
			$ret = nl2br($this->value($fieldName));
			return $ret;
		}
		$ret = $this->NetCommonsForm->textarea($fieldName, array(
			'div' => 'form-inline',
			'label' => false,
			'class' => 'form-control',
			'rows' => 5,
			'error' => false,
		));
		return $ret;
	}
/**
 * リストボックス回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function singleList($index, $fieldName, $question, $readonly) {
		if ($readonly) {
			$answer = $this->value($fieldName);
			$ret = substr($answer, strrpos($answer, QuestionnairesComponent::ANSWER_VALUE_DELIMITER) + 1);
			return $ret;
		}
		if (isset($question['QuestionnaireChoice'])) {
			$options = $this->_getChoiceOptionElement($question['QuestionnaireChoice']);
			$ret = $this->NetCommonsForm->input($fieldName, array(
					'type' => 'select',
					'options' => $options,
					'label' => false,
					'div' => 'form-inline',
					'disabled' => $readonly,
					'empty' => __d('questionnaires', 'Please choose one')
			));
		}
		return $ret;
	}
/**
 * マトリクス回答作成
 *
 * @param string $index  回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question  質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function matrix($index, $fieldName, $question, $readonly) {
		if (isset($question['QuestionnaireChoice'])) {
			$cols = Hash::extract($question['QuestionnaireChoice'], '{n}[matrix_type=' . QuestionnairesComponent::MATRIX_TYPE_COLUMN . ']');
			$rowChoices = Hash::extract($question['QuestionnaireChoice'], '{n}[matrix_type!=' . QuestionnairesComponent::MATRIX_TYPE_COLUMN . ']');
			$options = $this->_getChoiceOptionElement($cols);
		}
		$addClass = '';
		if (! $readonly) {
			$addClass = ' table-striped table-hover ';
		}
		$errorMessage = '';

		$ret = '<table class="table ' . $addClass . 'table-bordered text-center questionnaire-matrix-table">';
		$ret .= '<thead><tr><th></th>';
		foreach ($options as $opt) {
			$ret .= '<th class="text-center">' . $opt . '</th>';
		}
		$ret .= '</thead><tbody>';

		foreach ($rowChoices as $rowIndex => $row) {
			$ret .= '<tr><th>' . $row['choice_label'];
			$ret .= $this->NetCommonsForm->hidden('QuestionnaireAnswer.' . $index . '.' . $rowIndex . '.questionnaire_answer_summary_id');
			$ret .= $this->NetCommonsForm->hidden('QuestionnaireAnswer.' . $index . '.' . $rowIndex . '.questionnaire_question_key', array('value' => $index));
			$ret .= $this->NetCommonsForm->hidden('QuestionnaireAnswer.' . $index . '.' . $rowIndex . '.matrix_choice_key', array('value' => $row['key']));
			$ret .= $this->NetCommonsForm->hidden('QuestionnaireAnswer.' . $index . '.' . $rowIndex . '.id');
			if ($row['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
				$ret .= $this->NetCommonsForm->input('QuestionnaireAnswer.' . $index . '.' . $rowIndex . '.other_answer_value', array(
					'type' => 'text',
					'label' => false,
					'div' => false,
					'disabled' => $readonly,
				));
			}
			$ret .= '</th>';
			$ret .= $this->_getMatrixRow($question['question_type'], 'QuestionnaireAnswer.' . $index . '.' . $rowIndex . '.answer_value', $options, $readonly);
			$ret .= '</tr>';
			$errorMessage .= $this->_error('QuestionnaireAnswer.' . $index . '.' . $rowIndex . '.answer_value');
		}
		$ret .= '</tbody></table>';
		$ret .= $errorMessage;
		return $ret;
	}
/**
 * 日付・時間回答作成
 *
 * @param string $index 回答データのPOST用dataのインデックス値
 * @param string $fieldName フィールド名
 * @param array $question 質問データ
 * @param bool $readonly 読み取り専用
 * @return string 複数選択肢回答のHTML
 */
	public function dateTimeInput($index, $fieldName, $question, $readonly) {
		if ($readonly) {
			$ret = $this->value($fieldName);
			return $ret;
		}

		$rangeMessage = '<span class="help-block">';
		$options = array();
		if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE) {
			$icon = 'glyphicon-calendar';
			$options['format'] = 'YYYY-MM-DD';
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				$options['minDate'] = $question['min'];
				$options['maxDate'] = $question['max'];
				$rangeMessage .= sprintf(__d('questionnaires', 'Please enter at %s to %s'), date('Y-m-d', strtotime($question['min'])), date('Y-m-d', strtotime($question['max'])));
			}
		} elseif ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_TIME) {
			$icon = 'glyphicon-time';
			$options['format'] = 'HH:mm';
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				$options['minDate'] = date('Y-m-d ', time()) . $question['min'];
				$options['maxDate'] = date('Y-m-d ', time()) . $question['max'];
				$rangeMessage .= sprintf(__d('questionnaires', 'Please enter at %s to %s'), date('H:i', strtotime($question['min'])), date('H:i', strtotime($question['max'])));
			}
		} elseif ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE_TIME) {
			$icon = 'glyphicon-calendar';
			$options['format'] = 'YYYY-MM-DD HH:mm';
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				$options['minDate'] = $question['min'];
				$options['maxDate'] = $question['max'];
				$rangeMessage .= sprintf(__d('questionnaires', 'Please enter at %s to %s'), date('Y-m-d H:i', strtotime($question['min'])), date('Y-m-d H:i', strtotime($question['max'])));
			}
		}
		$options = json_encode($options);
		$rangeMessage .= '</span>';

		$ret = '<div class="row"><div class="col-sm-4">';
		$ret .= $this->_View->element('NetCommons.datetimepicker');
		$ret .= '<div class="input-group date" >';
		$ret .= $this->NetCommonsForm->input($fieldName,
							array('type' => 'text',
								'div' => false,
								'class' => 'form-control glyphicon',
								'datetimepicker',
								'datetimepicker-options' => $options,
								'ng-model' => 'dateAnswer[' . "'" . $question['key'] . "'" . ']',
								//'value' => $this->value($fieldName),
								'label' => false,
								'error' => false));
		$ret .= '<span class="input-group-addon glyphicon ' . $icon . '"></span>';
		$ret .= '</div>';
		$ret .= '</div></div>';
		$ret .= $rangeMessage;
		return $ret;
	}

/**
 * エラーメッセージ表示要素作成
 *
 * @param string $fieldName フィールド名
 * @return string エラーメッセージ表示要素のHTML
 */
	protected function _error($fieldName) {
		$output = '<div class="has-error">';
		$output .= $this->NetCommonsForm->error($fieldName, null, array('class' => 'help-block'));
		$output .= '</div>';
		return $output;
	}

/**
 * 選択肢要素作成
 *
 * @param array $choices 選択肢データ
 * @return string 選択肢要素のHTML
 */
	protected function _getChoiceOptionElement($choices) {
		$ret = array();
		foreach ($choices as $choice) {
			$ret[QuestionnairesComponent::ANSWER_DELIMITER . $choice['key'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . $choice['choice_label']] = $choice['choice_label'];
		}
		return $ret;
	}
/**
 * マトリクス選択肢要素作成
 *
 * @param int $questionType 質問種別
 * @param string $fieldName フィールド名
 * @param array $options 選択肢データ
 * @param bool $readonly 読み取り専用
 * @return string マトリクス選択行のHTML
 */
	protected function _getMatrixRow($questionType, $fieldName, $options, $readonly) {
		$ret = '';
		$optCount = 0;
		$keys = array_keys($options);
		foreach ($keys as $key) {
			$ret .= '<td>';
			$inputOptions = array(
				'options' => array($key => ''),
				'label' => false,
				'div' => false,
				'class' => '',
				'hiddenField' => ($readonly || $optCount != 0) ? false : true,
				'disabled' => $readonly,
				'error' => false,
			);
			if ($questionType == QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST) {
				$inputOptions['type'] = 'radio';
			} else {
				$inputOptions['type'] = 'select';
				$inputOptions['multiple'] = 'checkbox';
			}
			$ret .= $this->NetCommonsForm->input($fieldName, $inputOptions);
			$optCount++;
			$ret .= '</td>';
		}
		return $ret;
	}
}
