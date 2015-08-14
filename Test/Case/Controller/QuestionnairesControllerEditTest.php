<?php
/**
 * Edit test on QuestionsController
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
 * Edit test on QuestionsController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller
 */
class QuestionnairesControllerEditTest extends QuestionnairesControllerTestBase {

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
					'Paginator',
					'NetCommons.NetCommonsWorkflow'
				]
			]
		);

		parent::setUp();
	}

/**
 * Expect Edit action
 *
 * @return void
 */
	public function testEdit() {
		//指示された編集状態ステータスが無い場合
		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array(
			'Questionnaire' => array(
				'title' => 'testtitle',
				'key' => '',
				'status' => 3,
				'is_total_show' => 1,
				'block_id' => 5,
				'origin_id' => 1,
				'id' => 1
			),
		);

		//---モックでaddからのsessionを設定
		$this->controller->Session->expects($this->any())
				->method('read')
				->will($this->returnValueMap([['Questionnaires.questionnaire', $data]]));

		$this->testAction(
				'/questionnaires/questionnaires/edit/' . $frameId,

				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'view',
				)
			);

		$this->assertTextEquals('edit', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect Edit action
 *
 * @return void
 */
	public function testEditSessionTimeout() {
		//セッションタイムアウト
		$this->setExpectedException('BadRequestException');
		RolesControllerTest::login($this);
		$frameId = '1';

		$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));

		$data = array(
			'Questionnaire' => array(
				'title' => 'testtitle',
			),
			'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW,
			'past_questionnaire_id' => 1,
			);
		$this->testAction(
				'/questionnaires/questionnaires/edit/' . $frameId,

				array(
					'method' => 'POST',
					'data'	=> $data,
					'return' => 'view',
				)
			);

		$this->assertTextEquals('Edit', $this->controller->view);
		//print_r($this->headers['Location']);
		$this->assertRegExp('#/questionnaire_questions/edit#', $this->headers['Location']);//リダイレクト確認
		AuthGeneralControllerTest::logout($this);
	}

}
