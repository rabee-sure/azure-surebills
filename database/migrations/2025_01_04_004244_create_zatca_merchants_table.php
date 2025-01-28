<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZatcaMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zatca_merchants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('email')->unique();
            $table->string('business_name_en');
            $table->string('vat_registration_number');
            $table->string('tin');
            $table->string('crn');
            $table->string('invoices_type');
            $table->string('business_category');
            $table->string('building_no');
            $table->string('street_name');
            $table->string('district');
            $table->string('city');
            $table->string('postal_code');
            $table->string('additional_number');
            $table->string('other_buyer_id');
            $table->string('otp');
            $table->text('zatca_pih')->nullable();
            $table->longText('complianceCertificate')->nullable();
            $table->longText('complianceSecret')->nullable();
            $table->string('complianceRequestID')->nullable();
            $table->longText('productionCertificate')->nullable();
            $table->longText('productionCertificateSecret')->nullable();
            $table->string('productionCertificateRequestID')->nullable();
            $table->longText('privateKey')->nullable();
            $table->longText('publicKey')->nullable();
            $table->longText('csrKey')->nullable();
            $table->longText('configData')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zatca_merchants');
    }
}
