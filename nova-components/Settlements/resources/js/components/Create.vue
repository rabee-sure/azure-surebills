<template>
<div>
    <Card :bordered="false">
        <p slot="title">{{ __('Create Transfer to') }} {{ user.name}}</p>
        <Form ref="form" :model="form" label-position="left" :label-width="150" :rules="ruleInline">
            <Row :gutter="10">
                <Col span="23">
                    <FormItem :label="__('Date Range')" prop="cycle_date">
                        <DatePicker :options="options" v-model="form.cycle_date" size="large" type="date" placement="bottom-end" placeholder="Select date" style="width: 100%" @on-change="handleChangeDate"></DatePicker>
                        <p :hidden="validDateRange" style="color:red;">{{__("invalid date range")}}</p>
                    </FormItem>
                </Col>
                <Col span="1">
                    <Tooltip :content="__('Transactions')" >
                        <Badge :count="transactions.length">
                            <Button @click="transactionsModal = true" icon="md-reorder"  size="large"></Button>
                        </Badge>
                    </Tooltip>
                </Col>
            </Row>


            <FormItem :label="__('Amount')" prop="amount">
                <InputNumber :min="1" :step=".5" size="large" placeholder="Enter number" name="amount" v-model="form.amount"
                :formatter="value => `${value} SAR`"
                :parser="value => value.replace(' SAR', '')"
                style="width: 100%" disabled></InputNumber>
            </FormItem>

            <FormItem :label="__('Transfer Status')" prop="status">
                <Select size="large"  v-model="form.status" style="width: 100%">
                    <Option v-for="item in statuses" :value="item.value" :key="item.value">
                        {{ item.label }}
                    </Option>
                </Select>
            </FormItem>


            <FormItem :label="__('Note')"  prop="note">
                <Input size="large" v-model="form.note" type="textarea" :autosize="{minRows: 4,maxRows: 5}" :placeholder="__('')" />
            </FormItem>

            <FormItem :label="__('Attachment')">
                <Upload
                    ref="uploadFiles"
                    :on-success="handleUploadFileSuccess" :multiple="false"
                    :on-progress="handleProgress"
                    type="drag"
                    :action="this.uploadFileActionUrl">
                    <div style="padding: 20px 0">
                        <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                        <p>{{ __('Click or drag files here to upload') }}</p>
                    </div>
                </Upload>
                <p class="file-error" v-bind:style="{ color: 'red', textAlign: 'center', direction: this.language == 'en'? 'ltr': 'rtl' }">{{fileError}}</p>
            </FormItem>
            <Divider orientation="left">{{__('Bank Info')}}</Divider>
            <FormItem :label="__('Bank') + ' :'" v-if="user.bank">
                <div>{{ user.bank.name.en }}</div>
            </FormItem>
            <FormItem :label="__('Iban Number')+ ' :'">
                <div>{{ user.iban_number }}</div>
            </FormItem>
            <FormItem :label="__('Beneficiary Name')+ ' :'">
                <div>{{ user.beneficiary_name }}</div>
            </FormItem>

            <FormItem>
                <Button type="primary" @click="handleSubmit('form')" :disabled="disableBtn">   {{__('Submit')}} 
                </Button>
                <Button style="margin-left: 8px" @click="handleCancel" >{{__('Cancel')}}</Button>
            </FormItem>
        </Form>
    </Card>

    <div style="padding-top: 10px;">
        <Card :bordered="false">
            <p slot="title">{{ __('Transfers for')}} {{ user.name}}</p>
            <Table :columns="transfersTable" :data="transfers" :no-data-text="__('No Data')">
                <template slot-scope="{ row }" slot="fromto">
                    {{ __(row.filter_from) }} - {{ __(row.filter_to) }}
                </template>
                <template slot-scope="{ row }" slot="status">
                    <Badge v-if="row.status == 'completed'" status="success" :text="__('completed transfer')"/>
                    <Badge v-if="row.status == 'canceled'" status="error" :text="__('canceled')"/>
                    <Badge v-if="row.status == 'pending'" status="warning" :text="__('pending transfer')"/>
                </template>

                <template slot-scope="{ row, index }" slot="action">
                    <Row>
                        <Col span="12">                    
                            <i-switch :disabled="row.status_bool" :loading="switch_loading" v-model="row.status_bool" @on-change="changeStatus($event, row.id)" false-color="#f90" true-color="#13ce66" />
                        </Col>
                        <Col span="12">
                            <Upload :action="'/api/transfers/'+row.id+'/upload_attachment'" :show-upload-list="false">
                                <div>
                                    <Icon type="ios-cloud-upload" size="30" style="color: #3399ff"></Icon>
                                </div>
                            </Upload>
                        </Col>
                    </Row>
                </template>
            </Table>
        </Card>
    </div>

    <Modal
        :title="__('transactions')"
        v-model="transactionsModal"
        width="760">
        <download-excel v-if="new_transactions.length" :data="new_transactions" 
            :name="'Transactions-'+user.business_name_en+'-FROM-'+ formatDate(form.cycle_date)">
            <Button :size="buttonSize" icon="ios-download-outline" type="primary">{{ __('Export') }}</Button>
        </download-excel>
        <Table stripe height="400" :columns="transactionsTable" :data="transactions" :no-data-text="__('No Data')">
            <template slot-scope="{ row }" slot="type">
                <Button type="success" v-if="row.type == 'credit'" size="small">{{ __(row.type) }}</Button>
                <Button type="error" v-if="row.type == 'debit'" size="small">{{ __(row.type) }}</Button>
            </template>
        </Table>
        <div slot="footer">
            <Button type="primary"  @click="transactionsModal = !transactionsModal">{{__('OK')}}</Button>
        </div>
    </Modal>
</div>

</template>

<script>

import expandRow from './table-expand.vue';

export default {
    name: 'create transfer',
    components: { expandRow },
    data() {
        return {
             statuses: [
                {
                    value: 'pending',
                    label: this.__('Pending Transfer')
                },
                {
                    value: 'completed',
                    label: this.__('Completed Transfer')
                },
            ],
            switch_loading: false,
            validDateRange: true,
            billsModal: false,
            disableBtn: false,
            language: 'ar',
            uploadFileActionUrl: '/api/upload?lang=',

            transactionsModal: false,
            transactions: [],
            new_transactions: [],
            transactionsTable: [
                {
                    type: 'expand',
                    width: 20,
                    render: (h, params) => {
                        return h(expandRow, {
                            props: {
                                row: params.row
                            }
                        })
                    }
                },
                {
                    title: this.__('Created At'),
                    key: 'created_at',
                    width: 160,
                },
                {
                    title: this.__('Description'),
                    key: 'description'
                },
                {
                    title: this.__('Type'),
                    slot: 'type',
                    width: 90,
                },
                {
                    title: this.__('Amount'),
                    key: 'amount',
                    width: 90,
                }
            ],
            user: [],
            errors: [],
            loading: false,
            fileError: null,
            options: {
                disabledDate (date) {
                    return date && date.valueOf() >= Date.now();
                }
            },
            form: {
                cycle_date: null,
                amount: 0,
                note: null,
                attachment: null,
                status: 'completed',
            },
            transfers: [],
            transfersTable: [
                {
                    title: this.__('Id'),
                    key: 'id',
                    width: 70,
                },
                {
                    title: this.__('Amount'),
                    key: 'amount',
                    width: 120,
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
                {
                    title: this.__('Created By'),
                    key: 'created_by_name'
                },
                {
                    title: this.__('Created At'),
                    key: 'created_at',
                    width: 150,
                },
                {
                    title: this.__('Status'),
                    slot: 'status',
                    key: 'status',
                    width: 150,
                    align: 'center'
                },
            ],
            ruleInline: {
                cycle_date: [{type: 'date',  required: true, message: this.__('select date range'), trigger: 'blur'}],
                amount: [{ type: 'number', min:1, required: true, message: this.__('invalid amount'), trigger: 'blur'}]
            }
        };
    },
    mounted() {
        this.getUser(this.$route.params.id)
    },
    methods: {
        formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) 
                month = '0' + month;
            if (day.length < 2) 
                day = '0' + day;

            return [year, month, day].join('-');
        },
        getUser(id) {
            Nova.request().get('/users/'+id)
            .then(response => {
                this.user = response.data.data;
                this.uploadFileActionUrl += response.data.data.language;
                this.language = response.data.data.language;
            });
            Nova.request().get('/users/'+id+'/transfers', {
                    params: {
                        per_page: 5,
                    }
                })
            .then(response => {
                this.transfers = response.data.data;
            });
        },
        isValidDate(d){
            return !isNaN((new Date(d)).getTime());
        },
        handleChangeDate (date) {
            this.refresh();
            if(date != '' && this.isValidDate(date) && this.isValidDate(date)){
                this.validDateRange = true;
                Nova.request().get('/users/'+this.$route.params.id+'/transactions', {
                    params: {
                        cycle_date: date,
                        bills_not_settled: true
                    }
                })
                .then(response => {
                    this.transactions = response.data.data;
                    this.new_transactions = this.transactions.map((item) => {
                        return {
                            'created_at': item.created_at,
                            'description': item.description,
                            'type': item.type,
                            'amount': item.amount,
                            'customer_notes': item.customer_notes,
                            'reference_id': item.reference_id,
                        }
                    });
                    this.form.amount = response.data.meta.balance;
                    if(this.transactions.length == 0){
                        this.$Message.warning({
                            duration:3,
                            render: h => {
                                return h('span', [
                                    this.language == 'en'? 'No transaction on this date': 'لا يوجد معاملات في هذا التاريخ'
                                ])
                            }
                        });
                    }
                });
            }
            else
            {
                this.validDateRange = false;
            }
        },
        handleProgress()
        {
            this.disableBtn=true;
        },
        handleUploadFileSuccess (res, file, filelist) {
            this.disableBtn = false;
            if(file.response.error)
            {
                filelist.splice(0, filelist.length);
                this.form.attachment = null;
                this.fileError = file.response.error.file[0];
            }
            else
            {
                if(filelist.length > 1)
                {
                    filelist.splice(0 , 1)
                }
                this.fileError = null;
                this.form.attachment = file.response.data;
            }
        },
        handleSubmit(name) {
            this.loading = true;
            this.disableBtn = true;

            this.$refs[name].validate((valid) => {
                if (valid && this.user.bank_id != null) {
                    Nova.request().post('/transfers', {
                        user_id: this.user.id,
                        status: this.form.status,
                        amount: this.form.amount,
                        note: this.form.note,
                        attachment: this.form.attachment,
                        cycle_date: this.form.cycle_date,
                        transactions_ids: this.transactions.map(a => a.id),
                        bank_id: this.user.bank_id,
                        iban_number: this.user.iban_number,
                        beneficiary_name: this.user.beneficiary_name,
                    })
                    .then(response => {
                        console.log('response.data')
                        console.log(response.data)
                        this.$router.push('/resources/transfers/' + response.data.data.id)
                        this.loading = false
                        this.transactions = [];
                        this.form.cycle_date = null;
                        this.form.amount = 0;
                        this.form.status = 'completed';
                        this.form.note = null;
                        this.form.attachment = null;
                        this.disableBtn = false;
                        this.$Message.success(this.language == 'en'? 'Success': 'تم');
                    })
                    .catch(error => {
                        console.log(error.response.data)
                        this.$Message.error(error.response.data.error);
                        this.disableBtn = false;
                    });
                } else if(this.user.bank_id == null){
                    this.disableBtn = false;
                    this.$Message.error(this.language == 'en'? 'User Must complete Profile Info': 'يجب استكمال بيانات هذا العميل');
                }
                else {
                    this.disableBtn = false;
                    this.$Message.error(this.language == 'en'? 'Fail': 'فشل');
                }
            })
        },
        handleCancel() {
            this.$Message.success(this.language == 'en'? 'Cancel Transfer successfully': 'تم الغاء التحويل بنجاح');
            this.form.cycle_date = null;     
            this.form.note = null;     
            this.form.attachment = null;     
            this.$refs['uploadFiles'].clearFiles();
            this.refresh();  
        },
        refresh() {
            this.transactions = [];
            this.new_transactions = [];
            this.form.amount = 0;           
            this.form.status = 'completed';           
        },
        changeStatus(status, id) {
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
        }
    }
}
</script>

<style>
/* Scoped Styles */
</style>
