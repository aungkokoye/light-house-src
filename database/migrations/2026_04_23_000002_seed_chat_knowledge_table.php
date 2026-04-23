<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // ── Seed categories ───────────────────────────────────────────────────
        $categories = [
            ['name' => 'Overview',       'description' => 'General company information and mission.',               'sort_order' => 1],
            ['name' => 'Services',        'description' => 'All printing services and products offered.',           'sort_order' => 2],
            ['name' => 'Locations',       'description' => 'Printing site locations and contact numbers.',          'sort_order' => 3],
            ['name' => 'Business Hours',  'description' => 'Opening hours and public holiday information.',         'sort_order' => 4],
            ['name' => 'Contact',         'description' => 'How to reach LightHouse.',                              'sort_order' => 5],
            ['name' => 'Orders',          'description' => 'How to place orders and file requirements.',            'sort_order' => 6],
            ['name' => 'FAQ',             'description' => 'Frequently asked questions.',                           'sort_order' => 7],
        ];

        foreach ($categories as $cat) {
            DB::table('chat_knowledge_categories')->insert([
                ...$cat,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $cat = fn (string $name) => DB::table('chat_knowledge_categories')->where('name', $name)->value('id');

        // ── Seed knowledge entries ────────────────────────────────────────────
        $entries = [
            // Overview
            [
                'chat_knowledge_category_id' => $cat('Overview'),
                'title'      => 'About LightHouse',
                'content'    => "LightHouse is a professional printing house based in Myanmar. We provide high-quality printing services to businesses and individuals across the country. Our management system handles customers, staff, orders, production workflows, and invoices.",
                'sort_order' => 1,
            ],
            [
                'chat_knowledge_category_id' => $cat('Overview'),
                'title'      => 'Our Mission',
                'content'    => "We are committed to delivering exceptional print quality with fast turnaround times. Whether you need business cards, brochures, banners, or large-format prints, LightHouse has the expertise and equipment to bring your vision to life.",
                'sort_order' => 2,
            ],

            // Services
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'All Services Overview',
                'content'    => "LightHouse provides professional printing services across four sites in Myanmar. Our core services are:\n1. Offset Printing\n2. Digital Printing\n3. Large-Format Printing\n4. Packaging Printing\n5. Book & Catalogue Printing\n6. Finishing Services\n7. Design File Support\n8. Delivery across Myanmar",
                'sort_order' => 1,
            ],
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'Offset Printing',
                'content'    => "Offset printing is ideal for high-volume orders with consistent colour quality. Best suited for:\n- Brochures and flyers (500+ copies)\n- Books and catalogues\n- Magazines\n- Letterheads and envelopes\n- Packaging\n\nOffset printing delivers the lowest cost-per-unit at large quantities.",
                'sort_order' => 2,
            ],
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'Digital Printing',
                'content'    => "Digital printing is perfect for short runs and fast turnaround. Ideal for:\n- Business cards\n- Flyers and leaflets\n- Posters (A4–A0)\n- Personalised or variable-data printing\n- Proofs and samples\n\nNo printing plate required — orders can start the same day files are approved.",
                'sort_order' => 3,
            ],
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'Large-Format Printing',
                'content'    => "Large-format printing covers oversized print jobs for display and signage:\n- Banners (vinyl, fabric)\n- Outdoor and indoor posters\n- Roll-up and pull-up banners\n- Exhibition boards\n- Vehicle wraps\n- Window graphics\n\nAvailable at all four LightHouse sites.",
                'sort_order' => 4,
            ],
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'Packaging Printing',
                'content'    => "We print custom packaging for businesses including:\n- Product boxes and cartons\n- Labels and stickers\n- Hang tags\n- Wrapping paper\n- Food-safe packaging (on request)\n\nContact us with your box dimensions and quantity for a quote.",
                'sort_order' => 5,
            ],
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'Books & Catalogues',
                'content'    => "LightHouse handles full book and catalogue production:\n- Saddle-stitched booklets\n- Perfect-bound books\n- Spiral and comb binding\n- Company catalogues and product brochures\n- Annual reports\n- Training manuals\n\nWe manage everything from pre-press to final binding.",
                'sort_order' => 6,
            ],
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'Finishing Services',
                'content'    => "We offer a full range of finishing options to enhance your printed materials:\n- Gloss or matte lamination\n- Spot UV coating\n- Foil stamping (gold, silver)\n- Embossing and debossing\n- Die-cutting (custom shapes)\n- Perforating and scoring\n- Folding (bi-fold, tri-fold, etc.)\n- Cutting and trimming",
                'sort_order' => 7,
            ],
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'Products We Print',
                'content'    => "A complete list of printed products available at LightHouse:\n- Business cards\n- Flyers and leaflets\n- Brochures\n- Posters and banners\n- Stickers and labels\n- Packaging and boxes\n- Books, catalogues, and magazines\n- Letterheads and envelopes\n- Calendars (wall and desk)\n- Notepads and notebooks\n- Certificates and awards\n- ID cards\n- Roll-up banners\n- Custom promotional materials",
                'sort_order' => 8,
            ],
            [
                'chat_knowledge_category_id' => $cat('Services'),
                'title'      => 'Design File Support',
                'content'    => "Our pre-press team can assist with file preparation. We accept:\n- PDF (preferred — with bleed and embedded fonts)\n- Adobe Illustrator (.ai)\n- Adobe Photoshop (.psd)\n- Adobe InDesign (.indd)\n- CorelDRAW (.cdr)\n\nRequirements: 300 DPI minimum, CMYK colour mode, 3mm bleed on all sides, fonts outlined or embedded.\n\nSend your files to info@lighthouse-print.com and we will check them before going to print.",
                'sort_order' => 9,
            ],

            // Locations
            [
                'chat_knowledge_category_id' => $cat('Locations'),
                'title'      => 'Our Printing Site Addresses',
                'content'    => "LightHouse address: 195(A) 33rd street, Upper Block, Kyauktada Township, Yangon, Myanmar",
                'sort_order' => 1,
            ],

            // Business Hours
            [
                'chat_knowledge_category_id' => $cat('Business Hours'),
                'title'      => 'Opening Hours',
                'content'    => "LightHouse is open Monday to Saturday, 9:00 AM – 6:00 PM.\nWe are closed on Sundays and all Myanmar public holidays.\n\nIf you contact us outside business hours, we will get back to you on the next working day.",
                'sort_order' => 1,
            ],
            [
                'chat_knowledge_category_id' => $cat('Business Hours'),
                'title'      => 'Public Holidays',
                'content'    => "All LightHouse sites are closed on Myanmar public holidays. This includes Thingyan (Water Festival), Independence Day, Union Day, Workers Day, and other nationally observed holidays. Orders placed during holidays will be processed on the next working day.",
                'sort_order' => 2,
            ],

            // Contact
            [
                'chat_knowledge_category_id' => $cat('Contact'),
                'title'      => 'How to Reach Us',
                'content'    => "You can reach LightHouse through the following channels:\n- Phone / Mobile: +95 9 782 275275\n- Email: info@lighthouse-print.com\n- Facebook Messenger: Lighthouse Printing - Myanmar\n\nFor site-specific inquiries, please contact the nearest site directly using the phone numbers listed under Locations.",
                'sort_order' => 1,
            ],
            [
                'chat_knowledge_category_id' => $cat('Contact'),
                'title'      => 'Customer Registration',
                'content'    => "New customers can register on our website to access our customer portal. After registration, you will need to verify your email address. Once verified, you can submit inquiries, track orders, and manage your company profile.",
                'sort_order' => 2,
            ],

            // Orders
            [
                'chat_knowledge_category_id' => $cat('Orders'),
                'title'      => 'How to Place an Order',
                'content'    => "To place a printing order with LightHouse:\n1. Register or log in to your account on our website\n2. Contact us via email at info@lighthouse-print.com with your requirements\n3. Our team will provide a quote based on your specifications\n4. Once approved, send your print-ready files\n5. We will confirm production timeline and delivery details",
                'sort_order' => 1,
            ],
            [
                'chat_knowledge_category_id' => $cat('Orders'),
                'title'      => 'File Requirements',
                'content'    => "For best print results, please provide:\n- Files in PDF, AI, PSD, or INDD format\n- Resolution: minimum 300 DPI for print\n- Colour mode: CMYK (not RGB)\n- Include 3mm bleed on all sides\n- Fonts should be embedded or outlined\n\nIf you are unsure about your files, send them to us and our team will check them before production.",
                'sort_order' => 2,
            ],

            // FAQ
            [
                'chat_knowledge_category_id' => $cat('FAQ'),
                'title'      => 'What is the minimum order quantity?',
                'content'    => "Minimum order quantities vary by product. Digital printing allows for very short runs (even single copies for some products), while offset printing is more cost-effective for larger quantities (typically 500+). Contact us with your specific requirements for accurate pricing.",
                'sort_order' => 1,
            ],
            [
                'chat_knowledge_category_id' => $cat('FAQ'),
                'title'      => 'How long does printing take?',
                'content'    => "Production times depend on the product, quantity, and current workload. Typical turnaround times:\n- Digital printing: 1–3 business days\n- Offset printing: 3–7 business days\n- Large-format: 1–2 business days\n\nRush orders may be available — contact your nearest site for availability.",
                'sort_order' => 2,
            ],
            [
                'chat_knowledge_category_id' => $cat('FAQ'),
                'title'      => 'Do you offer delivery?',
                'content'    => "Yes, we offer delivery across Myanmar. Delivery charges depend on location and order size. For large orders, we can arrange freight. Contact us to discuss delivery options for your order.",
                'sort_order' => 3,
            ],
            [
                'chat_knowledge_category_id' => $cat('FAQ'),
                'title'      => 'What payment methods do you accept?',
                'content'    => "We accept bank transfer and cash payments. For regular customers, credit terms may be available upon agreement. Please contact us for payment details when placing your order.",
                'sort_order' => 4,
            ],
            [
                'chat_knowledge_category_id' => $cat('FAQ'),
                'title'      => 'Can I get a sample or proof before full production?',
                'content'    => "Yes. We recommend requesting a digital proof (PDF) before production at no extra charge. For colour-critical jobs, a physical proof can be arranged — ask your site contact for details.",
                'sort_order' => 5,
            ],
        ];

        foreach ($entries as $entry) {
            DB::table('chat_knowledge')->insert([
                ...$entry,
                'active'     => true,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_knowledge')) {
            DB::table('chat_knowledge')->whereNull('created_by')->delete();
        }
        if (Schema::hasTable('chat_knowledge_categories')) {
            DB::table('chat_knowledge_categories')->whereNull('created_by')->delete();
        }
    }
};
