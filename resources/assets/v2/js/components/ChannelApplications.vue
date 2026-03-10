<template>
  <section id="channelApplicationsPage" v-if="userPermissions.includes('show applications')">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-4 mb-6">
      <div class="d-flex flex-column gap-1">
        <h4 class="mb-0">{{ __('Channel') }} : {{ applications[0].name }}</h4>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-custom-icon mb-0">
            <li class="breadcrumb-item">
              <a href="/account" :title="__('Settings')">{{ __('Settings')}}</a>
              <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
            </li>
            <li class="breadcrumb-item">
              <a href="/channels" :title="__('Channels')">{{ __('Channels')}}</a>
              <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
            </li>
            <li class="breadcrumb-item active">{{ __('Channel') }} : {{ applications[0].name }}</li>
          </ol>
        </nav>
      </div><!-- d-flex -->
      <button type="button" class="btn btn-primary waves-effect waves-light" v-if="userPermissions.includes('create application')" @click="showCreateApplicationForm">
        <span class="icon-xs icon-base ti ti-plus me-2"></span> {{ __('Create New Application')}}
      </button>
    </div><!-- d-flex -->





    <div class="card">
      <h5 class="card-title p-4 m-0">{{ __('Applications')}}</h5>
      <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column py-5" v-if="applications.length === 0">
        <i class="ti ti-device-imac ti-xl"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('You have not created any applications.')}}</span>
      </div><!-- no_bills_yet -->

      <div class="table-responsive text-nowrap" v-if="applications.length > 0">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col" class="fw-bold">{{ __('ID')}}</th>
              <th scope="col" class="fw-bold">{{ __('Name')}}</th>
              <th scope="col" class="fw-bold">{{ __('Client Email')}}</th>
              <th scope="col" class="fw-bold">{{ __('Secret')}}</th>
              <th scope="col" class="fw-bold">{{ __('webhook URL')}}</th>
              <th scope="col" class="fw-bold">{{ __('webhook Secret')}}</th>
              <th scope="col" class="fw-bold">{{ __('Redirect Url')}}</th>
              <th scope="col" class="fw-bold" v-if="userPermissions.includes('update application') || userPermissions.includes('delete application')">{{__('Actions')}}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="application in applications">
              <td>{{ application.id }}</td>
              <td>{{ application.name }}</td>
              <td>{{ application.email}}</td>
              <td><code>{{ application.secret ? application.secret : '-' }}</code></td>
              <td><code>{{ application.webhook_url }}</code></td>
              <td><code>{{ application.webhook_secret ? application.webhook_secret : '-' }}</code></td>
              <td><code>{{ application.redirect }}</code></td>
              <td v-if="userPermissions.includes('update application') || userPermissions.includes('delete application')">
                <div class="d-flex align-items-center justify-content-start gap-2">
                  <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" :title="__('Edit')" class="btn btn-icon text-white btn-sm btn-info waves-effect waves-light" v-if="userPermissions.includes('update application')" @click="edit(application)">
                    <span class="icon-base ti ti-edit icon-18px"></span>
                  </button>
                  <button type="button" class="btn btn-icon text-white btn-sm btn-danger waves-effect waves-light" v-if="userPermissions.includes('delete application')" @click="deletes(application)">
                    <span class="w-100 h-100 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" :title="__('Delete')"><i class="icon-base ti ti-trash icon-18px"></i></span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- table-responsive -->
    </div><!-- card -->



    <!-- Create Application Modal -->
    <div class="modal fade" id="modal-create-application" tabindex="-1" aria-hidden="true" v-if="userPermissions.includes('create application')">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Create Application') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div><!-- modal-header -->
          <form role="form" class="modal-body">
            <div class="row row-cols-1 row-cols-md-1 g-6">
              <div class="col">
                <label for="email" class="form-label">{{ __('Client Email')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('email') }" type="email" inputmode="email" class="form-control" name="email" id="email" @keyup.enter="store" v-model="createForm.email">
                <div class="invalid-feedback text-danger" v-if="haveError('email')">{{errorMessage('email')}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="redirect" class="form-label">{{ __('Redirect URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('redirect') }" type="url" inputmode="url" class="form-control" name="redirect" id="redirect" @keyup.enter="store" v-model="createForm.redirect">
                <div class="form-text">{{ __('Your application\'s authorization callback URL.')}}</div>
                <div class="invalid-feedback text-danger" v-if="haveError('redirect')">{{errorMessage('redirect')}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="webhook_url" class="form-label">{{ __('Webhook URL')}} <span class="requirement text-danger">*</span></label>
                <input  :class="{'is-invalid': haveError('webhook_url') }" type="url" inputmode="url" class="form-control" name="webhook_url" id="webhook_url" @keyup.enter="store" v-model="createForm.webhook_url">
                <div class="invalid-feedback text-danger" v-if="haveError('webhook_url')">{{errorMessage('webhook_url')}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="mada_fixed" class="form-label">{{ __('Mada Fixed') }} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('mada_fixed') }" type="number" inputmode="numaric" class="form-control" name="mada_fixed" id="mada_fixed" @keyup.enter="store" v-model="createForm.mada_fixed" step="0.01">
                <div class="invalid-feedback text-danger" v-if="haveError('mada_fixed')">{{errorMessage('mada_fixed')}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="mada_percentage" class="form-label">{{ __('Mada Percentage') }} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('mada_percentage') }" type="number" inputmode="numaric" id="mada_percentage" class="form-control" name="mada_percentage" @keyup.enter="store" v-model="createForm.mada_percentage"  step="0.01">
                <div class="invalid-feedback text-danger" v-if="haveError('mada_percentage')">{{errorMessage('mada_percentage')}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="credit_cards_fixed" class="form-label">{{ __('Credit Cards Fixed') }} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('credit_cards_fixed') }" type="number" inputmode="numaric" id="credit_cards_fixed" class="form-control" name="credit_cards_fixed" @keyup.enter="store" v-model="createForm.credit_cards_fixed"  step="0.01">
                <div class="invalid-feedback text-danger" v-if="haveError('credit_cards_fixed')">{{errorMessage('credit_cards_fixed')}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="credit_cards_percentage" class="form-label">{{ __('Credit Cards Percentage') }} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('credit_cards_percentage') }" type="number" inputmode="numaric" id="credit_cards_percentage" class="form-control" name="credit_cards_percentage" @keyup.enter="store" v-model="createForm.credit_cards_percentage"  step="0.01">
                <div class="invalid-feedback text-danger" v-if="haveError('credit_cards_percentage')">{{errorMessage('credit_cards_percentage')}}</div>
              </div><!-- col -->
            </div><!-- row -->
          </form><!-- modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="button" class="btn btn-primary" @click="store">{{__('Create')}}</button>
          </div><!-- modal-footer -->
        </div><!-- modal-content -->
      </div><!-- modal-dialog -->
    </div><!-- modal -->


    <!-- Edit Application Modal -->
    <div class="modal fade" id="modal-edit-application" tabindex="-1" aria-hidden="true" v-if="userPermissions.includes('update application')">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('Edit Application') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div><!-- modal-header -->
          <form role="form" class="modal-body">
            <div class="row row-cols-1 row-cols-md-1 g-6">
              <div class="col">
                <label for="email" class="form-label">{{ __('Client Email')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('email', 2) }" type="email" inputmode="email" id="email" class="form-control" name="email" @keyup.enter="update" v-model="editForm.email" disabled>
                <div class="invalid-feedback" v-if="haveError('email', 2)">{{errorMessage('email', 2)}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="redirect" class="form-label">{{ __('Redirect URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('redirect', 2) }"  type="url" inputmode="url" class="form-control" name="redirect" @keyup.enter="update" v-model="editForm.redirect">
                <div class="form-text">{{ __('Your application\'s authorization callback URL.')}}</div>
                <div class="invalid-feedback text-danger" v-if="haveError('redirect', 2)">{{errorMessage('redirect', 2)}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="webhook_url" class="form-label">{{ __('Webhook URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('webhook_url', 2) }" type="url" inputmode="url" id="webhook_url" class="form-control" name="webhook_url" @keyup.enter="update" v-model="editForm.webhook_url">
                <div class="invalid-feedback" v-if="haveError('webhook_url', 2)">{{errorMessage('webhook_url', 2)}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="mada_fixed" class="form-label">{{ __('Mada Fixed') }} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('mada_fixed', 2) }" type="number" class="form-control" name="mada_fixed" id="mada_fixed" @keyup.enter="update" v-model="editForm.mada_fixed" step="0.01">
                <div class="invalid-feedback text-danger" v-if="haveError('mada_fixed', 2)">{{errorMessage('mada_fixed', 2)}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="mada_percentage" class="form-label">{{ __('Mada Percentage') }} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('mada_percentage', 2) }" type="number" class="form-control" name="mada_percentage" id="mada_percentage" @keyup.enter="update" v-model="editForm.mada_percentage" step="0.01">
                <div class="invalid-feedback text-danger" v-if="haveError('mada_percentage', 2)">{{errorMessage('mada_percentage', 2)}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="credit_cards_fixed" class="form-label">{{ __('Credit Cards Fixed') }} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('credit_cards_fixed', 2) }" type="number" class="form-control" name="credit_cards_fixed" id="credit_cards_fixed" @keyup.enter="update" v-model="editForm.credit_cards_fixed" step="0.01">
                <div class="invalid-feedback text-danger" v-if="haveError('credit_cards_fixed', 2)">{{errorMessage('credit_cards_fixed', 2)}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="credit_cards_fixed" class="form-label">{{ __('Credit Cards Percentage') }} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('credit_cards_percentage', 2) }"  type="number" class="form-control" name="credit_cards_percentage" id="credit_cards_percentage" @keyup.enter="update" v-model="editForm.credit_cards_percentage" step="0.01">
                <div class="invalid-feedback text-danger" v-if="haveError('credit_cards_percentage', 2)">{{errorMessage('credit_cards_percentage', 2)}}</div>
              </div><!-- col -->
            </div><!-- row -->
          </form><!-- modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="button" class="btn btn-primary" @click="update">{{__('Save Changes')}}</button>
          </div><!-- modal-footer -->
        </div><!-- modal-content -->
      </div><!-- modal-dialog -->
    </div><!-- modal -->

    <!-- Delete Application Modal -->
    <div class="modal fade" id="modal-delete-application" tabindex="-1" aria-hidden="true" v-if="userPermissions.includes('delete application')">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div><!-- modal-header -->
          <div class="modal-body">
            <div class="d-flex align-items-center justify-content-center text-warning mb-3">
              <i class="icon-base ti ti-info-triangle icon-50px"></i>
            </div>
            <h5 class="m-0 text-center">{{ __('Are you sure you want to delete this item?')}}</h5>
          </div><!-- modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="button" class="btn btn-danger" @click="destroy()">{{__('Delete')}}</button>
          </div><!-- modal-footer -->
        </div><!-- modal-content -->
      </div>
    </div><!-- modal -->

    <!-- Application Secret Modal -->
    <div class="modal fade" id="modal-application-secret" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ __('Application Secret')}}</h4>
            <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
          </div>

          <!-- Modal Actions -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close')}}</button>
          </div>
        </div>
      </div>
    </div>

  </section><!-- channelApplicationsPage -->
</template>

<script>
    export default {
        props: ['channel_id'],
        data() {
            return {
                applications: [],
                userPermissions: [],

                applicationSecret: null,

                createForm: {
                    errors: [],
                    errors_obj: [],
                    email: '',
                    redirect: '',
                    webhook_url: '',
                    mada_fixed: '',
                    mada_percentage: '',
                    credit_cards_fixed: '',
                    credit_cards_percentage: '',
                    confidential: true
                },
                deleteId: null,
                editForm: {
                    errors: [],
                    errors_obj: [],
                    email: '',
                    redirect: '',
                    webhook_url: '',
                    mada_fixed: '',
                    mada_percentage: '',
                    credit_cards_fixed: '',
                    credit_cards_percentage: '',
                }
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getUserPermissions();
            },

            /**
             * Get all of the OAuth applications for the user.
             */
            getApplications() {
                axios.get('/channels/'+ this.channel_id +'/applications')
                        .then(response => {
                            this.applications = response.data.data;
                        });
            },

            /**
             * Get user permissions.
             */
            getUserPermissions(){
                axios.get('/user-permissions')
                    .then(response => {
                        this.userPermissions = response.data;
                        if(this.userPermissions.includes('show applications'))
                        {
                            this.getApplications();
                        }
                        if(this.userPermissions.includes('create application'))
                        {
                            $('#modal-create-application').on('shown.bs.modal', () => {
                                $('#create-application-name').focus();
                            });
                        }
                        if(this.userPermissions.includes('update application'))
                        {
                            $('#modal-edit-application').on('shown.bs.modal', () => {
                                $('#edit-application-name').focus();
                            });
                        }
                    });
            },

            /**
             * Show the form for creating new applications.
             */
            showCreateApplicationForm() {
                $('#modal-create-application').modal('show');
            },

            /**
             * Create a new OAuth application for the user.
             */
            store() {
                this.persistApplication(
                    'post',
                    '/channels/'+ this.channel_id +'/applications',
                    this.createForm,
                    '#modal-create-application'
                );
            },

            /**
             * Edit the given application.
             */
            edit(application) {
                this.editForm.id = application.id;
                this.editForm.email = application.email;
                this.editForm.redirect = application.redirect;
                this.editForm.webhook_url = application.webhook_url;
                this.editForm.mada_fixed = application.mada_fixed;
                this.editForm.mada_percentage = application.mada_percentage;
                this.editForm.credit_cards_fixed = application.credit_cards_fixed;
                this.editForm.credit_cards_percentage = application.credit_cards_percentage;

                $('#modal-edit-application').modal('show');
            },

            /**
             * Update the application being edited.
             */
            update() {
                this.persistApplication(
                    'put',
                    '/channels/'+ this.channel_id +'/applications/' + this.editForm.id,
                    this.editForm,
                    '#modal-edit-application'
                );
            },

            /**
             * Persist the application to storage using the given form.
             */
            persistApplication(method, uri, form, modal) {
                form.errors = [];
                form.errors_obj = [];

                axios[method](uri, form)
                    .then(response => {
                        this.getApplications();

                        form.redirect = '';
                        form.webhook_url = '';
                        form.email = '';
                        form.mada_fixed = '';
                        form.mada_percentage = '';
                        form.credit_cards_fixed = '';
                        form.credit_cards_percentage = '';

                        form.errors = [];
                        form.errors_obj = [];

                        $(modal).modal('hide');

                        if (response.data.plainSecret) {
                            this.showApplicationSecret(response.data.plainSecret);
                        }
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                            var obj = error.response.data.errors;
                            form.errors_obj = Object.keys(obj).map(function(key, index) {
                                return {key: key,value: obj[key][0]};
                            });
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },
            showApplicationSecret(applicationSecret) {
                this.applicationSecret = applicationSecret;
                $('#modal-application-secret').modal('show');
            },
            deletes(application) {
                this.deleteId = application.id;

                $('#modal-delete-application').modal('show');
            },
            destroy() {
              axios.delete('/applications/' + this.deleteId )
                        .then(response => {
                            this.getApplications();
                            $('#modal-delete-application').modal('hide');
                        })
            },
            haveError(key, type=1) {
                if(type == 1){
                    return !!this.createForm.errors_obj.find(x => x.key === key)
                }else{
                    return !!this.editForm.errors_obj.find(x => x.key === key)
                }
            },
            errorMessage(key, type=1) {
                if(type == 1){
                    return this.createForm.errors_obj.find(x => x.key === key).value
                }else{
                    return this.editForm.errors_obj.find(x => x.key === key).value
                }
            }
        }
    }
</script>
