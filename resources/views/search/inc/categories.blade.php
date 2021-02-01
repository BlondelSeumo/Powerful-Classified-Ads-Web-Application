@if (isset($cat) and !empty($cat))
	@if (isset($cat->children) and $cat->children->count() > 0)
		<div class="container hide-xs">
			<div>
				<ul class="list-inline">
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
					<li class="list-inline-item mt-2">
						<a href="{{ $iUrl }}" class="badge badge-light">
							{{ $iSubCat->name }}
						</a>
					</li>
				@endforeach
				</ul>
			</div>
		</div>
	@else
		@if (isset($cat->parent, $cat->parent->children) and $cat->parent->children->count() > 0)
			<div class="container hide-xs">
				<div>
					<ul class="list-inline">
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
							<li class="list-inline-item mt-2">
								@if ($iSubCat->tid == $cat->tid)
									<span class="badge badge-primary">
										{{ $iSubCat->name }}
									</span>
								@else
									<a href="{{ $iUrl }}" class="badge badge-light">
										{{ $iSubCat->name }}
									</a>
								@endif
							</li>
						@endforeach
					</ul>
				</div>
			</div>
		@else
			
			@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.categories-root', 'search.inc.categories-root'])
			
		@endif
	@endif
@else
	
	@includeFirst([config('larapen.core.customizedViewPath') . 'search.inc.categories-root', 'search.inc.categories-root'])
	
@endif