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
		'NetCommons.Token',
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
			$options = array_merge($options, array('disabled' => true));
		}

		$options = array_merge($options, array('div' => false, 'label' => false));
		if ($type == 'wysiwyg') {
			if ($this->_View->viewVars['isPublished']) {
				$ret .= '<div class="well well-sm" ng-bind-html="' . $ngModel . ' | ncHtmlContent"></div>';
			} else {
				$ret .= $this->NetCommonsForm->wysiwyg($fieldName, $options);
			}
		} elseif ($type == 'checkbox') {
			$options = array_merge($options, array('label' => $label));
			$ret .= $this->NetCommonsForm->checkbox($fieldName, $options);
		} else {
			$ret .= $this->NetCommonsForm->input($fieldName, $options);
		}

		if (isset($options['ng-model'])) {
			$ret .= '<div class="has-error" ng-if="' . $errorMsgModelName . '">';
			$ret .= '<div class="help-block" ng-repeat="errorMessage in ' . $errorMsgModelName . '">';
			$ret .= '{{errorMessage}}</div></div>';
		}
		$ret .= '</div></div>';

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
		$options = array_merge(array(
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
		$options = array_merge($defaultOptions, $options);
		if (isset($options['min']) && isset($options['max'])) {
			$min = $options['min'];
			$max = $options['max'];
			$options = array_merge($options, array(
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
/**
 * questionnaireGetFinallySubmit
 *
 * アンケートは質問編集画面、集計結果編集画面では分割送信をする
 * 分割送信後、最終POSTをする時に使用するFormを作成する
 *
 * @param array $postUrl POST先URL情報
 * @param array $ngValues 最終POSTに合わせて送る付加情報のフィールド名とng-Value名
 * @return string HTML
 */
	public function questionnaireGetFinallySubmit($postUrl, $ngValues = array()) {
		$html = '';
		$html .= $this->NetCommonsForm->create('QuestionnaireQuestion',
			array_merge(array('id' => 'finallySubmitForm'), $postUrl)
		);
		$html .= $this->NetCommonsForm->hidden('Frame.id');
		$html .= $this->NetCommonsForm->hidden('Block.id');
		$html .= $this->NetCommonsForm->hidden('Questionnaire.key');
		if (! empty($ngValues)) {
			foreach ($ngValues as $hiddenName => $ngValue) {
				$html .= $this->NetCommonsForm->hidden($hiddenName, array('ng-value' => $ngValue));
				$this->NetCommonsForm->unlockField($hiddenName);
			}
		}
		$this->NetCommonsForm->unlockField('QuestionnairePage');
		$html .= $this->NetCommonsForm->end();
		return $html;
	}

/**
 * getJsPostData
 *
 * アンケートは分割送信をAjaxで行う
 * AjaxでPostを行うときにtoken含みの配列を取得する
 *
 * @param string $questionnaireKey アンケートキー（Postデータの一つとして必要）
 * @param string $ajaxPostUrl Post先URL（セッション保存キーが含まれるためコントローラーからもらわないとわからない）
 * @return array
 */
	public function getJsPostData($questionnaireKey, $ajaxPostUrl) {
		$postData = array(
			'Frame' => array('id' => Current::read('Frame.id')),
			'Block' => array('id' => Current::read('Block.id')),
			'Questionnaire' => array('key' => $questionnaireKey),
		);
		$tokenFields = Hash::flatten($postData);
		$hiddenFields = $tokenFields;
		$hiddenFields = array_keys($hiddenFields);
		$this->Token->unlockField('QuestionnairePage');

		$tokens = $this->Token->getToken('Questionnaire', $ajaxPostUrl, $tokenFields, $hiddenFields);
		$postData += $tokens;
		return $postData;
	}
}
