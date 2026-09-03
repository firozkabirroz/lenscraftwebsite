-- LensCraft Production — demo data
-- Import AFTER schema.sql:  mysql -u root lenscraft < database/seed.sql
--
-- Admin logins
--   studio@lenscraftproduction.com / lenscraft123   (owner)
--   editor@lenscraftproduction.com / editor123      (editor)

SET NAMES utf8mb4;

DELETE FROM video_views;
DELETE FROM page_views;
DELETE FROM activity_log;
DELETE FROM message_replies;
DELETE FROM messages;
DELETE FROM booking_events;
DELETE FROM booking_crew;
DELETE FROM booking_items;
DELETE FROM bookings;
DELETE FROM clients;
DELETE FROM brands;
DELETE FROM packages;
DELETE FROM project_media;
DELETE FROM videos;
DELETE FROM projects;
DELETE FROM media;
DELETE FROM content_revisions;
DELETE FROM content_sections;
DELETE FROM settings;
DELETE FROM users;

INSERT INTO users (id, name, email, password_hash, role, phone, created_at) VALUES
(1, 'Bayzed Kabir', 'studio@lenscraftproduction.com', '$2y$10$DCLpXwetmQivxW1e5eDHWORZoRLGH7S7lOM7hNVDOlfqcv5nR2XSC', 'owner', '+880 1712 000000', NOW()),
(2, 'Studio Editor', 'editor@lenscraftproduction.com', '$2y$10$0X.u6bLsW.Iwj8Dr.B7HceF3krJToAABp/yYWuZdoB7eaTUHTz1Ei', 'editor', '+880 1712 111111', NOW());

INSERT INTO settings (`k`, `v`) VALUES
('studio_name', 'LensCraft Production'),
('tagline', 'Crafting Stories with Vision & Precision'),
('email', 'hello@lenscraftproduction.com'),
('phone', '+880 1712 345678'),
('whatsapp', '+8801712345678'),
('address', 'Dhanmondi, Dhaka 1209, Bangladesh'),
('hours', 'Sun–Thu · 10:00–19:00'),
('instagram', 'https://instagram.com/lenscraftproduction'),
('youtube', 'https://youtube.com/@lenscraftproduction'),
('facebook', 'https://facebook.com/lenscraftproduction'),
('meta_description', 'LensCraft Production is a Dhaka based film studio making documentaries, commercials, films and natoks.'),
('footer_note', 'Documentary · Commercial · Film & Natok · Corporate AV'),
('notify_booking', '1'),
('notify_message', '1'),
('notify_upload', '1');

INSERT INTO content_sections (page, section_key, title, description, data, updated_by) VALUES
('home', 'hero', 'Home Hero', 'Headline, tagline and calls to action', '{"brand_line":"LensCraft Production","tagline":"Crafting Stories with Vision & Precision","intro":"A Dhaka based production house making documentaries, commercials, films and natoks for brands and broadcasters.","primary_cta":"View Work","primary_link":"/work","secondary_cta":"Watch Reel","background_mode":"video","show_scroll_hint":"1","dim_overlay":"1"}', 1),
('home', 'selected_work', 'Selected Work', 'Projects featured on the homepage', '{"heading":"Selected Work","subheading":"A short cut of recent films, campaigns and documentaries.","limit":"6"}', 1),
('home', 'disciplines', 'Disciplines', 'Service categories on the homepage', '{"heading":"Disciplines","items":[{"title":"Documentary","desc":"Research led long form storytelling with field crews across Bangladesh."},{"title":"Commercial","desc":"Brand films and TVCs from concept board to final grade."},{"title":"Film & Natok","desc":"Narrative production for screen, OTT and television."},{"title":"Corporate AV","desc":"Annual reports, explainers and internal communication films."}]}', 1),
('home', 'about_teaser', 'About Teaser', 'Short studio introduction', '{"heading":"A studio built around craft","body":"We are directors, cinematographers and editors who care about the frame and the story behind it. Every project starts with research and ends with a film we are proud to sign.","cta":"About the studio"}', 1),
('home', 'cta_band', 'CTA Band', 'Closing call to action', '{"heading":"Have a story worth filming?","body":"Tell us about your project and we will come back with an approach and a quote.","cta":"Start a Project"}', 1),
('work', 'hero', 'Work Hero', 'Heading for the work page', '{"heading":"Work","subheading":"Documentaries, commercials, films and natoks produced by the LensCraft crew."}', 1),
('services', 'hero', 'Services Hero', 'Heading for the services page', '{"heading":"Services","subheading":"Full service production — from first treatment to final delivery."}', 1),
('services', 'list', 'Services List', 'Service blocks with details', '{"items":[{"title":"Documentary Production","desc":"Field research, interviews, archival integration and festival ready delivery.","bullets":["Research and treatment","Multi day field shoots","Subtitling and grading"]},{"title":"Commercial & TVC","desc":"Concept to screen production for brands, agencies and startups.","bullets":["Storyboards and casting","Studio and location shoots","Broadcast delivery"]},{"title":"Film & Natok","desc":"Narrative production for OTT, television and independent release.","bullets":["Script breakdown","Crew and equipment","Post production"]},{"title":"Corporate AV","desc":"Company profiles, annual reports and internal films.","bullets":["Interview setups","Motion graphics","Multi language versions"]},{"title":"Aerial & FPV","desc":"Licensed drone coverage for landscapes, events and action sequences.","bullets":["Permits and safety","4K and 6K capture","FPV chase shots"]},{"title":"Event Coverage","desc":"Festivals, launches and conferences with same week recap edits.","bullets":["Multi camera setup","Live switching","48 hour recap cut"]},{"title":"Post Production","desc":"Edit, colour, sound design and mastering in our Dhaka suite.","bullets":["Offline and online edit","Colour grading","Sound mix"]},{"title":"Photography","desc":"Stills packages that ship alongside every film project.","bullets":["Behind the scenes","Product and portrait","Retouching"]}]}', 1),
('services', 'packages', 'Service Packages', 'Pricing tiers on the services page', '{"heading":"Production packages","subheading":"Clear starting points — every project is scoped to your brief after a discovery call.","enabled":"1"}', 1),
('about', 'hero', 'About Hero', 'Heading for the about page', '{"heading":"About LensCraft","subheading":"A production house with a crew that has been shooting together for a decade."}', 1),
('about', 'vision', 'Vision & Mission', 'Studio vision and mission', '{"vision":"To make Bangladeshi stories that travel — films that hold up on a festival screen and on a phone.","mission":"Give every client a crew that plans carefully, shoots calmly and delivers on the promised date."}', 1),
('about', 'approach', 'Approach', 'How the studio works', '{"steps":[{"title":"Research","desc":"We read, scout and talk to people before a single frame is shot."},{"title":"Plan","desc":"Treatment, schedule, crew and budget agreed in writing."},{"title":"Shoot","desc":"Small, fast crews with broadcast grade equipment."},{"title":"Deliver","desc":"Edit, grade, mix and master in every format you need."}]}', 1),
('contact', 'info', 'Contact Info', 'Phone, email and studio address', '{"heading":"Start a project","subheading":"Tell us about the film you need. We usually reply within one working day.","phone":"+880 1712 345678","email":"hello@lenscraftproduction.com","address":"Dhanmondi, Dhaka 1209, Bangladesh","hours":"Sun–Thu · 10:00–19:00"}', 1),
('home', 'brands', 'Brand Strip', 'Logos of brands the studio has worked with', '{"heading":"Trusted by brands & broadcasters","enabled":"1"}', 1);

INSERT INTO brands (name, website, sort_order) VALUES
('Aarong', 'https://www.aarong.com', 1),
('Pathao', 'https://pathao.com', 2),
('BTV', '', 3),
('Delta Bank', '', 4),
('NGO Trust BD', '', 5),
('City Fest', '', 6);

INSERT INTO packages (name, slug, tagline, description, price_from, price_label, currency, features, service_type, sort_order, is_featured, is_active, cta_label) VALUES
('Documentary Starter', 'documentary-starter', 'Research-led short form', 'A compact documentary package for NGOs, brands and broadcasters who need a polished film without a full broadcast crew.', 350000, 'From', 'BDT', '["Pre-production research & treatment","2 shoot days with director + DOP","Field interviews & B-roll","Offline edit + colour grade","Delivery in 16:9 and 9:16"]', 'Documentary', 1, 0, 1, 'Enquire'),
('Commercial Essentials', 'commercial-essentials', 'Brand film in two weeks', 'Concept to screen for product launches, campaigns and TVCs — a fast crew with broadcast-ready delivery.', 450000, 'From', 'BDT', '["Creative treatment & storyboard","1–2 shoot days (studio or location)","Talent & product styling support","Edit, grade and sound mix","Master for TV, web and social"]', 'Commercial', 2, 1, 1, 'Get a quote'),
('Event Coverage', 'event-coverage', 'Same-week recap included', 'Multi-camera coverage for launches, festivals and conferences with a recap cut within 48 hours.', 180000, 'From', 'BDT', '["2–3 camera crew on site","Live switching or multi-cam edit","Same-week 60–90s recap","Full event film within 5 days","Stills package add-on available"]', 'Events', 3, 0, 1, 'Book coverage'),
('Film & Natok', 'film-natok', 'Narrative production unit', 'A narrative unit for OTT, television and independent release — script breakdown through final master.', 850000, 'From', 'BDT', '["Script breakdown & schedule","5+ shoot days with full unit","Casting & location support","Offline + online edit","Sound design and final master"]', 'Film & Natok', 4, 0, 1, 'Start a project');

INSERT INTO media (id, title, alt, filename, path, mime, type, width, height, size_bytes) VALUES
(1, 'River of Voices still', 'Boat on a river at sunrise', 'river-still-01.jpg', 'images/river-still-01.jpg', 'image/jpeg', 'image', 1920, 1080, 486000),
(2, 'Aarong winter frame', 'Model in winter collection', 'aarong-frame.jpg', 'images/aarong-frame.jpg', 'image/jpeg', 'image', 1920, 1080, 512000),
(3, 'Shomoy poster', 'Natok poster artwork', 'shomoy-poster.jpg', 'images/shomoy-poster.jpg', 'image/jpeg', 'image', 1080, 1350, 654000),
(4, 'Studio behind the scenes', 'Crew on set', 'studio-bts-04.jpg', 'images/studio-bts-04.jpg', 'image/jpeg', 'image', 1920, 1080, 398000),
(5, 'Night market', 'Street market at night', 'night-market-02.jpg', 'images/night-market-02.jpg', 'image/jpeg', 'image', 1920, 1080, 442000),
(6, 'Boardroom still', 'Corporate interview setup', 'boardroom-still.jpg', 'images/boardroom-still.jpg', 'image/jpeg', 'image', 1920, 1080, 371000);

INSERT INTO projects (id, title, slug, category, client_name, year, summary, description, hero_video_url, cover_media_id, status, show_on_homepage, featured_in_reel, sort_order, meta_title, meta_description, views) VALUES
(1, 'River of Voices', 'river-of-voices', 'Documentary', 'NGO Trust BD', 2025, 'A long form documentary following river communities across three districts.', 'A long form documentary following river communities across three districts — research, trust and cinematic craft. Shot over eleven days with a four person crew, the film pairs interviews with aerial coverage of the delta.', 'https://vimeo.com/22439234', 1, 'published', 1, 1, 1, 'River of Voices — LensCraft Production', 'Documentary on river communities produced by LensCraft Production.', 1420),
(2, 'Aarong Winter', 'aarong-winter', 'Commercial', 'Aarong', 2025, 'Winter campaign film for a national retail brand.', 'A three film winter campaign shot across Dhaka and Srimangal, delivered in broadcast and vertical formats within two weeks of the final shoot day.', 'https://vimeo.com/22439234', 2, 'published', 1, 1, 2, 'Aarong Winter Campaign — LensCraft Production', 'Winter campaign film produced for Aarong.', 960),
(3, 'Shomoy', 'shomoy', 'Film & Natok', 'Independent', 2024, 'A six episode natok about a family running a printing press.', 'A six episode natok about a family running a printing press in old Dhaka. LensCraft handled production, cinematography and post.', '', 3, 'published', 1, 0, 3, 'Shomoy — LensCraft Production', 'Six episode natok produced by LensCraft Production.', 780),
(4, 'Boardroom Trust', 'boardroom-trust', 'Corporate AV', 'Delta Bank', 2025, 'Annual report film with interviews across four offices.', 'An annual report film built around leadership interviews, branch footage and motion graphics, delivered in Bangla and English versions.', '', 6, 'draft', 0, 0, 4, NULL, NULL, 120),
(5, 'Night Market', 'night-market', 'Documentary', 'Self initiated', 2024, 'A short observational film shot entirely after midnight.', 'A short observational documentary shot entirely between midnight and dawn in Karwan Bazar, screened at two regional festivals.', '', 5, 'published', 1, 0, 5, NULL, NULL, 640),
(6, 'Launch Pulse', 'launch-pulse', 'Commercial', 'Pathao', 2025, 'Product launch film cut for social and broadcast.', 'A product launch film with a studio build, motion graphics and a fourteen day turnaround.', '', 2, 'scheduled', 1, 0, 6, NULL, NULL, 310),
(7, 'City Lights Recap', 'city-lights-recap', 'Events', 'City Fest', 2025, 'Two day festival recap delivered in 48 hours.', 'Multi camera coverage of a two day city festival with a recap edit delivered within 48 hours of the closing act.', '', 4, 'published', 0, 0, 7, NULL, NULL, 280);

INSERT INTO project_media (project_id, media_id, sort_order) VALUES
(1, 1, 1), (1, 4, 2), (1, 5, 3),
(2, 2, 1),
(3, 3, 1),
(5, 5, 1),
(7, 4, 1);

INSERT INTO videos (id, title, category, source, file_path, embed_url, provider, poster_media_id, project_id, duration_seconds, size_bytes, status, is_published, place_home_hero, place_work_grid, place_services, views) VALUES
(1, 'Showreel 2026', 'Hero Reel', 'embed', NULL, 'https://vimeo.com/22439234', 'vimeo', 4, NULL, 92, 0, 'ready', 1, 1, 0, 1, 14200),
(2, 'River of Voices — Trailer', 'Documentary', 'embed', NULL, 'https://vimeo.com/22439234', 'vimeo', 1, 1, 128, 0, 'ready', 1, 0, 1, 0, 9600),
(3, 'Aarong Winter — Film 01', 'Commercial', 'embed', NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 'youtube', 2, 2, 62, 0, 'ready', 1, 0, 1, 0, 6400),
(4, 'Shomoy — Episode 01', 'Film & Natok', 'embed', NULL, 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 'youtube', 3, 3, 1420, 0, 'ready', 1, 0, 1, 0, 4100),
(5, 'City Lights Recap', 'Events', 'embed', NULL, 'https://vimeo.com/22439234', 'vimeo', 4, 7, 180, 0, 'ready', 1, 0, 1, 0, 2800);

INSERT INTO clients (id, name, organisation, email, phone, notes) VALUES
(1, 'Nadia Rahman', 'Independent', 'nadia@example.com', '+880 1712 345678', 'Documentary producer, festival submissions.'),
(2, 'Aarong Marketing', 'Aarong', 'marketing@aarong.example', '+880 1811 220033', 'Retail brand, two campaigns per year.'),
(3, 'BTV Drama Unit', 'BTV', 'drama@btv.example', '+880 1911 445566', 'Natok series commissioning.'),
(4, 'NGO Trust BD', 'NGO Trust', 'comms@ngotrust.example', '+880 1611 778899', 'Annual report and field documentaries.');

INSERT INTO bookings (id, code, client_id, client_name, organisation, email, phone, project_type, shoot_date, shoot_days, location, brief, budget, quote_total, status, internal_notes, source, created_at) VALUES
(1, 'BK-1042', 1, 'Nadia Rahman', 'Independent', 'nadia@example.com', '+880 1712 345678', 'Documentary', '2026-08-20', 3, 'Sylhet · 3 locations', 'A 20 minute documentary on tea garden workers — interviews, drone coverage and archival integration. Festival submission in November.', 400000, 400000, 'pending', 'Needs drone permit for tea estate — apply by Aug 14.', 'website', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 'BK-1041', 2, 'Aarong Marketing', 'Aarong', 'marketing@aarong.example', '+880 1811 220033', 'Commercial', '2026-08-14', 2, 'Dhaka studio', 'Winter campaign — three films plus vertical cutdowns.', 650000, 650000, 'confirmed', 'Studio booked, art team confirmed.', 'website', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(3, 'BK-1040', 3, 'BTV Drama Unit', 'BTV', 'drama@btv.example', '+880 1911 445566', 'Film & Natok', '2026-09-02', 8, 'Old Dhaka', 'Eight day natok shoot, two units.', 1200000, 0, 'inquiry', NULL, 'website', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(4, 'BK-1039', 4, 'NGO Trust BD', 'NGO Trust', 'comms@ngotrust.example', '+880 1611 778899', 'Corporate AV', '2026-07-28', 2, 'Dhaka + Khulna', 'Annual report film with field interviews.', 320000, 320000, 'completed', 'Delivered on time, invoice cleared.', 'admin', DATE_SUB(NOW(), INTERVAL 25 DAY));

INSERT INTO booking_items (booking_id, label, amount) VALUES
(1, 'Production', 240000),
(1, 'Post-production', 85000),
(1, 'Aerial / FPV', 45000),
(1, 'Travel & logistics', 30000),
(2, 'Production', 420000),
(2, 'Post-production', 160000),
(2, 'Studio rental', 70000);

INSERT INTO booking_crew (booking_id, person, role, days) VALUES
(1, 'Bayzed Kabir', 'Director', 'Aug 20–22'),
(1, 'Rakib Hasan', 'DOP', 'Aug 20–22'),
(1, 'Tanvir Ahmed', 'Sound', 'Aug 20–21'),
(1, 'Sabbir Khan', 'Drone / FPV', 'Aug 21'),
(2, 'Bayzed Kabir', 'Director', 'Aug 14–15'),
(2, 'Rakib Hasan', 'DOP', 'Aug 14–15');

INSERT INTO booking_events (booking_id, label, note, is_done, happened_at) VALUES
(1, 'Inquiry received', 'Website contact form', 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 'Call scheduled', 'Intro call with client', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'Quote sent', 'BDT 400,000', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 'Client approval', 'Awaiting', 0, NULL),
(1, 'Shoot day', 'Aug 20', 0, NULL),
(2, 'Inquiry received', 'Agency email', 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(2, 'Quote sent', 'BDT 650,000', 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 'Confirmed', 'Advance received', 1, DATE_SUB(NOW(), INTERVAL 4 DAY));

INSERT INTO messages (id, name, email, phone, subject, body, status, booking_id, created_at) VALUES
(1, 'Nadia Rahman', 'nadia@example.com', '+880 1712 345678', 'Documentary on tea workers', 'Hello LensCraft team,\n\nI am producing a 20 minute documentary about tea garden workers in Sylhet and would like to work with your crew. We need interviews, drone coverage and support with archival footage integration. The festival submission deadline is in November.\n\nCould you share availability for late August and an estimated budget?\n\nThank you,\nNadia', 'unread', 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 'Aarong Marketing', 'marketing@aarong.example', '+880 1811 220033', 'Winter campaign film — timeline?', 'We would like to lock the winter campaign shoot dates. Can you confirm the studio availability for the second week of August?', 'unread', 2, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(3, 'BTV Drama Unit', 'drama@btv.example', '+880 1911 445566', 'Natok series production quote', 'Please share a quote for an eight day natok shoot with two units in Old Dhaka.', 'unread', 3, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(4, 'Rafi Chowdhury', 'rafi@example.com', '+880 1511 998877', 'Wedding film availability', 'Are you available for a wedding film in December? Two days of coverage.', 'read', NULL, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(5, 'City Fest Org', 'hello@cityfest.example', '+880 1311 224466', 'Festival recap edit', 'We need a 48 hour recap edit for our two day festival in October.', 'archived', NULL, DATE_SUB(NOW(), INTERVAL 20 DAY));

INSERT INTO activity_log (user_id, action, target_type, target_id, meta, created_at) VALUES
(1, 'published section', 'content', 1, 'Home Hero', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'uploaded', 'media', 1, 'river-still-01.jpg', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(2, 'confirmed booking', 'booking', 2, 'BK-1041 · Aarong', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 'replied to', 'message', 1, 'Nadia Rahman', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'edited project', 'project', 1, 'River of Voices', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'signed in', 'session', 1, '127.0.0.1', DATE_SUB(NOW(), INTERVAL 2 DAY));
