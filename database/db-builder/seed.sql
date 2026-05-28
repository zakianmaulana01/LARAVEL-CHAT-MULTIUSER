-- =============================================
-- Laravel Chat Multiuser - Sample Data
-- Import setelah schema.sql
-- =============================================

SET NAMES utf8mb4;

-- ----------------------------
-- Users: 1 admin + 5 users
-- Password: 'password' (bcrypt hash)
-- ----------------------------
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `last_seen`, `is_banned`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@erp.test', NOW(), '$2y$12$YJKw1bGKLHhH8Dx7AwI5xOIL1oy9iNqf0yNhK9tQcrjG.gXZPJFCO', 'superadmin', NULL, NOW(), 0, NOW(), NOW()),
(2, 'Budi Santoso', 'budi@example.com', NOW(), '$2y$12$YJKw1bGKLHhH8Dx7AwI5xOIL1oy9iNqf0yNhK9tQcrjG.gXZPJFCO', 'user', NULL, NOW(), 0, NOW(), NOW()),
(3, 'Siti Rahayu', 'siti@example.com', NOW(), '$2y$12$YJKw1bGKLHhH8Dx7AwI5xOIL1oy9iNqf0yNhK9tQcrjG.gXZPJFCO', 'user', NULL, NOW(), 0, NOW(), NOW()),
(4, 'Ahmad Pratama', 'ahmad@example.com', NOW(), '$2y$12$YJKw1bGKLHhH8Dx7AwI5xOIL1oy9iNqf0yNhK9tQcrjG.gXZPJFCO', 'user', NULL, NOW(), 0, NOW(), NOW()),
(5, 'Dewi Lestari', 'dewi@example.com', NOW(), '$2y$12$YJKw1bGKLHhH8Dx7AwI5xOIL1oy9iNqf0yNhK9tQcrjG.gXZPJFCO', 'user', NULL, NOW(), 0, NOW(), NOW()),
(6, 'Rizky Hidayat', 'rizky@example.com', NOW(), '$2y$12$YJKw1bGKLHhH8Dx7AwI5xOIL1oy9iNqf0yNhK9tQcrjG.gXZPJFCO', 'user', NULL, NOW(), 0, NOW(), NOW());

-- ----------------------------
-- Conversations: 5 percakapan direct
-- ----------------------------
INSERT INTO `conversations` (`id`, `name`, `type`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'direct', 2, NOW(), NOW()),
(2, NULL, 'direct', 3, NOW(), NOW()),
(3, NULL, 'direct', 1, NOW(), NOW()),
(4, NULL, 'direct', 4, NOW(), NOW()),
(5, NULL, 'direct', 5, NOW(), NOW());

-- ----------------------------
-- Conversation Participants
-- ----------------------------
INSERT INTO `conversation_user` (`conversation_id`, `user_id`, `joined_at`) VALUES
(1, 2, NOW()), (1, 3, NOW()),
(2, 3, NOW()), (2, 4, NOW()),
(3, 1, NOW()), (3, 2, NOW()),
(4, 4, NOW()), (4, 5, NOW()),
(5, 5, NOW()), (5, 6, NOW());

-- ----------------------------
-- Messages: 20 pesan contoh
-- ----------------------------
INSERT INTO `messages` (`conversation_id`, `sender_id`, `body`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 2, 'Halo Siti, apa kabar?', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 3, 'Baik Budi! Kamu gimana?', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR 50 MINUTE), DATE_SUB(NOW(), INTERVAL 1 HOUR 50 MINUTE)),
(1, 2, 'Alhamdulillah sehat. Lagi ngerjain apa?', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR 40 MINUTE), DATE_SUB(NOW(), INTERVAL 1 HOUR 40 MINUTE)),
(1, 3, 'Lagi bikin laporan bulanan nih 😅', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR 30 MINUTE), DATE_SUB(NOW(), INTERVAL 1 HOUR 30 MINUTE)),
(2, 3, 'Ahmad, meeting jam berapa hari ini?', 1, DATE_SUB(NOW(), INTERVAL 3 HOUR), DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(2, 4, 'Jam 10 pagi di ruang rapat lantai 3', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR 50 MINUTE), DATE_SUB(NOW(), INTERVAL 2 HOUR 50 MINUTE)),
(2, 3, 'Oke siap, saya prepare materinya dulu ya', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR 40 MINUTE), DATE_SUB(NOW(), INTERVAL 2 HOUR 40 MINUTE)),
(2, 4, 'Sip, jangan lupa bawa USB ya', 0, DATE_SUB(NOW(), INTERVAL 2 HOUR 30 MINUTE), DATE_SUB(NOW(), INTERVAL 2 HOUR 30 MINUTE)),
(3, 1, 'Budi, tolong kirim laporan kemarin ya', 1, DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(3, 2, 'Siap pak, saya kirim sekarang via email', 1, DATE_SUB(NOW(), INTERVAL 4 HOUR 50 MINUTE), DATE_SUB(NOW(), INTERVAL 4 HOUR 50 MINUTE)),
(3, 1, 'Terima kasih, saya tunggu ya', 1, DATE_SUB(NOW(), INTERVAL 4 HOUR 40 MINUTE), DATE_SUB(NOW(), INTERVAL 4 HOUR 40 MINUTE)),
(3, 2, 'Sudah saya kirim pak. Mohon dicek 🙏', 0, DATE_SUB(NOW(), INTERVAL 4 HOUR), DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(4, 4, 'Dewi, ada project baru nih dari client', 1, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 5, 'Wah serius? Project apa?', 1, DATE_SUB(NOW(), INTERVAL 23 HOUR), DATE_SUB(NOW(), INTERVAL 23 HOUR)),
(4, 4, 'Bikin aplikasi chat, deadline 2 minggu', 1, DATE_SUB(NOW(), INTERVAL 22 HOUR), DATE_SUB(NOW(), INTERVAL 22 HOUR)),
(4, 5, 'Wadu cepet banget 😱 Kita bisa ga ya?', 0, DATE_SUB(NOW(), INTERVAL 21 HOUR), DATE_SUB(NOW(), INTERVAL 21 HOUR)),
(5, 5, 'Rizky, kamu udah coba framework baru itu?', 1, DATE_SUB(NOW(), INTERVAL 6 HOUR), DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(5, 6, 'Udah! Laravel 11 mantap banget', 1, DATE_SUB(NOW(), INTERVAL 5 HOUR 50 MINUTE), DATE_SUB(NOW(), INTERVAL 5 HOUR 50 MINUTE)),
(5, 5, 'Iya denger-denger lebih simpel ya structurenya?', 1, DATE_SUB(NOW(), INTERVAL 5 HOUR 40 MINUTE), DATE_SUB(NOW(), INTERVAL 5 HOUR 40 MINUTE)),
(5, 6, 'Bener, config file lebih sedikit. Lebih clean!', 0, DATE_SUB(NOW(), INTERVAL 5 HOUR 30 MINUTE), DATE_SUB(NOW(), INTERVAL 5 HOUR 30 MINUTE));

-- ----------------------------
-- Migrations record
-- ----------------------------
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('0001_01_01_000003_create_conversations_table', 1),
('0001_01_01_000004_create_messages_table', 1);
