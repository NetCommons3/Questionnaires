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
class QuestionnaireAnswerValidateTest extends NetCommonsModelTestCase {

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
	public function testValidationError(
		$data, $summaryId, $targetQuestion, $field, $value, $message, $overwrite = array()) {
		$model = $this->_modelName;

		if (! is_null($field)) {
			if (is_null($value)) {
				unset($data[0][$field]);
			} else {
				$data[0][$field] = $value;
			}
		}
		$questionnaire = $this->_getQuestionnaire(2);
		$addQuestion = array(
			'QuestionnairePage' => array(
				array(
					'QuestionnaireQuestion' => array($targetQuestion)
				)
			)
		);
		$questionnaire = Hash::merge($questionnaire, $addQuestion);
		$summary = array('QuestionnaireAnswerSummary' => array('id' => $summaryId));
		$result = $this->$model->saveAnswer(
			array('QuestionnaireAnswer' => array($targetQuestion['key'] => $data)),
			$questionnaire,
			$summary);
		$this->assertFalse($result);

		if ($message) {
			//$this->assertEquals($validationErrors[0][$field][0], $message);
			// アンケートの回答時のエラーメッセージはすべてこのフィールドに集約してるのだった
			$this->assertEquals(
				$this->$model->validationErrors[$targetQuestion['key']][0]['answer_value'][0],
				$message);
		}
	}

/**
 * _getQuestion
 *
 * @param int $id 質問ID
 * @return array
 */
	protected function _getQuestion($id) {
		$fixtureQuestion = new QuestionnaireQuestionFixture();
		$fixtureChoice = new QuestionnaireChoiceFixture();
		$rec = Hash::extract($fixtureQuestion->records, '{n}[id=' . $id . ']');
		$question = $rec[0];
		$rec = Hash::extract($fixtureChoice->records, '{n}[questionnaire_question_id=' . $id . ']');
		$question['QuestionnaireChoice'] = $rec;
		return $question;
	}

/**
 * _getQuestionnaire
 *
 * @param int $id 質問ID
 * @return array
 */
	protected function _getQuestionnaire($id) {
		$fixtureQuestionnaire = new QuestionnaireFixture();
		$fixturePage = new QuestionnairePageFixture();
		$fixtureQuestion = new QuestionnaireQuestionFixture();
		$fixtureChoice = new QuestionnaireChoiceFixture();

		$data = array();
		$rec = Hash::extract($fixtureQuestionnaire->records,
			'{n}[id=' . $id . ']');
		$data['Questionnaire'] = $rec[0];

		$rec = Hash::extract($fixturePage->records,
			'{n}[questionnaire_id=' . $data['Questionnaire']['id'] . ']');
		$rec = Hash::extract($rec,
			'{n}[language_id=2]');
		$data['QuestionnairePage'] = $rec;

		foreach ($data['QuestionnairePage'] as &$page) {
			$pageId = $page['id'];

			$rec = Hash::extract($fixtureQuestion->records,
				'{n}[questionnaire_page_id=' . $pageId . ']');
			$rec = Hash::extract($rec, '{n}[language_id=2]');
			$page['QuestionnaireQuestion'] = $rec;
			$questionId = $rec[0]['id'];

			$rec = Hash::extract($fixtureChoice->records,
				'{n}[questionnaire_question_id=' . $questionId . ']');
			if ($rec) {
				$page['QuestionnaireQuestion'][0]['QuestionnaireChoice'] = $rec;
			}
		}
		return $data;
	}

}
