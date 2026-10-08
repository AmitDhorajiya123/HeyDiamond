<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diamonds', function (Blueprint $table) {
            $table->id();
            $table->string('stockNo')->nullable()->unique();
            $table->text('status')->nullable();
            $table->text('shape')->nullable();
            $table->text('Diamond_Type')->nullable();
            $table->text('weight')->nullable();
            $table->text('color')->nullable();
            $table->text('clarity')->nullable();
            $table->text('cut')->nullable();
            $table->text('polish')->nullable();
            $table->text('symmetry')->nullable();
            $table->text('fluorescenceIntensity')->nullable();
            $table->text('fluorescenceColor')->nullable();
            $table->text('measurements')->nullable();
            $table->text('shade')->nullable();
            $table->text('BGM')->nullable();
            $table->text('milky')->nullable();
            $table->text('eyeClean')->nullable();
            $table->text('lab')->nullable();
            $table->text('reportNo')->nullable();
            $table->text('reportDate')->nullable();
            $table->text('location')->nullable();
            $table->text('treatment')->nullable();
            $table->text('ratio')->nullable();
            $table->text('growthType')->nullable();
            $table->text('rapPricePerCt')->nullable();
            $table->text('depth')->nullable();
            $table->text('table')->nullable();
            $table->text('girdleThin')->nullable();
            $table->text('girdleThick')->nullable();
            $table->text('gridlePer')->nullable();
            $table->text('girdleCondition')->nullable();
            $table->text('culetSize')->nullable();
            $table->text('ciletCondition')->nullable();
            $table->text('crownHeight')->nullable();
            $table->text('crownAngle')->nullable();
            $table->text('pavilionDepth')->nullable();
            $table->text('pavilionAngle')->nullable();
            $table->text('starLength')->nullable();
            $table->text('inscription')->nullable();
            $table->text('laserInscription')->nullable();
            $table->text('certiComment')->nullable();
            $table->text('keyToSymbole')->nullable();
            $table->text('inclusion')->nullable();
            $table->text('whiteInclusion')->nullable();
            $table->text('blackInclusion')->nullable();
            $table->text('openInclusion')->nullable();
            $table->text('fancyColor')->nullable();
            $table->text('fancyColorIntensity')->nullable();
            $table->text('fancyColorOvertone')->nullable();
            $table->text('country')->nullable();
            $table->text('state')->nullable();
            $table->text('city')->nullable();
            $table->text('certiFile')->nullable();
            $table->text('videoLink')->nullable();
            $table->text('vdbVideo')->nullable();
            $table->text('imageLink')->nullable();
            $table->text('heartImage')->nullable();
            $table->text('arrowImage')->nullable();
            $table->text('assetImage')->nullable();
            $table->text('heartAndArrow')->nullable();
            $table->text('luster')->nullable();
            $table->text('brand')->nullable();
            $table->text('origin')->nullable();
            $table->text('COP')->nullable();
            $table->text('DNA')->nullable();
            $table->text('handyVideo')->nullable();
            $table->text('supplierComment')->nullable();
            $table->text('tinge')->nullable();
            $table->text('memberComment')->nullable();
            $table->text('isPair')->nullable();
            $table->text('pairStock')->nullable();
            $table->text('tweezerVideo')->nullable();
            $table->text('length')->nullable();
            $table->text('height')->nullable();
            $table->text('width')->nullable();
            $table->text('pricePerCt')->nullable();
            $table->text('totalPrice')->nullable();
            $table->text('discount')->nullable();
            $table->text('exclusive')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diamonds');
    }
};
