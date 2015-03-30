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
        <div class="checkbox">
            <label ng-if="question.question_type != <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
                <?php echo $this->Form->checkbox('QuestionnairePage.'.$pageIndex.'.QuestionnaireQuestion.'.$qIndex.'.choice_random_flag',
                array(
                'value' => QuestionnairesComponent::USES_USE,
                'ng-model' => 'question.choice_random_flag',
                'ng-checked' => 'question.choice_random_flag == '.QuestionnairesComponent::USES_USE
                ));
                ?>
                <?php echo __d('questionnaires', 'randomaize choices'); ?>
            </label>
            <label ng-if="question.question_type != <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
                <?php echo $this->Form->checkbox('QuestionnairePage.'.$pageIndex.'.QuestionnaireQuestion.'.$qIndex.'.skip_flag',
                array(
                'value' => QuestionnairesComponent::USES_USE,
                'ng-model' => 'question.skip_flag',
                'ng-checked' => 'question.skip_flag == '.QuestionnairesComponent::USES_USE,
                'ng-disabled' => 'questionnaire.QuestionnairePage.length <= 2'
                ));
                ?>
                <?php echo __d('questionnaires', 'set page skip'); ?>
            </label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <ul class="list-group ">
            <li class="list-group-item" ng-repeat="(cIndex, choice) in question.QuestionnaireChoice" >
                <button class="close pull-right" type="button" ng-click="deleteChoice($event, pageIndex, qIndex, choice.choice_sequence, '<?php echo __d('questionnaires', 'Do you want to delete this choice ?'); ?>')">
                    <span class="glyphicon glyphicon-remove"> </span>
                </button>
                <select name="data[QuestionnairePage][<?php echo $pageIndex; ?>][QuestionnaireQuestion][<?php echo $qIndex; ?>][QuestionnaireChoice][{{cIndex}}][skip_page_sequence]"
                        class="pull-right"
                        ng-model="choice.skip_page_sequence"
                        ng-if="question.skip_flag == <?php echo QuestionnairesComponent::USES_USE; ?> && question.question_type != <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
                    <option ng-repeat="skip_page in questionnaire.QuestionnairePage" ng-value="{{skip_page.page_sequence}}" ng-disabled="skip_page.page_sequence == pageIndex">{{skip_page.page_title}}</option>
                    <option value="<?php echo QuestionnairesComponent::SKIP_GO_TO_END ?>"><?php echo __d('questionnaires', 'goto end'); ?></option>
                </select>
                <?php echo $this->element('Questionnaires.question_setting_choice_element', array('pageIndex'=>$pageIndex, 'qIndex'=>$qIndex)); ?>
                <span class="clearfix"></span>
            </li>
        </ul>
    </div>
</div>
<div class="row" ng-if="question.question_type != <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
    <div class="col-sm-12">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="question.has_another_choice" ng-change="changeAnotherChoice(<?php echo trim($pageIndex, '{}') ?>, <?php echo trim($qIndex, '{}') ?>, '<?php echo __d('questionnaires', 'other choice'); ?>', '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>')">
                <?php echo __d('questionnaires', 'add another choice'); ?>
            </label>
        </div>
    </div>
</div>
<div class="row text-center">
    <button type="button" class="btn btn-default" ng-click="addChoice($event, <?php echo trim($pageIndex, '{}') ?>, <?php echo trim($qIndex, '{}') ?>, '<?php echo __d('questionnaires', 'new choice'); ?>', '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
        <span class="glyphicon glyphicon-plus"></span>
        <?php echo __d('questionnaires', 'add choices'); ?>
    </button>
</div>
<?php
/* まだデータテンプレートからの読み込み方式が提唱されていないのでコメントアウトしておく TODO: 2015.03.11
<div class="row text-center">
    <select class="form-control">
        <option>データテンプレートから選択肢を読み込む</option>
    </select>
</div>
*/
?>
