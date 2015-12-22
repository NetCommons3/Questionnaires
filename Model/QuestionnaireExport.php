<?php
/**
 * QuestionnaireExport Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');
App::uses('WysIsWygDownloader', 'Questionnaires.Utility');

/**
 * Summary for Questionnaire Model
 */
class QuestionnaireExport extends QuestionnairesAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'questionnaires';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'AuthorizationKeys.AuthorizationKey',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * getExportData
 *
 * @param string $questionnaireKey アンケートキー
 * @return array QuestionnaireData for Export
 */
	public function getExportData($questionnaireKey) {
		// アンケートデータをjsonにして記述した内容を含むZIPファイルを作成する
		$zipData = array();

		// バージョン情報を取得するためComposer情報を得る
		$Plugin = ClassRegistry::init('Plugins.Plugin');
		$composer = $Plugin->getComposer('netcommons/questionnaires');
		// 最初のデータはアンケートプラグインのバージョン
		$zipData['version'] = $composer['version'];

		// 言語数分
		$Language = ClassRegistry::init('Languages.Language');
		$languages = $Language->find('all', array(
			'recursive' => -1
		));
		$Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$questionnaires = array();
		foreach ($languages as $lang) {
			// 指定のアンケートデータを取得
			$questionnaire = $Questionnaire->find('first', array(
				'conditions' => array(
					'Questionnaire.key' => $questionnaireKey,
					'Questionnaire.is_active' => true,
					'Questionnaire.is_latest' => true,
					'Questionnaire.language_id' => $lang['Language']['id']
				),
				'recursive' => 0
			));
			// 指定の言語データがない場合もあることを想定
			if (empty($questionnaire)) {
				continue;
			}
			$questionnaire = Hash::remove($questionnaire, 'Block');
			$questionnaire = Hash::remove($questionnaire, 'TrackableCreator');
			$questionnaire = Hash::remove($questionnaire, 'TrackableUpdater');
			$Questionnaire->clearQuestionnaireId($questionnaire);
			$questionnaires[] = $questionnaire;
		}
		$zipData['Questionnaires'] = $questionnaires;
		return $zipData;
	}
/**
 * putToZip
 *
 * @param ZipDownloader $zipFile ZIPファイルオブジェクト
 * @param array $zipData zip data
 * @return void
 */
	public function putToZip($zipFile, $zipData) {
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage');
		$this->QuestionnaireQuestion = ClassRegistry::init('Questionnaires.QuestionnaireQuestion');

		$wysiswyg = new WysIsWygDownloader();

		// アンケートデータの中でもWYSISWYGデータのものについては
		// フォルダ別に確保(フォルダの中にZIPがある）
		$flatQuestionnaire = Hash::flatten($zipData);
		foreach ($flatQuestionnaire as $key => &$value) {
			$model = null;
			if (strpos($key, 'QuestionnaireQuestion.') !== false) {
				$model = $this->QuestionnaireQuestion;
			} elseif (strpos($key, 'QuestionnairePage.') !== false) {
				$model = $this->QuestionnairePage;
			} elseif (strpos($key, 'Questionnaire.') !== false) {
				$model = $this->Questionnaire;
			}
			if (!$model) {
				continue;
			}
			$columnName = substr($key, strrpos($key, '.') + 1);
			if ($model->hasField($columnName)) {
				if ($model->getColumnType($columnName) == 'text') {
					$wysiswygZipFile = $wysiswyg->createWysIsWygZIP($model->alias . '.' . $columnName, $value);
					$wysiswygFileName = $key . '.zip';
					$zipFile->addFile($wysiswygZipFile, $wysiswygFileName);
					$value = $wysiswygFileName;
				}
			}
		}
		$questionnaire = Hash::expand($flatQuestionnaire);
		// jsonデータにして書き込み
		$zipFile->addFromString(QuestionnairesComponent::QUESTIONNAIRE_JSON_FILENAME, json_encode($questionnaire));
	}
}
