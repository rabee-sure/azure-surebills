@extends('layouts.app')

@section('title', __('Products'))

@section('content')

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Product Sections') }}</h1>
        <div class="text-zero top-right-button-container">
          <button type="button" class="btn btn-primary btn-md top-right-button"  data-toggle="modal" data-target="#add_categorie_Modal">{{ __('Add Categorie')}}</button>
        </div><!-- text-zero -->
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="{{ url('/') }}">{{ __('Home')}}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Product Sections') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <table class="table">
            <thead class="thead-light">
              <tr>
                <th scope="col">اسم القسم</th>
                <th scope="col">الترتيب</th>
                <th scope="col">الحالة</th>
                <th scope="col">خيارات</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>اسم القسم هنا</td>
                <td>2</td>
                <td><span class="badge badge-pill badge-primary">{{ __('Enabled') }}</span></td>
                <td>
                  <button type="button" class="btn btn-primary btn-xs mx-1">تعديل</button>
                  <button type="button" class="btn btn-danger btn-xs mx-1">حذف</button>
                </td>
              </tr>
              <tr>
                <td>اسم القسم هنا</td>
                <td>2</td>
                <td><span class="badge badge-pill badge-danger">{{ __('Disabled') }}</span></td>
                <td>
                  <button type="button" class="btn btn-primary btn-xs mx-1">تعديل</button>
                  <button type="button" class="btn btn-danger btn-xs mx-1">حذف</button>
                </td>
              </tr>
              <tr>
                <td>اسم القسم هنا</td>
                <td>2</td>
                <td><span class="badge badge-pill badge-primary">{{ __('Enabled') }}</span></td>
                <td>
                  <button type="button" class="btn btn-primary btn-xs mx-1">تعديل</button>
                  <button type="button" class="btn btn-danger btn-xs mx-1">حذف</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="add_categorie_Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="add_categorie_ModalLabel">{{ __('Add Categorie') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form method="POST" action="#" id="add_categorie_form">
          <div class="modal-body">
            <div class="form-group">
              <label for="Department_Name">{{__('Department Name')}}</label>
              <input name="name" type="text" class="form-control" id="Department_Name" placeholder="{{__('Department Name')}}">
            </div>
            <div class="form-group">
              <label for="Arrangement">{{__('Arrangement')}}</label>
              <input name="name" type="text" class="form-control" id="Arrangement" placeholder="{{__('Arrangement')}}">
            </div>
            <div class="form-group">
              <label for="Status">{{__('Status')}}</label>
              <select value="#" name="#" class="form-control">
                <option value="1">{{ __('Enabled') }}</option>
                <option value="2">{{ __('Disabled') }}</option>
                </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary login_button">{{__('Add')}}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal -->


@endsection