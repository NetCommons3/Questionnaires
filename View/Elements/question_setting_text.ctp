<div class="row">
    <div class="col-sm-12">
        <div class="checkbox">
            <label>
                <?php echo $this->Form->checkbox('QuestionnairePage.'.$pageIndex.'.QuestionnaireQuestion.'.$qIndex.'.question_type_option',
                array(
                'value' => QuestionnairesComponent::TYPE_OPTION_NUMERIC,
                'ng-model' => 'question.question_type_option',
                'ng-checked' => 'question.question_type_option == '.QuestionnairesComponent::TYPE_OPTION_NUMERIC
                ));
                ?>
                <?php echo __d('questionnaires', 'Numeric'); ?>
            </label>
        </div>
    </div>
</div>

<div class="row"  ng-if="question.question_type_option == <?php echo QuestionnairesComponent::TYPE_OPTION_NUMERIC; ?>">
    <div class="col-sm-12">
        <div class="form-inline">
            <?php echo $this->Form->input('QuestionnairePage.'.$pageIndex.'.QuestionnaireQuestion.'.$qIndex.'.min',
            array(
            'label' => __d('questionnaires', 'minimum'),
            'class' => 'form-control',
            'ng-model' => 'question.min'
            ));
            ?>

        </div>
        <div class="form-inline">
            <?php echo $this->Form->input('QuestionnairePage.'.$pageIndex.'.QuestionnaireQuestion.'.$qIndex.'.max',
            array(
            'label' => __d('questionnaires', 'maximum'),
            'class' => 'form-control',
            'ng-model' => 'question.max'
            ));
            ?>
        </div>
    </div>
</div>
