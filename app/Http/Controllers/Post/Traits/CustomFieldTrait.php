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

namespace App\Http\Controllers\Post\Traits;

use App\Models\CategoryField;
use App\Models\Post;
use App\Models\PostValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait CustomFieldTrait
{
	/**
	 * Get Category's Custom Fields Buffer
	 *
	 * @param $catId
	 * @param $languageCode
	 * @param null $errors
	 * @param null $oldInput
	 * @param null $postId
	 * @return string
	 */
	public function getCategoryFieldsBuffer($catId, $languageCode, $errors = null, $oldInput = null, $postId = null)
	{
		$html = '';
		
		$fields = CategoryField::getFields($catId, $postId, $languageCode);
		
		if (count($fields) > 0) {
			$data = [
				'fields'       => $fields,
				'languageCode' => $languageCode,
				'errors'       => $errors,
				'oldInput'     => $oldInput,
			];
			$html = getViewContent('post.inc.fields', $data);
		}
		
		return $html;
	}
	
	/**
	 * Create & Update for Custom Fields
	 *
	 * @param Post $post
	 * @param Request $request
	 * @return array
	 */
	public function createPostFieldsValues(Post $post, Request $request)
	{
		$postValues = [];
		
		if (empty($post)) {
			return $postValues;
		}
		
		// Delete all old PostValue entries, if exist
		$oldPostValues = PostValue::with(['field'])->where('post_id', $post->id)->get();
		if ($oldPostValues->count() > 0) {
			foreach ($oldPostValues as $oldPostValue) {
				if ($oldPostValue->field->type == 'file') {
					if ($request->hasFile('cf.' . $oldPostValue->field->tid)) {
						$oldPostValue->delete();
					}
				} else {
					$oldPostValue->delete();
				}
			}
		}
		
		// Get Category's Fields details
		$fields = CategoryField::getFields($request->input('category_id'));
		if ($fields->count() > 0) {
			foreach ($fields as $field) {
				if ($field->type == 'file') {
					if ($request->hasFile('cf.' . $field->tid)) {
						// Get file's destination path
						$destinationPath = 'files/' . strtolower($post->country_code) . '/' . $post->id;
						
						// Get the file
						$file = $request->file('cf.' . $field->tid);
						
						// Check if the file is valid
						if (!$file->isValid()) {
							continue;
						}
						
						// Get filename & file path
						$filename    = $file->getClientOriginalName();
						$extension   = $file->getClientOriginalExtension();
						$newFilename = md5($filename . time()) . '.' . $extension;
						$filePath    = $destinationPath . '/' . $newFilename;
						
						$postValueInfo = [
							'post_id'  => $post->id,
							'field_id' => $field->tid,
							'value'    => $filePath,
						];
						
						$newPostValue = new PostValue($postValueInfo);
						$newPostValue->save();
						
						$this->disk->put($newPostValue->value, File::get($file->getrealpath()));
						
						$postValues[$newPostValue->id] = $newPostValue;
					}
				} else {
					if ($request->filled('cf.' . $field->tid)) {
						// Get the input
						$input = $request->input('cf.' . $field->tid);
						
						if (is_array($input)) {
							foreach ($input as $optionId => $optionValue) {
								$postValueInfo = [
									'post_id'   => $post->id,
									'field_id'  => $field->tid,
									'option_id' => $optionId,
									'value'     => $optionValue,
								];
								
								$newPostValue = new PostValue($postValueInfo);
								$newPostValue->save();
								$postValues[$newPostValue->id][$optionId] = $newPostValue;
							}
						} else {
							$postValueInfo = [
								'post_id'  => $post->id,
								'field_id' => $field->tid,
								'value'    => $input,
							];
							
							$newPostValue = new PostValue($postValueInfo);
							$newPostValue->save();
							$postValues[$newPostValue->id] = $newPostValue;
						}
					}
				}
			}
		}
		
		return $postValues;
	}
	
	/**
	 * Get Post's Custom Fields Values
	 *
	 * @param $catId
	 * @param $postId
	 * @return \Illuminate\Support\Collection
	 */
	public function getPostFieldsValues($catId, $postId)
	{
		// Get the Post's Custom Fields by its Parent Category
		$customFields = CategoryField::getFields($catId, $postId);
		
		// Get the Post's Custom Fields that have a value
		$postValue = [];
		if ($customFields->count() > 0) {
			foreach ($customFields as $key => $field) {
				if (!empty($field->default)) {
					$postValue[$key] = $field;
				}
			}
		}
		
		return collect($postValue);
	}
}
