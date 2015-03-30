<?php
/**
 * questionnaire comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
* @author Allcreator <info@allcreator.net>
* @link http://www.netcommons.org NetCommons Project
* @license http://www.netcommons.org/license.txt NetCommons License
* @copyright Copyright 2014, NetCommons Project
*/
?>
<script type="text/ng-template" id="templateId.html">
    <!--
    <?php echo $this->element('Comments.form'); ?>
    <div class="text-center">
        <button type="button" class="btn btn-primary"><?php echo __d('questionnaires', 'send'); ?></button>
    </div>
    -->
    <?php echo $this->element('Comments.index'); ?>
</script>
<button type="button" comment-popover popover-placement="top" class="btn btn-primary questionnaire-btn-comment btn-lg questionnaire-comment-fixed">
    <span class="glyphicon glyphicon-comment"></span>
</button>