# LensCraft Production — website + admin panel

Core PHP 8 (no framework) + MySQL implementation of the LensCraft Figma design:
a public film-studio website and a full studio admin panel for video uploads,
bookings and site content.

Figma file: <https://www.figma.com/design/gcnKdRdKE3GIpgZZD75sNd>

## Requirements

- PHP 8.1+ (tested on 8.3) with `pdo_mysql`, `fileinfo` and `gd` enabled
- MySQL / MariaDB 10.4+ (XAMPP default)
- Apache with `mod_rewrite` (XAMPP default)

## Setup with XAMPP

1. Copy the `lenscraft` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** from the XAMPP control panel.
3. Create the database and load the tables and demo data:

```bash
cd C:\xampp\mysql\bin
mysql -u root -e "CREATE DATABASE lenscraft CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root lenscraft < C:\xampp\htdocs\lenscraft\database\schema.sql
mysql -u root lenscraft < C:\xampp\htdocs\lenscraft\database\seed.sql
```

(Or import the two `.sql` files through phpMyAdmin after creating the `lenscraft` database.)

4. Copy `app/config.local.php.example` to `app/config.local.php` and set `base_url`:

```php
'base_url' => '/lenscraft/public',
```

5. Generate the demo placeholder stills (optional, already included):

```bash
php tools\generate-placeholders.php
```

6. Open <http://localhost/lenscraft/public> for the site
   and <http://localhost/lenscraft/public/admin> for the panel.

### Admin logins

| Role   | Email                            | Password       |
|--------|----------------------------------|----------------|
| Owner  | studio@lenscraftproduction.com   | `lenscraft123` |
| Editor | editor@lenscraftproduction.com   | `editor123`    |

Change both passwords from **Settings → Your password** before going live.

## Running without Apache

```bash
php -S localhost:8000 -t public
```

Set `'base_url' => ''` in `app/config.local.php` when using the built-in server.

## What is included

**Public site** — Home (hero reel, selected work, disciplines, studio teaser, CTA),
Work with category filters, project detail pages with gallery, Services, About,
and a Contact page whose form creates both an inquiry message and a booking record.

**Admin panel**

| Screen | What it does |
|---|---|
| Dashboard | Open bookings, unread messages, uploads, activity, traffic snapshot |
| Content | Edits every section of the public pages, with version history and restore |
| Projects | Portfolio CRUD, cover image, gallery picker, homepage/reel placement, SEO |
| Videos | Chunked local upload **and** YouTube/Vimeo embeds, poster, placement toggles |
| Media | Image/PDF library with upload, preview and delete |
| Bookings | List, detail, quote line items, crew, status timeline, manual creation |
| Messages | Inbox with reply thread, archive, and one-click convert to booking |
| Clients | Address book with booking count and booked value |
| Analytics | Visits, video plays, traffic sources, bookings by type, top pages |
| Settings | Studio profile, notifications, admin users, password change |
| Activity | Full audit log of who changed what |

## Large video uploads

Files are sent to `/admin/videos/chunk` in 4 MB pieces and reassembled on the
server, so uploads work even when `upload_max_filesize` is small. To raise the
per-request limits anyway, edit `C:\xampp\php\php.ini`:

```ini
upload_max_filesize = 64M
post_max_size = 68M
max_execution_time = 300
```

Uploaded files live in `public/uploads/` (`videos/`, `images/`, `thumbs/`) and
partial chunks in `storage/temp/`.

## Project layout

```
app/
  Controllers/   Site + Admin controllers
  Models/        Project, Video, MediaAsset, Booking, Message, Client, Content, Analytics
  Support/       Router, Database (PDO), Auth, Settings, Uploader, Activity
  Views/         layouts, site pages, admin screens
  config.php     base configuration (override with config.local.php)
  helpers.php    view + request helpers
database/        schema.sql, seed.sql
public/          front controller, .htaccess, assets, uploads
routes.php       every route in one file
```

## Security notes

- Passwords are bcrypt hashed; sessions regenerate on login.
- Every POST route verifies a CSRF token.
- All output is escaped with `e()`.
- Uploads are validated by MIME type and stored with generated filenames;
  `public/.htaccess` blocks script execution inside `uploads/`.
- Set `'app_debug' => false` and `'app_env' => 'production'` before deploying.
