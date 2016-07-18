<?php
/**
 * View/Elements/QuestionnaireEdit/Edit/questionnaire_method/group_methodテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/QuestionnarieEdit/Edit/questionnaire_method/group_methodテスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\test_app\Plugin\TestQuestionnaires\Controller
 */
class TestViewElementsQuestionnaireEditEditQuestionnaireMethodGroupMethodController extends AppController {

/**
 * use helpers
 *
 */
	public $helpers = array(
		'Questionnaires.QuestionEdit',
	);

/**
 * group_method
 *
 * @return void
 */
	public function group_method() {
		$this->autoRender = true;
	}

}
