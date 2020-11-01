@extends('layouts.bill')

@section('title', __('Bill'))

@section('content')
    <div class="single_bill_page"  id="app">
      {{ $error }}
    </div>
@endsection


@push('footer-scripts')
<script type="text/javascript">

</script>
@endpush
