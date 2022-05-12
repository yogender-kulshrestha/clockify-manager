-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.28 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table clockify_manager.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.admins: ~0 rows (approximately)
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.approvers
CREATE TABLE IF NOT EXISTS `approvers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `approver_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.approvers: ~0 rows (approximately)
/*!40000 ALTER TABLE `approvers` DISABLE KEYS */;
/*!40000 ALTER TABLE `approvers` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.email_alerts
CREATE TABLE IF NOT EXISTS `email_alerts` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` enum('1','0') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table clockify_manager.email_alerts: ~24 rows (approximately)
/*!40000 ALTER TABLE `email_alerts` DISABLE KEYS */;
INSERT INTO `email_alerts` (`id`, `type`, `description`, `status`) VALUES
	(1, 'leaveSubmitToEmployee', 'Leave Request Submit Mail To Employee', '1'),
	(2, 'leaveResubmitToEmployee', 'Leave Request Resubmit Mail To Employee', '1'),
	(3, 'leaveCancelledToEmployee', 'Leave Request Cancel Mail To Employee', '1'),
	(4, 'leaveReviseToEmployee', 'Leave Revise and Resubmit Mail To Employee', '1'),
	(5, 'leaveApprovedToEmployee', 'Leave Request Approve Mail To Employee', '1'),
	(6, 'leaveRejectedToEmployee', 'Leave Request Reject Mail To Employee', '1'),
	(7, 'leaveFinalApprovedToEmployee', 'Leave Final Approve Mail To Employee', '1'),
	(8, 'timesheetSubmitToEmployee', 'Timecard Submit Mail To Employee', '1'),
	(9, 'timesheetResubmitToEmployee', 'Timecard Resubmit Mail To Employee', '1'),
	(10, 'timesheetReviseToEmployee', 'Timecard Revise and Resubmit Mail To Employee', '1'),
	(11, 'timesheetApprovedToEmployee', 'Timecard Approve Mail To Employee', '1'),
	(12, 'reminderToEmployee', 'Reminder Mail To Employee[Pending Week Report Submit]', '1'),
	(13, 'leaveSubmitToApprover', 'Leave Request Submit Mail To Approver', '1'),
	(14, 'leaveResubmitToApprover', 'Leave Request Resubmit Mail To Approver', '1'),
	(15, 'leaveCancelledToApprover', 'Leave Request Cancel Mail To Approver', '1'),
	(16, 'leaveReviseToApprover', 'Leave Request Revise and Resubmit Mail To Approver', '1'),
	(17, 'leaveApprovedToApprover', 'Leave Request Approve Mail To Approver', '1'),
	(18, 'leaveRejectedToApprover', 'Leave Request Reject Mail To Approver', '1'),
	(19, 'leaveFinalApprovedToApprover', 'Leave Request Final Approve Mail To Approver', '1'),
	(20, 'timesheetSubmitToApprover', 'Timecard Submit Mail To Approver', '1'),
	(21, 'timesheetResubmitToApprover', 'Timecard Resubmit Mail To Approver', '1'),
	(22, 'timesheetReviseToApprover', 'Timecard Revise and Resubmit Mail To Approver', '1'),
	(23, 'timesheetApprovedToApprover', 'Timecard Approve Mail To Approver', '1'),
	(24, 'reminderToApprover', 'Reminder Mail To Approver [Pending Timecard Approving] ', '1');
/*!40000 ALTER TABLE `email_alerts` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.failed_jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.holidays
CREATE TABLE IF NOT EXISTS `holidays` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table clockify_manager.holidays: ~2 rows (approximately)
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
INSERT INTO `holidays` (`id`, `date`, `description`, `created_at`, `updated_at`) VALUES
	(1, '2022-01-01', 'New Year', '2022-03-12 04:31:19', '2022-03-12 04:31:19');
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.jobs: ~151 rows (approximately)
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.leaves
CREATE TABLE IF NOT EXISTS `leaves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leave_type_id` bigint NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `exception` int NOT NULL DEFAULT '0',
  `status` enum('Submitted','Revise and Resubmit','Approved','Final Approved','Rejected','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Submitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.leaves: ~5 rows (approximately)
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.leave_balances
CREATE TABLE IF NOT EXISTS `leave_balances` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `leave_type_id` bigint NOT NULL,
  `balance` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table clockify_manager.leave_balances: ~511 rows (approximately)
/*!40000 ALTER TABLE `leave_balances` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_balances` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.leave_types
CREATE TABLE IF NOT EXISTS `leave_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.leave_types: ~7 rows (approximately)
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` (`id`, `name`, `balance`, `created_at`, `updated_at`) VALUES
	(1, 'Paid Leave', 1, '2021-12-29 14:35:02', '2022-03-15 13:27:15'),
	(2, 'Rest Day', 0, '2021-12-29 14:35:03', '2022-03-15 12:54:21'),
	(3, 'Sick Leave', 1, '2021-12-29 14:35:03', '2022-03-15 13:33:50'),
	(4, 'Half Paid Sick Leave', 1, '2022-01-05 15:34:21', '2022-03-11 13:07:51'),
	(5, 'Bereavement', 0, '2022-01-05 15:34:21', '2022-01-05 15:34:21'),
	(6, 'Maternity', 0, '2022-01-05 15:34:21', '2022-01-05 15:34:21'),
	(7, 'Other (explain)', 1, '2022-01-05 15:34:21', '2022-02-10 15:29:50');
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.migrations: ~0 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.password_resets: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.personal_access_tokens: ~0 rows (approximately)
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.projects
CREATE TABLE IF NOT EXISTS `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clockify_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.projects: ~81 rows (approximately)
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` (`id`, `clockify_id`, `name`, `created_at`, `updated_at`) VALUES
	(1, '60ab1bc1f917c56d38bfcbaf', '1:20MW', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(2, '60ab1bbdb7cf503450a0f41e', '2:Engineering R&D', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(3, '60ab1bb9f917c56d38bfcba3', '3:Makebe Minigrid', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(4, '60ab1bb6b7cf503450a0f414', '4:Minigrids', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(5, '60ab1bb279793e3042ffcb38', '5:CarbonTrust', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(6, '60ab1bad79793e3042ffcb2a', '6:Project Development', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(7, '60ab1ba9b7cf503450a0f3e9', '7:Administrative/Overhead', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(8, '60ab1ba5b7cf503450a0f3df', '8:PAOP', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(9, '60ab1ba179793e3042ffcb14', '9:EEP', '2022-01-04 09:22:05', '2022-01-04 09:22:05'),
	(10, '60ab1b97f917c56d38bfcb5c', '1:20MW', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(11, '60ab1b93f917c56d38bfcb55', '2:Engineering R&D', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(12, '60ab1b8fb7cf503450a0f3b9', '3:Makebe Minigrid', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(13, '60ab1b8cf917c56d38bfcb43', '4:Minigrids', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(14, '60ab1b88f917c56d38bfcb3e', '5:CarbonTrust', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(15, '60ab1b84b7cf503450a0f3ab', '6:Project Development', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(16, '60ab1b8079793e3042ffcadd', '7:Administrative/Overhead', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(17, '60ab1b7cb7cf503450a0f3a1', '8:PAOP', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(18, '60ab1b78b7cf503450a0f39b', '9:EEP', '2022-01-04 09:22:06', '2022-01-04 09:22:06'),
	(19, '60ab1b6c79793e3042ffcabc', '1:20MW', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(20, '60ab1b68f917c56d38bfcb0b', '2:Engineering R&D', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(21, '60ab1b6479793e3042ffcaaa', '3:Makebe Minigrid', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(22, '60ab1b6079793e3042ffca9e', '4:Minigrids', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(23, '60ab1b5cb7cf503450a0f372', '5:CarbonTrust', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(24, '60ab1b58f917c56d38bfcaea', '6:Project Development', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(25, '60ab1b5479793e3042ffca8a', '7:Administrative/Overhead', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(26, '60ab1b50f917c56d38bfcadb', '8:PAOP', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(27, '60ab1b4c79793e3042ffca79', '9:EEP', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(28, '60ab1b40f917c56d38bfcab2', '1:20MW', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(29, '60ab1b3cb7cf503450a0f322', '2:Engineering R&D', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(30, '60ab1b38f917c56d38bfcaa8', '3:Makebe Minigrid', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(31, '60ab1b3479793e3042ffca40', '4:Minigrids', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(32, '60ab1b30f917c56d38bfca95', '5:CarbonTrust', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(33, '60ab1b2cf917c56d38bfca90', '6:Project Development', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(34, '60ab1b28b7cf503450a0f2f6', '7:Administrative/Overhead', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(35, '60ab1b2479793e3042ffca18', '8:PAOP', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(36, '60ab1b20b7cf503450a0f2e2', '9:EEP', '2022-01-04 09:22:07', '2022-01-04 09:22:07'),
	(37, '60ab1b1279793e3042ffc9e8', '1:20MW', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(38, '60ab1b0d79793e3042ffc9e1', '2:Engineering R&D', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(39, '60ab1b0a79793e3042ffc9d1', '3:Makebe Minigrid', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(40, '60ab1b05f917c56d38bfca4a', '4:Minigrids', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(41, '60ab1b0179793e3042ffc9ba', '5:CarbonTrust', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(42, '60ab1afc79793e3042ffc9b1', '6:Project Development', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(43, '60ab1af8f917c56d38bfca33', '7:Administrative/Overhead', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(44, '60ab1af3f917c56d38bfca22', '8:PAOP', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(45, '60ab1aeef917c56d38bfca1b', '9:EEP', '2022-01-04 09:22:08', '2022-01-04 09:22:08'),
	(46, '60ab127cb7cf503450a0e1fa', '1:20MW', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(47, '60ab1278f917c56d38bfba57', '2:Engineering R&D', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(48, '60ab1274f917c56d38bfba4a', '3:Makebe Minigrid', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(49, '60ab1270b7cf503450a0e1e6', '4:Minigrids', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(50, '60ab126cf917c56d38bfba3c', '5:CarbonTrust', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(51, '60ab1267f917c56d38bfba32', '6:Project Development', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(52, '60ab126279793e3042ffb949', '7:Administrative/Overhead', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(53, '60ab125c79793e3042ffb93b', '8:PAOP', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(54, '60ab1255b7cf503450a0e1b0', '9:EEP', '2022-01-04 09:22:09', '2022-01-04 09:22:09'),
	(55, '60ace061215fef1f3a3baec9', '1:20MW', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(56, '60ace05d215fef1f3a3baea6', '2:Engineering R&D', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(57, '60ace0590ecd812af6cbe276', '3:Makebe Minigrid', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(58, '60ace0540ecd812af6cbe258', '4:Minigrids', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(59, '60ace0500ecd812af6cbe244', '5:CarbonTrust', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(60, '60ace04c72b1b72df488755e', '6:Project Development', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(61, '60ace0470ecd812af6cbe221', '7:Administrative/Overhead', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(62, '60ace041215fef1f3a3bae37', '8:PAOP', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(63, '60ace03c0ecd812af6cbe1d8', '9:EEP', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(64, '6130b49faaaebb6f4b08dede', '1:20MW', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(65, '6130b4996783ee6c7a0d86d1', '2:Engineering R&D', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(66, '6130b493aaaebb6f4b08deac', '3:Makebe Minigrid', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(67, '6130b48daaaebb6f4b08de88', '4:Minigrids', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(68, '6130b487aaaebb6f4b08de77', '5:CarbonTrust', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(69, '6130b4818f7b1544cc8609c2', '6:Project Development', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(70, '6130b47baaaebb6f4b08de2b', '7:Administrative/Overhead', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(71, '6130b475aaaebb6f4b08de0a', '8:PAOP', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(72, '6130b46c6783ee6c7a0d8613', '9:EEP', '2022-01-04 09:22:10', '2022-01-04 09:22:10'),
	(73, '6130b4c68f7b1544cc860af0', '1:20MW', '2022-01-04 09:22:11', '2022-01-04 09:22:11'),
	(74, '6130b4cc8f7b1544cc860b07', '2:Engineering R&D', '2022-01-04 09:22:11', '2022-01-04 09:22:11'),
	(75, '6130b4d26783ee6c7a0d87dd', '3:Makebe Minigrid', '2022-01-04 09:22:11', '2022-01-04 09:22:11'),
	(76, '6130b4d7aaaebb6f4b08dfcb', '4:Minigrids', '2022-01-04 09:22:11', '2022-01-04 09:22:11'),
	(77, '6130b4dc6783ee6c7a0d8808', '5:CarbonTrust', '2022-01-04 09:22:11', '2022-01-04 09:22:11'),
	(78, '6130b4e1aaaebb6f4b08dff8', '6:Project Development', '2022-01-04 09:22:11', '2022-01-04 09:22:11'),
	(79, '6130b4e68f7b1544cc860b8a', '7:Administrative/Overhead', '2022-01-04 09:22:11', '2022-01-04 09:22:11'),
	(80, '6130b4ec8f7b1544cc860bbd', '8:PAOP', '2022-01-04 09:22:11', '2022-01-04 09:22:11'),
	(81, '6130b4f5aaaebb6f4b08e071', '9:EEP', '2022-01-04 09:22:11', '2022-01-04 09:22:11');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.records
CREATE TABLE IF NOT EXISTS `records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('Submitted','Revise and Resubmit','Approved','Edit Later','Resubmitted','Final Approved','Rejected','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Submitted',
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.records: ~7 rows (approximately)
/*!40000 ALTER TABLE `records` DISABLE KEYS */;
/*!40000 ALTER TABLE `records` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `working_time_from` time NOT NULL,
  `working_time_to` time NOT NULL,
  `day_working_hours` int NOT NULL,
  `overclocking_hours` int NOT NULL,
  `weekly_hours` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table clockify_manager.settings: ~1 rows (approximately)
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`working_time_from`, `working_time_to`, `day_working_hours`, `overclocking_hours`, `weekly_hours`) VALUES
	('06:00:00', '20:00:00', 9, 15, 56);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.time_cards
CREATE TABLE IF NOT EXISTS `time_cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `week` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `flags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `net_hours` float NOT NULL DEFAULT '0',
  `ot_hours` float NOT NULL DEFAULT '0',
  `short_hours` float NOT NULL DEFAULT '0',
  `unpaid_hours` float NOT NULL DEFAULT '0',
  `exception` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `employee_remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approver_remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.time_cards: ~21 rows (approximately)
/*!40000 ALTER TABLE `time_cards` DISABLE KEYS */;
/*!40000 ALTER TABLE `time_cards` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.time_sheets
CREATE TABLE IF NOT EXISTS `time_sheets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clockify_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tag_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billable` tinyint(1) DEFAULT NULL,
  `task_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `week` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `duration_time` bigint DEFAULT NULL,
  `ot_time` bigint DEFAULT NULL,
  `duration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workspace_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT NULL,
  `custom_field_values` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `time_error` enum('0','1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '0.NoError 1.Error',
  `error_eo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `error_ot` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `error_bm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `error_wh` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `error_le` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `error_remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `exception` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `employee_remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approver_remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.time_sheets: ~886 rows (approximately)
/*!40000 ALTER TABLE `time_sheets` DISABLE KEYS */;
/*!40000 ALTER TABLE `time_sheets` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clockify_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','hr','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `memberships` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_holidays` int NOT NULL DEFAULT '0',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table clockify_manager.users: ~86 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `clockify_id`, `employee_id`, `role`, `type`, `name`, `image`, `email`, `email_verified_at`, `password`, `memberships`, `settings`, `remember_token`, `paid_holidays`, `status`, `created_at`, `updated_at`) VALUES
	(1, '1', '1PWR0001', 'admin', 'fulltime', 'Matthew', 'https://img.clockify.me/no-user-image.png', 'admin@1pwrafrica.com', NULL, '$2y$10$6OHQqh1LLGn4.kkePUkijOkIErUdNbClhsdTbbDcSZ20qhvZfP48y', NULL, NULL, 'KtZbBwiPIS0qGdJCVHtIeagRGNB9ersvihRbZoxNabJ5KTCmLDjQjILMUIVC', 0, 'active', '2021-12-27 05:25:47', '2022-04-23 05:13:14'),
	(2, '609935adba9fdd7cafab3447', '1PWR0002', 'hr', 'fulltime', '1PWR HR', 'https://img.clockify.me/no-user-image.png', 'hr@1pwrafrica.com', NULL, '$2y$10$1AujAVTAk8R9AGFpPjCZcu37uclOH5zdxhHxKsWP1WDOX9H.LCm0a', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60aadc5c79793e3042ff5272","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1ba179793e3042ffcb14","membershipType":"PROJECT","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1ba5b7cf503450a0f3df","membershipType":"PROJECT","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1ba9b7cf503450a0f3e9","membershipType":"PROJECT","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1bad79793e3042ffcb2a","membershipType":"PROJECT","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1bb279793e3042ffcb38","membershipType":"PROJECT","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1bb6b7cf503450a0f414","membershipType":"PROJECT","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1bb9f917c56d38bfcba3","membershipType":"PROJECT","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1bbdb7cf503450a0f41e","membershipType":"PROJECT","membershipStatus":"ACTIVE"},{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60ab1bc1f917c56d38bfcbaf","membershipType":"PROJECT","membershipStatus":"ACTIVE"}]', '{"weekStart":"SUNDAY","timeZone":"Asia\\/Calcutta","timeFormat":"HOUR12","dateFormat":"MM\\/DD\\/YYYY","sendNewsletter":false,"weeklyUpdates":false,"longRunning":false,"scheduledReports":true,"approval":true,"pto":true,"alerts":true,"reminders":true,"timeTrackingManual":true,"summaryReportSettings":{"group":"Project","subgroup":"Time Entry"},"isCompactViewOn":false,"dashboardSelection":"ME","dashboardViewType":"PROJECT","dashboardPinToTop":false,"projectListCollapse":50,"collapseAllProjectLists":false,"groupSimilarEntriesDisabled":false,"myStartOfDay":"09:00","projectPickerTaskFilter":false,"lang":null,"theme":"DEFAULT"}', NULL, 0, 'active', '2022-02-14 10:54:18', '2022-03-03 09:39:40');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table clockify_manager.workspaces
CREATE TABLE IF NOT EXISTS `workspaces` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `clockify_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `hourly_rate` text NOT NULL,
  `memberships` longtext NOT NULL,
  `workspace_settings` longtext NOT NULL,
  `image_url` text,
  `feature_subscription_type` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table clockify_manager.workspaces: ~10 rows (approximately)
/*!40000 ALTER TABLE `workspaces` DISABLE KEYS */;
INSERT INTO `workspaces` (`id`, `clockify_id`, `name`, `hourly_rate`, `memberships`, `workspace_settings`, `image_url`, `feature_subscription_type`, `created_at`, `updated_at`) VALUES
	(1, '60aadc5c79793e3042ff5272', '1PWR EHS', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60aadc5c79793e3042ff5272","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf8f379793e3042ff8873","hourlyRate":null,"costRate":null,"targetId":"60aadc5c79793e3042ff5272","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf90879793e3042ff88ad","hourlyRate":null,"costRate":null,"targetId":"60aadc5c79793e3042ff5272","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60d07b87b6aa904717603f14","hourlyRate":null,"costRate":null,"targetId":"60aadc5c79793e3042ff5272","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":false,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"category","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', 'https://img.clockify.me/2021-05-24T00%3A25%3A22.494Z1powerlogo.jpg', NULL, '2022-01-04 07:29:29', '2022-01-04 07:29:29'),
	(2, '60aadc66f917c56d38bf51ec', '1PWR ADMIN AND GRANTS', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60aadc66f917c56d38bf51ec","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf970f917c56d38bf8a48","hourlyRate":null,"costRate":null,"targetId":"60aadc66f917c56d38bf51ec","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf97e79793e3042ff8975","hourlyRate":null,"costRate":null,"targetId":"60aadc66f917c56d38bf51ec","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"},{"userId":"60aaf988b7cf503450a0b1f2","hourlyRate":null,"costRate":null,"targetId":"60aadc66f917c56d38bf51ec","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf998f917c56d38bf8a83","hourlyRate":null,"costRate":null,"targetId":"60aadc66f917c56d38bf51ec","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61a771a6d8884f11e74ebbf6","hourlyRate":null,"costRate":null,"targetId":"60aadc66f917c56d38bf51ec","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b6044cb6c54d408d9778a2","hourlyRate":null,"costRate":null,"targetId":"60aadc66f917c56d38bf51ec","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":false,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"category","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', 'https://img.clockify.me/2021-05-23T23%3A49%3A36.595Z1powerlogo.jpg', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50'),
	(3, '60aadc73b7cf503450a079d2', '1PWR MANUFACTURING & ENGINEERING', '{"amount":0,"currency":"ZAR"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf9ccf917c56d38bf8ae5","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf9d5b7cf503450a0b27d","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf9ddf917c56d38bf8b0d","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf9e7b7cf503450a0b29b","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"},{"userId":"60aaf9f1b7cf503450a0b2ac","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf9fbb7cf503450a0b2bc","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafa05f917c56d38bf8b51","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafa0e79793e3042ff8a6e","hourlyRate":null,"costRate":null,"targetId":"60aadc73b7cf503450a079d2","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":false,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"category","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', 'https://img.clockify.me/2021-05-23T22%3A51%3A45.736Z1powerlogo.jpg', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50'),
	(4, '60aaf2a179793e3042ff7c9e', '1PWR FACILITIES', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60aaf2a179793e3042ff7c9e","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf9ccf917c56d38bf8ae5","hourlyRate":null,"costRate":null,"targetId":"60aaf2a179793e3042ff7c9e","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafa28b7cf503450a0b302","hourlyRate":null,"costRate":null,"targetId":"60aaf2a179793e3042ff7c9e","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafa34f917c56d38bf8bae","hourlyRate":null,"costRate":null,"targetId":"60aaf2a179793e3042ff7c9e","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60e6bcd49efece4c32dc596a","hourlyRate":null,"costRate":null,"targetId":"60aaf2a179793e3042ff7c9e","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"615d723b4a94067919bcac87","hourlyRate":null,"costRate":null,"targetId":"60aaf2a179793e3042ff7c9e","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":false,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"category","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', 'https://img.clockify.me/2021-05-24T00%3A26%3A47.952Z1powerlogo.jpg', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50'),
	(5, '60aaf32279793e3042ff7d63', '1PWR EE', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafa73f917c56d38bf8c1d","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafa73f917c56d38bf8c1f","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"},{"userId":"60aafa73f917c56d38bf8c1e","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafa73f917c56d38bf8c1c","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60ace09972b1b72df48876d9","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"},{"userId":"60c758393852d02ead929860","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"},{"userId":"61420c929b2ba07fe3d438aa","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"614dcc1f607a372a9e15f02e","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"619cca379fc1a370a8c6f4d4","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61a610d166c1ec71cfb203db","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61a610dd9ed9a71e3211a78f","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61a610e5b7af9443e8f01c79","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b09cd4d363db4f302737c1","hourlyRate":null,"costRate":null,"targetId":"60aaf32279793e3042ff7d63","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":false,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"category","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', 'https://img.clockify.me/2021-05-24T00%3A29%3A23.784Z1powerlogo.jpg', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50'),
	(6, '60aaf33d79793e3042ff7d80', '1PWR MINIGRIDS', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60aaf33d79793e3042ff7d80","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafad7b7cf503450a0b475","hourlyRate":null,"costRate":null,"targetId":"60aaf33d79793e3042ff7d80","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafad7b7cf503450a0b476","hourlyRate":null,"costRate":null,"targetId":"60aaf33d79793e3042ff7d80","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafad7b7cf503450a0b477","hourlyRate":null,"costRate":null,"targetId":"60aaf33d79793e3042ff7d80","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafae1b7cf503450a0b497","hourlyRate":null,"costRate":null,"targetId":"60aaf33d79793e3042ff7d80","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafae1b7cf503450a0b498","hourlyRate":null,"costRate":null,"targetId":"60aaf33d79793e3042ff7d80","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"},{"userId":"6166cd1b8fe7056cf58d1a69","hourlyRate":null,"costRate":null,"targetId":"60aaf33d79793e3042ff7d80","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":false,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"category","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', 'https://img.clockify.me/2021-05-24T00%3A29%3A04.171Z1powerlogo.jpg', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50'),
	(7, '60acdfe0215fef1f3a3bac53', '1PWR O&M', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60acdfe0215fef1f3a3bac53","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60ace02372b1b72df4887495","hourlyRate":null,"costRate":null,"targetId":"60acdfe0215fef1f3a3bac53","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"},{"userId":"60aafae1b7cf503450a0b496","hourlyRate":null,"costRate":null,"targetId":"60acdfe0215fef1f3a3bac53","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"611e053e95dbb63f7a63d546","hourlyRate":null,"costRate":null,"targetId":"60acdfe0215fef1f3a3bac53","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"614c6905a787f00cd48a3ac6","hourlyRate":null,"costRate":null,"targetId":"60acdfe0215fef1f3a3bac53","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61a610fa66c1ec71cfb2069b","hourlyRate":null,"costRate":null,"targetId":"60acdfe0215fef1f3a3bac53","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":false,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"category","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', 'https://img.clockify.me/2021-05-25T11%3A30%3A59.327Z1powerlogo.jpg', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50'),
	(8, '60e822fb89e4e6159d0c6441', '1PWR PUECO', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"60e822fb89e4e6159d0c6441","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60e823179efece4c32e2167f","hourlyRate":null,"costRate":null,"targetId":"60e822fb89e4e6159d0c6441","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60e8234d3160b67670bfef03","hourlyRate":null,"costRate":null,"targetId":"60e822fb89e4e6159d0c6441","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":true,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"client","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', '', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50'),
	(9, '611e60236bfccc0d09af6e60', '1PWR PROJECT MANAGEMENT', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"611e60236bfccc0d09af6e60","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafa73f917c56d38bf8c1f","hourlyRate":null,"costRate":null,"targetId":"611e60236bfccc0d09af6e60","membershipType":"WORKSPACE","membershipStatus":"INACTIVE"},{"userId":"611e61096bfccc0d09af75b1","hourlyRate":null,"costRate":null,"targetId":"611e60236bfccc0d09af6e60","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60ace02372b1b72df4887495","hourlyRate":null,"costRate":null,"targetId":"611e60236bfccc0d09af6e60","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61bb4c69fd9fb01600d0aecb","hourlyRate":null,"costRate":null,"targetId":"611e60236bfccc0d09af6e60","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aaf9e7b7cf503450a0b29b","hourlyRate":null,"costRate":null,"targetId":"611e60236bfccc0d09af6e60","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":true,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"client","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', '', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50'),
	(10, '61b7f346664ba9756b7de63f', '1PWR LINESMEN', '{"amount":0,"currency":"USD"}', '[{"userId":"609935adba9fdd7cafab3447","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b4b736e40d0045c47d90ec","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b74b71b7d87c745a76cb97","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b73f2a7353867cc2326975","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b74bb77353867cc232d2bb","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b750109580a3265cd8efcd","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b74baf7353867cc232d26e","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b75b07045d2778097e3f96","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b752027353867cc2330619","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b74e6864ba14188feb6275","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b7521a7353867cc233070e","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b74858045d2778097db0a8","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b75324045d2778097e0819","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b6ec1357df0302606650ed","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b748587353867cc232b6a8","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b75612664ba9756b72016f","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b75b54045d2778097e416d","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b74858045d2778097db0a5","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b742dc045d2778097d8050","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b752007353867cc23305f5","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b73f0c7353867cc2326896","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b757737353867cc2332eca","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"61b757c7b7d87c745a772d4e","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"},{"userId":"60aafad7b7cf503450a0b477","hourlyRate":null,"costRate":null,"targetId":"61b7f346664ba9756b7de63f","membershipType":"WORKSPACE","membershipStatus":"ACTIVE"}]', '{"timeRoundingInReports":false,"onlyAdminsSeeBillableRates":true,"onlyAdminsCreateProject":true,"onlyAdminsSeeDashboard":false,"defaultBillableProjects":true,"lockTimeEntries":null,"round":{"round":"Round to nearest","minutes":"15"},"projectFavorites":true,"canSeeTimeSheet":false,"canSeeTracker":true,"projectPickerSpecialFilter":false,"forceProjects":false,"forceTasks":false,"forceTags":false,"forceDescription":false,"onlyAdminsSeeAllTimeEntries":false,"onlyAdminsSeePublicProjectsEntries":false,"trackTimeDownToSecond":true,"projectGroupingLabel":"client","adminOnlyPages":[],"automaticLock":null,"onlyAdminsCreateTag":false,"onlyAdminsCreateTask":false,"timeTrackingMode":"DEFAULT","isProjectPublicByDefault":true}', '', NULL, '2022-01-04 07:29:50', '2022-01-04 07:29:50');
/*!40000 ALTER TABLE `workspaces` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
