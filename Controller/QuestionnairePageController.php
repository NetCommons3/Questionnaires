<?php
/**
 * Created by PhpStorm.
 * User: りか
 * Date: 2015/01/05
 * Time: 16:59
 */

App::uses('AppController', 'Controller');

class QuestionnairePageController extends QuestionnairesAppController {

    /**
     * use components
     *
     * @var array
     */
    public $components = array(
        'NetCommons.NetCommonsBlock', //Use Questionnaire model
        'NetCommons.NetCommonsFrame',
        'NetCommons.NetCommonsRoomRole' => array(
            //コンテンツの権限設定
            'allowedActions' => array(
                'contentEditable' => array('setting', 'edit')
            ),
        ),
    );
    /**
     * use helpers
     *
     * @var array
     */
    public $helpers = array(
        'NetCommons.Token'
    );
    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->set('questionnaire', array('name'=>'FUJIWARA RIKA'));
        $this->view = 'QuestionnairePage/index';
    }

    /**
     * setting method
     *
     * @return void
     */
    public function setting() {
        $this->layout = 'NetCommons.modal';
    }
    /**
     * edit method
     *
     * @return void
     */
    public function edit() {
    }
    /**
     * question setting method
     *
     * @return void
     */
    public function question_setting() {
        $this->layout = 'NetCommons.modal';
        $this->view = 'QuestionnaireQuestions/setting';
    }
    /**
     * question total disolay setting method
     *
     * @return void
     */
    public function total_setting() {
        $this->layout = 'NetCommons.modal';
        $this->view = 'QuestionnaireQuestions/total';
    }
}
