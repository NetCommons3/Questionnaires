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

<?php echo $this->element('Questionnaires.scripts'); ?>

<div id="nc-questionnaires-<?php echo (int)$frameId; ?>"
	 ng-controller="Questionnaires">

	<div class="pull-right">
		<?php echo $this->element('Questionnaires.Questionnaires/add_button'); ?>
	</div>

	<div class="pull-left">
		<?php echo $this->element('Questionnaires.Questionnaires/answer_status'); ?>
	</div>

	<div class="clearfix"></div>
	<article>
		<p>
			<?php echo __d('questionnaires', 'no questionnaire'); ?>
		</p>
		<?php if($this->viewVars['contentEditable'] == true): ?>
		<p>
			<?php echo __d('questionnaires', 'Please create new questionnaire by pressing the "+" button.'); ?>
		</p>
		<?php endif ?>
	</article>
</div>
<div class="text-center">
	<?php echo $this->BackToPage->backToPageButton(__d('questionnaires', 'Back to page'), 'menu-up'); ?>
</div>