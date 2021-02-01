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

namespace App\Http\Controllers\Post\CreateOrEdit\MultiSteps;

// Increase the server resources
$iniConfigFile = __DIR__ . '/../../../../../Helpers/Functions/ini.php';
if (file_exists($iniConfigFile)) {
	$configForUpload = true;
	include_once $iniConfigFile;
}

use App\Helpers\UrlGen;
use App\Http\Controllers\Post\CreateOrEdit\Traits\PricingTrait;
use App\Http\Controllers\Post\CreateOrEdit\Traits\RetrievePaymentTrait;
use App\Http\Requests\PhotoRequest;
use App\Models\Post;
use App\Models\Package;
use App\Models\PaymentMethod;
use App\Models\Picture;
use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Torann\LaravelMetaTags\Facades\MetaTag;

class PhotoController extends FrontController
{
	use PricingTrait, RetrievePaymentTrait;
	
	public $data;
	public $package = null;
	
	/**
	 * PhotoController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		// From Laravel 5.3.4 or above
		$this->middleware(function ($request, $next) {
			$this->commonQueries();
			
			return $next($request);
		});
		
		$this->middleware('only.ajax')->only('delete');
	}
	
	/**
	 * Common Queries
	 */
	public function commonQueries()
	{
		$data = [];
		
		// Selected Package
		$this->package = $this->getSelectedPackage();
		view()->share('selectedPackage', $this->package);
		
		// Set the Package's pictures limit
		$this->sharePackageInfo($this->package);
		
		// Count Packages
		$data['countPackages'] = Package::trans()->applyCurrency()->count();
		view()->share('countPackages', $data['countPackages']);
		
		// Count Payment Methods
		$data['countPaymentMethods'] = PaymentMethod::whereIn('name', array_keys((array)config('plugins.installed')))
			->where(function ($query) {
				$query->whereRaw('FIND_IN_SET("' . config('country.icode') . '", LOWER(countries)) > 0')
					->orWhereNull('countries');
			})->count();
		view()->share('countPaymentMethods', $data['countPaymentMethods']);
		
		// Save common's data
		$this->data = $data;
		
		// Keep the Post's creation message
		// session()->keep(['message']);
		if (request()->segment(2) == 'create') {
			if (session()->has('tmpPostId')) {
				session()->flash('message', t('your_ad_has_been_created'));
			}
		}
	}
	
	/**
	 * Show the form the create a new ad post.
	 *
	 * @param $postIdOrToken
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function getForm($postIdOrToken)
	{
		// Check if the 'Pricing Page' must be started first, and make redirection to it.
		$pricingUrl = $this->getPricingPage($this->package);
		if (!empty($pricingUrl)) {
			return redirect($pricingUrl)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		}
		
		// Check if the form type is 'Single Step Form', and make redirection to it (permanently).
		if (config('settings.single.publication_form_type') == '2') {
			$postEditionUrl = url('edit/' . $postIdOrToken);
			$postEditionUrl = qsUrl($postEditionUrl, request()->only(['package']), null, false);
			return redirect($postEditionUrl, 301)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		}
		
		$data = [];
		
		// Get Post
		if (request()->segment(2) == 'create') {
			if (!session()->has('tmpPostId')) {
				$postCreationUrl = url('posts/create');
				$postCreationUrl = qsUrl($postCreationUrl, request()->only(['package']), null, false);
				return redirect($postCreationUrl);
			}
			$post = Post::currentCountry()->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', session('tmpPostId'))
				->where('tmp_token', $postIdOrToken)
				->with(['pictures'])
				->first();
		} else {
			$post = Post::currentCountry()->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', auth()->user()->id)
				->where('id', $postIdOrToken)
				->with(['pictures'])
				->first();
		}
		
		if (empty($post)) {
			abort(404);
		}
		
		view()->share('post', $post);
		
		// Set the Package's pictures limit
		if (!empty($this->package)) {
			$this->sharePackageInfo($this->package);
		} else {
			// Share the Post's Latest Payment Info (If exists)
			// & Set the Package's pictures limit
			$this->sharePostLatestPaymentInfo($post);
		}
		
		// Get next step URI
		$creationPath = (request()->segment(2) == 'create') ? 'create/' : '';
		if (
			isset($this->data['countPackages']) &&
			isset($this->data['countPaymentMethods']) &&
			$this->data['countPackages'] > 0 &&
			$this->data['countPaymentMethods'] > 0
		) {
			$nextStepUrl = 'posts/' . $creationPath . $postIdOrToken . '/payment';
			$nextStepLabel = t('Next');
		} else {
			if (request()->segment(2) == 'create') {
				$nextStepUrl = 'posts/create/' . $postIdOrToken . '/finish';
			} else {
				$nextStepUrl = UrlGen::postUri($post);
			}
			$nextStepLabel = t('Finish');
		}
		view()->share('nextStepUrl', $nextStepUrl);
		view()->share('nextStepLabel', $nextStepLabel);
		
		
		// Meta Tags
		if (request()->segment(2) == 'create') {
			MetaTag::set('title', getMetaTag('title', 'create'));
			MetaTag::set('description', strip_tags(getMetaTag('description', 'create')));
			MetaTag::set('keywords', getMetaTag('keywords', 'create'));
		} else {
			MetaTag::set('title', t('update_my_ad'));
			MetaTag::set('description', t('update_my_ad'));
		}
		
		return appView('post.createOrEdit.multiSteps.photos', $data);
	}
	
	/**
	 * Store a new ad post.
	 *
	 * @param $postIdOrToken
	 * @param PhotoRequest $request
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postForm($postIdOrToken, PhotoRequest $request)
	{
		// Get Post
		if (request()->segment(2) == 'create') {
			if (!session()->has('tmpPostId')) {
				if ($request->ajax()) {
					return response()->json(['error' => t('Post not found')]);
				}
				
				return redirect('posts/create');
			}
			$post = Post::currentCountry()->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('id', session('tmpPostId'))
				->where('tmp_token', $postIdOrToken)->first();
		} else {
			$post = Post::currentCountry()->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->where('user_id', auth()->user()->id)
				->where('id', $postIdOrToken)
				->first();
		}
		
		if (empty($post)) {
			if ($request->ajax()) {
				return response()->json(['error' => t('Post not found')]);
			}
			abort(404);
		}
		
		// Set the Package's pictures limit
		if (!empty($this->package)) {
			$this->sharePackageInfo($this->package);
		} else {
			// Share the Post's Latest Payment Info (If exists)
			// & Set the Package's pictures limit
			$this->sharePostLatestPaymentInfo($post);
		}
		
		// Get pictures limit
		$countExistingPictures = Picture::where('post_id', $post->id)->count();
		$picturesLimit = (int)config('settings.single.pictures_limit', 5) - $countExistingPictures;
		
		// Get pictures initial position
		$latestPosition = Picture::where('post_id', $post->id)->orderBy('position', 'DESC')->first();
		$initialPosition = (!empty($latestPosition) && (int)$latestPosition->position > 0) ? (int)$latestPosition->position : 0;
		$initialPosition = ($countExistingPictures >= $initialPosition) ? $countExistingPictures : $initialPosition;
		
		// Save all pictures
		$pictures = [];
		$files = $request->file('pictures');
		if (count($files) > 0) {
			foreach ($files as $key => $file) {
				if (empty($file)) {
					continue;
				}
				
				// Delete old file if new file has uploaded
				// Check if current Post have a pictures
				$picturePosition = $initialPosition + (int)$key + 1;
				$picture = Picture::where('post_id', $post->id)->where('id', $key)->first();
				if (!empty($picture)) {
					$picturePosition = $picture->position;
					$picture->delete();
				}
				
				// Post Picture in database
				$picture = new Picture([
					'post_id'  => $post->id,
					'filename' => $file,
					'position' => $picturePosition,
				]);
				if (isset($picture->filename) && !empty($picture->filename)) {
					$picture->save();
				}
				
				$pictures[] = $picture;
				
				// Check the pictures limit
				if ($key >= ($picturesLimit - 1)) {
					break;
				}
			}
		}
		
		// Get next step URI
		$creationPath = (request()->segment(2) == 'create') ? 'create/' : '';
		if (
			isset($this->data['countPackages']) &&
			isset($this->data['countPaymentMethods']) &&
			$this->data['countPackages'] > 0 &&
			$this->data['countPaymentMethods'] > 0
		) {
			flash(t('The pictures have been updated'))->success();
			$nextStepUrl = 'posts/' . $creationPath . $postIdOrToken . '/payment';
			$nextStepLabel = t('Next');
		} else {
			if (request()->segment(2) == 'create') {
				$request->session()->flash('message', t('your_ad_has_been_created'));
				$nextStepUrl = 'posts/create/' . $postIdOrToken . '/finish';
			} else {
				flash(t('The pictures have been updated'))->success();
				$nextStepUrl = UrlGen::postUri($post);
			}
			$nextStepLabel = t('Done');
		}
		
		view()->share('nextStepUrl', $nextStepUrl);
		view()->share('nextStepLabel', $nextStepLabel);
		
		
		// Ajax response
		if ($request->ajax()) {
			$data = [];
			$data['initialPreview'] = [];
			$data['initialPreviewConfig'] = [];
			
			$pictures = collect($pictures);
			if ($pictures->count() > 0) {
				foreach ($pictures as $picture) {
					// Get Deletion Url
					if (request()->segment(2) == 'create') {
						$initialPreviewConfigUrl = url('posts/create/' . $post->tmp_token . '/photos/' . $picture->id . '/delete');
					} else {
						$initialPreviewConfigUrl = url('posts/' . $post->id . '/photos/' . $picture->id . '/delete');
					}
					
					// Build Bootstrap-Input plugin's parameters
					$data['initialPreview'][] = imgUrl($picture->filename, 'medium');
					$data['initialPreviewConfig'][] = [
						'caption' => last(explode(DIRECTORY_SEPARATOR, $picture->filename)),
						'size'    => (isset($this->disk) && $this->disk->exists($picture->filename)) ? (int)$this->disk->size($picture->filename) : 0,
						'url'     => $initialPreviewConfigUrl,
						'key'     => $picture->id,
						'extra'   => ['id' => $picture->id],
					];
				}
			}
			
			return response()->json($data);
		}
		
		// Non ajax response
		return redirect($nextStepUrl);
	}
	
	/**
	 * Delete picture
	 *
	 * @param $postIdOrToken
	 * @param $pictureId
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function delete($postIdOrToken, $pictureId, Request $request)
	{
		$inputs = $request->all();
		
		// Get Post
		if (request()->segment(2) == 'create') {
			if (!session()->has('tmpPostId')) {
				if ($request->ajax()) {
					return response()->json(['error' => t('Post not found')]);
				}
				
				return redirect('posts/create');
			}
			$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->where('id', session('tmpPostId'))->where('tmp_token', $postIdOrToken)->first();
		} else {
			$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->where('user_id', auth()->user()->id)->where('id', $postIdOrToken)->first();
		}
		
		if (empty($post)) {
			if ($request->ajax()) {
				return response()->json(['error' => t('Post not found')]);
			}
			abort(404);
		}
		
		$picture = Picture::withoutGlobalScopes([ActiveScope::class])->find($pictureId);
		if (!empty($picture)) {
			$nb = $picture->delete();
		}
		
		if ($request->ajax()) {
			return response()->json([]);
		}
		
		flash(t("The picture has been deleted"))->success();
		
		return back();
	}
}
