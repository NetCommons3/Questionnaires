<?php
/**
 * Questionnaires FrameSettingsController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BlocksController', 'Questionnaires.Controller');

class QuestionnaireFrameSettingsController extends BlocksController {

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Blocks.Block',
		'Frames.Frame',
		'Questionnaires.Questionnaire',
		'Questionnaires.QuestionnaireFrameSettings'
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
				'pageEditable' => array('edit')
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
		'NetCommons.Date',
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
		$this->Auth->deny('index');

		$results = $this->camelizeKeyRecursive($this->NetCommonsFrame->data);
		$this->set($results);

		//タブの設定
		$this->initTabs('frame_settings');
	}

/**
 * content_list
 *
 * @return void
 */
	/*
	public function content_list() {
		// 画面表示に纏わるパラメータをキャッシュより取り出す
		$cache = $this->__getCache();

		// 作成リストデータ準備
		$questionnaire = array();

		// 画面表示パラメータ準備
		$filter = $cache['filter'];
		$page = $cache['page'];

		// 画面表示パラメータに対してGET指定があればパラメータを上書き
		if (array_key_exists('status', $this->request->query)) {
			if (strlen($this->request->query['status']) > 0) {
				$filter['status'] = $this->request->query['status'];
			} else {
				unset($filter['status']);
			}
		}
		if (array_key_exists('page', $this->request->query)) {
			$page['currentPageNumber'] = $this->request->query['page'];
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
		$questionnaires['itemCount'] = count($questionnaires['items']);

		$cache['filter'] = $filter;
		$cache['page'] = $page;
		$this->Session->write('Questionnaires.QuestionnaireFrameSettingsContentList', $cache);

		$questionnaires['QuestionnaireFrameSettingsContentList'] = $cache;
		$questionnaires['questionnaire'] = $questionnaire;

		$this->set('tabLists', $this->__getTabLists('list'));
		$this->set('questionnaires', $questionnaires);
		$this->set('page', $page);
		$this->set('filter', $filter);
		$this->Session->write('Questionnaires.nowUrl', $this->request->url);
	}
	*/

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		// 全件 取り出し
		$questionnaires = $this->Questionnaire->getQuestionnairesList(
			$this->viewVars,
			$this->Session->id(),
			$this->Auth->user('id'),
			array(),
			'modified DESC',
			0,
			1000
		);

		$frame = $this->QuestionnaireFrameSettings->find('first', array(
			'conditions' => array(
				'frame_key' => $this->viewVars['frameKey'],
			),
			'order' => 'QuestionnaireFrameSettings.id DESC'
		));
		if (!$frame) {
			$frame = array(
				'QuestionnaireFrameSettings' => array(
					'display_type' => QuestionnairesComponent::DISPLAY_TYPE_LIST,
					'display_num_per_page' => QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
					'sort_type' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
				)
			);
		}

		$this->set('questionnaireFrameSettings', $frame['QuestionnaireFrameSettings']);
		$this->set('questionnaires', $questionnaires);
		$this->set('topUrl', $this->Questionnaires->getPageUrl($this->viewVars['frameId']));
	}
}