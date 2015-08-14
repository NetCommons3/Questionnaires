<?php
/**
 * Edit test on QuestionnaireFrameSettingsController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireFrameSettiongController', 'Questionnaires.Controller');
App::uses('QuestionnairesController', 'Questionnaires.Controller');
App::uses('QuestionnairesControllerTestBase', 'Questionnaires.Test/Case/Controller');
App::uses('QuestionnaireFrameSetting', 'Questionnaires.Model');

/**
 * Edit test on QuestionnaireQuestionsController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller
 */
class QuestionnaireFrameSettingsControllerTest extends QuestionnairesControllerTestBase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		$this->generate(
			'Questionnaires.QuestionnaireFrameSettings',
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
		//正常
		RolesControllerTest::login($this);
		$frameId = '1';

		$data = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => 2, //QuestionnairesComponent::DISPLAY_TYPE_SINGLE,
				'display_num_per_page' => 1,
				'sort_type' => 0),
			'QuestionnaireFrameDisplayQuestionnaires' => array(
				'Single' => array(
					'questionnaire_origin_id' => 1)
			)
		);

		$this->testAction(
				'/questionnaires/questionnaire_frame_settings/edit/' . $frameId,
				array(
					'method' => 'POST',
					'data' => $data,
					'return' => 'view',
				)
			);
		$this->assertTextEquals('edit', $this->controller->view);
		$this->assertRegExp('#questionnaire_blocks/index#', $this->headers['Location']); //リダイレクトを評価

		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect Edit action
 *
 * @return void
 */
	public function testEditNotPost() {
		//QuestionnairesFrameSetting(POSTではない)
		RolesControllerTest::login($this);
		$frameId = '1';
		$blockId = '5';

		$data = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => 2,
				'display_num_per_page' => 1,
				'sort_type' => 0,
				'questionnaire_origin_id' => 1,
				),
			'QuestionnaireFrameDisplayQuestionnaires' => array(
				'Single' => array(
					'questionnaire_origin_id' => 1)

			)
		);

		$result = $this->testAction(
				'/questionnaires/questionnaire_frame_settings/edit/' . $frameId . '/' . $blockId,

				array(
					'method' => 'GET',
					'data' => $data,
					'return' => 'vars', //vars(setメソッドでviewに渡された値)
					//'return' => 'view',
				)
			);

		//viewに渡された値
		$questionnaires = $result['questionnaires'];
		$qFrameSettings = $result['questionnaireFrameSettings'];
		$displayQuestionnaire = $result['displayQuestionnaire'];
		$this->assertEquals($qFrameSettings['id'], 1);
		$this->assertEquals($displayQuestionnaire[1], 1);
		$this->assertEmpty($questionnaires);
		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect Edit action
 *
 * @return void
 */
	public function testEditNotPost2() {
		//QuestionnairesFrameSetting(getDefaltFrameSettingを通るパターン)
		RolesControllerTest::login($this);
		$frameId = '5';
		$blockId = '1';

		$data = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => 2,
				'display_num_per_page' => 1,
				'sort_type' => 0,
				'frame_key' => '',
				'questionnaire_origin_id' => 1,
				),
			'QuestionnaireFrameDisplayQuestionnaires' => array(
				'Single' => array(
					'questionnaire_origin_id' => 1) //

			)
		);

		$model = $this->getMockForModel('Questionnaires.QuestionnaireFrameSetting', array('find'));
		$model->expects($this->any())
			->method('find')
			->will($this->returnValue(false));

		$this->testAction(
				'/questionnaires/questionnaire_frame_settings/edit/' . $frameId . '/' . $blockId,

				array(
					'method' => 'GET',
					'data' => $data,
					'return' => 'vars', //vars(setメソッドでviewに渡された値)
					//'return' => 'view',
				)
			);

		$this->assertTextEquals('edit', $this->controller->view);
		AuthGeneralControllerTest::logout($this);
	}

/**
 * Expect Edit action
 *
 * @return void
 * @throws NotFoundException
 */
	public function testEditNotPost3() {
		//QuestionnairesFrameSetting(NotFoundを通るパターン)

		RolesControllerTest::login($this);
		$frameId = '5';
		$blockId = '1';

		//paginator
		$this->controller->Components->Paginator
			->expects($this->once())
			->method('paginate')
			->will($this->returnCallback(function () {
				throw new NotFoundException();
			}));

		$data = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => 2,
				'display_num_per_page' => 1,
				'sort_type' => 0,
				'frame_key' => '',
				'questionnaire_origin_id' => 1,
				),
			'QuestionnaireFrameDisplayQuestionnaires' => array(
				'Single' => array(
					'questionnaire_origin_id' => 1) //

			)
		);

		$model = $this->getMockForModel('Questionnaires.QuestionnaireFrameSetting', array('find'));
		$model->expects($this->any())
			->method('find')
			->will($this->returnValue(false));

		$this->testAction(
				'/questionnaires/questionnaire_frame_settings/edit/' . $frameId . '/' . $blockId,

				array(
					'method' => 'GET',
					'data' => $data,
					'return' => 'vars', //vars(setメソッドでviewに渡された値)
					//'return' => 'view',
				)
			);

		$this->assertTextEquals('edit', $this->controller->view);
		AuthGeneralControllerTest::logout($this);
	}

}
