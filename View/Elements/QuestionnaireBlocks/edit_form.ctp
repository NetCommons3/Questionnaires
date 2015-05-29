<?php
/**
 * Blocks edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->element('Blocks.form_hidden'); ?>

<?php echo $this->Form->hidden('Questionnaire.id', array(
		'value' => isset($questionnaire['id']) ? (int)$questionnaire['id'] : null,
	)); ?>

<?php echo $this->Form->hidden('Questionnaire.key', array(
		'value' => isset($questionnaire['key']) ? $questionnaire['key'] : null,
	)); ?>

<?php echo $this->Form->hidden('QuestionnaireFrameSetting.id', array(
		'value' => isset($questionnaireFrameSetting['id']) ? (int)$questionnaireFrameSetting['id'] : null,
	)); ?>

<?php echo $this->element('Blocks.public_type');