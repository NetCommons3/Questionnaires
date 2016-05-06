<?php
/**
 * メール設定 template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @author Mitsuru Mutaguchi <mutaguchi@opensource-workshop.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_MAIL_SETTING); ?>

	<div class="tab-content">
		<?php /** @see MailFormHelper::editFrom() */ ?>
		<?php echo $this->MailForm->editFrom(
			array(
				array(
					'mailBodyPopoverMessage' => __d('questionnaires', 'MailSetting.mail_fixed_phrase_body.popover'),
				),
				array(
					'mailBodyPopoverMessage' => __d('mails', 'MailSetting.mail_fixed_phrase_body.popover.answer'),
				),
			),
			NetCommonsUrl::backToIndexUrl('default_setting_action')
		); ?>
	</div>
</div>
