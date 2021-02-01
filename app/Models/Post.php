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

namespace App\Models;

use App\Helpers\Date;
use App\Helpers\RemoveFromString;
use App\Helpers\UrlGen;
use App\Models\Post\LatestOrPremium;
use App\Models\Post\SimilarByCategory;
use App\Models\Post\SimilarByLocation;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use App\Models\Traits\CountryTrait;
use App\Observers\PostObserver;
use Creativeorange\Gravatar\Facades\Gravatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Larapen\Admin\app\Models\Crud;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Post extends BaseModel implements Feedable
{
	use Crud, CountryTrait, Notifiable, HasFactory, LatestOrPremium, SimilarByCategory, SimilarByLocation;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
	protected $appends = ['created_at_formatted', 'user_photo_url'];
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	public $timestamps = true;
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'country_code',
		'user_id',
		'category_id',
		'post_type_id',
		'title',
		'description',
		'tags',
		'price',
		'negotiable',
		'contact_name',
		'email',
		'phone',
		'phone_hidden',
		'address',
		'city_id',
		'lat',
		'lon',
		'ip_addr',
		'visits',
		'tmp_token',
		'email_token',
		'phone_token',
		'verified_email',
		'verified_phone',
		'accept_terms',
		'accept_marketing_offers',
		'is_permanent',
		'reviewed',
		'featured',
		'archived',
		'archived_at',
		'deletion_mail_sent_at',
		'fb_profile',
		'partner',
		'created_at',
	];
	
	/**
	 * The attributes that should be hidden for arrays
	 *
	 * @var array
	 */
	// protected $hidden = [];
	
	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		Post::observe(PostObserver::class);
		
		static::addGlobalScope(new VerifiedScope());
		static::addGlobalScope(new ReviewedScope());
		static::addGlobalScope(new LocalizedScope());
	}
	
	public function routeNotificationForMail()
	{
		return $this->email;
	}
	
	public function routeNotificationForNexmo()
	{
		$phone = phoneFormatInt($this->phone, $this->country_code);
		$phone = setPhoneSign($phone, 'nexmo');
		
		return $phone;
	}
	
	public function routeNotificationForTwilio()
	{
		$phone = phoneFormatInt($this->phone, $this->country_code);
		$phone = setPhoneSign($phone, 'twilio');
		
		return $phone;
	}
	
	public static function getFeedItems()
	{
		$postsPerPage = (int)config('settings.listing.items_per_page', 50);
		
		$posts = Post::reviewed()->unarchived();
		
		if (request()->has('d') || config('plugins.domainmapping.installed')) {
			$countryCode = config('country.code');
			if (!config('plugins.domainmapping.installed')) {
				if (request()->has('d')) {
					$countryCode = request()->input('d');
				}
			}
			$posts = $posts->where('country_code', $countryCode);
		}
		
		$posts = $posts->take($postsPerPage)->orderByDesc('id')->get();
		
		return $posts;
	}
	
	public function toFeedItem()
	{
		$title = $this->title;
		$title .= (isset($this->city) && !empty($this->city)) ? ' - ' . $this->city->name : '';
		$title .= (isset($this->country) && !empty($this->country)) ? ', ' . $this->country->name : '';
		// $summary = str_limit(str_strip(strip_tags($this->description)), 5000);
		$summary = transformDescription($this->description);
		$link = UrlGen::postUri($this, true);
		
		return FeedItem::create()
			->id($link)
			->title($title)
			->summary($summary)
			->category((!empty($this->category)) ? $this->category->name : '')
			->updated($this->updated_at)
			->link($link)
			->author($this->contact_name);
	}
	
	public function getTitleHtml()
	{
		$out = '';
		
		$post = self::find($this->id);
		$out .= getPostUrl($post);
		$out .= '<br>';
		$out .= '<small>';
		$out .= $this->pictures->count() . ' ' . trans('admin.pictures');
		$out .= '</small>';
		
		return $out;
	}
	
	public function getPictureHtml()
	{
		// Get ad URL
		$url = url(UrlGen::postUri($this));
		
		$style = ' style="width:auto; max-height:90px;"';
		// Get first picture
		if ($this->pictures->count() > 0) {
			foreach ($this->pictures as $picture) {
				$url = dmUrl($picture->post->country_code, UrlGen::postPath($this));
				$out = '<img src="' . imgUrl($picture->filename, 'small') . '" data-toggle="tooltip" title="' . $this->title . '"' . $style . ' class="img-rounded">';
				break;
			}
		} else {
			// Default picture
			$out = '<img src="' . imgUrl(config('larapen.core.picture.default'), 'small') . '" data-toggle="tooltip" title="' . $this->title . '"' . $style . ' class="img-rounded">';
		}
		
		// Add link to the Ad
		$out = '<a href="' . $url . '" target="_blank">' . $out . '</a>';
		
		return $out;
	}
	
	public function getUserNameHtml()
	{
		if (isset($this->user) and !empty($this->user)) {
			$url = admin_url('users/' . $this->user->getKey() . '/edit');
			$tooltip = ' data-toggle="tooltip" title="' . $this->user->name . '"';
			
			return '<a href="' . $url . '"' . $tooltip . '>' . $this->contact_name . '</a>';
		} else {
			return $this->contact_name;
		}
	}
	
	public function getCityHtml()
	{
		$out = $this->getCountryHtml();
		$out .= ' - ';
		if (isset($this->city) and !empty($this->city)) {
			$out .= '<a href="' . UrlGen::city($this->city) . '" target="_blank">' . $this->city->name . '</a>';
		} else {
			$out .= $this->city_id;
		}
		
		return $out;
	}
	
	public function getReviewedHtml()
	{
		return ajaxCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'reviewed', $this->reviewed);
	}
	
	public function getFeaturedHtml()
	{
		$out = '-';
		if (config('plugins.offlinepayment.installed')) {
			$opTool = '\extras\plugins\offlinepayment\app\Helpers\OpTools';
			if (class_exists($opTool)) {
				$out = $opTool::featuredCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'featured', $this->featured);
			}
		}
		
		return $out;
	}
	
	/*
	|--------------------------------------------------------------------------
	| QUERIES
	|--------------------------------------------------------------------------
	*/
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function postType()
	{
		return $this->belongsTo(PostType::class, 'post_type_id', 'translation_of')->where('translation_lang', config('app.locale'));
	}
	
	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id', 'translation_of')->where('translation_lang', config('app.locale'));
	}
	
	public function city()
	{
		return $this->belongsTo(City::class, 'city_id');
	}
	
	public function latestPayment()
	{
		return $this->hasOne(Payment::class, 'post_id')->orderBy('id', 'DESC');
	}
	
	public function payments()
	{
		return $this->hasMany(Payment::class, 'post_id');
	}
	
	public function pictures()
	{
		return $this->hasMany(Picture::class, 'post_id')->orderBy('position')->orderBy('id', 'DESC');
	}
	
	public function savedByLoggedUser()
	{
		$userId = (auth()->check()) ? auth()->user()->id : '-1';
		
		return $this->hasMany(SavedPost::class, 'post_id')->where('user_id', $userId);
	}
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	public function postValues()
	{
		return $this->hasMany(PostValue::class, 'post_id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeVerified($builder)
	{
		$builder->where(function ($query) {
			$query->where('verified_email', 1)->where('verified_phone', 1);
		});
		
		if (config('settings.single.posts_review_activation')) {
			$builder->where('reviewed', 1);
		}
		
		return $builder;
	}
	
	public function scopeUnverified($builder)
	{
		$builder->where(function ($query) {
			$query->where('verified_email', 0)->orWhere('verified_phone', 0);
		});
		
		if (config('settings.single.posts_review_activation')) {
			$builder->orWhere('reviewed', 0);
		}
		
		return $builder;
	}
	
	public function scopeArchived($builder)
	{
		return $builder->where('archived', 1);
	}
	
	public function scopeUnarchived($builder)
	{
		return $builder->where('archived', 0);
	}
	
	public function scopeReviewed($builder)
	{
		if (config('settings.single.posts_review_activation')) {
			return $builder->where('reviewed', 1);
		} else {
			return $builder;
		}
	}
	
	public function scopeUnreviewed($builder)
	{
		if (config('settings.single.posts_review_activation')) {
			return $builder->where('reviewed', 0);
		} else {
			return $builder;
		}
	}
	
	public function scopeWithCountryFix($builder)
	{
		// Check the Domain Mapping Plugin
		if (config('plugins.domainmapping.installed')) {
			return $builder->where('country_code', config('country.code'));
		} else {
			return $builder;
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESSORS
	|--------------------------------------------------------------------------
	*/
	public function getCreatedAtAttribute($value)
	{
		$value = new Carbon($value);
		$value->timezone(Date::getAppTimeZone());
		
		return $value;
	}
	
	public function getUpdatedAtAttribute($value)
	{
		$value = new Carbon($value);
		$value->timezone(Date::getAppTimeZone());
		
		return $value;
	}
	
	public function getDeletedAtAttribute($value)
	{
		$value = new Carbon($value);
		$value->timezone(Date::getAppTimeZone());
		
		return $value;
	}
	
	public function getCreatedAtFormattedAttribute($value)
	{
		$value = new Carbon($this->attributes['created_at']);
		$value->timezone(Date::getAppTimeZone());
		
		$value = Date::formatFormNow($value);
		
		return $value;
	}
	
	public function getArchivedAtAttribute($value)
	{
		$value = (is_null($value)) ? $this->updated_at : $value;
		
		$value = new Carbon($value);
		$value->timezone(Date::getAppTimeZone());
		
		return $value;
	}
	
	public function getDeletionMailSentAtAttribute($value)
	{
		$value = (is_null($value)) ? $this->updated_at : $value;
		
		$value = new Carbon($value);
		$value->timezone(Date::getAppTimeZone());
		
		return $value;
	}
	
	public function getUserPhotoUrlAttribute($value)
	{
		// Default Photo
		$defaultPhotoUrl = url('images/user.jpg');
		
		// Photo from Gravatar
		$gravatarUrl = null;
		try {
			$gravatarUrl = (!empty($this->email)) ? Gravatar::fallback($defaultPhotoUrl)->get($this->email) : null;
		} catch (\Exception $e) {}
		if (
			Str::contains(urldecode($gravatarUrl), $defaultPhotoUrl)
			|| Str::contains($gravatarUrl, $defaultPhotoUrl)
		) {
			$gravatarUrl = $defaultPhotoUrl;
		}
		$gravatarUrl = empty($gravatarUrl) ? $defaultPhotoUrl : $gravatarUrl;
		
		// Photo from User's account
		$userPhotoUrl = null;
		if (!empty($this->user) && isset($this->user->photo_url) && !empty($this->user->photo_url)) {
			$userPhotoUrl = $this->user->photo_url;
		}
		
		$value = (!empty($userPhotoUrl) && $userPhotoUrl != $defaultPhotoUrl) ? $userPhotoUrl : $gravatarUrl;
		
		return $value;
	}
	
	public function getEmailAttribute($value)
	{
		if (isFromAdminPanel() || (!isFromAdminPanel() && in_array(request()->method(), ['GET']))) {
			if (
				isDemo() &&
				request()->segment(2) != 'password'
			) {
				if (auth()->check()) {
					if (auth()->user()->id != 1) {
						if (isset($this->phone_token)) {
							if ($this->phone_token == 'demoFaker') {
								return $value;
							}
						}
						$value = hidePartOfEmail($value);
					}
				}
			}
		}
		
		return $value;
	}
	
	public function getPhoneAttribute($value)
	{
		$countryCode = config('country.code');
		if (isset($this->country_code) && !empty($this->country_code)) {
			$countryCode = $this->country_code;
		}
		
		$value = phoneFormatInt($value, $countryCode);
		
		return $value;
	}
	
	public function getTitleAttribute($value)
	{
		$value = mb_ucfirst($value);
		
		if (!isFromAdminPanel()) {
			if (!empty($this->user)) {
				if (!$this->user->hasAllPermissions(Permission::getStaffPermissions())) {
					$value = RemoveFromString::contactInfo($value, false, true);
				}
			} else {
				$value = RemoveFromString::contactInfo($value, false, true);
			}
		}
		
		return $value;
	}
	
	public function getContactNameAttribute($value)
	{
		$value = mb_ucwords($value);
		
		return $value;
	}
	
	public function getDescriptionAttribute($value)
	{
		if (!isFromAdminPanel()) {
			if (!empty($this->user)) {
				if (!$this->user->hasAllPermissions(Permission::getStaffPermissions())) {
					$value = RemoveFromString::contactInfo($value, false, true);
				}
			} else {
				$value = RemoveFromString::contactInfo($value, false, true);
			}
		}
		
		return $value;
	}
	
	/*
	|--------------------------------------------------------------------------
	| MUTATORS
	|--------------------------------------------------------------------------
	*/
	public function setTagsAttribute($value)
	{
		$this->attributes['tags'] = (!empty($value)) ? mb_strtolower($value) : $value;
	}
}
