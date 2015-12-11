<?php
/**
 * 実施後のアンケート
 * アンケート質問の種別によって異なる詳細設定のファイル
 * このファイルでは日付け・時間入力タイプをフォローしている
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<?php echo $this->NetCommonsForm->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_type_option',
		array(
			'type' => 'radio',
			'before' => '<div class="col-sm-3"><div class="radio"><label>',
			'separator' => '</label></div></div><div class="col-sm-3"><div class="radio"><label>',
			'after' => '</label></div></div>',
			'options' => array(QuestionnairesComponent::TYPE_OPTION_DATE => __d('questionnaires', 'Date'),
			QuestionnairesComponent::TYPE_OPTION_TIME => __d('questionnaires', 'Time'),
			QuestionnairesComponent::TYPE_OPTION_DATE_TIME => __d('questionnaires', 'Date and Time')),
			'legend' => false,
			'div' => false,
			'label' => false,
			'class' => '',
			'ng-model' => 'question.questionTypeOption',
			'disabled' => 'disabled',
		));
	?>
	<div class="col-sm-3">
	</div>
</div>


<div class="row">
	<div class="col-sm-12">
		<div class="checkbox disabled">
			<label>
				<?php echo $this->NetCommonsForm->checkbox('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_range',
					array(
						'value' => QuestionnairesComponent::USES_USE,
						'ng-model' => 'question.isRange',
						'ng-checked' => 'question.isRange == ' . QuestionnairesComponent::USES_USE,
						'disabled' => 'disabled',
					));
				?>
				<?php echo __d('questionnaires', 'set range to answer date and time'); ?>
			</label>
		</div>
	</div>
</div>

<div class="row">
	<div ng-show="question.isRange == <?php echo QuestionnairesComponent::USES_USE; ?>">
		<div class="col-sm-12">

			<span ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE; ?>">
				{{question.min | date : 'yyyy-MM-dd'}}
			</span>
			<span ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_TIME; ?>">
				{{question.min | date : 'HH:mm'}}
			</span>
			<span ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE_TIME; ?>">
				{{question.min | date : 'yyyy-MM-dd HH:mm'}}
			</span>

			<span class="form-control-static text-center"><?php echo __d('questionnaires', ' - '); ?></span>

			<span ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE; ?>">
				{{question.max | date : 'yyyy-MM-dd'}}
			</span>
			<span ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_TIME; ?>">
				{{question.max | date : 'HH:mm'}}
			</span>
			<span ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE_TIME; ?>">
				{{question.max | date : 'yyyy-MM-dd HH:mm'}}
			</span>
		</div>

	</div>
</div>

