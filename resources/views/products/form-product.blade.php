@extends('layouts.app')
@section('title', __($title))

@section('css_styles')
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2-bootstrap.min.css') }}" />
@endsection

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

  <div class="row">
    <div class="col-12">
      <h1>{{ __($title) }}</h1>
      <div class="separator mb-5"></div>
    </div>
    <div class="col-12">
      <div class="create_bill_page card mb-4">
        
        <div class="card-body" >
          <form method="POST" action="#" class="repeater" id="categoryForm">
            @csrf

            <input type="hidden" name="product_id" value="{{isset($id) ? $id : null}}">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>{{ __('Name Ar') }}</label>
                <input name="name_ar" type="text" class="form-control" id="Name_ar" placeholder="{{__('Name Ar')}}">
              </div><!-- form-group -->
              <div class="form-group col-md-6" >
                <label>{{ __('Name En') }}</label>
                <input name="name_en" type="text" class="form-control" id="Name_en" placeholder="{{__('Name En')}}">
              </div><!-- form-group -->
            </div><!-- form-row -->

            <div class="form-row">
                <div class="form-group col-md-6">
                  <label>{{ __('Discription Ar') }}</label>
                  <textarea class="form-control" name="discription_ar" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div><!-- form-group -->
                <div class="form-group col-md-6" >
                  <label>{{ __('Discription En') }}</label>
                  <textarea class="form-control" name="discription_en" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div><!-- form-group -->
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                  <label for="price">{{ __('Price') }} <span class="requirement">*</span></label>
                  <div class="input-group phone_inputs">
                      <input name="price" type="number" class="form-control" id="price" placeholder="{{__('Price')}}">
                  </div>
              </div>

              <div class="form-group col-md-6">
                  <label for="inputEmail5">{{__('Category')}} <span class="requirement">*</span></label>
                  <select id="sel_1" name="category_id" style="width:16em" multiple></select>
              </div> 
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                  <label for="sort_number">{{ __('Sort No.') }} <span class="requirement">*</span></label>
                  <div class="input-group phone_inputs">
                      <input name="sort_number" type="number" class="form-control" id="sort_number" placeholder="{{__('Sort No.')}}">
                  </div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail8">{{ __('Image') }}</label>
                <div class="custom-file">
                    <input name="image[]" type="file"  id="formFile" class="custom-file-input" accept="image/png, image/jpeg, image/jpg" multiple>
                    <input type="hidden" name="hidden_image" value="" />
                    <label class="custom-file-label" for="inputEmail8">{{ __('Choose file') }}</label>
                    @if($errors->has('image'))
                        <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('image') }}</span>
                    @endif
                </div>
              </div>
              <div class="form-group col-md-6">
                  <div class="custom-file" id="products_images">
                      
                  </div>
              </div>
            </div>

            <!-- <div class="form-row">
              <div class="form-group col-md-6">
              <h5 class="mb-2 mt-2">{{ __('Image') }}</h5>
              <div class="dropzone"></div>
              </div>
            </div> -->
            
            <div class="form-row">
              <div class="form-group col-12">
                <label for="api_bill_style">{{ __('Activate') }}</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input name="active" class="custom-switch-input" id="api_bill_style" type="checkbox">
                  <label class="custom-switch-btn" for="api_bill_style"></label>
                </div>
              </div><!-- form-group -->
            </div><!-- form-row -->
            
            <hr>

            <div class="d-flex justify-content-start mt-3">
              <button type="button" id="SubmitForm" class="btn btn-primary btn-lg login_button"> {{__('Save')}}</button>
            </div><!-- d-flex  -->
          </form>
        </div>
       
      </div>
    </div>
  </div>
@endsection

@push('footer-scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript" src="{{ asset('js/select2.full.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/select2totree.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/select2tree.js') }}"></script>
  <script>
    var deletedImages = new Array();
    $("#sel_1").select2();

    $(document).ready(function(){
      var base_url = "{{url('/')}}";
      var product_id = $("input[name=product_id]").val();
      
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              'X-application-id' : 390,
              'X-application-secret' : '8sIFHOEwpIVN2X5Pida1',
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
        $.ajax({
          type:'GET',
          url: base_url+"/api/v1/products/"+product_id,
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
            console.log(product["images"]);
            $.each(product["images"], function(imgindex, image){
              $('#products_images').append('<img id="prodImg_'+image["id"]+'" src="'+imgUrl+''+image["image"]+'" class="img-thumbnail logo_image" width="100" /><i class="glyph-icon simple-icon-trash delete_logo" id="remove_'+image["id"]+'" onclick="deleteImage('+image["id"]+')"></i>');
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

        formData.append('name_en', $("input[name=name_en]").val());
        formData.append('name_ar', $("input[name=name_ar]").val());
        formData.append('discription_en', $("textarea[name=discription_en]").val());
        formData.append('discription_ar', $("textarea[name=discription_ar]").val());
        formData.append('price', $("input[name=price]").val());
        formData.append('sort_number', $("input[name=sort_number]").val());
        formData.append('category_id', $("select[name=category_id] option:selected").val());
        formData.append('active', active);

        requestUrl = "{{ route('products.store') }}";
        if(product_id){
          requestUrl = base_url+"/api/v1/products/"+product_id+"/update";
        }

        $.ajax({
          type:'POST',
          url:requestUrl,
          data:formData,
          processData: false,
          contentType: false, 
          success:function(response){
            window.location.replace("{{ route('products.all') }}");
          },
          error: function(error) {
            console.log(error);
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
  </script>
@endpush
