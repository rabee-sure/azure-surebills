@canany(['edit general settings', 'show banks', 'create bank', 'edit bank', 'delete bank', 'show webhook logs'])
<ul class="sidemenu">
    <li class="sidebar-dropdown mb-2">
        <input type="checkbox" checked>
        <a href="#" data-toggle="dropdown">
            <span class="sidebar-label ml-8">{{ __('Settings') }} </span>
        </a>
        <ul class="dropdown-menu">
            @can('edit general settings')
            <li class="sidebar-dropdown">
                <router-link tag="h3" :to="{ name: 'settings-tool' }" class="cursor-pointer flex items-center font-normal dim text-white mb-6 text-base no-underline">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill="var(--sidebar-icon)" d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"/>
                    </svg>
                    <span class="sidebar-label">{{ __(config('nova-settings-tool.sidebar-label', 'Settings')) }}</span>
                </router-link>
            </li>
            @endcan

            @can('show banks')
            <li class="sidebar-dropdown">
                <router-link :to="{name: 'index', params: {resourceName: 'banks'}}" class="flex items-center font-normal text-white mb-6 text-base no-underline dim">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill="var(--sidebar-icon)" d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"/>
                    </svg>
                    <span class="sidebar-label">{{ __('Banks') }} </span>
                </router-link>
            </li>
            @endcan

            @can('show webhook logs')
            <li class="sidebar-dropdown">
                <router-link :to="{name: 'index', params: {resourceName: 'webhook-logs'}}" class="flex items-center font-normal text-white mb-6 text-base no-underline dim">
                    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill="var(--sidebar-icon)" d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"/>
                    </svg>
                    <span class="sidebar-label">{{ __('WebhookLog') }} </span>
                </router-link>
            </li>
            @endcan
        </ul>
    </li>
</ul>
@endcanany
