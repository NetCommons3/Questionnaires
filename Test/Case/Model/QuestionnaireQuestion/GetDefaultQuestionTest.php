<?php
/**
 * QuestionnaireQuestion::getDefaultQuestion()のテスト
 *
 * @property QuestionnaireQuestion $QuestionnaireQuestion
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireQuestion::getDefaultQuestion()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireQuestion
 */
class QuestionnaireGetDefaultQuestionTest extends NetCommonsGetTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireQuestion';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getDefaultQuestion';

/**
 * getDefaultQuestionのテスト
 *
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetDefaultQuestion($expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method();
		// Choiceは省く
		$result = Hash::remove($result, 'QuestionnaireChoice');

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getDefaultQuestionのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			'question_sequence' => 0,
			'question_value' => __d('questionnaires', 'New Question') . '1',
			'question_type' => QuestionnairesComponent::TYPE_SELECTION,
			'is_require' => QuestionnairesComponent::USES_NOT_USE,
			'is_skip' => QuestionnairesComponent::SKIP_FLAGS_NO_SKIP,
			'is_choice_random' => QuestionnairesComponent::USES_NOT_USE,
			'is_range' => QuestionnairesComponent::USES_NOT_USE,
			'is_result_display' => QuestionnairesComponent::EXPRESSION_SHOW,
			'result_display_type' => QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART
		);
		return array(
			array($expect),
		);
	}

}
