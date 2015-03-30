<?php
	echo $this->Form->create( 'Questionnaire', array(
		'id' => 'questionnare_answer_list_paginator_'. $frameId,
		'name' => 'questionnaire_form_answer_list_paginator',
		'type' => 'get',
		'novalidate' => true,
		'class' => 'form-inline',
		'inputDefaults' => array('label' => false, 'div' => false )
		)
	);
?>

<?php
	$maxSize = QUESTIONNAIRE_DEFAULT_DISPLAY_PAGE_SIZE;
	$pagination_directive =<<<EOL
<pagination
	ng-init="currentPage={$page['currentPageNumber']}; totalItems={$page['totalCount']}; itemsPerPage={$page['displayNumPerPage']};"
	num-pages="numPages"
	items-per-page="itemsPerPage"
	first-text="&laquo;"
	previous-text="&lsaquo;"
	next-text="&rsaquo;"
	last-text="&raquo;"
	total-items="totalItems"
	ng-model="currentPage"
	max-size="$maxSize"
	class="pagination-sm"
	boundary-links="true"
	on-select-page="pageChanged(page,{$frameId})"
	page="currentPage" >
</pagination>

EOL;
	echo $pagination_directive;

    echo $this->Form->input('Questionnaire.page.'.$frameId,
        array(
            'type' => 'hidden',
            'class' => 'form-control',
			'value' => ''
        )
    );


?>

<?php
	echo $this->Form->end();
?>
