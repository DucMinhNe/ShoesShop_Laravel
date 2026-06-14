# ShoesShop

> A full-stack shoe-store e-commerce application built with Laravel 11 and Blade — public storefront with catalog, AJAX cart, multi-gateway checkout and order tracking, social login, a blog, and a complete admin back office for managing products, orders, content and customers.

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Blade](https://img.shields.io/badge/Blade-templates-FF2D20?logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-4-7952B3?logo=bootstrap&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-3.x-0769AD?logo=jquery&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

## Overview

ShoesShop is a server-rendered e-commerce platform for an online footwear retailer. The storefront and admin panel are both built on Laravel's MVC stack with Blade templating, while interactive flows (cart updates, search, filters, reviews) use jQuery/AJAX against named routes. Authentication supports classic email/password as well as Google and Facebook OAuth via Laravel Socialite, and orders can be paid with cash on delivery, MoMo, VNPay or PayPal.

The codebase is split cleanly into three areas — a public **frontend**, an authenticated **user** account area, and a role-protected **backend** admin CMS — making it a readable, end-to-end example of how a Laravel shop is structured.

## Features

### Storefront
- **Product catalog** organised by brand and multi-level categories, with category, sub-category and brand listing pages, product detail pages, grid/list views and product search.
- **Faceted filtering** of the shop by category, brand and price (`shop.filter`).
- **AJAX shopping cart** — add to cart, single add-to-cart, update quantities and remove items without full page reloads.
- **Wishlist** for signed-in customers.
- **Checkout & orders** with discount coupons, configurable shipping options, order placement and order confirmation.
- **Multiple payment methods** — Cash on Delivery, MoMo, VNPay and PayPal (with provider return/callback handling).
- **Order tracking** by order code and downloadable **PDF invoices** (via `barryvdh/laravel-dompdf`).
- **Product reviews & ratings** submitted per product.
- **Blog / news** section with posts, categories, tags, search, filtering and threaded comments.
- **Contact form**, **newsletter subscription** and static About-Us page.

### Accounts & authentication
- Email/password registration and login with email verification and password reset.
- **Social login** with Google and Facebook through Laravel Socialite.
- Customer account dashboard — profile, password change, order history, invoices, reviews and comments.

### Admin back office (`/admin`, `auth` + `admin` middleware)
- Dashboard with sales/income charts (monthly and quarterly).
- CRUD management for **products, brands, categories, banners, coupons and shipping**.
- **Order management** with status updates, order detail views and PDF/printable orders.
- **Content management** — blog posts, post categories and post tags, plus moderation of product reviews and post comments.
- **Customer & user management** and contact **messages** inbox.
- **Notifications** (order/status notifications) and site **settings**.

### Realtime & integrations
- Realtime messaging/notifications scaffolding using **Pusher** + **Laravel Echo** (`MessageSent` event, `StatusNotification`).
- Newsletter integration via `spatie/laravel-newsletter`.

## Tech stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 11 (PHP 8.2+) |
| Templating | Blade |
| Frontend | Bootstrap 4, jQuery, SASS, Laravel Mix (webpack) |
| Database | MySQL |
| Auth | Laravel UI, Laravel Sanctum, Laravel Socialite (Google, Facebook) |
| Payments | MoMo, VNPay, PayPal (`srmklive/paypal`), Cash on Delivery |
| PDF | `barryvdh/laravel-dompdf` |
| Realtime | Pusher (`pusher/pusher-php-server`), Laravel Echo |
| Newsletter | `spatie/laravel-newsletter` |
| Tooling | Composer, Laravel Pint, Laravel Sail, PHPUnit |

## Getting started

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.x
- Node.js & npm

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/DucMinhNe/ShoesShop_Laravel.git
cd ShoesShop_Laravel

# 2. Install PHP dependencies
composer install

# 3. Install front-end dependencies and build assets
npm install
npm run dev          # or `npm run prod` for a production build

# 4. Create your environment file
cp .env.example .env
php artisan key:generate

# 5. Configure the database in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 6. Run migrations and seed reference data (settings, coupons, users)
php artisan migrate --seed

# 7. Serve the application
php artisan serve
```

The app will be available at `http://localhost:8000`. The storefront lives at `/` and the admin panel at `/admin` (requires an account with the admin role).

> A reference SQL dump (`shoes_shop.sql`) is also included if you prefer to import the schema and sample data directly instead of running migrations.

### Optional configuration

Set the relevant `.env` keys to enable the integrated services:

- **Social login** — `GOOGLE_*` and `FACEBOOK_*` client credentials (Socialite).
- **Payments** — MoMo, VNPay and PayPal (`PAYPAL_*`) credentials.
- **Mail** — `MAIL_*` for verification and password-reset emails.
- **Realtime** — `PUSHER_*` for live messaging/notifications.

## Project structure

```
ShoesShop_Laravel/
├── app/
│   ├── Events/                 # MessageSent (broadcast event)
│   ├── Http/
│   │   ├── Controllers/        # Frontend, Cart, Order, Payment, Product,
│   │   │   └── Auth/           # Wishlist, Admin & CMS controllers
│   │   └── Helpers.php         # Global helper functions
│   ├── Models/                 # Product, Category, Brand, Cart, Order,
│   │                           # Coupon, Post, Review, Wishlist, ...
│   └── Notifications/          # StatusNotification
├── config/                     # Framework & package configuration
├── database/
│   ├── migrations/             # Schema (products, orders, carts, posts, ...)
│   └── seeds/                  # Settings, Coupon & User seeders
├── public/                     # Web root & compiled assets
├── resources/
│   └── views/
│       ├── frontend/           # Storefront (pages + layouts)
│       ├── user/               # Customer account area
│       ├── backend/            # Admin CMS
│       └── auth/               # Login, register, password, verify
├── routes/
│   ├── web.php                 # Storefront, account & /admin routes
│   └── api.php
├── shoes_shop.sql              # Reference database dump
└── composer.json
```

## License

Released under the [MIT License](https://opensource.org/licenses/MIT).
