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
<label for="questionnaireStatusFilter-<?php echo $frameId?>" class="control-label">
    <?php echo __d('questionnaires', 'Edit status'); ?>
</label>
<?php
    $options = array(
       NetCommonsBlockComponent::STATUS_PUBLISHED => __d('net_commons', 'Published'),
        NetCommonsBlockComponent::STATUS_APPROVED => __d('net_commons', 'Approving'),
        NetCommonsBlockComponent::STATUS_IN_DRAFT => __d('net_commons', 'Temporary'),
        NetCommonsBlockComponent::STATUS_DISAPPROVED => __d('net_commons', 'Disapproving')
    );
    echo $this->Form->select('status', $options,
        array(
            'id' => "questionnaireStatusFilter-$frameId",
            'class' => 'form-control',
            'ng-model' => 'filter.status',
            'onchange' => 'angular.element(this).scope().statusChange(this)',
            'empty' => __d('net_commons', 'All Display')
        ));
?>


</select>