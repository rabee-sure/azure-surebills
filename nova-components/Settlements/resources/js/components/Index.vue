<template>
    <div>
      <div class="mb-3"><div></div> <nav resource-name="vrification-requests"><ul class="breadcrumbs"><li class="breadcrumbs__item"><a href="/nova/" class="router-link-active">
                {{ __('Home') }}
            </a>
          </li><li class="breadcrumbs__item"><span>{{ __('Pending Transfers') }}</span></li></ul></nav> <div></div>
          </div>

      <div style="padding-top: 10px;">
          <Card :bordered="false">
              <p slot="title">{{ __('Transfers')}}</p>

            <Dropdown style="margin-left: 20px" slot="extra" v-show="selected_transfers_ids.length">
                <Button type="primary">
                    {{ __('Actions')}}
                    <Icon type="ios-arrow-down"></Icon>
                </Button>
                <DropdownMenu slot="list">
                    <DropdownItem >
                        <Button @click="makeAction('canceled')" type="error" long>{{ __('Cancel')}}</Button>
                    </DropdownItem>
                    <DropdownItem>
                        <Button @click="makeAction('completed')" type="warning" long>
                            {{ __('Confirm Transfer')}}
                        </Button>
                    </DropdownItem>
                    <DropdownItem>
                        <Button @click="makeAction('send_to_sps')" type="success" long>
                            {{ __('Send To SPS')}}
                        </Button>

                    </DropdownItem>
                </DropdownMenu>
            </Dropdown>

              <Table @on-selection-change="updateSelectedList" ref="selection" :columns="transfersTable" :data="transfers" :no-data-text="__('No Data')">
                  <template slot-scope="{ row }" slot="fromto">
                    <div v-if="row.filter_from">{{ __(row.filter_to) }}</div>
                    <div v-else>{{ __(row.cycle_date) }}</div>

                  </template>

                  <template slot-scope="{ row, index }" slot="confirm">
                    <i-switch v-if="row.status == 'pending' || row.status =='completed'" :disabled="!row.status_is_pending" :loading="switch_loading" v-model="row.status_bool" @on-change="changeStatus($event, row.id, 'completed')" false-color="#f90" true-color="#13ce66" :ref="'switch' + row.id" />
                  </template>


                  <template slot-scope="{ row, index }" slot="sps">
                    <i-switch  v-if="row.status == 'pending' || row.status =='send_to_sps'" :disabled="!row.status_is_pending" :loading="switch_loading" v-model="row.status_sps" @on-change="changeStatus($event, row.id, 'send_to_sps')"  :ref="'switch' + row.id" />
                  </template>

                  <template slot-scope="{ row, index }" slot="cancel">
                      <Button :disabled="row.status == 'canceled' || row.status == 'completed' || row.status == 'send_to_sps'" :loading="cancel_loading" @click="cancelTranfer(row.id)"  type="error" icon="ios-close-circle" >{{ __('Cancel')}}</Button>

                  </template>

                  <template slot-scope="{ row, index }" slot="deed">
                      <Upload :on-success="uploadSuccess" :action="'/api/transfers/'+row.id+'/upload_attachment'" :show-upload-list="false">
                          <div>
                              <Icon type="ios-cloud-upload" size="30" style="color: #3399ff"></Icon>
                          </div>
                      </Upload>
                  </template>
                    <template slot-scope="{ row, index }" slot="show_transfer">
                        <span class="inline-flex">
                            <a :href="'/nova/resources/transfers/'+row.id" class="cursor-pointer text-70 hover:text-primary mr-3 inline-flex items-center has-tooltip" data-testid="transfers-items-0-view-button" dusk="165-view-button" data-original-title="null" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 16" aria-labelledby="view" role="presentation" class="fill-current"><path d="M16.56 13.66a8 8 0 0 1-11.32 0L.3 8.7a1 1 0 0 1 0-1.42l4.95-4.95a8 8 0 0 1 11.32 0l4.95 4.95a1 1 0 0 1 0 1.42l-4.95 4.95-.01.01zm-9.9-1.42a6 6 0 0 0 8.48 0L19.38 8l-4.24-4.24a6 6 0 0 0-8.48 0L2.4 8l4.25 4.24h.01zM10.9 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path></svg>
                            </a>
                        </span>
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
            cancel_loading: false,
            meta: [],
            users: [],
            user: [],
            permissions: [],
            select: '',
            transfers: [],
            selected_transfers_ids: [],
            transfersTable: [
                {
                    type: 'selection',
                    width: 60,
                    align: 'center'
                },
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
                    title: this.__('Cycle Date'),
                    slot: 'fromto',
                    width: 120,
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
                    title: this.__('Send To SPS'),
                    slot: 'sps',
                    width: 150,
                    align: 'center'
                },
                {
                    title: this.__('Cancel Transfer'),
                    slot: 'cancel',
                    width: 150,
                    align: 'center'
                },
                {
                    title: this.__('Transfer Deed'),
                    slot: 'deed',
                    width: 150,
                    align: 'center'
                },
                {
                    title: this.__('Show'),
                    slot: 'show_transfer',
                    width: 100,
                    align: 'center'
                }
            ],
        };
    },
    mounted() {
        this.getUsers()
        this.getTransfers()
        this.userPermission();
    },
    methods: {
       userPermission(){
         return Nova.request().get('/user-permissions/admins').then(response => {
           this.permissions = response.data;
           if(!this.permissions.includes('show transfers'))
           {
                window.location.href = '/nova/403';
           }
         })
        },
        updateSelectedList(selection, row) {
            this.selected_transfers_ids = selection.map(row => row.id);
        },
        makeAction(type) {
            this.$swal({
            title: this.__('Attention'),
            text: this.__('Are you sure you confirm transfer, this action cannot be undone'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: this.__('Ok'),
            cancelButtonText: this.__('Cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.switch_loading = true;
                    Nova.request().put('/transfers/change_status', {
                        status: type,
                        ids: this.selected_transfers_ids,
                    })
                    .then(response => {
                        this.selected_transfers_ids.forEach(id => {
                            var index = this.transfers.map(x => x.id).indexOf(id);
                            let item = response.data.data.find(item=> item.id == id);
                            this.$set(this.transfers, index,item)

                        });
                        this.switch_loading = false;
                    })
                    .catch(error => {
                        this.switch_loading = false;
                    });
                }else{
                    this.$refs['switch'+id].value = false
                    this.$refs['switch'+id].disabled = false
                }
            });
        },
        getUsers() {
          axios.get('/users/all')
            .then(response => {
                this.users = response.data.data;
            });
        },
        getTransfers(page=1) {
            axios.get('/transfers/all', {
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
        changeStatus(e, id, status) {
            this.$swal({
            title: this.__('Attention'),
            text: this.__('Are you sure you confirm transfer, this action cannot be undone'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: this.__('Ok'),
            cancelButtonText: this.__('Cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.switch_loading = true;
                    Nova.request().put('/transfers/change_status', {
                        status: status,
                        ids: [id],
                    })
                    .then(response => {
                        var index = this.transfers.map(function(x) {return x.id; }).indexOf(id);
                        let item = response.data.data.find(item=> item.id == id);
                        this.$set(this.transfers, index, item)
                        this.switch_loading = false;
                    })
                    .catch(error => {
                        this.switch_loading = false;
                    });
                }else{
                    this.$refs['switch'+id].value = false
                    this.$refs['switch'+id].disabled = false
                }
            });

        },
        cancelTranfer(id) {
            this.$swal({
            title: this.__('Attention'),
            text: this.__('Are you sure you will Cancel transfer, this action cannot be undone ?'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: this.__('Ok'),
            cancelButtonText: this.__('Cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.cancel_loading = true;
                    Nova.request().put('/transfers/'+id+'/cancel')
                    .then(response => {
                        var index = this.transfers.map(function(x) {return x.id; }).indexOf(id);
                        this.$set(this.transfers, index, response.data.data)
                        this.cancel_loading = false;
                    })
                    .catch(error => {
                        this.cancel_loading = false;
                    });
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
