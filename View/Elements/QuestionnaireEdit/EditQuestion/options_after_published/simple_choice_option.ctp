<?php
/**
 * 実施後のアンケート
 * 質問の種別によって異なる詳細設定のファイル
 * このファイルでは択一選択、複数選択、リスト選択タイプをフォローしている
 * 設定内容を見せるだけで実質編集は何もできない
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<div class="col-sm-12">
		<label class="checkbox-inline" ng-show="question.questionType != <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
			<?php echo $this->NetCommonsForm->checkbox('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_choice_random',
			array(
			'value' => QuestionnairesComponent::USES_USE,
			'ng-model' => 'question.isChoiceRandom',
			'ng-checked' => 'question.isChoiceRandom == ' . QuestionnairesComponent::USES_USE,
			'disabled' => 'disabled',
			));
			?>
			<?php echo __d('questionnaires', 'randomaize choices'); ?>
		</label>
		<label class="checkbox-inline" ng-show="question.questionType != <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
			<?php echo $this->NetCommonsForm->checkbox('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_skip',
			array(
			'value' => QuestionnairesComponent::SKIP_FLAGS_SKIP,
			'ng-model' => 'question.isSkip',
			'ng-checked' => 'question.isSkip == ' . QuestionnairesComponent::SKIP_FLAGS_SKIP,
			'disabled' => 'disabled',
			));
			?>
			<?php echo __d('questionnaires', 'set page skip'); ?>
		</label>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<ul class="list-group ">
			<li class="list-group-item" ng-repeat="(cIndex, choice) in question.questionnaireChoice" >
				<div class="form-inline">
					<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/options_after_published/choice'); ?>
				</div>
			</li>
		</ul>
	</div>
</div>