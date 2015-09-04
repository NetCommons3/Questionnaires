<?php
/**
 * Delete test on QuestionsController
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
 * Delete test on QuestionsController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller
 */
class QuestionnairesControllerDeleteTest extends QuestionnairesControllerTestBase {

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
					'NetCommons.NetCommonsWorkflow'
				]
			]
		);

		parent::setUp();
	}

/**
 * Expect Delete action
 *
 * @return void
 */
	public function testDelete() {
		//削除（正常）
		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array(
			'Questionnaire' => array(
				'id' => 1,
				'origin_id' => 1,
				'key' => 'frame_1',
			),
		);

		$this->testAction(
				'/questionnaires/questionnaires/delete/' . $frameId,

				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'view',
				)
			);

		$this->assertTextEquals('delete', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect Delete action
 *
 * @return void
 */
	public function testDeleteNothing() {
		//削除（削除なし deleteQuestionnaireでfalseが返却される場合）

		$questionMock = $this->getMockForModel('Questionnaires.Questionnaire', array('deleteQuestionnaire'));
		$questionMock->expects($this->once())
			->method('deleteQuestionnaire')
			->will($this->returnValue(false));

		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array(
			'Questionnaire' => array(
				'id' => 1,
				'origin_id' => 1,
				'key' => 'frame_1',
			),
		);

		$this->testAction(
				'/questionnaires/questionnaires/delete/' . $frameId,

				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'view',
				)
			);
		//PENDING ↓　「選ばれたビューファイルがありません。」
		//1) QuestionnairesControllerDeleteTest::testDeleteNothing
		//MissingViewException: View file "Questionnaires/delete.ctp" is missing.

		$this->assertTextEquals('delete', $this->controller->view);

		AuthGeneralControllerTest::logout($this);
	}

}
