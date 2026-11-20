<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;

/**
 * Serves /robots.txt and /sitemap.xml.
 * Read-only: nothing here changes any data.
 */
class SeoController extends Controller
{
    // sitemap.xml - only public, active pages.
    public function sitemap()
    {
        $urls = [];

        // Home, blog index and static pages
        $staticRoutes = ['home', 'blog', 'about-us', 'contact', 'privacy-policy',
            'terms-conditions', 'return-refund-policy', 'exchange-policy'];
        foreach ($staticRoutes as $name) {
            $urls[] = ['loc' => route($name), 'lastmod' => null];
        }

        // Categories and sub-categories (one query, then matched in memory)
        $categories = Category::where('status', 'active')
            ->select('id', 'slug', 'parent_id', 'is_parent', 'updated_at')
            ->get();
        $parents = $categories->where('is_parent', 1)->keyBy('id');

        foreach ($parents as $parent) {
            $urls[] = [
                'loc' => route('productlist', $parent->slug),
                'lastmod' => $parent->updated_at,
            ];
        }
        foreach ($categories->where('is_parent', 0) as $sub) {
            $parent = $parents->get($sub->parent_id);
            if ($parent) {
                $urls[] = [
                    'loc' => route('productlist', $sub->slug),
                    'lastmod' => $sub->updated_at,
                ];
            }
        }

        // Active brand pages
        foreach (Brand::where('status', 'active')->select('slug', 'updated_at')->get() as $brand) {
            $urls[] = [
                'loc' => route('product-brand', $brand->slug),
                'lastmod' => $brand->updated_at,
            ];
        }

        // Active products that belong to an active category (soft-deleted ones are skipped automatically)
        $products = Product::where('status', 'active')
            ->whereIn('cat_id', $parents->keys())
            ->select('slug', 'updated_at')
            ->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('product-detail', $product->slug),
                'lastmod' => $product->updated_at,
            ];
        }

        // Active blog posts
        $posts = Post::where('status', 'active')->select('slug', 'updated_at')->get();
        foreach ($posts as $post) {
            $urls[] = [
                'loc' => route('blog.detail', $post->slug),
                'lastmod' => $post->updated_at,
            ];
        }

        // Built as a plain string (not Blade) so view caching / short_open_tag can't break the XML header.
        $xml = '<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "    <url>\n        <loc>" . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
            if ($url['lastmod']) {
                $xml .= '        <lastmod>' . $url['lastmod']->toAtomString() . "</lastmod>\n";
            }
            $xml .= "    </url>\n";
        }
        $xml .= "</urlset>\n";

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
