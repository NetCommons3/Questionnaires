<?php
/**
 * QuestionnairesAdd Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * QuestionnairesAddController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireAddController extends QuestionnairesAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Files.FileModel',					// FileUpload
		'PluginManager.Plugin',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Files.FileUpload',					// FileUpload
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'add' => 'content_creatable',
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
		'Questionnaires.QuestionnaireStatusLabel',
		'Questionnaires.QuestionnaireUtil'
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		// ここへは設定画面の一覧から来たのか、一般画面の一覧から来たのか
		$this->_decideSettingLayout();
	}

/**
 * add questionnaire display method
 *
 * @return void
 */
	public function add() {
		// NetCommonsお約束：投稿権限のある人物しかこのアクションにアクセスできない
		// それは$componentsの組み込みでallow => add => content_creatableで担保される
		// アクション処理内でチェックする必要はない

		// POSTされたデータを読み取り
		if ($this->request->is('post')) {
			// Postデータをもとにした新アンケートデータの取得をModelに依頼する
			$actionModel = ClassRegistry::init('Questionnaires.ActionQuestionnaireAdd', 'true');

			if ($questionnaire = $actionModel->createQuestionnaire($this->request->data)) {
				$tm = $this->_getQuestionnaireEditSessionIndex();
				// 作成中アンケートデータをセッションキャッシュに書く
				$this->Session->write('Questionnaires.questionnaireEdit.' . $tm, $questionnaire);

				// 次の画面へリダイレクト
				$urlArray = array(
					'controller' => 'questionnaire_edit',
					'action' => 'edit_question',
					Current::read('Block.id'),
					'frame_id' => Current::read('Frame.id'),
					's_id' => $tm,
				);
				if ($this->layout == 'NetCommons.setting') {
					$urlArray['q_mode'] = 'setting';
				}
				$this->redirect(NetCommonsUrl::actionUrl($urlArray));
				return;
			} else {
				// データに不備があった場合
				$this->NetCommons->handleValidationError($actionModel->validationErrors);
			}
		}

		// 過去データ 取り出し
		$pastQuestionnaires = $this->Questionnaire->find('all',
			array(
				'fields' => array(
					'id', 'title', 'status', 'answer_timing', 'answer_start_period', 'answer_end_period',
				),
				'conditions' => $this->Questionnaire->getBaseCondition(),
				'offset' => 0,
				'limit' => 1000,
				'recursive' => -1,
				'order' => array('Questionnaire.modified DESC'),
			));
		$this->set('pastQuestionnaires', $pastQuestionnaires);

		if ($this->layout == 'NetCommons.setting') {
			$this->set('cancelUrl', NetCommonsUrl::backToIndexUrl('default_setting_action'));
		} else {
			$this->set('cancelUrl', NetCommonsUrl::backToPageUrl());
		}
		//
		// NetCommonsお約束：投稿のデータはrequest dataに設定する
		//
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		// create_optionが未設定のときは初期値として「ＮＥＷ」を設定する
		if (! $this->request->data('ActionQuestionnaireAdd.create_option')) {
			$this->request->data(
				'ActionQuestionnaireAdd.create_option',
				QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW);
		}
	}
}