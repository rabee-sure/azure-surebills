<template>
  <section id="integrationIndexPage">
    <div class="title d-flex align-items-center justify-content-between mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Integration')}}</h1>
      <a class="d-flex align-items-center justify-content-center btn-primary text-white rounded-pill border-0 shadow-none" tabindex="-1" @click="showCreateApplicationForm"> {{ __('Create New Application')}}</a>
    </div><!-- title -->
    <span class="d-bock fs-6 mb-3 text-body"> {{ __('Applications')}}</span>
    <div class="notApplicationsYet d-flex align-items-center justify-content-center flex-column bg-white shadow-sm rounded-3 p-3" v-if="applications.length === 0">
      <i class="fal fa-desktop"></i>
      <span class="d-block text-center mt-3 text-capitalize">{{ __('You have not created any applications.')}}</span>
    </div><!-- notApplicationsYet -->
    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
      <div class="table-responsive">
        <table class="table table-striped table-hover" v-if="applications.length > 0">
          <thead>
            <tr>
              <th scope="col" class="text-center bg-transparent">{{ __('ID')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('Name')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('Secret')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('webhook URL')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('webhook Secret')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('Redirect Url')}}</th>
              <th scope="col" class="text-center bg-transparent"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="application in applications">
              <td class="text-center">{{ application.id }}</td>
              <td class="text-center">{{ application.name }}</td>
              <td class="text-center" dir="ltr"><code>{{ application.secret ? application.secret : '-' }}</code></td>
              <td class="text-center" dir="ltr">{{ application.webhook_url }}</td>
              <td class="text-center" dir="ltr"><code>{{ application.webhook_secret ? application.webhook_secret : '-' }}</code></td>
              <td class="text-center" dir="ltr">{{ application.redirect }}</td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <button type="button" @click="edit(application)" v-if="application.channel == null" class="rounded-3 border-0 shadow-none p-0 btn-primary d-flex align-items-center justify-content-center mx-1" data-bs-toggle="tooltip" data-bs-placement="top" :title="__('Edit')"><i class="fal fa-edit"></i></button>
                  <button type="button" @click="deletes(application)" v-if="application.channel == null" class="rounded-3 border-0 shadow-none p-0 mx-1 btn-danger d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" :title="__('Delete')"><i class="fal fa-trash-alt"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div><!-- blockArea -->

    <!-- Create Application Modal -->
    <div class="modal fade applicationModals" id="modal-create-application" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm rounded-3">
          <div class="modal-header d-flex align-items-center justify-content-between">
            <h5 class="modal-title">{{ __('Create Application')}}</h5>
            <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
          </div>
          <div class="modal-body">
            <form role="form">
              <div class="form-group mb-3">
                <label for="create-application-name" class="d-block mb-2">{{ __('Name')}} <span class="requirement text-danger">*</span></label>
                <input id="create-application-name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" :class="{'is-invalid': haveError('name') }" @keyup.enter="store" v-model="createForm.name" :placeholder="__('Something your users will recognize and trust.')">
                <div class="invalid-feedback text-danger" v-if="haveError('name')">{{errorMessage('name')}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="redirect" class="d-block mb-2">{{ __('Redirect URL')}} <span class="requirement text-danger">*</span></label>
                <input type="url" inputmode="url" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" :class="{'is-invalid': haveError('redirect') }" name="redirect" id="redirect" @keyup.enter="store" v-model="createForm.redirect" :placeholder="__('Your application\'s authorization callback URL.')">
                <div class="invalid-feedback text-danger" v-if="haveError('redirect')">{{errorMessage('redirect')}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="webhook_url" class="d-block mb-2">{{ __('Webhook URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('webhook_url') }" type="url" inputmode="url" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="webhook_url" id="webhook_url" @keyup.enter="store" v-model="createForm.webhook_url">
                <div class="invalid-feedback text-danger" v-if="haveError('webhook_url')">{{errorMessage('webhook_url')}}</div>  
              </div><!-- form-group -->     
            </form>
          </div>
          <div class="modal-footer p-2">
            <button type="button" class="border-0 shadow-none rounded-3 btn-primary" @click="store">{{ __('Create')}}</button>
            <button type="button" class="border-0 shadow-none rounded-3 btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Application Modal -->
    <div class="modal fade applicationModals" id="modal-edit-application" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm rounded-3">
          <div class="modal-header d-flex align-items-center justify-content-between">
            <h5 class="modal-title">{{ __('Edit Application')}}</h5>
            <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
          </div>
          <div class="modal-body">
            <form role="form">
              <div class="form-group mb-3">
                <label for="webhook_url" class="d-block mb-2">{{ __('Name')}} <span class="requirement text-danger">*</span></label>
                <input id="edit-application-name" :class="{'is-invalid': haveError('name', 2) }" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" @keyup.enter="update" v-model="editForm.name">
                <span class="form-text text-muted d-block">{{ __('Something your users will recognize and trust.')}}</span>
                <div class="invalid-feedback text-danger" v-if="haveError('name', 2)">{{errorMessage('name', 2)}}</div>  
              </div><!-- form-group --> 
              <div class="form-group mb-3">
                <label class="col-md-3 col-form-label">{{ __('Redirect URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('redirect', 2) }" type="url" inputmode="url" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="redirect" @keyup.enter="update" v-model="editForm.redirect">
                <span class="form-text text-muted d-block">{{ __('Your application\'s authorization callback URL.')}}</span>
                <div class="invalid-feedback text-danger" v-if="haveError('redirect', 2)">{{errorMessage('redirect', 2)}}</div>  
              </div><!-- form-group -->            
              <div class="form-group mb-3">
                <label for="webhook_url" class="d-block mb-2">{{ __('Webhook URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('webhook_url', 2) }" type="url" inputmode="url" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="webhook_url" id="webhook_url" @keyup.enter="update" v-model="editForm.webhook_url">
                <div class="invalid-feedback text-danger" v-if="haveError('webhook_url', 2)">{{errorMessage('webhook_url', 2)}}</div>
              </div><!-- form-group -->               
            </form>
          </div>
          <!-- Modal Actions -->
          <div class="modal-footer p-2">
            <button type="button" class="border-0 shadow-none rounded-3 btn-primary" @click="update">{{ __('Save Changes')}}</button>
            <button type="button" class="border-0 shadow-none rounded-3 btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade modalDeleteApplication" id="modal-delete-application" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow-sm rounded-3">
          <div class="modal-body d-flex align-items-center justify-content-center flex-column">
            <div class="closeBtn d-flex align-items-center justify-content-end mb-3 w-100">
              <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
            </div><!-- closeBtn -->
            <span class="d-block text-center text-body mb-4 fs-5">{{ __('Are you sure you want to delete this item?')}}</span>
            <div class="btnsArea d-flex align-items-center justify-content-end flex-wrap">
              <button type="button" class="border-0 shadow-none rounded-3 btn-danger mx-2" @click="destroy">{{ __('Delete')}}</button>
              <button type="button" class="border-0 shadow-none rounded-3 btn-light mx-2" data-bs-dismiss="modal">{{ __('Close')}}</button>
            </div>
          </div>
        </div>
      </div>
    </div>

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
                this.getApplications();

                $('#modal-create-application').on('shown.bs.modal', () => {
                    $('#create-application-name').focus();
                });

                $('#modal-edit-application').on('shown.bs.modal', () => {
                    $('#edit-application-name').focus();
                });
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