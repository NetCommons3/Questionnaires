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
		'Questionnaires.QuestionnaireFrameSettings',
		'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Security',
		'NetCommons.NetCommonsBlock', //Use Questionnaire model
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'pageEditable' => array('edit')
			),
		),
		'Questionnaires.Questionnaires',
		'Paginator',
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
 * edit method
 *
 * @return void
 */
	public function edit() {
		$conditions = array(
			'block_id' => $this->viewVars['blockId'],
			'is_latest' => true,
		);
		try {
			$this->paginate = array(
				'conditions' => $conditions,
				'page' => 1,
				'sort' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
				'limit' => 1000,
				'direction' => 'desc',
				'recursive' => -1,
			);
			$questionnaires = $this->paginate('Questionnaire');
		} catch (NotFoundException $e) {
			// NotFoundの例外
			// アンケートデータが存在しないこととする
			$questionnaires = array();
		}

		$frame = $this->QuestionnaireFrameSettings->find('first', array(
			'conditions' => array(
				'frame_key' => $this->viewVars['frameKey'],
			),
			'order' => 'QuestionnaireFrameSettings.id DESC'
		));
		if (!$frame) {
			$frame = $this->QuestionnaireFrameSettings->getDefaultFrameSetting();
		}

		$displayQuestionnaire = $this->QuestionnaireFrameDisplayQuestionnaire->find('list', array(
			'fields' => array(
				'questionnaire_origin_id', 'questionnaire_origin_id'
			),
			'conditions' => array(
				'frame_key' => $this->viewVars['frameKey'],
			),
		));
		$this->set('questionnaires', $questionnaires);
		$this->set('questionnaireFrameSettings', $frame['QuestionnaireFrameSettings']);
		$this->set('displayQuestionnaire', $displayQuestionnaire);
	}
}