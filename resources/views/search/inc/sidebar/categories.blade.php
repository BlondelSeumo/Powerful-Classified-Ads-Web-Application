@if (isset($cat) and !empty($cat))
	<?php
	$exceptParams = ['page', 'cf'];
	if (request()->filled('c') && request()->filled('sc')) {
		$exceptParams[] = 'sc';
	} else {
		if (request()->filled('c')) {
			$exceptParams[] = 'c';
		}
		if (request()->filled('sc')) {
			array_push($exceptParams, 'sc');
			$exceptParams[] = 'sc';
		}
	}
	$searchUrlWithoutCat = \App\Helpers\UrlGen::search([], $exceptParams);
	?>
	
	<!-- SubCategory -->
	<div id="subCatsList">
		@if (isset($cat->children) and $cat->children->count() > 0)
			
			<div class="block-title has-arrow sidebar-header">
				<h5>
				<span class="font-weight-bold">
					@if (isset($cat->parent) and !empty($cat->parent))
						<?php
						$catParams = [];
						if (isset($city) and !empty($city)) {
							$catParams = [
								'l'  => $city->id,
								'r'  => '',
								'c'  => $cat->parent->tid,
							];
							$iUrl = \App\Helpers\UrlGen::search(array_merge(request()->except(['page'] + array_keys($catParams)), $catParams));
						} else {
							$iUrl = \App\Helpers\UrlGen::category($cat->parent);
						}
						?>
						<a href="{{ $iUrl }}">
							<i class="fas fa-reply"></i> {{ $cat->parent->name }}
						</a>
					@else
						<a href="{{ $searchUrlWithoutCat }}">
							<i class="fas fa-reply"></i> {{ t('all_categories') }}
						</a>
					@endif
				</span>
				</h5>
			</div>
			<div class="block-content list-filter categories-list">
				<ul class="list-unstyled">
					<li>
						<?php
						$catParams = [];
						if (isset($city) and !empty($city)) {
							$catParams = [
								'l'  => $city->id,
								'r'  => '',
								'c'  => $cat->tid,
							];
							$iUrl = \App\Helpers\UrlGen::search(array_merge(request()->except(['page'] + array_keys($catParams)), $catParams));
						} else {
							$iUrl = \App\Helpers\UrlGen::category($cat);
						}
						?>
						<a href="{{ $iUrl }}" title="{{ $cat->name }}">
							<span class="title font-weight-bold">{{ $cat->name }}</span>
							@if (config('settings.listing.count_categories_posts'))
								<span class="count">&nbsp;({{ $countPostsByCat->get($cat->tid)->total ?? 0 }})</span>
							@endif
						</a>
						<ul class="list-unstyled long-list">
							@foreach ($cat->children as $iSubCat)
								<?php
								$catParams = [];
								if (isset($city) and !empty($city)) {
									$catParams = [
										'l'  => $city->id,
										'r'  => '',
										'c'  => $cat->tid,
										'sc' => $iSubCat->tid,
									];
									$iUrl = \App\Helpers\UrlGen::search(array_merge(request()->except(['page'] + array_keys($catParams)), $catParams));
								} else {
									$iUrl = \App\Helpers\UrlGen::category($iSubCat);
								}
								?>
								<li>
									<a href="{{ $iUrl }}" title="{{ $iSubCat->name }}">
										{{ \Illuminate\Support\Str::limit($iSubCat->name, 100) }}
										@if (config('settings.listing.count_categories_posts'))
											<span class="count">({{ $countPostsByCat->get($iSubCat->tid)->total ?? 0 }})</span>
										@endif
									</a>
								</li>
							@endforeach
						</ul>
					</li>
				</ul>
			</div>
			
		@else
			
			@if (isset($cat->parent, $cat->parent->children) and $cat->parent->children->count() > 0)
				<div class="block-title has-arrow sidebar-header">
					<h5>
						<span class="font-weight-bold">
							@if (isset($cat->parent->parent) and !empty($cat->parent->parent))
								<?php
								$catParams = [];
								if (isset($city) and !empty($city)) {
									$catParams = [
										'l'  => $city->id,
										'r'  => '',
										'c'  => $cat->parent->parent->tid,
									];
									$iUrl = \App\Helpers\UrlGen::search(array_merge(request()->except(['page'] + array_keys($catParams)), $catParams));
								} else {
									$iUrl = \App\Helpers\UrlGen::category($cat->parent->parent);
								}
								?>
								<a href="{{ $iUrl }}">
									<i class="fas fa-reply"></i> {{ $cat->parent->parent->name }}
								</a>
							@else
								<a href="{{ $searchUrlWithoutCat }}">
									<i class="fas fa-reply"></i> {{ t('all_categories') }}
								</a>
							@endif
						</span>
					</h5>
				</div>
				<div class="block-content list-filter categories-list">
					<ul class="list-unstyled">
						@foreach ($cat->parent->children as $iSubCat)
							<?php
							$catParams = [];
							if (isset($city) and !empty($city)) {
								$catParams = [
									'l'  => $city->id,
									'r'  => '',
									'c'  => $cat->parent->tid,
									'sc' => $iSubCat->tid,
								];
								$iUrl = \App\Helpers\UrlGen::search(array_merge(request()->except(['page'] + array_keys($catParams)), $catParams));
							} else {
								$iUrl = \App\Helpers\UrlGen::category($iSubCat);
							}
							?>
							<li>
								@if ($iSubCat->tid == $cat->tid)
									<strong>
										<a href="{{ $iUrl }}" title="{{ $iSubCat->name }}">
											{{ \Illuminate\Support\Str::limit($iSubCat->name, 100) }}
											@if (config('settings.listing.count_categories_posts'))
												<span class="count">({{ $countPostsByCat->get($iSubCat->tid)->total ?? 0 }})</span>
											@endif
										</a>
									</strong>
								@else
									<a href="{{ $iUrl }}" title="{{ $iSubCat->name }}">
										{{ \Illuminate\Support\Str::limit($iSubCat->name, 100) }}
										@if (config('settings.listing.count_categories_posts'))
											<span class="count">({{ $countPostsByCat->get($iSubCat->tid)->total ?? 0 }})</span>
										@endif
									</a>
								@endif
							</li>
						@endforeach
					</ul>
				</div>
			@else
				
				@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.sidebar.categories.root', 'search.inc.sidebar.categories.root'])
			
			@endif
			
		@endif
	</div>
	
@else
	
	@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.sidebar.categories.root', 'search.inc.sidebar.categories.root'])
	
@endif
<div style="clear:both"></div>