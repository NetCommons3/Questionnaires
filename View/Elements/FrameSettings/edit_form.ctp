<?php
/**
 * Questionnaire frame display setting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->hidden('id'); ?>
<?php echo $this->NetCommonsForm->hidden('frame_key'); ?>
<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

<div class="col-sm-12 form-group">
	<?php echo $this->element('Questionnaires.FrameSettings/edit_display_type'); ?>
</div>

<div class="col-sm-12 form-group" ng-show="questionnaireFrameSettings.displayType == <?php echo QuestionnairesComponent::DISPLAY_TYPE_LIST; ?>">
	<?php echo $this->element('Questionnaires.FrameSettings/edit_list_display_option'); ?>
</div>

<div class="col-sm-12 form-group">
	<?php echo $this->element('Questionnaires.FrameSettings/edit_display_questionnaire'); ?>
</div>