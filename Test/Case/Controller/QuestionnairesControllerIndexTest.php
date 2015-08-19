<?php
/**
 * Index test on QuestionnairesController
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
 * Index test on QuestionnairesController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Questionnaires\Test\Case\Controller
 */
class QuestionnairesControllerIndexTest extends QuestionnairesControllerTestBase {

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
 * Expect index action
 *
 * @return void
 */
	public function testIndex() {
		RolesControllerTest::login($this);

		//$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));

		$frameId = '1';

		$this->controller->Session->expects($this->any())
				->method('id')
				->will($this->returnValue(1));

		$this->testAction(
				'/questionnaires/questionnaires/index/' . $frameId, // . '/' . $blockId,
				array(
					'method' => 'get',
					'return' => 'view',
				)
			);

		$this->assertTextEquals('index', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect index action
 *
 * @return void
 */
	public function testIndex2() {
		//単独表示ではない Questionnaireが存在しない
		//RolesControllerTest::login($this);

		$frameId = '100';
		$this->testAction(
				'/questionnaires/questionnaires/index/' . $frameId,
				array(
					'method' => 'get',
					'return' => 'view',
				)
			);

		$this->assertTextEquals('Questionnaires/noQuestionnaire', $this->controller->view);

		//AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect index action
 *
 * @return void
 */
	public function testIndex3() {
		//単独表示 Questionnaireが存在しない
		RolesControllerTest::login($this);
		$frameId = '10';
		$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));
		$this->controller->Session->expects($this->any())
				->method('id')
				->will($this->returnValue(1));

		$this->testAction(
				'/questionnaires/questionnaires/index/' . $frameId,
				array(
					'method' => 'get',
					'return' => 'view',
				)
			);

		$this->assertTextEquals('Questionnaires/noQuestionnaire', $this->controller->view);
		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect index action
 *
 * @return void
 */
	public function testIndexNoFrameD() {
		RolesControllerTest::login($this);

		$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));

		$frameId = '1';

		//QuestionnaireFrameDisplayQuestionnaireが存在しない
		$questionMock = $this->getMockForModel('Questionnaires.QuestionnaireFrameDisplayQuestionnaire', array('find'));
		$questionMock->expects($this->once())
			->method('find')
			->will($this->returnValue(null));

		$this->testAction(
				'/questionnaires/questionnaires/index/' . $frameId,
				array(
					'method' => 'get',
					'return' => 'view',
				)
			);

		$this->assertTextEquals('QuestionnaireAnswers/noMoreAnswer', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect index action
 *
 * @return void
 */
	public function testIndexErrException() {
		//NotFoundの例外(154行目)
		//$this->setExpectedException('NotFoundException');
		//RolesControllerTest::login($this);

		//$this->Questionnaire->unbindModel(array('belongsTo' => array('Block')));

		//$frameId = '2';

		//$this->testAction(
		//		'/questionnaires/questionnaires/index/' . $frameId,
		//		array(
		//			'method' => 'get',
		//			'return' => 'view',
		//		)
		//	);

		//$this->assertTextEquals('QuestionnaireAnswers/noMoreAnswer', $this->controller->view);

		//AuthGeneralControllerTest::logout($this);
	}

}
