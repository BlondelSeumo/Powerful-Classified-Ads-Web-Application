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

use App\Helpers\VideoEmbedding;
use Illuminate\Contracts\Validation\Rule;

class VideoLinkIsValidRule implements Rule
{
	public $attrLabel = '';
	
	public function __construct($attrLabel = '')
	{
		$this->attrLabel = $attrLabel;
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
		$value = trim($value);
		
		// Get the video standard link
		try {
			$value = VideoEmbedding::getVideoUrl($value);
		} catch (\Exception $e) {
			dd($e->getMessage());
		}
		
		if ($value !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		if (!empty($this->attrLabel)) {
			return trans('validation.video_link_is_valid_rule', ['attribute' => mb_strtolower($this->attrLabel)]);
		} else {
			if (!empty($this->attr) && !empty(trans('validation.attributes.' . $this->attr))) {
				return trans('validation.video_link_is_valid_rule', ['attribute' => trans('validation.attributes.' . $this->attr)]);
			} else {
				return trans('validation.video_link_is_valid_rule');
			}
		}
	}
}
