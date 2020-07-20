@extends('andiwijaya::list-page')

@push('head')
  <script type="text/javascript" src="/js/tinymce/tinymce.min.js" defer></script>
@endpush

@push('body-post')

<script>
  function _(){ }

  function tinymce_init(selector){

    tinymce.init({
      selector: selector,
      height: 300,
      content_css:"{{ $content_css ?? '' }}",
      plugins: [ 'image', 'table', 'paste', 'link', 'lists' ],
      menubar:false,
      statusbar:false,
      paste_as_text:true,
      toolbar: 'table tabledelete | styleselect removeformat | bold italic strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | undo redo | image link',
      style_formats: [
        { title: 'Headings' },
        { title: 'Heading 1', block: 'h1' },
        { title: 'Heading 2', block: 'h2' },
        { title: 'Heading 3', block: 'h3' },
        { title: 'Heading 4', block: 'h4' },
        { title: 'Heading 5', block: 'h5' },
        { title: 'Image formats' },
        { title: 'Image Left', selector: 'img', styles: { 'float': 'left', 'margin': '0 10px 0 10px' } },
        { title: 'Image Right', selector: 'img', styles: { 'float': 'right', 'margin': '0 0 10px 10px' } },
        @if(isset($custom_tags) && is_array($custom_tags))
        { title: 'Custom' },
          @foreach($custom_tags as $custom_tag)
            { title: '{{ $custom_tag['title'] }}', block: 'pre', classes: '{{ $custom_tag['class'] }}' },
          @endforeach
        @endif
      ],
      image_title: true,
      automatic_uploads: true,
      images_upload_url: '/images/upload',
      file_picker_types: 'image',
      file_picker_callback: function(cb, value, meta) {

        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/!*');

        input.onchange = function() {
          var file = this.files[0];

          var reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = function () {
            var id = 'blobid' + (new Date()).getTime();
            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            var base64 = reader.result.split(',')[1];
            var blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);
            cb(blobInfo.blobUri(), { title: file.name });
          };
        };
        input.click();
      },
      images_upload_handler: function (blobInfo, success, failure, progress) {
        var xhr, formData;

        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '/images/upload');

        xhr.upload.onprogress = function (e) {
          progress(e.loaded / e.total * 100);
        };

        xhr.onload = function() {
          var json;

          if (xhr.status < 200 || xhr.status >= 300) {
            failure('HTTP Error: ' + xhr.status);
            return;
          }

          json = JSON.parse(xhr.responseText);

          if(json){
            if(typeof json.error !== 'undefined'){
              failure(json.message);
            }
            else if(typeof json.location !== 'undefined')
              success(json.location);
          }
          else
            failure('Invalid JSON: ' + xhr.responseText);

        };

        xhr.onerror = function () {
          failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        };

        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        xhr.send(formData);
      },
      table_default_attributes: { border: '0' },
      setup: function (editor) {
        editor.on('change', function () {
          editor.save();
        });
      }
    });

  }

  function tinymce_init_item(item){

    $('.section-html', item).each(function(){

      this.id = 'section_html_' + $.uniqid();
      tinymce_init('#' + this.id);

    })

  }
</script>

@endpush