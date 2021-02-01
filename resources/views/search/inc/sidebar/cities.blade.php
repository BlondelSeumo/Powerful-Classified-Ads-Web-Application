<!-- City -->
<div class="block-title has-arrow sidebar-header">
	<h5>
		<span class="font-weight-bold">
			{{ t('locations') }}
		</span>
	</h5>
</div>
<div class="block-content list-filter locations-list">
	<ul class="browse-list list-unstyled long-list">
		@if (isset($cities) and $cities->count() > 0)
			@foreach ($cities as $iCity)
				<?php
				$locationParams = [];
				if (isset($cat) and !empty($cat)) {
					if (isset($cat->parent) and !empty($cat->parent)) {
						$locationParams = [
							'l'  => $iCity->id,
							'r'  => '',
							'c'  => $cat->parent->tid,
							'sc' => $cat->tid,
						];
					} else {
						$locationParams = [
							'l'  => $iCity->id,
							'r'  => '',
							'c'  => $cat->tid,
						];
					}
				} else {
					$locationParams = [
						'l'  => $iCity->id,
						'r'  => '',
					];
				}
				?>
				<li>
					@if ((isset($city) and !empty($city) and $city->id == $iCity->id) or (request()->input('l') == $iCity->id))
						<strong>
							<a href="{!! \App\Helpers\UrlGen::search(array_merge(request()->except(['page'] + array_keys($locationParams)), $locationParams)) !!}"
							   title="{{ $iCity->name }}"
							>
								{{ $iCity->name }}
								@if (config('settings.listing.count_cities_posts'))
									<span class="count">{{ $iCity->posts_count ?? 0 }}</span>
								@endif
							</a>
						</strong>
					@else
						<a href="{!! \App\Helpers\UrlGen::search(array_merge(request()->except(['page'] + array_keys($locationParams)), $locationParams)) !!}"
						   title="{{ $iCity->name }}"
						>
							{{ $iCity->name }}
							@if (config('settings.listing.count_cities_posts'))
								<span class="count">{{ $iCity->posts_count ?? 0 }}</span>
							@endif
						</a>
					@endif
				</li>
			@endforeach
		@endif
	</ul>
</div>
<div style="clear:both"></div>