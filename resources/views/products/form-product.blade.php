@extends('layouts.app')
@section('title', __($title))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <a href="{{ url('/products')}}" title="{{ __('Products') }}">{{ __('Products') }}</a>
    <i>/</i>
    <span>{{ __($title) }}</span>
  </div><!-- breadcrump -->

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div><!-- alert -->
  @endif

  <section id="productcreatePage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __($title) }}</h1>
    </div><!-- title -->
    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3 p-3">
      <form method="POST" action="#" id="productForm">
        @csrf
        <input type="hidden" name="product_id" value="{{isset($id) ? $id : null}}">
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="Name_ar" class="d-block mb-2">{{ __('Name Ar') }}</label>
              <input name="name_ar" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name_ar" placeholder="{{__('Name Ar')}}">
              <span id="name_ar-error" class="invalid-feedback"></span>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="Name_en" class="d-block mb-2">{{ __('Name En') }}</label>
              <input name="name_en" type="text" class="onlyEng form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name_en" placeholder="{{__('Name En')}}">
              <span id="name_en-error" class="invalid-feedback"></span>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="discription_ar" class="d-block mb-2">{{ __('Discription Ar') }}</label>
              <textarea class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="discription_ar" id="discription_ar" rows="3"></textarea>
              <span id="discription_ar-error" class="invalid-feedback"></span>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="discription_en" class="d-block mb-2">{{ __('Discription En') }}</label>
              <textarea class="onlyEng form-control shadow-none bg-white border w-100 rounded-3 text-body" name="discription_en" id="discription_en" rows="3"></textarea>
              <span id="discription_en-error" class="invalid-feedback"></span>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="price" class="d-block mb-2">{{ __('Price') }} <span class="requirement text-danger">*</span></label>
              <input name="price" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="price" placeholder="{{__('Price')}}">
              <span id="price-error" class="invalid-feedback"></span>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="sel_1" class="d-block mb-2">{{__('Category')}} <span class="requirement text-danger">*</span></label>
              <select id="sel_1" name="category_id" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" multiple></select>
              <span id="category_id-error" class="invalid-feedback"></span>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="sort_number" class="d-block mb-2">{{ __('Sort No.') }} <span class="requirement text-danger">*</span></label>
              <input name="sort_number" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="sort_number" placeholder="{{__('Sort No.')}}">
              <span id="sort_number-error" class="invalid-feedback"></span>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="inputEmail8" class="d-block mb-2">{{ __('Image') }}</label>
              <div class="upoadInput border rounded-3 position-relative d-flex align-items-center justify-content-start">
                <input name="image[]" type="file"  id="formFile" class="d-block position-absolute top-0 start-0 w-100 h-100" accept="image/png, image/jpeg, image/jpg" multiple>
                <input type="hidden" name="hidden_image" value="" />
                <div class="fileName h-100 d-flex align-items-center justify-content-start flex-grow-1 px-2"></div>
                <div class="fileBtn text-body d-flex align-items-center justify-content-center fw-bold">{{ __('Choose file') }}</div>
              </div><!-- upoadInput -->
              @if($errors->has('image'))
                <span id="image-error" class="invalid-feedback">{{ $errors->first('image') }}</span>
              @else
              <span id="image-error" class="invalid-feedback"></span>
              @endif
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12">
            <div class="form-group mb-3">
              <div class="d-flex align-items-center justify-content-start flex-wrap" id="products_images"></div>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-6">
            <div class="form-group mb-3">
              <label for="api_bill_style" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="active" class="position-absolute top-0 strat-0 w-100 h-100" id="api_bill_style" type="checkbox">
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Activate') }}
                </span>
              </label>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-6">
            <div class="form-group mb-3">
              <label for="customizations" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="enable_customizations" class="position-absolute top-0 strat-0 w-100 h-100" id="customizations" type="checkbox">
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Customizations') }}
                </span>
              </label>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="row after-add-more customization_row d-none">
            @if(isset($id))
            <input name="customization_id[]" type="hidden">
            @endif
            <div class="col-3 col-md-3">
              <div class="form-group mb-3">
                <label for="customization_name_ar" class="d-block mb-2">{{__('Name Ar')}}</label>
                <input name="customization_name_ar[]" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" placeholder="الاسم {{__('Name Ar')}}">
              </div><!-- form-group -->
            </div>
            <div class="col-3 col-md-3">
              <div class="form-group mb-3">
                <label for="customization_name_en" class="d-block mb-2">{{__('Name En')}}</label>
                <input name="customization_name_en[]" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" placeholder="{{__('Name En')}}">
              </div><!-- form-group -->
            </div>
            <div class="col-3 col-md-3">
              <div class="form-group mb-3">
                <label for="customization_price" class="d-block mb-2">{{__('Price')}}</label>
                <input name="customization_price[]" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" placeholder="{{__('Price')}}">
              </div><!-- form-group -->
            </div>
            <div class="col-3 col-md-3">
              <div class="input-group-btn mt-4">
                <button class="btn btn-success add-more" type="button">{{__('Add')}}</button>
              </div>
            </div>
          </div>

          <div class="copy d-none">
            <div class="row customization_row">
            @if(isset($id))
            <input name="customization_id[]" type="hidden">
            @endif
            <div class="col-3 col-md-3">
                <div class="form-group mb-3">
                  <label for="Name_ar" class="d-block mb-2">{{__('Name Ar')}}</label>
                  <input name="customization_name_ar[]" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" placeholder="الاسم {{__('Name Ar')}}">
                  <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('customization_name_ar.*') }}</span>
                </div><!-- form-group -->
              </div>
              <div class="col-3 col-md-3">
                <div class="form-group mb-3">
                  <label for="Name_ar" class="d-block mb-2">{{__('Name En')}}</label>
                  <input name="customization_name_en[]" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" placeholder="{{__('Name En')}}">
                  <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('customization_name_en.*') }}</span>
                </div><!-- form-group -->
              </div>
              <div class="col-3 col-md-3">
                <div class="form-group mb-3">
                  <label for="Name_ar" class="d-block mb-2">{{__('Price')}}</label>
                  <input name="customization_price[]" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" placeholder="{{__('Price')}}">
                  <span id="inputEmail8-error" class="invalid-feedback" style="display: inline; color:black;">{{ $errors->first('customization_price.*') }}</span>
                </div><!-- form-group -->
              </div>
              <div class="col-3 col-md-3">
                <div class="input-group-btn mt-4">
                  <button class="btn btn-danger remove" type="button">{{__('Remove')}}</button>
                </div>
              </div>
          </div>
          </div>



        </div><!-- row -->
        <div class="saveBtn d-flex justify-content-start mt-3">
          <button type="button" id="SubmitForm" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold">{{__('Save')}}</button>
        </div><!-- saveBtn -->
      </form>
    </div><!-- blockArea -->
  </section><!-- productcreatePage -->

@endsection

@push('footer-scripts')
  {{-- {!! JsValidator::formRequest('App\Http\Requests\ProductApiRequest', '#productForm') !!} --}}
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/select2/select2totree.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/select2/select2tree.js') }}?v={{ config('app.asset_version') }}"></script>
  <script>


$(document).ready(function() {

    $('#productForm').submit(false);

$("body").on("click",".add-more",function(){
    if($('.customization_row').length == 10)
    {
      alert('You reach the max number of customizations');
    }
    else{
      var html = $(".copy").html();
      $(".after-add-more").after(html);
    }
  });

  $("body").on("click",".remove",function(){
    $(this).parents(".customization_row").remove();
  });

});


    $('#customizations').on('change', function(){
        if($(this).is(':checked'))
        {
            $('.customization_row').removeClass('d-none');
            $('.customization_row').css('display', 'none');
            $('.customization_row').slideDown(500);
        }
        else
        {
            $('.customization_row').slideUp(500);
        }
    })

    var deletedImages = new Array();
    $("#sel_1").select2();

    $(document).ready(function(){
      var base_url = "{{url('/')}}";
      var product_id = $("input[name=product_id]").val();

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              'Accept' : 'application/json'
          }
      });

      // get categories options
      $.ajax({
        type:'GET',
        url:"{{ route('categories.top') }}",
        success:function(categories){
          $("#sel_1").select2ToTree({treeData: {dataArr:categories.data, labelFld: "name", incFld: "childiren"}, maximumSelectionLength: 1});
        }
      });

      if(product_id){
        var product_single_url = "{{ route('products.show', ':product_id') }}";
        $.ajax({
          type:'GET',
          url: product_single_url.replace(':product_id', product_id),
          success:function(product){
            var imgUrl = "{{Storage::url('products/')}}";

            $("input[name=name_en]").val(product['name'].en);
            $("input[name=name_ar]").val(product['name'].ar);

            $("textarea[name=discription_en]").val(product['discription'].en);
            $("textarea[name=discription_ar]").val(product['discription'].ar);

            $('#sel_1').val(product['category_id']);
            $('#sel_1').trigger('change');

            $("input[name=price]").val(product['price']);
            $("input[name=sort_number]").val(product['sort_number']);

            if(product['active'] == 1){
              $("input[name='active']").attr('checked', 'checked');
            }else{
              $("input[name='active']").removeAttr('checked', 'checked');
            }
            if(product['enable_customizations'] == 1){
              $("input[name='enable_customizations']").attr('checked', 'checked');
            }else{
              $("input[name='enable_customizations']").removeAttr('checked', 'checked');
            }

            if(product['customizations'].length > 0)
            {
                $.each( product['customizations'], function( idx, val ) {
                    if(idx > 0)
                    {
                        $(".add-more").click();
                    }
                    setTimeout(function(){
                        $('[name="customization_id[]"]:eq('+idx+')').val(val['id']);
                    $('[name="customization_name_ar[]"]:eq('+idx+')').val(val['name']['ar']);
                    $('[name="customization_name_en[]"]:eq('+idx+')').val(val['name']['en']);
                    $('[name="customization_price[]"]:eq('+idx+')').val(val['price']);

                    },100)
                });
            }
            $('#customizations').change();

            $.each(product["images"], function(imgindex, image){
              $('#products_images').append('<figure id="prodImg_'+image["id"]+'" class="p-2 border overflow-hidden rounded-3 position-relative align-items-center justify-content-center"><img src="'+imgUrl+''+image["image"]+'" class="mw-100 mh-100"><i class="fal fa-trash-alt position-absolute align-items-center justify-content-center rounded-1 btn-danger delete_logo" id="remove_'+image["id"]+'" onclick="deleteImage('+image["id"]+')"></i></figure>');
            });
          }
        });
      }

      //submit from
      $('#SubmitForm').click(function(){
        var formData = new FormData();

        let active = 0;
        if($("input[name='active']").prop("checked") == true){
          active = 1;
        }

        var totalfiles = document.getElementById('formFile').files.length;
        for (var index = 0; index < totalfiles; index++) {
          formData.append("image[]", document.getElementById('formFile').files[index]);
        }

        for (var i = 0; i < deletedImages.length; i++) {
          formData.append('deletedImages[]', deletedImages[i]);
        }

        var customizationNameAr = document.getElementsByName('customization_name_ar[]');
        var customizationNameEn = document.getElementsByName('customization_name_en[]');
        var customizationNamePrice = document.getElementsByName('customization_price[]');
        var customizationId = document.getElementsByName('customization_id[]');

        for( var i = 0; i < customizationNameAr.length; i++ )
        {
            if($(customizationNameAr[i]).is(":visible"))
            {
                formData.append('customization_name_ar[]', customizationNameAr[i].value);
                formData.append('customization_name_en[]', customizationNameEn[i].value);
                formData.append('customization_price[]', customizationNamePrice[i].value);
                if(customizationId.length > 0)
                {
                    formData.append('customization_id[]', customizationId[i].value);
                }
            }
        }

        formData.append('name_en', $("input[name=name_en]").val());
        formData.append('name_ar', $("input[name=name_ar]").val());
        formData.append('discription_en', $("textarea[name=discription_en]").val());
        formData.append('discription_ar', $("textarea[name=discription_ar]").val());
        formData.append('price', $("input[name=price]").val());
        formData.append('sort_number', $("input[name=sort_number]").val());
        formData.append('category_id', $("select[name=category_id] option:selected").val() ?? '');
        formData.append('active', active);
        formData.append('enable_customizations', $('#customizations').is(':checked') ? 1 : 0);

        requestUrl = "{{ route('products.store') }}";
        if(product_id){
          var product_update_url = "{{ route('products.update', ':product_id') }}";
          requestUrl = product_update_url.replace(':product_id', product_id);
        }

        $.ajax({
          type:'POST',
          url:requestUrl,
          data:formData,
          processData: false,
          contentType: false,
          beforeSend: function(){
            $('.invalid-feedback').empty();
          },
          success:function(response){
            window.location.replace("{{ route('products.all') }}");
          },
          error: function(error) {
            $.each(error.responseJSON.errors, function(index, value) {
                    if($('#'+index+'-error').length)
                    {
                        $('#'+index+'-error').show();
                        $('#'+index+'-error').text(value);
                    }
                    else if(index.match(/image.*/)) {
                        $('#image-error').show();
                        $('#image-error').text(value);
                    }
                });
          }
        });

      });
    });

    function deleteImage(id){
      deletedImages.push(id);
      $("#prodImg_"+id).hide();
      $("#remove_"+id).hide();
      console.log(deletedImages);
    }

    $('#formFile').bind('change', function () {
      var filename = $("#formFile").val();
      if (/^\s*$/.test(filename)) {
        $(".fileName").text("No file chosen...");
      }
      else {
        $(".fileName").text(filename.replace("C:\\fakepath\\", ""));
      }
    });
  </script>
@endpush
