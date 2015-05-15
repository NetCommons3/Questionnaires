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
<?php if ($isDuringTest): ?>

	<?php echo $this->element('Questionnaires.comment_form'); ?>

	<div class="alert alert-info enp-testmode">
		<div class="pull-right">
			<?php echo $this->Html->link(
			__d('questionnaires', 'Edit this question'),
			array(
			'controller' => 'questionnaire_questions',
			'action' => 'setting_list',
			$frameId,
			'?' => array('questionnaire_id' => $questionnaire['Questionnaire']['id'])),
			array(
			'class' => 'btn btn-primary'
			)
			);
			?>
		</div>
		<h3><?php echo __d('questionnaires', 'Test Mode'); ?></h3>
		<div class="clearfix"></div>
		<p class="enp-tcome">
			<?php echo __d('questionnaires',
				'This questionnaire is being temporarily stored . You can questionnaire test before performed in this page . If you want to modify or change the questionnaire , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner . Also, when you click on the bottom<span class="btn btn-primary questionnaire-btn-comment"><span class="glyphicon glyphicon-comment"></span></span> right of the screen , you can see the comments .'); ?>
		</p>
	</div>
<?php endif;