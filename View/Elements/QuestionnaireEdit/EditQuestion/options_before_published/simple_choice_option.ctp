<?php
/**
 * アンケート質問の種別によって異なる詳細設定のファイル
 * このファイルでは択一選択、複数選択、リスト選択タイプをフォローしている
 * 択一選択が設定か所はもっとも多い
 * 複数選択は択一選択の設定から「スキップロジック」を抜いたもの
 * リスト選択は択一選択から「その他」設定を抜いたものである
 * 2015.03.12現在では「テンプレートデータから読み込み」の部分が未対応です
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
		<button type="button" class="btn btn-default pull-right" ng-click="addChoice($event, pageIndex, qIndex, question.questionnaireChoice.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
			<span class="glyphicon glyphicon-plus"></span><?php echo __d('questionnaires', 'add choices'); ?>
		</button>
		<label class="checkbox-inline" ng-if="question.questionType != <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
			<?php echo $this->NetCommonsForm->checkbox('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_choice_random',
			array(
			'value' => QuestionnairesComponent::USES_USE,
			'ng-model' => 'question.isChoiceRandom',
			'ng-checked' => 'question.isChoiceRandom == ' . QuestionnairesComponent::USES_USE
			));
			?>
			<?php echo __d('questionnaires', 'randomaize choices'); ?>
		</label>
		<label class="checkbox-inline" ng-if="question.questionType != <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
			<?php echo $this->NetCommonsForm->checkbox('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_skip',
			array(
			'value' => QuestionnairesComponent::SKIP_FLAGS_SKIP,
			'ng-model' => 'question.isSkip',
			'ng-checked' => 'question.isSkip == ' . QuestionnairesComponent::SKIP_FLAGS_SKIP,
			'ng-disabled' => 'isDisabledSetSkip(page, question)'
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
				<div class="form-inline pull-right">
					<select name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{cIndex}}][skip_page_sequence]"
							class="form-control input-sm"
							ng-change="changeSkipPageChoice(choice.skipPageSequence)"
							ng-model="choice.skipPageSequence"
							ng-if="question.isSkip == <?php echo QuestionnairesComponent::SKIP_FLAGS_SKIP; ?>
							&& question.questionType != <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
						<option ng-repeat="skipPage in questionnaire.questionnairePage | filter: greaterThan('pageSequence', page)"
								value="{{skipPage.pageSequence}}"
								ng-selected="choice.skipPageSequence == skipPage.pageSequence || (choice.skipPageSequence == null && skipPage.pageSequence == pageIndex+1)">
							{{1 * skipPage.pageSequence + 1}}
						</option>
						<option value="<?php echo QuestionnairesComponent::SKIP_GO_TO_END ?>"
								ng-selected="choice.skipPageSequence == <?php echo QuestionnairesComponent::SKIP_GO_TO_END ?> || (choice.skipPageSequence == null && questionnaire.questionnairePage.length-1==pageIndex)">
							<?php echo __d('questionnaires', 'goto end'); ?>
						</option>
						<option
								value="{{questionnaire.questionnairePage.length}}"
								title="<?php echo __d('questionnaires', '(new page will be created)'); ?>">
							<?php echo __d('questionnaires', 'create new page for this skip'); ?>
						</option>
					</select>
					<button class="btn btn-default" type="button"
							ng-disabled="question.questionnaireChoice.length < 2"
							ng-click="deleteChoice($event, pageIndex, qIndex, choice.choiceSequence)"
							ng-if="choice.otherChoiceType == <?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
						<span class="glyphicon glyphicon-remove"> </span>
					</button>
				</div>
				<div class="form-inline">
					<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/options_before_published/choice'); ?>
				</div>
				<?php echo $this->element(
					'Questionnaires.QuestionnaireEdit/ng_errors', array(
					'errorArrayName' => 'choice.errorMessages.skipPageSequence',
				)); ?>
			</li>
		</ul>

	</div>
</div>
<div class="row" ng-if="question.questionType != <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
	<div class="col-sm-12">
		<button type="button" class="btn btn-default pull-right"
				ng-show="question.questionnaireChoice.length > 2"
				ng-click="addChoice($event, pageIndex, qIndex, question.questionnaireChoice.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
			<span class="glyphicon glyphicon-plus"></span><?php echo __d('questionnaires', 'add choices'); ?>
		</button>

		<label class="checkbox-inline">
			<input type="checkbox" ng-model="question.hasAnotherChoice" ng-change="changeAnotherChoice(pageIndex, qIndex, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>')">
			<?php echo __d('questionnaires', 'add another choice'); ?>
		</label>
	</div>
</div>
<?php
/* まだデータテンプレートからの読み込み方式が提唱されていないのでコメントアウトしておく FUJI: 2015.03.11
<div class="row text-center">
	<select class="form-control">
		<option>データテンプレートから選択肢を読み込む</option>
	</select>
</div>
*/