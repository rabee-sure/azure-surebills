<template>
    <div>
        <heading class="mb-6">Settlement for {{ user.name}}</heading>


  <div class="md:flex md:items-center mb-6">
    <div class="md:w-1/3">
      <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
        {{ user.name}} -  Balance {{ user.balance}}
      </label>
    </div>
    <div class="md:w-2/3">
      <input v-model="amount" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" type="number" name="amount" step=".01" :max="user.balance" id="amount" :disabled="user.balance == 0">
      <p class="text-gray-600 text-xs italic">Make sure amount be less than or equal user balance</p>

      <button v-show="amount > 0" :loading="loading" class="flex-shrink-0 bg-teal-500 hover:bg-teal-700 border-teal-500 hover:border-teal-700 text-sm border-4 text-white py-1 px-2 rounded"  @click="doSomething">
        Create
      </button>
    </div>
  </div>



        <table class="table-auto" v-if="settlements.length > 0">
          <thead>
            <tr>
              <th class="px-4 py-2">Id</th>
              <th class="px-4 py-2">User</th>
              <th class="px-4 py-2">Amount</th>
              <th class="px-4 py-2">Create At</th>
            </tr>
          </thead>
          <tbody >
            <tr  v-for="settlement in settlements" >
              <td class="border px-4 py-2">{{ settlement.id}}</td>
              <td class="border px-4 py-2">{{ user.name}}</td>
              <td class="border px-4 py-2">{{ settlement.amount}}</td>
              <td class="border px-4 py-2">{{ settlement.created_at}}</td>
            </tr>
          </tbody>
        </table>
        <div v-else>
            don't have settlements ...
        </div>
    </div>

</template>

<script>
export default {
    data() {
        return {
            settlements: [],
            user: [],
            amount: null,
            errors: [],
            loading: false,
        };
    },
    mounted() {
        this.getUser(this.$route.params.id)
    },
    methods: {
        getUser(id) {
            axios.get('/users/'+id)
                    .then(response => {
                        this.user = response.data.data;
                        this.amount = this.user.balance;
                    });
            axios.get('/users/'+id+'/settlements')
                    .then(response => {
                        this.settlements = response.data.data;
                    });
        },
        doSomething() {
              console.log( this.user.balance);
              if(this.loading == false && this.amount > 0 && this.amount <= this.user.balance){
                this.loading = true
                Nova.request().post('/settlements', {
                    user_id: this.user.id,
                    amount: this.amount
                  })
                  .then(response => {
                    this.getUser(this.$route.params.id)
                    this.loading = false
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
              }


        },
    }
}
</script>

<style>
/* Scoped Styles */
</style>
