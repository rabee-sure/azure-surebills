@extends('layouts.app')

@section('title', __('Products'))

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

<div class="productsTabs d-flex align-items-center justify-content-center justify-content-md-start mb-4">
  <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="d-flex align-items-center justify-content-center shadow-sm {{ Request::is('products*') ? 'active' : '' }}">{{ __('Products') }}</a>
  <a href="{{ route('categories.all') }}" title="{{ __('Product Sections') }}" class="d-flex align-items-center justify-content-center shadow-sm {{ Request::is('categories*') ? 'active' : '' }}">{{ __('Product Sections') }}</a>
</div><!-- productsTabs -->


  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Products')}}</h1>
        <div class="top-right-button-container">
        <a href="{{ route('products.create')}}" class="btn btn-primary btn-md top-right-button mr-1" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Add Product') }}">{{ __('Add Product') }}</a>
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Products')}}</li>
        </ol>
      </nav>
      <div class="separator mt-3 mb-5"></div>
      </div>
    </div>
  </div>
  <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive" id="table-responsive">
          
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection

@push('footer-scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <script>
    var base_url = "{{url('/')}}";
    
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
        url:"{{ route('products.index') }}",
        success:function(products){
          console.log(products.data);
          if(products.data.length > 0){
            $("#table-responsive").append('<table id="prodTable" class="table table-striped">');
              $("#prodTable").append('<thead id="tblTh">');
                $("#tblTh").append('<tr id="thTr">');
                  $("#thTr").append('<th scope="col">#</th>');
                  $("#thTr").append('<th scope="col">{{__('Name')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Price')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Sort No.')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Category')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Status')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Actions')}}</th>');
                $("#tblTh").append('</tr>');
              $("#prodTable").append('</thead>');
              $("#prodTable").append('<tbody id="tblBody">');
              $.each(products.data, function( index, product ) {
                var imgUrl = "{{Storage::url('products/')}}";
                $("#tblBody").append('<tr id="bodyTr'+index+'">');
                  $("#bodyTr"+index).append('<th scope="row">'+product["id"]+'</th>');
                  $("#bodyTr"+index).append('<td>'+product["name"]+'</td>');
                  $("#bodyTr"+index).append('<td>'+product["price"]+'</td>');
                  $("#bodyTr"+index).append('<td>'+product["sort_number"]+'</td>');
                  $("#bodyTr"+index).append('<td>'+product["category"]+'</td>');
                  $("#bodyTr"+index).append('<td>'+product["active"]+'</td>');
                  $("#bodyTr"+index).append('<td id="tdActions'+index+'">');
                    $("#tdActions"+index).append('<a href="/products/'+product["id"]+'/edit" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Edit') }}">{{ __('Edit') }}</a>');
                    $("#tdActions"+index).append('<a href="javascript:;" onclick="return deleteItem('+product["id"]+')" class="btn btn-danger" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Delete') }}">{{ __('Delete') }}</a>');
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
      $.ajax({
        type:'DELETE',
        url:base_url+"/api/v1/products/"+id+"/delete",
        success:function(categories){
          window.location.replace("{{ route('products.all') }}");
        }
      });
    }
  </script>
@endpush