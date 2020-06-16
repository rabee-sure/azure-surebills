@extends('layouts.auth')
@section('title', __('ٌegister') )

@section('content')

        <div class="row h-100">
          <div class="col-12 col-md-10 mx-auto my-auto">
            <div class="card auth-card">
              <div class="position-relative image-side ">
                <p class=" text-white h2">{{ __('Start Sending Bills') }}</p>
                <p class="white mb-0">
                    {{ __('Please use this form to register.') }}
                    <br>
                    {{ __('If you are a member, please') }} <a href="login.html" class="white">login</a>.</p>
              </div>
              <div class="form-side">
                <a href="index.html"><span class="logo-single"></span></a>
                <h6 class="mb-4">Register</h6>
                <form>
                  <div class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                      <label class="form-group has-float-label mb-4">
                        <input class="form-control" type="text" />
                        <span>Business Name</span>
                      </label>
                    </div><!-- col-12 -->
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                      <label class="form-group has-float-label mb-4">
                        <input class="form-control" type="text" />
                        <span>Instagram Account</span>
                      </label>
                    </div><!-- col-12 -->
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                      <label class="form-group has-float-label mb-4">
                        <input class="form-control" type="text" />
                        <span>Full Name</span>
                      </label>
                    </div><!-- col-12 -->
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                      <label class="form-group has-float-label mb-4">
                        <input class="form-control" type="email" />
                        <span>E-mail</span>
                      </label>
                    </div><!-- col-12 -->
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                      <label class="form-group has-float-label mb-4">
                        <input class="form-control" type="tel" />
                        <span>Mobile Number</span>
                      </label>
                    </div><!-- col-12 -->
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                      <label class="form-group has-float-label mb-4">
                        <input class="form-control" type="password" placeholder="" />
                        <span>Password</span>
                      </label>
                    </div><!-- col-12 -->
                  </div><!-- row -->
                  <div class="custom-control custom-checkbox mb-4">
                    <input type="checkbox" class="custom-control-input" id="customCheckThis">
                    <label class="custom-control-label" for="customCheckThis">
                      I agree to <a href="#" title="Terms & Conditions"  data-toggle="modal" data-target=".bd-example-modal-lg">Terms & Conditions</a>
                    </label>
                  </div>
                  <div class="d-flex justify-content-end align-items-center">
                    <button class="btn btn-primary btn-lg btn-shadow" type="submit">{{ __('Register') }}</button>
                  </div>
                </form>
                <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Terms & Conditions</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
