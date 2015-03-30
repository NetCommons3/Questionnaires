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
<?php echo $this->element('Questionnaires.scripts'); ?>
<?php echo $this->Html->script('Questionnaires.questionnaires_edit.js');?>
<?php echo $this->Html->script('Questionnaires.questionnaires_edit_question.js');?>



<?php echo $this->element('Questionnaires.comment_form'); ?>


<div id="nc-questionnaires-setting-list-<?php echo (int)$frameId; ?>"
     ng-controller="Questionnaires.edit.question"
     ng-init="initialize(<?php echo (int)$frameId; ?>,
									<?php echo h(json_encode($questionnaire)); ?>)">

    <?php $this->start('title'); ?>
    <?php echo __d('questionnaires', 'plugin_name'); ?>
    <?php $this->end(); ?>

    <div class="modal-header">
        <?php $title = $this->fetch('title'); ?>
        <?php if ($title) : ?>
        <?php echo $title; ?>
        <?php else : ?>
        <br />
        <?php endif; ?>
    </div>

    <?php echo $this->Form->create('QuestionnaireEntity', array(
    'type' => 'post',
    'novalidate' => true,
    )); ?>

    <?php echo $this->Form->hidden('id'); ?>
    <?php echo $this->Form->hidden('Frame.id', array(
    'value' => $frameId,
    )); ?>
    <?php echo $this->Form->hidden('Block.id', array(
    'value' => $blockId,
    )); ?>

        <div class="modal-body">

            <?php echo $this->element('Questionnaires.edit_flow_chart', array('current'=>'2')); ?>

            <div class="page-header">
            <h2><?php echo __d('questionnaires', 'Aggregate results setting'); ?></h2>
            </div>

            <div class="bg-info">
                <h3  class="questionnaire-setting-ttl">{{questionnaire.QuestionnaireEntity.title}}</h3>
                <span class="help-block questionnaire-setting-ttl-help"><?php echo __d('questionnaires', 'Please edit return to create the question screen if you want to change the questionnaire title'); ?></span>
            </div>

            <div class="row form-group questionnaire-group">
                <label ><?php echo __d('questionnaires', 'Published aggregate results'); ?></label>
                    <?php echo $this->Form->input('total_show_flag',
                            array('type' => 'radio',
                            'options' => array(QuestionnairesComponent::EXPRESSION_NOT_SHOW=>__d('questionnaires', 'not disclose the total result'), QuestionnairesComponent::EXPRESSION_SHOW=>__d('questionnaires', 'publish aggregate result')),
                            'legend' => false,
                            'label' => false,
                            'before' => '<div class="radio"><label>',
                            'separator' => '</label></div><div class="radio"><label>',
                           'after' => '</label></div>',
                            'ng-model' => 'questionnaire.QuestionnaireEntity.total_show_flag'
                        )); ?>
            </div>

            <div ng-if="questionnaire.QuestionnaireEntity.total_show_flag == <?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>">

                <div class="form-group questionnaire-group">
                    <label><?php echo __d('questionnaires', 'Text to be displayed in the aggregate results page'); ?></label>
                    <div class="nc-wysiwyg-alert">
                        <?php echo $this->Form->textarea('QuestionnaireEntity.total_comment',
                        array(
                        'class' => 'form-control',
                        'ng-model' => 'questionnaire.QuestionnaireEntity.total_comment',
                        'ui-tinymce' => 'tinymce.options',
                        'rows' => 5,
                        )) ?>
                    </div>
                </div>

                <div class="form-group questionnaire-group">
                    <label><?php echo __d('questionnaires', 'Question you want to display the aggregate results'); ?></label>
                    <accordion ng-repeat="(pageIndex, page) in questionnaire.QuestionnairePage">
                        <accordion-group ng-repeat="(qIndex, question) in page.QuestionnaireQuestion">
                            <accordion-heading>
                                <span class="glyphicon" ng-class="{'glyphicon-eye-open': question.result_display_flag==<?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>, 'glyphicon-eye-close': question.result_display_flag==<?php echo QuestionnairesComponent::EXPRESSION_NOT_SHOW; ?>}"> </span>
                                {{question.question_value|htmlToPlaintext}}
                            </accordion-heading>

                            <div class="form-group">
                                <label><?php echo __d('questionnaires', 'aggregate display');?></label>
                                <?php echo $this->Form->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.result_display_flag',
                                array('type' => 'radio',
                                'options' => array(QuestionnairesComponent::EXPRESSION_NOT_SHOW => __d('questionnaires', 'The results of this question will not be displayed'),
                                                    QuestionnairesComponent::EXPRESSION_SHOW => __d('questionnaires', 'The results of this question will be displayed')),
                                'legend' => false,
                                'label' => false,
                                'before' => '<div class="radio"><label>',
                                'separator' => '</label></div><div class="radio"><label>',
                                'after' => '</label></div>',
                                'ng-model' => 'question.result_display_flag',
                                'ng-disabled' => 'question.question_type == ' . QuestionnairesComponent::TYPE_TEXT . ' || question.question_type == ' .  QuestionnairesComponent::TYPE_TEXT_AREA . ' || question.question_type == ' . QuestionnairesComponent::TYPE_DATE_AND_TIME
                                )); ?>
                            </div>
                            <div ng-if="question.result_display_flag == <?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>">
                                <div class="form-group">
                                    <label><?php echo __d('questionnaires', 'display type');?></label>
                                    <?php echo $this->Form->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.result_display_type',
                                    array('type' => 'radio',
                                    'options' => array(QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART => __d('questionnaires', 'Bar Chart'),
                                                        QuestionnairesComponent::RESULT_DISPLAY_TYPE_PIE_CHART => __d('questionnaires', 'Pie Chart'),
                                                        QuestionnairesComponent::RESULT_DISPLAY_TYPE_TABLE => __d('questionnaires', 'Table')),
                                    'legend' => false,
                                    'label' => false,
                                    'before' => '<div class="radio"><label>',
                                    'separator' => '</label></div><div class="radio"><label>',
                                    'after' => '</label></div>',
                                    'ng-model' => 'question.result_display_type'
                                    )); ?>
                                </div>
                                <div class="form-group" ng-if="question.result_display_type != <?php echo QuestionnairesComponent::RESULT_DISPLAY_TYPE_TABLE; ?>">
                                    <label><?php echo __d('questionnaires', 'graph color');?></label>
                                    <table class="table table-condensed" ng-if="question.question_type != <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?> && question.question_type != <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
                                        <tr ng-repeat="(cIndex, choice) in question.QuestionnaireChoice">
                                            <td>
                                                <div class="col-sm-9">
                                                    {{choice.choice_label}}
                                                </div>
                                                <div class="col-sm-3">
                                                    <color-palette-picker selected='selected' name='QuestionnairePage[{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{cIndex}}][graph_color]' ng-model='choice.graph_color'></color-palette-picker>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table table-condensed" ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?> || question.question_type == <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
                                        <tr ng-repeat="(cIndex, choice) in question.QuestionnaireChoice | filter : {matrix_type:<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>}">
                                            <td>
                                                <div class="col-sm-9">
                                                    {{choice.choice_label}}
                                                </div>
                                                <div class="col-sm-3">
                                                    <color-palette-picker selected='selected' name='QuestionnairePage[{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{cIndex}}][graph_color]' ng-model='choice.graph_color'></color-palette-picker>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </accordion-group>
                    </accordion>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="text-center">
                <a class="btn btn-default" href="/<?php echo $topUrl; ?>">
                    <span class="glyphicon glyphicon-remove"></span>
                    <?php echo __d('net_commons', 'Cancel'); ?>
                </a>
                <a class="btn btn-default" href="<?php echo $backUrl; ?>">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    <?php echo __d('net_commons', 'BACK'); ?>
                </a>
                <?php echo $this->Form->button(
                __d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
                array(
                'class' => 'btn  btn-default',
                'name' => 'next_' . '',
                )) ?>
            </div>
        </div>
    <?php echo $this->Form->end(); ?>
</div>