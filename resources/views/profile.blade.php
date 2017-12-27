@extends(Config::get('chatter.master_file_extend'))

@section(Config::get('chatter.yields.head'))
    <link href="/vendor/devdojo/chatter/assets/vendor/spectrum/spectrum.css" rel="stylesheet">
	<link href="/vendor/devdojo/chatter/assets/css/chatter.css" rel="stylesheet">
	@if($chatter_editor == 'simplemde')
		<link href="/vendor/devdojo/chatter/assets/css/simplemde.min.css" rel="stylesheet">
	@elseif($chatter_editor == 'trumbowyg')
		<link href="/vendor/devdojo/chatter/assets/vendor/trumbowyg/ui/trumbowyg.css" rel="stylesheet">
		<style>
			.trumbowyg-box, .trumbowyg-editor {
				margin: 0px auto;
			}
		</style>
	@endif
	<style type="text/css">
	#chatter .chatter_avatar.profile {
		position: relative !important;
		float: none !important;
	}
	#chatter .chatter_avatar.profile img {
		height: auto !important;
		width: 100% !important;
	}
	</style>
@stop

@section('content')

<div id="chatter" class="chatter_home">

	<div class="container chatter_container">

	    <div class="row">

	    	<div class="col-md-3 left-column">
	    		<!-- SIDEBAR -->
	    		<div class="row">
	    			<div class="col-xs-12">
			    		<div class="chatter_avatar profile">
			    			@if( ($userData->avatar) && file_exists(public_path()."/".$userData->avatar) )
		    					<img src="{{ asset($userData->avatar)  }}">
		    				@else
		    					<span class="chatter_avatar_circle" style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode($userData->email) ?>">
		        					{{ strtoupper(substr($userData->email, 0, 1)) }}
		        				</span>
		    				@endif
			    		</div>
	    			</div>
	    		</div>
	    		<div class="row">
	    			<div class="col-xs-12">
	    				<div class="chatter_sidebar">
	    					<a href="{{ \DevDojo\Chatter\Helpers\ChatterHelper::userLink($userData) }}">
	    				<strong class="chatter_middle_details">
	    						{{ ucfirst($userData->{Config::get('chatter.user.database_field_with_user_name')}) }}
	    				</strong>
	    					</a>
	    				</div>
	    			</div>
	    		</div>
	    		<div class="row">
	    			<div class="col-xs-12">
			    		<div class="chatter_sidebar">
							<a href="{{ route('profile', $userData->id) }}"><i class="chatter-bubble"></i> All User's {{ Config::get('chatter.titles.discussions') }}</a>
							<ul class="nav nav-pills nav-stacked">
								<?php //$categories = DevDojo\Chatter\Models\Models::category()->all(); ?>
								@foreach($categories as $category)
									<li><a href="{{ route('profile.category',[$userData->id,$category->slug]) }}" class="
									{{ (route('profile.category',[$userData->id,$category->slug]) == \Request::url()?'active':'') }}
									"><div class="chatter-box" style="background-color:{{ $category->color }}"></div> {{ $category->name }}</a></li>
								@endforeach
							</ul>
						</div>
	    			</div>
	    		</div>
				<!-- END SIDEBAR -->
	    	</div>
	        <div class="col-md-9 right-column">
	        	<div class="panel">
		        	<ul class="discussions">
		        		@if(count($discussions))
		        		@foreach($discussions as $discussion)
				        	<li>
				        		<a class="discussion_list" href="{{ url('/') }}{{ Config::get('chatter.routes.home') }}/{{ Config::get('chatter.routes.discussion') }}/{{ $discussion->category->slug }}/{{ $discussion->slug }}">
					        		<div class="chatter_avatar">
					        			@if(Config::get('chatter.user.avatar_image_database_field'))

					        				<?php $db_field = Config::get('chatter.user.avatar_image_database_field'); ?>

					        				<!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
					        				@if( ($discussion->user->{$db_field}) && file_exists(public_path()."/".$discussion->user->{$db_field}) )
					        					<img src="{{ asset($discussion->user->{$db_field})  }}">
					        				@else
					        					<span class="chatter_avatar_circle" style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode($discussion->user->email) ?>">
						        					{{ strtoupper(substr($discussion->user->email, 0, 1)) }}
						        				</span>
					        				@endif

					        			@else

					        				<span class="chatter_avatar_circle" style="background-color:#<?= \DevDojo\Chatter\Helpers\ChatterHelper::stringToColorCode($discussion->user->email) ?>">
					        					{{ strtoupper(substr($discussion->user->email, 0, 1)) }}
					        				</span>

					        			@endif
					        		</div>

					        		<div class="chatter_middle">
					        			<h3 class="chatter_middle_title">{{ $discussion->title }} <div class="chatter_cat" style="background-color:{{ $discussion->category->color }}">{{ $discussion->category->name }}</div></h3>
					        			<span class="chatter_middle_details">Posted By: <span data-href="/user">{{ ucfirst($discussion->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</span> {{ \Carbon\Carbon::createFromTimeStamp(strtotime($discussion->created_at))->diffForHumans() }}</span>
					        			@if(isset($discussion->post[0]))
						        			@if($discussion->post[0]->markdown)
						        				<?php $discussion_body = GrahamCampbell\Markdown\Facades\Markdown::convertToHtml( $discussion->post[0]->body ); ?>
						        			@else
						        				<?php $discussion_body = $discussion->post[0]->body; ?>
						        			@endif
					        			<p>{{ substr(strip_tags($discussion_body), 0, 200) }}@if(strlen(strip_tags($discussion_body)) > 200){{ '...' }}@endif</p>
					        			@endif
					        		</div>

					        		<div class="chatter_right">

					        			<div class="chatter_count"><i class="chatter-bubble"></i> {{ isset($discussion->postsCount[0])?$discussion->postsCount[0]->total:0 }}</div>
					        		</div>

					        		<div class="chatter_clear"></div>
					        	</a>
				        	</li>
			        	@endforeach
			        	@else
			        		<li>
			        			<a href="" class="discussion_list">
			        			<h3>No discussion found</h3>
			        			</a>
			        		</li>
			        	@endif
		        	</ul>
	        	</div>

	        	<div id="pagination">
	        		{{ $discussions->links() }}
	        	</div>

	        </div>
	    </div>
	</div>

</div>

@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
	<!-- <input type="hidden" id="chatter_tinymce_toolbar" value="{{ Config::get('chatter.tinymce.toolbar') }}">
	<input type="hidden" id="chatter_tinymce_plugins" value="{{ Config::get('chatter.tinymce.plugins') }}"> -->
@endif
<!-- <input type="hidden" id="current_path" value="{{ Request::path() }}"> -->

@endsection

@section(Config::get('chatter.yields.footer'))


@if( $chatter_editor == 'tinymce' || empty($chatter_editor) )
	<!-- <script src="/vendor/devdojo/chatter/assets/vendor/tinymce/tinymce.min.js"></script>
	<script src="/vendor/devdojo/chatter/assets/js/tinymce.js"></script>
	<script>
		var my_tinymce = tinyMCE;
		$('document').ready(function(){
			$('#tinymce_placeholder').click(function(){
				my_tinymce.activeEditor.focus();
			});
		});
	</script> -->
@elseif($chatter_editor == 'simplemde')
	<!-- <script src="/vendor/devdojo/chatter/assets/js/simplemde.min.js"></script>
	<script src="/vendor/devdojo/chatter/assets/js/chatter_simplemde.js"></script> -->
@elseif($chatter_editor == 'trumbowyg')
	<!-- <script src="/vendor/devdojo/chatter/assets/vendor/trumbowyg/trumbowyg.min.js"></script>
	<script src="/vendor/devdojo/chatter/assets/vendor/trumbowyg/plugins/preformatted/trumbowyg.preformatted.min.js"></script>
	<script src="/vendor/devdojo/chatter/assets/js/trumbowyg.js"></script> -->
@endif

<script src="/vendor/devdojo/chatter/assets/vendor/spectrum/spectrum.js"></script>
<script src="/vendor/devdojo/chatter/assets/js/chatter.js"></script>
<script>
	// $('document').ready(function(){

	// 	$('.chatter-close').click(function(){
	// 		$('#new_discussion').slideUp();
	// 	});
	// 	$('#new_discussion_btn, #cancel_discussion').click(function(){
	// 		@if(Auth::guest())
	// 			window.location.href = "{{ url('/') }}{{ Config::get('chatter.routes.home') }}/login";
	// 		@else
	// 			$('#new_discussion').slideDown();
	// 			$('#title').focus();
	// 		@endif
	// 	});

	// 	$("#color").spectrum({
	// 	    color: "#333639",
	// 	    preferredFormat: "hex",
	// 	    containerClassName: 'chatter-color-picker',
	// 	    cancelText: '',
 //    		chooseText: 'close',
	// 	    move: function(color) {
	// 			$("#color").val(color.toHexString());
	// 		}
	// 	});

	// 	@if (count($errors) > 0)
	// 		$('#new_discussion').slideDown();
	// 		$('#title').focus();
	// 	@endif

	// });


	
</script>
@stop
