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
    <a href="{{ route('categories.all') }}" title="{{ __('Product Sections') }}">{{ __('Product Sections') }}</a>
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
      <form method="POST" action="#" class="repeater" id="categoryForm">
        @csrf
        <input type="hidden" name="category_id" value="{{isset($id) ? $id : null}}">
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="Name_ar" class="d-block mb-2">{{ __('Name Ar') }}</label>
              <input name="name_ar" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name_ar" placeholder="{{__('Name Ar')}}">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="Name_en" class="d-block mb-2">{{ __('Name En') }}</label>
              <input name="name_en" type="text" class="onlyEng form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name_en" placeholder="{{__('Name En')}}">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="sort_number" class="d-block mb-2">{{ __('Sort No.') }} <span class="requirement text-danger">*</span></label>
              <input name="sort_number" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="sort_number" placeholder="{{__('Sort No.')}}">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="sel_1" class="d-block mb-2">{{__('Parent')}} <span class="requirement text-danger">*</span></label>
              <select id="sel_1" name="parent_id" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" multiple></select>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="inputEmail8" class="d-block mb-2">{{ __('Image') }}</label>
              <div class="upoadInput border rounded-3 position-relative overflow-hidden d-flex align-items-center justify-content-start">
                <input name="image" type="file" id="formFile" class="d-block position-absolute top-0 start-0 w-100 h-100" accept="image/png, image/jpeg, image/jpg" multiple>
                <input type="hidden" name="hidden_image" value="" />
                <div class="fileName h-100 d-flex align-items-center justify-content-start flex-grow-1 px-2"></div>
                <div class="fileBtn text-body d-flex align-items-center justify-content-center fw-bold">{{ __('Choose file') }}</div>
              </div><!-- upoadInput -->
              @if($errors->has('image'))
                <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('image') }}</span>
              @endif
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12">
            <div class="form-group mb-3">
              <div class="imgthumb p-2 m-0 border overflow-hidden rounded-3 position-relative d-flex align-items-center justify-content-center">
                <img id="catImg" src="" class="logo_image mw-100 mh-100" />
              </div>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12">
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
        </div><!-- row --> 
        <div class="saveBtn d-flex justify-content-start mt-3">
          <button type="button" id="SubmitForm" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold">{{__('Save')}}</button>
        </div><!-- saveBtn -->
      </form>
    </div><!-- blockArea -->
  </section><!-- productcreatePage -->
@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/select2/select2totree.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/select2/select2tree.js') }}?v={{ config('app.asset_version') }}"></script>
  <script>
    $("#sel_1").select2();

    $(document).ready(function(){
      var base_url = "{{url('/')}}";
      var category_id = $("input[name=category_id]").val();
      
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              'X-application-id' : 195,
              'X-application-secret' : 'aajO9ETFeqfaIiGgJLSp',
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

      if(category_id){
        $.ajax({
          type:'GET',
          url: base_url+"/api/v1/categories/"+category_id,
          success:function(category){
            var imgUrl = "{{Storage::url('categories/')}}";

            $("input[name=name_en]").val(category['name'].en);
            $("input[name=name_ar]").val(category['name'].ar);

            $('#sel_1').val(category['parent_id']);
            $('#sel_1').trigger('change');
                
            $("input[name=sort_number]").val(category['sort_number']);
            if(category['active'] == 1){
              $("input[name='active']").attr('checked', 'checked');
            }else{
              $("input[name='active']").removeAttr('checked', 'checked');
            }
            $('#catImg').attr('src', imgUrl+''+category["image"]);
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
        
        formData.append('image', $('#formFile')[0].files[0]);
        formData.append('name_en', $("input[name=name_en]").val());
        formData.append('name_ar', $("input[name=name_ar]").val());
        formData.append('parent_id', $("select[name=parent_id] option:selected").val());
        formData.append('sort_number', $("input[name=sort_number]").val());
        formData.append('active', active);

        requestUrl = "{{ route('categories.store') }}";
        if(category_id){
          requestUrl = base_url+"/api/v1/category/"+category_id+"/update";
        }

        $.ajax({
          type:'POST',
          url:requestUrl,
          data:formData,
          processData: false,
          contentType: false, 
          success:function(response){
            window.location.replace("{{ route('categories.all') }}");
          },
          error: function(error) {
            console.log(error);
          }
        });

      });
    });

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
