<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Mode de vente : unit (à l'unité) | pack (vendu en pack) | meter (au mètre)
            $table->string('pricing_mode')->default('unit')->after('design_service_price');
            // Libellé de l'unité affichée (pièce, pack, mètre…) — personnalisable
            $table->string('unit_label')->nullable()->after('pricing_mode');
            // Quantité minimum commandable
            $table->unsignedInteger('min_quantity')->default(1)->after('unit_label');
            // Incrément de quantité (pas)
            $table->unsignedInteger('quantity_step')->default(1)->after('min_quantity');
            // Nombre d'unités par pack (quand pricing_mode = pack), informatif
            $table->unsignedInteger('pack_size')->nullable()->after('quantity_step');
            // Produit personnalisable (étape design) ou vendu tel quel
            $table->boolean('is_customizable')->default(true)->after('pack_size');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['pricing_mode', 'unit_label', 'min_quantity', 'quantity_step', 'pack_size', 'is_customizable']);
        });
    }
};
