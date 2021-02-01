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

namespace App\Http\Controllers\Post;

use App\Events\PostWasVisited;
use App\Helpers\ArrayHelper;
use App\Helpers\Date;
use App\Helpers\UrlGen;
use App\Http\Controllers\Post\Traits\CatBreadcrumbTrait;
use App\Http\Controllers\Post\Traits\CustomFieldTrait;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Package;
use App\Http\Controllers\FrontController;
use App\Models\User;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;

class DetailsController extends FrontController
{
	use CatBreadcrumbTrait, CustomFieldTrait;
	
	/**
	 * Post expire time (in months)
	 *
	 * @var int
	 */
	public $expireTime = 24;
	
	/**
	 * DetailsController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		// From Laravel 5.3.4 or above
		$this->middleware(function ($request, $next) {
			$this->commonQueries();
			
			return $next($request);
		});
	}
	
	/**
	 * Common Queries
	 */
	public function commonQueries()
	{
		// Check Country URL for SEO
		$countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
		view()->share('countries', $countries);
		
		// Count Packages
		$countPackages = Package::trans()->applyCurrency()->count();
		view()->share('countPackages', $countPackages);
		
		// Count Payment Methods
		view()->share('countPaymentMethods', $this->countPaymentMethods);
	}
	
	/**
	 * Show Dost's Details.
	 *
	 * @param $postId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index($postId)
	{
		$data = [];
		
		// Get and Check the Controller's Method Parameters
		$parameters = request()->route()->parameters();
		
		// Show 404 error if the Post's ID is not numeric
		if (!isset($parameters['id']) || empty($parameters['id']) || !is_numeric($parameters['id'])) {
			abort(404);
		}
		
		// Set the Parameters
		$postId = $parameters['id'];
		if (isset($parameters['slug'])) {
			$slug = $parameters['slug'];
		}
		
		// GET POST'S DETAILS
		if (auth()->check()) {
			// Get post's details even if it's not activated and reviewed
			$cacheId = 'post.withoutGlobalScopes.with.city.pictures.' . $postId . '.' . config('app.locale');
			$post    = Cache::remember($cacheId, $this->cacheExpiration, function () use ($postId) {
				return Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
					->withCountryFix()
					->unarchived()
					->where('id', $postId)
					->with([
						'category'      => function ($builder) { $builder->with(['parent']); },
						'postType',
						'city',
						'pictures',
						'latestPayment' => function ($builder) { $builder->with(['package']); },
					])
					->first();
			});
			
			// If the logged user is not an admin user...
			if (!auth()->user()->can(Permission::getStaffPermissions())) {
				// Then don't get post that are not from the user
				if (!empty($post) && $post->user_id != auth()->user()->id) {
					$cacheId = 'post.with.city.pictures.' . $postId . '.' . config('app.locale');
					$post    = Cache::remember($cacheId, $this->cacheExpiration, function () use ($postId) {
						return Post::withCountryFix()
							->unarchived()
							->where('id', $postId)
							->with([
								'category'      => function ($builder) { $builder->with(['parent']); },
								'postType',
								'city',
								'pictures',
								'latestPayment' => function ($builder) { $builder->with(['package']); },
							])
							->first();
					});
				}
			}
		} else {
			$cacheId = 'post.with.city.pictures.' . $postId . '.' . config('app.locale');
			$post    = Cache::remember($cacheId, $this->cacheExpiration, function () use ($postId) {
				return Post::withCountryFix()
					->unarchived()
					->where('id', $postId)
					->with([
						'category'      => function ($builder) { $builder->with(['parent']); },
						'postType',
						'city',
						'pictures',
						'latestPayment' => function ($builder) { $builder->with(['package']); },
					])
					->first();
			});
		}
		// Preview Post after activation
		if (request()->filled('preview') && request()->get('preview') == 1) {
			// Get post's details even if it's not activated and reviewed
			$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->withCountryFix()
				->where('id', $postId)
				->with([
					'category'      => function ($builder) { $builder->with(['parent']); },
					'postType',
					'city',
					'pictures',
					'latestPayment' => function ($builder) { $builder->with(['package']); },
				])
				->first();
		}
		
		// Post not found
		if (empty($post) || empty($post->category) || empty($post->city)) {
			abort(404, t('Post not found'));
		}
		
		// Share post's details
		view()->share('post', $post);
		
		// Get possible post's registered Author (User)
		$user = null;
		if (isset($post->user_id) && !empty($post->user_id)) {
			$user = User::find($post->user_id);
		}
		view()->share('user', $user);
		
		// Get Post's user decision about comments activation
		$commentsAreDisabledByUser = false;
		if (isset($user) && !empty($user)) {
			if ($user->disable_comments == 1) {
				$commentsAreDisabledByUser = true;
			}
		}
		view()->share('commentsAreDisabledByUser', $commentsAreDisabledByUser);
		
		// Category Breadcrumb
		$catBreadcrumb = $this->getCatBreadcrumb($post->category, 1);
		view()->share('catBreadcrumb', $catBreadcrumb);
		
		// Get Custom Fields
		$customFields = $this->getPostFieldsValues($post->category->tid, $post->id);
		view()->share('customFields', $customFields);
		
		// Increment Post visits counter
		Event::dispatch(new PostWasVisited($post));
		
		// GET SIMILAR POSTS
		if (config('settings.single.similar_posts') == '1') {
			$cacheId = 'posts.similar.category.' . $post->category->tid . '.post.' . $post->id;
			$posts   = Cache::remember($cacheId, $this->cacheExpiration, function () use ($post) {
				return $post->getSimilarByCategory();
			});
			
			// Featured Area Data
			$featured         = [
				'title' => t('Similar Ads'),
				'link'  => UrlGen::search(array_merge(request()->except('c'), ['c' => $post->category->tid])),
				'posts' => $posts,
			];
			$data['featured'] = (count($posts) > 0) ? ArrayHelper::toObject($featured) : null;
		} else if (config('settings.single.similar_posts') == '2') {
			$distance = 50; // km OR miles
			
			$cacheId = 'posts.similar.city.' . $post->city->id . '.post.' . $post->id;
			$posts   = Cache::remember($cacheId, $this->cacheExpiration, function () use ($post, $distance) {
				return $post->getSimilarByLocation($distance);
			});
			
			// Featured Area Data
			$featured         = [
				'title' => t('more_ads_at_x_distance_around_city', [
					'distance' => $distance,
					'unit'     => getDistanceUnit(config('country.code')),
					'city'     => $post->city->name,
				]),
				'link'  => UrlGen::search(array_merge(request()->except(['l', 'location']), ['l' => $post->city->id])),
				'posts' => $posts,
			];
			$data['featured'] = (count($posts) > 0) ? ArrayHelper::toObject($featured) : null;
		}
		
		// SEO
		$title       = $post->title . ', ' . $post->city->name;
		$description = Str::limit(str_strip(strip_tags($post->description)), 200);
		
		// Meta Tags
		MetaTag::set('title', $title);
		MetaTag::set('description', $description);
		if (!empty($post->tags)) {
			MetaTag::set('keywords', str_replace(',', ', ', $post->tags));
		}
		
		// Open Graph
		$this->og->title($title)
			->description($description)
			->type('article');
		if (!$post->pictures->isEmpty()) {
			if ($this->og->has('image')) {
				$this->og->forget('image')->forget('image:width')->forget('image:height');
			}
			foreach ($post->pictures as $picture) {
				$this->og->image(imgUrl($picture->filename, 'big'), [
					'width'  => 600,
					'height' => 600,
				]);
			}
		}
		view()->share('og', $this->og);
		
		/*
		// Expiration Info
		$today = Carbon::now(Date::getAppTimeZone());
		if ($today->gt($post->created_at->addMonths($this->expireTime))) {
			flash(t("this_ad_is_expired"))->error();
		}
		*/
		
		// Reviews Plugin Data
		if (config('plugins.reviews.installed')) {
			try {
				$rvPost = \extras\plugins\reviews\app\Models\Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($post->id);
				view()->share('rvPost', $rvPost);
			} catch (\Exception $e) {
			}
		}
		
		// View
		return appView('post.details', $data);
	}
}
