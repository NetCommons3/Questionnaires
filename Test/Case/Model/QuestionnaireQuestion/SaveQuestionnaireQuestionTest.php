<?php
/**
 * QuestionnaireQuestion::saveQuestionnaireQuestion()のテスト
 *
 * @property QuestionnaireQuestion $QuestionnaireQuestion
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
 * QuestionnaireQuestion::saveQuestionnaireQuestion()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireQuestion
 */
class QuestionnaireSaveQuestionnaireQuestionTest extends QuestionnairesSaveTest {

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
	protected $_methodName = 'saveQuestionnaireQuestion';

/**
 * テストDataの取得
 *
 * @param string $key questionKey
 * @param string $questionType
 * @return array
 */
	private function __getData($key = null, $questionType = '1') {
		$data = array();
		$data['QuestionnaireQuestion'][0] = array(
			'language_id' => '2',
			'question_sequence' => '0',
			'question_value' => 'Question_1',
			'question_type' => $questionType, // 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
			'description' => 'It is question description',
			'is_require' => false,
			'question_type_option' => '0', // '1: 数値 | 2:日付(未実装) | 3:時刻(未実装) | 4:メール(未実装) | 5:URL(未実装) | 6:電話番号(未実装) | HTML５チェックで将来的に実装されそうなものに順次対応'),
			'is_choice_random' => false,
			'is_choice_horizon' => false,
			'is_skip' => false,
			'is_jump' => false,
			'is_range' => false,
			'min' => null,
			'max' => null,
			'is_result_display' => true,
			'result_display_type' => '0',
			'questionnaire_page_id' => '54',	// 編集長が編集中のページデータのIDです
		);
		if (! is_null($key)) {
			$data['QuestionnaireQuestion'][0]['key'] = $key;
		}
		return $data;
	}

/**
 * テストDataの取得(選択肢付き
 *
 * @param string $questionType
 * @return array
 */
	private function __getDataWithChoice($questionType = '1') {
		$data = $this->__getData(null, $questionType);
		$choices = array();
		if ($questionType == '1' || $questionType == '2' || $questionType == '7') {
			$choices[] = array(
				'language_id' => '2',
				'matrix_type' => '0',
				'other_choice_type' => '0',
				'choice_sequence' => '0',
				'choice_label' => 'choice1',
				'choice_value' => 'choice1val',
				'skip_page_sequence' => null,
				'jump_route_number' => null,
				'graph_color' => '#ff0000',
			);
		} else {
			$choices[] = array(
				'language_id' => '2',
				'matrix_type' => '0',
				'other_choice_type' => '0',
				'choice_sequence' => '0',
				'choice_label' => 'choice1',
				'choice_value' => 'choice1val',
				'skip_page_sequence' => null,
				'jump_route_number' => null,
				'graph_color' => '#ff0000',
			);
			$choices[] = array(
				'language_id' => '2',
				'matrix_type' => '1',
				'other_choice_type' => '0',
				'choice_sequence' => '1',
				'choice_label' => 'choice1',
				'choice_value' => 'choice1val',
				'skip_page_sequence' => null,
				'jump_route_number' => null,
				'graph_color' => '#ff0000',
			);
		}
		$data['QuestionnaireQuestion'][0]['QuestionnaireChoice'] = $choices;
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
			array($this->__getData('question_key1')), //編集
			array($this->__getData()), //新規
			array($this->__getData(null, 3)), //新規
			array($this->__getData(null, 4)), //新規
			array($this->__getDataWithChoice('1')), //新規
			array($this->__getDataWithChoice('2')), //新規
			array($this->__getDataWithChoice('5')), //新規
			array($this->__getDataWithChoice('6')), //新規
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
		$data = $this->__getData();
		return array(
			array($data, 'Questionnaires.QuestionnaireQuestion', 'save'),
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
		$data = $this->__getData();
		$options = array(
			'questionIndex' => 0,
			'choiceIndex' => 0,
			'isSkip' => 0,
		);
		return array(
			array($data['QuestionnaireQuestion'][0], $options, 'Questionnaires.QuestionnaireQuestion'),
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
			'questionIndex' => 0,
			'choiceIndex' => 0,
			'isSkip' => 0,
			'pageIndex' => 0,
			'maxPageIndex' => 0,
		);
		return array(
			array($this->__getDataWithChoice(), $options, 'question_type', '12',
				__d('net_commons', 'Invalid request.')),
			array($this->__getData(), $options, 'question_type', '2',
				__d('questionnaires', 'please set at least one choice.')),
			array($this->__getDataWithChoice(), $options, 'question_type', '5',
				__d('questionnaires', 'please set at least one choice at row and column.')),
			array($this->__getDataWithChoice(), $options, 'min', '',
				__d('questionnaires', 'Please enter both the maximum and minimum values.'), array('question_type' => 3, 'is_range' => true, 'max' => 8)),
		);
	}

}
