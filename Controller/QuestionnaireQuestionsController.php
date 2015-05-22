<?php
/**
 * QuestionnaireQuestions Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

class QuestionnaireQuestionsController extends QuestionnairesAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Questionnaires.Questionnaire',
		'Questionnaires.QuestionnairePage',
		'Questionnaires.QuestionnaireQuestion',
		'Questionnaires.QuestionnaireChoice',
		'Comments.Comment',
		'Questionnaires.QuestionnaireValidation',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock', //Use Questionnaire model
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'contentEditable' => array('edit', 'edit_result'),
			),
		),
		'Questionnaires.Questionnaires',
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'NetCommons.BackToPage',
		'NetCommons.Token'
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * edit method
 *
 * @param int $frameId frame id
 * @throws BadRequestException
 * @return void
 */
	public function edit($frameId) {
		if ($this->request->isPost()) {

			$postQuestionnaire = $this->request->data;
			$questionnaire = $this->Session->read('Questionnaires.questionnaire');
			if (!$questionnaire) {
				// セッションタイムアウト
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}

			// アンケートデータに作成されたPost質問データをかぶせる
			// （質問作成画面では質問データ属性全てをPOSTしています）
			$questionnaire['Questionnaire'] = Hash::merge($questionnaire['Questionnaire'], $postQuestionnaire['Questionnaire']);
			$questionnaire['QuestionnairePage'] = $postQuestionnaire['QuestionnairePage'];

			// バリデート
			if (! $this->__validateQuestionnaireQuestionSetting($questionnaire)) {
				$this->__setupViewParameters($questionnaire, '');
				return;
			}

			// それをキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $questionnaire);

			// 次の画面へリダイレクト
			$this->redirect('edit_result/' . $this->viewVars['frameId']);

		} else {

			// redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
			// クエリでアンケートIDの指定がある場合はそちらを優先
			if (!empty($this->request->query['questionnaire_id'])) {
				// 指定されたアンケートデータを取得
				$questionnaire = $this->Questionnaire->find('first', array(
					'conditions' => array(
						'Questionnaire.id' => $this->request->query['questionnaire_id']
					)
				));
			} elseif ($this->Session->check('Questionnaires.questionnaire')) {
				// クエリがない場合はセッションを確認
				$questionnaire = $this->Session->read('Questionnaires.questionnaire');
			} else {
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}

			// それをキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $this->_sorted($questionnaire));

			$this->__setupViewParameters($questionnaire, '');
		}
	}

/**
 * edit_result method
 *
 * @param int $frameId frame id
 * @throws BadRequestException
 * @return void
 */
	public function edit_result($frameId) {
		if ($this->request->isPost()) {

			$postQuestionnaire = $this->request->data;

			$questionnaire = $this->Session->read('Questionnaires.questionnaire');
			if (!$questionnaire) {
				// セッションタイムアウト
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}

			// 集計設定画面では集計に纏わる情報のみがPOSTされる
			$questionnaire = Hash::merge($questionnaire, $postQuestionnaire);

			if (! $this->__validateQuestionnaireResultDisplaySetting($questionnaire)) {
				$this->__setupViewParameters($questionnaire, '/questionnaires/questionnaire_questions/edit/');
				return;
			}

			// それをキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $questionnaire);

			// 次の画面へリダイレクト
			$this->redirect('../questionnaires/edit/' . $this->viewVars['frameId']);
		} else {
			// redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
			// クエリでアンケートIDの指定がある場合はそちらを優先
			if (!empty($this->request->query['questionnaire_id'])) {
				// 指定されたアンケートデータを取得
				$questionnaire = $this->Questionnaire->find('first', array(
					'conditions' => array(
						'Questionnaire.id' => $this->request->query['questionnaire_id']
					)
				));
			} elseif ($this->Session->check('Questionnaires.questionnaire')) {
				// クエリがない場合はセッションを確認
				$questionnaire = $this->Session->read('Questionnaires.questionnaire');
			} else {
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}
			$this->__setupViewParameters($questionnaire, '/questionnaires/questionnaire_questions/edit/');
		}
	}

/**
 * validate questionnaire question setting
 *
 * @param array $data received post data
 * @return bool True on success, false on error
 */
	private function __validateQuestionnaireQuestionSetting($data) {
		$this->QuestionnaireValidation->checkPage($data);
		if ($this->QuestionnaireValidation->validationErrors) {
			$this->log(print_r($this->QuestionnaireValidation->validationErrors, true), 'debug');
			$this->qValidationErrors = $this->QuestionnaireValidation->validationErrors;
			return false;
		} else {
			return true;
		}
	}

/**
 * validate questionnaire result display setting
 *
 * @param array $data received post data
 * @return bool True on success, false on error
 */
	private function __validateQuestionnaireResultDisplaySetting($data) {
		$errors = array();

		$this->Questionnaire->set($data);
		$this->Questionnaire->validates(array(
			'fieldList' => array(
				'is_total_show',
				'total_show_timing',
				'total_comment',
			)
		));
		if ($this->Questionnaire->validationErrors) {
			$errors['Questionnaire'] = $this->Questionnaire->validationErrors;
		}

		foreach ($data['QuestionnairePage'] as $pIndex => $p) {
			foreach ($p['QuestionnaireQuestion'] as $qqIndex => $qq) {

				$this->QuestionnaireQuestion->set($qq);
				$this->QuestionnaireQuestion->validates(array(
					'fieldList' => array(
						'is_result_display',
						'result_display_type',
					)
				));
				if ($this->QuestionnaireQuestion->validationErrors) {
					$errors['QuestionnairePage'][$pIndex]['QuestionnaireQuestion'][$qqIndex] = $this->QuestionnaireQuestion->validationErrors;
				}

				if (isset($qq['QuestionnaireChoice'])) {
					foreach ($qq['QuestionnaireChoice'] as $cIndex => $c) {
						$this->QuestionnaireChoice->set($c);
						$this->QuestionnaireChoice->validates(array(
							'fieldList' => array(
								'graph_color',
							)
						));
						if ($this->QuestionnaireChoice->validationErrors) {
							$errors['QuestionnairePage'][$pIndex]['QuestionnaireQuestion'][$qqIndex]['QuestionnaireChoice'][$cIndex] = $this->QuestionnaireChoice->validationErrors;
						}
					}
				}
			}
		}
		if ($errors) {
			$this->qValidationErrors = $errors;
			return false;
		} else {
			return true;
		}
	}

/**
 * __setupViewParameters method
 *
 * @param array $questionnaire アンケートデータ
 * @param string $backUrl BACKボタン押下時の戻るパス
 * @return void
 */
	private function __setupViewParameters($questionnaire, $backUrl) {
		$this->set('questionnaire', $this->_changeBooleansToNumbers($this->_sorted($questionnaire)));
		$this->set('questionnaireValidationErrors', $this->qValidationErrors);
		$this->set('backUrl', $backUrl . $this->viewVars['frameId']);
		$this->set('questionTypeOptions', $this->Questionnaires->getQuestionTypeOptionsWithLabel());
		$this->set('newPageLabel', __d('questionnaires', 'page'));
		$this->set('newQuestionLabel', __d('questionnaires', 'New Question'));
		$this->set('newChoiceLabel', __d('questionnaires', 'new choice'));
		$this->set('newChoiceColumnLabel', __d('questionnaires', 'new column choice'));
		$this->set('newChoiceOtherLabel', __d('questionnaires', 'other choice'));
	}

}