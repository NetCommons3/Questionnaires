<div class="modal-body">
	<?php echo $this->element('NetCommons.setting_tabs', $settingTabs); ?>

	<div class="tab-content">
		<div class="text-right">
			<a class="btn btn-success" href="<?php echo $this->Html->url('/questionnaires/questionnaires/add/' . $frameId);?>">
				<span class="glyphicon glyphicon-plus"> </span>
			</a>
		</div>

		<div id="nc-questionnaire-setting-<?php echo $frameId; ?>">
			<?php echo $this->Form->create('', array(
			'url' => '/frames/frames/edit/' . $frameId
			)); ?>

			<?php echo $this->Form->hidden('Frame.id', array(
			'value' => $frameId,
			)); ?>

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
				</tr>
				</thead>
				<tbody>
					<?php foreach ($questionnaires as $questionnaire) : ?>
					<tr>
						<td>
							<?php echo $this->QuestionnaireStatusLabel->statusLabelManagementWidget($questionnaire);?>
						</td>
						<td>
							<a href="<?php echo $this->Html->url(
										'/questionnaires/questionnaire_questions/edit/' . $frameId . '/?questionnaire_id=' . $questionnaire['Questionnaire']['id']) ?>">
								<?php echo h($questionnaire['Questionnaire']['title']); ?>
							</a>
						</td>
						<td>
							<?php echo $this->Date->dateFormat($questionnaire['Questionnaire']['modified']); ?>
						</td>
						<td>
							<?php if ($questionnaire['Questionnaire']['all_answer_count'] > 0): ?>
							<a class="btn btn-warning"
							   href="<?php echo $this->Html->url(
										'download/' . $frameId . '/?questionnaire_id=' . $questionnaire['Questionnaire']['origin_id']) ?>">
								<span class="glyphicon glyphicon-download" ></span>
							</a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php echo $this->Form->end(); ?>

			<div class="text-center">
				<?php echo $this->element('NetCommons.paginator', array(
				'url' => Hash::merge(
				array('controller' => 'blocks', 'action' => 'index', $frameId),
				$this->Paginator->params['named']
				)
				)); ?>
			</div>
		</div>
	</div>
</div>