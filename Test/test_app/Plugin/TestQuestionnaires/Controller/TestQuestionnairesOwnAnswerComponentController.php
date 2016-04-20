<?php
/**
 * QuestionnaireOwnAnswerComponentテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnairesComponent::isOnlyInputType()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\test_app\Plugin\Questionnaires\Controller
 */
class TestQuestionnairesOwnAnswerComponentController extends AppController {

/**
 * 使用コンポーネント
 *
 * @var array
 */
	public $components = array(
		'Session',
		'Questionnaires.QuestionnairesOwnAnswer'
	);

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->autoRender = true;
	}
}
