<?php

namespace App\Services;

use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Small helper that builds SEO meta values (title, description, image, canonical)
 * from data that already exists. It never reads or writes the database.
 */
class SeoMeta
{
    const SITE_NAME = 'Aashi Retail';

    // Slugs that were renamed in the database (old slug => new slug).
    // Old URLs get a single 301 redirect to the new slug.
    const OLD_BRAND_SLUGS = [
        'adidas' => 'aashi',   // brand "Aashi"
        'nike' => 'a-one',     // brand "A-one"
        'kappa' => 'kiash',    // brand "Kiash"
    ];
    const OLD_SUB_CATEGORY_SLUGS = [
        'men-2609032643-794' => 'men',      // Windcheater > Men
        'women-2609032734-505' => 'women',  // Windcheater > Women
    ];

    // Converts a comma separated list of brand slugs, replacing old slugs with the new ones.
    public static function brandSlugs($commaSeparated)
    {
        return array_map(function ($slug) {
            return self::OLD_BRAND_SLUGS[$slug] ?? $slug;
        }, explode(',', $commaSeparated));
    }

    // "Main | Middle | Aashi Retail", kept under 60 characters.
    public static function title($main, $middle = null)
    {
        $suffix = ' | ' . self::SITE_NAME;
        $main = trim(strip_tags($main));

        if ($middle && mb_strlen($main . ' | ' . $middle . $suffix) <= 60) {
            return $main . ' | ' . $middle . $suffix;
        }

        // Not enough room (or no middle part): shorten the main text if needed.
        // the "..." is added after the cut, so leave room for it as well
        return Str::limit($main, 60 - mb_strlen($suffix) - 3, '...', true) . $suffix;
    }

    // Plain-text description, cut to 155 characters. Uses $fallback when $html is empty.
    public static function description($html, $fallback)
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if ($text === '') {
            $text = $fallback;
        }

        return Str::limit($text, 152, '...');
    }

    // Absolute image URL for social sharing. Only JPG/PNG are used, otherwise the PNG logo.
    public static function image($photo)
    {
        $photo = trim(explode(',', (string) $photo)[0]);
        $extension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));

        // Share images are shown in a wide 1.91:1 frame. Tall product photos would be cropped,
        // so use a 1200x630 JPG with the whole photo fitted inside (WebP is also converted to JPG here).
        if ($photo !== '' && in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $shareUrl = self::makeShareImage($photo);
            if ($shareUrl) {
                return $shareUrl;
            }
            // Could not make the copy: JPG/PNG originals can still be used as they are.
            if ($extension !== 'webp') {
                return asset('public/' . ltrim($photo, '/'));
            }
        }

        return asset('public/frontend/img/logo.png');
    }

    // Creates (once) a 1200x630 JPG in public/storage/og-images: the photo is fitted inside without
    // cropping, on a background taken from the photo's own corner colour. The original is never changed.
    // Returns null if the copy cannot be made.
    private static function makeShareImage($photo)
    {
        $source = public_path(ltrim($photo, '/'));
        if (!file_exists($source)) {
            return null;
        }

        $fileName = pathinfo($photo, PATHINFO_FILENAME) . '-1200x630.jpg';
        $target = public_path('storage/og-images/' . $fileName);

        if (!file_exists($target)) {
            try {
                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target), 0755, true);
                }
                $manager = new ImageManager(new Driver());
                $image = $manager->read($source)->scaleDown(width: 1200, height: 630);
                $background = $image->pickColor(2, 2)->toHex();

                $manager->create(1200, 630)->fill($background)->place($image, 'center')->toJpeg(85)->save($target);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return asset('public/storage/og-images/' . $fileName);
    }

    // ---------------------------------------------------------------
    // JSON-LD structured data (schema.org). Each method returns an array
    // that the page head prints as <script type="application/ld+json">.
    // ---------------------------------------------------------------

    // Organization details come from the Settings record (phone, email, address).
    public static function organizationSchema($settings)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => self::SITE_NAME,
            'url' => route('home'),
            'logo' => asset('public/frontend/img/logo.png'),
        ];

        if ($settings) {
            if ($settings->email) {
                $schema['email'] = $settings->email;
            }
            if ($settings->phone) {
                $schema['telephone'] = $settings->phone;
            }
            if ($settings->address) {
                $schema['address'] = [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $settings->address,
                    'addressCountry' => 'IN',
                ];
            }
        }

        return $schema;
    }

    public static function websiteSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => self::SITE_NAME,
            'url' => route('home'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('product.search') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    // $items = [['Home', 'https://...'], ['Category', 'https://...'], ...]
    public static function breadcrumbSchema(array $items)
    {
        $list = [];
        foreach ($items as $index => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item[0],
                'item' => $item[1],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    // Product + Offer + availability. Price is read the same way the product page shows it:
    // first price in the size list, minus the discount percentage.
    // AggregateRating is added only when the product has real active reviews.
    public static function productSchema($product, $description)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => $description,
            'sku' => $product->product_code,
            'url' => route('product-detail', $product->slug),
        ];

        $images = [];
        foreach (explode(',', (string) $product->photo) as $photo) {
            $photo = trim($photo);
            if ($photo !== '' && in_array(strtolower(pathinfo($photo, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])) {
                $images[] = asset('public' . $photo);
            }
        }
        if ($images) {
            $schema['image'] = $images;
        }

        if ($product->brand) {
            $schema['brand'] = ['@type' => 'Brand', 'name' => $product->brand->title];
        }

        $sizes = json_decode($product->size, true);
        if (isset($sizes['price'][0])) {
            $price = $sizes['price'][0] - ($sizes['price'][0] * $product->discount) / 100;
            $schema['offers'] = [
                '@type' => 'Offer',
                'url' => route('product-detail', $product->slug),
                'priceCurrency' => 'INR',
                'price' => number_format($price, 2, '.', ''),
                'availability' => $product->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ];
        }

        $reviewCount = $product->getReview->count();
        if ($reviewCount > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round($product->getReview->avg('rate'), 1),
                'reviewCount' => $reviewCount,
            ];
        }

        return $schema;
    }

    // ItemList for a category page: the products shown on the current page.
    public static function itemListSchema($products, $firstPosition = 1)
    {
        $items = [];
        foreach ($products as $index => $product) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $firstPosition + $index,
                'url' => route('product-detail', $product->slug),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $items,
        ];
    }

    // FAQ list from the JSON saved in the admin: [['question' => '...', 'answer' => '...'], ...]
    public static function faqList($json)
    {
        $faqs = json_decode((string) $json, true);

        return is_array($faqs) ? $faqs : [];
    }

    public static function faqSchema(array $faqs)
    {
        $questions = [];
        foreach ($faqs as $faq) {
            $questions[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions,
        ];
    }

    // Canonical URL for list pages: the clean base URL, plus ?page=N only for page 2 and above.
    public static function listCanonical($baseUrl, $page)
    {
        $page = (int) $page;

        return $page > 1 ? $baseUrl . '?page=' . $page : $baseUrl;
    }
}
