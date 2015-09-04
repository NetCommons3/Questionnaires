<?php
/**
 * Add test on QuestionnaireAnswersController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAnswersController', 'Questionnaires.Controller');
App::uses('QuestionnairesControllerTestBase', 'Questionnaires.Test/Case/Controller');

/**
 * Add test on QuestionnaireAnswersController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller
 */
class QuestionnaireAnswersControllerTest extends QuestionnairesControllerTestBase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		$this->generate(
			'Questionnaires.QuestionnaireAnswers',
			[
				'components' => [
					'Auth' => ['user'],
					'Session',
					'Security',
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
	public function testanswer() {
		//正常(プレ回答)
		RolesControllerTest::login($this);
		$frameId = '1';
		$questionnaireId = '1';

		$data = array(
			'PreAnswer' => array(
			'key_phrase' => 'aaa')
			);
		//	'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW);
		$this->testAction(
				'/questionnaires/questionnaire_answers/answer/' . $frameId . '/' . $questionnaireId,

				array(
					'method' => 'POST',
					'data'	=> $data,
					'return' => 'view',
				)
			);
		//print_r($this->controller->view);
		$this->assertTextEquals('QuestionnaireAnswers/test_mode', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect add action
 *
 * @return void
 */
	public function testanswerNoLogin() {
		//正常(権限なし)
		//RolesControllerTest::login($this);
		$frameId = '1';
		$questionnaireId = '1';

		$data = array(
			'PreAnswer' => array(
			'key_phrase' => 'aaa')
			);
		//	'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW);
		$this->testAction(
				'/questionnaires/questionnaire_answers/answer/' . $frameId . '/' . $questionnaireId,

				array(
					'method' => 'POST',
					'data'	=> $data,
					'return' => 'view',
				)
			);
		//print_r($this->controller->view);
		$this->assertTextEquals('QuestionnaireAnswers/noMoreAnswer', $this->controller->view);

		//AuthGeneralControllerTest::logout($this);
	}

}
