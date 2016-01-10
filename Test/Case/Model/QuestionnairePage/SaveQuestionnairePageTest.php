<?php
/**
 * QuestionnaireQuestion::saveQuestionnairePage()のテスト
 *
 * @property QuestionnaireQuestion $Questionnaire
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
 * QuestionnaireQuestion::saveQuestionnairePage()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnairePage
 */
class QuestionnaireSaveQuestionnairePageTest extends QuestionnairesSaveTest {

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
	protected $_modelName = 'QuestionnairePage';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveQuestionnairePage';

/**
 * テストDataの取得
 *
 * @param string $key pageKey
 * @return array
 */
	private function __getData($key = null) {
		$data = array();
		$data['QuestionnairePage'][0] = array(
			'language_id' => '2',
			'questionnaire_id' => '34',	// 編集長が編集中のデータのIDです
			'page_title' => 'Page Title',
			'route_number' => '0',
			'page_sequence' => '0',
		);
		if (! is_null($key)) {
			$data['QuestionnairePage'][0]['key'] = $key;
		}
		return $data;
	}

/**
 * テストDataの取得(選択肢付き
 *
 * @param string $key pageKey
 * @return array
 */
	private function __getDataWithQuestion($key = null) {
		$data = $this->__getData($key);
		$questions = array();
		$questions[] = array(
			'language_id' => '2',
			'question_sequence' => '0',
			'question_value' => 'Question_1',
			'question_type' => 3, // 1:択一選択 | 2:複数選択 | 3:テキスト | 4:テキストエリア | 5:マトリクス（択一） | 6:マトリクス（複数） | 7:日付・時刻 | 8:リスト
			'description' => 'It is question description',
			'is_require' => false,
			'question_type_option' => '0', // '1: 数値 | 2:日付(未実装) | 3:時刻(未実装) | 4:メール(未実装) | 5:URL(未実装) | 6:電話番号(未実装) | HTML５チェックで将来的に実装されそうなものに順次対応'),
			'is_choice_random' => false,
			'is_skip' => false,
			'is_jump' => false,
			'is_range' => false,
			'min' => null,
			'max' => null,
			'is_result_display' => true,
			'result_display_type' => '0',
			'QuestionnaireChoice' => array(array(
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
			))
		);
		$data['QuestionnairePage'][0]['QuestionnaireQuestion'] = $questions;
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
			array($this->__getDataWithQuestion('page_key1')), //編集
			array($this->__getDataWithQuestion()), //新規
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
		$data = $this->__getDataWithQuestion();
		return array(
			array($data, 'Questionnaires.QuestionnairePage', 'save'),
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
		$data = $this->__getDataWithQuestion();
		$options = array(
			'questionIndex' => 0,
			'choiceIndex' => 0,
			'isSkip' => 0,
		);
		return array(
			array($data['QuestionnairePage'][0], $options, 'Questionnaires.QuestionnairePage'),
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
			'pageIndex' => 0,
			'maxPageIndex' => 0,
		);
		return array(
			array($this->__getDataWithQuestion(), $options, 'page_sequence', '2',
				__d('questionnaires', 'page sequence is illegal.')),
			array($this->__getData(), $options, 'page_sequence', '0',
				__d('questionnaires', 'please set at least one question.')),
		);
	}

}
