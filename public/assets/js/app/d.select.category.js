/*
 * LaraClassified - Classified Ads Web Application
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

/* Prevent errors, If these variables are missing. */
if (typeof packageIsEnabled === 'undefined') {
	var packageIsEnabled = false;
}
var select2Language = languageCode;
if (typeof langLayout !== 'undefined' && typeof langLayout.select2 !== 'undefined') {
	select2Language = langLayout.select2;
}
if (typeof permanentPostsEnabled === 'undefined') {
	var permanentPostsEnabled = 0;
}
if (typeof postTypeId === 'undefined') {
	var postTypeId = 0;
}

$(document).ready(function () {
	
	/* CSRF Protection */
	var token = $('meta[name="csrf-token"]').attr('content');
	if (token) {
		$.ajaxSetup({
			headers: {'X-CSRF-TOKEN': token},
			async: true,
			cache: false
		});
	}
	
	/* On load */
	var catId = $('#categoryId').val();
	getCategories(siteUrl, languageCode, catId);
	
	/* On category selected */
	$(document).on('click', '.cat-link', function (e) {
		e.preventDefault(); /* prevents the submit or reload */
		
		catId = $(this).data('id');
		if (typeof catId === 'undefined') {
			catId = 0;
		}
		getCategories(siteUrl, languageCode, catId);
	});
	
	/* Show the permanent posts option field */
	showPermanentPostsOption(permanentPostsEnabled, postTypeId);
	$('input[name="post_type_id"]').on('click', function () {
		postTypeId = $(this).val();
		showPermanentPostsOption(permanentPostsEnabled, postTypeId);
	});
	
});

function getCategories(siteUrl, languageCode, catId) {
	/* Make ajax call */
	$.ajax({
		method: 'POST',
		url: siteUrl + '/ajax/category/select-category',
		data: {
			'_token': $('input[name=_token]').val(),
			'catId': catId
		}
	}).done(function (obj) {
		
		if (typeof obj.html === 'undefined' || typeof obj.hasChildren === 'undefined') {
			return false;
		}
		
		/* Get & append the category's children */
		if (obj.hasChildren) {
			$('#selectCats').html(obj.html);
		} else {
			/* Select the category & append it */
			$('#catsContainer').html(obj.html);
			
			if (typeof obj.category === 'undefined' || typeof obj.category.tid === 'undefined') {
				return false;
			}
			
			/* Save data in hidden field */
			$('#categoryId').val(obj.category.tid);
			$('#categoryType').val(obj.category.type);
			
			/* Close Categories Modal */
			$('#browseCategories').modal('hide');
			
			/* Apply category's type actions & Get category's custom-fields */
			applyCategoryTypeActions('categoryType', obj.category.type, packageIsEnabled);
			getCustomFieldsByCategory(siteUrl, languageCode, obj.category.tid);
		}
	});
}

/**
 * Get the Custom Fields by Category
 *
 * @param siteUrl
 * @param languageCode
 * @param catId
 * @returns {*}
 */
function getCustomFieldsByCategory(siteUrl, languageCode, catId) {
	/* Check undefined variables */
	if (typeof languageCode === 'undefined' || typeof catId === 'undefined') {
		return false;
	}
	
	/* Don't make ajax request if any category has selected. */
	if (catId === 0 || catId === '') {
		return false;
	}
	
	/* Make ajax call */
	$.ajax({
		method: 'POST',
		url: siteUrl + '/ajax/category/custom-fields',
		data: {
			'_token': $('input[name=_token]').val(),
			'languageCode': languageCode,
			'catId': catId,
			'errors': errors,
			'oldInput': oldInput,
			'postId': (typeof postId !== 'undefined') ? postId : ''
		}
	}).done(function (obj) {
		/* Load Custom Fields */
		$('#cfContainer').html(obj.customFields);
		
		/* Apply Fields Components */
		initSelect2($('#cfContainer'), languageCode);
		$('#cfContainer').find('.selecter, .sselecter').select2({
			width: '100%'
		});
	});
	
	return catId;
}

/**
 * Apply Category Type actions (for Job offer/search & Services for example)
 *
 * @param categoryTypeFieldId
 * @param categoryTypeValue
 * @param packageIsEnabled
 */
function applyCategoryTypeActions(categoryTypeFieldId, categoryTypeValue, packageIsEnabled) {
	$('#' + categoryTypeFieldId).val(categoryTypeValue);
	
	/* Debug */
	/* console.log(categoryTypeFieldId + ': ' + categoryTypeValue); */
	
	if (categoryTypeValue === 'job-offer') {
		$('#postTypeBloc label[for="post_type_id-1"]').show();
		$('#priceBloc label[for="price"]').html(lang.salary);
		$('#priceBloc').show();
	} else if (categoryTypeValue === 'job-search') {
		$('#postTypeBloc label[for="post_type_id-2"]').hide();
		
		$('#postTypeBloc input[value="1"]').attr('checked', 'checked');
		$('#priceBloc label[for="price"]').html(lang.salary);
		$('#priceBloc').show();
	} else if (categoryTypeValue === 'not-salable') {
		$('#priceBloc').hide();
		
		$('#postTypeBloc label[for="post_type_id-2"]').show();
	} else {
		$('#postTypeBloc label[for="post_type_id-2"]').show();
		$('#priceBloc label[for="price"]').html(lang.price);
		$('#priceBloc').show();
	}
	
	$('#nextStepBtn').html(lang.nextStepBtnLabel.next);
}

function initSelect2(selectElementObj, languageCode) {
	selectElementObj.find('.selecter').select2({
		language: select2Language,
		dropdownAutoWidth: 'true',
		minimumResultsForSearch: Infinity
	});
	
	selectElementObj.find('.sselecter').select2({
		language: select2Language,
		dropdownAutoWidth: 'true'
	});
}

/**
 * Show the permanent posts option field
 *
 * @param permanentPostsEnabled
 * @param postTypeId
 * @returns {boolean}
 */
function showPermanentPostsOption(permanentPostsEnabled, postTypeId)
{
	if (permanentPostsEnabled == '0') {
		$('#isPermanentBox').empty();
		return false;
	}
	if (permanentPostsEnabled == '1') {
		if (postTypeId == '1') {
			$('#isPermanentBox').removeClass('hide');
		} else {
			$('#isPermanentBox').addClass('hide');
			$('#isPermanent').prop('checked', false);
		}
	}
	if (permanentPostsEnabled == '2') {
		if (postTypeId == '2') {
			$('#isPermanentBox').removeClass('hide');
		} else {
			$('#isPermanentBox').addClass('hide');
			$('#isPermanent').prop('checked', false);
		}
	}
	if (permanentPostsEnabled == '3') {
		var isPermanentField = $('#isPermanent');
		if (isPermanentField.length) {
			if (postTypeId == '2') {
				isPermanentField.val('1');
			} else {
				isPermanentField.val('0');
			}
		}
	}
	if (permanentPostsEnabled == '4') {
		$('#isPermanentBox').removeClass('hide');
	}
}