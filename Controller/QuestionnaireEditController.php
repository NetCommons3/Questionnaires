<?php
/**
 * QuestionnaireEdit Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('MailSetting', 'Mails.Model');

/**
 * QuestionnaireEditController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireEditController extends QuestionnairesAppController {

/**
 * edit questionnaire session key
 *
 * @var int
 */
	const	QUESTIONNAIRE_EDIT_SESSION_INDEX = 'Questionnaires.questionnaireEdit.';

/**
 * layout
 *
 * @var array
 */
	public $layout = '';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'edit,edit_question,edit_result,delete' => 'content_creatable',
			),
		),
		'Questionnaires.Questionnaires',
		'NetCommons.NetCommonsTime',
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'Workflow.Workflow',
		'NetCommons.TitleIcon',
		'Questionnaires.QuestionEdit',
		'NetCommons.Wizard' => array(
			'navibar' => array(
				'edit_question' => array(
					'url' => array(
						'controller' => 'questionnaire_edit',
						'action' => 'edit_question',
					),
					'label' => array('questionnaires', 'Set questions'),
				),
				'edit_result' => array(
					'url' => array(
						'controller' => 'questionnaire_edit',
						'action' => 'edit_result',
					),
					'label' => array('questionnaires', 'Set result display'),
				),
				'edit' => array(
					'url' => array(
						'controller' => 'questionnaire_edit',
						'action' => 'edit',
					),
					'label' => array('questionnaires', 'Set questionnaire'),
				),
			),
			'cancelUrl' => null
		),
		'Wysiwyg.Wysiwyg',
		);

/**
 * target questionnaire　
 *
 */
	protected $_questionnaire = null;

/**
 * session index
 *
 */
	protected $_sessionIndex = null;

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		$questionnaireKey = $this->_getQuestionnaireKeyFromPass();

		// セッションインデックスパラメータ
		$sessionName =
			self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex();

		if ($this->request->is('post') || $this->request->is('put')) {
			// ウィザード画面なのでセッションに記録された前画面データが必要
			$this->_questionnaire = $this->Session->read($sessionName);
			if (! $this->_questionnaire) {
				// セッションタイムアウトの場合
				return;
			}
		} else {
			// redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
			// セッションに記録された値がある場合はそちらを優先
			if ($this->Session->check($sessionName)) {
				$this->_questionnaire = $this->Session->read($sessionName);
			} elseif (! empty($questionnaireKey)) {
				// アンケートキーの指定がある場合は過去データ編集と判断
				// 指定されたアンケートデータを取得
				// NetCommonsお約束：履歴を持つタイプのコンテンツデータはgetWorkflowContentsで取り出す
				$this->_questionnaire = $this->Questionnaire->getWorkflowContents('first', array(
					'recursive' => 0,
					'conditions' => array(
						$this->Questionnaire->alias . '.key' => $questionnaireKey
					)
				));
			}
		}
		if ($questionnaireKey) {
			// NetCommonsお約束：編集の場合には改めて編集権限をチェックする必要がある
			// getWorkflowContentsはとりあえず自分が「見られる」コンテンツデータを取ってきてしまうので
			if (! $this->Questionnaire->canEditWorkflowContent($this->_questionnaire)) {
				$this->_questionnaire = null;
			}
		}
		// ここへは設定画面の一覧から来たのか、一般画面の一覧から来たのか
		$this->_decideSettingLayout();
	}
/**
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();

		//ウィザード
		foreach ($this->helpers['NetCommons.Wizard']['navibar'] as &$actions) {
			$urlParam = $actions['url'];
			$urlParam = Hash::merge($urlParam, $this->request->params['named']);
			foreach ($this->request->params['pass'] as $passParam) {
				$urlParam[$passParam] = null;
			}
			$actions['url'] = $urlParam;
		}
	}

/**
 * edit question method
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit_question() {
		// 処理対象のアンケートデータが見つかっていない場合、エラー
		if (empty($this->_questionnaire)) {
			$this->throwBadRequest();
			return false;
		}

		// Postの場合
		if ($this->request->is('post') || $this->request->is('put')) {
			$postQuestionnaire = $this->request->data;
			// アンケートデータに作成されたPost質問データをかぶせる
			// （質問作成画面では質問データ属性全てをPOSTしているのですり替えでOK）
			$questionnaire = $this->_questionnaire;
			$questionnaire['Questionnaire'] = Hash::merge(
				$this->_questionnaire['Questionnaire'],
				$postQuestionnaire['Questionnaire']);

			// 発行後のアンケートは質問情報は書き換えない
			// 未発行の場合はPostデータを上書き設定して
			if ($this->Questionnaire->hasPublished($questionnaire) == 0) {
				$questionnaire['QuestionnairePage'] = $postQuestionnaire['QuestionnairePage'];
			} else {
				$this->Questionnaire->clearQuestionnaireId($questionnaire, true);
				// booleanの値がPOST時と同じようになるように調整
				$questionnaire['QuestionnairePage'] =
					QuestionnairesAppController::changeBooleansToNumbers(
						$questionnaire['QuestionnairePage']);
			}
			// バリデート
			$this->Questionnaire->set($questionnaire);
			if (! $this->Questionnaire->validates(
				array('validate' => QuestionnairesComponent::QUESTIONNAIRE_VALIDATE_TYPE))) {
				$this->__setupViewParameters($questionnaire, '');
				return;
			}

			// バリデートがOKであればPOSTで出来上がったデータをセッションキャッシュに書く
			$this->Session->write(
				self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_sessionIndex,
				$questionnaire);

			// 次の画面へリダイレクト
			$this->redirect($this->_getActionUrl('edit_result'));
		} else {
			// アンケートデータが取り出せている場合、それをキャッシュに書く
			$this->Session->write(
				self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex(),
				$this->_sorted($this->_questionnaire));
			$this->__setupViewParameters($this->_questionnaire, '');
		}
	}

/**
 * edit_result
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit_result() {
		// 処理対象のアンケートデータが見つかっていない場合、エラー
		if (empty($this->_questionnaire)) {
			$this->throwBadRequest();
			return false;
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$postQuestionnaire = $this->request->data;

			// 集計設定画面では集計に纏わる情報のみがPOSTされるので安心してマージ
			$questionnaire = Hash::merge($this->_questionnaire, $postQuestionnaire);
			// バリデート
			$this->Questionnaire->set($questionnaire);
			if (! $this->Questionnaire->validates(
				array('validate' => QuestionnairesComponent::QUESTIONNAIRE_VALIDATE_TYPE))) {
				$this->__setupViewParameters($questionnaire, $this->_getActionUrl('edit_question'));
				return;
			}
			// それをキャッシュに書く
			$this->Session->write(
				self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex(),
				$questionnaire);

			// 次の画面へリダイレクト
			$this->redirect($this->_getActionUrl('edit'));

		} else {
			$this->Session->write(
				self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex(),
				$this->_questionnaire);
			$this->__setupViewParameters($this->_questionnaire, $this->_getActionUrl('edit_question'));
		}
	}

/**
 * edit method
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit() {
		// 処理対象のアンケートデータが見つかっていない場合、エラー
		if (empty($this->_questionnaire)) {
			$this->throwBadRequest();
			return;
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$postQuestionnaire = $this->request->data;

			$beforeStatus = $this->_questionnaire['Questionnaire']['status'];

			// 設定画面ではアンケート本体に纏わる情報のみがPOSTされる
			$questionnaire = Hash::merge($this->_questionnaire, $postQuestionnaire);

			// 指示された編集状態ステータス
			$questionnaire['Questionnaire']['status'] = $this->Workflow->parseStatus();

			// それをDBに書く
			$saveQuestionnaire = $this->Questionnaire->saveQuestionnaire($questionnaire);
			// エラー
			if ($saveQuestionnaire == false) {
				$questionnaire['Questionnaire']['status'] = $beforeStatus;
				$this->__setupViewParameters($questionnaire, $this->_getActionUrl('edit_result'));
				return;
			}

			// 成功時 セッションに書き溜めた編集情報を削除
			$this->Session->delete(
				self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex());

			if ($this->layout == 'NetCommons.setting') {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
			} else {
				// 回答画面（詳細）へリダイレクト
				if ($saveQuestionnaire['Questionnaire']['status'] == WorkflowComponent::STATUS_PUBLISHED) {
					$action = 'view';
				} else {
					$action = 'test_mode';
				}
				$urlArray = array(
					'controller' => 'questionnaire_answers',
					'action' => $action,
					Current::read('Block.id'),
					$this->_getQuestionnaireKey($saveQuestionnaire),
					'frame_id' => Current::read('Frame.id'),
				);
				$this->redirect(NetCommonsUrl::actionUrl($urlArray));
			}
			return;
		} else {
			// 指定されて取り出したアンケートデータをセッションキャッシュに書く
			$this->Session->write(
				$this->_getQuestionnaireEditSessionIndex(),
				$this->_questionnaire);
			$this->__setupViewParameters($this->_questionnaire, $this->_getActionUrl('edit_result'));
		}
		$comments = $this->Questionnaire->getCommentsByContentKey(
			$this->_questionnaire['Questionnaire']['key']);
		$this->set('comments', $comments);
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete() {
		if (! $this->request->is('delete')) {
			$this->throwBadRequest();
			return;
		}

		//削除権限チェック
		if (! $this->Questionnaire->canDeleteWorkflowContent($this->_questionnaire)) {
			$this->throwBadRequest();
			return;
		}

		// 削除処理
		if (! $this->Questionnaire->deleteQuestionnaire($this->request->data)) {
			$this->throwBadRequest();
			return;
		}

		$this->Session->delete(self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_sessionIndex);

		if ($this->layout == 'NetCommons.setting') {
			$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
		} else {
			$this->redirect(NetCommonsUrl::backToPageUrl());
		}
	}

/**
 * cancel method
 *
 * @return void
 */
	public function cancel() {
		$this->Session->delete(self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_sessionIndex);
		$this->redirect(NetCommonsUrl::backToPageUrl());
	}
/**
 * _getActionUrl method
 *
 * @param string $method 遷移先アクション名
 * @return void
 */
	protected function _getActionUrl($method) {
		$urlArray = array(
			'controller' => Inflector::underscore($this->name),
			'action' => $method,
			Current::read('Block.id'),
			$this->_getQuestionnaireKey($this->_questionnaire),
			'frame_id' => Current::read('Frame.id'),
			's_id' => $this->_getQuestionnaireEditSessionIndex()
		);
		if ($this->layout == 'NetCommons.setting') {
			$urlArray['q_mode'] = 'setting';
		}
		return NetCommonsUrl::actionUrl($urlArray);
	}
/**
 * __setupViewParameters method
 *
 * @param array $questionnaire アンケートデータ
 * @param string $backUrl BACKボタン押下時の戻るパス
 * @return void
 */
	private function __setupViewParameters($questionnaire, $backUrl) {
		$isPublished = $this->Questionnaire->hasPublished($questionnaire);

		// エラーメッセージはページ、質問、選択肢要素のそれぞれの場所に割り当てる
		$this->NetCommons->handleValidationError($this->Questionnaire->validationErrors);
		$flatError = Hash::flatten($this->Questionnaire->validationErrors);
		$newFlatError = array();
		foreach ($flatError as $key => $val) {
			if (preg_match('/^(.*)\.(.*)\.(.*)$/', $key, $matches)) {
				$newFlatError[$matches[1] . '.error_messages.' . $matches[2] . '.' . $matches[3]] = $val;
			}
		}
		$questionnaire = Hash::merge($questionnaire, Hash::expand($newFlatError));
		$questionnaire = $this->NetCommonsTime->toUserDatetimeArray(
			$questionnaire,
			array(
				'Questionnaire.answer_start_period',
				'Questionnaire.answer_end_period',
				'Questionnaire.total_show_start_period',
		));

		$this->set('postUrl', array('url' => $this->_getActionUrl($this->action)));
		if ($this->layout == 'NetCommons.setting') {
			$this->set('cancelUrl', array('url' => NetCommonsUrl::backToIndexUrl('default_setting_action')));
		} else {
			$this->set('cancelUrl', array('url' => NetCommonsUrl::backToPageUrl()));
		}
		$this->set('deleteUrl', array('url' => $this->_getActionUrl('delete')));

		$this->set('questionTypeOptions', $this->Questionnaires->getQuestionTypeOptionsWithLabel());
		$this->set('newPageLabel', __d('questionnaires', 'page'));
		$this->set('newQuestionLabel', __d('questionnaires', 'New Question'));
		$this->set('newChoiceLabel', __d('questionnaires', 'new choice'));
		$this->set('newChoiceColumnLabel', __d('questionnaires', 'new column choice'));
		$this->set('newChoiceOtherLabel', __d('questionnaires', 'other choice'));
		$this->set('isPublished', $isPublished);

		$this->request->data = $questionnaire;
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}
}
