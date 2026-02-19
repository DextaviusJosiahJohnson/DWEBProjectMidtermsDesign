-- adjustments: Remove the creation of table, all of the creation will be on smart_browser_state.sql file
use smart_browser_state;

-- Insert sample data
INSERT INTO browser_states (user_id, device, browser, tab_data, created_at) VALUES
(2,'Laptop','Chrome','["https://google.com","https://github.com","https://youtube.com"]','2026-02-10 09:15:00'),
(2,'Desktop','Firefox','["https://mozilla.org","https://stackoverflow.com"]','2026-02-09 14:25:00'),
(2,'Work PC','Edge','["https://microsoft.com","https://office.com","https://linkedin.com"]','2026-02-08 11:40:00'),
(2,'Mobile','Safari','["https://apple.com","https://icloud.com"]','2026-02-11 18:10:00'),
(2,'Laptop','Chrome','["https://github.com","https://chat.openai.com","https://gmail.com","https://drive.google.com"]','2026-02-12 08:05:00'),
(2,'Desktop','Edge','["https://bing.com","https://amazon.com"]','2026-02-12 16:30:00'),
(2,'Work PC','Chrome','["https://slack.com","https://notion.so","https://figma.com"]','2026-02-13 09:55:00'),
(2,'Mobile','Firefox','["https://reddit.com","https://twitter.com"]','2026-02-13 21:15:00'),
(2,'Laptop','Safari','["https://netflix.com","https://spotify.com","https://medium.com"]','2026-02-14 12:10:00'),
(2,'Desktop','Chrome','["https://stackoverflow.com","https://w3schools.com","https://geeksforgeeks.org"]','2026-02-14 15:20:00'),
(2,'Work PC','Firefox','["https://trello.com","https://asana.com"]','2026-02-15 10:05:00'),
(2,'Mobile','Chrome','["https://instagram.com"]','2026-02-15 22:00:00'),
(2,'Laptop','Edge','["https://coursera.org","https://udemy.com"]','2026-02-16 08:30:00'),
(2,'Desktop','Safari','["https://bbc.com","https://cnn.com"]','2026-02-16 14:45:00'),
(2,'Work PC','Chrome','["https://jira.com","https://confluence.com","https://bitbucket.org"]','2026-02-16 18:55:00'),
(2,'Mobile','Edge','["https://whatsapp.com"]','2026-02-17 07:25:00'),
(2,'Laptop','Firefox','["https://paypal.com","https://stripe.com"]','2026-02-17 09:10:00'),
(2,'Desktop','Chrome','["https://github.com","https://gitlab.com","https://docker.com"]','2026-02-17 20:30:00');



