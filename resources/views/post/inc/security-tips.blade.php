<div class="modal fade" id="securityTips" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			
			<div class="modal-header">
				<h5 class="modal-title font-weight-bold" id="securityTipsLabel">{{ t('phone_number') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<?php
				$phoneModal = '';
				$phoneModalLink = '';
				// If the 'hide_phone_number' option is disabled, append phone number in modal
				if (config('settings.single.hide_phone_number') == '') {
					if (isset($post, $post->phone)) {
						$phoneModal = $post->phone;
						$phoneModalLink = 'tel:' . $post->phone;
					}
				}
			?>
			
			<div class="modal-body">
				<div class="row">
					<div class="col-12 text-center">
						<h1 id="phoneModal" class="p-4 font-weight-bold rounded" style="border: 2px dashed red; background-color: #ffebb7;">
							{{ $phoneModal }}
						</h1>
					</div>
					<div class="col-12 mt-4">
						<h3 class="text-danger" style="font-weight: bold;">
							<i class="fas fa-exclamation-triangle"></i> {!! t('security_tips_title') !!}
						</h3>
					</div>
					<div class="col-12">
						{!! t('security_tips_text', ['appName' => config('app.name')]) !!}
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t('Close') }}</button>
				<a id="phoneModalLink" href="{{ $phoneModalLink }}" class="btn btn-success">
					<i class="icon-phone-1"></i> {{ t('call_now') }}
				</a>
			</div>
			
		</div>
	</div>
</div>