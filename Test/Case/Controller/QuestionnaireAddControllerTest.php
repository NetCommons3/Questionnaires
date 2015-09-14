<?php
/**
 * Add test on QuestionnaireAddController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAddController', 'Questionnaires.Controller');
App::uses('QuestionnairesControllerTestBase', 'Questionnaires.Test/Case/Controller');

/**
 * Add test on QuestionsController
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Faqs\Test\Case\Controller
 */
class QuestionnaireAddControllerTest extends QuestionnairesControllerTestBase {

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() {
        $this->generate(
            'Questionnaires.QuestionnaireAdd',
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
    public function testAddNew() {
        //�V�K�쐬
        RolesControllerTest::login($this);
        $frameId = '1';

        $data = array(
            'Questionnaire' => array(
                'title' => 'testtitle',
            ),
            'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW,
            //	'past_questionnaire_id' => 1,
        );
        $this->testAction(
            '/questionnaires/questionnaire_add/add/' . $frameId,

            array(
                'method' => 'POST',
                'data'	=> $data,
                //			'return' => 'view',
                'return' => 'contents',
            )
        );

        $this->assertTextEquals('add', $this->controller->view);
        //print_r($this->headers['Location']);
        $this->assertRegExp('#/questionnaire_questions/edit#', $this->headers['Location']);//���_�C���N�g�m�F
        AuthGeneralControllerTest::logout($this);
    }

    /**
     * Expect add action
     *
     * @return void
     */
    public function testAddReuse() {
        //�V�K�쐬(�ߋ��̃A���P�[�g�𗬗p)
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
            '/questionnaires/questionnaire_add/add/' . $frameId,

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
        //�V�K�쐬(�ߋ��̃A���P�[�g�𗬗p)�i�ߋ��̃A���P�[�g���w��G���[�j
        RolesControllerTest::login($this);
        $frameId = '1';

        $data = array(
            'Questionnaire' => array(
                'title' => 'testtitle',
            ),
            'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE,
        );
        $this->testAction(
            '/questionnaires/questionnaire_add/add/' . $frameId,

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
        //�V�K�쐬(�f�[�^�Ȃ�����ʂ̍ĕ\���j
        RolesControllerTest::login($this);
        $frameId = '1';

        // PENDING Error�ɂȂ邽�ߒǉ��B��
        //1) QuestionnairesControllerAddTest::testAdd
        //PDOException: SQLSTATE[42000]: Syntax error or access violation: 1066 Not unique table/alias: 'Block'

        $data = array();
        //	'create_option' => QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW);
        $this->testAction(
            '/questionnaires/questionnaire_add/add/' . $frameId,
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
