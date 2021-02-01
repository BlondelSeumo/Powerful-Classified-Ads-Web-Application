<?php
if (!isset($cacheExpiration)) {
    $cacheExpiration = (int)config('settings.optimization.cache_expiration');
}
?>
@if (isset($posts) and $posts->total() > 0)
	<?php
	if (!isset($cats)) {
		$cats = collect([]);
	}

	foreach($posts->items() as $key => $post):
		if (empty($countries) or !$countries->has($post->country_code)) continue;
		if (empty($post->city)) continue;
		
		// Main Picture
		if ($post->pictures->count() > 0) {
			$postImg = imgUrl($post->pictures->get(0)->filename, 'medium');
		} else {
			$postImg = imgUrl(config('larapen.core.picture.default'), 'medium');
		}
	?>
	<div class="item-list">
        @if (isset($post->latestPayment, $post->latestPayment->package) and !empty($post->latestPayment->package))
            @if ($post->latestPayment->package->ribbon != '')
                <div class="cornerRibbons {{ $post->latestPayment->package->ribbon }}">
					<a href="#"> {{ $post->latestPayment->package->short_name }}</a>
				</div>
            @endif
        @endif
		
		<div class="row">
			<div class="col-sm-2 col-12 no-padding photobox">
				<div class="add-image">
					<span class="photo-count"><i class="fa fa-camera"></i> {{ $post->pictures->count() }} </span>
					<a href="{{ \App\Helpers\UrlGen::post($post) }}">
						<img class="lazyload img-thumbnail no-margin" src="{{ $postImg }}" alt="{{ $post->title }}">
					</a>
				</div>
			</div>
	
			<div class="col-sm-7 col-12 add-desc-box">
				<div class="items-details">
					<h5 class="add-title">
						<a href="{{ \App\Helpers\UrlGen::post($post) }}">{{ \Illuminate\Support\Str::limit($post->title, 70) }} </a>
					</h5>
					
					<span class="info-row">
						@if (isset($post->postType) and !empty($post->postType))
							<span class="add-type business-ads tooltipHere"
								  data-toggle="tooltip"
								  data-placement="bottom"
								  title="{{ $post->postType->name }}"
							>
								{{ strtoupper(mb_substr($post->postType->name, 0, 1)) }}
							</span>&nbsp;
						@endif
						@if (!config('settings.listing.hide_dates'))
							<span class="date"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
								<i class="icon-clock"></i> {!! $post->created_at_formatted !!}
							</span>
						@endif
						<span class="category"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
							<i class="icon-folder-circled"></i>&nbsp;
							@if (isset($post->category->parent) and !empty($post->category->parent))
								<a href="{!! \App\Helpers\UrlGen::search(
											array_merge(
												request()->except(['page', 'c']),
												['c' => $post->category->parent->tid]
											)
										) !!}"
								   class="info-link"
								>{{ $post->category->parent->name }}</a>&nbsp;&raquo;&nbsp;
							@endif
							<a href="{!! \App\Helpers\UrlGen::search(
										array_merge(
											request()->except(['page', 'c']),
											['c' => $post->category->tid]
										)
									) !!}"
							   class="info-link"
							>{{ $post->category->name }}</a>
						</span>
						<?php
							$locationParams = [];
							if (isset($cat) and !empty($cat)) {
								if (isset($cat->parent) and !empty($cat->parent)) {
									$locationParams = [
										'l'  => $post->city_id,
										'r'  => '',
										'c'  => $cat->parent->tid,
										'sc' => $cat->tid,
									];
								} else {
									$locationParams = [
										'l'  => $post->city_id,
										'r'  => '',
										'c'  => $cat->tid,
									];
								}
							} else {
								$locationParams = [
									'l'  => $post->city_id,
									'r'  => '',
								];
							}
						?>
						<span class="item-location"{!! (config('lang.direction')=='rtl') ? ' dir="rtl"' : '' !!}>
							<i class="icon-location-2"></i>&nbsp;
							<a href="{!! \App\Helpers\UrlGen::search(
										array_merge(
											request()->except(['page'] + array_keys($locationParams)),
											$locationParams
										)
									) !!}" class="info-link">
								{{ $post->city->name }}
							</a> {{ (isset($post->distance)) ? '- ' . round($post->distance, 2) . getDistanceUnit() : '' }}
						</span>
					</span>
				</div>
	
				@if (config('plugins.reviews.installed'))
					@if (view()->exists('reviews::ratings-list'))
						@include('reviews::ratings-list')
					@endif
				@endif
				
			</div>
			
			<div class="col-sm-3 col-12 text-right price-box" style="white-space: nowrap;">
				<h4 class="item-price">
					@if (isset($post->category->type))
						@if (!in_array($post->category->type, ['not-salable']))
							@if ($post->price > 0)
								{!! \App\Helpers\Number::money($post->price) !!}
							@else
								{!! \App\Helpers\Number::money(' --') !!}
							@endif
						@endif
					@else
						{{ '--' }}
					@endif
				</h4>&nbsp;
				@if (isset($post->latestPayment, $post->latestPayment->package) and !empty($post->latestPayment->package))
					@if ($post->latestPayment->package->has_badge == 1)
						<a class="btn btn-danger btn-sm make-favorite">
							<i class="fa fa-certificate"></i>
							<span> {{ $post->latestPayment->package->short_name }} </span>
						</a>&nbsp;
					@endif
				@endif
				@if (isset($post->savedByLoggedUser) and $post->savedByLoggedUser->count() > 0)
					<a class="btn btn-success btn-sm make-favorite" id="{{ $post->id }}">
						<i class="fa fa-heart"></i><span> {{ t('Saved') }} </span>
					</a>
				@else
					<a class="btn btn-default btn-sm make-favorite" id="{{ $post->id }}">
						<i class="fa fa-heart"></i><span> {{ t('Save') }} </span>
					</a>
				@endif
			</div>
		</div>
	</div>
	<?php endforeach; ?>
@else
	<div class="p-4" style="width: 100%;">
		{{ t('no_result_refine_your_search') }}
	</div>
@endif

@section('after_scripts')
	@parent
	<script>
		/* Default view (See in /js/script.js) */
		@if ($count->get('all') > 0)
			@if (config('settings.listing.display_mode') == '.grid-view')
				gridView('.grid-view');
			@elseif (config('settings.listing.display_mode') == '.list-view')
				listView('.list-view');
			@elseif (config('settings.listing.display_mode') == '.compact-view')
				compactView('.compact-view');
			@else
				gridView('.grid-view');
			@endif
		@else
			listView('.list-view');
		@endif
		/* Save the Search page display mode */
		var listingDisplayMode = readCookie('listing_display_mode');
		if (!listingDisplayMode) {
			createCookie('listing_display_mode', '{{ config('settings.listing.display_mode', '.grid-view') }}', 7);
		}
		
		/* Favorites Translation */
		var lang = {
			labelSavePostSave: "{!! t('Save ad') !!}",
			labelSavePostRemove: "{!! t('Remove favorite') !!}",
			loginToSavePost: "{!! t('Please log in to save the Ads') !!}",
			loginToSaveSearch: "{!! t('Please log in to save your search') !!}",
			confirmationSavePost: "{!! t('Post saved in favorites successfully') !!}",
			confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully') !!}",
			confirmationSaveSearch: "{!! t('Search saved successfully') !!}",
			confirmationRemoveSaveSearch: "{!! t('Search deleted successfully') !!}"
		};
	</script>
@endsection
