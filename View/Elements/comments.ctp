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

<div class="panel panel-default">
    <div class="panel-body workflow-comments">
        <div ng-repeat="(cIndex, comment) in comments" class="comment form-group">
            <div>
                <a href="" ng-click="user.showUser(comment.trackableCreator.id)">
                    <b>{{comment.trackableCreator.username}}</b>
                </a>
                <small class="text-muted">{{comment.comment.created}}</small>
            </div>
            <div>
                {{comment.comment.comment}}
            </div>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-info btn-block more "
                    ng-click="workflow.more()">
                <?php echo h(__d('net_commons', 'More')); ?>
            </button>
        </div>
    </div>
</div>
