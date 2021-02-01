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

namespace App\Rules;

use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Contracts\Validation\Rule;

class CustomFieldUniqueChildrenRule implements Rule
{
	public $parameters = [];
	public $attribute;
	
	public function __construct($parameters)
	{
		$this->parameters = $parameters;
	}
	
	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		if (!isset($this->parameters[0]) || !isset($this->parameters[1])) {
			return false;
		}
		
		$this->attribute = $attribute;
		
		$categoryId = ($attribute == 'category_id') ? $value : $this->parameters[1];
		
		// Get category's children
		$cats = Category::trans()->where('parent_id', $categoryId)->get();
		
		// Check children records
		return $this->checkIfFieldExistsInAllChildren($cats, $attribute, $value);
	}
	
	/**
	 * @param $cats
	 * @param $attribute
	 * @param $value
	 * @return bool
	 */
	private function checkIfFieldExistsInAllChildren($cats, $attribute, $value)
	{
		$doesNotExist = true;
		
		if ($cats->count() > 0) {
			foreach ($cats as $cat) {
				if ($attribute == 'category_id') {
					$subCatField = CategoryField::where($this->parameters[0], $this->parameters[1])->where($attribute, $cat->id)->first();
				} else {
					$subCatField = CategoryField::where($this->parameters[0], $cat->id)->where($attribute, $value)->first();
				}
				
				// If field exists in a child of the current category & the 'disabled_in_subcategories' option is not set,
				// Then prevent to link this field to the current category.
				if (!empty($subCatField) && request()->input('disabled_in_subcategories') != 1) {
					$doesNotExist = false;
					break;
				}
				
				if ($doesNotExist && isset($cat->children) && $cat->children->count() > 0) {
					return $this->checkIfFieldExistsInAllChildren($cat->children, $attribute, $value);
				}
			}
		}
		
		return $doesNotExist;
	}
	
	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		if ($this->attribute == 'category_id') {
			$message = trans('validation.custom_field_unique_children_rule', [
				'field_1' => trans('admin.category'),
				'field_2' => trans('admin.custom field'),
			]);
		} else {
			$message = trans('validation.custom_field_unique_children_rule_field', [
				'field_1' => trans('admin.custom field'),
				'field_2' => trans('admin.category'),
			]);
		}
		
		return $message;
	}
}
