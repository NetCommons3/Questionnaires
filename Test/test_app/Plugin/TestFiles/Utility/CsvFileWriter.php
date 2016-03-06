<?php
/**
 * CsvFileWriter Test
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @author   AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Class CsvFileWriter Test
 */
class CsvFileWriter {

/**
 * csv lines
 *
 * @var array
 */
	public $lines = array();

/**
 * CsvFileWriter constructor.
 *
 * @param array $options folder => CSVファイルを生成するフォルダ header => array(key => ヘッダ名) ヘッダ&カラム名
 */
	public function __construct($options = array()) {
	}

/**
 * CSVファイルに追加する行データ
 *
 * @param array $line 配列
 * @return void
 */
	public function add(array $line) {
		$this->lines[] = $line;
	}

/**
 * zip download
 *
 * @param string $zipFilename Zipファイル名
 * @param string $csvFilename ZipされるCsvファイル名
 * @param string|null $password Zipにつけるパスワード
 * @return CakeResponse
 */
	public function zipDownload($zipFilename, $csvFilename, $password = null) {
		return array($zipFilename, $csvFilename, $password, $this->lines);
	}
}
