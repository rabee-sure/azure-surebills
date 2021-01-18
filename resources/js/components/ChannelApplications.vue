<template>
  <div class="Applications">
    <div class="card card-default">
      <div class="card-header">
        <span> {{ __('Applications')}}</span>
        <a class="btn btn-primary" tabindex="-1" @click="showCreateApplicationForm">
          {{ __('Create New Application')}}
        </a>
      </div>
      <div class="card-body">
        <!-- Current Applications -->
        <div class="not_created_OAuth" v-if="applications.length === 0">
          <svg xmlns="http://www.w3.org/2000/svg" height="682.667" viewBox="0 -4 512 512" width="682.667" xmlns:v="https://vecta.io/nano"><path d="M399.953 235.2L400 100 200 0 0 100v132.465c-.078 82.953 41.383 160.437 110.44 206.398l89.56 59.68 68.992-45.957c37.3 47.766 101.703 65.066 157.906 42.414s90.62-79.78 84.37-140.055-51.66-109.125-111.316-119.762zM200 479.344l-80.68-53.777A231.66 231.66 0 0 1 16 232.465V109.887l184-92 184 92v123.465c-2.656-.16-5.3-.406-8-.406s-5.352.246-8 .406V119.754L200 35.762l-168 84v112.703a215.75 215.75 0 0 0 96.199 179.773l71.8 47.84 52.336-34.87a135.57 135.57 0 0 0 7.566 14.211zm46.352-69.367L200 440.855l-62.918-41.926c-3.105-2.074-6.113-4.25-9.082-6.48v-71.504h-16v58.078c-8.78-8.14-16.812-17.06-24-26.64v-46.488c.004-11 7.484-20.6 18.152-23.27l54.254-13.602A39.95 39.95 0 0 0 192 304.143v32.8h16v-32.8a39.94 39.94 0 0 0 31.586-35.121l35.613 8.945a135.31 135.31 0 0 0-28.848 132zM160 168.945h22.113a84.28 84.28 0 0 0 35.72-8H220l20 26.664v33.336l-26.664 20h-26.672l-26.664-20zm-15.336-16a31.3 31.3 0 0 1 17.137-23.121 80.78 80.78 0 0 1 56.176-3.535l1.84 2.656H232a8 8 0 0 1 8 8v24l-12-16h-13.887l-1.687.84c-9.418 4.7-19.793 7.145-30.312 7.16zm79.336 100v12c0 13.254-10.746 24-24 24s-24-10.746-24-24v-12l5.336 4h37.328zm16-.312v-11.687l16-12v-92c0-13.258-10.746-24-24-24h-5.32c-11.574-7.035-40.4-8.457-60.8-2.074A49.79 49.79 0 0 0 128 160.946v8h16v60l16 12v11.648l-57.742 14.52A39.93 39.93 0 0 0 72 305.894v21.602c-15.766-29.2-24.016-61.852-24-95.03V129.648l152-76 152 76v102.816c0 .918-.055 1.832-.062 2.742a135.38 135.38 0 0 0-63.297 29.648zm136 236.313c-66.273 0-120-53.727-120-120s53.727-120 120-120 120 53.723 120 120c-.074 66.242-53.758 119.922-120 120zm40-352h96v16h-96zm0 32h64v16h-64zm0 32h32v16h-32zm0 0"/><path d="M346.543 363.29l-28.277-28.28-45.258 45.25 73.535 73.535 124.45-124.45-45.258-45.246zm0 67.88l-50.9-50.9 22.633-22.625 28.277 28.277 79.2-79.2 22.633 22.625zm0 0"/></svg>
          <span>{{ __('You have not created any applications.')}}</span>
        </div>
        <div class="table-responsive">
          <table class="Applications_table table table-borderless mb-0" v-if="applications.length > 0">
            <thead>
              <tr>
                <th scope="col">{{ __('ID')}}</th>
                <th scope="col">{{ __('Name')}}</th>
                <th scope="col">{{ __('Client Email')}}</th>
                <th scope="col">{{ __('Secret')}}</th>
                <th scope="col">{{ __('webhook URL')}}</th>
                <th scope="col">{{ __('webhook Secret')}}</th>
                <th scope="col">{{ __('Redirect Url')}}</th>
                <th scope="col"></th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="application in applications">
                <!-- ID -->
                <td style="vertical-align: middle;">{{ application.id }}</td>
                <!-- Name -->
                <td style="vertical-align: middle;">{{ application.name }}</td>
                <td style="vertical-align: middle;">{{ application.email}}</td>
                <!-- Secret -->
                <td style="vertical-align: middle;"><code>{{ application.secret ? application.secret : '-' }}</code></td>
                <td style="vertical-align: middle;">{{ application.webhook_url }}</td>
                <td style="vertical-align: middle;"><code>{{ application.webhook_secret ? application.webhook_secret : '-' }}</code></td>
                <td style="vertical-align: middle;">{{ application.redirect }}</td>
                <!-- Edit Button -->
                <td style="vertical-align: middle;">
                  <a class="action-link" tabindex="-1" @click="edit(application)">{{ __('Edit')}}</a>
                </td>
                <!-- Delete Button -->
                <td style="vertical-align: middle;">
                  <a class="action-link text-danger" @click="destroy(application)">{{ __('Delete')}}</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create Application Modal -->
    <div class="modal fade" id="modal-create-application" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ __('Create Application')}} </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body">
            <!-- Form Errors -->
            <div class="alert alert-danger" v-if="createForm.errors.length > 0">
              <p class="mb-0"><strong> {{ __('Whoops!')}}</strong> {{ __('Something went wrong!')}}</p>
              <br>
              <ul>
                <li v-for="error in createForm.errors">{{ error }}</li>
              </ul>
            </div>
            <!-- Create Application Form -->
            <form role="form">     

              <!-- Email -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Client Email')}} *</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="email" @keyup.enter="store" v-model="createForm.email">
                </div>
              </div>   

              <!-- Redirect URL -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Redirect URL')}} *</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="redirect" @keyup.enter="store" v-model="createForm.redirect">
                  <span class="form-text text-muted">{{ __('Your application\'s authorization callback URL.')}}</span>
                </div>
              </div>              
              <!-- Redirect URL -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Webhook URL')}} *</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="webhook_url" @keyup.enter="store" v-model="createForm.webhook_url">
                </div>
              </div>                         

              <!-- Mada Fixed -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Mada Fixed') }} *</label>
                <div class="col-md-8">
                  <input type="number" class="form-control" name="mada_fixed" @keyup.enter="store" v-model="createForm.mada_fixed">
                </div>
              </div>              

              <!-- Mada Percentage -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Mada Percentage') }} *</label>
                <div class="col-md-8">
                  <input type="number" class="form-control" name="mada_percentage" @keyup.enter="store" v-model="createForm.mada_percentage">
                </div>
              </div>              

              <!-- Credit Cards Fixed -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Credit Cards Fixed') }} *</label>
                <div class="col-md-8">
                  <input type="number" class="form-control" name="credit_cards_fixed" @keyup.enter="store" v-model="createForm.credit_cards_fixed">
                </div>
              </div>              

              <!-- Credit Cards Percentage -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Credit Cards Percentage') }} *</label>
                <div class="col-md-8">
                  <input type="number" class="form-control" name="credit_cards_percentage" @keyup.enter="store" v-model="createForm.credit_cards_percentage">
                </div>
              </div>              

            </form>
          </div>
          <!-- Modal Actions -->
          <div class="modal-footer">
            <button type="button" class="btn btn-primary mr-2" @click="store">{{ __('Create')}}</button>
            <button type="button" class="btn btn-secondary m-0" data-dismiss="modal">{{ __('Close')}}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Application Modal -->
    <div class="modal fade" id="modal-edit-application" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ __('Edit Application')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body">
            <!-- Form Errors -->
            <div class="alert alert-danger" v-if="editForm.errors.length > 0">
              <p class="mb-0"><strong>{{ __('Whoops!')}}</strong> {{ __('Something went wrong!')}}</p>
              <br>
              <ul>
                <li v-for="error in editForm.errors">{{ error }}</li>
              </ul>
            </div>
            <!-- Edit Application Form -->
            <form role="form">

              <!-- Email -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Client Email')}} *</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="email" @keyup.enter="update" v-model="editForm.email" disabled>
                </div>
              </div>  

              <!-- Redirect URL -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Redirect URL')}} *</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="redirect" @keyup.enter="update" v-model="editForm.redirect">
                  <span class="form-text text-muted">{{ __('Your application\'s authorization callback URL.')}}</span>
                </div>
              </div>    

              <!-- Webhook URL -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Webhook URL')}} *</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="webhook_url" @keyup.enter="update" v-model="editForm.webhook_url">
                </div>
              </div>              

              <!-- Mada Fixed -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Mada Fixed') }} *</label>
                <div class="col-md-8">
                  <input type="number" class="form-control" name="mada_fixed" @keyup.enter="update" v-model="editForm.mada_fixed">
                </div>
              </div>              

              <!-- Mada Percentage -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Mada Percentage') }} *</label>
                <div class="col-md-8">
                  <input type="number" class="form-control" name="mada_percentage" @keyup.enter="update" v-model="editForm.mada_percentage">
                </div>
              </div>              

              <!-- Credit Cards Fixed -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Credit Cards Fixed') }} *</label>
                <div class="col-md-8">
                  <input type="number" class="form-control" name="credit_cards_fixed" @keyup.enter="update" v-model="editForm.credit_cards_fixed">
                </div>
              </div>              

              <!-- Credit Cards Percentage -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">{{ __('Credit Cards Percentage') }} *</label>
                <div class="col-md-8">
                  <input type="number" class="form-control" name="credit_cards_percentage" @keyup.enter="update" v-model="editForm.credit_cards_percentage">
                </div>
              </div>              
            </form>
          </div>
          <!-- Modal Actions -->
          <div class="modal-footer">
            <button type="button" class="btn btn-primary mr-2" @click="update">{{ __('Save Changes')}}</button>
            <button type="button" class="btn btn-secondary m-0" data-dismiss="modal">{{ __('Close')}}</button>
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
  </div>
</template>

<script>
    export default {
        props: ['channel_id'],
        data() {
            return {
                applications: [],

                applicationSecret: null,

                createForm: {
                    errors: [],
                    email: '',
                    redirect: '',
                    webhook_url: '',
                    mada_fixed: '',
                    mada_percentage: '',
                    credit_cards_fixed: '',
                    credit_cards_percentage: '',
                    confidential: true
                },

                editForm: {
                    errors: [],
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
                axios.get('/channels/'+ this.channel_id +'/applications')
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

                        $(modal).modal('hide');

                        if (response.data.plainSecret) {
                            this.showApplicationSecret(response.data.plainSecret);
                        }
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
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

            /**
             * Destroy the given application.
             */
            destroy(application) {
                axios.delete('/channels/'+ this.channel_id +'/applications/' + application.id)
                        .then(response => {
                            this.getApplications();
                        });
            }
        }
    }
</script>

<style lang="scss" scoped>
  .Applications {
    margin: 20px auto;
    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      padding: 20px 20px 0px 20px;
      span {
        display: block;
        font-weight: bold;
        font-size: 17px;
        text-transform: capitalize;
        color: #000;
        [class="body-dark-mode"] & {
          color: #ffffff;
        } /* Dark Mode */
      } /* span */
      a {
        display: block;
        cursor: pointer;
        font-size: 14px;
        border-radius: 4px;
        padding: 5px 15px;
        color: #fff;
      } /* a */
    } /* card-header */
    .card-body {
      padding: 20px;
      .not_created_OAuth {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-flow: column;
        flex-wrap: wrap;
        padding: 30px 0;
        min-height: 200px;
        svg {
          height: 110px;
          fill: #ddd;
          width: auto;
          [class="body-dark-mode"] & {
            fill: #666;
          } /* Dark Mode */
        } /* svg */
        span {
          display: block;
          font-size: 16px;
          margin: 20px auto 0;
          text-transform: capitalize;
          color: #777;
          [class="body-dark-mode"] & {
            color: #666;
          } /* Dark Mode */
        } /* span */
      } /* not_created_OAuth */
      table.Applications_table {
        thead {
          th {
            background: #eee;
            vertical-align: middle;
            border: 1px solid #ddd !important;
            [class="body-dark-mode"] & {
              background: #313131;
              border: 1px solid #222222 !important;
            } /* Dark Mode */
          } /* th */
        } /* thead */
        tbody {
          td {
            background: #fff;
            vertical-align: middle;
            font-size: 13px;
            border: 1px solid #ddd !important;
            [class="body-dark-mode"] & {
              background: #232223;
              border: 1px solid #313131 !important;
            } /* Dark Mode */
            a.action-link {
              cursor: pointer;
            } /* a */
          } /* td */
        } /* tbody */
      } /* Applications_table */
    } /* card-body */
  } /* Applications */
</style>