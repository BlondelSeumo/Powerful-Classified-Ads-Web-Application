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

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Post\CreateOrEdit\Traits\CategoriesTrait;
use App\Http\Controllers\Post\Traits\CustomFieldTrait;
use App\Http\Controllers\FrontController;
use Illuminate\Http\Request;

class CategoryController extends FrontController
{
	use CategoriesTrait, CustomFieldTrait;
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCategoriesHtml(Request $request)
	{
		$languageCode = $request->input('languageCode');
		$catId = $request->input('catId');
		$catId = !empty($catId) ? $catId : null;
		
		// Get categories, parent & children
		$data = $this->categories($catId);
		
		// Get categories list buffer
		$html = getViewContent('post.createOrEdit.inc.category.select', $data);
		
		// Send JSON Response
		$result = [
			'html'        => $html,
			'hasChildren' => $data['hasChildren'],
			'category'    => $data['category'],
			'parent'      => (!empty($data['category']->parent)) ? $data['category']->parent : null,
		];
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCustomFields(Request $request)
	{
		$languageCode = $request->input('languageCode');
		$catId = $request->input('catId');
		$postId = $request->input('postId');
		
		$catId = !empty($catId) ? $catId : null;
		
		// Custom Fields vars
		$errors = $request->input('errors');
		$errors = convertUTF8HtmlToAnsi($errors); // Convert UTF-8 HTML to ANSI
		$errors = stripslashes($errors);
		$errors = collect(json_decode($errors, true));
		// ...
		$oldInput = $request->input('oldInput');
		$oldInput = convertUTF8HtmlToAnsi($oldInput); // Convert UTF-8 HTML to ANSI
		$oldInput = stripslashes($oldInput);
		$oldInput = json_decode($oldInput, true);
		
		// Get the Category's Custom Fields buffer
		$customFields = $this->getCategoryFieldsBuffer($catId, $languageCode, $errors, $oldInput, $postId);
		
		// Get Result's Data
		$data = [
			'customFields' => $customFields,
		];
		
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
	}
}
