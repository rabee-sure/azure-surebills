<template>
  <section id="channelApplicationsPage" v-if="userPermissions.includes('show applications')">
    <div class="title d-flex align-items-center justify-content-between mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Applications')}}</h1>
      <a class="d-flex align-items-center justify-content-center btn-primary text-white rounded-pill border-0 shadow-none" :title="__('Create New Application')" v-if="userPermissions.includes('create application')" @click="showCreateApplicationForm">{{ __('Create New Application')}}</a>
    </div><!-- title -->

    <div class="notApplicationsYet d-flex align-items-center justify-content-center flex-column bg-white shadow-sm rounded-3 p-3" v-if="applications.length === 0">
      <i class="fal fa-desktop"></i>
      <span class="d-block text-center mt-3 text-capitalize">{{ __('You have not created any applications.')}}</span>
    </div><!-- notApplicationsYet -->

    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3" v-if="applications.length > 0">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col" class="text-center bg-transparent">{{ __('ID')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('Name')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('Client Email')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('Secret')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('webhook URL')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('webhook Secret')}}</th>
              <th scope="col" class="text-center bg-transparent">{{ __('Redirect Url')}}</th>
              <th scope="col" class="text-center bg-transparent" v-if="userPermissions.includes('update application') || userPermissions.includes('delete application')"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="application in applications">
              <td class="text-center">{{ application.id }}</td>
              <td class="text-center">{{ application.name }}</td>
              <td class="text-center" dir="ltr">{{ application.email}}</td>
              <td class="text-center"><code dir="ltr">{{ application.secret ? application.secret : '-' }}</code></td>
              <td class="text-center" dir="ltr">{{ application.webhook_url }}</td>
              <td class="text-center"><code dir="ltr">{{ application.webhook_secret ? application.webhook_secret : '-' }}</code></td>
              <td class="text-center" dir="ltr">{{ application.redirect }}</td>
              <td class="text-center" v-if="userPermissions.includes('update application') || userPermissions.includes('delete application')">
                <div class="d-flex align-items-center justify-content-center">
                  <a href="#" v-if="userPermissions.includes('update application')" @click="edit(application)" class="rounded-3 border-0 shadow-none p-0 btn-primary d-flex align-items-center justify-content-center mx-1" data-bs-toggle="tooltip" data-bs-placement="top" :title="__('Edit')"><i class="fal fa-edit"></i></a>
                  <a href="#" v-if="userPermissions.includes('delete application')" @click="deletes(application)" class="rounded-3 border-0 shadow-none p-0 mx-1 btn-danger d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" :title="__('Delete')"><i class="fal fa-trash-alt"></i></a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div><!-- table-responsive -->
    </div><!-- blockArea -->

    <!-- Create Application Modal -->
    <div class="modal fade applicationModals" id="modal-create-application" tabindex="-1" role="dialog" v-if="userPermissions.includes('create application')">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm rounded-3">
          <div class="modal-header d-flex align-items-center justify-content-between">
            <h5 class="modal-title">{{ __('Create Application')}}</h5>
            <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
          </div>
          <div class="modal-body">
            <form role="form">
              <div class="form-group mb-3">
                <label for="email" class="d-block mb-2">{{ __('Client Email')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('email') }" type="email" inputmode="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="email" id="email" @keyup.enter="store" v-model="createForm.email">
                <div class="invalid-feedback text-danger" v-if="haveError('email')">{{errorMessage('email')}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="redirect" class="d-block mb-2">{{ __('Redirect URL')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('redirect') }" type="url" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="redirect" inputmode="url" id="redirect" @keyup.enter="store" v-model="createForm.redirect">
                <span class="form-text text-muted d-block mt-1">{{ __('Your application\'s authorization callback URL.')}}</span>
                <div class="invalid-feedback text-danger" v-if="haveError('redirect')">{{errorMessage('redirect')}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="webhook_url" class="d-block mb-2">{{ __('Webhook URL')}} <span class="requirement text-danger">*</span></label>
                <input  :class="{'is-invalid': haveError('webhook_url') }" type="url" inputmode="url" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="webhook_url" id="webhook_url" @keyup.enter="store" v-model="createForm.webhook_url">
                <div class="invalid-feedback text-danger" v-if="haveError('webhook_url')">{{errorMessage('webhook_url')}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="mada_fixed" class="d-block mb-2">{{ __('Mada Fixed') }} <span class="requirement text-danger">*</span></label>

                <input :class="{'is-invalid': haveError('mada_fixed') }" type="number" inputmode="numaric" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="mada_fixed" id="mada_fixed" @keyup.enter="store" v-model="createForm.mada_fixed" step="0.01">

                <div class="invalid-feedback text-danger" v-if="haveError('mada_fixed')">{{errorMessage('mada_fixed')}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="mada_percentage" class="d-block mb-2">{{ __('Mada Percentage') }}<span class="requirement">*</span></label>

                <input :class="{'is-invalid': haveError('mada_percentage') }" type="number" inputmode="numaric" id="mada_percentage" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="mada_percentage" @keyup.enter="store" v-model="createForm.mada_percentage"  step="0.01">

                <div class="invalid-feedback text-danger" v-if="haveError('mada_percentage')">{{errorMessage('mada_percentage')}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="credit_cards_fixed" class="d-block mb-2">{{ __('Credit Cards Fixed') }} <span class="requirement text-danger">*</span></label>

                <input :class="{'is-invalid': haveError('credit_cards_fixed') }" type="number" inputmode="numaric" id="credit_cards_fixed" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="credit_cards_fixed" @keyup.enter="store" v-model="createForm.credit_cards_fixed"  step="0.01">

                <div class="invalid-feedback text-danger" v-if="haveError('credit_cards_fixed')">{{errorMessage('credit_cards_fixed')}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="credit_cards_percentage" class="d-block mb-2">{{ __('Credit Cards Percentage') }}<span class="requirement">*</span></label>

                <input :class="{'is-invalid': haveError('credit_cards_percentage') }" type="number" inputmode="numaric" id="credit_cards_percentage" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="credit_cards_percentage" @keyup.enter="store" v-model="createForm.credit_cards_percentage"  step="0.01">

                <div class="invalid-feedback text-danger" v-if="haveError('credit_cards_percentage')">{{errorMessage('credit_cards_percentage')}}</div>
              </div><!-- form-group -->
            </form>
          </div><!-- modal-body -->
          <div class="modal-footer p-2">
            <button type="button" class="border-0 shadow-none rounded-3 btn-primary" @click="store">{{ __('Create')}}</button>
            <button type="button" class="border-0 shadow-none rounded-3 btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Application Modal -->
    <div class="modal fade applicationModals" id="modal-edit-application" tabindex="-1" role="dialog" v-if="userPermissions.includes('update application')">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm rounded-3">
          <div class="modal-header d-flex align-items-center justify-content-between">
            <h5 class="modal-title">{{ __('Edit Application')}}</h5>
            <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
          </div>
          <div class="modal-body">
            <form role="form">
              <div class="form-group mb-3">
                <label for="email" class="d-block mb-2">{{ __('Client Email')}} <span class="requirement text-danger">*</span></label>
                <input :class="{'is-invalid': haveError('email', 2) }" type="email" inputmode="email" id="email" class="form-control shadow-none bg-light border w-100 rounded-3 text-body" name="email" @keyup.enter="update" v-model="editForm.email" disabled>
                <div class="invalid-feedback" v-if="haveError('email', 2)">{{errorMessage('email', 2)}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="redirect" class="d-block mb-2">{{ __('Redirect URL')}} <span class="requirement text-danger">*</span></label>
                  <input :class="{'is-invalid': haveError('redirect', 2) }"  type="url" id="redirect" inputmode="url" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="redirect" @keyup.enter="update" v-model="editForm.redirect">
                <span class="form-text text-muted d-block mt-1">{{ __('Your application\'s authorization callback URL.')}}</span>
                <div class="invalid-feedback" v-if="haveError('redirect', 2)">{{errorMessage('redirect', 2)}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="webhook_url" class="d-block mb-2">{{ __('Webhook URL')}} <span class="requirement text-danger">*</span></label>
                  <input :class="{'is-invalid': haveError('webhook_url', 2) }" type="url" inputmode="url" id="webhook_url" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="webhook_url" @keyup.enter="update" v-model="editForm.webhook_url">
                <div class="invalid-feedback" v-if="haveError('webhook_url', 2)">{{errorMessage('webhook_url', 2)}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="mada_fixed" class="d-block mb-2">{{ __('Mada Fixed') }} <span class="requirement text-danger">*</span></label>

                <input :class="{'is-invalid': haveError('mada_fixed', 2) }" type="number" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="mada_fixed" id="mada_fixed" @keyup.enter="update" v-model="editForm.mada_fixed" step="0.01">

                <div class="invalid-feedback text-danger" v-if="haveError('mada_fixed', 2)">{{errorMessage('mada_fixed', 2)}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="mada_percentage" class="d-block mb-2">{{ __('Mada Percentage') }} <span class="requirement text-danger">*</span></label>

                <input :class="{'is-invalid': haveError('mada_percentage', 2) }" type="number" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="mada_percentage" id="mada_percentage" @keyup.enter="update" v-model="editForm.mada_percentage" step="0.01">

                <div class="invalid-feedback text-danger" v-if="haveError('mada_percentage', 2)">{{errorMessage('mada_percentage', 2)}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="credit_cards_fixed" class="d-block mb-2">{{ __('Credit Cards Fixed') }} <span class="requirement text-danger">*</span></label>

                <input :class="{'is-invalid': haveError('credit_cards_fixed', 2) }" type="number" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="credit_cards_fixed" id="credit_cards_fixed" @keyup.enter="update" v-model="editForm.credit_cards_fixed" step="0.01">

                <div class="invalid-feedback" v-if="haveError('credit_cards_fixed', 2)">{{errorMessage('credit_cards_fixed', 2)}}</div>
              </div><!-- form-group -->
              <div class="form-group mb-3">
                <label for="credit_cards_fixed" class="d-block mb-2">{{ __('Credit Cards Percentage') }} <span class="requirement text-danger">*</span></label>

                <input :class="{'is-invalid': haveError('credit_cards_percentage', 2) }"  type="number" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="credit_cards_percentage" id="credit_cards_percentage" @keyup.enter="update" v-model="editForm.credit_cards_percentage" step="0.01">

                <div class="invalid-feedback text-danger" v-if="haveError('credit_cards_percentage', 2)">{{errorMessage('credit_cards_percentage', 2)}}</div>
              </div><!-- form-group -->
            </form>
          </div>
          <div class="modal-footer p-2">
            <button type="button" class="border-0 shadow-none rounded-3 btn-primary" @click="update">{{ __('Save Changes')}}</button>
            <button type="button" class="border-0 shadow-none rounded-3 btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
          </div>
        </div>
      </div>
    </div>

     <div class="modal fade modalDeleteApplication" id="modal-delete-application" tabindex="-1" role="dialog" v-if="userPermissions.includes('delete application')">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow-sm rounded-3">
          <div class="modal-body d-flex align-items-center justify-content-center flex-column">
            <div class="closeBtn d-flex align-items-center justify-content-end mb-3 w-100">
              <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
            </div><!-- closeBtn -->
            <span class="d-block text-center text-body mb-4 fs-5">{{ __('Are you sure you want to delete this item?')}}</span>
            <div class="btnsArea d-flex align-items-center justify-content-end flex-wrap">
              <button type="button" class="border-0 shadow-none rounded-3 btn-danger mx-2" @click="destroy()">{{ __('Delete')}}</button>
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
