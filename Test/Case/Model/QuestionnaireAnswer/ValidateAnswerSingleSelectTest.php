<?php
/**
 * QuestionnaireAnswer::validate()のテスト
 *
 * @property QuestionnaireAnswer $QuestionnaireAnswer
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');
App::uses('QuestionnaireQuestionFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireChoiceFixture', 'Questionnaires.Test/Fixture');

/**
 * QuestionnaireAnswer::validate()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireAnswer
 */
class ValidateQuestionnaireAnswerSingleSelectTest extends NetCommonsModelTestCase {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.questionnaire_setting',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireAnswer';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveMany';

/**
 * Validatesのテスト
 *
 * @param array $data 登録データ
 * @param int $summaryId サマリID
 * @param array $targetQuestion 質問データ
 * @param string $field フィールド名
 * @param string $value セットする値
 * @param string $message エラーメッセージ
 * @param array $overwrite 上書きするデータ
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidationError($data, $summaryId, $targetQuestion, $field, $value, $message, $overwrite = array()) {
		$model = $this->_modelName;

		if (is_null($value)) {
			unset($data[0][$field]);
		} else {
			$data[0][$field] = $value;
		}

		$options = array(
			'questionnaire_answer_summary_id' => $summaryId,
			'question' => $targetQuestion,
			'allAnswers' => $data,
			'validate' => 'only'
		);
		//validate処理実行
		$result = $this->$model->saveMany($data, $options);
		$this->assertFalse($result);
		$validationErrors = Hash::filter($this->$model->validationErrors);

		if ($message) {
			//$this->assertEquals($validationErrors[0][$field][0], $message);
			// アンケートの回答時のエラーメッセージはすべてこのフィールドに集約してるのだった
			$this->assertEquals($validationErrors[0]['answer_value'][0], $message);
		}
	}

/**
 * __getData
 *
 * @param string $qKey 質問キー
 * @param int $summaryId サマリID
 * @return array
 */
	private function __getData($qKey, $summaryId) {
		$answerData = array(
			array(
				'questionnaire_answer_summary_id' => $summaryId,
				'answer_value' => '|choice_2:choice label1',
				'questionnaire_question_key' => $qKey,
				'id' => '',
				'matrix_choice_key' => '',
				'other_answer_value' => ''
			)
		);
		return $answerData;
	}

/**
 * __getQuestion
 *
 * @param int $id 質問ID
 * @return array
 */
	private function __getQuestion($id) {
		$fixtureQuestion = new QuestionnaireQuestionFixture();
		$fixtureChoice = new QuestionnaireChoiceFixture();
		$rec = Hash::extract($fixtureQuestion->records, '{n}[id=' . $id . ']');
		$question = $rec[0];
		$rec = Hash::extract($fixtureChoice->records, '{n}[questionnaire_question_id=' . $id . ']');
		$question['QuestionnaireChoice'] = $rec;
		return $question;
	}
/**
 * testValidationErrorのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderValidationError() {
		$data = $this->__getData('qKey_1', 3);
		// 通常の質問
		$normalQuestion = $this->__getQuestion(2);
		// 解答必須質問
		$requireQuestion = Hash::merge($normalQuestion, array('is_require' => QuestionnairesComponent::REQUIRES_REQUIRE));
		// その他回答がある質問
		$otherQuestion = Hash::merge($normalQuestion, array('QuestionnaireChoice' => array(array('other_choice_type' => 1))));
		return array(
			array($data, 3, $normalQuestion, 'answer_value', 'aaa',
				__d('questionnaires', 'Invalid choice')),
			array($data, 3, $requireQuestion, 'answer_value', '',
				__d('questionnaires', 'Input required')),
			array($data, 3, $otherQuestion, 'other_answer_value', '',
				__d('questionnaires', 'Please enter something, if you chose the other item')),
		);
	}
}
