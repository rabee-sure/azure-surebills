@component('mail::message')
# Introduction

to pay bill please open link below.

@component('mail::button', ['url' => $bill->pay_url, 'color' => 'success'])
Pay Link
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
