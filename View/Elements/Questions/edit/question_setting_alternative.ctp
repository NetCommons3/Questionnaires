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
		<button type="button" class="btn btn-default pull-right" ng-click="addChoice($event, <?php echo trim($pageIndex, '{}') ?>, <?php echo trim($qIndex, '{}') ?>, question.QuestionnaireChoice.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
			<span class="glyphicon glyphicon-plus"></span><?php echo __d('questionnaires', 'add choices'); ?>
		</button>
		<label class="checkbox-inline" ng-if="question.question_type != <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
			<?php echo $this->Form->checkbox('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.is_choice_random',
			array(
			'value' => QuestionnairesComponent::USES_USE,
			'ng-model' => 'question.is_choice_random',
			'ng-checked' => 'question.is_choice_random == ' . QuestionnairesComponent::USES_USE
			));
			?>
			<?php echo __d('questionnaires', 'randomaize choices'); ?>
		</label>
		<label class="checkbox-inline" ng-if="question.question_type != <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
			<?php echo $this->Form->checkbox('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.is_skip',
			array(
			'value' => QuestionnairesComponent::USES_USE,
			'ng-model' => 'question.is_skip',
			'ng-checked' => 'question.is_skip == ' . QuestionnairesComponent::USES_USE,
			));
			?>
			<?php echo __d('questionnaires', 'set page skip'); ?>
		</label>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">

		<ul class="list-group ">
			<li class="list-group-item" ng-repeat="(cIndex, choice) in question.QuestionnaireChoice" >
				<div class="form-inline pull-right">
					<select name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{cIndex}}][skip_page_sequence]"
							class="form-control input-sm"
							ng-change="changeSkipPageChoice(choice.skip_page_sequence)"
							ng-model="choice.skip_page_sequence"
							ng-if="question.is_skip == <?php echo QuestionnairesComponent::USES_USE; ?>
							&& question.question_type != <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
						<option
								ng-repeat="skip_page in questionnaire.QuestionnairePage"
								ng-value="{{skip_page.page_sequence}}"
								ng-selected="choice.skip_page_sequence == skip_page.page_sequence"
								ng-disabled="skip_page.page_sequence == pageIndex">
							{{1 * skip_page.page_sequence + 1}}
						</option>
						<option value="<?php echo QuestionnairesComponent::SKIP_GO_TO_END ?>"><?php echo __d('questionnaires', 'goto end'); ?></option>
						<option value="{{questionnaire.QuestionnairePage.length}}" title="<?php echo __d('questionnaires', '(new page will be created)'); ?>">
							<?php echo __d('questionnaires', 'create new page for this skip'); ?>
						</option>
					</select>
					<button class="btn btn-default" type="button"
							ng-disabled="question.QuestionnaireChoice.length < 2"
							ng-click="deleteChoice($event, pageIndex, qIndex, choice.choice_sequence)">
						<span class="glyphicon glyphicon-remove"> </span>
					</button>
				</div>
				<div class="form-inline">
					<?php echo $this->element('Questionnaires.Questions/edit/question_setting_choice_element', array('pageIndex' => $pageIndex, 'qIndex' => $qIndex)); ?>
				</div>
				<span class="clearfix"></span>
			</li>
		</ul>

	</div>
</div>
<div class="row" ng-if="question.question_type != <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
	<div class="col-sm-12">
		<button type="button" class="btn btn-default pull-right"
				ng-show="question.QuestionnaireChoice.length > 2"
				ng-click="addChoice($event, <?php echo trim($pageIndex, '{}') ?>, <?php echo trim($qIndex, '{}') ?>, question.QuestionnaireChoice.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
			<span class="glyphicon glyphicon-plus"></span><?php echo __d('questionnaires', 'add choices'); ?>
		</button>

		<label class="checkbox-inline">
			<input type="checkbox" ng-model="question.has_another_choice" ng-change="changeAnotherChoice(<?php echo trim($pageIndex, '{}') ?>, <?php echo trim($qIndex, '{}') ?>, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>')">
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