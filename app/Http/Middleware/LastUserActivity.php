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

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class LastUserActivity
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// Waiting time in minutes
		$waitingTime = 5;
		
		if (auth()->check()) {
			if (config('settings.optimization.cache_driver') == 'array') {
				if (Schema::hasColumn('users', 'last_activity')) {
					$user = auth()->user();
					if ($user->last_activity < Carbon::now()->subMinutes($waitingTime)->format('Y-m-d H:i:s')) {
						$user = auth()->user();
						$user->last_activity = new Carbon;
						$user->timestamps = false;
						$user->save();
					}
				}
			} else {
				$expiresAt = Carbon::now()->addMinutes($waitingTime);
				Cache::store('file')->put('user-is-online-' . auth()->user()->id, true, $expiresAt);
			}
		}
		
		return $next($request);
	}
}
