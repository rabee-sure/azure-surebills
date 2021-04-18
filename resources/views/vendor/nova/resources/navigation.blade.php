@if (count(Nova::availableResources(request())))
<ul class="sidemenu">
    @foreach($navigation as $group => $resources)
      @if (count($groups) > 1)
      <li class="sidebar-dropdown mb-2">
        <input type="checkbox" />
        <a href="#" data-toggle="dropdown">

            <span class="sidebar-label ml-8">{{ $group }}</span>
        </a>
        <ul class="dropdown-menu">
          @foreach($resources as $resource)
          <li>
            <router-link :to="{
                name: 'index',
                params: {
                    resourceName: '{{ $resource::uriKey() }}'
                }
            }" class="flex items-center font-normal text-white mb-4 text-base no-underline dim">
                @if(property_exists($resource, 'icon'))
                    {!! $resource::$icon !!}
                @elseif(method_exists($resource, 'icon'))
                    {!! $resource::icon() !!}
                @else
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill="var(--sidebar-icon)" d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"
                    />
                </svg>
                @endif
                <span class="sidebar-label">{{ $resource::label() }}</span>
            </router-link>
          </li>
          @endforeach
        </ul>
      </li>

      @else
        @foreach($resources as $resource)
        <li class="sidebar-dropdown">
            <router-link :to="{
                name: 'index',
                params: {
                    resourceName: '{{ $resource::uriKey() }}'
                }
            }" class="flex items-center font-normal text-white mb-6 text-base no-underline dim">
                @if(property_exists($resource, 'icon'))
                    {!! $resource::$icon !!}
                @elseif(method_exists($resource, 'icon'))
                    {!! $resource::icon() !!}
                @else
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill="var(--sidebar-icon)" d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"
                    />
                </svg>
                @endif
                <span class="sidebar-label">{{ $resource::label() }}</span>
            </router-link>
        </li>
        @endforeach
      @endif
    @endforeach

        <li class="sidebar-dropdown">
            <router-link :to="{
                name: 'index',
                params: {
                    resourceName: 'vrification-requests'
                }
            }" class="flex items-center font-normal text-white mb-6 text-base no-underline dim">
                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill="var(--sidebar-icon)" d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"
                    />
                </svg>
                <span class="sidebar-label">{{ __('Verification Requests') }} </span>
                <span class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full" style="
                    --bg-opacity: 1;
                    background-color: #e53e3e;
                    background-color: rgba(229,62,62,var(--bg-opacity));
                    margin-left: 10px;
                ">{{ App\Models\User::vrificationRequest()->count()
                    }}</span>
            </router-link>
        </li>
        <li class="sidebar-dropdown mb-2">
            <input type="checkbox"> 
            <a href="#" data-toggle="dropdown">
            <span class="sidebar-label ml-8">{{ __('Transfers') }} </span></a> 
            <ul class="dropdown-menu">
                <li class="sidebar-dropdown">
                    <router-link :to="{                 
                        name: 'index',
                        params: {
                            resourceName: 'transfers'
                        }
                    }" class="flex items-center font-normal text-white mb-6 text-base no-underline dim">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill="var(--sidebar-icon)" d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"
                            />
                        </svg>
                        <span class="sidebar-label">{{ __('Transfers') }} </span>
                    </router-link>
                </li>
                <li class="sidebar-dropdown">
                    <router-link :to="{ name: 'settlements'}" class="flex items-center font-normal text-white mb-6 text-base no-underline dim">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill="var(--sidebar-icon)" d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"
                            />
                        </svg>
                        <span class="sidebar-label">{{ __('Pending Transfers') }} </span>
                    </router-link>
                </li>
            </ul>
        </li>
    </ul>
@endif
