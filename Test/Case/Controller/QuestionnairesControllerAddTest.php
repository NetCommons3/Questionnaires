<?php
/**
 * Add test on QuestionsController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnairesController', 'Questionnaires.Controller');
App::uses('QuestionnairesControllerTestBase', 'Questionnaires.Test/Case/Controller');

/**
 * Add test on QuestionsController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller
 */
class QuestionnairesControllerAddTest extends QuestionnairesControllerTestBase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		$this->generate(
			'Questionnaires.Questionnaires',
			[
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
					'Paginator'
				]
			]
		);
		parent::setUp();
	}

/**
 * Expect add action
 *
 * @return void
 */
	public function testAddNew() {
		//新規作成
		RolesControllerTest::login($this);
		$frameId = '1';

		$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));

		$data = array(
			'Questionnaire' => array(
				'title' => 'testtitle',
			),
			'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW,
		//	'past_questionnaire_id' => 1,
			);
		$this->testAction(
				'/questionnaires/questionnaires/add/' . $frameId,

				array(
					'method' => 'POST',
					'data'	=> $data,
		//			'return' => 'view',
					'return' => 'contents',
				)
			);

		$this->assertTextEquals('add', $this->controller->view);
		//print_r($this->headers['Location']);
		$this->assertRegExp('#/questionnaire_questions/edit#', $this->headers['Location']);//リダイレクト確認
		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect add action
 *
 * @return void
 */
	public function testAddReuse() {
		//新規作成(過去のアンケートを流用)
		RolesControllerTest::login($this);
		$frameId = '1';

		//$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));

		$data = array(
			'Questionnaire' => array(
				'title' => 'testtitle',
			),
			'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE,
			'past_questionnaire_id' => 1,
			);
		$this->testAction(
				'/questionnaires/questionnaires/add/' . $frameId,

				array(
					'method' => 'POST',
					'data'	=> $data,
					//'return' => 'view',
					'return' => 'contents',
				)
			);

		$this->assertTextEquals('add', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect add action
 *
 * @return void
 */
	public function testAddReuseErr() {
		//新規作成(過去のアンケートを流用)（過去のアンケート未指定エラー）
		RolesControllerTest::login($this);
		$frameId = '1';

		$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));

		$data = array(
			'Questionnaire' => array(
				'title' => 'testtitle',
			),
			'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE,
			);
		$this->testAction(
				'/questionnaires/questionnaires/add/' . $frameId,

				array(
					'method' => 'POST',
					'data'	=> $data,
					//'return' => 'view',
					'return' => 'contents',
				)
			);

		$this->assertTextEquals('add', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect add action
 *
 * @return void
 */
	public function testAddNoData() {
		//新規作成(データなし＝画面の再表示）
		RolesControllerTest::login($this);
		$frameId = '1';

		$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));
		// PENDING Errorになるため追加。↑
		//1) QuestionnairesControllerAddTest::testAdd
		//PDOException: SQLSTATE[42000]: Syntax error or access violation: 1066 Not unique table/alias: 'Block'

		$data = array();
		//	'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW);
		$this->testAction(
				'/questionnaires/questionnaires/add/' . $frameId,
		//		'/questionnaires/questionnaires/add/' . $frameId . '/' . $blockId,
		//		'/questionnaires/questionnaires/add/' . $frameId . '.json',

				array(
					'method' => 'POST',
					'data'	=> $data,
					'return' => 'view',
				)
			);

		$this->assertTextEquals('add', $this->controller->view);
		//1) QuestionnairesControllerAddTest::testAdd
		//PDOException: SQLSTATE[42000]: Syntax error or access violation: 1066 Not unique table/alias: 'Block'

		AuthGeneralControllerTest::logout($this);
	}
}
