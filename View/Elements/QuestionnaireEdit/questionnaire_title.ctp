<?php
/**
 * questionnaire questionnaire edit title template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="bg-info">
	<h2 class="questionnaire-setting-ttl">
		<?php if (isset($this->data['Questionnaire']['title_icon'])) {
		echo $this->TitleIcon->titleIcon($this->data['Questionnaire']['title_icon']);
								}
		?>
		<?php echo h($this->data['Questionnaire']['title']); ?>
	</h2>
	<span class="help-block questionnaire-setting-ttl-help"><?php echo __d('questionnaires', 'If you want to change the questionnaire title, please edit in "Set questionnaire" step.'); ?></span>
</div>
