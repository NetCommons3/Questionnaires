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
echo $this->NetCommonsHtml->script(array(
	'/authorization_keys/js/authorization_keys.js',
));
?>
<article class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>
	<div class="tab-content">
		<?php echo $this->BlockIndex->addLink('',
		array(
			'controller' => 'questionnaire_add',
			'action' => 'add',
			'frame_id' => Current::read('Frame.id'),
			'block_id' => Current::read('Block.id'),
		)); ?>

		<div id="nc-questionnaire-setting-<?php echo Current::read('Frame.id'); ?>">
			<?php echo $this->BlockIndex->startTable(); ?>
				<thead>
				<tr>
					<?php echo $this->BlockIndex->tableHeader(
						'Questionnaire.status', __d('questionnaires', 'Status'),
						array('sort' => true, 'type' => false)
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'Questionnaire.title', __d('questionnaires', 'Title'),
						array('sort' => true)
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'Questionnaire.modified', __d('net_commons', 'Updated date'),
						array('sort' => true, 'type' => 'datetime')
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'', __d('questionnaires', 'Answer CSV'),
						array('type' => 'center')
					); ?>
					<?php echo $this->BlockIndex->tableHeader(
						'', __d('questionnaires', 'Templates'),
						array('type' => 'center')
					); ?>
				</tr>
				</thead>
				<tbody>
					<?php foreach ((array)$questionnaires as $questionnaire) : ?>
					<?php echo $this->BlockIndex->startTableRow($questionnaire['Questionnaire']['key']); ?>
						<?php echo $this->BlockIndex->tableData(
						'',
						$this->QuestionnaireStatusLabel->statusLabelManagementWidget($questionnaire),
						array('escape' => false)
						); ?>
						<?php echo $this->BlockIndex->tableData(
						'',
						$this->TitleIcon->titleIcon($questionnaire['Questionnaire']['title_icon']) . $questionnaire['Questionnaire']['title'],
						array(
							'escape' => false,
							'editUrl' => array(
								'plugin' => 'questionnaires',
								'controller' => 'questionnaire_edit',
								'action' => 'edit_question',
								Current::read('Block.id'),
								$questionnaire['Questionnaire']['key'],
								'frame_id' => Current::read('Frame.id')
							)
						)); ?>
						<?php echo $this->BlockIndex->tableData(
						'',
						$questionnaire['Questionnaire']['modified'],
						array('type' => 'datetime')
						); ?>
						<?php if ($questionnaire['Questionnaire']['all_answer_count'] > 0): ?>
							<?php echo $this->BlockIndex->tableData(
							'',
							$this->AuthKeyPopupButton->popupButton(
								array(
									'url' => NetCommonsUrl::actionUrl(array(
									'plugin' => 'questionnaires',
									'controller' => 'questionnaire_blocks',
									'action' => 'download',
									Current::read('Block.id'),
									$questionnaire['Questionnaire']['key'],
									'frame_id' => Current::read('Frame.id'))),
									'popup-title' => __d('authorization_keys', 'Compression password'),
									'popup-label' => __d('authorization_keys', 'Compression password'),
									'popup-placeholder' => __d('authorization_keys', 'please input compression password'),
								)
							),
							array('escape' => false, 'type' => 'center')
							); ?>
						<?php else: ?>
							<td></td>
						<?php endif; ?>
						<td>
							<?php if ($questionnaire['Questionnaire']['status'] == WorkflowComponent::STATUS_PUBLISHED): ?>
							<a class="btn btn-warning"
							   href="<?php echo NetCommonsUrl::actionUrl(array(
										'plugin' => 'questionnaires',
										'controller' => 'questionnaire_blocks',
										'action' => 'export',
										Current::read('Block.id'),
										$questionnaire['Questionnaire']['key'],
										'frame_id' => Current::read('Frame.id'))); ?>">
								<span class="glyphicon glyphicon-export" ></span>
							</a>
							<?php endif; ?>
						</td>
					<?php echo $this->BlockIndex->endTableRow(); ?>
					<?php endforeach; ?>
				</tbody>
			<?php echo $this->BlockIndex->endTable(); ?>
			<?php echo $this->element('NetCommons.paginator'); ?>
		</div>
	</div>
</article>