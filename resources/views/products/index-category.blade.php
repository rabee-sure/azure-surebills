@extends('layouts.app')

@section('title', __('Categories'))

@section('css_styles')
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Product Sections')}}</span>
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

  <section id="productsIndexPage">
    @canany(['show products', 'show product categories'])
      <div class="tabsArea d-flex align-items-center justify-content-start flex-wrap mb-4">
        @can('show products')
          <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-3">{{ __('Products') }}</a>
        @endcan
        @can('show product categories')
          <span class="d-flex shadow-none align-items-center justify-content-center border bg-white text-body rounded-3">{{ __('Product Sections') }}</span>
        @endcan
      </div><!-- tabsArea -->
    @endcanany
    <div class="title d-flex align-items-center justify-content-between mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Product Sections')}}</h1>
      @can('create product category')
        <a href="{{ route('categories.create')}}" class="addProductBtn d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-pill" title="{{ __('Add a product category') }}">{{ __('Add a product category') }}</a>
      @endcan
    </div><!-- title -->
    <div class="blockArea">
      <div class="table-responsive" id="table-responsive"></div>
    </div><!-- blockArea -->
  </section><!-- productsIndexPage -->
@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/jquery-ui/jquery-ui.js') }}?v={{ config('app.asset_version') }}" defer></script>

  <script>
    var base_url = "{{url('/')}}";
    
    $(document).ready(function(){
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
        url:"{{ route('categories.index') }}",
        success:function(categories){
          console.log(categories.data);
          if(categories.data.length > 0){
            $("#table-responsive").append('<table id="catTable" class="table table-striped">');
              $("#catTable").append('<thead id="tblTh">');
                $("#tblTh").append('<tr id="thTr">');
                  $("#thTr").append('<th scope="col">#</th>');
                  $("#thTr").append('<th scope="col">{{__('Image')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Name')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Sort No.')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Parent')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Status')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Actions')}}</th>');
                $("#tblTh").append('</tr>');
              $("#catTable").append('</thead>');
              $("#catTable").append('<tbody id="tblBody">');
              $.each(categories.data, function( index, category ) {
                var imgUrl = "{{Storage::url('categories/')}}";
                $("#tblBody").append('<tr id="bodyTr'+index+'">');
                  $("#bodyTr"+index).append('<th scope="row">'+category["id"]+'</th>');
                  $("#bodyTr"+index).append('<td><img style="width: 100px;" src="'+imgUrl+''+category["image"]+'" ></td>');
                  $("#bodyTr"+index).append('<td>'+category["name"]+'</td>');
                  $("#bodyTr"+index).append('<td>'+category["sort_number"]+'</td>');
                  $("#bodyTr"+index).append('<td>'+category["parent"]+'</td>');
                  $("#bodyTr"+index).append('<td>'+category["active"]+'</td>');
                  $("#bodyTr"+index).append('<td id="tdActions'+index+'">');
                    $("#tdActions"+index).append('<a href="/categories/'+category["id"]+'/edit" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Edit') }}">{{ __('Edit') }}</a>');
                    $("#tdActions"+index).append('<a href="javascript:;" onclick="return deleteItem('+category["id"]+')" class="btn btn-danger" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Delete') }}">{{ __('Delete') }}</a>');
                  $("#bodyTr"+index).append('</td>');
                $("#tblBody").append('</tr>');
              });
              $("#catTable").append('</tbody>');
            $("#table-responsive").append('</table>');
          }
        }
      });
    });

    function deleteItem(id){
      $.ajax({
        type:'DELETE',
        url:base_url+"/api/v1/category/"+id+"/delete",
        success:function(categories){
          window.location.replace("{{ route('categories.all') }}");
        }
      });
    }
  </script>
@endpush
