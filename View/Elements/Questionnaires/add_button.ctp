<?php
/**
 * questionnaire page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php if($this->viewVars['contentEditable'] == true): ?>
		<span class="nc-tooltip" tooltip="<?php echo __d('net_commons', 'Add'); ?>">
			<a href="<?php echo $this->Html->url('/questionnaires/questionnaire_add/add/' . $frameId) ?>" class="btn btn-success">
				<span class="glyphicon glyphicon-plus"> </span>
			</a>
		</span>
<?php endif;