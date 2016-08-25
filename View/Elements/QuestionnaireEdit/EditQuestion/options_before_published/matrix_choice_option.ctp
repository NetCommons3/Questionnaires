<?php
/**
 * questionnaire page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="row">
	<?php
		echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_choice_random',
			array('value' => QuestionnairesComponent::USES_NOT_USE,
		));
		echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_skip',
			array('value' => QuestionnairesComponent::SKIP_FLAGS_NO_SKIP,
		));
	?>

    <div class="col-xs-6">
        <button type="button" class="btn btn-default pull-right"
                ng-click="addChoice($event, pageIndex, qIndex, matrixRows.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
            <span class="glyphicon glyphicon-plus"></span>
            <?php echo __d('questionnaires', 'Add line choices'); ?>
        </button>
        <label><?php echo __d('questionnaires', 'Line choices'); ?></label>
        <div class="clearfix"></div>
        <ul class="list-group questionnaire-edit-choice-list-group">
            <li class="list-group-item" ng-repeat="(cIndex, choice) in matrixRows =
            (question.questionnaireChoice |
            filter: {matrixType:<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>})" >
                <button class="btn btn-default pull-right" type="button"
                        ng-disabled="matrixRows.length < 2"
                        ng-click="deleteChoice($event, pageIndex, qIndex, choice.choiceSequence)"
                        ng-if="choice.otherChoiceType == <?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
                    <span class="glyphicon glyphicon-remove"> </span>
                </button>
                <div class="form-inline">
                    <?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/options_before_published/choice'); ?>
                </div>
            </li>
        </ul>
        <button type="button" class="btn btn-default pull-right"
                ng-show="matrixRows.length > 2"
                ng-click="addChoice($event, pageIndex, qIndex, matrixRows.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
            <span class="glyphicon glyphicon-plus"></span>
            <?php echo __d('questionnaires', 'Add line choices'); ?>
        </button>
        <div class="clearfix"></div>
        <div class="checkbox">
            <label>
                <input type="checkbox"
                       ng-model="question.hasAnotherChoice"
                       ng-change="changeAnotherChoice(pageIndex, qIndex, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>')">
                <?php echo __d('questionnaires', 'add another choice'); ?>
            </label>
        </div>
    </div>

    <div class="col-xs-6">
        <button type="button" class="btn btn-default pull-right" ng-click="addChoice($event, pageIndex, qIndex, matrixColumns.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>');">
            <span class="glyphicon glyphicon-plus"></span>
            <?php echo __d('questionnaires', 'Add column choices'); ?>
        </button>
        <label><?php echo __d('questionnaires', 'Column choices'); ?></label>
        <div class="clearfix"></div>

        <ul class="list-group questionnaire-edit-choice-list-group">
            <li class="list-group-item" ng-repeat="(cIndex, choice) in matrixColumns = (question.questionnaireChoice | filter: {matrixType:<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>})" >
                <button class="btn btn-default pull-right" type="button"
                        ng-disabled="matrixColumns.length < 2"
                        ng-click="deleteChoice($event, pageIndex, qIndex, choice.choiceSequence, '<?php echo __d('questionnaires', 'Do you want to delete this choice ?'); ?>')">
                    <span class="glyphicon glyphicon-remove"> </span>
                </button>
                <div class="form-inline">
                    <?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/options_before_published/choice'); ?>
                </div>
            </li>
        </ul>
        <button type="button" class="btn btn-default pull-right"
                ng-show="matrixColumns.length > 2"
                ng-click="addChoice($event, pageIndex, qIndex, matrixColumns.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>');">
            <span class="glyphicon glyphicon-plus"></span>
            <?php echo __d('questionnaires', 'Add column choices'); ?>
        </button>

    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <p class="help-block small pull-right">
            <?php echo __d('questionnaires', 'You can not use the character of |, : for choice text '); ?>
        </p>
    </div>

</div>

