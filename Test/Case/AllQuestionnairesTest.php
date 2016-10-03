<?php
/**
 * Questionnaires All Test Suite
 *
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

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
		$suite = new NetCommonsTestSuite(sprintf('All %s Plugin tests', $plugin));

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
		$suite->addTestDirectoryRecursive(CakePlugin::path($plugin) . 'Test' . DS . 'Case');
		return $suite;
	}
}
