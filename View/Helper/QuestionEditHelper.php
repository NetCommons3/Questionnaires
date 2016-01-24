<?php
/**
 * Question Edit Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Question edit Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Questionnaires\View\Helper
 */
class QuestionEditHelper extends AppHelper {

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
 * 質問属性設定作成
 *
 * @param string $fieldName フィールド名
 * @param string $title 見出しラベル
 * @param array $options INPUT要素に与えるオプション属性
 * @param string $label checkboxの時のラベル
 * @return string HTML
 */
	public function questionInput($fieldName, $title, $options, $label = '') {
		$ret = '<div class="row form-group"><label	class="col-sm-2 control-label">' . $title;
		if (isset($options['required']) && $options['required'] == true) {
			$ret .= $this->_View->element('NetCommons.required');
		}
		$ret .= '</label><div class="col-sm-10">';

		$type = $options['type'];
		if ($this->_View->viewVars['isPublished']) {
			$disabled = 'disabled';
			$options = Hash::remove($options, 'ui-tinymce');
		} else {
			$disabled = '';
		}

		if ($type == 'checkbox') {
			$ret .= '<div class="checkbox ' . $disabled . '"><label>';
		}
		if ($type == 'wysiswyg') {
			$ret .= '<div class="nc-wysiwyg-alert">';
		}

		$options = Hash::merge($options, array('div' => false, 'label' => false));
		$ret .= $this->NetCommonsForm->input($fieldName, $options);

		if ($type == 'checkbox') {
			$ret .= $label . '</label></div>';
		}
		if ($type == 'wysiswyg') {
			$ret .= '</div>';
		}

		if (isset($options['ng-model'])) {
			$ngModel = $options['ng-model'];
			$modelNames = explode('.', $ngModel);
			$errorMsgModelName = $modelNames[0] . '.errorMessages.' . $modelNames[1];
			$ret .= '<div class="has-error" ng-if="' . $errorMsgModelName . '">';
			$ret .= '<div class="help-block" ng-repeat="errorMessage in ' . $errorMsgModelName . '">';
				$ret .= '{{errorMessage}}</div></div>';
			$ret .= '</div></div>';
		}

		return $ret;
	}
/**
 * アンケート属性設定作成
 *
 * @param string $fieldName フィールド名
 * @param string $label checkboxの時のラベル
 * @param array $options INPUT要素に与えるオプション属性
 * @param string $help 追加説明文
 * @return string HTML
 */
	public function questionnaireAttributeCheckbox($fieldName, $label, $options = array(), $help = '') {
		$ngModel = 'questionnaires.questionnaire.' . Inflector::variable($fieldName);
		$ret = '<div class=" checkbox"><label>';
		$options = Hash::merge(array(
			'type' => 'checkbox',
			'div' => false,
			'label' => false,
			'class' => '',
			'error' => false,
			'ng-model' => $ngModel,
			'ng-checked' => $ngModel . '==' . QuestionnairesComponent::USES_USE),
			$options
		);

		$ret .= $this->NetCommonsForm->input($fieldName, $options);
		$ret .= $label;
		if (!empty($help)) {
			$ret .= '<span class="help-block">' . $help . '</span>';
		}
		$ret .= '</label>';
		$ret .= '<div class="has-error">' . $this->NetCommonsForm->error($fieldName, null, array('class' => 'help-block')) . '</div>';
		$ret .= '</div>';
		return $ret;
	}
/**
 * アンケート期間設定作成
 *
 * @param string $fieldName フィールド名
 * @param string $label checkboxの時のラベル
 * @param array $minMax 日時指定の範囲がある場合のmin, max
 * @param string $help 追加説明文
 * @return string HTML
 */
	public function questionnaireAttributeDatetime($fieldName, $label, $minMax = array(), $help = '') {
		$ngModel = 'questionnaires.questionnaire.' . Inflector::variable($fieldName);

		$options = array('type' => 'text',
				'id' => $fieldName,
				'class' => 'form-control',
				'placeholder' => 'yyyy-mm-dd',
				'show-weeks' => 'false',
				'ng-model' => $ngModel,
				'datetimepicker',
				'datetimepicker-options' => "{format:'YYYY-MM-DD HH:mm'}",
				'show-meridian' => 'false',
				'label' => $label,
				'div' => false);
		if (! empty($minMax)) {
			$min = $minMax['min'];
			$max = $minMax['max'];
			$options = Hash::merge($options, array(
				'min' => $min,
				'max' => $max,
				'ng-focus' => 'setMinMaxDate($event, \'' . $min . '\', \'' . $max . '\')',
			));
		}

		$ret = $this->_View->element('NetCommons.datetimepicker');
		$ret .= '<div class="form-group "><div class="input-group">';
		$ret .= $this->NetCommonsForm->input($fieldName, $options);
		if (!empty($help)) {
			$ret .= '<span class="help-block">' . $help . '</span>';
		}
		$ret .= '<div class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></div></div></div>';
		return $ret;
	}
}
