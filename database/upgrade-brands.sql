-- Adds the "brands we worked with" strip (run once on an existing database).

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

INSERT INTO brands (name, website, sort_order) VALUES
('Aarong', 'https://www.aarong.com', 1),
('Pathao', 'https://pathao.com', 2),
('BTV', '', 3),
('Delta Bank', '', 4),
('NGO Trust BD', '', 5),
('City Fest', '', 6);

INSERT INTO content_sections (page, section_key, title, description, data, updated_by) VALUES
('home', 'brands', 'Brand Strip', 'Logos of brands the studio has worked with',
 '{"heading":"Trusted by brands & broadcasters","enabled":"1"}', 1)
ON DUPLICATE KEY UPDATE description = VALUES(description);
