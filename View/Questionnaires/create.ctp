<?php
/**
 * questionnaire create view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
*/
?>

<?php echo $this->Html->script('http://rawgit.com/angular/bower-angular-sanitize/v1.2.25/angular-sanitize.js', false); ?>
<?php echo $this->Html->script('http://rawgit.com/m-e-conroy/angular-dialog-service/v5.2.0/src/dialogs.js', false); ?>

<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('Questionnaires.questionnaire_common.js');?>
<?php echo $this->Html->script('Questionnaires.questionnaires.js');?>
<?php echo $this->Html->script('Questionnaires.questionnaires_edit.js');?>

<?php echo $this->Html->css('Questionnaires.questionnaire.css'); ?>

<div id="nc-questionnaires-setting-list-<?php echo (int)$frameId; ?>"
     ng-controller="Questionnaires.create"
     ng-init="initialize(<?php echo (int)$frameId; ?>,
									<?php echo h(json_encode($questionnaires)); ?>)">

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
            <div class="row">

                <div class="col-lg-12">
                    <p><?php echo __d('questionnaires', 'You can create a new questionnaire.'); ?></p>
                </div>

                <?php echo $this->Form->create('QuestionnaireEntity', array(
                'type' => 'post',
                'novalidate' => true,
                )); ?>
                <?php echo $this->Form->hidden('Frame.id', array(
                'value' => $frameId,
                )); ?>
                <?php echo $this->Form->hidden('Block.id', array(
                'value' => $blockId,
                )); ?>
                <div class="form-group col-lg-12">
                    <div class="radio">
                        <label>
                            <input type="radio" name="create_option" value="<?php echo QUESTIONNAIRE_CREATE_OPT_NEW; ?>" ng-model="createOption"><?php echo __d('questionnaires', 'Create new questionnaire'); ?>
                        </label>
                    </div>
                    <div  collapse="createOption != '<?php echo QUESTIONNAIRE_CREATE_OPT_NEW; ?>'">
                        <label for="QuestionnaireEntity.title" class="sr-only"><?php echo __d('questionnaires', 'Questionnaire title'); ?></label>
                        <input type="text" class="form-control" id="QuestionnaireEntity.title" name="title" ng-model="newTitle" placeholder="<?php echo __d('questionnaires', 'Please input questionnaire title'); ?>">
                    </div>
                </div><!-- /form-group 1-->

                <div class="form-group col-lg-12">
                    <div class="radio" name="create_option" ng-model="isCollapse">
                        <label>
                            <input type="radio" name="create_option" value="<?php echo QUESTIONNAIRE_CREATE_OPT_REUSE; ?>" ng-model="createOption"><?php echo __d('questionnaires', 'Re-use past questionnaire'); ?>
                        </label>
                    </div>
                    <div class="form-group" collapse="createOption != '<?php echo QUESTIONNAIRE_CREATE_OPT_REUSE; ?>'">
                        <div class="form-inline">
                            <label>
                                <?php echo __d('questionnaires', 'Narrow down'); ?>
                                <input type="search" class="form-control" ng-model="q.Entity.title" placeholder="<?php echo __d('questionnaires', 'Refine by entering the part of the questionnaire name'); ?>" />
                            </label>
                        </div>
                        <ul class="questionnaire-select-box form-control ">
                            <li class="animate-repeat btn-default" ng-repeat="item in questionnaires.items | filter:q" ng-model="$parent.pastQuestionnaireSelect" btn-radio="item.Entity.questionnaire_id" uncheckable>
                                {{item.Entity.title}}(
                                                    <span ng-if="!item.Entity.start_period && !item.Entity.end_period">
                                                        <?php echo __d('questionnaires', 'Not limited'); ?>
                                                    </span>
                                                    <span ng-if="!!item.Entity.start_period || !!item.Entity.end_period">
                                                        {{item.Entity.start_period | ncDatetime}}
                                                        <?php echo __d('questionnaires', ' - '); ?>
                                                        {{item.Entity.end_period | ncDatetime}}
                                                        <?php echo __d('questionnaires', 'Implementation'); ?>
                                                    </span>
                                )
                            </li>
                        </ul>
                        <input type="hidden" name="questionnaire_id" value="{{pastQuestionnaireSelect}}" />
                    </div><!-- /input-group -->
                </div><!-- /form-group 2-->

                <div class="text-center">
                    <button class="btn btn-default" ng-click="cancel()" ng-disabled="sending">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?php echo __d('net_commons', 'Cancel'); ?>
                    </button>
                    <?php echo $this->Form->button(
                    __d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
                    array(
                    'class' => 'btn btn-default',
                    'name' => 'next_' . '',
                    'ng-disabled' => "!((createOption=='" . QUESTIONNAIRE_CREATE_OPT_NEW . "' && newTitle) || (createOption=='" . QUESTIONNAIRE_CREATE_OPT_REUSE . "' && pastQuestionnaireSelect))",
                    )) ?>
                </div>

                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>