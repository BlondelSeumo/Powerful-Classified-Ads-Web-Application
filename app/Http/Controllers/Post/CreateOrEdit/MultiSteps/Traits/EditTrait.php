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

namespace App\Http\Controllers\Post\CreateOrEdit\MultiSteps\Traits;

use App\Helpers\Ip;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\City;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use Torann\LaravelMetaTags\Facades\MetaTag;

trait EditTrait
{
	/**
	 * Show the form the create a new ad post.
	 *
	 * @param $postIdOrToken
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function getUpdateForm($postIdOrToken)
	{
		$data = [];
		
		// Get Post
		if (request()->segment(2) == 'create') {
			if (!session()->has('tmpPostId')) {
				return redirect('posts/create');
			}
			$post = Post::currentCountry()->with(['city'])
				->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', session('tmpPostId'))
				->where('tmp_token', $postIdOrToken)
				->first();
		} else {
			$post = Post::currentCountry()->with(['city'])
				->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', auth()->user()->id)
				->where('id', $postIdOrToken)
				->first();
		}
		
		if (empty($post)) {
			abort(404);
		}
		
		view()->share('post', $post);
		
		// Share the Post's Latest Payment Info (If exists)
		$this->sharePostLatestPaymentInfo($post);
		
		// Get the Post's Administrative Division
		if (config('country.admin_field_active') == 1 && in_array(config('country.admin_type'), ['1', '2'])) {
			if (!empty($post->city)) {
				$adminType = config('country.admin_type');
				$adminModel = '\App\Models\SubAdmin' . $adminType;
				
				// Get the City's Administrative Division
				$admin = $adminModel::where('code', $post->city->{'subadmin' . $adminType . '_code'})->first();
				if (!empty($admin)) {
					view()->share('admin', $admin);
				}
			}
		}
		
		// Meta Tags
		MetaTag::set('title', t('update_my_ad'));
		MetaTag::set('description', t('update_my_ad'));
		
		return appView('post.createOrEdit.multiSteps.edit', $data);
	}
	
	/**
	 * Update the Post
	 *
	 * @param $postIdOrToken
	 * @param PostRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postUpdateForm($postIdOrToken, PostRequest $request)
	{
		// Get Post
		if (request()->segment(2) == 'create') {
			if (!session()->has('tmpPostId')) {
				return redirect('posts/create');
			}
			$post = Post::currentCountry()->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', session('tmpPostId'))
				->where('tmp_token', $postIdOrToken)
				->first();
		} else {
			$post = Post::currentCountry()->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', auth()->user()->id)
				->where('id', $postIdOrToken)
				->first();
		}
		
		if (empty($post)) {
			abort(404);
		}
		
		// Get the Post's City
		$city = City::find($request->input('city_id', 0));
		if (empty($city)) {
			flash(t("posting_ads_is_disabled"))->error();
			
			return back()->withInput();
		}
		
		// Conditions to Verify User's Email or Phone
		$emailVerificationRequired = config('settings.mail.email_verification') == 1
			&& $request->filled('email')
			&& $request->input('email') != $post->email;
		$phoneVerificationRequired = config('settings.sms.phone_verification') == 1
			&& $request->filled('phone')
			&& $request->input('phone') != $post->phone;
		
		/*
		 * Allow admin users to approve the changes,
		 * If the ads approbation option is enable,
		 * And if important data have been changed.
		 */
		if (config('settings.single.posts_review_activation')) {
			if (
				md5($post->title) != md5($request->input('title')) ||
				md5($post->description) != md5($request->input('description'))
			) {
				$post->reviewed = 0;
			}
		}
		
		// Update Post
		$input = $request->only($post->getFillable());
		foreach ($input as $key => $value) {
			$post->{$key} = $value;
		}
		$post->negotiable = $request->input('negotiable');
		$post->phone_hidden = $request->input('phone_hidden');
		$post->lat = $city->latitude;
		$post->lon = $city->longitude;
		$post->ip_addr = Ip::get();
		
		// Email verification key generation
		if ($emailVerificationRequired) {
			$post->email_token = md5(microtime() . mt_rand());
			$post->verified_email = 0;
		}
		
		// Phone verification key generation
		if ($phoneVerificationRequired) {
			$post->phone_token = mt_rand(100000, 999999);
			$post->verified_phone = 0;
		}
		
		// Save
		$post->save();
		
		// Custom Fields
		$this->createPostFieldsValues($post, $request);
		
		// Get Next URL
		$creationPath = (request()->segment(2) == 'create') ? 'create/' : '';
		flash(t("your_ad_has_been_updated"))->success();
		$nextStepUrl = 'posts/' . $creationPath . $postIdOrToken . '/photos';
		
		// Send Email Verification message
		if ($emailVerificationRequired) {
			$this->sendVerificationEmail($post);
			$this->showReSendVerificationEmailLink($post, 'post');
		}
		
		// Send Phone Verification message
		if ($phoneVerificationRequired) {
			// Save the Next URL before verification
			session()->put('itemNextUrl', $nextStepUrl);
			
			$this->sendVerificationSms($post);
			$this->showReSendVerificationSmsLink($post, 'post');
			
			// Go to Phone Number verification
			$nextStepUrl = 'verify/post/phone/';
		}
		
		// Redirection
		return redirect($nextStepUrl);
	}
}
