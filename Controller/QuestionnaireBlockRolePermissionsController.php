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
		'Questionnaires.QuestionnaireSetting',
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
				'role_permissions' => array('url' => array('controller' => 'questionnaire_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'questionnaire_frame_settings')),
			),
		),
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'edit' => 'block_permission_editable',
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Blocks.BlockRolePermissionForm',
		'NetCommons.Date',
	);

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		$questionnaireSetting = $this->QuestionnaireSetting->getSetting();
		if (! $questionnaireSetting) {
			$this->setAction('throwBadRequest');
			return false;
		}
		$permissions = $this->Workflow->getBlockRolePermissions(
			array('content_creatable', 'content_publishable', 'content_comment_creatable', 'content_comment_publishable')
		);
		$this->set('roles', $permissions['Roles']);
		if ($this->request->isPost()) {
			if ($this->QuestionnaireSetting->saveQuestionnaireSetting($this->request->data)) {
				$this->redirect(NetCommonsUrl::backToIndexUrl('default_setting_action'));
				return;
			}
			$this->NetCommons->handleValidationError($this->QuestionnaireSetting->validationErrors);
			$this->request->data['BlockRolePermission'] = Hash::merge(
				$permissions['BlockRolePermissions'],
				$this->request->data['BlockRolePermission']
			);

		} else {
			$this->request->data['QuestionnaireSetting'] = $questionnaireSetting['QuestionnaireSetting'];
			$this->request->data['Block'] = $questionnaireSetting['Block'];
			$this->request->data['BlockRolePermission'] = $permissions['BlockRolePermissions'];
			$this->request->data['Frame'] = Current::read('Frame');
		}
	}
}
