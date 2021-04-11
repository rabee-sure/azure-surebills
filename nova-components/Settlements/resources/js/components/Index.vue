<template>
    <div>
      <div class="mb-3"><div></div> <nav resource-name="vrification-requests"><ul class="breadcrumbs"><li class="breadcrumbs__item"><a href="/nova/" class="router-link-active">
                {{ __('Home') }}
            </a>
          </li><li class="breadcrumbs__item"><span>{{ __('Pending Transfers') }}</span></li></ul></nav> <div></div>
          </div>
<!--         <heading class="mb-6">Settlements</heading>

        <select class="custom-select"  @change="onChange($event)" v-model="select">
          <option selected value="">select User</option>
          <option v-for="user in users" :value="user.id">{{ user.name }} - Balance {{user.balance}}</option>
        </select>
        <a v-if="select" :href="'/nova/settlements/'+user.id+'/create'">
          create Settlement
        </a>
 -->

      <div style="padding-top: 10px;">
          <Card :bordered="false">
              <p slot="title">{{ __('Transfers')}}</p>
              <Table :columns="transfersTable" :data="transfers" :no-data-text="__('No Data')">
                  <template slot-scope="{ row }" slot="fromto">
                      {{ __(row.filter_from) }} - {{ __(row.filter_to) }}
                  </template>
                  <template slot-scope="{ row }" slot="status">
                      <Badge v-if="row.status_bool" status="success" />
                      <Badge v-else status="warning" />
                  </template>

                  <template slot-scope="{ row, index }" slot="confirm">              
                    <i-switch :disabled="row.status_bool" :loading="switch_loading" v-model="row.status_bool" @on-change="changeStatus($event, row.id)" false-color="#f90" true-color="#13ce66" :ref="'switch' + row.id" />
                  </template>
                  <template slot-scope="{ row, index }" slot="deed">
                      <Upload :on-success="uploadSuccess" :action="'/api/transfers/'+row.id+'/upload_attachment'" :show-upload-list="false">
                          <div>
                              <Icon type="ios-cloud-upload" size="30" style="color: #3399ff"></Icon>
                          </div>
                      </Upload>
                  </template>
              </Table>
              <div style="margin: 10px;overflow: hidden">
                  <div style="float: right;">
                      <Page :total="meta.total" @on-change="changePage"></Page>
                  </div>
              </div>
          </Card>
      </div>
      <Modal
          v-model="modal1"
          title="Common Modal dialog box title"
          @on-ok="ok"
          @on-cancel="cancel">
          <p>Content of dialog</p>
          <p>Content of dialog</p>
          <p>Content of dialog</p>
      </Modal>
    </div>

</template>

<script>
export default {
    data() {
        return {
            modal1: false,
            switch_loading: false,
            meta: [],
            users: [],
            user: [],
            select: '',
            transfers: [],
            transfersTable: [
                {
                    title: this.__('Id'),
                    key: 'id',
                    width: 70,
                },
                {
                    title: this.__('Business Name'),
                    key: 'user_business_name_en',
                    width: 150,
                },
                {
                    title: this.__('Amount'),
                    key: 'amount',
                    width: 130,
                },
                {
                    title: this.__('Transfer Fees'),
                    key: 'transfer_fees',
                    width: 100,
                },
                {
                    title: this.__('Net Amount'),
                    key: 'net_amount',
                    width: 120,
                },
                {
                    title: this.__('From - To'),
                    slot: 'fromto',
                    width: 300,
                },
                // {
                //     title: this.__('Created By'),
                //     key: 'created_by_name'
                // },
                {
                    title: this.__('Created At'),
                    width: 150,
                    key: 'created_at'
                },
                {
                    title: this.__('Confirm Transfer'),
                    slot: 'confirm',
                    width: 150,
                    align: 'center'
                },
                {
                    title: this.__('Transfer Deed'),
                    slot: 'deed',
                    width: 150,
                    align: 'center'
                }
            ],
        };
    },
    mounted() {
        this.getUsers()
        this.getTransfers()
    },
    methods: {
        getUsers() {
          axios.get('/users/all')
            .then(response => {
                this.users = response.data.data;
            });
        },
        getTransfers(page=1) {
            Nova.request().get('/transfers/all', {
                    params: {
                        per_page: 10,
                        page: page,
                    }
                })
            .then(response => {
                this.transfers = response.data.data;
                this.meta = response.data.meta;
            });
        },
        onChange(event) {
            this.user = [];
            if(event.target.value){
                this.user = this.users.find(x => x.id == event.target.value);
                axios.get('/users/'+event.target.value+'/transfers')
                    .then(response => {
                        this.transfers = response.data.data;
                    });
            }
        },
        changePage(page) {
            this.getTransfers(page)
        },
        changeStatus(status, id) {

              this.$Modal.confirm({
                    title: this.__('Attention'),
                    content: this.__('Are you sure you confirm transfer, this action cannot be undone'),
                    onOk: () => {
                        this.switch_loading = true;
                        Nova.request().put('/transfers/'+id+'/change_status', {
                            status: status? 'completed': 'pending',
                        })
                        .then(response => {
                            this.switch_loading = false;
                        })
                        .catch(function (error) {
                            this.switch_loading = false;
                        });
                    },
                    onCancel: () => {
                      this.$refs['switch'+id].value = false
                      this.$refs['switch'+id].disabled = false
                    }
                });
   
        },
        uploadSuccess() {
          this.$Message.success(this.__('Upload Success'));
        },

    }
}
</script>

<style>
/* Scoped Styles */
</style>
