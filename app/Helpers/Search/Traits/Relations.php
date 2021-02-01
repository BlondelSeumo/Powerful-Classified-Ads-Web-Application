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

namespace App\Helpers\Search\Traits;

use App\Helpers\Search\Traits\Relations\PaymentRelation;

trait Relations
{
	use PaymentRelation;
	
	protected function setPostRelations()
	{
		if (!isset($this->posts)) {
			dd('Fatal Error: Search relations cannot be applied.');
		}
		
		// category
		$this->posts->with(['category' => function ($query) {
			$query->with('parent');
		}])->has('category');
		
		// postType
		$this->posts->with('postType');
		
		// latestPayment
		$this->setPaymentRelation();
		
		// city
		$this->posts->with('city')->has('city');
		
		// pictures
		$this->posts->with('pictures');
		
		// savedByLoggedUser
		$this->posts->with('savedByLoggedUser');
	}
}
