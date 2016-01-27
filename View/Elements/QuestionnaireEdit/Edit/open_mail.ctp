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
<?php if (Current::read('Room.space_id') == Space::ROOM_SPACE_ID): ?>

	<label class="h3"><?php echo __d('questionnaires', 'Questionnaire open mail'); ?></label>
	<div class="form-group questionnaire-group">
		<?php
			echo $this->QuestionEdit->questionnaireAttributeCheckbox('is_open_mail_send',
				__d('questionnaires', 'Deliver e-mail when questionnaire has opened'));
		?>
		<div ng-show="questionnaires.questionnaire.isOpenMailSend == <?php echo QuestionnairesComponent::USES_USE; ?>">
			<?php
				echo $this->NetCommonsForm->input('open_mail_subject', array(
					'label' => __d('questionnaires', 'open mail subject'),
					'ng-model' => 'questionnaires.questionnaire.openMailSubject'
				));
				echo $this->NetCommonsForm->wysiwyg('open_mail_body', array(
				'label' => __d('questionnaires', 'open mail text'),
					'ng-model' => 'questionnaires.questionnaire.openMailBody'
				));
			?>
		</div>
	</div>
<?php else: ?>
	<?php echo $this->NetCommonsForm->hidden('is_open_mail_send', array(
		'value' => QuestionnairesComponent::USES_NOT_USE,
		'ng-model' => 'questionnaires.questionnaire.isOpenMailSend'
		));
		echo $this->NetCommonsForm->hidden('open_mail_subject', array(
			'value' => '',
			'ng-model' => 'questionnaires.questionnaire.openMailSubject'
		));
		echo $this->NetCommonsForm->hidden('open_mail_body', array(
			'value' => '',
			'ng-model' => 'questionnaires.questionnaire.openMailBody'
		));?>
<?php endif;
