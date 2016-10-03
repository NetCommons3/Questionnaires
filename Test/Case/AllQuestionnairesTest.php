<?php
/**
 * Questionnaires All Test Suite
 *
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('NetCommonsTestSuite', 'NetCommons.TestSuite');

/**
 * Questionnaires All Test Suite
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Announcements\Test\Case
 * @codeCoverageIgnore
 */

class AllQuestionnairesTest extends CakeTestSuite {

/**
 * All test suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$plugin = preg_replace('/^All([\w]+)Test$/', '$1', __CLASS__);
		$suite = new CakeTestSuite(sprintf('All %s Plugin tests', $plugin));
		/*
		$Folder = new Folder(CakePlugin::path($plugin) . 'Test' . DS . 'Case');
		$files = $Folder->tree(null, true, 'files');

		foreach ($files as $file) {
			if (preg_match('/\/All([\w]+)Test\.php$/', $file)) {
				continue;
			}
			if (substr($file, -8) === 'Test.php') {
				var_dump($file);
			}
		}
		*/
		//$suite->addTestDirectoryRecursive(CakePlugin::path($plugin) . 'Test' . DS . 'Case');

		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/Component/QuestionnaireOwnAnswerComponent/GetConfirmSummaryOfThisUserTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/Component/QuestionnaireOwnAnswerComponent/GetOwnAnsweredKeysTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/Component/QuestionnaireOwnAnswerComponent/GetProgressiveSummaryOfThisUserTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/Component/QuestionnaireOwnAnswerComponent/SaveOwnAnsweredKeysTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/Component/QuestionnairesComponent/IsOnlyInputTypeTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/Component/QuestionnairesComponent/IsSelectionInputTypeTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireAddController/AddTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireAnswerSummariesController/ViewTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireAnswersController/PostTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireAnswersController/ViewTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireBlockRolePermissionsController/EditTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireBlocksController/DownloadTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireBlocksController/ExportTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireBlocksController/IndexTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireEditController/CancelTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireEditController/DeleteTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireEditController/EditQuestionTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireEditController/EditResultTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireEditController/EditTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireFrameSettingsController/EditTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnaireMailSettingCntroller/EditTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Controller/QuestionnairesController/IndexTest.php');

		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/ActionQuestionnaireAdd/CerateFromReuseTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/ActionQuestionnaireAdd/CheckPastQuestionnaireTest.php');

		/*
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/ActionQuestionnaireAdd/CreateNewTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/ActionQuestionnaireAdd/CreatefromTemplateTest.php');

		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/Behavior/QuestionnaireValidateBehavior/CheckDateCompTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/Behavior/QuestionnaireValidateBehavior/CheckDateTimeTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/Behavior/QuestionnaireValidateBehavior/RequireOtherFieldsTest.php');

		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswer/SaveAnswerTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswer/ValidateAnswerDatetimeTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswer/ValidateAnswerMultipleMatrixTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswer/ValidateAnswerMultipleSelectTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswer/ValidateAnswerSingleMatrixTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswer/ValidateAnswerSingleSelectTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswer/ValidateAnswerTextAreaTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswer/ValidateAnswerTextTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswerSummary/ForceGetProgressiveAnswerSummaryTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswerSummary/GetAggregateTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswerSummary/GetProgressiveSummaryTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswerSummary/SaveAnswerStatusTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireAnswerSummaryCsv/GetAnswerSummaryCsvTest.php');

		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireChoice/GetDefaultChoiceTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireChoice/SaveQuestionnaireChoiceTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireExport/GetExportDataTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireExport/PutToZipTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireFrameDisplayQuestionnaire/SaveFrameDisplayQuestionnaireTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireFrameDisplayQuestionnaire/ValidateFrameDisplayQuestionnaireTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireFrameSetting/GetQuestionnaireFrameSettingTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireFrameSetting/SaveFrameSettingsTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireFrameSetting/ValidateQuestionnaireFrameSettingTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnairePage/GetDefaultPageTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnairePage/GetNextPageTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnairePage/SaveQuestionnairePageTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireQuestion/GetDefaultQuestionTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireQuestion/SaveQuestionnaireQuestionTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireSetting/SaveBlockTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireSetting/SaveQuestionnaireSettingTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/QuestionnaireSetting/SaveSettingTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/Questionnaires/AfterFrameSaveTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/Questionnaires/DeleteQuestionnaireTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/Questionnaires/SaveExportKeyTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/Model/Questionnaires/SaveQuestionnaireTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/View/Elements/AnswerSummaries/AggregateMatrixTableTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/View/Elements/AnswerSummaries/AggregateTableTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/View/Elements/QuestionnaireEdit/Edit/questionnaire_method/GroupMethodTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/View/Helper/QuestionEdit/GetJsPostDataTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/View/Helper/QuestionnaireAnswer/ChoiceTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/View/Helper/QuestionnaireAnswer/DatetimeTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/View/Helper/QuestionnaireAnswer/TextTest.php');
		$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . '/View/Helper/QuestionnaireAnswer/MatrixTest.php');
		*/
		return $suite;
	}
}
