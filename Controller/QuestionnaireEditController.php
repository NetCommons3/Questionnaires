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
 * post QuestionnaireQuestions session key
 *
 * @var int
 */
	const	QUESTIONNAIRE_POST_QUESTION_SESSION_INDEX = 'Questionnaires.postQuestionnaireQuestion.';

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
		'Mails.MailSetting'
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
		'NetCommons.Token',
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
 * @return void
 */
	public function edit_question() {
		$this->_editMidstream('', 'edit_result');
	}

/**
 * edit_result
 *
 * @return void
 */
	public function edit_result() {
		$this->_editMidstream('edit_question', 'edit');
	}
/**
 * _edit_midstream
 *
 * @param string $prevAction 前のアクション名
 * @param string $redirectAction 次に遷移するアクション名
 * @throws BadRequestException
 * @return void
 */
	protected function _editMidstream($prevAction, $redirectAction) {
		if (empty($this->_questionnaire)) {
			$this->throwBadRequest();
			return false;
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$postQuestionnaire = $this->request->data;
			if (! empty($postQuestionnaire['QuestionnairePage'])) {
				if ($this->request->is('ajax')) {
					$this->_postPage($postQuestionnaire);
					$this->view = 'edit_json';
					return;
				}
			} else {
				// バリデート
				$questionnaire = $this->_questionnaire;

				// 蓄積データを取り出す
				$accumSessName =
					self::QUESTIONNAIRE_POST_QUESTION_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex();
				$accumulationPost = $this->Session->read($accumSessName);

				// 取り出し後、蓄積データは消す
				$this->Session->delete($accumSessName);

				// 集計結果編集画面からのPOSTの場合は無条件で上書き
				// 質問編集画面からのPOSTは、発行後は書き換えない 未発行の場合はPostデータを上書き設定
				if ($this->action == 'edit_result' || $this->Questionnaire->hasPublished($questionnaire) == 0) {
					$questionnaire['QuestionnairePage'] = $accumulationPost['QuestionnairePage'];
				}

				$this->Questionnaire->set($questionnaire);
				if (! $this->Questionnaire->validates(
					array('validate' => QuestionnairesComponent::QUESTIONNAIRE_VALIDATE_TYPE))) {
					$this->__setupViewParameters($questionnaire, $this->_getActionUrl($prevAction));
					return;
				}
				// それをキャッシュに書く
				$this->Session->write(
					self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex(),
					$questionnaire);

				// 次の画面へリダイレクト
				$this->redirect($this->_getActionUrl($redirectAction));
			}

		} else {
			$this->Session->write(
				self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex(),
				$this->_questionnaire);
			$this->__setupViewParameters($this->_questionnaire, $this->_getActionUrl($prevAction));

			// Getの場合、蓄積データは消す
			$accumSessName =
				self::QUESTIONNAIRE_POST_QUESTION_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex();
			$this->Session->delete($accumSessName);
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
				// 回答画面（詳細）へリダイレクト×
				// 発行したときはページの最初に戻るべきとの指摘アリ
				if ($saveQuestionnaire['Questionnaire']['status'] == WorkflowComponent::STATUS_PUBLISHED) {
					$this->redirect(NetCommonsUrl::backToPageUrl());
				} else {
					$action = 'test_mode';
					$urlArray = array(
						'controller' => 'questionnaire_answers',
						'action' => $action,
						Current::read('Block.id'),
						$this->_getQuestionnaireKey($saveQuestionnaire),
						'frame_id' => Current::read('Frame.id'),
					);
					$this->redirect(NetCommonsUrl::actionUrl($urlArray));
				}
			}
			return;
		} else {
			// 指定されて取り出したアンケートデータをセッションキャッシュに書く
			$this->Session->write(
				self::QUESTIONNAIRE_EDIT_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex(),
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
 * _postPage
 *
 * 分割送信されている編集された質問情報をまとめ上げる
 *
 * @param array $postPage 分割送信された質問情報（１質問ずつ送信）
 * @return void
 */
	protected function _postPage($postPage) {
		// 分割されて送られてくるデータをひたすら蓄積する
		$accumSessName =
			self::QUESTIONNAIRE_POST_QUESTION_SESSION_INDEX . $this->_getQuestionnaireEditSessionIndex();
		$accumulationPost = $this->Session->read($accumSessName);
		if (! $accumulationPost) {
			$accumulationPost = array();
		}

		// JSからPOSTされたデータはtrue, false画文字列で来てしまうので
		$postPage = $this->_changeBoolean($postPage);

		// マージ
		$accumulationPost = Hash::merge($accumulationPost, $postPage);

		// マージ結果をセッションに記録
		$this->Session->write($accumSessName, $accumulationPost);
	}

/**
 * _changeBoolean
 *
 * JSから送られるデータはbooleanの値のものがtrueとかfalseの文字列データで来てしまうので
 * 正式なbool値に変換しておく
 *
 * @param array $orig 元データ
 * @return array 変換後の配列データ
 */
	protected function _changeBoolean($orig) {
		$new = [];

		foreach ($orig as $key => $value) {
			if (is_array($value)) {
				$new[$key] = $this->_changeBoolean($value);
			} else {
				if ($value === 'true') {
					$value = true;
				}
				if ($value === 'false') {
					$value = false;
				}
				$new[$key] = $value;
			}
		}
		return $new;
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

		$ajaxPostUrl = $this->_getActionUrl($this->action);
		$this->set('ajaxPostUrl', $ajaxPostUrl);
		$this->set('postUrl', array('url' => $ajaxPostUrl));
		if ($this->layout == 'NetCommons.setting') {
			$this->set('cancelUrl', array('url' => NetCommonsUrl::backToIndexUrl('default_setting_action')));
		} else {
			$this->set('cancelUrl', array('url' => NetCommonsUrl::backToPageUrl()));
		}
		$this->set('deleteUrl', array('url' => $this->_getActionUrl('delete')));
		// これを使うのは集計結果編集画面だけなので固定体に書いています
		$this->set('prevUrl', array('url' => $this->_getActionUrl('edit_question')));

		$this->set('questionTypeOptions', $this->Questionnaires->getQuestionTypeOptionsWithLabel());
		$this->set('isPublished', $isPublished);
		$this->set('questionnaireKey', Hash::get($questionnaire, 'Questionnaire.key'));

		$isMailSetting = $this->MailSetting->getMailSetting(
			array(
				'plugin_key' => 'questionnaires',
				'block_key' => Current::read('Block.key')
			)
		);
		$isMailSetting = Hash::get($isMailSetting, 'MailSetting.is_mail_send');
		$this->set('isMailSetting', $isMailSetting);

		$this->request->data = $questionnaire;
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}
}
