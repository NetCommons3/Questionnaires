<?php
/**
 * Questionnaires WysIsWyg Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuestionnairesWysIsWygComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnairesWysIsWygComponent extends Component {

/**
 * getFromWysIsWygZIP
 *このメソッドはいずれはWYSISWYGエディタダウンロードコンポーネントへ移動します
 *
 * @param string $zipFilePath Zipファイルへのパス
 * @param string $file Zipファイルの中にある読み取るべきファイル名
 * @return string wysiswyg editor data
 */
	public function getFromWysIsWygZIP($zipFilePath, $file) {
		$zip = new ZipArchive;
		if ($zip->open($zipFilePath) !== true) {
			return false;
		}
		if ($zip->extractTo(pathinfo($zipFilePath, PATHINFO_DIRNAME)) === false) {
			return false;
		}
		$zip->close();

		//
		// 本当はこの辺で添付ファイルをUPLOADSに設定する処理が入る
		//

		$filePath = pathinfo($zipFilePath, PATHINFO_DIRNAME) . DS . $file;
		$fileSize = filesize($filePath);
		if ($fileSize == 0) {
			return '';
		}
		$fp = fopen($filePath, 'rb');
		$data = fread($fp, $fileSize);
		fclose($fp);
		return $data;
	}
/**
 * createWysIsWygZIP
 * このメソッドはいずれはWYSISWYGエディタダウンロードコンポーネントへ移動します
 *
 * @param Folder $folder 保存先フォルダオブジェクト
 * @param string $zipFileName Zipファイルへのパス
 * @param string $data wysiswyg editor content
 * @return string path to zip file about "data"
 */
	public function createWysIsWygZIP($folder, $zipFileName, $data) {
		$fileName = $zipFileName . '.zip';
		$zip = new ZipArchive();
		$filePath = $folder->pwd() . DS . $fileName;
		$zipFp = $zip->open($filePath, ZipArchive::CREATE);
		if ($zipFp === true) {
			$zip->addFromString($zipFileName, $data);
		} else {
			return null;
		}

		//
		// 本当はここにWysISWygエディタの中に添付されている画像ファイルなどを
		// zipに突っ込む処理が入る
		//

		$zip->close();
		return $filePath;
	}
}