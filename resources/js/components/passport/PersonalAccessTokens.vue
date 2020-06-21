<template>
  <div class="Personal_Access_Tokenscard">
    <div class="card card-default">
      <div class="card-header">
        <span>Personal Access Tokens</span>
        <a class="btn btn-primary" tabindex="-1" @click="showCreateTokenForm">Create New Token</a>
      </div>
      <div class="card-body">
        <!-- No Tokens Notice -->
        <div class="not_created_personal" v-if="tokens.length === 0">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 333331 267872" shape-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" xmlns:v="https://vecta.io/nano"><path d="M208099 64019l42760 43315v20375l-20232 1v-15317h-16762V95629h-16043l-4638-15109c-7383 4925-16344 7811-25999 7811-25290 0-45802-19778-45802-44164C121383 19777 141895 0 167185 0c25287 0 45797 19778 45797 44167 0 7140-1761 13883-4881 19852h-1zM0 123339h67357v119843H0V123339zm80916 109783V132962h45050c19099 3425 38197 13780 57297 25805h34991c15839 955 24133 17007 8748 27555-12275 8996-28460 8487-45050 6998-11442-563-11943 14819 0 14870 4140 321 8653-650 12588-654 20711-20 37770-3979 48209-20340l5248-12246 52049-25806c26047-8576 44555 18671 25368 37615-37699 27429-76366 50000-115906 68231-28721 17470-57443 16877-86164 0l-42426-21869zm79685-203106c4513 0 8177 3661 8177 8177s-3663 8177-8177 8177c-4516 0-8177-3661-8177-8177s3661-8177 8177-8177z"/></svg>
          <span>You have not created any personal access tokens.</span>
        </div>
        <!-- Personal Access Tokens -->
        <table class="table table-borderless mb-0" v-if="tokens.length > 0">
          <thead>
            <tr>
              <th>Name</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="token in tokens">
              <!-- Client Name -->
              <td style="vertical-align: middle;">{{ token.name }}</td>
              <!-- Delete Button -->
              <td style="vertical-align: middle;">
                <a class="action-link text-danger" @click="revoke(token)">Delete</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Token Modal -->
    <div class="modal fade" id="modal-create-token" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create Token</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body">
            <!-- Form Errors -->
            <div class="alert alert-danger" v-if="form.errors.length > 0">
              <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
              <br>
              <ul>
                <li v-for="error in form.errors">{{ error }}</li>
              </ul>
            </div>
            <!-- Create Token Form -->
            <form role="form" @submit.prevent="store">
              <!-- Name -->
              <div class="form-group row">
                <label class="col-md-4 col-form-label">Name</label>
                <div class="col-md-6">
                  <input id="create-token-name" type="text" class="form-control" name="name" v-model="form.name">
                </div>
              </div>
              <!-- Scopes -->
              <div class="form-group row" v-if="scopes.length > 0">
                <label class="col-md-4 col-form-label">Scopes</label>
                <div class="col-md-6">
                  <div v-for="scope in scopes">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" @click="toggleScope(scope.id)" :checked="scopeIsAssigned(scope.id)">
                        {{ scope.id }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <!-- Modal Actions -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="store">Create</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Access Token Modal -->
    <div class="modal fade" id="modal-access-token" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Personal Access Token</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body">
            <p>Here is your new personal access token. This is the only time it will be shown so don't lose it! <br> You may now use this token to make API requests.</p>
            <textarea class="form-control" rows="10">{{ accessToken }}</textarea>
          </div>
          <!-- Modal Actions -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                accessToken: null,

                tokens: [],
                scopes: [],

                form: {
                    name: '',
                    scopes: [],
                    errors: []
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
                this.getTokens();
                this.getScopes();

                $('#modal-create-token').on('shown.bs.modal', () => {
                    $('#create-token-name').focus();
                });
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getTokens() {
                axios.get('/oauth/personal-access-tokens')
                        .then(response => {
                            this.tokens = response.data;
                        });
            },

            /**
             * Get all of the available scopes.
             */
            getScopes() {
                axios.get('/oauth/scopes')
                        .then(response => {
                            this.scopes = response.data;
                        });
            },

            /**
             * Show the form for creating new tokens.
             */
            showCreateTokenForm() {
                $('#modal-create-token').modal('show');
            },

            /**
             * Create a new personal access token.
             */
            store() {
                this.accessToken = null;

                this.form.errors = [];

                axios.post('/oauth/personal-access-tokens', this.form)
                        .then(response => {
                            this.form.name = '';
                            this.form.scopes = [];
                            this.form.errors = [];

                            this.tokens.push(response.data.token);

                            this.showAccessToken(response.data.accessToken);
                        })
                        .catch(error => {
                            if (typeof error.response.data === 'object') {
                                this.form.errors = _.flatten(_.toArray(error.response.data.errors));
                            } else {
                                this.form.errors = ['Something went wrong. Please try again.'];
                            }
                        });
            },

            /**
             * Toggle the given scope in the list of assigned scopes.
             */
            toggleScope(scope) {
                if (this.scopeIsAssigned(scope)) {
                    this.form.scopes = _.reject(this.form.scopes, s => s == scope);
                } else {
                    this.form.scopes.push(scope);
                }
            },

            /**
             * Determine if the given scope has been assigned to the token.
             */
            scopeIsAssigned(scope) {
                return _.indexOf(this.form.scopes, scope) >= 0;
            },

            /**
             * Show the given access token to the user.
             */
            showAccessToken(accessToken) {
                $('#modal-create-token').modal('hide');

                this.accessToken = accessToken;

                $('#modal-access-token').modal('show');
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                axios.delete('/oauth/personal-access-tokens/' + token.id)
                        .then(response => {
                            this.getTokens();
                        });
            }
        }
    }
</script>


<style lang="scss" scoped>
.Personal_Access_Tokenscard {
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
        color: #fff;
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
    .not_created_personal {
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
    } /* not_created_personal */
  } /* card-body */
} /* Personal_Access_Tokenscard */
</style>