<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Option;
use App\Models\OptionGroup;
use App\Models\Page;
use App\Models\PriceTier;
use App\Models\Product;
use App\Models\ProductFamily;
use App\Models\Setting;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedSettings();
        $this->seedCatalog();
        $this->seedShipping();
        $this->seedPages();
    }

    private function seedAdmin(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bepack.dz'],
            ['name' => 'Admin BePack', 'password' => Hash::make('password')]
        );
    }

    private function seedSettings(): void
    {
        $defaults = [
            ['general', 'site_name', 'BePack'],
            ['general', 'tagline', 'Votre marque, bien emballée.'],
            ['general', 'currency', 'DZD'],
            ['general', 'contact_phone', '+213 555 00 00 00'],
            ['general', 'contact_email', 'contact@bepack.dz'],
            ['theme', 'primary_color', '#B5793A'],
            ['theme', 'accent_color', '#FF5A3C'],
            ['marketing', 'facebook_pixel_id', ''],
            ['marketing', 'facebook_capi_token', ''],
            ['marketing', 'pixel_purchase_event', 'Purchase'],
            ['shipping', 'free_shipping_threshold', ''],
        ];

        foreach ($defaults as [$group, $key, $value]) {
            Setting::firstOrCreate(['key' => $key], ['group' => $group, 'value' => $value]);
        }
    }

    private function seedCatalog(): void
    {
        $families = [
            [
                'name' => 'Boîtes en carton',
                'description' => 'Caisses et boîtes carton personnalisées pour l\'expédition e-commerce et l\'unboxing.',
                'products' => [
                    ['name' => 'Mailer e-commerce', 'short' => 'La boîte d\'expédition à votre marque.', 'tiers' => [50 => 120, 100 => 95, 250 => 78, 500 => 62, 1000 => 49]],
                    ['name' => 'Caisse américaine', 'short' => 'Robuste, idéale pour le transport en volume.', 'tiers' => [50 => 140, 100 => 110, 250 => 90, 500 => 72, 1000 => 58]],
                ],
            ],
            [
                'name' => 'Boîtes en papier',
                'description' => 'Boîtes et étuis papier premium pour cosmétique, food et cadeaux.',
                'products' => [
                    ['name' => 'Boîte cadeau à rabat', 'short' => 'Finition premium pour un unboxing mémorable.', 'tiers' => [50 => 160, 100 => 130, 250 => 105, 500 => 85, 1000 => 70]],
                ],
            ],
            [
                'name' => 'Sacs en plastique',
                'description' => 'Sacs et pochettes plastique personnalisés pour la livraison.',
                'products' => [
                    ['name' => 'Pochette d\'expédition', 'short' => 'Légère, étanche, à votre logo.', 'tiers' => [100 => 35, 250 => 28, 500 => 22, 1000 => 17, 2000 => 13]],
                ],
            ],
        ];

        foreach ($families as $fIndex => $f) {
            $family = ProductFamily::create([
                'name' => $f['name'],
                'slug' => Str::slug($f['name']),
                'description' => $f['description'],
                'sort_order' => $fIndex,
                'is_active' => true,
            ]);

            foreach ($f['products'] as $pIndex => $p) {
                $product = Product::create([
                    'product_family_id' => $family->id,
                    'name' => $p['name'],
                    'slug' => Str::slug($p['name']),
                    'short_description' => $p['short'],
                    'description' => 'Personnalisez ce produit avec votre design ou laissez notre équipe créer un visuel sur mesure. Tarif dégressif selon la quantité.',
                    'allow_upload' => true,
                    'allow_design_service' => true,
                    'design_service_price' => 3000,
                    'allow_custom_dimensions' => false,
                    'is_featured' => $pIndex === 0,
                    'sort_order' => $pIndex,
                    'is_active' => true,
                ]);

                $dims = [
                    ['Petit', 200, 150, 100, 1.0],
                    ['Moyen', 300, 250, 150, 1.25],
                    ['Grand', 400, 350, 250, 1.6],
                ];
                foreach ($dims as $di => [$label, $l, $w, $h, $mult]) {
                    Dimension::create([
                        'product_id' => $product->id,
                        'label' => "{$label} — {$l}×{$w}×{$h} mm",
                        'length_mm' => $l, 'width_mm' => $w, 'height_mm' => $h,
                        'price_multiplier' => $mult,
                        'sort_order' => $di,
                    ]);
                }

                foreach ($p['tiers'] as $minQty => $unit) {
                    PriceTier::create([
                        'product_id' => $product->id,
                        'min_quantity' => $minQty,
                        'unit_price' => $unit,
                    ]);
                }

                $print = OptionGroup::create([
                    'product_id' => $product->id, 'name' => 'Impression', 'is_required' => true, 'sort_order' => 0,
                ]);
                Option::create(['option_group_id' => $print->id, 'label' => '1 couleur', 'price_delta' => 0, 'sort_order' => 0]);
                Option::create(['option_group_id' => $print->id, 'label' => 'Quadri (CMJN)', 'price_delta' => 12, 'sort_order' => 1]);

                $finish = OptionGroup::create([
                    'product_id' => $product->id, 'name' => 'Finition', 'is_required' => true, 'sort_order' => 1,
                ]);
                Option::create(['option_group_id' => $finish->id, 'label' => 'Mate', 'price_delta' => 0, 'sort_order' => 0]);
                Option::create(['option_group_id' => $finish->id, 'label' => 'Brillante (pelliculage)', 'price_delta' => 8, 'sort_order' => 1]);
            }
        }
    }

    private function seedShipping(): void
    {
        $wilayas = [
            '01' => 'Adrar', '02' => 'Chlef', '03' => 'Laghouat', '04' => 'Oum El Bouaghi', '05' => 'Batna',
            '06' => 'Béjaïa', '07' => 'Biskra', '08' => 'Béchar', '09' => 'Blida', '10' => 'Bouira',
            '11' => 'Tamanrasset', '12' => 'Tébessa', '13' => 'Tlemcen', '14' => 'Tiaret', '15' => 'Tizi Ouzou',
            '16' => 'Alger', '17' => 'Djelfa', '18' => 'Jijel', '19' => 'Sétif', '20' => 'Saïda',
            '21' => 'Skikda', '22' => 'Sidi Bel Abbès', '23' => 'Annaba', '24' => 'Guelma', '25' => 'Constantine',
            '26' => 'Médéa', '27' => 'Mostaganem', '28' => 'M\'Sila', '29' => 'Mascara', '30' => 'Ouargla',
            '31' => 'Oran', '32' => 'El Bayadh', '33' => 'Illizi', '34' => 'Bordj Bou Arréridj', '35' => 'Boumerdès',
            '36' => 'El Tarf', '37' => 'Tindouf', '38' => 'Tissemsilt', '39' => 'El Oued', '40' => 'Khenchela',
            '41' => 'Souk Ahras', '42' => 'Tipaza', '43' => 'Mila', '44' => 'Aïn Defla', '45' => 'Naâma',
            '46' => 'Aïn Témouchent', '47' => 'Ghardaïa', '48' => 'Relizane', '49' => 'Timimoun', '50' => 'Bordj Badji Mokhtar',
            '51' => 'Ouled Djellal', '52' => 'Béni Abbès', '53' => 'In Salah', '54' => 'In Guezzam', '55' => 'Touggourt',
            '56' => 'Djanet', '57' => 'El M\'Ghair', '58' => 'El Meniaa',
        ];

        $cheap = ['16', '09', '35', '42', '06', '15', '19', '25', '31', '23'];
        foreach ($wilayas as $code => $name) {
            // Les clés numériques de tableau ("16") sont converties en int par PHP : on normalise.
            $code = str_pad((string) $code, 2, '0', STR_PAD_LEFT);
            $home = in_array($code, $cheap, true) ? 400 : 700;
            ShippingRate::firstOrCreate(
                ['wilaya_code' => $code],
                ['wilaya_name' => $name, 'home_price' => $home, 'stopdesk_price' => $home - 150, 'delay_days' => 3, 'is_active' => true]
            );
        }
    }

    private function seedPages(): void
    {
        $pages = [
            ['Comment ça marche', 'comment-ca-marche', "<p>Choisissez votre produit, une dimension, importez votre design (ou laissez-nous le créer), sélectionnez la quantité et commandez. Paiement à la livraison.</p>"],
            ['FAQ', 'faq', "<p>Questions fréquentes sur les délais, les formats de fichiers et la livraison.</p>"],
            ['Livraison & retours', 'livraison', "<p>Livraison dans les 58 wilayas. Paiement à la réception (COD).</p>"],
            ['CGV', 'cgv', "<p>Conditions générales de vente de BePack.</p>"],
            ['Mentions légales', 'mentions-legales', "<p>Mentions légales.</p>"],
            ['À propos', 'a-propos', "<p>BePack — l'emballage personnalisé pensé pour les e-commerçants.</p>"],
        ];

        foreach ($pages as $i => [$title, $slug, $body]) {
            Page::firstOrCreate(
                ['slug' => $slug],
                ['title' => $title, 'body' => $body, 'show_in_footer' => true, 'sort_order' => $i, 'is_active' => true]
            );
        }
    }
}
