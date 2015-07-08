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
					<?php if ($questionnaire['Questionnaire']['is_period'] == QuestionnairesComponent::USES_USE): ?>
						<div class="row">
							<div class="col-md-12 col-xs-12">
								<?php echo $this->Date->dateFormat($questionnaire['Questionnaire']['start_period']); ?>
								<?php echo __d('questionnaires', ' - '); ?>
								<?php echo $this->Date->dateFormat($questionnaire['Questionnaire']['end_period']); ?>
							</div>
						</div>
					<?php endif ?>

					<article class="row">

						<div class="col-md-8 col-xs-12">
							<?php echo $this->QuestionnaireStatusLabel->statusLabel($questionnaire);?>
							<h2>
								<?php echo h($questionnaire['Questionnaire']['title']); ?>
								<br>
								<small><?php echo h($questionnaire['Questionnaire']['sub_title']); ?></small>
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
										'questionnaire_questions/edit/' . $frameId . '/?questionnaire_id=' . $questionnaire['Questionnaire']['id']) ?>">
										<span class="glyphicon glyphicon-edit" ></span>
									</a>
								</div>
								<small>
									<dl class="questionnaire-editor-dl">
										<dt><?php echo __d('questionnaires', 'Author'); ?></dt>
										<dd><?php echo $questionnaire['TrackableCreator']['username']; ?></dd>
										<dt><?php echo __d('questionnaires', 'Modified by'); ?></dt>
										<dd><?php echo $questionnaire['TrackableUpdater']['username']; ?>
											(<?php echo $this->Date->dateFormat($questionnaire['Questionnaire']['modified']); ?>)
										</dd>
									</dl>
									<dl class="questionnaire-editor-dl">
										<dt><?php echo __d('questionnaires', 'Pages'); ?></dt>
										<dd><?php echo $questionnaire['Questionnaire']['page_count']; ?></dd>
										<dt><?php echo __d('questionnaires', 'Questions'); ?></dt>
										<dd><?php echo $questionnaire['Questionnaire']['question_count']; ?></dd>
										<dt><?php echo __d('questionnaires', 'Answers' ); ?></dt>
										<dd><?php echo $questionnaire['Questionnaire']['all_answer_count']; ?></dd>
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
