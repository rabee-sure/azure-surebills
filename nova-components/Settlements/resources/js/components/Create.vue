<template>
<div>
    <Card :bordered="false">
        <p slot="title">{{ __('Create Transfer to') }} {{ user.name}}</p>
        <Form ref="form" :model="form" label-position="left" :label-width="150" :rules="ruleInline">
            <Row :gutter="10">
                <Col span="22">
                    <FormItem :label="__('Date Range')" prop="date_range">
                        <DatePicker v-model="form.date_range" size="large" type="datetimerange" placement="bottom-end" placeholder="Select date" style="width: 100%" @on-change="handleChangeDate"></DatePicker>
                    </FormItem>
                </Col>
                <Col span="1">
                    <Tooltip :content="__('Transactions')" >
                        <Badge :count="transactions.length">
                            <Button @click="transactionsModal = true" icon="md-reorder"  size="large"></Button>
                        </Badge>
                    </Tooltip>
                </Col>
                <Col span="1">
                    <Tooltip :content="__('bills')">
                         <Badge :count="bills.length">
                            <Button @click="billsModal = true" icon="ios-paper" size="large"></Button>
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

            <FormItem :label="__('Note')"  prop="note">
                <Input size="large" v-model="form.note" type="textarea" :autosize="{minRows: 4,maxRows: 5}" :placeholder="__('')" />
            </FormItem>

            <FormItem :label="__('Attachment')">
                <Upload
                    :on-success="handleUploadFileSuccess" :multiple="false"
                    :on-progress="handleProgress"
                    type="drag"
                    :action="this.uploadFileActionUrl">
                    <div style="padding: 20px 0">
                        <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                        <p>{{ __('Click or drag files here to upload') }}</p>
                    </div>
                </Upload>
                <p class="file-error" style="color:red; direction: rtl;">{{fileError}} {{this.language}}</p>
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
                <Button type="primary" @click="handleSubmit('form')" :disabled="disableBtn"> {{__('Submit')}} </Button>
                <Button style="margin-left: 8px">{{__('Cancel')}}</Button>
            </FormItem>
        </Form>
    </Card>

    <div  style="padding-top: 10px;">
        <Card :bordered="false">
            <p slot="title">{{ __('Transfers for')}} {{ user.name}}</p>
            <Table :columns="transfersTable" :data="transfers"></Table>
        </Card>
    </div>

    <Modal
        :title="__('transactions')"
        v-model="transactionsModal"
        width="760"
        :ok-text="__('OK')"
        :cancel-text="__('Cancel')">
        <download-excel v-if="new_transactions.length" :data="new_transactions" :name="'transactions-'+ Date.now()">
            <Button :size="buttonSize" icon="ios-download-outline" type="primary">{{ __('Export') }}</Button>
        </download-excel>
        <Table stripe height="400" :columns="transactionsTable" :data="transactions">
            <template slot-scope="{ row }" slot="type">
                <Button type="success" v-if="row.type == 'credit'" size="small">{{ __(row.type) }}</Button>
                <Button type="error" v-if="row.type == 'debit'" size="small">{{ __(row.type) }}</Button>
            </template>
        </Table>
    </Modal>

    <Modal
        :title="__('Bills')"
        v-model="billsModal"
        width="760"
        :ok-text="__('OK')"
        :cancel-text="__('Cancel')">
        <download-excel v-if="new_bills.length" :data="new_bills" :name="'bills-'+ Date.now()">
            <Button :size="buttonSize" icon="ios-download-outline" type="primary">{{ __('Export') }}</Button>
        </download-excel>
        <Table stripe height="400" :columns="billsTable" :data="bills">
        </Table>
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
            billsModal: false,
            disableBtn: false,
            uploadFileActionUrl: '/api/upload?lang=',
            bills: [],
            new_bills: [],
            billsTable: [
                {
                    title: this.__('Name'),
                    key: 'name',
                    width: 220,
                },
                {
                    title: this.__('Total'),
                    key: 'total',
                    width: 100,
                },
                {
                    title: this.__('FEES'),
                    key: 'payment_fees',
                    width: 100,
                },
                {
                    title: this.__('Payment Fees Vat'),
                    key: 'payment_fees_vat',
                    width: 100,
                },
                {
                    title: this.__('Net'),
                    key: 'net',
                    width: 100,
                },
                {
                    title: this.__('Paid At'),
                    key: 'paid_at',
                    width: 150,
                },{
                    title: this.__('A'),
                    key: 'action',
                    width: 50,
                    align: 'center',
                    render: (h, params) => {
                        return h('div', [
                            h('Button', {
                                props: {
                                    size: 'small',
                                    shape: "circle",
                                    icon: "md-eye"
                                },
                                on: {
                                    click: () => {
                                        var win = window.open('/nova/resources/bills/'+params.row.id, '_blank');
                                        win.focus();
                                    }
                                }
                            })
                        ]);
                    }
                }
            ],
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
            form: {
                date_range: null,
                amount: 0,
                note: null,
                attachment: null,
            },
            transfers: [],
            transfersTable: [
                {
                    title: this.__('Id'),
                    key: 'id',
                },
                {
                    title: this.__('Amount'),
                    key: 'amount'
                },
                {
                    title: this.__('Note'),
                    key: 'note'
                },
                {
                    title: this.__('Created By'),
                    key: 'created_by_name'
                },
                {
                    title: this.__('Created At'),
                    key: 'created_at'
                }
            ],
            ruleInline: {
                date_range: [
                    { type: 'array', required: true, message: 'Choose date Range', trigger: 'blur' },
                ],
                amount: [
                    { type: 'number', min:1, required: true, message: 'Incorrect amount', trigger: 'blur' }
                ]
            }
        };
    },
    mounted() {
        this.getUser(this.$route.params.id)
    },
    methods: {
        getUser(id) {
            Nova.request().get('/users/'+id)
            .then(response => {
                this.user = response.data.data;
                this.uploadFileActionUrl += response.data.data.language;
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
        handleChangeDate (date) {
            this.bills = [];
            this.new_bills = [];
            this.transactions = [];
            this.new_transactions = [];
            this.form.amount = 0;
            if(date[0] != ''){
                Nova.request().get('/users/'+this.$route.params.id+'/transactions', {
                    params: {
                        from: date[0],
                        to: date[1],
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
                            'hyperpay_id': item.hyperpay_id,
                        }
                    });
                    this.form.amount = response.data.meta.balance;
                    if(this.transactions.length == 0){
                        this.$Message.warning({
                            duration:3,
                            render: h => {
                                return h('span', [
                                    'لا يوجد اي فواتير في التاريخ '
                                ])
                            }
                        });
                    }
                });

                Nova.request().get('/users/'+this.$route.params.id+'/bills', {
                    params: {
                        from: date[0],
                        to: date[1],
                        not_settled: true
                    }
                })
                .then(response => {
                    this.bills = response.data.data;
                    this.new_bills = this.bills.map((item) => {
                        return {
                            'name': item.name,
                            'total': item.total,
                            'payment_fees': item.payment_fees,
                            'payment_fees_vat': item.payment_fees_vat,
                            'net': item.net,
                            'paid_at': item.paid_at,
                            'hyperpay_id': item.hyperpay_id,
                            'merchant_name': item.business_name,
                        }
                    });
                });
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
                if (valid) {
                    Nova.request().post('/transfers', {
                        user_id: this.user.id,
                        amount: this.form.amount,
                        note: this.form.note,
                        attachment: this.form.attachment,
                        from: this.form.date_range[0],
                        to: this.form.date_range[1],
                        bills_ids: this.bills.map(a => a.id),
                        bank_id: this.user.bank_id,
                        iban_number: this.user.iban_number,
                        beneficiary_name: this.user.beneficiary_name,
                    })
                    .then(response => {
                        this.$router.push('/resources/transfers/' + response.data.data.id)
                        this.loading = false
                        this.bills = [];
                        this.transactions = [];
                        this.form.date_range = null;
                        this.form.amount = 0;
                        this.form.note = null;
                        this.form.attachment = null;
                        this.disableBtn = false;
                    })
                    .catch(function (error) {
                        this.disableBtn = false;
                    });
                    this.$Message.success('Success!');
                } else {
                    this.disableBtn = false;
                    this.$Message.error('Fail!');
                }
            })
        }
    }
}
</script>

<style>
/* Scoped Styles */
</style>
