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

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Auth\Traits\VerificationTrait;
use App\Http\Requests\UserRequest;
use App\Models\Scopes\VerifiedScope;
use App\Models\UserType;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use App\Models\User;

class EditController extends AccountBaseController
{
	use VerificationTrait;
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$data = [];
		
		$data['countries'] = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
		$data['genders'] = Gender::trans()->get();
		
		// Mini Stats
		$data['countPostsVisits'] = DB::table('posts')
			->select('user_id', DB::raw('SUM(visits) as total_visits'))
			->where('country_code', config('country.code'))
			->where('user_id', auth()->user()->id)
			->groupBy('user_id')
			->first();
		$data['countPosts'] = Post::currentCountry()
			->where('user_id', auth()->user()->id)
			->count();
		$data['countFavoritePosts'] = SavedPost::whereHas('post', function ($query) {
			$query->currentCountry();
		})->where('user_id', auth()->user()->id)
			->count();
		
		// Meta Tags
		MetaTag::set('title', t('my_account'));
		MetaTag::set('description', t('my_account_on', ['appName' => config('settings.app.app_name')]));
		
		return appView('account.edit', $data);
	}
	
	/**
	 * @param UserRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function updateDetails(UserRequest $request)
	{
		// Check if these fields has changed
		$emailChanged = $request->filled('email') && $request->input('email') != auth()->user()->email;
		$phoneChanged = $request->filled('phone') && $request->input('phone') != auth()->user()->phone;
		$usernameChanged = $request->filled('username') && $request->input('username') != auth()->user()->username;
		
		// Conditions to Verify User's Email or Phone
		$emailVerificationRequired = config('settings.mail.email_verification') == 1 && $emailChanged;
		$phoneVerificationRequired = config('settings.sms.phone_verification') == 1 && $phoneChanged;
		
		// Get User
		$user = User::withoutGlobalScopes([VerifiedScope::class])->find(auth()->user()->id);
		
		// Update User
		$input = $request->only($user->getFillable());
		foreach ($input as $key => $value) {
			if (in_array($key, ['email', 'phone', 'username']) && empty($value)) {
				continue;
			}
			$user->{$key} = $value;
		}
		
		$user->phone_hidden = $request->input('phone_hidden');
		
		// Email verification key generation
		if ($emailVerificationRequired) {
			$user->email_token = md5(microtime() . mt_rand());
			$user->verified_email = 0;
		}
		
		// Phone verification key generation
		if ($phoneVerificationRequired) {
			$user->phone_token = mt_rand(100000, 999999);
			$user->verified_phone = 0;
		}
		
		// Don't logout the User (See User model)
		if ($emailVerificationRequired || $phoneVerificationRequired) {
			session()->put('emailOrPhoneChanged', true);
		}
		
		// Save
		$user->save();
		
		// Message Notification & Redirection
		flash(t("account_details_has_updated_successfully"))->success();
		$nextUrl = 'account';
		
		// Send Email Verification message
		if ($emailVerificationRequired) {
			$this->sendVerificationEmail($user);
			$this->showReSendVerificationEmailLink($user, 'user');
		}
		
		// Send Phone Verification message
		if ($phoneVerificationRequired) {
			// Save the Next URL before verification
			session()->put('itemNextUrl', $nextUrl);
			
			$this->sendVerificationSms($user);
			$this->showReSendVerificationSmsLink($user, 'user');
			
			// Go to Phone Number verification
			$nextUrl = 'verify/user/phone/';
		}
		
		// Redirection
		return redirect($nextUrl);
	}
	
	/**
	 * Store the User's photo.
	 *
	 * @param $userId
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function updatePhoto($userId, Request $request)
	{
		if (isDemo()) {
			$message = t('demo_mode_message');
			
			if ($request->ajax()) {
				return response()->json(['error' => $message]);
			}
			
			flash($message)->info();
			
			return back();
		}
		
		// Get User
		$user = null;
		if ($userId == auth()->user()->id) {
			$user = User::find($userId);
		}
		
		if (!isset($user) || empty($user)) {
			$msg = t('User not found');
			if ($request->ajax()) {
				return response()->json(['error' => $msg]);
			}
			abort(404, $msg);
		}
		
		// Save all pictures
		$file = $request->file('photo');
		if (!empty($file)) {
			// Post Picture in database
			$user->photo = $file;
			$user->save();
		}
		
		// Ajax response
		if ($request->ajax()) {
			$data = [];
			$data['initialPreview'] = [];
			$data['initialPreviewConfig'] = [];
			
			if (!empty($user->photo)) {
				// Get Deletion Url
				$initialPreviewConfigUrl = url('account/' . $user->id . '/photo/delete');
				
				// Build Bootstrap-Input plugin's parameters
				$data['initialPreview'][] = imgUrl($user->photo, 'user');
				
				$data['initialPreviewConfig'][] = [
					'caption' => last(explode(DIRECTORY_SEPARATOR, $user->photo)),
					'size'    => (isset($this->disk) && $this->disk->exists($user->photo)) ? (int)$this->disk->size($user->photo) : 0,
					'url'     => $initialPreviewConfigUrl,
					'key'     => $user->id,
					'extra'   => ['id' => $user->id],
				];
			}
			
			return response()->json($data);
		}
		
		flash(t('Your photo or avatar have been updated'))->success();
		
		// Non ajax response
		return redirect(url('account'));
	}
	
	/**
	 * Delete the User's photo
	 *
	 * @param $userId
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function deletePhoto($userId, Request $request)
	{
		if (isDemo()) {
			$message = t('demo_mode_message');
			
			if ($request->ajax()) {
				return response()->json(['error' => $message]);
			}
			
			flash($message)->info();
			
			return back();
		}
		
		// Get User
		$user = null;
		if ($userId == auth()->user()->id) {
			$user = User::find($userId);
		}
		
		if (!isset($user) || empty($user)) {
			$msg = t('User not found');
			if ($request->ajax()) {
				return response()->json(['error' => $msg]);
			}
			abort(404, $msg);
		}
		
		// Remove all the current user's photos, by removing his photo directory.
		$destinationPath = substr($user->photo, 0, strrpos($user->photo, '/'));
		if ($this->disk->exists($destinationPath)) {
			$this->disk->deleteDirectory($destinationPath);
		}
		
		// Delete the photo path from DB
		$user->photo = null;
		$user->save();
		
		if ($request->ajax()) {
			return response()->json([]);
		}
		
		flash(t("Your photo or avatar has been deleted"))->success();
		
		return back();
	}
	
	/**
	 * @param UserRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function updateSettings(UserRequest $request)
	{
		// Get User
		$user = User::find(auth()->user()->id);
		
		// Update
		$user->disable_comments = (int)$request->input('disable_comments');
		if ($request->filled('password')) {
			$user->password = Hash::make($request->input('password'));
		}
		if ($request->filled('accept_terms')) {
			$user->accept_terms = (int)$request->input('accept_terms');
		}
		$user->accept_marketing_offers = (int)$request->input('accept_marketing_offers');
		$user->time_zone = $request->input('time_zone');
		$user->save();
		
		flash(t("account_settings_has_updated_successfully"))->success();
		
		return redirect('account');
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function updatePreferences()
	{
		$data = [];
		
		return appView('account.edit', $data);
	}
}
