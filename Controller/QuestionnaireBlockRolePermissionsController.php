<?php
/**
 * QuestionnaireBlockRolePermissions Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireBlocksController', 'Questionnaires.Controller');

/**
 * QuestionnaireBlockRolePermissions Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireBlockRolePermissionsController extends QuestionnaireBlocksController {

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Roles.Role',
		'Roles.DefaultRolePermission',
		'Blocks.Block',
		'Blocks.BlockRolePermission',
		'Rooms.RolesRoom',
		'Questionnaires.Questionnaires',
		'Questionnaires.QuestionnaireFrameSetting',
		'Questionnaires.QuestionnaireBlocksSetting',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock',
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'blockPermissionEditable' => array('edit')
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token'
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		//タブの設定
		$this->initTabs('role_permissions');
	}

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		if (! $this->QuestionnaireFrameSetting->prepareBlock($this->viewVars['frameId'])) {
			$this->view = 'QuestionnaireBlockRolePermissions/noQuestionnaireBlock';
			return;
		}
		if (! $this->NetCommonsBlock->validateBlockId()) {
			$this->throwBadRequest();
			return false;
		}
		$this->set('blockId', (int)$this->params['pass'][1]);

		$blockSetting = $this->QuestionnaireBlocksSetting->find('first', array(
			'conditions' => array(
				'block_key' => $this->viewVars['blockKey']
			)
		));
		if (!$blockSetting) {
			$this->QuestionnaireBlocksSetting->create();
			$blockSetting = $this->QuestionnaireBlocksSetting->save(
				array(
					'block_key' => $this->viewVars['blockKey'],
					'use_workflow' => true
				)
			);
		}
		$this->set($blockSetting);

		if (! $block = $this->Block->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'Block.id' => $this->viewVars['blockId'],
			),
		))) {
			$this->throwBadRequest();
			return false;
		};
		$this->set('blockId', $block['Block']['id']);
		$this->set('blockKey', $block['Block']['key']);

		$permissions = $this->NetCommonsBlock->getBlockRolePermissions(
			$this->viewVars['blockKey'],
			['content_creatable', 'content_publishable']
		);
		if ($this->request->isPost()) {
			$data = $this->data;
			$this->QuestionnaireBlocksSetting->saveQuestionnaireBlocksSetting($data);
			if ($this->handleValidationError($this->QuestionnaireBlocksSetting->validationErrors)) {
				if (! $this->request->is('ajax')) {
					$this->redirect('/questionnaires/questionnaire_blocks/index/' . $this->viewVars['frameId']);
				}
				return;
			}
		}
		$results = array(
			'BlockRolePermissions' => $permissions['BlockRolePermissions'],
			'roles' => $permissions['Roles'],
		);
		$results = $this->camelizeKeyRecursive($results);

		$this->set($results);
	}
}
