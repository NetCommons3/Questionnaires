<?php
/**
 * QuestionnaireChoice::saveQuestionnaireChoice()のテスト
 *
 * @property QuestionnaireChoice $QuestionnaireChoice
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesSaveTest', 'Questionnaires.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireChoice::saveQuestionnaireChoice()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireChoice
 */
class QuestionnaireSaveQuestionnaireChoiceTest extends QuestionnairesSaveTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaire_choices';

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
	protected $_modelName = 'QuestionnaireChoice';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveQuestionnaireChoice';

/**
 * テストDataの取得
 *
 * @param string $faqKey faqKey
 * @return array
 */
	private function __getData() {
		$data = array(
			'QuestionnaireChoice' => array(
				'language_id' => '2',
				'matrix_type' => '0',
				'other_choice_type' => '0',
				'choice_sequence' => '0',
				'choice_label' => 'choice1',
				'choice_value' => 'choice1val',
				'skip_page_sequence' => null,
				'jump_route_number' => null,
				'graph_color' => '#ff0000',
				'questionnaire_question_id' => '2',
			)
		);
		return $data;
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		return array(
			array($this->__getData()), //新規
		);
	}

/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderSaveOnExceptionError() {
		return array(
			array($this->__getData(), 'Questionnaires.QuestionnaireChoice', 'save'),
		);
	}
/**
 * SaveのValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *
 * @return void
 */
	public function dataProviderSaveOnValidationError() {
		$options = array(
			'choiceIndex' => 0,
			'isSkip' => 0,
		);
		return array(
			array($this->__getData(), $options, 'Questionnaires.QuestionnaireChoice'),
		);
	}
/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ
 *
 * @return void
 */
	public function dataProviderValidationError() {
		$options = array(
			'choiceIndex' => 0,
			'isSkip' => 0,
			'pageIndex' => 0,
			'maxPageIndex' => 0,
		);
		$skipOptions = array(
			'choiceIndex' => 0,
			'isSkip' => 1,
			'pageIndex' => 0,
			'maxPageIndex' => 0,
		);
		return array(
			array($this->__getData(), $options, 'choice_label', '',
				__d('questionnaires', 'Please input choice text.')),
			array($this->__getData(), $options, 'choice_label', 'has|data:abc',
				__d('questionnaires', 'You can not use the character of |, : for choice text ')),
			array($this->__getData(), $options, 'other_choice_type', 'abc',
				__d('net_commons', 'Invalid request.')),
			array($this->__getData(), $options, 'choice_sequence', '1',
				__d('questionnaires', 'choice sequence is illegal.')),
			array($this->__getData(), $options, 'graph_color', 'avvv1',
				__d('questionnaires', 'First character is "#". And input the hexadecimal numbers by six digits.')),
			array($this->__getData(), $skipOptions, 'skip_page_sequence', '9',
				__d('questionnaires', 'Invalid skip page. page does not exist.')),
			array($this->__getData(), $skipOptions, 'skip_page_sequence', '0',
				__d('questionnaires', 'Invalid skip page. Please set forward page.')),
			array($this->__getData(), $skipOptions, 'skip_page_sequence', null,
				__d('questionnaires', 'Invalid skip page. page does not exist.')),
		);
	}

}
