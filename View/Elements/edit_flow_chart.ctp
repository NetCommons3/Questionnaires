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
<?php
	$steps = array(
	1 => __d('questionnaires', 'Set questions'),
	2 => __d('questionnaires', 'Set result display'),
	3 => __d('questionnaires', 'Set questionnaire'));
	$stepCount = count($steps);
	$stepWidth = 'style="width: ' . 100 / $stepCount . '%;"';
	$check = $steps;
	?>

<div class="progress questionnaire-steps">
	<?php foreach ($steps as $index => $step): ?>
		<?php if ($index == $current): ?>
			<div class="progress-bar progress-bar questionnaire-step-item" <?php echo $stepWidth; ?> >
				<span class="questionnaire-step-item-title">
					<span class="btn-primary"><span class="badge"><?php echo $index; ?></span></span>
		<?php else : ?>
			<div class="questionnaire-step-item" <?php echo $stepWidth; ?>>
				<span class="questionnaire-step-item-title">
					<span class="badge"><?php echo $index; ?></span>
		<?php endif; ?>
					<?php echo $step; ?>
				</span>
					<?php if (next($check)) :?>
						<span class="glyphicon glyphicon-chevron-right"></span>
					<?php endif; ?>
			</div>
	<?php endforeach; ?>
</div>
