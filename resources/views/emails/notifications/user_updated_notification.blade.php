<!-- Start Code Here -->
@if(!empty($data['changes']) || !empty($data['documents']))
  <table style="font-family: arial;border-spacing: 0;border-collapse: collapse;width: 100%;">
    <thead>
      <tr>
        <th style="background-color: #edf2f7;padding: 25px 0 0;font-weight: bold;font-size: 30px;color: #3d4852;">SureBills</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="background-color: #edf2f7;padding: 25px;">
          <div style="background-color: #ffffff;padding: 30px;border-radius: 20px;box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.08);">
            <h1 style="color: #000;font-size: 18px;font-weight: bold;text-transform: capitalize;margin: 0 auto 20px;">User {{$data['user']}} update his {{$data['mode']}} as the following :</h1>
            @if(!empty($data['changes']))
            <ul style="color: #000;list-style: decimal;margin: 0 0 20px;padding-inline-start: 30px;">
              @foreach ($data['changes'] as $line)
              <li style="font-size: 16px;margin-bottom: 7px;">{{$line}}</li>
              @endforeach
            </ul>
            @endif
            @if(!empty($data['documents']))
            <h1 style="color: #000;font-size: 18px;font-weight: bold;text-transform: capitalize;margin: 0 auto 20px;">Old Documents</h1>
            <ul style="color: #000;list-style: decimal;margin: 0 0 20px;padding-inline-start: 30px;">
              @foreach ($data['documents']['old'] as $oldDoc)
              <li style="font-size: 16px;margin-bottom: 7px;">{{$oldDoc}}</li>
              @endforeach
            </ul>
            <h1 style="color: #000;font-size: 18px;font-weight: bold;text-transform: capitalize;margin: 0 auto 20px;">Updated Documents</h1>
            <ul style="color: #000;list-style: decimal;margin: 0 0 20px;padding-inline-start: 30px;">
              @foreach ($data['documents']['updated'] as $updatedDoc)
              <li style="font-size: 16px;margin-bottom: 7px;">{{$updatedDoc}}</li>
              @endforeach
            </ul>
            @endif
            <a href="{{ rtrim(config('app.admin_app_url') ?: config('app.url'), '/') }}/merchants/{{ $data['user'] }}" title="#" style="display: inline-block;height: 45px;background-color: #2d3748;padding: 0 20px;min-width: 200px;text-align: center;line-height: 45px;border-radius: 4px;text-decoration: none;color: #fff;text-transform: capitalize;">visit merchant account</a>
          </div>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <th style="background-color: #edf2f7;font-weight: 100;font-size: 14px;direction: ltr;text-align: center;color: #b0adc5;padding:  0 0 25px">© 2022 SureBills. All rights reserved.</th>
      </tr>
    </tfoot>
  </table>
@endif
<!-- End Code Here -->