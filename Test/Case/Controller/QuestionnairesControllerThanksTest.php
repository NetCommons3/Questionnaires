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
class QuestionnairesControllerThanksTest extends QuestionnairesControllerTestBase {

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
	public function testThanks() {
		//正常
		RolesControllerTest::login($this);
		$frameId = '1';
		$questionnaireId = '1';

		$data = array();
		//	'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW);
		$this->testAction(
				'/questionnaires/questionnaires/thanks/' . $frameId . '/' . $questionnaireId,

				array(
					'method' => 'POST',
					'data'	=> $data,
					'return' => 'view',
				)
			);
		//print_r($this->controller->view);
		$this->assertTextEquals('thanks', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect add action
 *
 * @return void
 */
	public function testThanksNotFound() {
		//NotFoundException
		$this->setExpectedException('NotFoundException');

		RolesControllerTest::login($this);

		$frameId = '1';
		$questionnaireId = '0';

		$data = array();
		//	'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW);
		$this->testAction(
				'/questionnaires/questionnaires/thanks/' . $frameId . '/' . $questionnaireId,

				array(
					'method' => 'POST',
					'data'	=> $data,
					'return' => 'view',
				)
			);
		//print_r($this->controller->view);
		$this->assertTextEquals('thanks', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect add action
 *
 * @return void
 */
	public function testThanksDisable() {
		//表示不可(ログインしていない)
		//RolesControllerTest::login($this);

		$this->setExpectedException('ForbiddenException');

		$frameId = '1';
		$questionnaireId = '1';

		$data = array();
		//	'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW);
		$this->testAction(
				'/questionnaires/questionnaires/thanks/' . $frameId . '/' . $questionnaireId,

				array(
					'method' => 'POST',
					'data'	=> $data,
					'return' => 'view',
				)
			);
		//print_r($this->controller->view);
		$this->assertTextEquals('thanks', $this->controller->view);

		//AuthGeneralControllerTest::logout($this);
	}

}
