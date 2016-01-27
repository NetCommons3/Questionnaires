<?php
/**
 * questionnaire setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="form-group questionnaire-group">
	<?php
		echo $this->NetCommonsForm->hidden('is_no_member_allow', array(
			'value' => QuestionnairesComponent::USES_NOT_USE,
			'ng-model' => 'questionnaires.questionnaire.isNoMemberAllow'
		));
		echo $this->NetCommonsForm->hidden('is_key_pass_use', array(
			'value' => QuestionnairesComponent::USES_NOT_USE,
			'ng-model' => 'questionnaires.questionnaire.isKeyPassUse'
		));
		echo $this->NetCommonsForm->hidden('is_image_authentication', array(
			'value' => QuestionnairesComponent::USES_NOT_USE,
			'ng-model' => 'questionnaires.questionnaire.isImageAuthentication'
		));
		echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_repeat_allow',
			__d('questionnaires', 'forgive the repetition of the answer'), array(
			'hiddenField' => false,
		));
		echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_anonymity',
			__d('questionnaires', 'anonymous answer'
		));
	?>
</div>
