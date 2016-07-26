<?php
/**
 * QuestionnaireEditController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuestionnaireEditController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Controller
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class QuestionnaireEditControllerCancelTest extends NetCommonsControllerTestCase {

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
	protected $_controller = 'questionnaire_edit';

/**
 * testCancel
 *
 * @return void
 */
	public function testCancel() {
		//テスト実施
		$urlOptions = array(
			'action' => 'cancel', 'block_id' => 2, 'frame_id' => 6
		);
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
		), $urlOptions);
		$this->_testNcAction($url, array('method' => 'get'), null, 'view');
		$result = $this->headers['Location'];
		$this->assertNotEmpty($result);
	}
}