<table class="table table-striped">
    <thead>
        <tr>
        <th scope="col">#</th>
        <th scope="col">{{__('Image')}}</th>
        <th scope="col">{{__('Name Ar')}}</th>
        <th scope="col">{{__('Name En')}}</th>
        <th scope="col">{{__('Sort No.')}}</th>
        <th scope="col">{{__('Parent')}}</th>
        <th scope="col">{{__('Status')}}</th>
        <th scope="col">{{__('Actions')}}</th>
        </tr>
    </thead>
    <tbody>
        
        <tr>
            <th scope="row">id</th>
            <td>name</td>
            <td>mobile</td>
            <td>email</td>
            <td>count</td>
            <td>created_at</td>
            <td>created_at</td>
            <td>
            <a href="{{ route('categories.edit', 1)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Edit') }}">{{ __('Edit') }}</a>
            <a href="#" class="btn btn-danger" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Delete') }}">{{ __('Delete') }}</a>
            </td>
        </tr>
        

    </tbody>
</table>