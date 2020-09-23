<template>
    <div class="w-full max-w-3xl">
        <div class="-mx-2 md:flex">
            <div class="w-full md:w-1/4 px-2">
                <div class="rounded-lg shadow-sm mb-4">
                    <div class="rounded-lg bg-white shadow-lg md:shadow-xl relative overflow-hidden">
                        <a :href="'/nova/resources/statements?statements_page=1&statements_filter='+user.stats.filter_user_id" class="px-3 pt-8 pb-10 text-center relative z-10">
                            <h4 class="text-sm uppercase text-gray-500 leading-tight">Balance</h4>
                            <h3 class="text-3xl text-gray-700 font-semibold leading-tight my-3" v-if="user">{{ user.balance }}</h3>
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/4 px-2" >
                <div class="rounded-lg shadow-sm mb-4">
                    <div class="rounded-lg bg-white shadow-lg md:shadow-xl relative overflow-hidden">
                        <a :href="'/nova/resources/statements?statements_page=1&statements_filter='+user.stats.filter_user_id" class="px-3 pt-8 pb-10 text-center relative z-10" >
                            <h4 class="text-sm uppercase text-gray-500 leading-tight">Total paid</h4>
                            <h3 class="text-3xl text-gray-700 font-semibold leading-tight my-3" v-if="user.stats">{{ user.stats.total_paid }}</h3>
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/4 px-2">
                <div class="rounded-lg shadow-sm mb-4">
                    <div class="rounded-lg bg-white shadow-lg md:shadow-xl relative overflow-hidden">
                        <a  :href="'/nova/resources/bills?bills_page=1&bills_filter='+user.stats.filter_user_id" class="px-3 pt-8 pb-10 text-center relative z-10">
                            <h4 class="text-sm uppercase text-gray-500 leading-tight">total Bills</h4>
                            <h3 class="text-3xl text-gray-700 font-semibold leading-tight my-3" v-if="user.stats">{{ user.stats.total_bills }}</h3>
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/4 px-2">
                <div class="rounded-lg shadow-sm mb-4">
                    <div class="rounded-lg bg-white shadow-lg md:shadow-xl relative overflow-hidden">
                        <a  :href="'/nova/resources/bills?bills_page=1&bills_filter='+user.stats.filter_user_id" class="px-3 pt-8 pb-10 text-center relative z-10">
                            <h4 class="text-sm uppercase text-gray-500 leading-tight">Total paid Bills</h4>
                            <h3 class="text-3xl text-gray-700 font-semibold leading-tight my-3" v-if="user.stats">{{ user.stats.total_paid_bills }}</h3>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'card',
        'resource',
        'resourceId',
        'resourceName',
    ],
    data: () => ({
        user: {}
    }),
    mounted() {
        return Nova.request()
            .get('/api/v1/users/' + this.resourceId + '/stats')
            .then(response => {
                this.user = response.data.data
            })
    },
}
</script>
