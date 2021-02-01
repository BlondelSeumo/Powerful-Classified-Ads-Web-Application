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

namespace App\Helpers\Search\Traits\Filters;

trait PriceFilter
{
	protected function applyPriceFilter()
	{
		if (!isset($this->having)) {
			return;
		}
		
		$minPrice = null;
		if (request()->filled('minPrice')) {
			$minPrice = request()->get('minPrice');
		}
		
		$maxPrice = null;
		if (request()->filled('maxPrice')) {
			$maxPrice = request()->get('maxPrice');
		}
		
		if (!empty($minPrice)) {
			$this->having[] = 'calculatedPrice >= ' . $minPrice; // price
		}
		
		if (!empty($maxPrice)) {
			$this->having[] = 'calculatedPrice <= ' . $maxPrice; // price
		}
	}
}
