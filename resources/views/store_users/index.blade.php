@extends('layouts.app')

@section('title', __('Users'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Users')}}</span>
  </div><!-- breadcrump -->

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section id="usersIndexPage">
    <div class="title d-flex align-items-center justify-content-between mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Users')}}</h1>
      @can('create user')
        @include('store_users.create')
      @endcan
    </div><!-- title -->
    <div class="tabsArea d-flex align-items-center justify-content-start flex-wrap mb-3">
      <a href="{{route('roles.index')}}" title="{{__('Roles')}}" class="d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-3">{{__('Roles')}}</a>
    </div><!-- tabsArea -->
    @if(count($users) == 0)
      <div class="no_customers_yet d-flex align-items-center justify-content-center flex-column bg-white shadow-sm rounded-3 overflow-hidden mb-3">
        <i class="fal fa-users"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('No users') }}</span>
      </div><!-- no_customers_yet -->
    @else
      <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
        <div class="table-responsive">
          <table class="table table-striped table-hover text-nowrap">
              <thead>
              <tr>
                  <th scope="col">#</th>
                  <th scope="col">{{__('Name')}}</th>
                  <th scope="col">{{__('Mobile')}}</th>
                  <th scope="col">{{__('Email')}}</th>
                  <th scope="col">{{__('Gender')}}</th>
                  <th scope="col">{{__('Role')}}</th>
                  @canany(['update user', 'delete user'])
                  <th scope="col">{{__('Actions')}}</th>
                  @endcanany
              </tr>
              </thead>
              <tbody>
              <!-- foreach of products -->
                  @foreach($users as $user)
                      <tr>
                          <th scope="row">{{$loop->index+1}}</th>
                          <td>{{$user->name}}</td>
                          <td>{{$user->mobile}}</td>
                          <td>{{$user->email}}</td>
                          <td>{{$user->gender == 1 ? __('Male') : __('female')}}</td>
                          <td>{{$user->getRoleNames()->first()}}</td>
                          @canany(['update user', 'delete user'])
                          <td>
                              @can('update user')
                              <a href="{{ route('users.edit', $user->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Edit') }}">
                                  <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve" style="width: 15px; height: auto; fill: rgb(255, 255, 255);"><g><g><path d="M476.828,302.249c-10.794,0-19.542,8.748-19.542,19.542v151.125H39.087V54.718h151.125
                                      c10.794,0,19.542-8.748,19.542-19.542c0-10.794-8.748-19.542-19.542-19.542H19.545c-10.794,0-19.542,8.748-19.542,19.542v457.282
                                      C0.003,503.252,8.752,512,19.545,512h457.282c10.794,0,19.542-8.748,19.542-19.542V321.791
                                      C496.37,310.998,487.621,302.249,476.828,302.249z"></path></g></g> <g><g><path d="M506.271,75.426l-69.693-69.7C432.917,2.058,427.947,0,422.762,0c-5.185,0-10.149,2.058-13.816,5.726L178.35,236.321
                                      c-2.371,2.365-4.084,5.296-4.996,8.514l-27.359,97.059c-1.915,6.807-0.006,14.116,4.996,19.119
                                      c3.713,3.713,8.703,5.726,13.816,5.726c1.765,0,3.55-0.241,5.296-0.73l97.059-27.359c3.224-0.912,6.156-2.632,8.52-4.996
                                      l230.589-230.595C513.905,95.43,513.905,83.053,506.271,75.426z M251.658,302.412l-58.58,16.506l16.513-58.567L422.762,47.181
                                      l42.061,42.061L251.658,302.412z"></path></g></g> <g><g><rect x="208.103" y="235.027" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -134.3583 244.2405)" width="39.084" height="98.556"></rect></g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g> <g></g></svg>
                              </a>
                              @endcan
                              @can('delete user')
                              @include('store_users.delete', ['user' => $user])
                              @endcan
                          </td>
                          @endcanany
                      </tr>
                  @endforeach
              <!-- endforeach -->

              </tbody>
          </table>
        </div><!-- table-responsive -->
      </div><!-- blockArea -->
    @endif
  </section><!-- usersIndexPage -->
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\StoreUserRequest', '#user_form') !!}
@endpush
