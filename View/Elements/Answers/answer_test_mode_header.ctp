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
<?php if ($this->Workflow->canEdit('Questionnaire', $questionnaire)) : ?>

	<?php
		$answerHeaderClass = '';
		if ($questionnaire['Questionnaire']['status'] != WorkflowComponent::STATUS_PUBLISHED) {
			$answerHeaderClass = 'alert alert-info';
		}
	?>

	<div class="<?php echo $answerHeaderClass; ?>">
		<div class="pull-right">
			<?php echo $this->Button->editLink('', array(
			'plugin' => 'questionnaires',
			'controller' => 'questionnaire_edit',
			'action' => 'edit_question',
			'key' => $questionnaire['Questionnaire']['key'])); ?>
		</div>

		<?php if ($questionnaire['Questionnaire']['status'] != WorkflowComponent::STATUS_PUBLISHED): ?>
			<h3><?php echo __d('questionnaires', 'Test Mode'); ?></h3>
			<div class="clearfix"></div>
			<p>
				<?php echo __d('questionnaires',
					'This questionnaire is being temporarily stored . You can questionnaire test before performed in this page . If you want to modify or change the questionnaire , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner .'); ?>
			</p>
		<?php endif; ?>
	</div>

<?php endif;