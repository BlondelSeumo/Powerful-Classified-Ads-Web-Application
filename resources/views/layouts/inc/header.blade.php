<?php
// Search parameters
$queryString = (request()->getQueryString() ? ('?' . request()->getQueryString()) : '');

// Check if the Multi-Countries selection is enabled
$multiCountriesIsEnabled = false;
$multiCountriesLabel = '';
if (config('settings.geo_location.country_flag_activation')) {
	if (!empty(config('country.code'))) {
		if (\App\Models\Country::where('active', 1)->count() > 1) {
			$multiCountriesIsEnabled = true;
			$multiCountriesLabel = 'title="' . t('Select a Country') . '"';
		}
	}
}

// Logo Label
$logoLabel = '';
if (request()->segment(1) != 'countries') {
	if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled) {
		$logoLabel = config('settings.app.app_name') . ((!empty(config('country.name'))) ? ' ' . config('country.name') : '');
	}
}
?>
<div class="header">
	<nav class="navbar fixed-top navbar-site navbar-light bg-light navbar-expand-md" role="navigation">
		<div class="container">
			
			<div class="navbar-identity">
				{{-- Logo --}}
				<a href="{{ url('/') }}" class="navbar-brand logo logo-title">
					<img src="{{ imgUrl(config('settings.app.logo'), 'logo') }}"
						 alt="{{ strtolower(config('settings.app.app_name')) }}" class="tooltipHere main-logo" title="" data-placement="bottom"
						 data-toggle="tooltip"
						 data-original-title="{!! isset($logoLabel) ? $logoLabel : '' !!}"/>
				</a>
				{{-- Toggle Nav (Mobile) --}}
				<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggler pull-right" type="button">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30" height="30" focusable="false">
						<title>{{ t('Menu') }}</title>
						<path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"></path>
					</svg>
				</button>
				{{-- Country Flag (Mobile) --}}
				@if (request()->segment(1) != 'countries')
					@if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
						@if (!empty(config('country.icode')))
							@if (file_exists(public_path() . '/images/flags/24/' . config('country.icode') . '.png'))
								<button class="flag-menu country-flag d-block d-md-none btn btn-secondary hidden pull-right" href="#selectCountry" data-toggle="modal">
									<img src="{{ url('images/flags/24/' . config('country.icode') . '.png') . getPictureVersion() }}"
										 alt="{{ config('country.name') }}"
										 style="float: left;"
									>
									<span class="caret hidden-xs"></span>
								</button>
							@endif
						@endif
					@endif
				@endif
			</div>
			
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-left">
					{{-- Country Flag --}}
					@if (request()->segment(1) != 'countries')
						@if (config('settings.geo_location.country_flag_activation'))
							@if (!empty(config('country.icode')))
								@if (file_exists(public_path() . '/images/flags/32/' . config('country.icode') . '.png'))
									<li class="flag-menu country-flag tooltipHere hidden-xs nav-item" data-toggle="tooltip" data-placement="{{ (config('lang.direction') == 'rtl') ? 'bottom' : 'right' }}" {!! $multiCountriesLabel !!}>
										@if (isset($multiCountriesIsEnabled) and $multiCountriesIsEnabled)
											<a href="#selectCountry" data-toggle="modal" class="nav-link">
												<img class="flag-icon"
													 src="{{ url('images/flags/32/' . config('country.icode') . '.png') . getPictureVersion() }}"
													 alt="{{ config('country.name') }}"
												>
												<span class="caret hidden-sm"></span>
											</a>
										@else
											<a style="cursor: default;">
												<img class="flag-icon no-caret"
													 src="{{ url('images/flags/32/' . config('country.icode') . '.png') . getPictureVersion() }}"
													 alt="{{ config('country.name') }}"
												>
											</a>
										@endif
									</li>
								@endif
							@endif
						@endif
					@endif
				</ul>
				
				<ul class="nav navbar-nav ml-auto navbar-right">
					@if (!auth()->check())
						<li class="nav-item">
							@if (config('settings.security.login_open_in_modal'))
								<a href="#quickLogin" class="nav-link" data-toggle="modal"><i class="icon-user fa"></i> {{ t('log_in') }}</a>
							@else
								<a href="{{ \App\Helpers\UrlGen::login() }}" class="nav-link"><i class="icon-user fa"></i> {{ t('log_in') }}</a>
							@endif
						</li>
						<li class="nav-item hidden-sm">
							<a href="{{ \App\Helpers\UrlGen::register() }}" class="nav-link"><i class="icon-user-add fa"></i> {{ t('register') }}</a>
						</li>
					@else
						<li class="nav-item hidden-sm">
							@if (app('impersonate')->isImpersonating())
								<a href="{{ route('impersonate.leave') }}" class="nav-link">
									<i class="icon-logout hidden-sm"></i> {{ t('Leave') }}
								</a>
							@else
								<a href="{{ \App\Helpers\UrlGen::logout() }}" class="nav-link">
									<i class="icon-logout hidden-sm"></i> {{ t('log_out') }}
								</a>
							@endif
						</li>
						<li class="nav-item dropdown no-arrow">
							<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
								<i class="icon-user fa hidden-sm"></i>
								<span>{{ auth()->user()->name }}</span>
								<span class="badge badge-pill badge-important count-threads-with-new-messages hidden-sm">0</span>
								<i class="icon-down-open-big fa hidden-sm"></i>
							</a>
							<ul id="userMenuDropdown" class="dropdown-menu user-menu dropdown-menu-right shadow-sm">
								<li class="dropdown-item active">
									<a href="{{ url('account') }}">
										<i class="icon-home"></i> {{ t('Personal Home') }}
									</a>
								</li>
								<li class="dropdown-item"><a href="{{ url('account/my-posts') }}"><i class="icon-th-thumb"></i> {{ t('my_ads') }} </a></li>
								<li class="dropdown-item"><a href="{{ url('account/favourite') }}"><i class="icon-heart"></i> {{ t('favourite_ads') }} </a></li>
								<li class="dropdown-item"><a href="{{ url('account/saved-search') }}"><i class="icon-star-circled"></i> {{ t('Saved searches') }} </a></li>
								<li class="dropdown-item"><a href="{{ url('account/pending-approval') }}"><i class="icon-hourglass"></i> {{ t('pending_approval') }} </a></li>
								<li class="dropdown-item"><a href="{{ url('account/archived') }}"><i class="icon-folder-close"></i> {{ t('archived_ads') }}</a></li>
								<li class="dropdown-item">
									<a href="{{ url('account/messages') }}">
										<i class="icon-mail-1"></i> {{ t('messenger') }}
										<span class="badge badge-pill badge-important count-threads-with-new-messages">0</span>
									</a>
								</li>
								<li class="dropdown-item"><a href="{{ url('account/transactions') }}"><i class="icon-money"></i> {{ t('Transactions') }}</a></li>
								<li class="dropdown-divider"></li>
								<li class="dropdown-item">
									@if (app('impersonate')->isImpersonating())
										<a href="{{ route('impersonate.leave') }}"><i class="icon-logout"></i> {{ t('Leave') }}</a>
									@else
										<a href="{{ \App\Helpers\UrlGen::logout() }}"><i class="icon-logout"></i> {{ t('log_out') }}</a>
									@endif
								</li>
							</ul>
						</li>
					@endif
					
					@if (config('plugins.currencyexchange.installed'))
						@include('currencyexchange::select-currency')
					@endif
					
					@if (config('settings.single.pricing_page_enabled') == '2')
						<li class="nav-item pricing">
							<a href="{{ \App\Helpers\UrlGen::pricing() }}" class="nav-link">
								<i class="fas fa-tags"></i> {{ t('pricing_label') }}
							</a>
						</li>
					@endif
					
					<?php
						$addListingUrl = \App\Helpers\UrlGen::addPost();
						$addListingAttr = '';
						if (!auth()->check()) {
							if (config('settings.single.guests_can_post_ads') != '1') {
								$addListingUrl = '#quickLogin';
								$addListingAttr = ' data-toggle="modal"';
							}
						}
						if (config('settings.single.pricing_page_enabled') == '1') {
							$addListingUrl = \App\Helpers\UrlGen::pricing();
							$addListingAttr = '';
						}
					?>
					<li class="nav-item postadd">
						<a class="btn btn-block btn-border btn-post btn-add-listing" href="{{ $addListingUrl }}"{!! $addListingAttr !!}>
							<i class="fa fa-plus-circle"></i> {{ t('Add Listing') }}
						</a>
					</li>
					
					@includeFirst([config('larapen.core.customizedViewPath') . 'layouts.inc.menu.select-language', 'layouts.inc.menu.select-language'])
					
				</ul>
			</div>
			
			
		</div>
	</nav>
</div>
