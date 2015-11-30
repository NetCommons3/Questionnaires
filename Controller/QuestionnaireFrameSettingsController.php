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

App::uses('QuestionnaireBlocksController', 'Questionnaires.Controller');

/**
 * QuestionnaireFrameSettingsController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireFrameSettingsController extends QuestionnaireBlocksController {

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
		'Questionnaires.QuestionnaireFrameSetting',
		'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'questionnaire_blocks')),
				'frame_settings' => array('url' => array('controller' => 'questionnaire_frame_settings')),
			),
		),
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'edit' => 'page_editable',
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
		'Questionnaires.QuestionnaireUtil'
	);

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		// Postデータ登録
		if ($this->request->isPut() || $this->request->isPost()) {
			if ($this->QuestionnaireFrameSetting->saveFrameSettings($this->request->data)) {
				$this->redirect(NetCommonsUrl::backToPageUrl());
				return;
			}
			$this->NetCommons->handleValidationError($this->QuestionnaireFrameSetting->validationErrors);
		}

		$conditions = array(
			'block_id' => Current::read('Block.id'),
			'is_latest' => true,
		);
		$this->paginate = array(
			'fields' => array('Questionnaire.*', 'QuestionnaireFrameDisplayQuestionnaire.*'),
			'conditions' => $conditions,
			'page' => 1,
			'sort' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
			'limit' => 1000,
			'direction' => 'desc',
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'questionnaire_frame_display_questionnaires',
					'alias' => 'QuestionnaireFrameDisplayQuestionnaire',
					'type' => 'LEFT',
					'conditions' => array(
						'QuestionnaireFrameDisplayQuestionnaire.questionnaire_key = Questionnaire.key',
						'QuestionnaireFrameDisplayQuestionnaire.frame_key' => Current::read('Frame.key'),
					),
				)
			)
		);
		$questionnaires = $this->paginate('Questionnaire');

		$frame = $this->QuestionnaireFrameSetting->find('first', array(
			'conditions' => array(
				'frame_key' => Current::read('Frame.key'),
			),
			'order' => 'QuestionnaireFrameSetting.id DESC'
		));
		if (!$frame) {
			$frame = $this->QuestionnaireFrameSetting->getDefaultFrameSetting();
		}

		$this->set('questionnaires', $questionnaires);
		$this->set('questionnaireFrameSettings', $frame['QuestionnaireFrameSetting']);
		$this->request->data['QuestionnaireFrameSetting'] = $frame['QuestionnaireFrameSetting'];
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}
}