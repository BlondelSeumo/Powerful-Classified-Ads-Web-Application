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

@section('wizard')
    @includeFirst([config('larapen.core.customizedViewPath') . 'post.createOrEdit.multiSteps.inc.wizard', 'post.createOrEdit.multiSteps.inc.wizard'])
@endsection

<?php
// The Next Step URL
$nextStepUrl = url($nextStepUrl);
$nextStepUrl = qsUrl($nextStepUrl, request()->only(['package']), null, false);
?>
@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
    <div class="main-container">
        <div class="container">
            <div class="row">
    
                @includeFirst([config('larapen.core.customizedViewPath') . 'post.inc.notification', 'post.inc.notification'])
                
                <div class="col-md-12 page-content">
                    <div class="inner-box">
						
                        <h2 class="title-2">
							<strong><i class="icon-camera-1"></i> {{ t('Photos') }}</strong>
							<?php
							try {
								if (auth()->check()) {
									if (auth()->user()->can(\App\Models\Permission::getStaffPermissions())) {
										$postLink = '-&nbsp;<a href="' . \App\Helpers\UrlGen::post($post) . '"
												  class="tooltipHere"
												  title=""
												  data-placement="top"
												  data-toggle="tooltip"
												  data-original-title="' . $post->title . '"
										>' . \Illuminate\Support\Str::limit($post->title, 45) . '</a>';
										
										echo $postLink;
									}
								}
							} catch (\Exception $e) {}
							?>
						</h2>
						
                        <div class="row">
                            <div class="col-md-12">
                                <form class="form-horizontal" id="postForm" method="POST" action="{{ request()->fullUrl() }}" enctype="multipart/form-data">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                    <fieldset>
                                        @if (isset($picturesLimit) and is_numeric($picturesLimit) and $picturesLimit > 0)
                                            <!-- Pictures -->
											<?php $picturesError = (isset($errors) and $errors->has('pictures')) ? ' is-invalid' : ''; ?>
                                            <div id="picturesBloc" class="form-group row">
												<label class="col-md-3 control-label{{ $picturesError }}" for="pictures"> {{ t('pictures') }} </label>
												<div class="col-md-8"></div>
												<div class="col-md-12 text-center pt-2" style="position: relative; float: {!! (config('lang.direction')=='rtl') ? 'left' : 'right' !!};">
													<div {!! (config('lang.direction')=='rtl') ? 'dir="rtl"' : '' !!} class="file-loading">
														<input id="pictureField" name="pictures[]" type="file" multiple class="file picimg{{ $picturesError }}">
													</div>
													<small id="" class="form-text text-muted">
														{{ t('add_up_to_x_pictures_text', [
															'pictures_number' => $picturesLimit
														]) }}
													</small>
												</div>
                                            </div>
                                        @endif
                                        <div id="uploadError mt-2" style="display: none;"></div>
                                        <div id="uploadSuccess" class="alert alert-success fade show mt-2" style="display: none;"></div>
                                    
                                    
                                        <!-- Button -->
                                        <div class="form-group row mt-4">
                                            <div class="col-md-12 text-center">
                                                @if (request()->segment(2) != 'create')
                                                    <a href="{{ url('posts/' . $post->id . '/edit') }}" class="btn btn-default btn-lg">{{ t('Previous') }}</a>
                                                @endif
                                                <a id="nextStepAction" href="{{ $nextStepUrl }}" class="btn btn-default btn-lg">{{ t('Skip') }}</a>
                                            </div>
                                        </div>
                                    
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.page-content -->
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
    <link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
	@if (config('lang.direction') == 'rtl')
		<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput-rtl.min.css') }}" rel="stylesheet">
	@endif
    <style>
        .krajee-default.file-preview-frame:hover:not(.file-preview-error) {
            box-shadow: 0 0 5px 0 #666666;
        }
		.file-loading:before {
			content: " {{ t('Loading') }}...";
		}
    </style>
@endsection

@section('after_scripts')
    <script src="{{ url('assets/plugins/bootstrap-fileinput/js/plugins/sortable.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js') }}" type="text/javascript"></script>
	<script src="{{ url('assets/plugins/bootstrap-fileinput/themes/fa/theme.js') }}" type="text/javascript"></script>
	<script src="{{ url('js/fileinput/locales/' . config('app.locale') . '.js') }}" type="text/javascript"></script>
    <script>
        /* Initialize with defaults (pictures) */
        @if (isset($post, $picturesLimit) and is_numeric($picturesLimit) and $picturesLimit > 0)
        <?php
            // Get Upload Url
            if (request()->segment(2) == 'create') {
                $uploadUrl = url('posts/create/' . $post->tmp_token . '/photos/');
            } else {
                $uploadUrl = url('posts/' . $post->id . '/photos/');
            }
            $uploadUrl = qsUrl($uploadUrl, request()->only(['package']), null, false);
        ?>
            $('#pictureField').fileinput(
            {
				theme: "fa",
                language: '{{ config('app.locale') }}',
				@if (config('lang.direction') == 'rtl')
					rtl: true,
				@endif
                overwriteInitial: false,
                showCaption: false,
                showPreview: true,
                allowedFileExtensions: {!! getUploadFileTypes('image', true) !!},
				uploadUrl: '{{ $uploadUrl }}',
                uploadAsync: false,
				showBrowse: true,
				showCancel: true,
				showUpload: false,
				showRemove: false,
				minFileSize: {{ (int)config('settings.upload.min_image_size', 0) }}, {{-- in KB --}}
                maxFileSize: {{ (int)config('settings.upload.max_image_size', 1000) }}, {{-- in KB --}}
                browseOnZoneClick: true,
                minFileCount: 0,
                maxFileCount: {{ (int)$picturesLimit }},
                validateInitialCount: true,
                @if (isset($post->pictures))
                /* Retrieve current images */
                /* Setup initial preview with data keys */
                initialPreview: [
                @for($i = 0; $i <= $picturesLimit-1; $i++)
                    @continue(!$post->pictures->has($i) or !isset($post->pictures->get($i)->filename))
                    '{{ imgUrl($post->pictures->get($i)->filename, 'medium') }}',
                @endfor
                ],
                initialPreviewAsData: true,
                initialPreviewFileType: 'image',
                /* Initial preview configuration */
                initialPreviewConfig: [
                @for($i = 0; $i <= $picturesLimit-1; $i++)
                    @continue(!$post->pictures->has($i) or !isset($post->pictures->get($i)->filename))
                    <?php
					// Get the file path
					$filePath = $post->pictures->get($i)->filename;
					
                    // Get the file's deletion URL
                    if (request()->segment(2) == 'create') {
                        $initialPreviewConfigUrl = url('posts/create/' . $post->tmp_token . '/photos/' . $post->pictures->get($i)->id . '/delete');
                    } else {
                        $initialPreviewConfigUrl = url('posts/' . $post->id . '/photos/' . $post->pictures->get($i)->id . '/delete');
                    }
                    
                    // Get the file size
					try {
						$fileSize = (isset($disk) && $disk->exists($filePath)) ? (int)$disk->size($filePath) : 0;
					} catch (\Exception $e) {
						$fileSize = 0;
					}
                    ?>
                    {
                        caption: '{{ last(explode(DIRECTORY_SEPARATOR, $filePath)) }}',
                        size: {{ $fileSize }},
                        url: '{{ $initialPreviewConfigUrl }}',
						key: {{ (int)$post->pictures->get($i)->id }}
                    },
                @endfor
                ],
                @endif
				
                /* elErrorContainer: '#uploadError', */
				/* msgErrorClass: 'file-error-message', */ /* @todo: depreciated. */
				
				uploadClass: 'btn btn-success'
            });
        @endif

		/* Auto-upload added file */
		$('#pictureField').on('filebatchselected', function(event, data, id, index) {
			if (typeof data === 'object') {
				{{--
					Display the exact error (If it exists (Before making AJAX call))
					NOTE: The index '0' is available when the first file size is smaller than the maximum size allowed.
					      This index does not exist in the opposite case.
				--}}
				if (data.hasOwnProperty('0')) {
					$(this).fileinput('upload');
					return true;
				}
			}
			
			return false;
		});
		
		/* Show upload status message */
        $('#pictureField').on('filebatchpreupload', function(event, data, id, index) {
            $('#uploadSuccess').html('<ul></ul>').hide();
        });
		
		/* Show success upload message */
        $('#pictureField').on('filebatchuploadsuccess', function(event, data, previewId, index) {
            /* Show uploads success messages */
            var out = '';
            $.each(data.files, function(key, file) {
                if (typeof file !== 'undefined') {
                    var fname = file.name;
                    out = out + {!! t('Uploaded file X successfully') !!};
                }
            });
            $('#uploadSuccess ul').append(out);
            $('#uploadSuccess').fadeIn('slow');
            
            /* Change button label */
            $('#nextStepAction').html('{{ $nextStepLabel }}').removeClass('btn-default').addClass('btn-primary');
            
            /* Check redirect */
            var maxFiles = {{ (isset($picturesLimit)) ? (int)$picturesLimit : 1 }};
            var oldFiles = {{ (isset($post) and isset($post->pictures)) ? $post->pictures->count() : 0 }};
            var newFiles = Object.keys(data.files).length;
            var countFiles = oldFiles + newFiles;
            if (countFiles >= maxFiles) {
                var nextStepUrl = '{{ $nextStepUrl }}';
				redirect(nextStepUrl);
            }
        });
		
		/* Reorder (Sort) files */
		$('#pictureField').on('filesorted', function(event, params) {
			picturesReorder(params);
		});
		
		/* Delete picture */
        $('#pictureField').on('filepredelete', function(jqXHR) {
            var abort = true;
            if (confirm("{{ t('Are you sure you want to delete this picture') }}")) {
                abort = false;
            }
            return abort;
        });

		/**
		 * Reorder (Sort) pictures
		 * @param params
		 * @returns {boolean}
		 */
		function picturesReorder(params)
		{
			if (typeof params.stack === 'undefined') {
				return false;
			}
			
			waitingDialog.show('{{ t('Processing') }}...');
	
			$.ajax({
				method: 'POST',
				url: siteUrl + '/ajax/post/pictures/reorder',
				data: {
					'params': params,
					'_token': $('input[name=_token]').val()
				}
			}).done(function(data) {
				
				setTimeout(function() {
					waitingDialog.hide();
				}, 200);
				
				if (typeof data.status === 'undefined') {
					return false;
				}
		
				/* Reorder Notification */
				if (parseInt(data.status) === 1) {
					$('#uploadSuccess').html('<ul></ul>').hide();
					$('#uploadSuccess ul').append('{{ t('Your picture has been reorder successfully') }}');
					$('#uploadSuccess').fadeIn('slow');
				}
		
				return false;
			});
	
			return false;
		}
    </script>
    
@endsection
