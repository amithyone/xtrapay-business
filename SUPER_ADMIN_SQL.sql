-- SQL Commands to Fix Super Admin Issues
-- Run these commands in your MySQL database

-- 1. First, update the password to use Bcrypt hash
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'superadmin@xtrapay.com';

-- 2. Create the super_admins table if it doesn't exist
CREATE TABLE IF NOT EXISTS `super_admins` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'super_admin',
  `permissions` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `super_admins_user_id_unique` (`user_id`),
  KEY `super_admins_user_id_foreign` (`user_id`),
  CONSTRAINT `super_admins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Insert super admin record for the existing user
INSERT INTO `super_admins` (`user_id`, `role`, `permissions`, `is_active`, `created_at`, `updated_at`) 
VALUES (
    1, -- Replace with actual user_id if different
    'super_admin',
    NULL, -- Super admins have all permissions
    1,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE 
    `is_active` = 1,
    `updated_at` = NOW();

-- 4. Alternative: Create a new super admin user if the above doesn't work
INSERT INTO `users` (`name`, `email`, `password`, `is_admin`, `email_verified_at`, `created_at`, `updated_at`) 
VALUES (
    'Super Admin',
    'admin@xtrapay.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1,
    NOW(),
    NOW(),
    NOW()
);

-- 5. Then create super admin record for the new user
INSERT INTO `super_admins` (`user_id`, `role`, `permissions`, `is_active`, `created_at`, `updated_at`) 
VALUES (
    LAST_INSERT_ID(),
    'super_admin',
    NULL,
    1,
    NOW(),
    NOW()
);

-- 6. Check if the records were created successfully
SELECT 
    u.id as user_id,
    u.name,
    u.email,
    u.is_admin,
    sa.id as super_admin_id,
    sa.role,
    sa.is_active as super_admin_active
FROM users u
LEFT JOIN super_admins sa ON u.id = sa.user_id
WHERE u.email IN ('superadmin@xtrapay.com', 'admin@xtrapay.com'); 