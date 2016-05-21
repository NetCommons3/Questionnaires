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
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
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
		if (isset($options['ng-model'])) {
			$ngModel = $options['ng-model'];
			$modelNames = explode('.', $ngModel);
			$errorMsgModelName = $modelNames[0] . '.errorMessages.' . $modelNames[1];
			$ret = '<div class="row form-group" ng-class="{\'has-error\': ' . $errorMsgModelName . '}">';
		} else {
			$ret = '<div class="row form-group">';
		}
		$ret .= '<label	class="col-xs-2 control-label">' . $title;
		if (isset($options['required']) && $options['required'] == true) {
			$ret .= $this->_View->element('NetCommons.required');
		}
		$ret .= '</label><div class="col-xs-10">';

		$type = $options['type'];
		if ($this->_View->viewVars['isPublished']) {
			$options = Hash::merge($options, array('disabled' => true));
		}

		$options = Hash::merge($options, array('div' => false, 'label' => false));
		if ($type == 'wysiwyg') {
			if ($this->_View->viewVars['isPublished']) {
				$ret .= '<div class="well well-sm" ng-bind-html="' . $ngModel . ' | ncHtmlContent"></div>';
			} else {
				$ret .= $this->NetCommonsForm->wysiwyg($fieldName, $options);
			}
		} elseif ($type == 'checkbox') {
			$options = Hash::merge($options, array('label' => $label));
			$ret .= $this->NetCommonsForm->checkbox($fieldName, $options);
		} else {
			$ret .= $this->NetCommonsForm->input($fieldName, $options);
		}

		if (isset($options['ng-model'])) {
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
	public function questionnaireAttributeCheckbox(
		$fieldName, $label, $options = array(), $help = '') {
		$ngModel = 'questionnaire.questionnaire.' . Inflector::variable($fieldName);
		$ret = '<div class=" checkbox"><label>';
		$options = Hash::merge(array(
			'type' => 'checkbox',
			'div' => false,
			'label' => false,
			'class' => '',
			'error' => false,
			'ng-model' => $ngModel,
			//'ng-checked' => $ngModel . '==' . RegistrationsComponent::USES_USE,
			// この記述でないと チェックON,OFFが正常に動作しない。
			'ng-false-value' => '"0"',
			'ng-true-value' => '"1"'
		),
			$options
		);

		$ret .= $this->NetCommonsForm->input($fieldName, $options);
		$ret .= $label;
		if (!empty($help)) {
			$ret .= '<span class="help-block">' . $help . '</span>';
		}
		$ret .= '</label>';
		$ret .= '<div class="has-error">';
		$ret .= $this->NetCommonsForm->error($fieldName, null, array('class' => 'help-block'));
		$ret .= '</div>';
		$ret .= '</div>';
		return $ret;
	}
/**
 * アンケート期間設定作成
 *
 * @param string $fieldName フィールド名
 * @param array $options オプション
 * @return string HTML
 */
	public function questionnaireAttributeDatetime($fieldName, $options) {
		$ngModel = 'questionnaire.questionnaire.' . Inflector::variable($fieldName);

		$defaultOptions = array(
			'type' => 'datetime',
			'id' => $fieldName,
			'ng-model' => $ngModel,
		);
		$options = Hash::merge($defaultOptions, $options);
		if (isset($options['min']) && isset($options['max'])) {
			$min = $options['min'];
			$max = $options['max'];
			$options = Hash::merge($options, array(
				'ng-focus' => 'setMinMaxDate($event, \'' . $min . '\', \'' . $max . '\')',
			));
		}

		$ret = '';
		$ret .= $this->NetCommonsForm->input($fieldName, $options);
		if (!empty($help)) {
			$ret .= '<span class="help-block">' . $help . '</span>';
		}
		return $ret;
	}
}
