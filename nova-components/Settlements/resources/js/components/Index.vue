<template>
    <div>
        <heading class="mb-6">Settlements</heading>

        <select class="custom-select"  @change="onChange($event)" v-model="select">
          <option selected value="">select User</option>
          <option v-for="user in users" :value="user.id">{{ user.name }} - Balance {{user.balance}}</option>
        </select>
        <a v-if="select" :href="'/nova/settlements/'+user.id+'/create'">
          create Settlement
        </a>


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
              <td class="border px-4 py-2">{{ settlement.id }}</td>
              <td class="border px-4 py-2">{{ user.name }}</td>
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
            users: [],
            settlements: [],
            user: [],
            select: '',
        };
    },
    mounted() {
        this.getUsers()
    },
    methods: {
        getUsers() {
          axios.get('/users/all')
            .then(response => {
                this.users = response.data.data;
            });
        },
        onChange(event) {
            this.user = [];
            if(event.target.value){
                this.user = this.users.find(x => x.id == event.target.value);
                console.log(this.user)
                axios.get('/users/'+event.target.value+'/settlements')
                    .then(response => {
                        this.settlements = response.data.data;
                    });
            }
            console.log(event.target.value)
        }
    }
}
</script>

<style>
/* Scoped Styles */
</style>
