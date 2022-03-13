@extends('layouts.app')

@section('title', __('Categories'))

@section('css_styles')
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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

<div class="productsTabs d-flex align-items-center justify-content-center justify-content-md-start mb-4">
  <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="d-flex align-items-center justify-content-center shadow-sm {{ Request::is('products*') ? 'active' : '' }}">{{ __('Products') }}</a>
  <a href="{{ route('categories.all') }}" title="{{ __('Product Sections') }}" class="d-flex align-items-center justify-content-center shadow-sm {{ Request::is('categories*') ? 'active' : '' }}">{{ __('Product Sections') }}</a>
</div><!-- productsTabs -->

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Product Sections')}}</h1>
        <div class="top-right-button-container">
        <a href="{{ route('categories.create')}}" class="btn btn-primary btn-md top-right-button mr-1" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Add a product category') }}">{{ __('Add a product category') }}</a>
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Product Sections')}}</li>
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
          <!-- condition of product count -->

          <!-- else -->
            
          <!-- endif -->
          <!-- produvts pagination links -->
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection

@push('footer-scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <script>
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
        url:"{{ route('categories.index') }}",
        success:function(categories){
          console.log(categories.data.length);
          if(categories.data.length > 0){
            $("#table-responsive").append('<table id="catTable" class="table table-striped">');
              $("#catTable").append('<thead id="tblTh">');
                $("#tblTh").append('<tr id="thTr">');
                  $("#thTr").append('<th scope="col">#</th>');
                  $("#thTr").append('<th scope="col">{{__('Image')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Name Ar')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Name En')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Sort No.')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Parent')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Status')}}</th>');
                  $("#thTr").append('<th scope="col">{{__('Actions')}}</th>');
                  $("#tblTh").append('</tr>');
              $("#catTable").append('</thead>');
              $("#table-responsive").append('<tbody id="tblBody">');
              $.each(categories.data, function( index, category ) {
                $("#tblBody").append('<tr id="bodyTr'+index+'">');
                  $("#bodyTr"+index).append('<th scope="row">id</th>');
                  $("#bodyTr"+index).append('<td>name</td>');
                  $("#bodyTr"+index).append('<td>mobile</td>');
                  $("#bodyTr"+index).append('<td>email</td>');
                  $("#bodyTr"+index).append('<td>count</td>');
                  $("#bodyTr"+index).append('<td>created_at</td>');
                  $("#bodyTr"+index).append('<td>created_at</td>');
                  $("#bodyTr"+index).append('<td id="tdActions'+index+'">');
                    $("#tdActions"+index).append('<a href="{{ route('categories.edit', 1)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Edit') }}">{{ __('Edit') }}</a>');
                    $("#tdActions"+index).append('<a href="#" class="btn btn-danger" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Delete') }}">{{ __('Delete') }}</a>');
                  $("#bodyTr"+index).append('</td>');
                $("#tblBody").append('</tr>');
              });
              $("#table-responsive").append('</tbody>');
            $("#table-responsive").append('</table>');
          }
        }
      });
    });
  </script>
@endpush