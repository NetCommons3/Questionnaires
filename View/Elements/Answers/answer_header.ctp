<?php
/**
 * answer header view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<header>
    <h1>
        <?php echo $questionnaire['questionnaire']['title']; ?>
        <?php if (isset($questionnaire['questionnaire']['subTitle'])): ?>
        <small><?php echo $questionnaire['questionnaire']['subTitle'];?></small>
        <?php endif ?>
    </h1>
</header>