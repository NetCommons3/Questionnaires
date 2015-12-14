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

/**
 * QuestionnaireEditController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireEditController extends QuestionnairesAppController {

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
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'Workflow.Workflow',
		'Questionnaires.QuestionEdit'
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

		if ($this->request->isPost() || $this->request->isPut()) {
			// ウィザード画面なのでセッションに記録された前画面データが必要
			$this->_questionnaire = $this->Session->read('Questionnaires.questionnaire');
			if (! $this->_questionnaire) {
				// セッションタイムアウトの場合
				return;
			}
		} else {
			// redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
			// セッションに記録された値がある場合はそちらを優先
			if ($this->Session->check('Questionnaires.questionnaire')) {
				$this->_questionnaire = $this->Session->read('Questionnaires.questionnaire');
			} elseif (!empty($questionnaireKey)) {
				// アンケートキーの指定がある場合は過去データ編集と判断
				// 指定されたアンケートデータを取得
				// NetCommonsお約束：履歴を持つタイプのコンテンツデータはgetWorkflowContentsで取り出す
				$this->_questionnaire = $this->Questionnaire->getWorkflowContents('first', array(
					'recursive' => 0,
					'conditions' => array(
						$this->Questionnaire->alias . '.key' => $questionnaireKey
					)
				));
				// NetCommonsお約束：編集の場合には改めて編集権限をチェックする必要がある
				// getWorkflowContentsはとりあえず自分が「見られる」コンテンツデータを取ってきてしまうので
				if (! $this->Questionnaire->canEditWorkflowContent($this->_questionnaire)) {
					$this->_questionnaire = null;
				}
			}
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
		if ($this->request->isPost()) {

			$postQuestionnaire = $this->request->data;

			// アンケートデータに作成されたPost質問データをかぶせる
			// （質問作成画面では質問データ属性全てをPOSTしているのですり替えでOK）
			$questionnaire = $this->_questionnaire;
			$questionnaire['Questionnaire'] = Hash::merge($this->_questionnaire['Questionnaire'], $postQuestionnaire['Questionnaire']);

			// 発行後のアンケートは質問情報は書き換えない
			// 未発行の場合はPostデータを上書き設定して
			if ($this->Questionnaire->hasPublished($questionnaire) == 0) {
				$questionnaire['QuestionnairePage'] = $postQuestionnaire['QuestionnairePage'];
			} else {
				// booleanの値がPOST時と同じようになるように調整
				$questionnaire['QuestionnairePage'] = QuestionnairesAppController::changeBooleansToNumbers($questionnaire['QuestionnairePage']);
			}

			// バリデート
			$this->Questionnaire->set($questionnaire);
			if (! $this->Questionnaire->validates(array('validate' => 'duringSetup'))) {
				$this->__setupViewParameters($questionnaire, '');
				return;
			}

			// バリデートがOKであればPOSTで出来上がったデータをセッションキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $questionnaire);

			// 次の画面へリダイレクト
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'questionnaire_edit',
				'action' => 'edit_result',
				Current::read('Block.id'),
				$this->_getQuestionnaireKey($questionnaire),
				'frame_id' => Current::read('Frame.id')
			)));

		} else {
			// アンケートデータが取り出せている場合、それをキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $this->_sorted($this->_questionnaire));
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
			return;
		}

		if ($this->request->isPost()) {

			$postQuestionnaire = $this->request->data;

			// 集計設定画面では集計に纏わる情報のみがPOSTされるので安心してマージ
			$questionnaire = Hash::merge($this->_questionnaire, $postQuestionnaire);
			// バリデート
			$this->Questionnaire->set($questionnaire);
			if (! $this->Questionnaire->validates(array('validate' => 'duringSetup'))) {
				$this->__setupViewParameters($questionnaire,
					NetCommonsUrl::actionUrl(array(
						'controller' => 'questionnaire_edit',
						'action' => 'edit_question',
						Current::read('Block.id'),
						$this->_getQuestionnaireKey($questionnaire),
						'frame_id' => Current::read('Frame.id')
					)));
				return;
			}
			// それをキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $questionnaire);

			// 次の画面へリダイレクト
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'questionnaire_edit',
				'action' => 'edit',
				Current::read('Block.id'),
				$this->_getQuestionnaireKey($questionnaire),
				'frame_id' => Current::read('Frame.id')
			)));

		} else {
			$this->Session->write('Questionnaires.questionnaire', $this->_questionnaire);
			$this->__setupViewParameters($this->_questionnaire,
				NetCommonsUrl::actionUrl(array(
					'controller' => 'questionnaire_edit',
					'action' => 'edit_question',
					Current::read('Block.id'),
					$this->_getQuestionnaireKey($this->_questionnaire),
					'frame_id' => Current::read('Frame.id')
				)));
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

		if ($this->request->isPost() || $this->request->isPut()) {
			$postQuestionnaire = $this->request->data;

			$beforeStatus = $this->_questionnaire['Questionnaire']['status'];

			// 設定画面ではアンケート本体に纏わる情報のみがPOSTされる
			$questionnaire = Hash::merge($this->_questionnaire, $postQuestionnaire);

			// 指示された編集状態ステータス
			$questionnaire['Questionnaire']['status'] = $this->Workflow->parseStatus();

			// それをDBに書く
			$saveQuestionnaire = $this->Questionnaire->saveQuestionnaire($questionnaire);
			if ($saveQuestionnaire == false) {
				$questionnaire['Questionnaire']['status'] = $beforeStatus;
				$this->__setupViewParameters($questionnaire,
					NetCommonsUrl::actionUrl(array(
						'controller' => 'questionnaire_edit',
						'action' => 'edit_result',
						Current::read('Block.id'),
						$this->_getQuestionnaireKey($questionnaire),
						'frame_id' => Current::read('Frame.id')
					)));
				return;
			}

			/////// セッションはまだ消しちゃいけない。表画面で消すこと　$this->Session->delete('Questionnaires');

			// ページトップへリダイレクト
			$this->redirect(NetCommonsUrl::backToPageUrl());

		} else {
			// 指定されて取り出したアンケートデータをセッションキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $this->_questionnaire);
			$this->__setupViewParameters($this->_questionnaire,
				NetCommonsUrl::actionUrl(array(
					'controller' => 'questionnaire_edit',
					'action' => 'edit_result',
					Current::read('Block.id'),
					$this->_getQuestionnaireKey($this->_questionnaire),
					'frame_id' => Current::read('Frame.id')
				)));
		}
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete() {
		if (! $this->request->isDelete()) {
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

		$this->redirect(NetCommonsUrl::backToPageUrl());
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

		$this->set('backUrl', $backUrl);
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

		// ? FUJI いる？
		//$this->set('contentStatus', $questionnaire['Questionnaire']['status']);
		//$this->set('comments', $this->Questionnaire->getCommentsByContentKey($questionnaire['Questionnaire']['key']));
	}
}
