<?php
/**
 * QuestionnaireComponent::isOnlyInputType()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireComponent::isOnlyInputType()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaire\Test\Case\Controller\Component\QuestionnaireComponent
 */
class QuestionnaireComponentIsOnlyInputTypeTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'questionnaires';

/**
 * isOnlyInputType()のテスト
 *
 * @param int $type チェック種別
 * @param mix $expect 期待値
 * @dataProvider dataProviderIsOnlyInputTypeType
 * @return void
 */
	public function testIsOnlyInputType($type, $expect) {
		$ret = QuestionnairesComponent::isOnlyInputType($type);
		$this->assertEqual($ret, $expect);
	}
/**
 * isOnlyInputType()のテストデータプロバイダ
 *
 * @return void
 */
	public function dataProviderIsOnlyInputTypeType() {
		$data = array(
			array(QuestionnairesComponent::TYPE_SELECTION, false),
			array(QuestionnairesComponent::TYPE_MULTIPLE_SELECTION, false),
			array(QuestionnairesComponent::TYPE_TEXT, true),
			array(QuestionnairesComponent::TYPE_TEXT_AREA, true),
			array(QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST, false),
			array(QuestionnairesComponent::TYPE_MATRIX_MULTIPLE, false),
			array(QuestionnairesComponent::TYPE_DATE_AND_TIME, true),
			array(QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX, false)
		);
		return $data;
	}
}
