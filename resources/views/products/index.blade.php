@extends('layouts.app')

@section('title', __('Products'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Products')}}</span>
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
      <div class="tabsArea d-flex align-items-center justify-content-center justify-content-md-start flex-wrap mb-5 mb-md-4">
        @can('show products')
          <span class="d-flex shadow-none align-items-center justify-content-center border bg-white text-body rounded-3">{{__('Products')}}</span>
        @endcan
        @can('show product categories')
          <a href="{{ route('categories.all') }}" title="{{ __('Product Sections') }}" class="d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-3">{{ __('Product Sections') }}</a>
        @endcan
      </div><!-- tabsArea -->
    @endcanany
    <div class="title d-flex align-items-center justify-content-between mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Products')}}</h1>
      @can('create product')
        <a href="{{ route('products.create')}}" class="addProductBtn d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-pill" title="{{ __('Add Product') }}">{{ __('Add Product') }}</a>
      @endcan
    </div><!-- title -->
    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
      <div class="table-responsive" id="table-responsive"></div>
    </div><!-- blockArea -->
  </section><!-- productsIndexPage -->

@endsection

@push('footer-scripts')
  <script>
    
    $(document).ready(function(){
      var base_url = "{{url('/')}}";
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              'Accept' : 'application/json'
          }
      });

      // get categories options
      $.ajax({
        type:'GET',
        url:"{{ route('products.index') }}",
        success:function(products){
          console.log(products.data);
          if(products.data.length > 0){
            $("#table-responsive").append('<table id="prodTable" class="table table-striped table-hover text-nowrap">');
              $("#prodTable").append('<thead id="tblTh">');
                $("#tblTh").append('<tr id="thTr">');
                  $("#thTr").append('<th scope="col" class="text-center">#</th>');
                  $("#thTr").append('<th scope="col" class="text-center">{{__('Name')}}</th>');
                  $("#thTr").append('<th scope="col" class="text-center">{{__('Price')}}</th>');
                  $("#thTr").append('<th scope="col" class="text-center">{{__('Sort No.')}}</th>');
                  $("#thTr").append('<th scope="col" class="text-center">{{__('Category')}}</th>');
                  $("#thTr").append('<th scope="col" class="text-center">{{__('Status')}}</th>');
                  $("#thTr").append('<th scope="col" class="text-center"></th>');
                $("#tblTh").append('</tr>');
              $("#prodTable").append('</thead>');
              $("#prodTable").append('<tbody id="tblBody">');
              $.each(products.data, function( index, product ) {
                var imgUrl = "{{Storage::url('products/')}}";
                $("#tblBody").append('<tr id="bodyTr'+index+'">');
                  $("#bodyTr"+index).append('<td class="text-center">'+product["id"]+'</td>');
                  $("#bodyTr"+index).append('<td class="text-center">'+product["name"]+'</td>');
                  $("#bodyTr"+index).append('<td class="text-center">'+product["price"]+'</td>');
                  $("#bodyTr"+index).append('<td class="text-center">'+product["sort_number"]+'</td>');
                  $("#bodyTr"+index).append('<td class="text-center">'+product["category"]+'</td>');
                  $("#bodyTr"+index).append('<td class="text-center">'+product["active"]+'</td>');
                  $("#bodyTr"+index).append('<td class="text-center" id="tdActions'+index+'">');
                    $("#tdActions"+index).append('<div id="ActionsBtns'+index+'" class="d-flex align-items-center justify-content-center">');
                      $("#ActionsBtns"+index).append('<a href="/products/'+product["id"]+'/edit" class="rounded-3 border-0 shadow-none p-0 btn-primary d-flex align-items-center justify-content-center mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}"><i class="fal fa-edit"></i></a>');
                      $("#ActionsBtns"+index).append('<a href="javascript:;" onclick="return deleteItem('+product["id"]+')" class="rounded-3 border-0 shadow-none p-0 mx-1 btn-danger d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete') }}"><i class="fal fa-trash-alt"></i></a>');
                    $("#tdActions"+index).append('</div>');
                  $("#bodyTr"+index).append('</td>');
                $("#tblBody").append('</tr>');
              });
              $("#prodTable").append('</tbody>');
            $("#table-responsive").append('</table>');
          }
        }
      });
    });

    function deleteItem(id){
      var product_delete_url = "{{ route('products.delete', ':id') }}";
      $.ajax({
        type:'DELETE',
        url:product_delete_url.replace(':id', id),
        success:function(categories){
          window.location.replace("{{ route('products.all') }}");
        }
      });
    }
  </script>
@endpush
