<?php
/**
 * QuestionnaireBlocksController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('TemporaryFolder', 'Files.Utility');
App::uses('CsvFileWriter', 'Files.Utility');
App::uses('ZipDownloader', 'Files.Utility');

/**
 * BlocksController
 *
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireBlocksController extends QuestionnairesAppController {

/**
 * csv download item count handling unit
 *
 * @var int
 */
	const	QUESTIONNAIRE_CSV_UNIT_NUMBER = 1000;

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Questionnaires.Questionnaire',
		'Questionnaires.QuestionnaireFrameSetting',
		'Questionnaires.QuestionnaireAnswerSummary',
		'Questionnaires.QuestionnaireAnswerSummaryCsv',
		'Blocks.Block',
		'Questionnaires.QuestionnaireExport',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'questionnaire_blocks')),
				'role_permissions' => array('url' => array('controller' => 'questionnaire_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'questionnaire_frame_settings')),
			),
		),
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'index,add,edit,delete' => 'block_editable',
			),
		),
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Session',
		'Blocks.BlockForm',
		'NetCommons.NetCommonsForm',
		'NetCommons.Date',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('index');
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		// 条件設定値取得
		// 条件設定値取得
		$conditions = $this->Questionnaire->getBaseCondition();

		// データ取得
		$subQuery = $this->Questionnaire->getQuestionnaireSubQuery();
		$this->paginate = array(
			'conditions' => $conditions,
			'page' => 1,
			'sort' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
			'limit' => QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
			'direction' => 'desc',
			'recursive' => 0,
			'joins' => $subQuery,
		);
		$questionnaire = $this->paginate('Questionnaire');
		if (! $questionnaire) {
			$this->view = 'not_found';
			return;
		}

		$this->set('questionnaires', $questionnaire);
	}

/**
 * download
 *
 * @return void
 * @throws InternalErrorException
 */
	public function download() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		$questionnaireKey = $this->_getQuestionnaireKeyFromPass();
		// キー情報をもとにデータを取り出す
		$questionnaire = $this->QuestionnaireAnswerSummaryCsv->getQuestionnaireForAnswerCsv($questionnaireKey);
		if (! $questionnaire) {
			$this->setAction('throwBadRequest');
			return;
		}
		// 圧縮用パスワードキーを求める
		if (! empty($this->request->data['AuthorizationKey']['authorization_key'])) {
			$zipPassword = $this->request->data['AuthorizationKey']['authorization_key'];
		} else {
			$this->NetCommons->setFlashNotification(__d('questionnaires', 'Setting of password is required always to download answers.'),
				array('interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL));
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'questionnaire_blocks',
				'action' => 'index',
				'frame_id' => Current::read('Frame.id'))));
			return;
		}

		try {
			$tmpFolder = new TemporaryFolder();
			$csvFile = new CsvFileWriter(array(
				'folder' => $tmpFolder->path
			));
			// 回答データを一気に全部取得するのは、データ爆発の可能性があるので
			// QUESTIONNAIRE_CSV_UNIT_NUMBER分に制限して取得する
			$offset = 0;
			do {
				$datas = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv($questionnaire, self::QUESTIONNAIRE_CSV_UNIT_NUMBER, $offset);
				// CSV形式で書きこみ
				foreach ($datas as $data) {
					$csvFile->add($data);
				}
				$dataCount = count($datas);	// データ数カウント
				$offset += $dataCount;		// 次の取得開始位置をずらす
			} while ($dataCount == self::QUESTIONNAIRE_CSV_UNIT_NUMBER);
			// データ取得数が制限値分だけとれている間は繰り返す

		} catch (Exception $e) {
			// NetCommonsお約束:エラーメッセージのFlash表示
			$this->NetCommons->setFlashNotification(__d('questionnaires', 'download error'),
				array('interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL));
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'questionnaire_blocks',
				'action' => 'index',
				'frame_id' => Current::read('Frame.id'))));
			return;
		}
		// Downloadの時はviewを使用しない
		$this->autoRender = false;
		// ダウンロードファイル名決定 アンケート名称をつける
		$zipFileName = $questionnaire['Questionnaire']['title'] . '.zip';
		$downloadFileName = $questionnaire['Questionnaire']['title'] . '.csv';
		// 出力
		return $csvFile->zipDownload(rawurlencode($zipFileName), $downloadFileName, $zipPassword);
	}

/**
 * export
 *
 * template file about questionnaire export action
 *
 * @return void
 */
	public function export() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		$questionnaireKey = $this->_getQuestionnaireKeyFromPass();
		// キー情報をもとにデータを取り出す
		$questionnaire = $this->QuestionnaireAnswerSummaryCsv->getQuestionnaireForAnswerCsv($questionnaireKey);
		if (! $questionnaire) {
			$this->setAction('throwBadRequest');
			return;
		}

		try {
			// zipファイル準備
			$zipFile = new ZipDownloader();

			// Export用のデータ配列を取得する
			$zipData = $this->QuestionnaireExport->getExportData($questionnaireKey);

			// Export用ファイルデータをZIPファイルに出力する
			// ※この中でWYSISWYGエディタデータは適宜処理されている
			$this->QuestionnaireExport->putToZip($zipFile, $zipData);

			// アーカイブ閉じる
			$zipFile->close();
		} catch(Exception $e) {
			$this->Session->setFlash(__d('questionnaires', 'export error') . $e->getMessage(),
				array('interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL));
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'questionnaire_blocks',
				'action' => 'index',
				'frame_id' => Current::read('Frame.id'))));
			return;
		}
		// 大外枠zipファイル準備
		$zipWrapperFile = new ZipDownloader();
		// アンケートデータファイルのフィンガープリントを得る
		$fingerPrint = sha1_file($zipFile->path, false);
		// フィンガープリントをアーカイブに加える
		$zipWrapperFile->addFromString(QuestionnairesComponent::QUESTIONNAIRE_FINGER_PRINT_FILENAME, $fingerPrint);
		// 本体ファイルを
		$zipWrapperFile->addFile($zipFile->path, QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_FILENAME);
		// export-key 設定
		$this->Questionnaire->saveExportKey($questionnaire['Questionnaire']['id'], $fingerPrint);

		// viewを使用しない
		$this->autoRender = false;

		// ダウンロード出力ファイル名確定
		$exportFileName = $questionnaire['Questionnaire']['title'] . '.zip';
		// 出力
		return $zipWrapperFile->download(rawurlencode($exportFileName));
	}
}