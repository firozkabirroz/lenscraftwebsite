-- Packages system upgrade
-- Run: mysql -u root lenscraft < database/upgrade-packages.sql

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS packages (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(120) NOT NULL,
  slug          VARCHAR(140) NOT NULL,
  tagline       VARCHAR(255) NULL,
  description   TEXT NULL,
  price_from    DECIMAL(12,2) NOT NULL DEFAULT 0,
  price_label   VARCHAR(60)  NULL DEFAULT 'From',
  currency      VARCHAR(3)   NOT NULL DEFAULT 'BDT',
  features      LONGTEXT NULL,
  service_type  VARCHAR(60)  NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  is_featured   TINYINT(1) NOT NULL DEFAULT 0,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  cta_label     VARCHAR(60)  NULL DEFAULT 'Enquire',
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_package_slug (slug),
  KEY idx_package_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE bookings
  ADD COLUMN IF NOT EXISTS package_id INT UNSIGNED NULL AFTER source,
  ADD KEY IF NOT EXISTS idx_booking_package (package_id);

DELETE FROM packages;

INSERT INTO packages (name, slug, tagline, description, price_from, price_label, currency, features, service_type, sort_order, is_featured, is_active, cta_label) VALUES
('Documentary Starter', 'documentary-starter', 'Research-led short form', 'A compact documentary package for NGOs, brands and broadcasters who need a polished film without a full broadcast crew.', 350000, 'From', 'BDT', '["Pre-production research & treatment","2 shoot days with director + DOP","Field interviews & B-roll","Offline edit + colour grade","Delivery in 16:9 and 9:16"]', 'Documentary', 1, 0, 1, 'Enquire'),
('Commercial Essentials', 'commercial-essentials', 'Brand film in two weeks', 'Concept to screen for product launches, campaigns and TVCs — a fast crew with broadcast-ready delivery.', 450000, 'From', 'BDT', '["Creative treatment & storyboard","1–2 shoot days (studio or location)","Talent & product styling support","Edit, grade and sound mix","Master for TV, web and social"]', 'Commercial', 2, 1, 1, 'Get a quote'),
('Event Coverage', 'event-coverage', 'Same-week recap included', 'Multi-camera coverage for launches, festivals and conferences with a recap cut within 48 hours.', 180000, 'From', 'BDT', '["2–3 camera crew on site","Live switching or multi-cam edit","Same-week 60–90s recap","Full event film within 5 days","Stills package add-on available"]', 'Events', 3, 0, 1, 'Book coverage'),
('Film & Natok', 'film-natok', 'Narrative production unit', 'A narrative unit for OTT, television and independent release — script breakdown through final master.', 850000, 'From', 'BDT', '["Script breakdown & schedule","5+ shoot days with full unit","Casting & location support","Offline + online edit","Sound design and final master"]', 'Film & Natok', 4, 0, 1, 'Start a project');

INSERT INTO content_sections (page, section_key, title, description, data, updated_by)
SELECT 'services', 'packages', 'Service Packages', 'Pricing tiers shown on the services page', '{"heading":"Production packages","subheading":"Clear starting points — every project is scoped to your brief after a discovery call.","enabled":"1"}', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM content_sections WHERE page = 'services' AND section_key = 'packages'
);
