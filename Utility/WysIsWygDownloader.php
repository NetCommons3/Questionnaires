<?php
/**
 * Questionnaires WysIsWyg Downloader
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */


App::uses('ZipDownloader', 'Files.Utility');
App::uses('UnZip', 'Files.Utility');

/**
 * QuestionnairesWysIsWygUtility
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class WysIsWygDownloader {

/**
 * getFromWysIsWygZIP
 * このメソッドはいずれはWYSISWYGエディタダウンロードコンポーネントへ移動します
 *
 * @param string $zipFilePath Zipファイルへのパス
 * @param string $file Zipファイルの中にある読み取るべきファイル名
 * @return string wysiswyg editor data
 */
	public function getFromWysIsWygZIP($zipFilePath, $file) {
		$unZip = new UnZip($zipFilePath);
		if ($unZip->extract() === false) {
			return false;
		}

		// FUJI
		// 本当はこの辺で添付ファイルをUPLOADSに設定する処理が入る
		//

		$filePath = $unZip->path . DS . $file;
		$file = new File($filePath);
		$data = $file->read();
		return $data;
	}
/**
 * createWysIsWygZIP
 * このメソッドはいずれはWYSISWYGエディタダウンロードコンポーネントへ移動します
 *
 * @param string $zipFileName Zipファイル名
 * @param string $data wysiswyg editor content
 * @return string path to zip file about "data"
 */
	public function createWysIsWygZIP($zipFileName, $data) {
		$zip = new ZipDownloader();
		$zip->addFromString($zipFileName, $data);

		//
		// FUJI
		// 本当はここにWysISWygエディタの中に添付されている画像ファイルなどを
		// zipに突っ込む処理が入る
		//

		$zip->close();
		return $zip->path;
	}
}
