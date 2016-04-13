<?php
/**
 * メール設定データのMigration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * メール設定データのMigration
 *
 * @package NetCommons\Questionnaires\Config\Migration
 */
class QuestionnaireMailSettingRecords extends NetCommonsMigration {

/**
 * プラグインキー
 *
 * @var string
 */
	const PLUGIN_KEY = 'questionnaires';

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'mail_setting_records';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(),
		'down' => array(),
	);

/**
 * mail setting data
 *
 * @var array $migration
 */
	public $records = array(
		'MailSetting' => array(
			//コンテンツ通知 - 設定
			array(
				'plugin_key' => self::PLUGIN_KEY,
				'block_key' => null,
				'is_mail_send' => false,
			),
		),
		'MailSettingFixedPhrase' => array(
			//コンテンツ通知 - 定型文
			// * 英語
			array(
				'language_id' => '1',
				'plugin_key' => self::PLUGIN_KEY,
				'block_key' => null,
				'type_key' => 'contents',
				'mail_fixed_phrase_subject' => '', //デフォルト(__d('mails', 'MailSetting.mail_fixed_phrase_subject'))
				'mail_fixed_phrase_body' => '', //デフォルト(__d('mails', 'MailSetting.mail_fixed_phrase_body'))
			),
			// * 日本語
			array(
				'language_id' => '2',
				'plugin_key' => self::PLUGIN_KEY,
				'block_key' => null,
				'type_key' => 'contents',
				'mail_fixed_phrase_subject' => '[{X-SITE_NAME}-{X-PLUGIN_NAME}]{X-SUBJECT}({X-ROOM})',
				'mail_fixed_phrase_body' => '{X-SUBJECT}に回答されたのでお知らせします。
ルーム名:{X-ROOM}
アンケート名:{X-SUBJECT}
回答者:{X-USER}
回答日時:{X-TO_DATE}

この回答内容を確認するにはCSVファイルをダウンロードする必要があります。
（CSVファイルのダウンロードには編集長以上の権限が必要です）
下記のリンクをクリックして下さい。
{X-URL}',
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$this->loadModels(array(
			'MailSetting' => 'Mails.MailSetting',
			'MailSettingFixedPhrase' => 'Mails.MailSettingFixedPhrase',
		));
		foreach ($this->records as $model => $records) {
			if ($direction == 'up') {
				if ($model == 'MailSettingFixedPhrase') {
					// mail_setting_id セット
					$data = $this->MailSetting->find('first', array(
						'recursive' => -1,
						'conditions' => array('plugin_key' => self::PLUGIN_KEY),
						'callbacks' => false,
					));
					foreach ($records as &$record) {
						$record['mail_setting_id'] = $data['MailSetting']['id'];
					}
				}
				if (!$this->updateRecords($model, $records)) {
					return false;
				}
			} elseif ($direction == 'down') {
				$conditions = array(
					'plugin_key' => self::PLUGIN_KEY,
					'block_key' => null,
				);
				if (!$this->MailSettingFixedPhrase->deleteAll($conditions, false, false)) {
					return false;
				}
				if (!$this->MailSetting->deleteAll($conditions, false, false)) {
					return false;
				}
			}
		}
		return true;
	}
}
