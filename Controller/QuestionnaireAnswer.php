<?php
/**
 * Created by PhpStorm.
 * User: りか
 * Date: 2015/01/14
 * Time: 17:28
 */
App::uses('AppController', 'Controller');

class QuestionnairesAnswerController extends QuestionnairesAppController
{

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
}