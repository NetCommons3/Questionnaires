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
<div class="col-xs-12 questionnaire-chart-wrapper" >
    <?php foreach ($question['QuestionnaireChoice'] as $choiceId => $choice): ?>
    <?php if ($choice['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX): ?>
        <?php $dataStr = '[' . $questionId . '][' . $choice['origin_id'] . ']'; ?>
                <nvd3 options='config<?php echo $dataStr; ?>'
                      data='data<?php echo $dataStr; ?>'>
                </nvd3>
    <?php endif; ?>
    <?php endforeach; ?>
</div>
