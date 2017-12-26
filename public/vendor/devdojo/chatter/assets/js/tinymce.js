var chatter_tinymce_toolbar = $('#chatter_tinymce_toolbar').val();
var chatter_tinymce_plugins = $('#chatter_tinymce_plugins').val();

// Initiate the tinymce editor on any textarea with a class of richText
tinymce.init({
	selector:'textarea.richText',
	skin: 'chatter',
	plugins: chatter_tinymce_plugins,
	toolbar: chatter_tinymce_toolbar,
	menubar: false,
	statusbar: false,
	height : '220',
	content_css : '/vendor/devdojo/chatter/assets/css/chatter.css',
	template_popup_height: 380,
	setup: function (editor) {
        editor.on('init', function(args) {
        	// The tinymce editor is ready
            document.getElementById('new_discussion_loader').style.display = "none";
            if(!editor.getContent()){
                document.getElementById('tinymce_placeholder').style.display = "block";
            }
			document.getElementById('chatter_form_editor').style.display = "block";

            // check if user is in discussion view
            if ($('#new_discussion_loader_in_discussion_view').length > 0) {
                document.getElementById('new_discussion_loader_in_discussion_view').style.display = "none";
                document.getElementById('chatter_form_editor_in_discussion_view').style.display = "block";
            }
        });
        editor.on('keyup', function(e) {
        	content = editor.getContent();
        	if(content){
        		//$('#tinymce_placeholder').fadeOut();
        		document.getElementById('tinymce_placeholder').style.display = "none";
        	} else {
        		//$('#tinymce_placeholder').fadeIn();
        		document.getElementById('tinymce_placeholder').style.display = "block";
        	}
        });
    },
    image_title: true, 
    // enable automatic uploads of images represented by blob or data URIs
    automatic_uploads: true,
    // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
    // images_upload_url: 'postAcceptor.php',
    // here we add custom filepicker only to Image dialog
    file_picker_types: 'image', 
    // and here's our custom image picker
    file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        // Note: In modern browsers input[type="file"] is functional without 
        // even adding it to the DOM, but that might not be the case in some older
        // or quirky browsers like IE, so you might want to add it to the DOM
        // just in case, and visually hide it. And do not forget do remove it
        // once you do not need it anymore.

        input.onchange = function() {
          var file = this.files[0];
          var reader = new FileReader();
          reader.onload = function () {
          //   // Note: Now we need to register the blob in TinyMCEs image blob
          //   // registry. In the next release this part hopefully won't be
          //   // necessary, as we are looking to handle it internally.
            var id = 'blobid' + (new Date()).getTime();
            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            var base64 = reader.result.split(',')[1];
            var blobInfo = blobCache.create(id, file, base64);
            // console.log('blobInfo.blobUri()');
            console.log(blobInfo.blobUri());
            uploadImage(base64,function(result){
                console.log('here');
                console.log(result);
                if(result.respond == 'true')
                    cb(result.data, { title: file.name });
                else{
                    // TODO
                }
            });
          //   blobCache.add(blobInfo);

          //   // call the callback and populate the Title field with the file name
            // cb(blobInfo.blobUri(), { title: file.name });
          };
          reader.readAsDataURL(file);

            
        };

        input.click();
    }
  
});

function initializeNewTinyMCE(id){
    tinymce.init({
        selector:'#'+id,
        skin: 'chatter',
        plugins: chatter_tinymce_plugins,
        toolbar: chatter_tinymce_toolbar,
        menubar: false,
        statusbar: false,
        height : '300',
        content_css : '/vendor/devdojo/chatter/assets/css/chatter.css',
        template_popup_height: 380,
    images_upload_credentials: true,
    // without images_upload_url set, Upload tab won't show up
    images_upload_url: '{{ url("upload") }}',

    // we override default upload handler to simulate successful upload
    images_upload_handler: function (blobInfo, success, failure) {
    setTimeout(function() {
      // no matter what you upload, we will turn it into TinyMCE logo :)
      success('http://moxiecode.cachefly.net/tinymce/v9/images/logo.png');
    }, 2000);
    },
    });
}
