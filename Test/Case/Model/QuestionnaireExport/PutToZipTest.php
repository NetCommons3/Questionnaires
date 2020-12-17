<?php
/**
 * QuestionnaireExport::putToZip()のテスト
 *
 * @property QuestionnaireExport $QuestionnaireExport
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');
App::uses('ZipDownloader', 'TestFiles.Utility');
App::uses('QuestionnaireFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnairePageFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireQuestionFixture', 'Questionnaires.Test/Fixture');
App::uses('QuestionnaireChoiceFixture', 'Questionnaires.Test/Fixture');

/**
 * QuestionnaireExport::putToZip()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireExport
 */
class QuestionnaireExportPutToZipTest extends NetCommonsGetTest {

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
		'plugin.m17n.language',
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.block_setting_for_questionnaire',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireExport';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'putToZip';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Questionnaires', 'TestFiles');
	}

/**
 * putToZip
 *
 * @param string $questionnaireKey 収集対象のアンケートキー
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testPutToZip($questionnaireKey, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//App::uses('ZipDownloader', 'TestFiles.Utility');
		App::uses('ZipDownloader', 'Questionnaires.Test/test_app/Plugin/TestFiles/Utility');

		$langCount = 2;	// 2 = 言語数
		$questionnaireId = intval($expected['questionnaireId']);

		$data = $this->$model->getExportData($questionnaireKey);
		$zipFile = new ZipDownloader();
		//テスト実行
		$this->$model->$method($zipFile, $data);

		//チェック
		// WysiswygエディタのZIPファイルが言語数分あるか
		$checkFiles = array(
			'total_comment',
			'thanks_content',
			'open_mail_body',
		);
		$addFiles = Hash::expand(array_flip($zipFile->addFiles));

		// アンケート本体のWysiswyg文章部分が言語数分あるかチェック
		foreach ($checkFiles as $file) {
			$records = Hash::extract($addFiles, 'Questionnaires.{n}.Questionnaire.' . $file . '.zip');
			$this->assertEqual(count($records), $langCount);
		}
		// 質問文が言語数×質問数文あるかチェック
		$records = Hash::extract($addFiles, 'Questionnaires.{n}.QuestionnairePage.{n}.QuestionnaireQuestion.{n}.description.zip');
		$this->assertEqual(count($records), $langCount * $expected['questionCount']);

		// ZIPファイルに追加されたJsonコードはアンケートの構造と同じか
		$jsonQuestionnaire = json_decode($zipFile->addStrings[QuestionnairesComponent::QUESTIONNAIRE_JSON_FILENAME], true);
		$orgQuestionnaire = $this->_getQuestionnaire($questionnaireId);
		$this->assertTrue($this->_hasSameArray($orgQuestionnaire, $jsonQuestionnaire['Questionnaires'][1]));
	}
/**
 * _hasSameArray
 *
 * @param array $part 期待値
 * @param array $hole 実際のデータ
 * @return bool
 */
	protected function _hasSameArray($part, $hole) {
		$flatPart = Hash::flatten($part);
		$flatHole = Hash::flatten($hole);
		foreach ($flatPart as $key => $val) {
			if (preg_match('/\.(id|key|total_comment|thanks_content|open_mail_body|description|created_user|created|modified_user|modified)$/', $key) == 1) {
				continue;
			}
			if (array_key_exists($key, $flatHole)) {
				$find = $flatHole[$key];
				if ($find != $val) {
					return false;
				}
			} else {
				return false;
			}
		}
		return true;
	}

/**
 * getExportDataのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		return array(
			// アンケートキー,ページ数,質問数
			array('questionnaire_6', array(
				'pageCount' => 1,
				'questionCount' => 1,
				'questionnaireId' => 6)),
		);
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
		$rec = Hash::extract($fixtureQuestionnaire->records, '{n}[id=' . $id . ']');
		$data['Questionnaire'] = $rec[0];

		$rec = Hash::extract($fixturePage->records, '{n}[questionnaire_id=' . $data['Questionnaire']['id'] . ']');
		$rec = Hash::extract($rec, '{n}[language_id=2]');
		$data['QuestionnairePage'] = $rec;

		foreach ($data['QuestionnairePage'] as &$page) {
			$pageId = $page['id'];

			$rec = Hash::extract($fixtureQuestion->records, '{n}[questionnaire_page_id=' . $pageId . ']');
			$rec = Hash::extract($rec, '{n}[language_id=2]');
			$page['QuestionnaireQuestion'] = $rec;
			$questionId = $rec[0]['id'];

			$rec = Hash::extract($fixtureChoice->records, '{n}[questionnaire_question_id=' . $questionId . ']');
			if ($rec) {
				$page['QuestionnaireQuestion'][0]['QuestionnaireChoice'] = $rec;
			}
		}
		return $data;
	}
}
