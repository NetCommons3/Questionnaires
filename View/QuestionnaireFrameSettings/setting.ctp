<?php
/**
 * questionnaire setting list view template
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
<div id="nc-questionnaire-frame-settings-content-list-<?php echo (int)$frameId; ?>"
     ng-controller="QuestionnairesFrame"
     ng-init="initialize(<?php echo (int)$frameId; ?>,	<?php echo h(json_encode($questionnaires)); ?>)">

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

    <?php echo $this->Form->create('QuestionnaireFrameSetting', array(
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

        <ul class="nav nav-tabs">
            <?php foreach ($tabLists as $tab): ?>
            <li role="presentation" class="<?php echo $tab['class'] ?>">
                <a href="<?php echo $tab['href']; ?>">
                    <?php echo $tab['tabTitle']; ?>
                </a>
            </li>
            <?php endforeach ?>
        </ul>
        <div class="tab-body has-feedback" >

            <div class="row form-group questionnaire-group">
                <label ><?php echo __d('questionnaires', 'Questionnaire display setting'); ?></label>
                <?php echo $this->Form->input('display_type',
                array('type' => 'radio',
                'options' => array(QuestionnairesComponent::DISPLAY_TYPE_SINGLE=>__d('questionnaires', 'Show only one questionnaire'),
                                QuestionnairesComponent::DISPLAY_TYPE_LIST=>__d('questionnaires', 'Show questionnaires list')),
                'legend' => false,
                'label' => false,
                'before' => '<div class="radio"><label>',
                'separator' => '</label></div><div class="radio"><label>',
                'after' => '</label></div>',
                'ng-model' => 'questionnaires.QuestionnaireFrameSettings.display_type'
                )); ?>
            </div>

            <div class="form-group questionnaire-group">
                <label><?php echo __d('questionnaires', 'select display questionnaires.'); ?></label>

                <div class="form-inline">
                    <label>
                        <?php echo __d('questionnaires', 'Narrow down'); ?>
                        <input type="search" class="form-control" ng-model="q.Entity.title" placeholder="<?php echo __d('questionnaires', 'Refine by entering the part of the questionnaire name'); ?>" />
                    </label>
                </div>
                <div style="height:300px;overflow-y: scroll;">
                <table class="table table-hover">
                    <tr>
                        <th><?php echo __d('questionnaires', 'Display'); ?></th>
                        <th><?php echo __d('questionnaires', 'Status'); ?></th>
                        <th><?php echo __d('questionnaires', 'Title'); ?></th>
                        <th><?php echo __d('questionnaires', 'Implementation date'); ?></th>
                        <th><?php echo __d('questionnaires', 'Aggregates'); ?></th>
                    </tr>
                    <tr class="animate-repeat btn-default" ng-repeat="item in questionnaires.items | filter:q" >
                        <td>
                            <div class="radio" ng-if="questionnaires.QuestionnaireFrameSettings.display_type == <?php echo QuestionnairesComponent::DISPLAY_TYPE_SINGLE; ?>">
                               <input type="radio" name="display" style="margin-left:10px;">
                            </div>
                            <div class="checkbox" ng-if="questionnaires.QuestionnaireFrameSettings.display_type == <?php echo QuestionnairesComponent::DISPLAY_TYPE_LIST; ?>">
                                <input type="checkbox" name="display[]" style="margin-left:10px;">
                            </div>
                        </td>
                        <td>
                            <?php echo $this->element('Questionnaires.status_label',
                            array('status' => 'item.Entity.status')); ?>
                            <span class="label label-danger" ng-if="item.Questionnaire.questionnaire_status == <?php echo(QuestionnairesComponent::STATUS_STOPPED); ?>">
                                <?php echo __d('questionnaire', 'Stopped'); ?>
                            </span>
                        </td>
                        <td>{{item.Entity.title}}</td>
                        <td><span ng-if="!item.Entity.start_period && !item.Entity.end_period">
                                                    <?php echo __d('questionnaires', 'Not limited'); ?>
                                                </span>
                                                <span ng-if="!!item.Entity.start_period || !!item.Entity.end_period">
                                                    {{item.Entity.start_period | ncDatetime}}
                                                    <?php echo __d('questionnaires', ' - '); ?>
                                                    {{item.Entity.end_period | ncDatetime}}
                                                </span>
                        </td>
                        <td>
                            <span ng-if="item.Entity.total_show_flag == <?php echo(QuestionnairesComponent::EXPRESSION_SHOW); ?>">
                                <?php echo __d('questionnaires', 'Yes'); ?>
                            </span>
                        </td>
                    </tr>
                </table>

                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <div class="text-center">
            <a class="btn btn-default" href="/<?php echo $topUrl; ?>">
                <span class="glyphicon glyphicon-remove"></span>
                <?php echo __d('net_commons', 'Cancel'); ?>
            </a>
            <?php echo $this->Form->button(
            __d('net_commons', 'OK'),
            array(
            'class' => 'btn btn-primary',
            'name' => 'save_' . NetCommonsBlockComponent::STATUS_PUBLISHED,
            )) ?>
        </div>
    </div>

    <?php echo $this->Form->end(); ?>
</div>