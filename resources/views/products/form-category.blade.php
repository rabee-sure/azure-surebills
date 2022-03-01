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
          <form method="POST" action="#" class="repeater" id="settings">
            @csrf
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
                  <label for="sort_number">{{ __('Sort No.') }} <span class="requirement">*</span></label>
                  <div class="input-group phone_inputs">
                      <input name="sort_number" type="number" class="form-control" id="sort_number" placeholder="{{__('Sort No.')}}">
                  </div>
              </div>
              <div class="form-group col-md-6">
                  <label for="inputEmail5">{{__('Parent')}} <span class="requirement">*</span></label>
                  <select name="parent_id" id="inputEmail5" class="form-control">
                    <option value="" disabled selected>{{__('Select parent category')}}</option>
                  </select>
              </div> 
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                  <label for="inputEmail5">{{__('Parent')}} <span class="requirement">*</span></label>
                  <select id="sel_1" style="width:16em" multiple></select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
              <label for="inputEmail8">{{ __('Image') }}</label>
              <div class="custom-file">
                  <input name="image" type="file" class="custom-file-input" accept="image/png, image/jpeg, image/jpg">
                  <input type="hidden" name="hidden_image" value="" />
                  <label class="custom-file-label" for="inputEmail8">{{ __('Choose file') }}</label>
                  @if($errors->has('image'))
                      <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('image') }}</span>
                  @endif
              </div>
              </div>
              <div class="form-group col-md-6">
                  <div class="custom-file">
                  @if(auth()->user()->logo)
                      <img src="@if(Storage::disk('public')->has(auth()->user()->logo)) {{url('storage/'.auth()->user()->logo)}} @else {{url(auth()->user()->logo)}} @endif" class="img-thumbnail logo_image" width="100" />
                      <i class="glyph-icon simple-icon-trash delete_logo"></i>

                  @endif
                  </div>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group col-12">
                <label for="api_bill_style">{{ __('Activate simple style for API bills?') }}</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input name="api_bill_style" class="custom-switch-input" id="api_bill_style" type="checkbox">
                  <label class="custom-switch-btn" for="api_bill_style"></label>
                </div>
              </div><!-- form-group -->
            </div><!-- form-row -->
            
            <hr>

            <div class="d-flex justify-content-start mt-3">
              <button type="submit" class="btn btn-primary btn-lg login_button"> {{__('Save')}}</button>
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
    $("#sel_1").select2();
    var categoriesTree = [
      // {id:1, text:"USA", inc:[
      //     {text:"west", inc:[
      //       {id:111, text:"California", inc:[
      //           {id:1111, text:"Los Angeles", inc:[
      //             {id:11111, text:"Hollywood"}
      //           ]},
      //           {id:1112, text:"San Diego"}
      //       ]},
      //       {id:112, text:"Oregon"}
      //     ]}
      // ]},
      // {id:2, text:"India"},
      // {id:3, text:"中国"}
    ];

    $(document).ready(function(){

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
            console.log(categories.data);
            $.each(categories.data, function(index, object){
              console.log(object.childiren.length);
              setChildren(object);
            });
            console.log(categoriesTree);
            $("#sel_1").select2ToTree({treeData: {dataArr:categoriesTree}, maximumSelectionLength: 1});
          }
      });

      function setChildren(category) {
        element = {id:category.id, text:category.name};
          if (Array.isArray(category.childiren) && category.childiren.length){
            console.log('zain');
            element.inc = [];
            $.each(category.childiren, function(index, object){
              console.log(object);
              subelement = {id:object.id, text:object.name};
              categoriesTree.push(element);
              setChildren(object.childiren);
            });
          }
      }
    });

  </script>
@endpush
