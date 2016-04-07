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
		echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_no_member_allow',
			__d('questionnaires', 'accept the non-members answer'
		));
	?>
	<div class="questionnaire-sub-group" ng-show="questionnaires.questionnaire.isNoMemberAllow==<?php echo QuestionnairesComponent::USES_USE; ?>">
		<?php
			echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_key_pass_use',
				__d('questionnaires', 'use key phrase'), array(
				'ng-disabled' => 'questionnaires.questionnaire.isImageAuthentication == ' . QuestionnairesComponent::USES_USE . ' || questionnaires.questionnaire.isNoMemberAllow != ' . QuestionnairesComponent::USES_USE
			));
			echo $this->element('AuthorizationKeys.edit_form', [
				'options' => array(
					'div' => false,
					'ng-show' => 'questionnaires.questionnaire.isKeyPassUse != 0',
			)]);
			echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_image_authentication',
				__d('questionnaires', 'do image authentication'), array(
				'ng-disabled' => 'questionnaires.questionnaire.isKeyPassUse == ' . QuestionnairesComponent::USES_USE . ' || questionnaires.questionnaire.isNoMemberAllow != ' . QuestionnairesComponent::USES_USE
			));
		?>
	</div>
	<?php
		echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_repeat_allow',
			__d('questionnaires', 'forgive the repetition of the answer'), array(
			'ng-checked' => 'questionnaires.questionnaire.isNoMemberAllow == ' . QuestionnairesComponent::USES_USE,
			'ng-disabled' => 'questionnaires.questionnaire.isNoMemberAllow == ' . QuestionnairesComponent::USES_USE
		));
		echo $this->NetCommonsForm->hidden('is_repeat_allow', array(
			'ng-if' => 'questionnaires.questionnaire.isNoMemberAllow == ' . QuestionnairesComponent::USES_USE,
			'value' => QuestionnairesComponent::USES_USE
		));
		echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_anonymity',
			__d('questionnaires', 'anonymous answer'
		));
	?>
</div>
