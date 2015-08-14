<?php
/**
 * Edit test on QuestionnaireBlocksController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireBlocksController', 'Questionnaires.Controller');
App::uses('QuestionnairesController', 'Questionnaires.Controller');
App::uses('QuestionnairesControllerTestBase', 'Questionnaires.Test/Case/Controller');

/**
 * Edit test on QuestionnaireQuestionsController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller
 */
class QuestionnaireBlocksControllerTest extends QuestionnairesControllerTestBase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		$this->generate(
			'Questionnaires.QuestionnaireBlocks',
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

		// file_existでtrueを返す
		parent::setUp();
	}

/**
 * Expect Edit action
 *
 * @return void
 * @throws NotFoundException
 */
	public function testEdit() {
		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array();

		//セッションID
		$this->controller->Session->expects($this->any())
			->method('id')
			->will($this->returnValue(1));

		//paginator
		$this->controller->Components->Paginator
			->expects($this->once())
			->method('paginate')
			->will($this->returnCallback(function () {
				throw new NotFoundException();
			}));

		$this->testAction(
				'/questionnaires/questionnaire_blocks/index/' . $frameId,
				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'vars', //vars(setメソッドでviewに渡された値)
				)
			);
		$this->assertTextEquals('index', $this->controller->view);
		//$this->assertRegExp('#questionnaire_blocks/index#', $this->headers['Location']); //リダイレクトを評価

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect download action
 *
 * @return void
 */
	public function testdownload() {
		//正常
		RolesControllerTest::login($this);
		$frameId = 1;
		$questionnaireId = 1;

		$data = array();

		$this->testAction(
				'/questionnaires/questionnaire_blocks/download/' . $frameId . '/' . $questionnaireId,
				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'vars', //vars(setメソッドでviewに渡された値)
				)
			);
		$this->assertTextEquals('download', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect download action
 *
 * @return void
 */
	public function testdownloaderr() {
		//エラー
		RolesControllerTest::login($this);
		$frameId = 1;
		$questionnaireId = 10;

		$data = array();

		$this->testAction(
				'/questionnaires/questionnaire_blocks/download/' . $frameId . '/' . $questionnaireId,
				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'vars', //vars(setメソッドでviewに渡された値)
				)
			);
		$this->assertTextEquals('download', $this->controller->view);
		//print_r($this->controller->response);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect download action
 *
 * @return void
 */
	public function testdownloadcompress() {
		//compressFile
		$this->setExpectedException('NotFoundException');
		RolesControllerTest::login($this);
		$frameId = 1;
		$questionnaireId = 1;

		$data = array();

		$this->testAction(
				'/questionnaires/questionnaire_blocks/download/' . $frameId . '/' . $questionnaireId,
				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'vars', //vars(setメソッドでviewに渡された値)
				)
			);
		$this->assertTextEquals('download', $this->controller->view);
		//print_r($this->controller->response);

		AuthGeneralControllerTest::logout($this);
	}

}
