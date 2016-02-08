<?php
/**
 * questionnaire summaries view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo __d('questionnaires', 'you will not be able to see this result.'); ?>
<?php if ($displayType == QuestionnairesComponent::DISPLAY_TYPE_LIST): ?>
	<div class="text-center">
		<?php echo $this->BackTo->pageLinkButton(__d('questionnaires', 'Back to Top'), array('icon' => 'chevron-left')); ?>
	</div>
<?php endif;
