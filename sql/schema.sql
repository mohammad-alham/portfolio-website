-- ============================================================
-- NETWORK ENGINEER PORTFOLIO - Database Schema
-- Database: portfolio_db
-- Engine: MySQL 5.7+ / MariaDB 10.2+ / InfinityFree Compatible
-- NOTE: VARCHAR(191) max for indexed utf8mb4 columns (767 byte limit)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `portfolio_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;

-- ============================================================
-- 1. USERS TABLE
-- ============================================================
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin') NOT NULL DEFAULT 'admin',
    `profile_image` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. PROFILE TABLE
-- ============================================================
CREATE TABLE `profile` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `headline` VARCHAR(500) DEFAULT NULL,
    `about_me` TEXT DEFAULT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `address` VARCHAR(500) DEFAULT NULL,
    `website` VARCHAR(500) DEFAULT NULL,
    `github` VARCHAR(500) DEFAULT NULL,
    `linkedin` VARCHAR(500) DEFAULT NULL,
    `facebook` VARCHAR(500) DEFAULT NULL,
    `twitter` VARCHAR(500) DEFAULT NULL,
    `cv_file` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_profile_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. SKILLS TABLE
-- ============================================================
CREATE TABLE `skills` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `percentage` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `icon` VARCHAR(100) DEFAULT NULL,
    `display_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_skills_category` (`category`),
    INDEX `idx_skills_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. SERVICES TABLE
-- ============================================================
CREATE TABLE `services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `icon` VARCHAR(100) DEFAULT NULL,
    `display_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_services_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. PROJECTS TABLE
-- ============================================================
CREATE TABLE `projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    `description` TEXT NOT NULL,
    `thumbnail` VARCHAR(500) DEFAULT NULL,
    `project_url` VARCHAR(500) DEFAULT NULL,
    `github_url` VARCHAR(500) DEFAULT NULL,
    `technology_stack` TEXT DEFAULT NULL,
    `featured` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_projects_featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. PROJECT IMAGES TABLE
-- ============================================================
CREATE TABLE `project_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
    INDEX `idx_project_images_project` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. CERTIFICATES TABLE
-- ============================================================
CREATE TABLE `certificates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `issuer` VARCHAR(255) NOT NULL,
    `issue_date` DATE DEFAULT NULL,
    `certificate_file` VARCHAR(500) DEFAULT NULL,
    `certificate_image` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. EXPERIENCE TABLE
-- ============================================================
CREATE TABLE `experience` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `company_name` VARCHAR(255) NOT NULL,
    `job_title` VARCHAR(255) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_experience_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. EDUCATION TABLE
-- ============================================================
CREATE TABLE `education` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `institution` VARCHAR(255) NOT NULL,
    `degree` VARCHAR(255) NOT NULL,
    `field_of_study` VARCHAR(255) DEFAULT NULL,
    `start_year` YEAR NOT NULL,
    `end_year` YEAR DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. TESTIMONIALS TABLE
-- ============================================================
CREATE TABLE `testimonials` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_name` VARCHAR(255) NOT NULL,
    `client_position` VARCHAR(255) DEFAULT NULL,
    `company_name` VARCHAR(255) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `client_image` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. CONTACT MESSAGES TABLE
-- ============================================================
CREATE TABLE `contact_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(500) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_messages_read` (`is_read`),
    INDEX `idx_messages_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. SITE SETTINGS TABLE
-- ============================================================
CREATE TABLE `site_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `site_name` VARCHAR(255) NOT NULL DEFAULT 'Network Engineer Portfolio',
    `site_title` VARCHAR(500) DEFAULT NULL,
    `site_description` TEXT DEFAULT NULL,
    `logo` VARCHAR(500) DEFAULT NULL,
    `favicon` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INSERT DEFAULT ADMIN USER (password: admin123)
-- ============================================================
INSERT INTO `users` (`full_name`, `email`, `password`, `role`) VALUES
('Admin User', 'admin@example.com', '$2y$10$jdO9n7CdNDywOf.Vs1ImhuZniaoBeCJVyJ326fH8qqnAEmHBjkatq', 'admin');

-- ============================================================
-- INSERT DEFAULT PROFILE
-- ============================================================
INSERT INTO `profile` (`user_id`, `headline`, `about_me`, `phone`, `address`, `github`, `linkedin`) VALUES
(1,
 'Network Engineer & IT Infrastructure Specialist',
 'I am a certified Network Engineer and IT Infrastructure Specialist with over 8 years of hands-on experience in designing, implementing, and managing complex network environments. My expertise spans across Cisco and Mikrotik networking, network security, server administration, and cloud infrastructure. Throughout my career, I have successfully delivered 150+ projects for enterprises ranging from small businesses to large corporations.',
 '+1 (555) 123-4567',
 '[Your City, Your Country]',
 'https://github.com/networkengineer',
 'https://linkedin.com/in/networkengineer'
);

-- ============================================================
-- INSERT SAMPLE SKILLS
-- ============================================================
INSERT INTO `skills` (`name`, `category`, `percentage`, `icon`, `display_order`) VALUES
('Cisco Routing (OSPF, EIGRP, BGP)', 'Networking', 95, 'fab fa-cisco', 1),
('Cisco Switching (VLAN, STP, EtherChannel)', 'Networking', 92, 'fab fa-cisco', 2),
('MikroTik RouterOS', 'Networking', 90, 'fas fa-wifi', 3),
('MPLS & VPN Technologies', 'Networking', 85, 'fas fa-network-wired', 4),
('Wireless Networking (Wi-Fi 6/6E)', 'Networking', 82, 'fas fa-wifi', 5),
('SD-WAN & Network Automation', 'Networking', 78, 'fas fa-robot', 6),
('Firewall Configuration (Cisco ASA, Fortinet)', 'Security', 93, 'fas fa-shield-halved', 7),
('IDS/IPS Implementation', 'Security', 88, 'fas fa-shield', 8),
('VPN (IPSec, SSL, WireGuard)', 'Security', 90, 'fas fa-lock', 9),
('Security Auditing & Compliance', 'Security', 85, 'fas fa-clipboard-check', 10),
('Access Control & AAA', 'Security', 87, 'fas fa-key', 11),
('Penetration Testing Basics', 'Security', 72, 'fas fa-bug', 12),
('Windows Server (2016/2019/2022)', 'Server', 91, 'fab fa-windows', 13),
('Linux (Ubuntu, CentOS, Debian)', 'Server', 89, 'fab fa-linux', 14),
('Active Directory & Group Policy', 'Server', 90, 'fas fa-users-cog', 15),
('VMware vSphere / Hyper-V', 'Server', 86, 'fas fa-cubes', 16),
('AWS / Azure Cloud', 'Cloud', 82, 'fas fa-cloud', 17),
('Docker & Kubernetes', 'Cloud', 75, 'fab fa-docker', 18),
('Backup & Disaster Recovery', 'Server', 84, 'fas fa-database', 19),
('Scripting (Python, Bash, PowerShell)', 'Server', 80, 'fas fa-code', 20);

-- ============================================================
-- INSERT SAMPLE SERVICES
-- ============================================================
INSERT INTO `services` (`title`, `description`, `icon`, `display_order`) VALUES
('Network Design & Architecture', 'Comprehensive network design services including topology planning, IP addressing schemes, redundancy planning, and scalability assessments for enterprise environments.', 'fas fa-sitemap', 1),
('Network Troubleshooting', 'Expert diagnosis and resolution of complex network issues including latency problems, packet loss, routing issues, and connectivity failures.', 'fas fa-wrench', 2),
('Server Management', 'Full lifecycle server administration including deployment, configuration, monitoring, maintenance, and upgrade of Windows and Linux server environments.', 'fas fa-server', 3),
('Security Audits & Hardening', 'Comprehensive security assessments, vulnerability scanning, and network hardening to protect your infrastructure against modern cyber threats.', 'fas fa-shield-halved', 4),
('IT Consulting', 'Strategic IT consulting services to help businesses align their technology infrastructure with organizational goals and industry best practices.', 'fas fa-headset', 5),
('Infrastructure Deployment', 'End-to-end deployment of network and IT infrastructure including cabling, equipment installation, configuration, and testing for new sites and upgrades.', 'fas fa-cloud-arrow-up', 6);

-- ============================================================
-- INSERT SAMPLE PROJECTS
-- ============================================================
INSERT INTO `projects` (`title`, `slug`, `description`, `technology_stack`, `featured`) VALUES
('Enterprise Multi-Site Network', 'enterprise-multi-site-network', 'Designed and implemented a complete routed network for a multi-site enterprise with 5 branch offices connected via MPLS VPN. Configured OSPF as the routing protocol, implemented VRRP for gateway redundancy, and deployed QoS policies to prioritize critical traffic.', 'Cisco, OSPF, MPLS, QoS, VRRP', 1),
('Network Security Hardening', 'network-security-hardening', 'Conducted a comprehensive security audit for a financial services company. Implemented next-generation firewall policies, deployed IDS/IPS systems, and established security monitoring protocols.', 'Fortinet, Snort, IDS/IPS, PCI DSS', 1),
('Data Center Migration', 'data-center-migration', 'Managed a complete data center migration from an outdated facility to a modern Tier III data center. Planned the migration strategy, supervised cabling, configured new equipment, and ensured zero downtime during the transition.', 'Tier III, Cat6A, OM4, Fiber Channel', 1),
('Hybrid Cloud Architecture', 'hybrid-cloud-architecture', 'Designed and implemented a hybrid cloud architecture connecting on-premises infrastructure with AWS. Set up Direct Connect, VPC peering, auto-scaling groups, and disaster recovery solutions.', 'AWS, Direct Connect, VPC, Auto Scaling', 1),
('Campus Wireless Network', 'campus-wireless-network', 'Designed and deployed a wireless network for a large university campus using Cisco Meraki. Configured over 200 access points, implemented seamless roaming, and set up guest network with voucher-based authentication.', 'Cisco Meraki, Wi-Fi 6, Seamless Roaming', 1),
('Server Virtualization Project', 'server-virtualization-project', 'Led a complete server infrastructure upgrade replacing legacy hardware with modern virtualization hosts. Migrated 50+ physical servers to VMware vSphere, implemented SAN storage, and established disaster recovery procedures.', 'VMware vSphere, SAN, vCenter, DR', 1);

-- ============================================================
-- INSERT SAMPLE CERTIFICATES
-- ============================================================
INSERT INTO `certificates` (`title`, `issuer`, `issue_date`) VALUES
('CCNA - Cisco Certified Network Associate', 'Cisco Systems', '2020-06-15'),
('CCNP Security - Cisco Certified Network Professional', 'Cisco Systems', '2022-08-20'),
('CCNP Enterprise', 'Cisco Systems', '2023-03-10'),
('MTCNA - MikroTik Certified Network Associate', 'MikroTik', '2021-11-05'),
('MTCRE - MikroTik Certified Routing Engineer', 'MikroTik', '2022-09-18'),
('MTCSE - MikroTik Certified Security Engineer', 'MikroTik', '2023-04-22'),
('MCSA - Microsoft Certified Solutions Associate', 'Microsoft', '2019-12-10'),
('AZ-104 Azure Administrator', 'Microsoft', '2023-07-14'),
('CompTIA Network+', 'CompTIA', '2018-05-30'),
('AWS Solutions Architect Associate', 'Amazon Web Services', '2023-10-05'),
('Certified Ethical Hacker (CEH)', 'EC-Council', '2023-01-20'),
('LPIC-1 Linux Administrator', 'Linux Professional Institute', '2020-03-15'),
('ITIL Foundation', 'AXELOS', '2019-08-12');

-- ============================================================
-- INSERT SAMPLE EXPERIENCE
-- ============================================================
INSERT INTO `experience` (`company_name`, `job_title`, `start_date`, `end_date`, `description`) VALUES
('[Company Name]', 'Senior Network Engineer', '2022-01-01', NULL, 'Lead network infrastructure design and implementation for enterprise clients. Manage a team of 5 engineers. Architect and deploy Cisco and Mikrotik-based solutions for high-availability environments. Implement security policies, monitor network performance, and conduct regular audits.'),
('[Company Name]', 'Network Administrator', '2019-03-01', '2021-12-31', 'Managed day-to-day network operations for a mid-size enterprise. Configured and maintained routers, switches, firewalls, and VPNs. Implemented network monitoring solutions and resolved complex connectivity issues. Led the migration to a new data center infrastructure.'),
('[Company Name]', 'Junior Network Engineer', '2017-06-01', '2019-02-28', 'Assisted in the design and deployment of network solutions for SMB clients. Configured Cisco routers and switches, implemented VLANs, and provided Level 2/3 technical support. Gained hands-on experience with firewall configurations and network security protocols.'),
('[Company Name]', 'IT Support Specialist', '2016-01-01', '2017-05-31', 'Provided technical support for hardware, software, and network issues. Assisted in server maintenance, user account management, and system backups. Developed foundational skills in networking and IT operations.');

-- ============================================================
-- INSERT SAMPLE EDUCATION
-- ============================================================
INSERT INTO `education` (`institution`, `degree`, `field_of_study`, `start_year`, `end_year`) VALUES
('[University Name]', 'Bachelor of Science', 'Computer Science', 2012, 2016),
('[College Name]', 'Associate Degree', 'Information Technology', 2010, 2012);

-- ============================================================
-- INSERT SAMPLE TESTIMONIALS
-- ============================================================
INSERT INTO `testimonials` (`client_name`, `client_position`, `company_name`, `message`) VALUES
('John Smith', 'IT Director', 'TechCorp International', 'Working with this network engineer was an absolute pleasure. He designed and implemented a complete network overhaul for our organization with minimal downtime. His expertise in Cisco and security is remarkable.'),
('Sarah Johnson', 'CEO', 'DataFlow Solutions', 'Exceptional network engineering skills. He transformed our outdated infrastructure into a modern, secure, and highly reliable network. Our productivity has increased significantly since the upgrade.'),
('Michael Chen', 'Operations Manager', 'GlobalNet Services', 'The most reliable network professional I have ever worked with. His attention to detail, deep technical knowledge, and professional approach made our data center migration completely seamless.');

-- ============================================================
-- INSERT DEFAULT SITE SETTINGS
-- ============================================================
INSERT INTO `site_settings` (`site_name`, `site_title`, `site_description`) VALUES
('NetEngineer', 'Network Engineer & IT Infrastructure Specialist', 'Professional Network Engineer Portfolio - Specializing in Cisco, Mikrotik, Network Security, and IT Infrastructure.');

-- ============================================================
-- ER DIAGRAM RELATIONSHIPS
-- ============================================================
-- users 1──1 profile
-- users 1──* contact_messages
-- projects 1──* project_images
-- All other tables are standalone reference entities

-- ============================================================
-- END OF SCHEMA
-- ============================================================
