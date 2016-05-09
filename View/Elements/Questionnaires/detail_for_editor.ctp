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
<div class="row">
	<div class="col-md-12 col-xs-12">
		<div class=" well well-sm">
			<div class="pull-right">
				<?php echo $this->Button->editLink('', array(
				'plugin' => 'questionnaires',
				'controller' => 'questionnaire_edit',
				'action' => 'edit_question',
				'key' => $questionnaire['Questionnaire']['key'])); ?>
			</div>
			<small>
				<dl class="questionnaire-editor-dl">
					<dt><?php echo __d('questionnaires', 'Author'); ?></dt>
					<dd><?php echo $questionnaire['TrackableCreator']['handlename']; ?></dd>
					<dt><?php echo __d('questionnaires', 'Modified by'); ?></dt>
					<dd><?php echo $questionnaire['TrackableUpdater']['handlename']; ?>
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
