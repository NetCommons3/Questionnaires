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
<div class="col-xs-12 questionnaire-chart-wrapper" >
    <nvd3 options='config[<?php echo $questionId; ?>]'
          data='data[<?php echo $questionId; ?>]'></nvd3>
</div>
