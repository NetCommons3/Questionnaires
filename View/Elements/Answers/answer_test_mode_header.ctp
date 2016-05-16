<?php
/**
 * questionnaire comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php if ($this->Workflow->canEdit('Questionnaire', $questionnaire) &&
		$questionnaire['Questionnaire']['status'] != WorkflowComponent::STATUS_PUBLISHED) : ?>

	<div class="alert alert-info">
		<p>
			<?php echo __d('questionnaires',
				'This questionnaire is being temporarily stored . You can questionnaire test before performed in this page . If you want to modify or change the questionnaire , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner .'); ?>
		</p>
	</div>

<?php endif;