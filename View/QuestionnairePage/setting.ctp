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

<?php $formName = 'QuestionnaireForm' . (int)$frameId; ?>

<?php $this->start('titleForModal'); ?>
<?php echo __d('questionnaires', 'plugin_name'); ?>
<?php $this->end(); ?>


<div class="panel panel-default" ng-init="initialize(<?php echo $formName; ?>)">

	<?php
	echo $this->Html->image('Questionnaires.Questionnaire_setting_page_1.png',
		array('alt' => 'list',
			'ng-click' => 'showQuestionEdit()'));
	?>

	<div class="panel-footer text-center">
	<button type="button" class="btn btn-default"
			ng-click='cancel()'>
		<?php echo __d('net_commons', 'Cancel'); ?>
	</button>

	<button type="button" class="btn btn-primary"
			ng-click='showSummaryEdit()'>
		保存して次へ
	</button>

	</div>
</div>

