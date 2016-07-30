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
	<h1 ng-cloak class="">
		<?php if (isset($this->data['Questionnaire']['title_icon'])) {
			echo $this->TitleIcon->titleIcon($this->data['Questionnaire']['title_icon']);
								}?>
		{{questionnaire.questionnaire.title}}
		<?php if ($this->action != 'edit'): ?>
		<small>
			<div class="help-block small">
				<?php echo __d('questionnaires', 'If you want to change the questionnaire title, please edit in "Set questionnaire" step.'); ?>
			</div>
		</small>
		<?php endif; ?>
	</h1>
