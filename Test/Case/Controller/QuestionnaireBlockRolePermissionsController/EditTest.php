<?php
/**
 * BlockRolePermissionsController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('QuestionnaireBlockRolePermissionsController', 'Questionnaires.Controller');
App::uses('BlockRolePermissionsControllerEditTest', 'Blocks.TestSuite');

/**
 * BlockRolePermissionsController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Controller
 */
class QuestionnaireBlockRolePermissionsControllerEditTest extends BlockRolePermissionsControllerEditTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.block_setting_for_questionnaire',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
		'plugin.authorization_keys.authorization_keys',
		'plugin.workflow.workflow_comment',
	);

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'questionnaire_block_role_permissions';

/**
 * テストDataの取得
 *
 * @param bool $isPost POSTかどうか
 * @return array
 */
	private function __getData($isPost) {
		if ($isPost) {
			$data = array(
				'QuestionnaireSetting' => array(
					'id' => 2,
					'block_key' => 'block_2',
					'use_workflow' => '1',
					'approval_type' => '1',
				)
			);
		} else {
			$data = array(
				'QuestionnaireSetting' => array(
					'use_workflow',
					'approval_type',
				)
			);
		}
		return $data;
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - approvalFields コンテンツ承認の利用有無のフィールド
 *  - exception Exception
 *  - return testActionの実行後の結果
 *
 * @return void
 */
	public function dataProviderEditGet() {
		return array(
			array('approvalFields' => $this->__getData(false))
		);
	}

/**
 * edit()アクションDataProvider
 *
 * ### 戻り値
 *  - data POSTデータ
 *  - exception Exception
 *  - return testActionの実行後の結果
 *
 * @return void
 */
	public function dataProviderEditPost() {
		return array(
			array('data' => $this->__getData(true))
		);
	}

/**
 * editアクションのPOSTテスト(Saveエラー)
 *
 * @param array $data POSTデータ
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditPost
 * @return void
 */
	public function testEditPostSaveError($data, $exception = null, $return = 'view') {
		$data['BlockRolePermission']['content_creatable'][Role::ROOM_ROLE_KEY_GENERAL_USER]['roles_room_id'] = 'aaaa';

		//テスト実施
		$exception = false;
		$result = $this->testEditPost($data, false, $return);

		$approvalFields = $this->__getData(false);
		$this->_assertEditGetPermission($approvalFields, $result);
	}
}
