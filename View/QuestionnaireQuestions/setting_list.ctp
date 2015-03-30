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

    <?php echo $this->Form->create('QuestionnaireQuestion', array(
    'name' => 'questionnaire_form_question',
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

        <?php echo $this->element('Questionnaires.edit_flow_chart', array('current'=>'1')); ?>

        <div class="form-group">
            <?php echo $this->Form->input(
                'QuestionnaireEntity.title',
                array(
                    'label' => __d('questionnaires', 'Questionnaire title'),
                    'class' => 'form-control',
                    'ng-model' => 'questionnaire.QuestionnaireEntity.title',
                    'placeholder' => __d('questionnaires', 'Please input questionnaire title')
                ));
            ?>
            <accordion>
                <accordion-group ng-repeat="(pageIndex, page) in questionnaire.QuestionnairePage" heading="{{page.page_title}}"  is-open="true">
                    <accordion-heading>
                            <button class="btn btn-danger pull-right" ng-click="deletePage($index, '<?php echo __d('questionnaires', 'Do you want to delete this page?'); ?>')">
                                <?php echo __d('questionnaires', 'delete'); ?>
                            </button>
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default dropdown-toggle"><?php echo __d('questionnaires', 'Move page'); ?><span class="caret"></span></button>
                                <ul class="dropdown-menu" role="menu">
                                    <li ng-repeat="move_page in questionnaire.QuestionnairePage" ng-class="{disabled:(page.page_sequence==move_page.page_sequence) || (((move_page.page_sequence)-(page.page_sequence))==1)}">
                                        <a href="#" ng-click="movePage(page.page_sequence, $index)"><?php echo __d('questionnaires', 'before {{move_page.page_title}}'); ?></a>
                                    </li>
                                    <li  ng-if="!$last" class="divider"></li>
                                    <li ng-if="!$last"><a href="#" ng-click="movePage(page.page_sequence, questionnaire.QuestionnairePage.length)"><?php echo __d('questionnaires', 'move last'); ?></a></li>
                                </ul>
                            </div><!-- /btn-group -->

                            {{page.page_title}}

                            <?php
                           echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.page_sequence',
                                array(
                                    'ng-value' => 'page.page_sequence'
                                ));
                                echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.id',
                                array(
                                'ng-value' => 'page.id'
                                ));
                            ?>
                        <span class="clearfix"></span>
                    </accordion-heading>
                    <div class="form-inline">
                        <?php echo $this->Form->input('QuestionnairePage.{{pageIndex}}.page_title',
                        array(
                        'label' => __d('questionnaires', 'page title'),
                        'class' => 'form-control',
                        'ng-model' => 'page.page_title'
                        ));
                        ?>
                    </div>
                    <accordion>
                        <accordion-group heading="{{question.question_value}}" ng-repeat="(qIndex, question) in page.QuestionnaireQuestion" class="panel-secondary">
                            <accordion-heading>

                                <button class="close pull-right" type="button" ng-click="deleteQuestion($event, pageIndex, qIndex, '<?php echo __d('questionnaires', 'Do you want to delete this question ?'); ?>')">
                                    <span class="glyphicon glyphicon-remove"> </span>
                                </button>

                                <button class="btn btn-default pull-left" type="button" ng-disabled="$first" ng-click="moveQuestion($event, pageIndex, qIndex, qIndex-1)">
                                    <span class="glyphicon glyphicon-arrow-up"></span>
                                </button>

                                <button class="btn btn-default pull-left" type="button" ng-disabled="$last" ng-click="moveQuestion($event, pageIndex, qIndex, qIndex+1)">
                                    <span class="glyphicon glyphicon-arrow-down"></span>
                                </button>

                                {{question.question_value|htmlToPlaintext}}
                                <span class="clearfix"></span>

                            </accordion-heading>

                            <div class="row form-group">
                                <?php echo $this->Form->label('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_value',
                                    __d('questionnaires', 'question sentence'),
                                    array('class' => 'col-sm-2'));
                                ?>
                                <?php
                           echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_sequence',
                                array(
                                'ng-value' => 'question.question_sequence'
                                ));
                                echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.id',
                                array(
                                'ng-value' => 'question.id'
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <?php echo $this->Form->checkbox('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.require_flag',
                                            array(
                                            'value' => true,
                                            'ng-model' => 'question.require_flag'
                                            ));
                                            ?>
                                            <?php echo __d('questionnaires', 'Required'); ?>
                                        </label>
                                    </div>
                                    <?php echo $this->Form->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_value',
                                    array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'form-control',
                                    'ng-model' => 'question.question_value',
                                    'required' => 'required',
                                    )) ?>
                                    <div class="nc-wysiwyg-alert">
                                        <?php echo $this->Form->textarea('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.description',
                                        array(
                                        'class' => 'form-control',
                                        'ng-model' => 'question.description',
                                        'ui-tinymce' => 'tinymce.options',
                                        'rows' => 5,
                                        'required' => 'required',
                                        )) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group">
                                <?php echo $this->Form->label('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_type',
                                __d('questionnaires', 'Question type'),
                                array('class' => 'col-sm-2'));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                  echo $this->Form->select('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_type',
                                        $questionTypeOptions,
                                        array(
                                            'class' => 'form-control',
                                            'ng-model' => 'question.question_type',
                                            'empty' => null
                                        ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group well">
                                <div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_SELECTION; ?>">
                                    <?php echo $this->element('Questionnaires.question_setting_alternative', array('pageIndex'=>'{{pageIndex}}', 'qIndex'=>'{{qIndex}}')); ?>
                                </div>
                                <div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
                                    <?php echo $this->element('Questionnaires.question_setting_alternative', array('pageIndex'=>'{{pageIndex}}', 'qIndex'=>'{{qIndex}}')); ?>
                                </div>
                                <div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_TEXT; ?>">
                                    <?php echo $this->element('Questionnaires.question_setting_text', array('pageIndex'=>'{{pageIndex}}', 'qIndex'=>'{{qIndex}}')); ?>
                                </div>
                                <div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_TEXT_AREA; ?>">
                                    <?php /* 複数行テキストの場合は詳細設定がないです */ ?>
                                </div>
                                <div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?>">
                                    <?php echo $this->element('Questionnaires.question_setting_matrix_alternative', array('pageIndex'=>'{{pageIndex}}', 'qIndex'=>'{{qIndex}}')); ?>
                                </div>
                                <div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
                                    <?php echo $this->element('Questionnaires.question_setting_matrix_alternative', array('pageIndex'=>'{{pageIndex}}', 'qIndex'=>'{{qIndex}}')); ?>
                                </div>
                                <div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_DATE_AND_TIME; ?>">
                                    <?php echo $this->element('Questionnaires.question_setting_date', array('pageIndex'=>'{{pageIndex}}', 'qIndex'=>'{{qIndex}}')); ?>
                                </div>
                                <div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
                                    <?php echo $this->element('Questionnaires.question_setting_alternative', array('pageIndex'=>'{{pageIndex}}', 'qIndex'=>'{{qIndex}}')); ?>
                                </div>
                            </div>

                        </accordion-group>
                    </accordion>
                    <div class="text-center">
                        <button class="btn btn-primary" type="button" ng-click="addQuestion($event, pageIndex, '<?php echo __d('questionnaires', 'New Question'); ?>')">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo __d('questionnaires', 'Add Question'); ?>
                        </button>
                    </div>
                </accordion-group>
            </accordion>
        </div>

        <div class="text-center">
            <button class="btn btn-primary" type="button" ng-click="addPage($event, '<?php echo __d('questionnaires', 'page'); ?>')">
                <span class="glyphicon glyphicon-plus"></span><?php echo __d('questionnaire', 'Add page'); ?>
            </button>
        </div>
    </div>
    <div class="modal-footer">
        <div class="text-center">
            <a class="btn btn-default" href="/<?php echo $topUrl; ?>">
                <span class="glyphicon glyphicon-remove"></span>
                <?php echo __d('net_commons', 'Cancel'); ?>
            </a>
            <?php
            /*
            <a class="btn btn-default" href="<?php echo $backUrl; ?>">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <?php echo __d('net_commons', 'BACK'); ?>
            </a>
            */
            ?>
            <?php echo $this->Form->button(
            __d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
            array(
            'class' => 'btn btn-default',
            'name' => 'next_' . '',
            )) ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div>