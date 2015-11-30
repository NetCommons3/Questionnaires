<?php
/**
 * questionnaire content list view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->element('Questionnaires.scripts'); ?>

<article class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsComponent::MAIN_TAB_BLOCK_INDEX); ?>

	<div class="tab-content">
		<div class="pull-right">
			<?php echo $this->element('Questionnaires.Questionnaires/add_button'); ?>
		</div>

		<div id="nc-questionnaire-setting-<?php echo Current::read('Frame.id'); ?>">
		<?php echo $this->NetCommonsForm->create('', array(
				'url' => NetCommonsUrl::actionUrl(array('plugin' => 'frames', 'controller' => 'frames', 'action' => 'edit'))
			)); ?>

			<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>

			<table class="table table-hover">
				<thead>
				<tr>
					<th>
						<?php echo $this->Paginator->sort('Questionnaire.status', __d('questionnaires', 'Status')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('Questionnaire.title', __d('questionnaires', 'Title')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('Questionnaire.modified', __d('net_commons', 'Updated date')); ?>
					</th>
					<th>

					</th>
					<th>

					</th>
				</tr>
				</thead>
				<tbody>
					<?php foreach ((array)$questionnaires as $questionnaire) : ?>
					<tr>
						<td>
							<?php echo $this->QuestionnaireStatusLabel->statusLabelManagementWidget($questionnaire);?>
						</td>
						<td>
							<a href="<?php echo $this->Html->url(
										'/questionnaires/questionnaire_questions/edit/' . Current::read('Frame.id') . '/?questionnaire_id=' . $questionnaire['Questionnaire']['id']) ?>">
								<?php echo h($questionnaire['Questionnaire']['title']); ?>
							</a>
						</td>
						<td>
							<?php echo $this->Date->dateFormat($questionnaire['Questionnaire']['modified']); ?>
						</td>
						<td>
							<?php if ($questionnaire['Questionnaire']['all_answer_count'] > 0): ?>
							<a href="<?php echo NetCommonsUrl::actionUrl(array(
									'plugin' => 'questionnaires',
									'controller' => 'questionnaire_blocks',
									'action' => 'download',
									$questionnaire['Questionnaire']['key'],
									'frame_id' => Current::read('Frame.id')
									)) ?>"
								class="btn btn-success">
								<span class="glyphicon glyphicon-download" ></span>
							</a>

							<?php endif; ?>
						</td>
						<td>
							<?php if ($questionnaire['Questionnaire']['status'] == WorkflowComponent::STATUS_PUBLISHED): ?>
							<a class="btn btn-warning"
							   href="<?php echo $this->Html->url(
										'export/' . Current::read('Frame.id') . '/' . $questionnaire['Questionnaire']['key']) ?>">
								<span class="glyphicon glyphicon-export" ></span>
							</a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php echo $this->NetCommonsForm->end(); ?>

			<div class="text-center">
				<?php echo $this->element('NetCommons.paginator', array(
				'url' => Hash::merge(
				array('controller' => 'questionnaire_blocks', 'action' => 'index', Current::read('Frame.id')),
				$this->Paginator->params['named']
				)
				)); ?>
			</div>
		</div>
	</div>
</article>
