<?php
/**
 * 実施後のアンケート
 * 質問の種別によって異なる詳細設定のファイル
 * このファイルでは択一選択、複数選択、リスト選択タイプをフォローしている
 * 設定内容を見せるだけで実質編集は何もできない
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
		<ul class="list-group ">
			<li class="list-group-item" ng-repeat="(cIndex, choice) in question.QuestionnaireChoice" >
				<div class="form-inline">
					<?php echo $this->element('Questionnaires.Questions/edit/question_setting_choice_element', array('pageIndex' => $pageIndex, 'qIndex' => $qIndex, 'isPublished' => $isPublished)); ?>
				</div>
			</li>
		</ul>
	</div>
</div>