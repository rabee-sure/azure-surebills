@extends('layouts.bill')
@section('title', __('Bill No.') . ' ' . $bill->number)

@section('content')

<div class="singlebBillSimple_page py-4 min-vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-5">

        <div class="change_lang d-flex align-items-center justify-content-end w-100 mb-2">
          @if($bill->user->settings->active_lang == 'all')
            @if(App::isLocale('en'))
              <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'ar'])}}" title="عربي" class="d-block fw-medium text-capitalize">عربي</a>
            @else
              <a href="{{ route('paybillpagelang', ['id' => $bill->pay_id, 'lang' => 'en'])}}" title="English" class="d-block fw-medium text-capitalize">English</a>
            @endif
          @endif
        </div><!-- change_lang -->

        <div class="card">
          <div class="card-body p-3">

            @if($bill->user->logo)
              <span class="app-brand-logo d-flex align-items-center justify-content-center mb-3">
                <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="w-auto" height="32px">
              </span>
            @endif
            <div class="text-heading mb-3 d-flex flex-column text-center">
              @if($bill->user->settings->add_tax_invoice)
                <p class="m-0">@if($bill->debit_note_bill_id == null) {{ __('Simplified Tax Invoice') }} @else {{ __('Tax debit note') }} @endif</p>
              @endif
              <p class="m-0">{{ $bill->user->business_name }}</p>
              @if(isset($bill->user->settings->header_bill))
                <p class="m-0">{{ $bill->user->settings->header_bill }}</p>
              @endif
              <p class="m-0">{{  $bill->user->business_address }}</p>
              <p class="m-0">{{  $bill->user->business_mobile }}</p>
            </div>

            @if($bill->application_id && !$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
              <div class="countdown alert alert-warning d-flex align-items-center justify-content-center gap-1 text-capitalize mb-3" id="new_countdown">
                <p class="mb-0 text-warning">{{ __('the bill will expire in')}}</p>
                <b id="hm_timer" class="text-warning"></b>
              </div><!-- countdown -->
            @endif

            @if($bill->status == 'expired')
              <div id="status">
                <div class="alert alert-danger text-danger text-center text-capitalize mb-3" role="alert"> {{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>
              </div><!-- status -->
            @elseif(in_array($bill->status, ['paid', 'refunded']))
              <div id="status">
                <div class="alert alert-success text-success text-center text-capitalize mb-3" role="alert">
                  @if ($bill->depositTransaction)
                    {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
                  @else
                    {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
                  @endif
                </div>
              </div><!-- status -->
            @elseif(in_array($bill->status, ['paid_cash', 'refunded_cash']))
              <div id="status">
                <div class="alert alert-success text-success text-center text-capitalize mb-3" role="alert"> {{ __('this bill has been Paid Cash successfully', ['number' => $bill->number ]) }}</div>
              </div><!-- status -->
            @elseif(in_array($bill->status, ['paid_bank_transfer', 'refunded_bank_transfer']))
              <div id="status">
                <div class="alert alert-success text-success text-center text-capitalize mb-3" role="alert"> {{ __('this bill has been Paid Bank Transfer successfully', ['number' => $bill->number ]) }}</div>
              </div><!-- status -->
            @elseif(in_array($bill->status, ['paid_machine', 'refunded_machine']))
              <div id="status">
                <div class="alert alert-success text-success text-center text-capitalize mb-3" role="alert"> {{ __('this bill has been Paid Machine successfully', ['number' => $bill->number ]) }}</div>
              </div><!-- status -->
            @elseif($bill->status == 'canceled')
              <div id="status">
                <div class="alert alert-danger text-danger text-center text-capitalize mb-3" role="alert"> {{ __('this bill has been canceled', ['number' => $bill->number ]) }}</div>
              </div><!-- status -->
            @elseif($bill->status == 'failed')
              <div id="status">
                <div class="alert alert-danger text-danger text-center text-capitalize mb-3" role="alert"> {{ __('this bill has been failed', ['number' => $bill->number ]) }}</div>
              </div><!-- status -->
            {{-- @elseif(in_array($bill->status, ['refunded', 'refunded_cash', 'refunded_bank_transfer']))
              <div id="status">
                <div class="alert alert-warning text-warning text-center text-capitalize mb-3" role="alert"> {{ __('this bill has been refunded', ['number' => $bill->number ]) }}</div>
              </div><!-- status --> --}}
            @elseif($bill->status == 'rejected')
              <div id="status">
                <div class="alert alert-danger text-danger text-center text-capitalize mb-3" role="alert"> {{ __('this bill has been rejected', ['number' => $bill->number ]) }}</div>
              </div>
            @endif
            @if(isset($errors) && $errors->any())
              <div class="alert alert-danger text-danger text-center text-capitalize mb-3" role="alert">{{ __($errors->first()) }}</div>
            @endif

            <div class="d-flex flex-column gap-1 mb-3">
              @if($bill->user->settings->add_tax_invoice)
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('Bill No.') }}</p>
                  <p class="mb-0">{{ $bill->number }}</p>
                </div><!-- d-flex -->
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('Date') }}</p>
                  <p class="mb-0">{{ $bill->created_at->format('d/m/Y')}}</p>
                </div><!-- d-flex -->
                @if($bill->user->vat_registration_number)
                  <div class="d-flex align-items-center justify-content-between gap-2">
                    <p class="mb-0">{{ __('Organization VAT Registration Number') }}</p>
                    <p class="mb-0">{{ $bill->user->vat_registration_number }}</p>
                  </div><!-- d-flex -->
                @endif
              @else
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('No.') }}</p>
                  <p class="mb-0">{{ $bill->number }}</p>
                </div><!-- d-flex -->
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('Date') }}</p>
                  <p class="mb-0">{{ $bill->created_at->format('d/m/Y')}}</p>
                </div><!-- d-flex -->
              @endif
              @if($bill->user->settings->display_customer_details)
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('Customer Name') }}</p>
                  <p class="mb-0">{{ $bill->customer_name }}</p>
                </div><!-- d-flex -->
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('Mobile Number') }}</p>
                  <p class="mb-0">{{ $bill->customer_mobile }}</p>
                </div><!-- d-flex -->
              @endif
            </div><!-- d-flex -->

            <div class="table-responsive border border-bottom-0 border-top-0 rounded mb-3">
              <table class="table m-0">
                <thead>
                  <tr>
                    <th class="text-nowrap">{{ __('Description') }}</th>
                    <th class="text-nowrap">{{ __('Price') }}</th>
                    <th class="text-nowrap">{{ __('Quantity') }}</th>
                    @if($bill->add_tax)
                      <th class="text-nowrap">{{ __('Total include added tax') }}</th>
                    @else
                      <th class="text-nowrap">{{ __('Total') }}</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @foreach($bill->items as $item)
                    @if($item->product_parent) @continue @endif
                    <tr>
                      <td class="text-nowrap">
                        {{ $item->product_name }}
                        @foreach($item->customizations as $customization)
                          <br>
                          <span class="text-muted">{{$customization->product_name}}</span>
                        @endforeach
                      </td>
                      <td class="text-nowrap">
                        <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
                          {{ $item->product_price  }} <i class="sar-icon"></i>
                        </span>
                        @foreach($item->customizations as $customization)
                          <br>
                          <span class="text-muted">{{$customization->product_price}}</span>
                        @endforeach
                      </td>
                      <td class="text-nowrap">
                        {{ $item->quantity  }}
                        @foreach($item->customizations as $customization)
                        <br>
                        <span>{{$customization->quantity}}</span>
                        @endforeach
                      </td>
                      <td class="text-nowrap">
                        @if( $bill->add_tax)
                          <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
                            {{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }} <i class="sar-icon"></i>
                          </span>
                        @else
                          <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
                            {{ $item->product_price * $item->quantity }} <i class="sar-icon"></i>
                          </span>
                        @endif
                        @foreach($item->customizations as $customization)
                          <br>
                          <span>{{$bill->add_tax ? $customization->product_price + ($customization->product_price * $bill->tax_value) / 100 : $customization->product_price}}</span>
                        @endforeach
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div><!-- table-responsive -->

            <div class="d-flex flex-column gap-3">
              @if( $bill->add_tax || $bill->add_discount)
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">
                    {{ __('Total amount') }}
                    @if( $bill->add_tax)
                      <small class="d-block mt-1">( {{ __('Exclude added tax') }} )</small>
                    @endif
                  </p>
                  <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 fw-medium">
                    {{ $bill->sub_total }} <i class="sar-icon"></i>
                  </p>
                </div>
              @endif
              @if( $bill->add_discount)
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('Discount amount') }}</p>
                  <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 fw-medium">
                    {{ $bill->discount }} <i class="sar-icon"></i>
                  </p>
                </div>
              @endif
              @if( $bill->user->pay_fees == 'client')
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('payment fees') }}</p>
                  <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 fw-medium">
                    {{ $bill->payment_fees }} <i class="sar-icon"></i>
                  </p>
                </div>
              @endif
              @if( $bill->add_tax)
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('Added tax value (:percentge %)', ['percentge'=>$bill->tax_value]) }}</p>
                  <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 fw-medium">
                    {{ $bill->vat }} <i class="sar-icon"></i>
                  </p>
                </div>
              @endif
              @if( $bill->channel_extra_amount)
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{$bill->channel_extra_title}}</p>
                  <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 fw-medium">
                    {{ $bill->channel_extra_amount }} <i class="sar-icon"></i>
                  </p>
                </div>
              @endif
              @if( $bill->channel_extra_vat)
                <div class="d-flex align-items-center justify-content-between gap-2">
                  <p class="mb-0">{{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</p>
                  <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 fw-medium">
                    {{ $bill->channel_extra_vat }} <i class="sar-icon"></i>
                  </p>
                </div>
              @endif
              {{-- @if( $bill->refund_amount)
                <div class="d-flex align-items-center justify-content-between">
                  <span class="d-block mb-2">{{ __('Refund Amount') }}</span>
                  <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                    {{ $bill->refund_amount }}  <span class="riyal-symbol-font">$</span>
                  </div><!-- d-flex -->
                </div><!-- d-flex -->
              @endif --}}
              <div class="d-flex align-items-center justify-content-between gap-2 border-top pt-3 fw-bold">
                <p class="mb-0">{{ __('Total amount') }}</p>
                <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                  {{ $bill->sub_total + $bill->vat - $bill->discount}} <i class="sar-icon"></i>
                </p>
              </div>
            </div><!-- d-flex -->

            @if($bill->customer_notes)
              <hr class="mt-3 mb-0">
              <div class="card-body p-0 text-heading text-capitalize py-3">{{$bill->customer_notes}}</div>
            @endif

            @if(!$bill->is_expired)
              @include('bills.partials.payment_form')
            @endif

            @if($bill->user->settings->add_tax_invoice)
              <hr class="my-3">
              <div class="card-body p-0 text-heading text-center text-capitalize">
                <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
                  {!! generateQRcode($bill) !!}
                  <!-- <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p> -->
                  <span class="d-block text-body">{{ __('Tax Invoice') }}</span>
                </a>
              </div>
            @endif

            @if(isset($bill->user->settings->footer_bill))
              <hr class="my-3">
              <div class="card-body p-0 text-heading text-center text-capitalize">{{ $bill->user->settings->footer_bill }}</div>
            @endif

            @if($bill->application && $bill->is_redirect)
              <div id="back_btn" class="text-center">
                <a href="{{ $bill->back_url}}" class="btn btn-light">{{__('Back')}}
                  <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M7.854 4.646a.5.5 0 0 1 0 .708L5.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z"/>
                    <path fill-rule="evenodd" d="M4.5 8a.5.5 0 0 1 .5-.5h6.5a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/>
                  </svg>
                </a>
              </div>
            @endif

          </div><!-- card-body -->
        </div><!-- card -->


      </div><!-- col -->
    </div><!-- row -->
  </div><!-- container -->
</div><!-- singlebBillSimple_page -->

  <div class="loading"></div>

@endsection

@push('footer-scripts')
  <script type='text/javascript'>
    if (typeof window.Echo !== 'undefined') {
      Echo.channel('bill.{{$bill->id}}')
      .listen('BillStatusUpdated', (e) => {

          var className;

          switch(e.bill.status) {
            case "pending":
              className = "badge-info";
              break;
            case "paid":
              $("#new_countdown").remove();
              $("#payment_area").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
              break;
            case "canceled":
              $("#new_countdown").remove();
              $("#payment_area").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
              break;
            case "expired":
              $("#new_countdown").remove();
              $("#payment_area").remove();
              $("#back_btn").remove();
              $("#status").empty();
              $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
              break;
            default:
              $("#payment_area").remove();
              $("#back_btn").remove();
              $("#status").empty();
              className = "badge-info";
          }
      });
    }
  </script>
@endpush
