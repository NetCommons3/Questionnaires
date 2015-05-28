<?php
/**
 * BlocksController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * BlocksController
 *
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @package NetCommons\Questionnaires\Controller
 */
class BlocksController extends QuestionnairesAppController {

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
		'Blocks.Block',
		'Frames.Frame',
		'Questionnaires.Questionnaire',
		'Questionnaires.QuestionnaireFrameSetting',
		'Questionnaires.QuestionnairePage',
		'Questionnaires.QuestionnaireQuestion',
		'Questionnaires.QuestionnaireChoice',
		'Questionnaires.QuestionnaireAnswerSummary',
		'Questionnaires.QuestionnaireAnswerSummaryCsv',
		'Comments.Comment',
		'Categories.Category',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock',
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'blockEditable' => array('index', 'download')
			),
		),
		'Questionnaires.Questionnaires',
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
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

		$results = $this->camelizeKeyRecursive($this->NetCommonsFrame->data);
		$this->set($results);

		//タブの設定
		$this->initTabs('block_index');
	}

/**
 * index
 *
 * @return void
 * @throws Exception
 */
	public function index() {
		// 条件設定値取得
		$conditions = $this->getConditionForAnswer();
		// データ取得
		// Modelの方ではカスタムfindメソッドを装備している
		// ここではtype属性を指定することでカスタムFindを呼び出すように指示している
		try {
			$this->paginate = array(
				'conditions' => $conditions,
				'page' => 1,
				'sort' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
				'limit' => QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
				'direction' => 'desc',
				'recursive' => 0,
				'type' => 'getQListWithAnsCnt',
				'sessionId' => $this->Session->id(),
				'userId' => $this->Auth->user('id')
			);
			$questionnaire = $this->paginate('Questionnaire');
		} catch (NotFoundException $e) {
			// NotFoundの例外
			// アンケートデータが存在しないこととする
			$questionnaire = array();
		}
		$this->set('questionnaires', $questionnaire);
	}

/**
 * download
 *
 * @param int $frameId frame id
 * @param int $questionnaireId questionnaire origin id
 * @return void
 */
	public function download($frameId, $questionnaireId) {
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array(
				'origin_id' => $questionnaireId,
				'is_active' => true,
			)
		));
		if (empty($questionnaire)) {
			$this->Session->setFlash(__d('questionnaires', 'download error'));
			return;
		}
		$fileName = $questionnaire['Questionnaire']['title'] . '.csv';
		$offset = 0;
		// テンポラリファイルオープン
		$folder = new Folder();
		$folderName = TMP . 'Questionnaires' . DS . 'download' . DS . microtime(true);
		$folder->create($folderName);
		$folder->cd($folderName);

		// フォルダ内のお掃除
		$this->__cleanupDownloadFolder(TMP . 'Questionnaires' . DS . 'download');

		$filePath = tempnam($folder->pwd(), 'csv') . '.csv';
		$fp = fopen($filePath, 'w+');
		do {
			$datas = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv($questionnaire, QUESTIONNAIRE_CSV_UNIT_NUMBER, $offset);
			// テンポラリファイルにCSV形式で書きこみ
			foreach ($datas as $data) {
				fputcsv($fp, $data);
			}
			$offset += count($datas);
			$dataCount = count($datas);
		} while ($dataCount == QUESTIONNAIRE_CSV_UNIT_NUMBER);
		// ファイルクローズ
		fclose($fp);

		// 暗号圧縮？現時点ではコマンドでしか実行できない
		if ($this->__compressFile($filePath)) {
			$fileName = substr($fileName, 0, strrpos($fileName, '.')) . '.zip';
		}

		// 出力
		$this->response->file($filePath, array('download' => true, 'name' => $fileName));
		return $this->response;
	}

/**
 * __cleanupDownloadFolder
 *
 * @param string $dir cleanup dir
 * @return bool
 */
	private function __cleanupDownloadFolder($dir) {
		return true;
	}

/**
 * __compressFile
 *
 * @param string &$filePath input file path
 * @return bool
 */
	private function __compressFile(&$filePath) {
		$cmd = '/usr/bin/zip';
		if (!file_exists($cmd)) {
			return false;
		}

		$outputFilePath = $filePath . '.zip';

		$execCmd = sprintf('%s -j -e -P %s %s %s', $cmd, $this->Auth->user('username'), $outputFilePath, $filePath);

		// コマンドを実行する
		exec(escapeshellcmd($execCmd));

		// 入力ファイルを削除する
		@unlink($filePath);

		$filePath = $outputFilePath;
		return true;
	}

/**
 * initTabs
 *
 * @param string $activeTab Block active tab
 * @return void
 */
	public function initTabs($activeTab) {
		$block = $this->Block->find('first', array(
			'conditions' => array(
				'Block.room_id' => $this->viewVars['roomId'],
				'Block.plugin_key' => 'questionnaires'
			)
		));

		if (isset($block['Block']['id'])) {
			$blockId = (int)$block['Block']['id'];
		} else {
			$blockId = null;
		}

		//タブの設定
		$settingTabs = array(
			'tabs' => array(
				'block_index' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'blocks',
						'action' => 'index',
						$this->viewVars['frameId'],
					)
				),
				'frame_settings' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'questionnaire_frame_settings',
						'action' => 'edit',
						$this->viewVars['frameId'],
						$blockId
					)
				),
				'role_permissions' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'block_role_permissions',
						'action' => 'edit',
						$this->viewVars['frameId'],
						$blockId
					)
				),
			),
			'active' => $activeTab
		);
		$this->set('settingTabs', $settingTabs);
	}
}
