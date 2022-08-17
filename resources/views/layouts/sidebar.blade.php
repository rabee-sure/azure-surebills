@auth

  @php
    $statues = session('status_filters', ['pending', 'paid'])?? [];
    $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
  @endphp

  <aside class="bg-white position-fixed end-0 d-print-none">
    <a href="/" title="{{ __('Dashboard') }}"  class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('home') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-tachometer-alt-fast"></i>
      <span class="text-center">{{ __('Dashboard') }}</span>
    </a>
    @can('show bills')
      <a href="/bills?{{$separated}}" title="{{ __('Bills') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('bills*') ? 'active' : '' }}">
        <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-file-invoice"></i>
        {{ __('Bills') }}
      </a>
    @endcan
    {{-- <a href="/pos/categories" title="{{ __('POS') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('pos*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-cash-register"></i>
      {{ __('POS') }}
    </a> --}}
    @can('show statement')
      <a href="{{ route('statement.index') }}" title="{{ __('Electronic payment') }}" class="d-flex text-center align-items-center justify-content-center text-center flex-column rounded w-100 {{ Request::is('statement*') ? 'active' : '' }}">
        <div class="icon flex-shrink-0 statementIcon"></div>
        {{ __('Electronic payment') }}
      </a>
    @endcan
    @can('show payment record')
      {{-- <a href="{{ route('reports.paymentRecord') }}" title="{{ __('Payment Record') }}" class="d-flex text-center align-items-center justify-content-center text-center flex-column rounded w-100 {{ Request::is('payment_record*') ? 'active' : '' }}">
        <div class="icon flex-shrink-0 paymentRecordIcon"></div>
        {{ __('Payment Record') }}
      </a> --}}
    @endcan
    @can('show customers')
      <a href="{{ route('customers.index') }}" title="{{ __('Customers') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('customers*') ? 'active' : '' }}">
        <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-users"></i>
        {{ __('Customers') }}
      </a>
    @endcan
    @can('show transfers')
      <a href="{{ route('transfers.index') }}" title="{{ __('Transfers') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('transfers*') ? 'active' : '' }}">
        <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-sack-dollar"></i>
        {{ __('Transfers') }}
      </a>
    @endcan
    <a href="{{ route('account') }}" title="{{__('Settings')}}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('account*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-user-cog"></i>
      {{__('Settings')}}
    </a>
    {{-- <a href="{{ route('pricing') }}" title="{{__('Pricing')}}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('pricing*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-user-cog"></i>
      {{__('Pricing')}}
    </a> --}}
    {{-- @if(in_array(Auth::user()->email, explode(',', env('NOVA_ALLOWED_ADMINS'))))
      <a href="{{ route('reports.index') }}" title="{{ __('Reports') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('reports*') ? 'active' : '' }}">
        <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-file-chart-line"></i>
        {{ __('Reports') }}
      </a>
    @endif --}}
    {{-- <a href="{{ route('orders.all') }}" title="{{ __('Orders') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('orders*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-file-chart-line"></i>
      {{ __('Orders') }}
    </a>
    <a href="{{ route('products.settings') }}" title="{{ __('Store Settings') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('products*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-file-chart-line"></i>
      {{ __('Store Settings') }}
    </a> --}}
  </aside>{{-- aside --}}

@endauth
