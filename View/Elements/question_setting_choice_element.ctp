<input type="text"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][choice_label]"
       ng-model="choice.choice_label"
        />
<span ng-if="choice.other_choice_type != <?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
    （これは「その他」選択肢です。実施時にはテキストを入力するエリアが自動的に付与されます。）
</span>
<?php // Version1ではchoice_valueの値はchoice_labelと同じにしておく ?>
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][choice_value]"
       ng-value="choice.choice_label"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][choice_sequence]"
       ng-value="choice.choice_sequence"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][matrix_type]"
       ng-value="choice.matrix_type"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][other_choice_type]"
       ng-value="choice.other_choice_type"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][id]"
       ng-value="choice.id"
        />
