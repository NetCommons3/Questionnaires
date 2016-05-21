<?php
/**
 * questionnaire accordion heading template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php if (! $isPublished): ?>
	<div class="pull-right">
		<div class="btn-group" uib-dropdown dropdown-append-to-body>
			<button type="button" class="btn btn-default" uib-dropdown-toggle ng-click="deter($event)">
				<?php echo __d('questionnaires', 'copy to another page'); ?>
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" uib-dropdown-menu role="menu">
				<li role="presentation" class="dropdown-header"><?php echo __d('questionnaires', 'destination page number'); ?></li>
				<li ng-repeat="(copyPageIndex, copyPage) in questionnaire.questionnairePage">
					<a href="#" ng-click="copyQuestionToAnotherPage($event, pageIndex, qIndex, copyPage.pageSequence)">{{1 * copyPage.pageSequence + 1}}</a>
				</li>
			</ul>
		</div>
		<button class="btn btn-danger " type="button"
				ng-disabled="page.questionnaireQuestion.length < 2"
				ng-click="deleteQuestion($event, pageIndex, qIndex, '<?php echo __d('questionnaires', 'Do you want to delete this question ?'); ?>')">
			<span class="glyphicon glyphicon-remove"> </span>
		</button>
	</div>

	<button
			class="btn btn-default pull-left"
			type="button"
			ng-disabled="$first"
			ng-click="moveQuestion($event, pageIndex, qIndex, qIndex-1)">
		<span class="glyphicon glyphicon-arrow-up"></span>
	</button>

	<button
			class="btn btn-default pull-left"
			type="button"
			ng-disabled="$last"
			ng-click="moveQuestion($event, pageIndex, qIndex, qIndex+1)">
		<span class="glyphicon glyphicon-arrow-down"></span>
	</button>
<?php endif; ?>

<span class="questionnaire-accordion-header-title">
	<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="question.hasError"></span>

	{{question.questionValue}}

	<strong ng-if="question.isRequire != 0" class="text-danger h4">
		<?php echo __d('net_commons', '*'); ?>
	</strong>
	<span ng-if="question.isSkip != 0" class="badge">
		<?php echo __d('questionnaires', 'Skip'); ?>
	</span>
</span>

<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="question.hasError"></span>
