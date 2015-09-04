<?php
/**
 * Edit test on QuestionnaireQuestionsController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireQuestionsController', 'Questionnaires.Controller');
App::uses('QuestionnairesController', 'Questionnaires.Controller');
App::uses('QuestionnairesControllerTestBase', 'Questionnaires.Test/Case/Controller');

/**
 * Edit test on QuestionnaireQuestionsController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller
 */
class QuestionnaireQuestionsControllerEditTest extends QuestionnairesControllerTestBase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		$this->generate(
			'Questionnaires.QuestionnaireQuestions',
			[
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
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
	public function testEditErrValidate() {
		//バリデートエラー(ページ無し)(POST)
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
				'/questionnaires/questionnaire_questions/edit/' . $frameId,

				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'view',
				)
			);
		$this->assertNotEmpty($this->controller->qValidationErrors);
		$this->assertTextEquals('edit', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect Edit action
 *
 * @return void
 */
	public function testEditSessionTimeOut() {
		//セッションタイムアウト(POST)
		$this->setExpectedException('BadRequestException');
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

		$this->testAction(
				'/questionnaires/questionnaire_questions/edit/' . $frameId,

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
	public function testEditNoOrigin() {
		//OriginID設定なし(POST)
		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array(
			'Questionnaire' => array(
				'title' => 'testtitle',
				'key' => '',
				'status' => 3,
				'is_total_show' => 1,
				'block_id' => 5,
				//'origin_id' => 1,
				'id' => 1
			),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'keywords',
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'route_number' => 0,
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'question_sequence' => 0,
							'question_type' => 1,
							'QuestionnaireChoice' => array(
								0 => array(
										'choice_sequence' => 5,
							)))
			)))
		);

		//---モックでaddからのsessionを設定
		$this->controller->Session->expects($this->any())
				->method('read')
				->will($this->returnValueMap([['Questionnaires.questionnaire', $data]]));

		$this->testAction(
				'/questionnaires/questionnaire_questions/edit/' . $frameId,

				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'view',
				)
			);
		//print_r();
		$this->assertTextEquals('edit', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect Edit action
 *
 * @return void
 */
	public function testEditNotPostErr() {
		//BadErrorExcrption(POSTではない)
		$this->setExpectedException('BadRequestException');

		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array();

		$this->testAction(
				'/questionnaires/questionnaire_questions/edit/' . $frameId,

				array(
					'method' => 'GET',
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
	public function testEditQuery() {
		//クエリ

		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array(
			'questionnaire_id' => 1);

		$this->testAction(
				'/questionnaires/questionnaire_questions/edit/' . $frameId,

				array(
					'method' => 'GET',
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
	public function testEditSession() {
		//Session
		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array(
			'Questionnaire' => array(
				'title' => 'testtitle',
				'key' => '',
				'status' => 3,
				'is_total_show' => 1,
				'block_id' => 5,
				//'origin_id' => 1,
				'id' => 1
			),
			'QuestionnairePage' => array(
				0 => array(
					'id' => 1,
					'key' => 'keywords',
					'questionnaire_id' => 1,
					'page_title' => 'pagetitle',
					'page_sequence' => 0,
					'route_number' => 0,
					'QuestionnaireQuestion' => array(
						0 => array(
							'id' => 1,
							'key' => 'testkey',
							'question_sequence' => 0,
							'question_type' => 1,
							'QuestionnaireChoice' => array(
								0 => array(
										'choice_sequence' => 5,
							)))
			)))
		);

		//---モックでaddからのsessionを設定
		$this->controller->Session->expects($this->any())
				->method('check')
				->will($this->returnValueMap([['Questionnaires.questionnaire', $data]]));
		$this->controller->Session->expects($this->any())
				->method('read')
				->will($this->returnValueMap([['Questionnaires.questionnaire', $data]]));

		$this->testAction(
				'/questionnaires/questionnaire_questions/edit/' . $frameId,

				array(
					'method' => 'GET',
					'data' => $data,
					'return' => 'view',
				)
			);

		$this->assertTextEquals('edit', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

}
