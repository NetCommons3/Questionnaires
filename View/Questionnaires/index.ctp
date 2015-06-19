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

	<table class="table nc-content-list">
		<?php foreach($questionnaires as $questionnaire): ?>
			<tr>
				<td>
					<?php if ($questionnaire['questionnaire']['isPeriod'] == QuestionnairesComponent::USES_USE): ?>
						<div class="row">
							<div class="col-md-12 col-xs-12">
								<?php echo $this->Date->dateFormat($questionnaire['questionnaire']['startPeriod']); ?>
								<?php echo __d('questionnaires', ' - '); ?>
								<?php echo $this->Date->dateFormat($questionnaire['questionnaire']['endPeriod']); ?>
							</div>
						</div>
					<?php endif ?>

					<article class="row">

						<div class="col-md-8 col-xs-12">
							<?php echo $this->QuestionnaireStatusLabel->statusLabel($questionnaire);?>
							<h2>
								<?php echo h($questionnaire['questionnaire']['title']); ?>
								<br>
								<small><?php echo h($questionnaire['questionnaire']['subTitle']); ?></small>
							</h2>
						</div>
						<div class="col-md-4 col-xs-12" >
							<div class="pull-right h3">
								<?php echo
						$this->QuestionnaireUtil->getAnswerButtons($frameId, $questionnaire);
								?>
								<?php echo
						$this->QuestionnaireUtil->getAggregateButtons($frameId, $questionnaire);
								?>
								<div class="clearfix"></div>
							</div>
						</div>
					</article>

					<?php if($this->viewVars['contentEditable'] == true): ?>
					<div class="row">
						<div class="col-md-12 col-xs-12">
							<div class="well well-sm">
								<div class="pull-right">
									<a class="btn btn-primary"
									   href="<?php echo $this->Html->url(
										'questionnaire_questions/edit/' . $frameId . '/?questionnaire_id=' . $questionnaire['questionnaire']['id']) ?>">
										<span class="glyphicon glyphicon-edit" ></span>
									</a>
								</div>
								<small>
									<dl class="questionnaire-editor-dl">
										<dt><?php echo __d('questionnaires', 'Author'); ?></dt>
										<dd><?php echo $questionnaire['createdUser']['value']; ?></dd>
										<dt><?php echo __d('questionnaires', 'Modified by'); ?></dt>
										<dd><?php echo $questionnaire['modifiedUser']['value']; ?>
											(<?php echo $this->Date->dateFormat($questionnaire['questionnaire']['modified']); ?>)
										</dd>
									</dl>
									<dl class="questionnaire-editor-dl">
										<dt><?php echo __d('questionnaires', 'Pages'); ?></dt>
										<dd><?php echo $questionnaire['questionnaire']['pageCount']; ?></dd>
										<dt><?php echo __d('questionnaires', 'Questions'); ?></dt>
										<dd><?php echo $questionnaire['questionnaire']['questionCount']; ?></dd>
										<dt><?php echo __d('questionnaires', 'Answers' ); ?></dt>
										<dd><?php echo $questionnaire['questionnaire']['allAnswerCount']; ?></dd>
									</dl>
									<div class="clearfix"></div>
								</small>
							</div>
						</div>
					</div>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<div class="text-center">
		<nav>
			<ul class="pagination">
				<?php echo $this->element('NetCommons.paginator', array(
				'url' => Hash::merge(
				array('controller' => 'questionnaires', 'action' => 'index', $frameId),
				$this->Paginator->params['named']
				)
				)); ?>
			</ul>
		</nav>
	</div>

</div>
<div class="text-center">
	<?php echo $this->BackToPage->backToPageButton(__d('questionnaires', 'Back to page'), 'menu-up'); ?>
</div>
