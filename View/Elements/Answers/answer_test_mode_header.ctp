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
<?php if ($contentEditable): ?>

	<?php
		$answerHeaderClass = '';
		if ($isDuringTest) {
			$answerHeaderClass = 'alert alert-info';
		}
	?>

	<div class="<?php echo $answerHeaderClass; ?>">
		<div class="pull-right">
			<?php echo $this->Html->link(
			'<span class="glyphicon glyphicon-edit" ></span>',
			array(
				'controller' => 'questionnaire_questions',
				'action' => 'edit',
				$frameId,
				'?' => array('questionnaire_id' => $questionnaire['Questionnaire']['id'])),
			array(
				'class' => 'btn btn-primary',
				'escape' => false,
			)
			);
			?>
		</div>
		<?php if ($isDuringTest): ?>
		<h3><?php echo __d('questionnaires', 'Test Mode'); ?></h3>
		<div class="clearfix"></div>
		<p class="enp-tcome">
			<?php echo __d('questionnaires',
				'This questionnaire is being temporarily stored . You can questionnaire test before performed in this page . If you want to modify or change the questionnaire , you will be able to edit by pressing the [ Edit question ] button in the upper-right corner . '); ?>
		</p>
		<?php endif; ?>
	</div>

<?php endif;