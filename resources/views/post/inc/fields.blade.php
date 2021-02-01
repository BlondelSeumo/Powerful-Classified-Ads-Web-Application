<?php
	if (!isset($languageCode) or empty($languageCode)) {
		$languageCode = config('app.locale', session('langCode'));
	}
?>
@if (isset($fields) and $fields->count() > 0)
	@foreach($fields as $field)
		<?php
		// Fields parameters
		$fieldId = 'cf.' . $field->tid;
        $fieldName = 'cf[' . $field->tid . ']';
		$fieldOld = 'cf.' . $field->tid;
        
        // Errors & Required CSS
        $requiredClass = ($field->required == 1) ? 'required' : '';
        $errorClass = (isset($errors) && $errors->has($fieldOld)) ? ' is-invalid' : '';
        
        // Get the default value
        $defaultValue = (isset($oldInput) && isset($oldInput[$field->tid])) ? $oldInput[$field->tid] : $field->default;
		?>
		
		@if ($field->type == 'checkbox')
			
			<!-- checkbox -->
			<div class="form-group row {{ $requiredClass }}" style="margin-top: -10px;">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}"></label>
				<div class="col-md-8">
					<div class="form-check pt-2">
						<input id="{{ $fieldId }}"
							   name="{{ $fieldName }}"
							   value="1"
							   type="checkbox"
							   class="form-check-input{{ $errorClass }}"
								{{ ($defaultValue=='1') ? 'checked="checked"' : '' }}
						>
						<label class="form-check-label" for="{{ $fieldId }}">
							{{ $field->name }}
						</label>
					</div>
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
		
		@elseif ($field->type == 'checkbox_multiple')
			
			@if ($field->options->count() > 0)
				<!-- checkbox_multiple -->
				<div class="form-group row {{ $requiredClass }}" style="margin-top: -10px;">
					<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
						{{ $field->name }}
						@if ($field->required == 1)
							<sup>*</sup>
						@endif
					</label>
					<?php $cmFieldStyle = ($field->options->count() > 12) ? ' style="height: 250px; overflow-y: scroll;"' : ''; ?>
					<div class="col-md-8"{!! $cmFieldStyle !!}>
						@foreach ($field->options as $option)
							<?php
							// Get the default value
							$defaultValue = (isset($oldInput) && isset($oldInput[$field->tid]) && isset($oldInput[$field->tid][$option->tid]))
								? $oldInput[$field->tid][$option->tid]
								: (
								(is_array($field->default) && isset($field->default[$option->tid]) && isset($field->default[$option->tid]->tid))
									? $field->default[$option->tid]->tid
									: $field->default
								);
							?>
							<div class="form-check pt-2">
								<input id="{{ $fieldId . '.' . $option->tid }}"
									   name="{{ $fieldName . '[' . $option->tid . ']' }}"
									   value="{{ $option->tid }}"
									   type="checkbox"
									   class="form-check-input{{ $errorClass }}"
										{{ ($defaultValue==$option->tid) ? 'checked="checked"' : '' }}
								>
								<label class="form-check-label" for="{{ $fieldId . '.' . $option->tid }}">
									 {{ $option->value }}
								</label>
							</div>
						@endforeach
						<small id="" class="form-text text-muted">{!! $field->help !!}</small>
					</div>
				</div>
			@endif
			
		@elseif ($field->type == 'file')
			
			<!-- file -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
					<div class="mb10">
						<input id="{{ $fieldId }}" name="{{ $fieldName }}" type="file" class="file{{ $errorClass }}">
					</div>
					<small id="" class="form-text text-muted">
						{!! $field->help !!} {{ t('file_types', ['file_types' => showValidFileTypes('file')], 'global', $languageCode) }}
					</small>
					@if (!empty($field->default) and $disk->exists($field->default))
						<div>
							<a class="btn btn-default" href="{{ fileUrl($field->default) }}" target="_blank">
								<i class="icon-attach-2"></i> {{ t('Download') }}
							</a>
						</div>
					@endif
				</div>
			</div>
		
		@elseif ($field->type == 'radio')
			
			@if ($field->options->count() > 0)
				<!-- radio -->
				<div class="form-group row {{ $requiredClass }}">
					<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
						{{ $field->name }}
						@if ($field->required == 1)
							<sup>*</sup>
						@endif
					</label>
					<div class="col-md-8">
						@foreach ($field->options as $option)
							<div class="form-check pt-2">
								<input id="{{ $fieldId }}"
									   name="{{ $fieldName }}"
									   value="{{ $option->tid }}"
									   type="radio"
									   class="form-check-input{{ $errorClass }}"
										{{ ($defaultValue==$option->tid) ? 'checked="checked"' : '' }}
								>
								<label class="form-check-label" for="{{ $fieldName }}">
									{{ $option->value }}
								</label>
							</div>
						@endforeach
					</div>
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			@endif
		
		@elseif ($field->type == 'select')
			
			<!-- select -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label{{ $errorClass }}" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
                    <?php
                    	$select2Type = ($field->options->count() <= 10) ? 'selecter' : 'sselecter';
                    ?>
					<select id="{{ $fieldId }}" name="{{ $fieldName }}" class="form-control {{ $select2Type . $errorClass }}">
						<option value="{{ $field->default }}"
								@if (old($fieldOld)=='' or old($fieldOld)==$field->default)
									selected="selected"
								@endif
						>
							{{ t('Select', [], 'global', $languageCode) }}
						</option>
						@if ($field->options->count() > 0)
							@foreach ($field->options as $option)
								<option value="{{ $option->tid }}"
										@if ($defaultValue==$option->tid)
											selected="selected"
										@endif
								>
									{{ $option->value }}
								</option>
							@endforeach
						@endif
					</select>
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
		
		@elseif ($field->type == 'textarea')
			
			<!-- textarea -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
					<textarea class="form-control{{ $errorClass }}"
							  id="{{ $fieldId }}"
							  name="{{ $fieldName }}"
							  placeholder="{{ $field->name }}"
							  rows="10">{{ $defaultValue }}</textarea>
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
		
		@elseif ($field->type == 'url')
			
			<!-- url -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
					<input id="{{ $fieldId }}"
						   name="{{ $fieldName }}"
						   type="text"
						   placeholder="{{ $field->name }}"
						   class="form-control input-md{{ $errorClass }}"
						   value="{{ $defaultValue }}">
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
		
		@elseif ($field->type == 'number')
			
			<!-- number -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
					<input id="{{ $fieldId }}"
						   name="{{ $fieldName }}"
						   type="number"
						   placeholder="{{ $field->name }}"
						   class="form-control input-md{{ $errorClass }}"
						   value="{{ $defaultValue }}">
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
		
		@elseif ($field->type == 'date')
			
			<!-- date -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
					<input id="{{ $fieldId }}"
						   name="{{ $fieldName }}"
						   type="text"
						   placeholder="{{ $field->name }}"
						   class="form-control input-md{{ $errorClass }} cf-date"
						   value="{{ $defaultValue }}"
						   autocomplete="off"
					>
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
			
		@elseif ($field->type == 'date_time')
			
			<!-- date_time -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
					<input id="{{ $fieldId }}"
						   name="{{ $fieldName }}"
						   type="text"
						   placeholder="{{ $field->name }}"
						   class="form-control input-md{{ $errorClass }} cf-date_time"
						   value="{{ $defaultValue }}"
						   autocomplete="off"
					>
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
			
		@elseif ($field->type == 'date_range')
			
			<!-- date_range -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
					<input id="{{ $fieldId }}"
						   name="{{ $fieldName }}"
						   type="text"
						   placeholder="{{ $field->name }}"
						   class="form-control input-md{{ $errorClass }} cf-date_range"
						   value="{{ $defaultValue }}"
						   autocomplete="off"
					>
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
			
		@else
			
			<!-- text -->
			<div class="form-group row {{ $requiredClass }}">
				<label class="col-md-3 col-form-label" for="{{ $fieldId }}">
					{{ $field->name }}
					@if ($field->required == 1)
						<sup>*</sup>
					@endif
				</label>
				<div class="col-md-8">
					<input id="{{ $fieldId }}"
						   name="{{ $fieldName }}"
						   type="text"
						   placeholder="{{ $field->name }}"
						   class="form-control input-md{{ $errorClass }}"
						   value="{{ $defaultValue }}">
					<small id="" class="form-text text-muted">{!! $field->help !!}</small>
				</div>
			</div>
			
		@endif
	@endforeach
@endif

<script>
	$(function() {
		/*
		 * Custom Fields Date Picker
		 * https://www.daterangepicker.com/#options
		 */
		{{-- Single Date --}}
		$('#cfContainer .cf-date').daterangepicker({
			autoUpdateInput: false,
			autoApply: true,
			showDropdowns: true,
			minYear: parseInt(moment().format('YYYY')) - 100,
			maxYear: parseInt(moment().format('YYYY')) + 20,
			locale: {
				format: '{{ t('datepicker_format') }}',
				applyLabel: "{{ t('datepicker_applyLabel') }}",
				cancelLabel: "{{ t('datepicker_cancelLabel') }}",
				fromLabel: "{{ t('datepicker_fromLabel') }}",
				toLabel: "{{ t('datepicker_toLabel') }}",
				customRangeLabel: "{{ t('datepicker_customRangeLabel') }}",
				weekLabel: "{{ t('datepicker_weekLabel') }}",
				daysOfWeek: [
					"{{ t('datepicker_sunday') }}",
					"{{ t('datepicker_monday') }}",
					"{{ t('datepicker_tuesday') }}",
					"{{ t('datepicker_wednesday') }}",
					"{{ t('datepicker_thursday') }}",
					"{{ t('datepicker_friday') }}",
					"{{ t('datepicker_saturday') }}"
				],
				monthNames: [
					"{{ t('January') }}",
					"{{ t('February') }}",
					"{{ t('March') }}",
					"{{ t('April') }}",
					"{{ t('May') }}",
					"{{ t('June') }}",
					"{{ t('July') }}",
					"{{ t('August') }}",
					"{{ t('September') }}",
					"{{ t('October') }}",
					"{{ t('November') }}",
					"{{ t('December') }}"
				],
				firstDay: 1
			},
			singleDatePicker: true,
			startDate: moment().format('{{ t('datepicker_format') }}')
		});
		$('#cfContainer .cf-date').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('{{ t('datepicker_format') }}'));
		});
		
		{{-- Single Date (with Time) --}}
		$('#cfContainer .cf-date_time').daterangepicker({
			autoUpdateInput: false,
			autoApply: true,
			showDropdowns: false,
			minYear: parseInt(moment().format('YYYY')) - 100,
			maxYear: parseInt(moment().format('YYYY')) + 20,
			locale: {
				format: '{{ t('datepicker_format_datetime') }}',
				applyLabel: "{{ t('datepicker_applyLabel') }}",
				cancelLabel: "{{ t('datepicker_cancelLabel') }}",
				fromLabel: "{{ t('datepicker_fromLabel') }}",
				toLabel: "{{ t('datepicker_toLabel') }}",
				customRangeLabel: "{{ t('datepicker_customRangeLabel') }}",
				weekLabel: "{{ t('datepicker_weekLabel') }}",
				daysOfWeek: [
					"{{ t('datepicker_sunday') }}",
					"{{ t('datepicker_monday') }}",
					"{{ t('datepicker_tuesday') }}",
					"{{ t('datepicker_wednesday') }}",
					"{{ t('datepicker_thursday') }}",
					"{{ t('datepicker_friday') }}",
					"{{ t('datepicker_saturday') }}"
				],
				monthNames: [
					"{{ t('January') }}",
					"{{ t('February') }}",
					"{{ t('March') }}",
					"{{ t('April') }}",
					"{{ t('May') }}",
					"{{ t('June') }}",
					"{{ t('July') }}",
					"{{ t('August') }}",
					"{{ t('September') }}",
					"{{ t('October') }}",
					"{{ t('November') }}",
					"{{ t('December') }}"
				],
				firstDay: 1
			},
			singleDatePicker: true,
			timePicker: true,
			timePicker24Hour: true,
			startDate: moment().format('{{ t('datepicker_format_datetime') }}')
		});
		$('#cfContainer .cf-date_time').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('{{ t('datepicker_format_datetime') }}'));
		});
		
		{{-- Date Range --}}
		$('#cfContainer .cf-date_range').daterangepicker({
			autoUpdateInput: false,
			autoApply: true,
			showDropdowns: false,
			minYear: parseInt(moment().format('YYYY')) - 100,
			maxYear: parseInt(moment().format('YYYY')) + 20,
			locale: {
				format: '{{ t('datepicker_format') }}',
				applyLabel: "{{ t('datepicker_applyLabel') }}",
				cancelLabel: "{{ t('datepicker_cancelLabel') }}",
				fromLabel: "{{ t('datepicker_fromLabel') }}",
				toLabel: "{{ t('datepicker_toLabel') }}",
				customRangeLabel: "{{ t('datepicker_customRangeLabel') }}",
				weekLabel: "{{ t('datepicker_weekLabel') }}",
				daysOfWeek: [
					"{{ t('datepicker_sunday') }}",
					"{{ t('datepicker_monday') }}",
					"{{ t('datepicker_tuesday') }}",
					"{{ t('datepicker_wednesday') }}",
					"{{ t('datepicker_thursday') }}",
					"{{ t('datepicker_friday') }}",
					"{{ t('datepicker_saturday') }}"
				],
				monthNames: [
					"{{ t('January') }}",
					"{{ t('February') }}",
					"{{ t('March') }}",
					"{{ t('April') }}",
					"{{ t('May') }}",
					"{{ t('June') }}",
					"{{ t('July') }}",
					"{{ t('August') }}",
					"{{ t('September') }}",
					"{{ t('October') }}",
					"{{ t('November') }}",
					"{{ t('December') }}"
				],
				firstDay: 1
			},
			startDate: moment().format('{{ t('datepicker_format') }}'),
			endDate: moment().add(1, 'days').format('{{ t('datepicker_format') }}')
		});
		$('#cfContainer .cf-date_range').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('{{ t('datepicker_format') }}') + ' - ' + picker.endDate.format('{{ t('datepicker_format') }}'));
		});
	});
</script>
