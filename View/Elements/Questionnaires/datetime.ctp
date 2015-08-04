<?php echo $this->element('NetCommons.datetimepicker'); ?>

<div class="form-group ">

<div class="input-group">
	<?php echo $this->Form->input($fieldName,
		array('type' => 'text',
		'id' => $fieldName,
		'class' => 'form-control',
		'placeholder' => 'yyyy-mm-dd',
		'show-weeks' => 'false',
		'ng-model' => $ngModelName,
		'datetimepicker',
		'datetimepicker-options' => "{format:'YYYY-MM-DD HH:mm'}",
		'min' => $min,
		'max' => $max,
		'ng-focus' => 'setMinMaxDate($event, \'' . $min . '\', \'' . $max . '\')',
		'show-meridian' => 'false',
		'label' => false,
		'div' => false));
		?>
	<div class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></div>
</div>

<?php echo $this->element(
	'NetCommons.errors', [
	'errors' => $this->validationErrors,
	'model' => $model,
	'field' => $fieldName,
	]) ?>
</div>
