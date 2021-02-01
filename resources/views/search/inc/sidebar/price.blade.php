@if (isset($cat) and !empty($cat))
	@if (!in_array($cat->type, ['not-salable']))
		<!-- Price -->
		<div class="block-title has-arrow sidebar-header">
			<h5>
				<span class="font-weight-bold">
					{{ (!in_array($cat->type, ['job-offer', 'job-search'])) ? t('price_range') : t('salary_range') }}
				</span>
			</h5>
		</div>
		<div class="block-content list-filter">
			<form role="form" class="form-inline" action="{{ request()->url() }}" method="GET">
				@foreach(request()->except(['page', 'minPrice', 'maxPrice', '_token']) as $key => $value)
					@if (is_array($value))
						@foreach($value as $k => $v)
							@if (is_array($v))
								@foreach($v as $ik => $iv)
									@continue(is_array($iv))
									<input type="hidden" name="{{ $key.'['.$k.']['.$ik.']' }}" value="{{ $iv }}">
								@endforeach
							@else
								<input type="hidden" name="{{ $key.'['.$k.']' }}" value="{{ $v }}">
							@endif
						@endforeach
					@else
						<input type="hidden" name="{{ $key }}" value="{{ $value }}">
					@endif
				@endforeach
				<div class="form-group col-sm-4 no-padding">
					<input type="text" placeholder="2000" id="minPrice" name="minPrice" class="form-control" value="{{ request()->get('minPrice') }}">
				</div>
				<div class="form-group col-sm-1 no-padding text-center hidden-xs"> -</div>
				<div class="form-group col-sm-4 no-padding">
					<input type="text" placeholder="3000" id="maxPrice" name="maxPrice" class="form-control" value="{{ request()->get('maxPrice') }}">
				</div>
				<div class="form-group col-sm-3 no-padding">
					<button class="btn btn-default pull-right btn-block-xs" type="submit">{{ t('go') }}</button>
				</div>
			</form>
		</div>
		<div style="clear:both"></div>
	@endif
@endif