<!-- Category -->
<div id="catsList">
	<div class="block-title has-arrow sidebar-header">
		<h5>
			<span class="font-weight-bold">
				{{ t('all_categories') }}
			</span>
		</h5>
	</div>
	<div class="block-content list-filter categories-list">
		<ul class="list-unstyled">
			@if (isset($cats) and $cats->count() > 0)
				@foreach ($cats as $iCat)
					<?php
					$catParams = [];
					if (isset($city) and !empty($city)) {
						$catParams = [
							'l'  => $city->id,
							'r'  => '',
							'c'  => $iCat->tid,
						];
						$iUrl = \App\Helpers\UrlGen::search(array_merge(request()->except(['page'] + array_keys($catParams)), $catParams));
					} else {
						$iUrl = \App\Helpers\UrlGen::category($iCat);
					}
					?>
					<li>
						@if (isset($cat) and !empty($cat) and $iCat->tid == $cat->tid)
							<strong>
								<a href="{{ $iUrl }}" title="{{ $iCat->name }}">
									<span class="title">{{ $iCat->name }}</span>
									@if (config('settings.listing.count_categories_posts'))
										<span class="count">&nbsp;{{ $countPostsByCat->get($iCat->tid)->total ?? 0 }}</span>
									@endif
								</a>
							</strong>
						@else
							<a href="{{ $iUrl }}" title="{{ $iCat->name }}">
								<span class="title">{{ $iCat->name }}</span>
								@if (config('settings.listing.count_categories_posts'))
									<span class="count">&nbsp;{{ $countPostsByCat->get($iCat->tid)->total ?? 0 }}</span>
								@endif
							</a>
						@endif
					</li>
				@endforeach
			@endif
		</ul>
	</div>
</div>