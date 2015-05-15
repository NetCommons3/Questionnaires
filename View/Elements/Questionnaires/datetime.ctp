<div class="form-group ">

<div class="input-group">
	<?php echo $this->Form->input($fieldName,
		array('type' => 'text',
		'class' => 'form-control',
		'placeholder' => 'yyyy-mm-dd',
		'show-weeks' => 'false',
		'ng-model' => $ngModelName,
		'datetimepicker',
		'min' => $min,
		'max' => $max,
		'show-meridian' => 'false',
		'date-format' => 'yyyy-MM-dd',
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
