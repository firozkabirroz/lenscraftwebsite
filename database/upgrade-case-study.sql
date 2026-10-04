-- Case-study narrative, client quote, and per-project hover preview clip.
-- Run: mysql -u root lenscraft < database/upgrade-case-study.sql
--
-- Purely additive and safe to re-run: every column is guarded against
-- information_schema first, so this works on MySQL 8 (which has no
-- ADD COLUMN IF NOT EXISTS) as well as MariaDB. No existing data is touched.

SET NAMES utf8mb4;

-- challenge -----------------------------------------------------------------
SET @sql := (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'challenge') > 0,
    'SELECT "projects.challenge already present" AS note',
    'ALTER TABLE projects ADD COLUMN challenge TEXT NULL AFTER description'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- approach ------------------------------------------------------------------
SET @sql := (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'approach') > 0,
    'SELECT "projects.approach already present" AS note',
    'ALTER TABLE projects ADD COLUMN approach TEXT NULL AFTER challenge'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- result --------------------------------------------------------------------
SET @sql := (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'outcome') > 0,
    'SELECT "projects.outcome already present" AS note',
    'ALTER TABLE projects ADD COLUMN outcome TEXT NULL AFTER approach'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- client quote --------------------------------------------------------------
SET @sql := (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'quote_text') > 0,
    'SELECT "projects.quote_text already present" AS note',
    'ALTER TABLE projects ADD COLUMN quote_text TEXT NULL AFTER outcome'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'quote_author') > 0,
    'SELECT "projects.quote_author already present" AS note',
    'ALTER TABLE projects ADD COLUMN quote_author VARCHAR(160) NULL AFTER quote_text'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'quote_role') > 0,
    'SELECT "projects.quote_role already present" AS note',
    'ALTER TABLE projects ADD COLUMN quote_role VARCHAR(160) NULL AFTER quote_author'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- hover preview clip --------------------------------------------------------
-- Short, muted, silent loop shown when a work card is hovered. Holds either an
-- uploads-relative path (uploads/videos/foo.mp4) or an absolute URL. Kept
-- separate from hero_video_url, which is the full film on YouTube/Vimeo and is
-- far too heavy to autoplay behind a thumbnail.
SET @sql := (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'preview_video_path') > 0,
    'SELECT "projects.preview_video_path already present" AS note',
    'ALTER TABLE projects ADD COLUMN preview_video_path VARCHAR(255) NULL AFTER hero_video_url'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
