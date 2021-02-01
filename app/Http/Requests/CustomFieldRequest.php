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

namespace App\Http\Requests;

use App\Helpers\Files\Storage\StorageDisk;
use App\Models\CategoryField;
use App\Models\PostValue;
use App\Rules\DateIsValidRule;
use App\Rules\VideoLinkIsValidRule;

class CustomFieldRequest extends Request
{
	protected $customFields = [];
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$request = request();
		if ($this->has('parent_id') && $this->has('category_id')) {
			$request = $this;
		}
		
		$rules = [];
		
		// Custom Fields
		$this->customFields = CategoryField::getFields($request->input('category_id'));
		if ($this->customFields->count() > 0) {
			foreach ($this->customFields as $field) {
				$cfRules = [];
				
				// Check if the field is required
				if ($field->required == 1 && $field->type != 'file') {
					$cfRules[] = 'required';
				}
				
				if ($field->type == 'url') {
					if ($request->filled('cf.' . $field->tid)) {
						$cfRules[] = 'url';
					}
				}
				
				if ($field->type == 'number') {
					if ($request->filled('cf.' . $field->tid)) {
						$cfRules[] = 'integer';
					}
				}
				
				if ($field->type == 'date' || $field->type == 'date_time') {
					if ($request->filled('cf.' . $field->tid)) {
						$cfRules[] = new DateIsValidRule('valid', true, $field->name);
					}
				}
				
				if ($field->type == 'video') {
					if ($request->filled('cf.' . $field->tid)) {
						$cfRules[] = 'url';
						$cfRules[] = new VideoLinkIsValidRule($field->name);
					}
				}
				
				// Check if the field is an upload type
				if ($field->type == 'file') {
					$fileExists = false;
					
					if ($request->filled('post_id')) {
						$postValue = PostValue::where('post_id', $request->input('post_id'))->where('field_id', $field->tid)->first();
						if (!empty($postValue)) {
							$disk = StorageDisk::getDisk();
							if (trim($postValue->value) != '' && $disk->exists($postValue->value)) {
								$fileExists = true;
							}
						}
					}
					
					if ($field->required == 1) {
						if (!$fileExists) {
							$cfRules[] = 'required';
						}
					}
					
					$cfRules[] = 'mimes:' . getUploadFileTypes('file');
					$cfRules[] = 'min:' . (int)config('settings.upload.min_file_size', 0);
					$cfRules[] = 'max:' . (int)config('settings.upload.max_file_size', 1000);
				}
				
				$rules['cf.' . $field->tid] = $cfRules;
			}
		}
		
		return $rules;
	}
	
	/**
	 * @return array
	 */
	public function messages()
	{
		$messages = [];
		
		if ($this->customFields->count() > 0) {
			foreach ($this->customFields as $field) {
				// If the field is required
				if ($field->required == 1) {
					$messages['cf.' . $field->tid . '.required'] = t('The field is required', ['field' => mb_strtolower($field->name)]);
				}
				
				if ($field->type == 'url' || $field->type == 'video') {
					$messages['cf.' . $field->tid . '.url'] = t('The field field must be a valid URL', ['field' => mb_strtolower($field->name)]);
				}
				
				if ($field->type == 'number') {
					$messages['cf.' . $field->tid . '.integer'] = t('The field field must be a number', ['field' => mb_strtolower($field->name)]);
				}
				
				if ($field->type == 'date' || $field->type == 'date_time') {
					// Check out the DateIsValidRule class
				}
				
				if ($field->type == 'video') {
					// See the URL type field above, and...
					// Check out the other rules about videos validation in the the VideoLinkIsValidRule class
				}
				
				// If the field is an upload type
				if ($field->type == 'file') {
					$messages['cf.' . $field->tid . '.mimes'] = t('The file of field must be in the good format', ['field' => mb_strtolower($field->name)]);
					$messages['cf.' . $field->tid . '.min'] = t('The file size of field may not be lower than N', [
						'field' => mb_strtolower($field->name),
						'min'   => (int)config('settings.upload.min_file_size', 0),
					]);
					$messages['cf.' . $field->tid . '.max'] = t('The file size of field may not be greater than N', [
						'field' => mb_strtolower($field->name),
						'max'   => (int)config('settings.upload.max_file_size', 1000),
					]);
				}
			}
		}
		
		return $messages;
	}
}
