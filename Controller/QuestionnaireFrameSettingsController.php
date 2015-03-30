<?php
/**
 * Questionnaires Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

class QuestionnaireFrameSettingsController extends QuestionnairesAppController
{

    /**
     * use model
     *
     * @var array
     */
    public $uses = array(
        'Questionnaires.Questionnaire',
        'Questionnaires.QuestionnaireEntity',
        'QuestionnaireFrameSettings'
    );

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
                'contentPublishable' => array('content_list', 'setting', 'style')
            ),
        ),
        'Questionnaires.Questionnaires',
    );
    /**
     * use helpers
     *
     * @var array
     */
    public $helpers = array(
        'NetCommons.Token',
        'Questionnaires.QuestionnaireUtil'
    );

    /**
     * index method
     *
     * @return void
     */
    public function index()
    {
        $this->redirect('list/'.$this->viewVars['frameId']);
    }

    public function content_list() {
        // 画面表示に纏わるパラメータをキャッシュより取り出す
        $cache = $this->_getCache();

        // 作成リストデータ準備
        $questionnaire = array();

        // 画面表示パラメータ準備
        $filter = $cache['filter'];
        $page = $cache['page'];

        // 画面表示パラメータに対してGET指定があればパラメータを上書き
        if (array_key_exists('status', $this->request->query)) {
            if(strlen($this->request->query['status']) > 0) {
                $filter['status'] = $this->request->query['status'];
            }
            else {
                unset($filter['status']);
            }
        }
        if (array_key_exists('page', $this->request->query)) {
            $page['currentPageNumber'] = $this->request->query['page'];
        }
        // オフセット
        $offset = ($page['currentPageNumber'] - 1) * $page['displayNumPerPage'];

        // 全件数カウント
        $page['totalCount'] = $this->Questionnaire->getQuestionnairesCount(
            $this->viewVars['roomId'],
            $this->viewVars['contentEditable'],
            $filter
        );

        // LIMIT件数 取り出し
        $questionnaires['items'] = $this->Questionnaire->getQuestionnaires(
            $this->viewVars['roomId'],
            $this->viewVars['contentEditable'],
            $filter,
            $offset,
            $page['displayNumPerPage']
        );
        $questionnaires['itemCount'] = count($questionnaires['items']);

        $cache['filter'] = $filter;
        $cache['page'] = $page;
        $this->Session->write('Questionnaires.QuestionnaireFrameSettingsContentList', $cache);

        $questionnaires['QuestionnaireFrameSettingsContentList'] = $cache;
        $questionnaires['questionnaire'] = $questionnaire;

        $this->set('tabLists', $this->_getTabLists('list'));
        $this->set('questionnaires', $questionnaires);
        $this->set('page', $page);
        $this->set('filter', $filter);
        $this->Session->write('Questionnaires.nowUrl', $this->request->url);
    }
    public function style() {
        $this->set('tabLists', $this->_getTabLists('style'));
    }
    public function setting($frameId) {
        // 全件 取り出し
        $questionnaires['items'] = $this->Questionnaire->getQuestionnaires(
            $this->viewVars['roomId'],
            $this->viewVars['contentEditable'],
            array(),
            0,
            1000
        );
        $frame = $this->QuestionnaireFrameSettings->find('first', array(
            'conditions' => array(
                'frame_id' => $frameId,
            ),
            'order' => 'QuestionnaireFrameSettings.id DESC'
        ));

        $questionnaires['QuestionnaireFrameSettings'] = $frame['QuestionnaireFrameSettings'];
        $this->set('questionnaires', $questionnaires);
        $this->set('tabLists', $this->_getTabLists('setting'));
        $this->set('topUrl', $this->Questionnaires->getPageUrl($this->viewVars['frameId']));

    }
    /**
     * get session cache method
     *
     * @return array
     */
    private function _getCache() {

        // default value

        $cache = $this->Session->read('Questionnaires.QuestionnaireFrameSettingsContentList');

        if(!isset($cache['filter'])) {
            $cache['filter'] = array();
        }
        if(!isset($cache['page'])) {
            $cache['page']['currentPageNumber'] = 1;
            $cache['page']['displayNumPerPage'] = QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE;
        }
        return $cache;
    }

    /**
     * get tab list method
     *
     * @return array
     */
    private function _getTabLists($active) {
        $tabLists = array(
            'list' => array('href' => '/questionnaires/questionnaire_frame_settings/content_list/'. $this->viewVars['frameId'],
                'tabTitle' => __d('questionnaires', 'Contents list'),
                'class' => ''),
            'style' => array('href' => '/questionnaires/questionnaire_frame_settings/style/'. $this->viewVars['frameId'],
                'tabTitle' => __d('questionnaires', 'Display style set'),
                'class' => ''),
            'setting' => array('href' => '/questionnaires/questionnaire_frame_settings/setting/'. $this->viewVars['frameId'],
                'tabTitle' => __d('questionnaires', 'Display settings'),
                'class' => ''),
        );
        $tabLists[$active]['class'] = 'active';
        return $tabLists;
    }

}