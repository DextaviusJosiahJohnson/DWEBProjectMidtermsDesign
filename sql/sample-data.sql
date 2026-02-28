-- adjustments: Remove the creation of table, all of the creation will be on smart_browser_state.sql file
use smart_browser_state;

-- Insert sample data
INSERT INTO browser_states (user_id, device, browser, tab_data, created_at) VALUES
(1,'Laptop','Chrome','["https://google.com","https://github.com","https://youtube.com"]','2026-02-10 09:15:00'),
(1,'Desktop','Firefox','["https://mozilla.org","https://stackoverflow.com"]','2026-02-09 14:25:00'),
(1,'Work PC','Edge','["https://microsoft.com","https://office.com","https://linkedin.com"]','2026-02-08 11:40:00'),
(1,'Mobile','Safari','["https://apple.com","https://icloud.com"]','2026-02-11 18:10:00'),
(1,'Laptop','Chrome','["https://github.com","https://chat.openai.com","https://gmail.com","https://drive.google.com"]','2026-02-12 08:05:00'),
(1,'Desktop','Edge','["https://bing.com","https://amazon.com"]','2026-02-12 16:30:00'),
(1,'Work PC','Chrome','["https://slack.com","https://notion.so","https://figma.com"]','2026-02-13 09:55:00'),
(1,'Mobile','Firefox','["https://reddit.com","https://twitter.com"]','2026-02-13 21:15:00'),
(1,'Laptop','Safari','["https://netflix.com","https://spotify.com","https://medium.com"]','2026-02-14 12:10:00'),
(1,'Desktop','Chrome','["https://stackoverflow.com","https://w3schools.com","https://geeksforgeeks.org"]','2026-02-14 15:20:00'),
(2,'Work PC','Firefox','["https://trello.com","https://asana.com"]','2026-02-15 10:05:00'),
(2,'Mobile','Chrome','["https://instagram.com"]','2026-02-15 22:00:00'),
(2,'Laptop','Edge','["https://coursera.org","https://udemy.com"]','2026-02-16 08:30:00'),
(2,'Desktop','Safari','["https://bbc.com","https://cnn.com"]','2026-02-16 14:45:00'),
(2,'Work PC','Chrome','["https://jira.com","https://confluence.com","https://bitbucket.org"]','2026-02-16 18:55:00'),
(2,'Mobile','Edge','["https://whatsapp.com"]','2026-02-17 07:25:00'),
(2,'Laptop','Firefox','["https://paypal.com","https://stripe.com"]','2026-02-17 09:10:00'),
(2,'Desktop','Chrome','["https://github.com","https://gitlab.com","https://docker.com"]','2026-02-17 20:30:00');


-- Sample data BOOKMARKS + SEARCH HISTORY --
INSERT INTO bookmarks (user_id, title, url, device, browser, created_at) VALUES
(1, 'OpenAI ChatGPT', 'https://chat.openai.com', 'Laptop', 'Chrome', '2025-02-01 10:15:00'),

(1, 'GitHub Repository', 'https://github.com', 'Laptop', 'Firefox', '2025-02-03 14:42:00'),

(1, 'Stack Overflow', 'https://stackoverflow.com', 'Desktop', 'Edge', '2025-02-05 09:30:00'),

(1, 'MDN Web Docs', 'https://developer.mozilla.org', 'Laptop', 'Chrome', '2025-02-08 18:20:00'),

(1, 'PHP Manual', 'https://www.php.net/manual/en/', 'Desktop', 'Chrome', '2025-02-10 11:05:00'),

(1, 'CSS Tricks', 'https://css-tricks.com', 'Tablet', 'Safari', '2025-02-12 16:55:00'),

(1, 'JavaScript Info', 'https://javascript.info', 'Laptop', 'Chrome', '2025-02-14 08:40:00'),

(1, 'MySQL Documentation', 'https://dev.mysql.com/doc/', 'Desktop', 'Firefox', '2025-02-15 13:10:00');

INSERT INTO search_history 
(user_id, search_query, search_engine, browser, device, created_at)
VALUES
(1, 'how to subnet a network', 'Google', 'Chrome', 'Laptop', '2026-01-18 10:15:00'),

(1, 'php ajax fetch example', 'Google', 'Chrome', 'Laptop', '2026-01-18 11:02:00'),

(1, 'best browser session manager', 'Bing', 'Edge', 'Desktop', '2026-01-19 09:45:00'),

(1, 'css flexbox cheat sheet', 'Google', 'Chrome', 'Laptop', '2026-01-19 14:20:00'),

(1, 'javascript modal popup example', 'DuckDuckGo', 'Firefox', 'Laptop', '2026-01-20 16:30:00');