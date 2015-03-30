<?php
/**
 * questionnaire setting view template
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


<div id="nc-questionnaires-setting-<?php echo (int)$frameId; ?>"
     ng-controller="Questionnaires.setting"
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

        <?php echo $this->element('Questionnaires.edit_flow_chart', array('current'=>'3')); ?>

        <div class="page-header">
            <h2><?php echo __d('questionnaires', 'Questionnaire setting'); ?></h2>
        </div>

        <h3  class="questionnaire-setting-ttl">{{questionnaires.QuestionnaireEntity.title}}</h3>

        <div class="form-group">
            <?php echo $this->Form->input('sub_title',
                array('class' => 'form-control',
                    'label' => 'Sub Title',
                    'ng-model' => 'questionnaires.QuestionnaireEntity.sub_title',
                    'placeholder' => __d('questionnaires', 'Please enter if there is a sub title')
                    )
                );
            ?>
        </div>
        <div class="form-group">
            <label><?php echo __d('questionnaires', 'Questionnaire answer period'); ?></label>
            <div class="checkbox">
                <label>
                    <?php echo $this->Form->input('period_flag',
                    array(
                    'type' => 'checkbox',
                    'div' => false,
                    'label' => false,
                    'ng-model' => 'questionnaires.QuestionnaireEntity.period_flag'
                    )
                    );
                    ?>
                    <?php echo __d('questionnaires', 'set the answer period'); ?>
                    <span class="help-block"><?php echo __d('questionnaires', 'After approval will be immediately published . Stop of the questionnaire to select the stop from the questionnaire data list .'); ?></span>
                </label>
            </div>

            <div class="row" ng-show="questionnaires.QuestionnaireEntity.period_flag">
                <div class="col-sm-5">
                    <p class="input-group">
                        <input type="text" class="form-control" datepicker-popup="yyyy-MM-dd" ng-model="questionnaires.QuestionnaireEntity.start_period" show-weeks="false" is-open="calendar_opened[0]" min="minDate" max="questionnaires.QuestionnaireEntity.end_period" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" ng-click="openCal($event, 0)"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                    </p>
                    <div ng-model="questionnaires.QuestionnaireEntity.start_period">
                        <timepicker hour-step="1" minute-step="15" ></timepicker>
                    </div>
                    <?php echo $this->Form->input('start_period',
                    array(
                    'type' => 'hidden',
                    'label' => false,
                    'ng-value' => "questionnaires.QuestionnaireEntity.start_period | date : 'yyyy-MM-dd HH:mm:ss'"
                    )
                    );
                    ?>
                </div>
                <div class="col-sm-2"><p class="form-control-static text-center">から</p></div>
                <div class="col-sm-5">
                    <p class="input-group">
                        <input type="text" class="form-control" datepicker-popup="yyyy-MM-dd" ng-model="questionnaires.QuestionnaireEntity.end_period" show-weeks="false" is-open="calendar_opened[1]" min="questionnaires.QuestionnaireEntity.start_period"  />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-default" ng-click="openCal($event, 1)"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                    </p>
                    <div ng-model="questionnaires.QuestionnaireEntity.end_period">
                        <timepicker hour-step="1" minute-step="15" ></timepicker>
                    </div>
                    <?php echo $this->Form->input('end_period',
                    array(
                    'type' => 'hidden',
                    'label' => false,
                    'ng-value' => "questionnaires.QuestionnaireEntity.end_period | date : 'yyyy-MM-dd HH:mm:ss'"
                    )
                    );
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label><?php echo __d('questionnaires', 'Counting result display start date'); ?></label>
            <div class="checkbox">
                <label>
                    <input type="checkbox" ng-model="total_period_flag" >
                    <?php echo __d('questionnaires', 'set the aggregate display period'); ?>
                    <span class="help-block"><?php echo __d('questionnaires', '設定しない場合は、回答者の回答後に表示されます。.'); ?></span>
                </label>
            </div>

            <div class="row" ng-show="total_period_flag">
                <div class="col-sm-3">
                    <p class="input-group">
                        <input type="text" class="form-control" datepicker-popup="yyyy-MM-dd" show-weeks="false" ng-model="questionnaires.QuestionnaireEntity.total_show_start_peirod" is-open="calendar_opened[2]" min="minDate" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-default" ng-click="openCal($event, 2)"><i class="glyphicon glyphicon-calendar"></i></button>
                        </span>
                    </p>
                </div>
                <div class="col-sm-3">
                    <div ng-model="questionnaires.QuestionnaireEntity.total_show_start_peirod">
                        <timepicker hour-step="1" minute-step="15"></timepicker>
                    </div>
                </div>
                <div class="col-sm-6">
                    <?php echo $this->Form->input('total_show_start_peirod',
                    array(
                    'type' => 'hidden',
                    'label' => false,
                    'ng-value' => "questionnaires.QuestionnaireEntity.total_show_start_peirod | date : 'yyyy-MM-dd HH:mm:ss'"
                    )
                    );
                    ?>
                    から集計結果の表示を開始する
                </div>
            </div>
        </div>
        <div class="form-group">
            <label><?php echo __d('questionnaires', 'Questionnaire method'); ?></label>

            <?php echo $this->Form->input('no_member_flag',
            array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="checkbox"><label>',
            'after' => __d('questionnaires', 'accept the non-members answer') . '</label></div>',
            'ng-model' => 'questionnaires.QuestionnaireEntity.no_member_flag'
            )
            );
            ?>

            <?php echo $this->Form->input('key_pass_use_flag',
            array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="checkbox"><label>',
            'after' => __d('questionnaires', 'use key phrase') . '</label></div>',
            'ng-model' => 'questionnaires.QuestionnaireEntity.key_pass_use_flag'
            )
            );
            ?>
            <?php echo $this->Form->input('key_phrase',
            array(
            'label' => false,
            'class' => 'form-control',
            'ng-if' => 'questionnaires.QuestionnaireEntity.key_pass_use_flag',
            'ng-model' => 'questionnaires.QuestionnaireEntity.key_phrase'
            )
            );
            ?>

            <?php echo $this->Form->input('anonymity_flag',
            array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="checkbox"><label>',
            'after' => __d('questionnaires', 'anonymous answer') . '</label></div>',
            'ng-model' => 'questionnaires.QuestionnaireEntity.anonymity_flag'
            )
            );
            ?>

            <?php echo $this->Form->input('repeate_flag',
            array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="checkbox"><label>',
            'after' => __d('questionnaires', 'forgive the repetition of the answer') . '</label></div>',
            'ng-model' => 'questionnaires.QuestionnaireEntity.repeate_flag'
            )
            );
            ?>

            <?php echo $this->Form->input('image_authentication_flag',
            array(
            'type' => 'checkbox',
            'label' => false,
            'before' => '<div class="checkbox"><label>',
            'after' => __d('questionnaires', 'do image authentication') . '</label></div>',
            'ng-model' => 'questionnaires.QuestionnaireEntity.image_authentication_flag'
            )
            );
            ?>
        </div>

        <div class="form-group questionnaire-group">
            <label><?php echo __d('questionnaires', 'Thanks page message settings'); ?></label>
            <div class="nc-wysiwyg-alert">
                <?php echo $this->Form->textarea('thanks_content',
                array(
                'class' => 'form-control',
                'ng-model' => 'questionnaires.QuestionnaireEntity.thanks_content',
                'ui-tinymce' => 'tinymce.options',
                'rows' => 5,
                )) ?>
            </div>
        </div>

        <div class="panel panel-danger questionnaire-group" ng-if="!!questionnaires.Questionnaire.id">
            <div class="panel-heading">Danger Zone</div>
            <div class="panel-body">
                <dl>
                    <dt>
                        <h4 class="text-danger" ng-if="questionnaires.Questionnaire.questionnaire_status != <?php echo QuestionnairesComponent::STATUS_STOPPED ?>"><?php echo __d('questionnaires', 'Emergency stop of the questionnaire'); ?></h4>
                    <h4 class="text-primary" ng-if="questionnaires.Questionnaire.questionnaire_status == <?php echo QuestionnairesComponent::STATUS_STOPPED ?>"><?php echo __d('questionnaires', 'Release emergency stop'); ?></h4>
                    </dt>
                    <dd ng-if="questionnaires.Questionnaire.questionnaire_status != <?php echo QuestionnairesComponent::STATUS_STOPPED ?>">
                        <?php echo $this->Form->button(
                        __d('questionnaires', 'Stop this questionnaire'),
                        array(
                        'class' => 'btn btn-danger pull-right',
                        'name' => 'questionnaire_stopped',
                        )) ?>
                        <?php echo __d('questionnaires', 'You can stop the questionnaire on going temporarily.'); ?>
                    </dd>
                    <dd ng-if="questionnaires.Questionnaire.questionnaire_status == <?php echo QuestionnairesComponent::STATUS_STOPPED ?>">
                        <?php echo $this->Form->button(
                        __d('questionnaires', 'Resume this questionnaire'),
                        array(
                        'class' => 'btn btn-primary pull-right',
                        'name' => 'questionnaire_resumed',
                        )) ?>
                        <?php echo __d('questionnaires', 'You can stop the questionnaire on going temporarily.'); ?>
                    </dd>

                    <hr>

                    <dt>
                        <h4 class="text-danger"><?php echo __d('questionnaires', 'Delete this questionnaire'); ?></h4>
                    </dt>
                    <dd>
                        <?php echo $this->Form->button(
                        __d('questionnaires', 'Delete this questionnaire'),
                        array(
                        'class' => 'btn btn-danger pull-right',
                        'name' => 'questionnaire_deleted',
                        )) ?>
                        <?php echo __d('questionnaires', 'You can delete all data of this questionnaire.'); ?>
                    </dd>
                </dl>
            </div>
        </div>

        <?php echo $this->element('Comments.form'); ?>

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
            __d('net_commons', 'Save temporally'),
            array(
            'class' => 'btn btn-default',
            'name' => 'save_' . NetCommonsBlockComponent::STATUS_IN_DRAFT,
            )) ?>

            <?php echo $this->Form->button(
            __d('net_commons', 'OK'),
            array(
            'class' => 'btn btn-primary',
            'name' => 'save_' . NetCommonsBlockComponent::STATUS_PUBLISHED,
            )) ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>

    <?php echo $this->element('Comments.index'); ?>

</div>
