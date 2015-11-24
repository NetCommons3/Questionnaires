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
		'Questionnaires.QuestionnairesDownload',
		'Questionnaires.QuestionnairesWysIsWyg',
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
 * add questionnaire display method
 *
 * @return void
 */
	public function add() {
		// NetCommonsお約束：
		//投稿権限チェック
		if (! $this->Questionnaire->canCreateWorkflowContent()) {
			$this->throwBadRequest();
			return false;
		}

		// POSTされたデータを読み取り
		if ($this->request->isPost()) {
			// Postデータをもとにした新アンケートデータの取得をModelに依頼する
			$actionModel = ClassRegistry::init('Questionnaires.ActionQuestionnaireAdd', 'true');
			if ($questionnaire = $actionModel->createQuestionnaire($this->request->data)) {
				// 作成中アンケートデータをセッションキャッシュに書く
				$this->Session->write('Questionnaires.questionnaire', $questionnaire);

				// 次の画面へリダイレクト
				$this->redirect(NetCommonsUrl::actionUrl(array(
					'controller' => 'questionnaire_edit',
					'action' => 'edit_question',
					Current::read('Block.id'),
					'frame_id' => Current::read('Frame.id')
				)));
				return;
			} else {
				// データに不備があった場合
				$this->NetCommons->handleValidationError($actionModel->validationErrors);
			}
		} else {
			// 初期表示の場合は、create_optionは初期値として「ＮＥＷ」を設定する
			$this->request->data['ActionQuestionnaireAdd']['create_option'] = QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW;
		}

		// 過去データ 取り出し
		$pastQuestionnaires = $this->Questionnaire->getQuestionnairesList(array(), array('limit' => 1000));
		$this->set('pastQuestionnaires', $pastQuestionnaires);
		// NetCommonsお約束：投稿のデータはrequest dataに設定する
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}
}