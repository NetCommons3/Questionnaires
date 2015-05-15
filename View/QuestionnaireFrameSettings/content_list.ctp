<?php
/**
 * questionnaire setting list view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->element('Questionnaires.scripts'); ?>
<?php echo $this->Html->script('Questionnaires.questionnaires_edit.js');?>

<div id="nc-questionnaire-frame-settings-content-list-<?php echo (int)$frameId; ?>"
	 >

	<?php $this->start('title'); ?>
	<?php echo __d('questionnaires', 'plugin_name'); ?>
	<?php $this->end(); ?>

	<div class="modal-header">
		<?php $title = $this->fetch('title'); ?>
		<?php if ($title) : ?>
		<?php echo $title; ?>
		<?php else : ?>
		<br />
		<?php endif; ?>
	</div>

	<div class="modal-body">

		<ul class="nav nav-tabs">
			<?php foreach ($tabLists as $tab): ?>
			<li role="presentation" class="<?php echo $tab['class'] ?>">
				<a href="<?php echo $tab['href']; ?>">
					<?php echo $tab['tabTitle']; ?>
				</a>
			</li>
			<?php endforeach ?>
		</ul>
		<div class="tab-body has-feedback" >

			<div class="pagination-label">
				<?php echo ($page['currentPageNumber'] - 1) * $page['displayNumPerPage'] + 1; ?>
				<?php echo __d('questionnaires', ' - '); ?>
				<?php echo $questionnaires['itemCount']; ?>
				<?php echo __d('questionnaires', '(All'); ?>
				<?php echo $page['totalCount']; ?>
				<?php echo __d('questionnaires', ' items)'); ?>
			</div>

			<table class="table table-bordered">
				<tr>
					<th><?php echo __d('questionnaires', 'Status'); ?></th>
					<th><?php echo __d('questionnaires', 'Title'); ?></th>
					<th><?php echo __d('questionnaires', 'Created'); ?></th>
					<th><?php echo __d('questionnaires', 'Modified'); ?></th>
					<th><?php echo __d('questionnaires', 'Answer download'); ?></th>
				</tr>
				<?php foreach ($questionnaires['items'] as $qu): ?>
					<tr>
						<td>
							<?php echo $this->element('Questionnaires.status_label',
							array('status' => $qu['Entity']['status'])); ?>
						</td>
						<td><?php echo ($qu['Entity']['title']); ?></td>
						<td><?php echo ($qu['Questionnaire']['created']); ?></td>
						<td><?php echo ($qu['Entity']['modified']); ?></td>
						<td><button class="btn btn-warning">CSV</button></td>
					</tr>
				<?php endforeach?>
			</table>





			<?php echo $this->Form->create('QuestionnaireFrameSettings', array(
			'id' => 'questionnaire_content_list_pagenation_' . $frameId,
			'name' => 'questionnaire_content_list_pagenation',
			'type' => 'get',
			'novalidate' => true,
			'class' => 'form-inline',
			)); ?>
			<div class="text-center">
				<pagination
						on-select-page="pageChanged(page)"
						direction-links="false"
						boundary-links="true"
						total-items="totalCount"
						max-size="<?php echo QUESTIONNAIRE_DEFAULT_DISPLAY_PAGE_SIZE; ?>"
						page="currentPageNumber"
						ng-model="currentPageNumber"
						class="pagination-sm"
						previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;">
				</pagination>
			</div>

			<?php echo $this->Form->input('page', array(
			'type' => 'hidden',
			'ng-value' => 'currentPageNumber'
			)); ?>
			<?php echo $this->Form->end(); ?>

		</div>
	</div>
</div>