<template>
  <section id="integrationIndexPage" v-if="userPermissions.includes('show applications')">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-4 mb-6">
      <div class="d-flex flex-column gap-1">
        <h4 class="mb-0">{{ __('Integration')}}</h4>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-custom-icon mb-6">
            <li class="breadcrumb-item">
              <a href="/account" :title="__('Settings')">{{ __('Settings')}}</a>
              <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
            </li>
            <li class="breadcrumb-item active">{{ __('Integration') }}</li>
          </ol>
        </nav>
      </div><!-- d-flex -->
      <div v-if="merchantSettings.includes('allow_create_integration_application')">
        <button type="button" class="btn btn-primary waves-effect waves-light" v-if="userPermissions.includes('create application')" @click="showCreateApplicationForm">
          <span class="icon-xs icon-base ti ti-plus me-2"></span> {{ __('Create New Application')}}
        </button>
      </div>
    </div><!-- d-flex -->

    <div class="card">
      <h5 class="card-title p-4 m-0">{{ __('Applications')}}</h5>
      <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column py-5" v-if="applications.length === 0">
        <i class="ti ti-users ti-xl"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('You have not created any applications.')}}</span>
      </div><!-- no_bills_yet -->
      <div class="table-responsive text-nowrap" v-if="applications.length > 0">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col" class="fw-bold">{{ __('ID')}}</th>
              <th scope="col" class="fw-bold">{{ __('Name')}}</th>
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
              <td dir="ltr"><code>{{ application.secret ? application.secret : '-' }}</code></td>
              <td>{{ application.webhook_url }}</td>
              <td><code>{{ application.webhook_secret ? application.webhook_secret : '-' }}</code></td>
              <td>{{ application.redirect }}</td>
              <td v-if="userPermissions.includes('update application') || userPermissions.includes('delete application')">
                <div class="d-flex align-items-center justify-content-start gap-2">
                  <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" :title="__('Edit')" class="btn btn-icon text-white btn-sm btn-info waves-effect waves-light" @click="edit(application)" v-if="application.channel == null && userPermissions.includes('update application')">
                    <span class="icon-base ti ti-edit icon-18px"></span>
                  </button>
                  <button type="button" class="btn btn-icon text-white btn-sm btn-danger waves-effect waves-light" @click="deletes(application)" v-if="application.channel == null && userPermissions.includes('delete application')">
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
                <label for="create-application-name" class="form-label">{{ __('Name')}} <span class="requirement text-danger">*</span></label>
                <input id="create-application-name" type="text" class="form-control" :class="{'is-invalid': haveError('name') }" @keyup.enter="store" v-model="createForm.name" :placeholder="__('Something your users will recognize and trust.')">
                <div class="invalid-feedback text-danger" v-if="haveError('name')">{{errorMessage('name')}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="create-application-redirect" class="form-label">{{ __('Redirect URL')}} <span class="requirement text-danger">*</span></label>
                <input id="create-application-redirect" type="url" inputmode="url" class="form-control" :class="{'is-invalid': haveError('redirect') }" name="redirect" @keyup.enter="store" v-model="createForm.redirect" :placeholder="__('Your application\'s authorization callback URL.')">
                <div class="invalid-feedback text-danger" v-if="haveError('redirect')">{{errorMessage('redirect')}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="create-application-webhook_url" class="form-label">{{ __('Webhook URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('webhook_url') }" type="url" inputmode="url" class="form-control" name="webhook_url" id="webhook_url" @keyup.enter="store" v-model="createForm.webhook_url">
                <div class="invalid-feedback text-danger" v-if="haveError('webhook_url')">{{errorMessage('webhook_url')}}</div>
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
                <label for="edit-application-name" class="form-label">{{ __('Name')}} <span class="requirement text-danger">*</span></label>
                <input id="edit-application-name" :class="{'is-invalid': haveError('name', 2) }" type="text" class="form-control" @keyup.enter="update" v-model="editForm.name">
                <span class="form-text text-muted d-block">{{ __('Something your users will recognize and trust.')}}</span>
                <div class="invalid-feedback text-danger" v-if="haveError('name', 2)">{{errorMessage('name', 2)}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="edit-application-redirect" class="form-label">{{ __('Redirect URL')}} <span class="requirement text-danger">*</span></label>
                <input id="edit-application-redirect" :class="{'is-invalid': haveError('redirect', 2) }" type="url" inputmode="url" class="form-control" name="redirect" @keyup.enter="update" v-model="editForm.redirect">
                <span class="form-text text-muted d-block">{{ __('Your application\'s authorization callback URL.')}}</span>
                <div class="invalid-feedback text-danger" v-if="haveError('redirect', 2)">{{errorMessage('redirect', 2)}}</div>
              </div><!-- col -->
              <div class="col">
                <label for="webhook_url" class="form-label">{{ __('Webhook URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('webhook_url', 2) }" type="url" inputmode="url" class="form-control" name="webhook_url" id="webhook_url" @keyup.enter="update" v-model="editForm.webhook_url">
                <div class="invalid-feedback text-danger" v-if="haveError('webhook_url', 2)">{{errorMessage('webhook_url', 2)}}</div>
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
            <button type="button" class="btn btn-danger" @click="destroy">{{__('Delete')}}</button>
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
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>

          <!-- Modal Actions -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close')}}</button>
          </div>
        </div>
      </div>
    </div>
  </section><!-- integrationIndexPage -->
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                applications: [],
                userPermissions: [],
                merchantSettings: [],

                applicationSecret: null,
                deleteId: null,

                createForm: {
                    errors: [],
                    errors_obj: [],
                    name: '',
                    redirect: '',
                    webhook_url: '',
                    confidential: true
                },

                editForm: {
                    errors: [],
                    errors_obj: [],
                    name: '',
                    redirect: '',
                    webhook_url: '',
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
                this.getMerchantSettings();
            },

            /**
             * Get all of the OAuth applications for the user.
             */
            getApplications() {
                axios.get('/applications')
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

            getMerchantSettings(){
              axios.get('/merchant-settings')
              .then(response => {
                  this.merchantSettings = response.data;
                  console.log(this.merchantSettings);
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
                    '/applications',
                    this.createForm,
                    '#modal-create-application'
                );
            },

            /**
             * Edit the given application.
             */
            edit(application) {
                this.editForm.id = application.id;
                this.editForm.name = application.name;
                this.editForm.redirect = application.redirect;
                this.editForm.webhook_url = application.webhook_url;

                $('#modal-edit-application').modal('show');
            },

            /**
             * Update the application being edited.
             */
            update() {
                this.persistApplication(
                    'put',
                    'applications/' + this.editForm.id,
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

                        form.name = '';
                        form.redirect = '';
                        form.webhook_url = '';
                        form.errors = [];
                        form.errors_obj = [];

                        $(modal).modal('hide');

                        if (response.data.plainSecret) {
                            this.showApplicationSecret(response.data.plainSecret);
                        }
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            var obj = error.response.data.errors;
                            form.errors_obj = Object.keys(obj).map(function(key, index) {
                                return {key: key,value: obj[key][0]};
                            });

                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Show the given application secret to the user.
             */
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
                        });
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
