@if (isset($cats) and $cats->count() > 0)
	<div class="container hide-xs">
		<div>
			<ul class="list-inline">
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
					<li class="list-inline-item mt-2">
						@if (isset($cat) and !empty($cat) and $iCat->tid == $cat->tid)
							<span class="badge badge-primary">
								{{ $iCat->name }}
							</span>
						@else
							<a href="{{ $iUrl }}" class="badge badge-light">
								{{ $iCat->name }}
							</a>
						@endif
					</li>
				@endforeach
			</ul>
		</div>
	</div>
@endif