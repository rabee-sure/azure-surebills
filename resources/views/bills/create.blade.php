@extends('layouts.app')
@section('title', 'Page Title')
@section('content')
  <div class="row">
    <div class="col-12">
      <h1>Create Bill</h1>
      <div class="separator mb-5"></div>
    </div>
    <div class="col-12">
      <div class="create_bill_page card mb-4">
        <div class="card-body">
          <form>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail1">Customer Name</label>
                <input type="text" class="form-control" id="inputEmail1" placeholder="Customer Name">
              </div><!-- form-group -->
              <div class="form-group col-md-6">
                <label for="MobileNumber">Mobile Number</label>
                <input type="tel" class="form-control _parseArabicNumbers" id="MobileNumber" placeholder="05XXXXXXXX" maxlength="10">
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail1">Email</label>
                <input type="email" class="form-control" id="inputEmail1" placeholder="Email">
              </div><!-- form-group -->
              <div class="form-group col-md-6">
                <label for="inputEmail2">Special Note</label>
                <input type="text" class="form-control" id="inputEmail2" placeholder="Special Note">
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Due Date</label>
                <input class="form-control datepicker" placeholder="Due Date">
              </div><!-- form-group -->
              <div class="form-group col-md-6">
                <label>Expiry Date</label>
                <select class="form-control">
                  <option value="AK">1 Day</option>
                  <option value="AK">2 Day</option>
                  <option value="AK">3 Day</option>
                  <option value="AK">7 Day</option>
                  <option value="AK" selected="selected">30 Day</option>
                  <option value="AK">60 Day</option>
                  <option value="AK">90 Day</option>
                  <option value="Never">Never</option>
                </select>
              </div><!-- form-group -->
            </div><!-- form-row -->
            <hr>
            <h1 class="mb-3">Bill items</h1>
            <div class="form-row mb-2">
              <div class="form-group col-12 col-md-4 col-lg-6 col-xl-6">
                <label for="inputEmail1">Product/Service</label>
                <input type="text" class="form-control" id="Name" placeholder="Name">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                <label for="Price">Product/Service Price</label>
                <input type="text" class="form-control" id="Price" placeholder="Price">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                <label for="Price">Quantity</label>
                <input type="text" class="form-control" id="Quantity" placeholder="Quantity">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1">
                <label for="Price">Total</label>
                <input type="text" class="form-control text-center font-weight-bold" id="Quantity" value="5451" disabled>
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="form-row mb-2">
              <div class="form-group col-12 col-md-4 col-lg-6 col-xl-6">
                <label for="inputEmail1">Product/Service</label>
                <input type="text" class="form-control" id="Name" placeholder="Name">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                <label for="Price">Product/Service Price</label>
                <input type="text" class="form-control" id="Price" placeholder="Price">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                <label for="Price">Quantity</label>
                <input type="text" class="form-control" id="Quantity" placeholder="Quantity">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1">
                <label for="Price">Total</label>
                <input type="text" class="form-control text-center font-weight-bold" id="Quantity" value="5451" disabled>
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1">
                <label for="Delete" class="d-block">Delete</label>
                <button type="button" class="btn btn-danger default d-block w-100">X</button>
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="form-row mb-2">
              <div class="form-group col-12 col-md-4 col-lg-6 col-xl-6">
                <label for="inputEmail1">Product/Service</label>
                <input type="text" class="form-control" id="Name" placeholder="Name">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                <label for="Price">Product/Service Price</label>
                <input type="text" class="form-control" id="Price" placeholder="Price">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-2 col-lg-2 col-xl-2">
                <label for="Price">Quantity</label>
                <input type="text" class="form-control" id="Quantity" placeholder="Quantity">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1">
                <label for="Price">Total</label>
                <input type="text" class="form-control text-center font-weight-bold" id="Quantity" value="5451" disabled>
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-1 col-lg-1 col-xl-1">
                <label for="Delete" class="d-block">Delete</label>
                <button type="button" class="btn btn-danger default d-block w-100">X</button>
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="d-flex justify-content-end my-3">
              <button type="button" class="btn btn-primary btn-lg">Add Item</button>
            </div><!-- d-flex  -->
            <hr>
            <h1 class="mb-3">Additonal Details</h1>
            <div class="form-row">
              <div class="form-group col-6">
                <label for="inputEmail1">Add Discount</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input class="custom-switch-input" id="Discount" type="checkbox">
                  <label class="custom-switch-btn" for="Discount"></label>
                </div>
              </div><!-- form-group -->
              <div class="form-group col-6">
                <label for="inputEmail1">Add Tax</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input class="custom-switch-input" id="Tax" type="checkbox">
                  <label class="custom-switch-btn" for="Tax"></label>
                </div>
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="form-row mb-2">
              <div class="form-group col-12 col-md-3 col-lg-3 col-xl-3">
                <label for="type">Discount type</label>
                <select class="form-control">
                  <option value="AK">Percentage Discount (%)</option>
                  <option value="AK">Percentage Discount (%)</option>
                  <option value="AK">Percentage Discount (%)</option>
                  <option value="AK">Percentage Discount (%)</option>
                </select>
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-3 col-lg-3 col-xl-3">
                <label for="Price">Discount Value</label>
                <input type="text" class="form-control" id="Discount_Value">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-3 col-lg-3 col-xl-3">
                <label for="Tax">Tax Name</label>
                <input type="text" class="form-control" id="Tax">
              </div><!-- form-group -->
              <div class="form-group col-12 col-md-3 col-lg-3 col-xl-3">
                <label for="Tax">Tax Value</label>
                <input type="text" class="form-control" id="Value">
              </div><!-- form-group -->
            </div><!-- form-row -->
            <hr>
            <h1 class="mb-3">Send The Bill To Customer</h1>
            <div class="form-row">
              <div class="form-group col-6">
                <label for="inputEmail1">Send SMS</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input class="custom-switch-input" id="SMS2" type="checkbox">
                  <label class="custom-switch-btn" for="SMS2"></label>
                </div>
              </div><!-- form-group -->
              <div class="form-group col-6">
                <label for="inputEmail1">Send Email</label>
                <div class="custom-switch custom-switch-primary mb-2">
                  <input class="custom-switch-input" id="Email2" type="checkbox">
                  <label class="custom-switch-btn" for="Email2"></label>
                </div>
              </div><!-- form-group -->
            </div><!-- form-row -->
            <div class="d-flex justify-content-start mt-3">
              <button type="button" class="btn btn-primary btn-lg">Send</button>
            </div><!-- d-flex  -->
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection