-- Team members + content section for About page
-- Run: mysql -u root lenscraft < database/upgrade-team.sql

SET NAMES utf8mb4;

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

DELETE FROM team_members;

INSERT INTO team_members (name, role, bio, sort_order, is_active) VALUES
('Bayzed Kabir', 'Director / Founder', 'Leads treatments, shoots and the final grade for every LensCraft film.', 1, 1),
('Studio Editor', 'Editor', 'Offline to online edit, sound pass and delivery masters.', 2, 1),
('Field Producer', 'Producer', 'Schedules, permits and crew coordination across Bangladesh.', 3, 1);

INSERT INTO content_sections (page, section_key, title, description, data, updated_by)
SELECT 'about', 'team', 'Team', 'Heading for the team grid on About', '{"heading":"The crew","subheading":"Directors, cinematographers and editors who shoot together.","enabled":"1"}', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM content_sections WHERE page = 'about' AND section_key = 'team'
);
