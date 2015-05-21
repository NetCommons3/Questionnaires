<?php
/**
 * questionnaire comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php
	echo $this->Form->create( 'Questionnaire', array(
		'id' => 'questionnare_answer_status_selector_' . $frameId,
		'name' => 'questionnaire_form_answer_status_filter',
		'type' => 'get',
		'novalidate' => true,
		'class' => 'form-inline',
		'inputDefaults' => array('label' => false, 'div' => false )
		)
	);
?>
<div class="form-group questionnaire-list-select">
<label><?php echo __d('questionnaires', 'Answer status'); ?></label>
<?php

	$list = array(
		QUESTIONNAIRE_ANSEWER_UNANSERERED => __d('questionnaires', 'Unanswered'),
		QUESTIONNAIRE_ANSEWER_ANSWERED => __d('questionnaires', 'Answered'),
	);
	if ($contentEditable) {
		$list[] = array(QUESTIONNAIRE_ANSEWER_TEST => __d('questionnaires', 'Test'));
	}

	echo $this->Form->input('Questionnaire.answer_status',
		array(
			'type' => 'select',
			'options' => $list,
			'selected' => $answerStatus,
			'class' => 'form-control',
			'onchange' => 'angular.element(this).scope().answerStatusChange(this)',
			'empty' => __d('questionnaires', 'View all')
		)
	);
?>
<?php
	echo $this->Form->end();
?>
</div>