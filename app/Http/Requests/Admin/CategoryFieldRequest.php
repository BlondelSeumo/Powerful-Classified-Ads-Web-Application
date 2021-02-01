<?php
/**
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

namespace App\Http\Requests\Admin;

use App\Rules\CustomFieldUniqueRule;
use App\Rules\CustomFieldUniqueChildrenRule;
use App\Rules\CustomFieldUniqueParentRule;

class CategoryFieldRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
	
		if ($this->segment(2) == 'categories') {
			$rules['field_id'] = [
				'required',
				'not_in:0',
				new CustomFieldUniqueRule(['category_id', $this->category_id]),
				new CustomFieldUniqueParentRule(['category_id', $this->category_id]),
				new CustomFieldUniqueChildrenRule(['category_id', $this->category_id]),
			];
		}
	
		if ($this->segment(2) == 'custom_fields') {
			$rules['category_id'] = [
				'required',
				'not_in:0',
				new CustomFieldUniqueRule(['field_id', $this->field_id]),
				new CustomFieldUniqueParentRule(['field_id', $this->field_id]),
				new CustomFieldUniqueChildrenRule(['field_id', $this->field_id]),
			];
		}
        
        return $rules;
    }
    
    /**
     * @return array
     */
    public function messages()
    {
        $messages = [
            'category_id.required' => trans('admin.The field is required', ['field' => trans('admin.category')]),
            'category_id.not_in'   => trans('admin.The field is required And cannot be 0.', ['field' => trans('admin.category')]),
            'field_id.required'    => trans('admin.The field is required', ['field' => trans('admin.custom field')]),
            'field_id.not_in'      => trans('admin.The field is required And cannot be 0.', ['field' => trans('admin.custom field')]),
        ];
        
        return $messages;
    }
}
