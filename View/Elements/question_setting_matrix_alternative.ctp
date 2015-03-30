<div class="row">

<div class="col-sm-6">
    <h5><?php echo __d('questionnaires', 'Line choices'); ?></h5>
    <button type="button" class="btn btn-default" ng-click="addChoice($event, pageIndex, qIndex, '<?php echo __d('questionnaires', 'new line choice'); ?>', '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
        <span class="glyphicon glyphicon-plus"></span>
        <?php echo __d('questionnaires', 'add line choices'); ?>
    </button>
    <ul class="list-group ">
        <li class="list-group-item" ng-repeat="(cIndex, choice) in question.QuestionnaireChoice | filter: {matrix_type:<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>}" >
            <button class="close pull-right" type="button" ng-click="deleteChoice($event, pageIndex, qIndex, choice.choice_sequence, '<?php echo __d('questionnaires', 'Do you want to delete this choice ?'); ?>')">
                <span class="glyphicon glyphicon-remove"> </span>
            </button>
            <?php echo $this->element('Questionnaires.question_setting_choice_element', array('pageIndex'=>$pageIndex, 'qIndex'=>$qIndex)); ?>
            <span class="clearfix"></span>
        </li>
    </ul>
</div>

<div class="col-sm-6">
    <h5><?php echo __d('questionnaires', 'Column choices'); ?></h5>
    <button type="button" class="btn btn-default" ng-click="addChoice($event, pageIndex, qIndex, '<?php echo __d('questionnaires', 'new column choice'); ?>', '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>');">
        <span class="glyphicon glyphicon-plus"></span>
        <?php echo __d('questionnaires', 'add column choices'); ?>
    </button>

    <ul class="list-group">
        <li class="list-group-item" ng-repeat="(cIndex, choice) in question.QuestionnaireChoice | filter: {matrix_type:<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>}" >
            <button class="close pull-right" type="button" ng-click="deleteChoice($event, pageIndex, qIndex, choice.choice_sequence, '<?php echo __d('questionnaires', 'Do you want to delete this choice ?'); ?>')">
                <span class="glyphicon glyphicon-remove"> </span>
            </button>
            <?php echo $this->element('Questionnaires.question_setting_choice_element', array('pageIndex'=>$pageIndex, 'qIndex'=>$qIndex)); ?>
            <span class="clearfix"></span>
        </li>
    </ul>

</div><!-- col-sm-6 -->

</div>

<div class="row">
    <div class="col-sm-12">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="question.has_another_choice" ng-change="changeAnotherChoice(<?php echo trim($pageIndex, '{}') ?>, <?php echo trim($qIndex, '{}') ?>, '<?php echo __d('questionnaires', 'other choice'); ?>', '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>')">
                <?php echo __d('questionnaires', 'add another choice'); ?>
            </label>
        </div>
    </div>
</div>