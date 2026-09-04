-- LensCraft Production — database schema
-- Import with:  mysql -u root lenscraft < database/schema.sql
-- or through phpMyAdmin (create the `lenscraft` database first).

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(120) NOT NULL,
  email         VARCHAR(160) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('owner','editor') NOT NULL DEFAULT 'editor',
  phone         VARCHAR(40)  NULL,
  last_login_at DATETIME     NULL,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  `k` VARCHAR(80)  NOT NULL,
  `v` TEXT         NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS content_sections (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  page        VARCHAR(40)  NOT NULL,
  section_key VARCHAR(60)  NOT NULL,
  title       VARCHAR(120) NOT NULL,
  description VARCHAR(255) NULL,
  data        LONGTEXT     NULL,
  updated_by  INT UNSIGNED NULL,
  updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_section (page, section_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS content_revisions (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  section_id INT UNSIGNED NOT NULL,
  data       LONGTEXT     NULL,
  note       VARCHAR(160) NULL,
  user_id    INT UNSIGNED NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_revision_section (section_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS media (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title      VARCHAR(160) NULL,
  alt        VARCHAR(200) NULL,
  filename   VARCHAR(200) NOT NULL,
  path       VARCHAR(255) NOT NULL,
  mime       VARCHAR(100) NOT NULL,
  type       ENUM('image','video','doc') NOT NULL DEFAULT 'image',
  width      INT UNSIGNED NOT NULL DEFAULT 0,
  height     INT UNSIGNED NOT NULL DEFAULT 0,
  size_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_media_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS projects (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title            VARCHAR(180) NOT NULL,
  slug             VARCHAR(200) NOT NULL,
  category         VARCHAR(60)  NOT NULL DEFAULT 'Documentary',
  client_name      VARCHAR(160) NULL,
  year             SMALLINT UNSIGNED NULL,
  summary          VARCHAR(255) NULL,
  description      TEXT         NULL,
  hero_video_url   VARCHAR(255) NULL,
  video_id         INT UNSIGNED NULL,
  cover_media_id   INT UNSIGNED NULL,
  status           ENUM('draft','scheduled','published') NOT NULL DEFAULT 'draft',
  show_on_homepage TINYINT(1)   NOT NULL DEFAULT 0,
  featured_in_reel TINYINT(1)   NOT NULL DEFAULT 0,
  sort_order       INT          NOT NULL DEFAULT 0,
  meta_title       VARCHAR(180) NULL,
  meta_description VARCHAR(255) NULL,
  views            INT UNSIGNED NOT NULL DEFAULT 0,
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_project_slug (slug),
  KEY idx_project_status (status),
  KEY idx_project_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS project_media (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  project_id INT UNSIGNED NOT NULL,
  media_id   INT UNSIGNED NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_pm_project (project_id),
  KEY idx_pm_media (media_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS videos (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title              VARCHAR(180) NOT NULL,
  category           VARCHAR(60)  NOT NULL DEFAULT 'Hero Reel',
  source             ENUM('local','embed') NOT NULL DEFAULT 'local',
  file_path          VARCHAR(255) NULL,
  embed_url          VARCHAR(255) NULL,
  provider           VARCHAR(30)  NULL,
  poster_media_id    INT UNSIGNED NULL,
  project_id         INT UNSIGNED NULL,
  duration_seconds   INT UNSIGNED NULL,
  size_bytes         BIGINT UNSIGNED NOT NULL DEFAULT 0,
  status             ENUM('processing','ready','failed') NOT NULL DEFAULT 'ready',
  is_published       TINYINT(1) NOT NULL DEFAULT 0,
  place_home_hero    TINYINT(1) NOT NULL DEFAULT 0,
  place_work_grid    TINYINT(1) NOT NULL DEFAULT 0,
  place_services     TINYINT(1) NOT NULL DEFAULT 0,
  views              INT UNSIGNED NOT NULL DEFAULT 0,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_video_status (status),
  KEY idx_video_project (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS clients (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name         VARCHAR(160) NOT NULL,
  organisation VARCHAR(160) NULL,
  email        VARCHAR(160) NULL,
  phone        VARCHAR(40)  NULL,
  notes        TEXT         NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_client_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bookings (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code           VARCHAR(20)  NOT NULL,
  client_id      INT UNSIGNED NULL,
  client_name    VARCHAR(160) NOT NULL,
  organisation   VARCHAR(160) NULL,
  email          VARCHAR(160) NULL,
  phone          VARCHAR(40)  NULL,
  project_type   VARCHAR(60)  NOT NULL DEFAULT 'Documentary',
  shoot_date     DATE         NULL,
  shoot_days     TINYINT UNSIGNED NOT NULL DEFAULT 1,
  location       VARCHAR(180) NULL,
  brief          TEXT         NULL,
  budget         DECIMAL(12,2) NOT NULL DEFAULT 0,
  quote_total    DECIMAL(12,2) NOT NULL DEFAULT 0,
  status         ENUM('inquiry','pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'inquiry',
  internal_notes TEXT         NULL,
  source         VARCHAR(40)  NOT NULL DEFAULT 'website',
  package_id     INT UNSIGNED NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_booking_code (code),
  KEY idx_booking_status (status),
  KEY idx_booking_date (shoot_date),
  KEY idx_booking_package (package_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS booking_items (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id INT UNSIGNED NOT NULL,
  label      VARCHAR(120) NOT NULL,
  amount     DECIMAL(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_bi_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS booking_crew (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id INT UNSIGNED NOT NULL,
  person     VARCHAR(120) NOT NULL,
  role       VARCHAR(80)  NOT NULL,
  days       VARCHAR(60)  NULL,
  PRIMARY KEY (id),
  KEY idx_bc_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS booking_events (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id  INT UNSIGNED NOT NULL,
  label       VARCHAR(120) NOT NULL,
  note        VARCHAR(200) NULL,
  is_done     TINYINT(1) NOT NULL DEFAULT 1,
  happened_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_be_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS messages (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(160) NOT NULL,
  email      VARCHAR(160) NULL,
  phone      VARCHAR(40)  NULL,
  subject    VARCHAR(200) NOT NULL,
  body       TEXT         NOT NULL,
  status     ENUM('unread','read','archived') NOT NULL DEFAULT 'unread',
  booking_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_message_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS message_replies (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  message_id INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NULL,
  body       TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_reply_message (message_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS brands (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(120) NOT NULL,
  website       VARCHAR(200) NULL,
  logo_media_id INT UNSIGNED NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_brand_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE IF NOT EXISTS team_members (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name           VARCHAR(120) NOT NULL,
  role           VARCHAR(120) NULL,
  bio            TEXT NULL,
  photo_media_id INT UNSIGNED NULL,
  sort_order     INT NOT NULL DEFAULT 0,
  is_active      TINYINT(1) NOT NULL DEFAULT 1,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_team_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS activity_log (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NULL,
  action      VARCHAR(120) NOT NULL,
  target_type VARCHAR(40)  NULL,
  target_id   INT UNSIGNED NULL,
  meta        VARCHAR(500) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_activity_type (target_type),
  KEY idx_activity_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS page_views (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  path       VARCHAR(200) NOT NULL,
  referrer   VARCHAR(255) NULL,
  source     VARCHAR(40)  NULL,
  ip_hash    CHAR(40)     NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pv_created (created_at),
  KEY idx_pv_path (path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS video_views (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  video_id   INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_vv_video (video_id),
  KEY idx_vv_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
