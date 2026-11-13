-- =====================================================================
-- Aashi Retail - SEO changes: SQL to run on the LIVE database
-- Run in this order, and take a database backup first.
-- Each UPDATE checks the old value, so running it twice does nothing.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. New category fields: editable intro text and FAQ (SEO brief task 16)
-- ---------------------------------------------------------------------
ALTER TABLE `categories`
  ADD COLUMN `intro_content` TEXT NULL AFTER `summary`,
  ADD COLUMN `faq` TEXT NULL AFTER `intro_content`;

-- Only needed if you run the SQL above by hand instead of "php artisan migrate":
-- it stops Laravel from trying to run the same migration later.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_13_000001_add_intro_content_and_faq_to_categories_table', MAX(`batch`) + 1 FROM `migrations`;

-- ---------------------------------------------------------------------
-- 2. Brand slugs (SEO brief task 8). Brand rows and product links stay the same.
--    Before:  id 1 "Aashi"  slug adidas    -> aashi
--             id 2 "A-one"  slug nike      -> a-one
--             id 3 "Kiash"  slug kappa     -> kiash
--    (id 4 "Rainshell" slug rainshell is unchanged)
--    Check ids/titles on live first:  SELECT id, title, slug FROM brands;
-- ---------------------------------------------------------------------
UPDATE `brands` SET `slug` = 'aashi' WHERE `id` = 1 AND `slug` = 'adidas';
UPDATE `brands` SET `slug` = 'a-one' WHERE `id` = 2 AND `slug` = 'nike';
UPDATE `brands` SET `slug` = 'kiash' WHERE `id` = 3 AND `slug` = 'kappa';

-- ---------------------------------------------------------------------
-- 3. Windcheater sub-category slugs (SEO brief task 9)
--    id 28 Windcheater > Men    men-2609032643-794   -> men
--    id 29 Windcheater > Women  women-2609032734-505 -> women
--    Check on live first:  SELECT id, title, slug, parent_id FROM categories WHERE parent_id = 2;
-- ---------------------------------------------------------------------
UPDATE `categories` SET `slug` = 'men'   WHERE `id` = 28 AND `slug` = 'men-2609032643-794';
UPDATE `categories` SET `slug` = 'women' WHERE `id` = 29 AND `slug` = 'women-2609032734-505';

-- ---------------------------------------------------------------------
-- 4. Broken image path of product ECHWJ-301 (SEO brief task 12)
--    The photo is saved as '/storage/photos/1/Products/86835001340.' (no file extension).
--    The file is NOT in the local storage folder, so the extension could not be checked.
--    On the live server look in public/storage/photos/1/Products/ for a file named
--    86835001340.* (jpg, webp or png) and then run the matching line below:
-- ---------------------------------------------------------------------
-- UPDATE `products` SET `photo` = '/storage/photos/1/Products/86835001340.jpg'  WHERE `product_code` = 'ECHWJ-301' AND `photo` = '/storage/photos/1/Products/86835001340.';
-- UPDATE `products` SET `photo` = '/storage/photos/1/Products/86835001340.webp' WHERE `product_code` = 'ECHWJ-301' AND `photo` = '/storage/photos/1/Products/86835001340.';

-- ---------------------------------------------------------------------
-- 5. Editable meta title and description (SEO brief task 5)
--    Empty = the website uses its automatic title/description.
-- ---------------------------------------------------------------------
ALTER TABLE `products`
  ADD COLUMN `meta_title` VARCHAR(191) NULL,
  ADD COLUMN `meta_description` VARCHAR(500) NULL;
ALTER TABLE `categories`
  ADD COLUMN `meta_title` VARCHAR(191) NULL,
  ADD COLUMN `meta_description` VARCHAR(500) NULL;
ALTER TABLE `posts`
  ADD COLUMN `meta_title` VARCHAR(191) NULL,
  ADD COLUMN `meta_description` VARCHAR(500) NULL;

-- ---------------------------------------------------------------------
-- 6. "Notify me" requests for out-of-stock products (SEO brief task 17)
-- ---------------------------------------------------------------------
CREATE TABLE `product_stock_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `email` varchar(191) NOT NULL,
  `notified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_stock_notifications_product_id_email_unique` (`product_id`,`email`),
  KEY `product_stock_notifications_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only needed if you run the SQL of steps 5 and 6 by hand instead of "php artisan migrate":
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_13_000002_add_meta_title_and_description_to_products_categories_posts', MAX(`batch`) + 1 FROM `migrations`;
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_13_000003_create_product_stock_notifications_table', MAX(`batch`) + 1 FROM `migrations`;
