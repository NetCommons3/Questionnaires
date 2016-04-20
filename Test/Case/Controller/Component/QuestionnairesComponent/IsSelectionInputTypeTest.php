<?php
/**
 * QuestionnaireComponent::isSelectionInputType()のテスト
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
 * QuestionnaireComponent::isSelectionInputType()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Questionnaire\Test\Case\Controller\Component\QuestionnaireComponent
 */
class QuestionnaireComponentIsSelectionInputTypeTest extends NetCommonsControllerTestCase {

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
 * isSelectionInputType()のテスト
 *
 * @param int $type チェック種別
 * @param mix $expect 期待値
 * @dataProvider dataProviderIsSelectionInputType
 * @return void
 */
	public function testIsSelectionInputType($type, $expect) {
		$ret = QuestionnairesComponent::isSelectionInputType($type);
		$this->assertEqual($ret, $expect);
	}
/**
 * isSelectionInputType()のテストデータプロバイダ
 *
 * @return void
 */
	public function dataProviderIsSelectionInputType() {
		$data = array(
			array(QuestionnairesComponent::TYPE_SELECTION, true),
			array(QuestionnairesComponent::TYPE_MULTIPLE_SELECTION, true),
			array(QuestionnairesComponent::TYPE_TEXT, false),
			array(QuestionnairesComponent::TYPE_TEXT_AREA, false),
			array(QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST, false),
			array(QuestionnairesComponent::TYPE_MATRIX_MULTIPLE, false),
			array(QuestionnairesComponent::TYPE_DATE_AND_TIME, false),
			array(QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX, true)
		);
		return $data;
	}
}
