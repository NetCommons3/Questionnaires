<?php
/**
 * Questionnaires Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

class QuestionnairesController extends QuestionnairesAppController {

/**
 * use model
 *
 * @var array
 */
public $uses = array(
	'Questionnaires.Questionnaire',
	'Questionnaires.QuestionnaireEntity',
	'Questionnaires.QuestionnairePage',
	'Questionnaires.QuestionnaireQuestion',
	'Questionnaires.QuestionnaireChoice',
	'Questionnaires.QuestionnaireAnswerSummary',
	'Comments.Comment'
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
				'contentEditable' => array('setting', 'edit')
			),
		),
		'Questionnaires.Questionnaires',
	);
/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token',
		'Questionnaires.QuestionnaireUtil'
	);
/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('thanks');
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		//$this->log("DBG: index直後のrequestは以下", QUESTIONNAIRE_DEBUG);
		//$this->log(print_r($this->request,true), QUESTIONNAIRE_DEBUG);

		if ($this->request->isPost()){
			//POSTなのでセッションをのこします
		} else {
			//非POSTなので redirect or 直接URL指定 or 初期表示. なのでセッションをクリア
			$this->Session->delete('Questionnaires');
		}

		// 回答一覧表示に纏わるパラメータ
		$answer_list_cache = $this->_getAnswerListCache();

		$filter = $answer_list_cache['filter'];
		$page = $answer_list_cache['page'];

		//ページネートの準備
		//

		//回答ステータス指定に従い、抽出結果のフィルターにセットする。
		//
		// GET引数に指定があれば、そちらを優先
		if (array_key_exists('answer_status', $this->request->query)) {
			$filter['answer_status'] = $this->request->query['answer_status'];
		}

		//ページ番号指定
		//
		// GET引数に指定があれば、そちらを優先

		if (array_key_exists('page', $this->request->query)) {
			$page['currentPageNumber'] = $this->request->query['page'];
		}

		//ニックネームとuser_idを取り出す
		$username = CakeSession::read('Auth.User.username');
		$username = empty($username) ?  __d('questionnaires','annonymous person') : $username;
		$user_id = CakeSession::read('Auth.User.id');
		$user_id = empty($user_id) ? null : $user_id;

//bbbbbbbbbbb
		// 回答一覧のオフセットの取得
		$offset = ($page['currentPageNumber'] - 1) * $page['displayNumPerPage'];

		// 回答対象のアンケートデータの全件数取得
		$page['totalCount'] = $this->Questionnaire->getQuestionnairesCountForAnswer(
			$this->viewVars['roomId'],
			$this->viewVars['contentEditable'],
			$this->viewVars['roomRoleKey'],
			$user_id,
			$this->Session->id(),
			$filter['answer_status']
		);

		//回答対象のアンケートデータを取り出す
		$answer_questionnaires = array();
		$answer_questionnaires['items'] = $this->Questionnaire->getQuestionnairesForAnswer(
			$this->viewVars['roomId'],
			$this->viewVars['contentEditable'],
			$this->viewVars['roomRoleKey'],
			$user_id,
			$this->Session->id(),
			$filter['answer_status'],
			$offset,
			$page['displayNumPerPage']
		);

		//回答一覧の件数
		$answer_questionnaires['itemCount'] = count($answer_questionnaires['items']);

		$answer_list_cache['page'] = $page;
		$answer_list_cache['filter'] = $filter;

		//$this->log("DBG: answer_list_cache[".print_r($answer_list_cache,true)."]",QUESTIONNAIRE_DEBUG);

		$this->Session->write('Questionnaires.QuestionnairesAnswerList', $answer_list_cache);

		//画面用データをセットする。
		$this->set('questionnaire', array('name'=> $username));
		$this->set('answer_status', $filter['answer_status']);
		$this->set('QuestionnairesAnswerList', $answer_list_cache);
		$this->set('answer_questionnaires', $answer_questionnaires['items']);

		$this->set('page', $page); 	//ページネーション関連情報

	}


/**
 * thanks method
 *
 * @return void
 */
	public function thanks($frameId = 0, $questionnaireId = 0) {

		// 指定されたアンケート情報を取り出す
		$questionnaireEntity = $this->QuestionnaireEntity->getQuestionnaireEntityById($questionnaireId, $this->viewVars['contentEditable']);
		if (!$questionnaireEntity) {
			throw new NotFoundException(__('Invalid questionnaire'));
		}

		$topUrl = $this->Questionnaires->getPageUrl($this->viewVars['frameId']);

		// View変数にセット
		$this->set('questionnaire', $questionnaireEntity);
		$this->set('isDuringTest', $this->Questionnaire->isDuringTest($questionnaireId, $this->viewVars['contentEditable']));
		$this->set('comments', $this->getComments($questionnaireEntity['Questionnaire']));
		$this->set('topUrl', $topUrl);

	}
/**
 * setting method
 *
 * @return void
 */
	public function setting_list()
	{
		// 画面表示に纏わるパラメータをキャッシュより取り出す
		$cache = $this->_getCache();

		// 作成リストデータ準備
		$questionnaire = array();

		// 画面表示パラメータ準備
		$filter = $cache['filter'];
		$page = $cache['page'];

		// 画面表示パラメータに対してGET指定があればパラメータを上書き
		if (array_key_exists('status', $this->request->query)) {
			if(strlen($this->request->query['status']) > 0) {
				$filter['status'] = $this->request->query['status'];
			}
			else {
				unset($filter['status']);
			}
		}
		if (array_key_exists('page', $this->request->query)) {
			$page['currentPageNumber'] = $this->request->query['page'];
		}

		// もしかしたら作成中のアンケートデータがキャッシュにあるかも
		// あったら読みだしておく
		if($this->Session->check('Questionnaires.questionnaire')) {
			$questionnaire = $this->Session->read('Questionnaires.questionnaire');
		}

		// オフセット
		$offset = ($page['currentPageNumber'] - 1) * $page['displayNumPerPage'];

		// 全件数カウント
		$page['totalCount'] = $this->Questionnaire->getQuestionnairesCount(
			$this->viewVars['roomId'],
			$this->viewVars['contentEditable'],
			$filter
		);

		// LIMIT件数 取り出し
		$questionnaires['items'] = $this->Questionnaire->getQuestionnaires(
							$this->viewVars['roomId'],
							$this->viewVars['contentEditable'],
							$filter,
							$offset,
							$page['displayNumPerPage']
		);
		foreach($questionnaires['items'] as &$item) {
			$item['Comments'] = $this->getComments($item['Questionnaire']);
		}
		$questionnaires['itemCount'] = count($questionnaires['items']);

		$cache['filter'] = $filter;
		$cache['page'] = $page;
		$this->Session->write('Questionnaires.QuestionnairesSettingList', $cache);

		$questionnaires['QuestionnairesSettingList'] = $cache;
		$questionnaires['questionnaire'] = $questionnaire;
		//$questionnaires['tabLists'] = $this->_getTabLists('edit');

		$this->set('tabLists', $this->_getTabLists('edit'));
		$this->set('questionnaires', $questionnaires);

		$this->Session->write('Questionnaires.nowUrl', $this->request->url);
	}
	/**
	 * create questionnaire display method
	 *
	 * @return void
	 */
	public function create() {

		// 画面表示に纏わるパラメータ
		$cache = $this->_getCache();

		// 作成中データ
		$questionnaire = array();

		if ($this->request->isPost()) {
			// POSTされたデータを読み取り

			// 選択生成方法設定
			if(isset($this->data['create_option'])) {

				// 空の新規作成
				if($this->data['create_option'] == QUESTIONNAIRE_CREATE_OPT_NEW) {
					$questionnaire = $this->QuestionnaireEntity->getDefaultQuestionnaireEntity();
					$newTitle = $this->data['title'];
					$questionnaire['QuestionnaireEntity']['title'] = $newTitle;
					$cache['newTitle'] = $newTitle;
				}
				// 過去のアンケートのコピー
				else if($this->data['create_option'] == QUESTIONNAIRE_CREATE_OPT_REUSE) {
					$cache['pastQuestionnaireSelect'] = $this->data['questionnaire_id'];
					$questionnaire = $this->QuestionnaireEntity->getQuestionnaireEntityCloneById($this->data['questionnaire_id']);
				}
				$questionnaire['Questionnaire']['block_id'] = $this->viewVars['blockId'];
				$cache['createOption'] = $this->data['create_option'];
			}
			else {
				// 過去データ取り出しか? TODO
			}
			// それをキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $questionnaire);
			$this->Session->write('Questionnaires.QuestionnairesSettingList', $cache);

			// 次の画面へリダイレクト
			$this->redirect('questionnaire_questions/setting_list/'.$this->viewVars['frameId']);

		}
		// 全件 取り出し
		$questionnaires['items'] = $this->Questionnaire->getQuestionnaires(
			$this->viewVars['roomId'],
			$this->viewVars['contentEditable']
			//$filter,
			//$offset,
			//$page['displayNumPerPage']
		);


		$questionnaires['QuestionnairesSettingList'] = $cache;

		$this->set('tabLists', $this->_getTabLists('create'));
		$this->set('questionnaires', $questionnaires);

		$this->Session->write('Questionnaires.nowUrl', $this->request->url);
	}
	/**
	 * 回答用アンケートを取り出す
	 *
	 * @return array
	 */



	/**
	 * get answer list cache method
	 *
	 * @return array
	 */
	private function _getAnswerListCache() {
		// default value

		$answer_list_cache = $this->Session->read('Questionnaires.QuestionnairesAnswerList');

		if(!isset($answer_list_cache['filter'])) {
			$answer_list_cache['filter']['answer_status'] = QUESTIONNAIRE_ANSEWER_VIEW_ALL;
		}
		if(!isset($answer_list_cache['page'])) {
			$answer_list_cache['page']['currentPageNumber'] = 1;
			$answer_list_cache['page']['displayNumPerPage'] = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE;
		}

		return $answer_list_cache;
	}

	/**
	 * get session cache method
	 *
	 * @return array
	 */
	private function _getCache() {

		// default value

		$cache = $this->Session->read('Questionnaires.QuestionnairesSettingList');

		if(!isset($cache['filter'])) {
			$cache['filter'] = array();
		}
		if(!isset($cache['page'])) {
			$cache['page']['currentPageNumber'] = 1;
			$cache['page']['displayNumPerPage'] = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE;
		}
		if(!isset($cache['tab'])) {
			$cache['tab'] = array('list'=>true, 'create'=>false);
		}
		if(!isset($cache['createOption'])) {
			$cache['createOption'] = QUESTIONNAIRE_CREATE_OPT_NEW;
		}
		if(!isset($cache['newTitle'])) {
			$cache['newTitle'] = '';
		}
		if(!isset($cache['pastQuestionnaireSelect'])) {
			$cache['pastQuestionnaireSelect'] = 0;
		}
		return $cache;
	}
	/**
	 * get tab list method
	 *
	 * @return array
	 */
	private function _getTabLists($active) {
		$tabLists = array(
			'edit' => array('href' => '/questionnaires/questionnaires/setting_list/'. $this->viewVars['frameId'],
					'tabTitle' => __d('questionnaires', 'Edit'),
					'class' => ''),
			'create' => array('href' => '/questionnaires/questionnaires/create/'. $this->viewVars['frameId'],
				'tabTitle' => __d('questionnaires', 'Create New'),
				'class' => ''),
		);
		$tabLists[$active]['class'] = 'active';
		return $tabLists;
	}
/**
 * setting method
 *
 * @return void
 */
	public function setting() {
		$topUrl = $this->Questionnaires->getPageUrl($this->viewVars['frameId']);

		if($this->request->isPost()) {

			$postQuestionnaire = $this->request->data;

			// 決定時のボタン種別を調べている
			if ($matches = preg_grep('/^save_\d/', array_keys($this->data))) {
				list(, $status) = explode('_', array_shift($matches));
			} else {
				if ($matches = preg_grep('/^questionnaire_(.*?)/', array_keys($this->data))) {
					list(, $status) = explode('_', array_shift($matches));
				}
			}
			// TODO: statusがない場合
			if(!$matches) {

			}

			$questionnaire = $this->Session->read('Questionnaires.questionnaire');

			$questionnaire = $this->array_merge_recursive_distinct($questionnaire, $postQuestionnaire);

			//
			// status が緊急停止
			//
			if($status == 'stopped') {
				// ステータス更新処理
				$questionnaire['Questionnaire']['questionnaire_status'] = QuestionnairesComponent::STATUS_STOPPED;
				$this->Questionnaire->save($questionnaire['Questionnaire']);
				// メッセージ表示
				$this->Session->setFlash(__('Questionnaire has been stopped.'));
			}

			//
			// status が緊急停止解除
			//
			if($status == 'resumed') {
				// ステータス更新処理
				$questionnaire['Questionnaire']['questionnaire_status'] = QuestionnairesComponent::STATUS_STARTED;
				$this->Questionnaire->save($questionnaire['Questionnaire']);
				// メッセージ表示
				$this->Session->setFlash(__('Questionnaire has been resumed.'));
			}

			//
			// status が削除
			//
			else if($status == 'deleted') {
				// 削除処理
				$this->Questionnaire->deleteQuestionnaire($questionnaire['Questionnaire']['id']);
				// メッセージ表示
				$this->Session->setFlash(__('This Questionnaire has been deleted.'));
				// 次の画面へリダイレクト
				$this->redirect('/'.$topUrl);
			}

			// 指示された編集状態ステータス
			else {
				$questionnaire['QuestionnaireEntity']['status'] = $status;

				// それをDBに書く
				$saveQuestionnaire = $this->Questionnaire->saveQuestionnaire($questionnaire);
//var_dump($saveQuestionnaire);
				$postQuestionnaire['Comment']['plugin_key'] = 'questionnaires';
				$id = isset($saveQuestionnaire['Questionnaire']['Questionnaire']['id']) ? $saveQuestionnaire['Questionnaire']['Questionnaire']['id'] : $saveQuestionnaire['Questionnaire']['id'];
				$postQuestionnaire['Comment']['content_key'] = $id;
				if($this->Comment->save($postQuestionnaire['Comment'])) {

				}
				else {
					$this->log(print_r($this->Comment->validationErrors, true), 'debug');
				}


				// セッションは消す
				/////// 消しちゃいかん。表画面で消すこと　$this->Session->delete('Questionnaires');

				// 次の画面へリダイレクト
				$this->redirect('/'.$topUrl);
			}
		}
		else {
			// redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
			// クエリでアンケートIDの指定がある場合はそちらを優先
			if(!empty($this->request->query['questionnaire_id'])) {
				$selectedQuestionnaireId = $this->request->query['questionnaire_id'];

				// 指定されたアンケートデータを取得
				$questionnaire = $this->QuestionnaireEntity->getQuestionnaireEntityById($selectedQuestionnaireId, $this->viewVars['contentEditable']);
			}
			// クエリがない場合はセッションを確認
			else if($this->Session->check('Questionnaires.questionnaire')) {
				$questionnaire = $this->Session->read('Questionnaires.questionnaire');
			}
			// それもない場合はエラー　TODO
			else {
				var_dump("ERR");
			}

			// それをキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $questionnaire);

//var_dump($questionnaire['QuestionnairePage'][0]);

		}
		$this->set('questionnaire', $questionnaire);
		$this->set('contentStatus', $questionnaire['QuestionnaireEntity']['status']);
		$this->set('comments', $this->getComments($questionnaire['Questionnaire']));
		$this->set('topUrl', $topUrl);
		$this->set('backUrl', '/questionnaires/questionnaire_questions/setting_total/' . $this->viewVars['frameId']);
	}

/**
 * delete method
 * @param int $frameId frames.id
 * @param int $blockId blocks.id
 * @return void
 */
	public function delete($frameId = 0, $blockId = 0) {
		// TODO: 指定されたブロックIDが存在するか確認
			// TODO: 存在しない場合はエラーをThrow

		// TODO: 指定されたブロックに纏わるすべてのデータを削除

		// TODO: DEBUG-CODE
		$questionnaire = true;

		// TODO: もしかして再描画用のデータを再取得する必要があるのか
		$results = array();

		if ($questionnaire) {
			$this->renderJson($results, __d('net_commons', 'Successfully finished.'));
		} else {
			$this->renderJson($results, __d('net_commons', 'Bad Request'), 400);
		}
	}
}
