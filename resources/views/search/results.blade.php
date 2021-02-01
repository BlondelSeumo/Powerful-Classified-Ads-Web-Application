{{--
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
--}}
@extends('layouts.master')

@section('search')
	@parent
	@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.form', 'search.inc.form'])
@endsection

@section('content')
	<div class="main-container">
		
		@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.breadcrumbs', 'search.inc.breadcrumbs'])
		@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.categories', 'search.inc.categories'])
		<?php if (\App\Models\Advertising::where('slug', 'top')->count() > 0): ?>
			@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.advertising.top', 'layouts.inc.advertising.top'], ['paddingTopExists' => true])
		<?php
			$paddingTopExists = false;
		else:
			if (isset($paddingTopExists) and $paddingTopExists) {
				$paddingTopExists = false;
			}
		endif;
		?>
		@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
		
		<div class="container">
			<div class="row">

				<!-- Sidebar -->
                @if (config('settings.listing.left_sidebar'))
                    @includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.sidebar', 'search.inc.sidebar'])
                    <?php $contentColSm = 'col-md-9'; ?>
                @else
                    <?php $contentColSm = 'col-md-12'; ?>
                @endif

				<!-- Content -->
				<div class="{{ $contentColSm }} page-content col-thin-left">
					<div class="category-list{{ ($contentColSm == 'col-md-12') ? ' noSideBar' : '' }}">
						<div class="tab-box">

							<!-- Nav tabs -->
							<ul id="postType" class="nav nav-tabs add-tabs tablist" role="tablist">
                                <?php
                                $liClass = 'class="nav-item"';
                                $spanClass = 'alert-danger';
								if (config('settings.single.show_post_types')) {
									if (!request()->filled('type') or request()->get('type') == '') {
										$liClass = 'class="nav-item active"';
										$spanClass = 'badge-danger';
									}
                                } else {
									$liClass = 'class="nav-item active"';
									$spanClass = 'badge-danger';
								}
                                ?>
								<li {!! $liClass !!}>
									<a href="{!! qsUrl(request()->url(), request()->except(['page', 'type']), null, false) !!}"
									   role="tab"
									   data-toggle="tab"
									   class="nav-link"
									>
										{{ t('All Ads') }} <span class="badge badge-pill {!! $spanClass !!}">{{ $count->get('all') }}</span>
									</a>
								</li>
								@if (config('settings.single.show_post_types'))
									@if (isset($postTypes) and $postTypes->count() > 0)
										@foreach ($postTypes as $postType)
											<?php
												$postTypeUrl = qsUrl(
													request()->url(),
													array_merge(request()->except(['page']), ['type' => $postType->tid]),
													null,
													false
												);
												$postTypeCount = ($count->has($postType->tid)) ? $count->get($postType->tid) : 0;
											?>
											@if (request()->filled('type') && request()->get('type') == $postType->tid)
												<li class="nav-item active">
													<a href="{!! $postTypeUrl !!}" role="tab" data-toggle="tab" class="nav-link">
														{{ $postType->name }}
														<span class="badge badge-pill badge-danger">
															{{ $postTypeCount }}
														</span>
													</a>
												</li>
											@else
												<li class="nav-item">
													<a href="{!! $postTypeUrl !!}" role="tab" data-toggle="tab" class="nav-link">
														{{ $postType->name }}
														<span class="badge badge-pill alert-danger">
															{{ $postTypeCount }}
														</span>
													</a>
												</li>
											@endif
										@endforeach
									@endif
								@endif
							</ul>
							
							<div class="tab-filter">
								<select id="orderBy" title="sort by" class="niceselecter select-sort-by" data-style="btn-select" data-width="auto">
									<option value="{!! qsUrl(request()->url(), request()->except(['orderBy', 'distance']), null, false) !!}">{{ t('Sort by') }}</option>
									<option{{ (request()->get('orderBy')=='priceAsc') ? ' selected="selected"' : '' }}
											value="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'priceAsc']), null, false) !!}">
										{{ t('price_low_to_high') }}
									</option>
									<option{{ (request()->get('orderBy')=='priceDesc') ? ' selected="selected"' : '' }}
											value="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'priceDesc']), null, false) !!}">
										{{ t('price_high_to_low') }}
									</option>
									@if (request()->filled('q'))
										<option{{ (request()->get('orderBy')=='relevance') ? ' selected="selected"' : '' }}
												value="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'relevance']), null, false) !!}">
											{{ t('Relevance') }}
										</option>
									@endif
									<option{{ (request()->get('orderBy')=='date') ? ' selected="selected"' : '' }}
											value="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'date']), null, false) !!}">
										{{ t('Date') }}
									</option>
									@if (isset($city, $distanceRange) and !empty($city) and !empty($distanceRange))
										@foreach($distanceRange as $key => $value)
											<option{{ (request()->get('distance', config('settings.listing.search_distance_default', 100))==$value) ? ' selected="selected"' : '' }}
													value="{!! qsUrl(request()->url(), array_merge(request()->except('distance'), ['distance' => $value]), null, false) !!}">
												{{ t('around_x_distance', ['distance' => $value, 'unit' => getDistanceUnit()]) }}
											</option>
										@endforeach
									@endif
									@if (config('plugins.reviews.installed'))
										<option{{ (request()->get('orderBy')=='rating') ? ' selected="selected"' : '' }}
												value="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'rating']), null, false) !!}">
										{{ trans('reviews::messages.Rating') }}
										</option>
									@endif
								</select>
							</div>

						</div>

						<div class="listing-filter">
							<div class="pull-left col-xs-6">
								<div class="breadcrumb-list">
									{!! (isset($htmlTitle)) ? $htmlTitle : '' !!}
								</div>
                                <div style="clear:both;"></div>
							</div>
                            
							@if (isset($posts) and $posts->count() > 0)
								<div class="pull-right col-xs-6 text-right listing-view-action">
									<span class="list-view"><i class="icon-th"></i></span>
									<span class="compact-view"><i class="icon-th-list"></i></span>
									<span class="grid-view active"><i class="icon-th-large"></i></span>
								</div>
							@endif

							<div style="clear:both"></div>
						</div>
						
						<!-- Mobile Filter Bar -->
						<div class="mobile-filter-bar col-xl-12">
							<ul class="list-unstyled list-inline no-margin no-padding">
								@if (config('settings.listing.left_sidebar'))
								<li class="filter-toggle">
									<a class="">
										<i class="icon-th-list"></i> {{ t('Filters') }}
									</a>
								</li>
								@endif
								<li>
									<div class="dropdown">
										<a data-toggle="dropdown" class="dropdown-toggle">{{ t('Sort by') }}</a>
										<ul class="dropdown-menu">
											<li>
												<a href="{!! qsUrl(request()->url(), request()->except(['orderBy', 'distance']), null, false) !!}" rel="nofollow">
													{{ t('Sort by') }}
												</a>
											</li>
											<li>
												<a href="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'priceAsc']), null, false) !!}" rel="nofollow">
													{{ t('price_low_to_high') }}
												</a>
											</li>
											<li>
												<a href="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'priceDesc']), null, false) !!}" rel="nofollow">
													{{ t('price_high_to_low') }}
												</a>
											</li>
											@if (request()->filled('q'))
												<li>
													<a href="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'relevance']), null, false) !!}" rel="nofollow">
														{{ t('Relevance') }}
													</a>
												</li>
											@endif
											<li>
												<a href="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'date']), null, false) !!}" rel="nofollow">
													{{ t('Date') }}
												</a>
											</li>
											@if (isset($city, $distanceRange) and !empty($city) and !empty($distanceRange))
												@foreach($distanceRange as $key => $value)
													<li>
														<a href="{!! qsUrl(request()->url(), array_merge(request()->except('distance'), ['distance' => $value]), null, false) !!}" rel="nofollow">
															{{ t('around_x_distance', ['distance' => $value, 'unit' => getDistanceUnit()]) }}
														</a>
													</li>
												@endforeach
											@endif
											@if (config('plugins.reviews.installed'))
												<li>
													<a href="{!! qsUrl(request()->url(), array_merge(request()->except('orderBy'), ['orderBy'=>'rating']), null, false) !!}"
													   rel="nofollow">
														{{ trans('reviews::messages.Rating') }}
													</a>
												</li>
											@endif
										</ul>
									</div>
								</li>
							</ul>
						</div>
						<div class="menu-overly-mask"></div>
						<!-- Mobile Filter bar End-->

						<div id="postsList" class="adds-wrapper row no-margin">
							@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.posts', 'search.inc.posts'])
						</div>

						<div class="tab-box save-search-bar text-center">
							@if (request()->filled('q') and request()->get('q') != '' and $count->get('all') > 0)
								<a name="{!! qsUrl(request()->url(), request()->except(['_token', 'location']), null, false) !!}" id="saveSearch"
								   count="{{ $count->get('all') }}">
									<i class="icon-star-empty"></i> {{ t('Save Search') }}
								</a>
							@else
								<a href="#"> &nbsp; </a>
							@endif
						</div>
					</div>
					
					<nav class="pagination-bar mb-5 pagination-sm" aria-label="">
						{!! $posts->appends(request()->query())->links() !!}
					</nav>

					<div class="post-promo text-center mb-5">
						<h2> {{ t('do_have_anything_to_sell_or_rent') }} </h2>
						<h5>{{ t('sell_products_and_services_online_for_free') }}</h5>
						@if (!auth()->check() and config('settings.single.guests_can_post_ads') != '1')
							<a href="#quickLogin" class="btn btn-border btn-post btn-add-listing" data-toggle="modal">{{ t('start_now') }}</a>
						@else
							<a href="{{ \App\Helpers\UrlGen::addPost() }}" class="btn btn-border btn-post btn-add-listing">{{ t('start_now') }}</a>
						@endif
					</div>

				</div>
				
				<div style="clear:both;"></div>

				<!-- Advertising -->
				@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.advertising.bottom', 'layouts.inc.advertising.bottom'])

			</div>
		</div>
	</div>
@endsection

@section('modal_location')
	@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.modal.location', 'layouts.inc.modal.location'])
@endsection

@section('after_scripts')
	<script>
		$(document).ready(function () {
			$('#postType a').click(function (e) {
				e.preventDefault();
				var goToUrl = $(this).attr('href');
				redirect(goToUrl);
			});
			$('#orderBy').change(function () {
				var goToUrl = $(this).val();
				redirect(goToUrl);
			});
		});
		
		@if (config('settings.optimization.lazy_loading_activation') == 1)
		$(document).ready(function () {
			$('#postsList').each(function () {
				var $masonry = $(this);
				var update = function () {
					$.fn.matchHeight._update();
				};
				$('.item-list', $masonry).matchHeight();
				this.addEventListener('load', update, true);
			});
		});
		@endif
	</script>
@endsection
