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
<div class="form-group">
	<?php
		echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_no_member_allow',
			__d('questionnaires', 'accept the non-members answer')
		);
	?>
	<div class="row" ng-show="questionnaire.questionnaire.isNoMemberAllow==<?php echo QuestionnairesComponent::USES_USE; ?>">
		<div class="col-xs-11 col-xs-offset-1">
		<?php
			echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_key_pass_use',
				__d('questionnaires', 'use key phrase'), array(
				'ng-disabled' => 'questionnaire.questionnaire.isImageAuthentication == ' . QuestionnairesComponent::USES_USE . ' || questionnaire.questionnaire.isNoMemberAllow != ' . QuestionnairesComponent::USES_USE
			));
			echo $this->element('AuthorizationKeys.edit_form', [
				'options' => array(
					'div' => false,
					'ng-show' => 'questionnaire.questionnaire.isKeyPassUse != 0',
			)]);
			echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_image_authentication',
				__d('questionnaires', 'do image authentication'), array(
				'ng-disabled' => 'questionnaire.questionnaire.isKeyPassUse == ' . QuestionnairesComponent::USES_USE . ' || questionnaire.questionnaire.isNoMemberAllow != ' . QuestionnairesComponent::USES_USE
			));
		?>
		<span class="help-block">
			<?php echo __d('questionnaires', 'If you allowed to say also to non-members , the questionnaire will be possible to repeatedly answer.'); ?>
		</span>
		</div>
	</div>
	<?php
		echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_anonymity',
			__d('questionnaires', 'anonymous answer'
		));
	?>
</div>
