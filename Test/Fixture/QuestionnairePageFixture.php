<?php
/**
 * QuestionnairePageFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuestionnairePageFixture
 */
class QuestionnairePageFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'questionnaire_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'page_title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ページ名', 'charset' => 'utf8'),
		'route_number' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'page_sequence' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'ページ表示順'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_questionnaire_pages_questionnaires1_idx' => array('column' => 'questionnaire_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array();

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		$id = 1;
		$sevenPages = [3];
		$threePages = [7, 11, 35, 39, 43];
		for ($i = 1; $i <= 50; $i = $i + 2) {
			$repeat = 1;
			if (in_array($i, $threePages)) {
				$repeat = 3;
			} elseif (in_array($i, $sevenPages)) {
				$repeat = 7;
			}
			for ($ii = 0; $ii < $repeat; $ii++) {
				$this->records[] = $this->getPage($id, $id, $i, 1, $ii);
				$this->records[] = $this->getPage($id + 1, $id, $i + 1, 2, $ii);
				$id = $id + 2;
			}
		}
		parent::init();
	}
/**
 * page the fixture.
 *
 * @param int $id page id
 * @param int $key key number
 * @param int $qId questionnaire id
 * @param int $langId language id
 * @param int $pageSeq page sequence
 * @return array
 */
	public function getPage($id, $key, $qId, $langId, $pageSeq = 0) {
		return array(
			'id' => $id,
			'key' => 'page_' . strval($key),
			'language_id' => $langId,
			'questionnaire_id' => $qId,
			'page_title' => 'Page Title',
			'route_number' => 0,
			'page_sequence' => $pageSeq,
			'created_user' => $this->getCreatedUser($qId),
			'created' => '2016-01-05 09:00:00',
			'modified_user' => $this->getCreatedUser($qId),
			'modified' => '2016-01-05 09:00:00',
		);
	}
/**
 * page creator the fixture.
 *
 * @param int $qId questionnaire id
 * @return int
 */
	public function getCreatedUser($qId) {
		$admin = array(1, 2, 3, 4, 13, 14, 19, 20, 33, 34, 39, 40, 45, 46);
		$chief = array(5, 6, 7, 8, 15, 16, 21, 22, 25, 26, 29, 30, 35, 36, 41, 42, 47, 48);
		if (in_array($qId, $admin)) {
			return 1;
		}
		if (in_array($qId, $chief)) {
			return 3;
		}
		return 4;
	}
}
