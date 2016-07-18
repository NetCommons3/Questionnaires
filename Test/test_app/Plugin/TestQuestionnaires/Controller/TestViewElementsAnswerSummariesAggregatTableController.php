<?php
/**
 * View/Elements/AnswerSummaries/aggregate_tableテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/AnswerSummaries/aggregate_tableテスト用Controller
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\test_app\Plugin\TestQuestionnaires\Controller
 */
class TestViewElementsAnswerSummariesAggregatTableController extends AppController {

/**
 * group_method
 *
 * @return void
 */
	public function aggregate_table() {
		$question = array(
			'QuestionnaireChoice' => array(
				array(
					'choice_label' => 'エレメントテスト',
				)
			)
		);
		$this->set('question', $question);
		$this->autoRender = true;
	}

}
