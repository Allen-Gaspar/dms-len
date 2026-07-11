-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2026 at 07:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kiwi_dms`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action_type`, `description`, `timestamp`) VALUES
(359, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 06:43:08'),
(360, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 06:55:49'),
(361, 1, 'USER_CREATION', 'Approved request ID 2. Created account profile \'allen2\' with role \'casual\'', '2026-07-02 06:55:58'),
(362, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:30:47'),
(363, 1, 'USER_CREATION', 'Approved request ID 3. Created account profile \'len3\' with role \'casual\'', '2026-07-02 08:31:01'),
(364, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:36:14'),
(365, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:37:26'),
(366, 1, 'USER_CREATION', 'Approved request ID 5. Created account profile \'len5\' with role \'casual\'', '2026-07-02 08:37:32'),
(367, 1, 'USER_DELETE', 'Deleted user \'len5\'', '2026-07-02 08:38:30'),
(368, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:41:34'),
(369, 1, 'USER_CREATION', 'Approved request ID 6. Created account profile \'len6\' with role \'casual\'', '2026-07-02 08:41:41'),
(370, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:43:08'),
(371, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:46:05'),
(372, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:47:00'),
(373, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:49:43'),
(374, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 08:54:59'),
(375, 1, 'USER_CREATION', 'Approved request ID 9. Created account profile \'len9\' with role \'casual\'', '2026-07-02 08:55:03'),
(376, 33, 'LOGIN', 'User \'len9\' logged in', '2026-07-02 08:56:02'),
(377, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 09:14:11'),
(378, 1, 'USER_CREATION', 'Approved request ID 10. Created account profile \'patricia10\' with role \'casual\'', '2026-07-02 09:14:23'),
(379, 34, 'LOGIN', 'User \'patricia10\' logged in', '2026-07-02 09:15:15'),
(380, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 09:56:35'),
(381, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-02 09:56:42'),
(382, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-02 09:56:49'),
(383, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 09:56:57'),
(384, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 10:01:32'),
(385, 1, 'UPLOAD', 'Uploaded file asset \'filestac.png\' into location index scope.', '2026-07-02 10:51:27'),
(386, 1, 'HARD_DELETE', 'Admin hard-deleted \'filestac.png\'', '2026-07-02 10:51:44'),
(387, 1, 'HARD_DELETE', 'Admin hard-deleted \'filestac.png\'', '2026-07-02 10:51:46'),
(388, 1, 'HARD_DELETE', 'Admin hard-deleted \'filestac.png\'', '2026-07-02 10:51:48'),
(389, 1, 'HARD_DELETE', 'Admin hard-deleted \'filestac.png\'', '2026-07-02 10:51:50'),
(390, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-02 10:51:58'),
(391, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-02 10:52:09'),
(392, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-02 10:52:26'),
(393, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 10:52:35'),
(394, 1, 'FOLDER_CREATION', 'Created new workspace folder structure: Pictures', '2026-07-02 10:55:25'),
(395, 1, 'UPLOAD', 'Uploaded file asset \'imglogo.png\' into location index scope.', '2026-07-02 10:55:53'),
(396, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-02 10:57:29'),
(397, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-02 10:57:48'),
(398, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 10:58:04'),
(399, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 12:06:27'),
(400, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version 2', '2026-07-02 12:06:28'),
(401, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 12:11:44'),
(402, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version 3', '2026-07-02 12:11:46'),
(403, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 12:19:32'),
(404, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version 4', '2026-07-02 12:19:33'),
(405, 1, 'VERSION_BUMP', 'Committed file revision for \'imglogo.png\' — now version v5', '2026-07-02 12:25:57'),
(406, 1, 'ROLLBACK', 'Rolled back file ID 14 to historical Version v4', '2026-07-02 12:26:21'),
(407, 1, 'ROLLBACK', 'Rolled back file ID 14 to historical Version v4', '2026-07-02 12:26:34'),
(408, 1, 'VERSION_BUMP', 'Committed file revision for \'imglogo.png\' — now version v5', '2026-07-02 12:32:04'),
(409, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 12:32:16'),
(410, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 12:33:12'),
(411, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v6', '2026-07-02 12:33:13'),
(412, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 12:33:14'),
(413, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v7', '2026-07-02 12:33:16'),
(414, 1, 'VERSION_BUMP', 'Committed file revision for \'imglogo.png\' — now version v8', '2026-07-02 12:34:11'),
(415, 1, 'VERSION_BUMP', 'Committed file revision for \'imglogo.png\' — now version v9', '2026-07-02 13:11:41'),
(416, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:12:07'),
(417, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v10', '2026-07-02 13:12:08'),
(418, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:12:10'),
(419, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v11', '2026-07-02 13:12:11'),
(420, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:12:13'),
(421, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v12', '2026-07-02 13:12:14'),
(422, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:12:15'),
(423, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v13', '2026-07-02 13:12:16'),
(424, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:13:48'),
(425, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v14', '2026-07-02 13:13:49'),
(426, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:13:50'),
(427, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v15', '2026-07-02 13:13:53'),
(428, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:13:54'),
(429, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v16', '2026-07-02 13:13:54'),
(430, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:13:56'),
(431, 1, 'CHECKIN', 'Checked in \'imglogo.png\' — now version v17', '2026-07-02 13:13:57'),
(432, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:15:17'),
(433, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-02 13:15:18'),
(434, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:15:19'),
(435, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-02 13:15:19'),
(436, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:15:25'),
(437, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-02 13:15:26'),
(438, 1, 'DOWNLOAD', 'Downloaded \'imglogo.png\'', '2026-07-02 13:15:37'),
(439, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:34:32'),
(440, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-02 13:34:34'),
(441, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:34:47'),
(442, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-02 13:34:48'),
(443, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:34:58'),
(444, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-02 13:35:00'),
(445, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:35:00'),
(446, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-02 13:35:02'),
(447, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-02 13:37:19'),
(448, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-02 13:37:20'),
(449, 1, 'CHECKOUT', 'Checked out \'filestac.png\'', '2026-07-02 13:37:26'),
(450, 1, 'CHECKIN_CANCEL', 'Checkin \'filestac.png\' released—No changes detected.', '2026-07-02 13:37:27'),
(451, 1, 'VERSION_BUMP', 'Committed file revision for \'filestac.png\' — now version v2', '2026-07-02 13:38:05'),
(452, 1, 'DOWNLOAD', 'Downloaded \'filestac.png\'', '2026-07-02 13:39:10'),
(453, 1, 'UPLOAD', 'Uploaded file asset \'ss1.PNG\' into location index scope.', '2026-07-02 13:44:33'),
(454, 1, 'FOLDER_CREATION', 'Created new workspace folder structure: PPT', '2026-07-02 13:57:15'),
(455, 1, 'UPLOAD', 'Uploaded file asset \'Document Management System.pptx\' into location index scope.', '2026-07-02 13:57:35'),
(456, 1, 'FOLDER_RENAME', 'Renamed folder ID 1 to \'Picturess\'', '2026-07-02 14:03:56'),
(457, 1, 'UPLOAD', 'Uploaded file \'04ea6d1c-de13-412a-a3ce-8413dcd0f270.jpg\' directly into folder ID 1', '2026-07-02 14:06:16'),
(458, 1, 'CHECKIN_CANCEL', 'Checkin \'ss1.PNG\' released—No changes detected.', '2026-07-02 14:07:07'),
(459, 1, 'DOWNLOAD', 'Downloaded \'04ea6d1c-de13-412a-a3ce-8413dcd0f270.jpg\'', '2026-07-02 14:10:32'),
(460, 1, 'HARD_DELETE', 'Admin hard-deleted \'04ea6d1c-de13-412a-a3ce-8413dcd0f270.jpg\'', '2026-07-02 14:10:39'),
(461, 1, 'UPLOAD', 'Uploaded file \'WIN_20250325_10_55_01_Pro.jpg\' directly into folder ID 1', '2026-07-02 14:14:03'),
(462, 1, 'DOWNLOAD', 'Downloaded \'WIN_20250325_10_55_01_Pro.jpg\'', '2026-07-02 14:14:11'),
(463, 1, 'HARD_DELETE', 'Admin hard-deleted \'ss1.PNG\'', '2026-07-02 14:14:21'),
(464, 1, 'DOWNLOAD', 'Downloaded \'WIN_20250325_10_55_01_Pro.jpg\'', '2026-07-02 14:45:51'),
(465, 1, 'HARD_DELETE', 'Admin hard-deleted \'WIN_20250325_10_55_01_Pro.jpg\'', '2026-07-02 14:45:56'),
(466, 1, 'UPLOAD', 'Uploaded file \'WIN_20260506_12_13_29_Pro.jpg\' directly into folder ID 1', '2026-07-02 14:46:17'),
(467, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 17:00:28'),
(468, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-02 17:01:22'),
(469, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-02 17:01:36'),
(470, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-02 17:01:49'),
(471, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 04:00:56'),
(472, 1, 'DOWNLOAD', 'Downloaded \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 04:17:47'),
(473, 1, 'CHECKIN_CANCEL', 'Checkin \'WIN_20260506_12_13_29_Pro.jpg\' released—No changes detected.', '2026-07-03 04:17:47'),
(474, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 04:18:42'),
(475, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-03 04:18:50'),
(476, 1, 'DOWNLOAD', 'Downloaded \'imglogo.png\'', '2026-07-03 04:18:58'),
(477, 1, 'HARD_DELETE', 'Admin hard-deleted \'1.png\'', '2026-07-03 04:32:03'),
(478, 1, 'DOWNLOAD', 'Downloaded \'imglogo.png\'', '2026-07-03 04:32:47'),
(479, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 04:32:52'),
(480, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-03 04:32:57'),
(481, 1, 'CHECKIN_CANCEL', 'Checkin \'Document Management System.pptx\' released—No changes detected.', '2026-07-03 04:34:55'),
(482, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 04:37:00'),
(483, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-03 04:40:21'),
(484, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 04:41:54'),
(485, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-03 04:47:58'),
(486, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 04:52:29'),
(487, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-03 04:52:31'),
(488, 1, 'DOWNLOAD', 'Downloaded \'imglogo.png\'', '2026-07-03 04:56:18'),
(489, 1, 'SHARE', 'Shared \'imglogo.png\' with user #3', '2026-07-03 05:07:48'),
(490, 1, 'UNSHARE', 'Removed share of \'imglogo.png\' from user #3', '2026-07-03 05:07:52'),
(491, 1, 'SHARE', 'Shared \'imglogo.png\' with user #33', '2026-07-03 05:07:56'),
(492, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 05:09:21'),
(493, 2, 'DOWNLOAD', 'Downloaded \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 05:09:33'),
(494, 2, 'CHECKOUT', 'Checked out \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 05:09:36'),
(495, 2, 'CHECKIN_CANCEL', 'Checkin \'WIN_20260506_12_13_29_Pro.jpg\' released—No changes detected.', '2026-07-03 05:09:38'),
(496, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 05:17:53'),
(497, 1, 'DOWNLOAD', 'Downloaded \'imglogo.png\'', '2026-07-03 05:18:13'),
(498, 1, 'SHARE', 'Shared \'imglogo.png\' with user #3', '2026-07-03 05:25:49'),
(499, 1, 'DOWNLOAD', 'Downloaded \'imglogo.png\'', '2026-07-03 05:25:52'),
(500, 1, 'DOWNLOAD', 'Downloaded \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 05:28:56'),
(501, 1, 'CHECKOUT', 'Checked out \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 05:28:58'),
(502, 1, 'CHECKIN_CANCEL', 'Checkin \'WIN_20260506_12_13_29_Pro.jpg\' released—No changes detected.', '2026-07-03 05:28:59'),
(503, 1, 'DOWNLOAD', 'Downloaded \'imglogo.png\'', '2026-07-03 05:29:36'),
(504, 1, 'DOWNLOAD', 'Downloaded \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 05:34:27'),
(505, 1, 'SHARE', 'Shared \'WIN_20260506_12_13_29_Pro.jpg\' with user #33', '2026-07-03 05:43:12'),
(506, 1, 'SHARE', 'Shared \'WIN_20260506_12_13_29_Pro.jpg\' with user #3', '2026-07-03 05:43:16'),
(507, 1, 'SHARE', 'Shared \'Document Management System.pptx\' with user #4', '2026-07-03 05:50:06'),
(508, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 05:50:11'),
(509, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-03 05:53:19'),
(510, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 05:53:22'),
(511, 1, 'CHECKIN_CANCEL', 'Checkin \'imglogo.png\' released—No changes detected.', '2026-07-03 05:53:23'),
(512, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 05:53:24'),
(513, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 06:19:55'),
(514, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 06:31:09'),
(515, 1, 'DOWNLOAD', 'Downloaded \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 06:38:31'),
(516, 1, 'CHECKOUT', 'Checked out \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 06:38:32'),
(517, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'WIN_20260506_12_13_29_Pro.jpg\' via access hierarchy rule.', '2026-07-03 06:38:34'),
(518, 1, 'HARD_DELETE', 'Admin hard-deleted \'filestac.png\'', '2026-07-03 06:38:38'),
(519, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 06:38:44'),
(520, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 06:38:57'),
(521, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 07:08:19'),
(522, 2, 'FOLDER_CREATION', 'Created new (private) workspace folder structure: Private Folder', '2026-07-03 07:31:38'),
(523, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 07:33:48'),
(524, 3, 'LOGOUT', 'User \'cha\' auto-logged out by visiting login page', '2026-07-03 07:34:00'),
(525, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 07:34:26'),
(526, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 07:35:09'),
(527, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 07:35:32'),
(528, 3, 'LOGOUT', 'User \'cha\' auto-logged out by visiting login page', '2026-07-03 07:48:59'),
(529, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 07:49:08'),
(530, 1, 'DOWNLOAD', 'Downloaded \'Document Management System.pptx\'', '2026-07-03 07:49:15'),
(531, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 07:50:15'),
(532, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 07:50:47'),
(533, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 08:18:33'),
(534, 1, 'CHECKOUT', 'Checked out \'image_9d5ad14c (1).png\'', '2026-07-03 08:18:44'),
(535, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 08:29:16'),
(536, 2, 'UPLOAD', 'Uploaded file asset \'[틴즈엘teensEL] Turn It Up- Planetshakers(360P).mp4\' into location index scope.', '2026-07-03 08:32:57'),
(537, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 08:35:18'),
(538, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 08:35:29'),
(539, 1, 'CHECKOUT', 'Checked out \'[틴즈엘teensEL] Turn It Up- Planetshakers(360P).mp4\'', '2026-07-03 08:36:16'),
(540, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'[틴즈엘teensEL] Turn It Up- Planetshakers(360P).mp4\' via access hierarchy rule.', '2026-07-03 08:36:17'),
(541, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 08:36:30'),
(542, 2, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'[틴즈엘teensEL] Turn It Up- Planetshakers(360P).mp4\' via access hierarchy rule.', '2026-07-03 08:39:36'),
(543, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 08:42:29'),
(544, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 08:51:45'),
(545, 2, 'SOFT_DELETE', 'Soft-deleted \'ss1.PNG\'', '2026-07-03 08:51:50'),
(546, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 08:51:55'),
(547, 1, 'PERMISSION_CHANGE', 'Updated access authorization matrix for User ID 3 on Document ID 14', '2026-07-03 09:02:01'),
(548, 1, 'VERSION_BUMP', 'Committed file revision for \'image_9d5ad14c.png\' — now version v3', '2026-07-03 09:03:23'),
(549, 1, 'VERSION_BUMP', 'Committed file revision for \'[틴즈엘teensEL] Turn It Up- Planetshakers(360P).mp4\' — now version v2', '2026-07-03 09:06:33'),
(550, 1, 'VERSION_BUMP', 'Committed file revision for \'WIN_20260506_12_13_29_Pro.jpg\' — now version v2', '2026-07-03 09:06:58'),
(551, 1, 'ROLLBACK', 'Rolled back file ID 19 to historical Version v1', '2026-07-03 09:07:24'),
(552, 1, 'ROLLBACK', 'Rolled back file ID 19 to historical Version v1', '2026-07-03 09:07:34'),
(553, 1, 'ROLLBACK', 'Rolled back file ID 19 to historical Version v1', '2026-07-03 09:07:42'),
(554, 1, 'ROLLBACK', 'Rolled back file ID 19 to historical Version v1', '2026-07-03 09:07:49'),
(555, 1, 'VERSION_BUMP', 'Committed file revision for \'WIN_20260506_12_13_29_Pro.jpg\' — now version v2', '2026-07-03 09:08:00'),
(556, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 09:10:55'),
(557, 1, 'USER_CREATION', 'Approved request ID 11. Created account profile \'patty11\' with role \'casual\'', '2026-07-03 09:11:27'),
(558, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 09:15:58'),
(559, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 09:21:51'),
(560, 1, 'USER_CREATION', 'Approved request ID 13. Created account profile \'try 113\' with role \'contributor\'', '2026-07-03 09:22:08'),
(561, 36, 'LOGIN', 'User \'try 113\' logged in', '2026-07-03 09:22:37'),
(562, 36, 'LOGIN', 'User \'try 113\' logged in', '2026-07-03 09:23:39'),
(563, 36, 'SOFT_DELETE', 'Soft-deleted \'image_9d5ad14c (1).png\'', '2026-07-03 09:23:47'),
(564, 36, 'SOFT_DELETE', 'Soft-deleted \'WIN_20260506_12_13_29_Pro.jpg\'', '2026-07-03 09:23:51'),
(565, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 09:28:20'),
(566, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 09:34:08'),
(567, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 09:34:59'),
(568, 2, 'DOWNLOAD', 'Downloaded \'DFD of DMS.png\'', '2026-07-03 09:44:56'),
(569, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 09:50:56'),
(570, 1, 'VERSION_BUMP', 'Committed file revision for \'Document Management System.pptx\' — now version v2', '2026-07-03 09:58:49'),
(571, 1, 'ROLLBACK', 'Rolled back file ID 4 to historical Version v2', '2026-07-03 09:59:20'),
(572, 1, 'VERSION_BUMP', 'Committed file revision for \'Document Management System.pptx\' — now version v3', '2026-07-03 09:59:34'),
(573, 1, 'DOWNLOAD', 'Downloaded \'Document Management System.pptx\'', '2026-07-03 09:59:43'),
(574, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-03 10:04:57'),
(575, 1, 'CHECKOUT', 'Checked out \'DFD of DMS.png\'', '2026-07-03 10:05:01'),
(576, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 10:06:09'),
(577, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 10:06:32'),
(578, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 10:06:55'),
(579, 3, 'LOGOUT', 'User \'cha\' auto-logged out by visiting login page', '2026-07-03 10:07:16'),
(580, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 10:16:17'),
(581, 2, 'LOGOUT', 'User \'pat\' auto-logged out by visiting login page', '2026-07-03 10:16:49'),
(582, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 10:30:26'),
(583, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 10:32:00'),
(584, 2, 'DOWNLOAD', 'Downloaded \'Document Management System.pptx\'', '2026-07-03 10:32:44'),
(585, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 10:34:39'),
(586, 1, 'VERSION_BUMP', 'Committed file revision for \'imglogo.png\' — now version v18', '2026-07-03 10:43:11'),
(587, 1, 'DOWNLOAD', 'Downloaded \'Document Management System.pptx\'', '2026-07-03 11:12:36'),
(588, 1, 'DOWNLOAD', 'Downloaded \'[틴즈엘teensEL] Turn It Up- Planetshakers(360P).mp4\'', '2026-07-03 11:13:20'),
(589, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 11:18:40'),
(590, 1, 'DOWNLOAD', 'Downloaded \'DFD of DMS.png\'', '2026-07-03 11:24:50'),
(591, 1, 'UPLOAD', 'Uploaded file asset \'Untitled designhhhh.pdf\' into location index scope.', '2026-07-03 11:25:50'),
(592, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 11:26:45'),
(593, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 11:27:01'),
(594, 1, 'SHARE', 'Shared \'Untitled designhhhh.pdf\' with user #3', '2026-07-03 11:27:21'),
(595, 1, 'CHECKOUT', 'Checked out \'Untitled designhhhh.pdf\'', '2026-07-03 11:29:19'),
(596, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 11:29:25'),
(597, 3, 'LOGOUT', 'User \'cha\' auto-logged out by visiting login page', '2026-07-03 11:29:30'),
(598, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 11:29:43'),
(599, 3, 'LOGOUT', 'User \'cha\' auto-logged out by visiting login page', '2026-07-03 11:29:46'),
(600, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 11:29:50'),
(601, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 11:30:01'),
(602, 3, 'DOWNLOAD', 'Downloaded \'Untitled designhhhh.pdf\'', '2026-07-03 11:30:04'),
(603, 3, 'LOGOUT', 'User \'cha\' auto-logged out by visiting login page', '2026-07-03 11:30:29'),
(604, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-03 11:31:55'),
(605, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 13:34:40'),
(606, 1, 'UPLOAD', 'Uploaded file asset \'db.php\' into location index scope.', '2026-07-03 14:57:38'),
(607, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 15:49:31'),
(608, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-03 16:02:56'),
(609, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-03 16:03:05'),
(610, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 08:37:51'),
(611, 1, 'CHECKOUT', 'Checked out \'db.php\'', '2026-07-06 08:51:57'),
(612, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'db.php\' via access hierarchy rule.', '2026-07-06 08:51:58'),
(613, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 09:18:42'),
(614, 1, 'LOGOUT', 'User \'admin\' auto-logged out by visiting login page', '2026-07-06 09:19:08'),
(615, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 09:19:46'),
(616, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 09:25:07'),
(617, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 09:25:47'),
(618, 1, 'LOGOUT', 'User \'admin\' auto-logged out by visiting login page', '2026-07-06 09:26:26'),
(619, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 09:26:31'),
(620, 1, 'LOGOUT', 'User \'admin\' auto-logged out by visiting login page', '2026-07-06 09:26:37'),
(621, 9, 'PASSWORD_RESET_REQUEST', 'Password reset requested', '2026-07-06 09:26:48'),
(622, 1, 'LOGOUT', 'User \'admin\' auto-logged out by visiting login page', '2026-07-06 09:29:27'),
(623, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 09:29:35'),
(624, 1, 'USER_TRASH', 'Moved user \'patricia10\' to trash', '2026-07-06 09:31:13'),
(625, 1, 'PROFILE_UPDATE', 'Updated account profile', '2026-07-06 09:32:47'),
(626, 1, 'DOWNLOAD', 'Downloaded \'db.php\'', '2026-07-06 09:33:10'),
(627, 1, 'CHECKOUT', 'Checked out \'db.php\'', '2026-07-06 09:33:13'),
(628, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'db.php\' via access hierarchy rule.', '2026-07-06 09:33:15'),
(629, 1, 'CHECKOUT', 'Checked out \'db.php\'', '2026-07-06 09:33:21'),
(630, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'db.php\' via access hierarchy rule.', '2026-07-06 09:33:23'),
(631, 1, 'LOGOUT', 'User \'admin\' auto-logged out by visiting login page', '2026-07-06 09:55:07'),
(632, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 09:55:25'),
(633, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 10:55:18'),
(634, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 11:02:59'),
(635, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 11:10:20'),
(636, 1, 'BRAND_UPDATE', 'Updated brand to \'FILESTAC Allen\'', '2026-07-06 11:30:07'),
(637, 1, 'BRAND_UPDATE', 'Updated brand to \'FILESTAC Allen\'', '2026-07-06 11:31:36'),
(638, 1, 'BRAND_UPDATE', 'Updated brand to \'FILESTAC Pat\'', '2026-07-06 11:58:44'),
(639, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-06 11:59:41'),
(640, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-06 11:59:47'),
(641, 2, 'PROFILE_UPDATE', 'Updated account profile', '2026-07-06 12:00:01'),
(642, 2, 'LOGOUT', 'User \'pat\' logged out', '2026-07-06 12:00:53'),
(643, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-06 12:01:02'),
(644, 3, 'PROFILE_UPDATE', 'Updated account profile', '2026-07-06 12:01:26'),
(645, 3, 'LOGOUT', 'User \'cha\' logged out', '2026-07-06 12:01:56'),
(646, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 12:02:06'),
(647, 1, 'USER_CREATION', 'Approved request ID 12. Created account profile \'charizma12\' with role \'casual\'', '2026-07-06 14:53:29'),
(648, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-06 14:54:56'),
(649, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-06 14:55:02'),
(650, 3, 'LOGOUT', 'User \'cha\' logged out', '2026-07-06 14:55:16'),
(651, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 14:55:22'),
(652, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-06 15:16:04'),
(653, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-06 15:16:08'),
(654, 3, 'LOGOUT', 'User \'cha\' logged out', '2026-07-06 15:16:51'),
(655, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-06 15:16:57'),
(656, 2, 'LOGOUT', 'User \'pat\' logged out', '2026-07-06 15:51:01'),
(657, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 15:51:16'),
(658, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 08:19:34'),
(659, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 08:21:28'),
(660, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-07 08:21:34'),
(661, 2, 'LOGOUT', 'User \'pat\' logged out', '2026-07-07 08:25:27'),
(662, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 08:26:20'),
(663, 1, 'SOFT_DELETE', 'Moved \'students.csv\' to trash', '2026-07-07 08:46:17'),
(664, 1, 'SOFT_DELETE', 'Moved \'students.csv\' to trash', '2026-07-07 08:46:20'),
(665, 1, 'SOFT_DELETE', 'Moved \'students.csv\' to trash', '2026-07-07 08:54:42'),
(666, 1, 'VERSION_BUMP', 'Committed file revision for \'imglogo.png\' — now version v19', '2026-07-07 08:56:08'),
(667, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 09:34:14'),
(668, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 09:37:12'),
(669, 1, 'UPLOAD', 'Uploaded \'2359c3f8-611d-40c4-88ab-49d4695dfd5f.5b26b18ccda9066176d9e3346f969843.webp\'', '2026-07-07 09:39:52'),
(670, 1, 'UPLOAD', 'Uploaded \'61ZB1f7oODL.jpg\'', '2026-07-07 09:39:52'),
(671, 1, 'UPLOAD', 'Uploaded \'71O0zctwPiL._AC_SL1200_.jpg\'', '2026-07-07 09:39:52'),
(672, 1, 'UPLOAD', 'Uploaded \'AT-ATR4750.jpg\'', '2026-07-07 09:39:52'),
(673, 1, 'UPLOAD', 'Uploaded \'Best-compact-gaming-keyboard-runner-up-Wooting-One-Analogue-Keyboard.webp\'', '2026-07-07 09:39:52'),
(674, 1, 'UPLOAD', 'Uploaded \'Digitizer-tablet-The-WACOM-Specifications-are-listed-below-456-BULLET-Active-Area.png\'', '2026-07-07 09:39:52'),
(675, 1, 'UPLOAD', 'Uploaded \'Epos_Now_2D_USB_Barcode_Scanner_-_Including_stand.jpg\'', '2026-07-07 09:39:52'),
(676, 1, 'UPLOAD', 'Uploaded \'generic-headphones.jpg\'', '2026-07-07 09:39:52'),
(677, 1, 'UPLOAD', 'Uploaded \'hp_hph200_h200_wired_stereo_gaming_1599010.jpg\'', '2026-07-07 09:39:52'),
(678, 1, 'UPLOAD', 'Uploaded \'IPF5100-2_1000x1000.jpg\'', '2026-07-07 09:39:52'),
(679, 1, 'UPLOAD', 'Uploaded \'light-pen.jpg\'', '2026-07-07 09:39:52'),
(680, 1, 'UPLOAD', 'Uploaded \'logitech-webcam-v-uas14.jpg\'', '2026-07-07 09:39:52'),
(681, 1, 'UPLOAD', 'Uploaded \'monitor.jpg\'', '2026-07-07 09:39:52'),
(682, 1, 'UPLOAD', 'Uploaded \'MSI-GTX960-2G-2.webp\'', '2026-07-07 09:39:52'),
(683, 1, 'UPLOAD', 'Uploaded \'navigation-global-positioning-system-gps-device-navigat-navigation-global-positioning-system-gps-device-117441831.webp\'', '2026-07-07 09:39:52'),
(684, 1, 'UPLOAD', 'Uploaded \'orbit-reader20-prime-braille-solutions.jpg\'', '2026-07-07 09:39:52'),
(685, 1, 'UPLOAD', 'Uploaded \'SvzYpM3ZgkrWQNvWfyMU7g-1600-80.jpg\'', '2026-07-07 09:39:52'),
(686, 1, 'UPLOAD', 'Uploaded \'WF-2010W.jpg\'', '2026-07-07 09:39:52'),
(687, 1, 'UPLOAD', 'Uploaded \'xsc.png\'', '2026-07-07 09:39:52'),
(688, 1, 'UPLOAD', 'Uploaded \'1.JPG\'', '2026-07-07 09:39:52'),
(689, 1, 'DOWNLOAD', 'Downloaded \'2359c3f8-611d-40c4-88ab-49d4695dfd5f.5b26b18ccda9066176d9e3346f969843.webp\'', '2026-07-07 09:40:17'),
(690, 1, 'PROFILE_UPDATE', 'Updated account profile', '2026-07-07 09:41:56'),
(691, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 10:37:01'),
(692, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-07 10:37:05'),
(693, 2, 'LOGOUT', 'User \'pat\' logged out', '2026-07-07 10:37:15'),
(694, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 10:37:18'),
(695, 1, 'BRAND_UPDATE', 'Updated brand to \'FILESTAC Pat\'', '2026-07-07 10:59:17'),
(696, 1, 'PROFILE_UPDATE', 'Updated account profile', '2026-07-07 10:59:23'),
(697, 1, 'CHECKOUT', 'Checked out \'2359c3f8-611d-40c4-88ab-49d4695dfd5f.5b26b18ccda9066176d9e3346f969843.webp\'', '2026-07-07 11:01:21'),
(698, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'2359c3f8-611d-40c4-88ab-49d4695dfd5f.5b26b18ccda9066176d9e3346f969843.webp\' via access hierarchy rule.', '2026-07-07 11:01:25'),
(699, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 11:25:44'),
(700, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 11:26:40'),
(701, 1, 'USER_CREATION', 'Approved request ID 14. Created account profile \'prince14567\' with role \'casual\'', '2026-07-07 11:27:14'),
(702, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 11:27:27'),
(703, 38, 'LOGIN', 'User \'prince14567\' logged in', '2026-07-07 11:28:04'),
(704, 38, 'PROFILE_UPDATE', 'Updated account profile', '2026-07-07 11:28:25'),
(705, 38, 'LOGOUT', 'User \'Prince Ace\' logged out', '2026-07-07 11:51:26'),
(706, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 11:51:31'),
(707, 1, 'UPLOAD', 'Uploaded \'14.09.2025_15.35.37_REC.png\'', '2026-07-07 13:08:18'),
(708, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 13:09:06'),
(709, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 13:37:19'),
(710, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 14:50:56'),
(711, 38, 'LOGIN', 'User \'Prince Ace\' logged in', '2026-07-07 14:51:02'),
(712, 38, 'LOGOUT', 'User \'Prince Ace\' logged out', '2026-07-07 14:51:14'),
(713, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 14:51:22'),
(714, 1, 'CHECKOUT', 'Checked out \'imglogo.png\'', '2026-07-07 14:59:59'),
(715, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'imglogo.png\' via access hierarchy rule.', '2026-07-07 15:00:00'),
(716, 1, 'DOWNLOAD', 'Downloaded \'imglogo.png\'', '2026-07-07 15:00:10'),
(717, 1, 'RENAME_FILE', 'Renamed \'14.09.2025_15.35.37_REC.png\' to \'14.09.2025_15.35.37_RECREC.png\'', '2026-07-07 15:16:33'),
(718, 1, 'DOWNLOAD', 'Downloaded \'14.09.2025_15.35.37_RECREC.png\'', '2026-07-07 15:17:03'),
(719, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'14.09.2025_15.35.37_RECREC.png\' via access hierarchy rule.', '2026-07-07 15:17:08'),
(720, 1, 'UPLOAD', 'Uploaded \'1.JPG\'', '2026-07-07 15:26:18'),
(721, 1, 'DOWNLOAD', 'Downloaded \'1.JPG\'', '2026-07-07 15:26:39'),
(722, 1, 'CHECKOUT', 'Checked out \'1.JPG\'', '2026-07-07 15:26:45'),
(723, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'1.JPG\' via access hierarchy rule.', '2026-07-07 15:26:46'),
(724, 1, 'SOFT_DELETE', 'Moved \'14.09.2025_15.35.37_RECREC.png\' to trash', '2026-07-07 15:26:49'),
(725, 1, 'SOFT_DELETE', 'Moved \'2359c3f8-611d-40c4-88ab-49d4695dfd5f.5b26b18ccda9066176d9e3346f969843.webp\' to trash', '2026-07-07 15:27:43'),
(726, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 15:35:23'),
(727, 38, 'PASSWORD_RESET_REQUEST', 'Password reset requested', '2026-07-07 15:36:56'),
(728, 33, 'PASSWORD_RESET_REQUEST', 'Password reset requested', '2026-07-07 15:38:32'),
(729, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-07 15:40:12'),
(730, 2, 'UPLOAD', 'Uploaded \'5501574c-0853-4abe-9435-d339e244f924.jpg\'', '2026-07-07 15:40:30'),
(731, 2, 'LOGOUT', 'User \'pat\' logged out', '2026-07-07 15:59:23'),
(732, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-07 15:59:28'),
(733, 3, 'LOGOUT', 'User \'cha\' logged out', '2026-07-07 15:59:33'),
(734, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 15:59:37'),
(735, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 16:34:31'),
(736, 1, 'DOWNLOAD', 'Downloaded \'5501574c-0853-4abe-9435-d339e244f924.jpg\'', '2026-07-07 16:35:23'),
(737, 1, 'DOWNLOAD', 'Downloaded \'5501574c-0853-4abe-9435-d339e244f924.jpg\'', '2026-07-07 16:35:28'),
(738, 1, 'CHECKOUT', 'Checked out \'5501574c-0853-4abe-9435-d339e244f924.jpg\'', '2026-07-07 16:35:47'),
(739, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'5501574c-0853-4abe-9435-d339e244f924.jpg\' via access hierarchy rule.', '2026-07-07 16:35:48'),
(740, 1, 'DOWNLOAD', 'Downloaded \'5501574c-0853-4abe-9435-d339e244f924.jpg\'', '2026-07-07 16:35:50'),
(741, 1, 'DOWNLOAD', 'Downloaded \'71O0zctwPiL._AC_SL1200_.jpg\'', '2026-07-07 16:35:52'),
(742, 1, 'SOFT_DELETE', 'Moved \'Digitizer-tablet-The-WACOM-Specifications-are-listed-below-456-BULLET-Active-Area.png\' to trash', '2026-07-07 16:37:20'),
(743, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 16:42:12'),
(744, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-07 16:42:17'),
(745, 2, 'LOGOUT', 'User \'pat\' logged out', '2026-07-07 16:42:22'),
(746, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-07 16:42:25'),
(747, 3, 'LOGOUT', 'User \'cha\' logged out', '2026-07-07 16:42:37'),
(748, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 16:42:42'),
(749, 1, 'SOFT_DELETE', 'Moved \'Best-compact-gaming-keyboard-runner-up-Wooting-One-Analogue-Keyboard.webp\' to trash', '2026-07-07 16:50:29'),
(750, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-07 16:50:48'),
(751, 33, 'PASSWORD_RESET_REQUEST', 'Password reset requested', '2026-07-07 16:50:56'),
(752, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-07 16:54:09'),
(753, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-08 08:17:11'),
(754, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-08 08:17:27'),
(755, 1, 'PERMISSIONS', 'Updated permissions for user #1', '2026-07-08 08:19:16'),
(756, 1, 'USER_CREATE', 'Created user \'try1\'', '2026-07-08 08:20:16'),
(757, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-08 08:33:20'),
(758, 33, 'PASSWORD_RESET_REQUEST', 'Password reset requested', '2026-07-08 08:33:28'),
(759, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-08 08:43:04'),
(760, 3, 'LOGOUT', 'User \'cha\' auto-logged out by visiting login page', '2026-07-08 08:43:10'),
(761, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-08 08:43:47'),
(762, 3, 'LOGOUT', 'User \'cha\' logged out', '2026-07-08 08:44:03'),
(763, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-08 08:44:07'),
(764, 2, 'LOGOUT', 'User \'pat\' logged out', '2026-07-08 08:44:33'),
(765, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-08 08:44:37'),
(766, 1, 'UPLOAD', 'Uploaded \'new.py\'', '2026-07-08 08:46:34'),
(767, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-08 08:54:07'),
(768, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-08 08:54:43'),
(769, 1, 'UPLOAD', 'Uploaded \'allen.py\'', '2026-07-08 09:00:16'),
(770, 1, 'USER_CREATION', 'Approved request ID 15. Created account profile \'test1\' with role \'casual\'', '2026-07-08 09:05:06'),
(771, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-08 09:05:22'),
(772, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-08 09:05:49'),
(773, 1, 'SOFT_DELETE', 'Moved \'Epos_Now_2D_USB_Barcode_Scanner_-_Including_stand.jpg\' to trash', '2026-07-08 09:08:03'),
(774, 1, 'SOFT_DELETE', 'Moved \'generic-headphones.jpg\' to trash', '2026-07-08 09:08:29'),
(775, 1, 'DOWNLOAD', 'Downloaded \'IPF5100-2_1000x1000.jpg\'', '2026-07-08 09:11:10'),
(776, 1, 'DOWNLOAD', 'Downloaded \'navigation-global-positioning-system-gps-device-navigat-navigation-global-positioning-system-gps-device-117441831.webp\'', '2026-07-08 09:11:17'),
(777, 1, 'DOWNLOAD', 'Downloaded \'1.JPG\'', '2026-07-08 09:11:25'),
(778, 1, 'DOWNLOAD', 'Downloaded \'allen.py\'', '2026-07-08 09:12:03'),
(779, 1, 'FOLDER_CREATION', 'Created folder: also folder', '2026-07-08 09:36:34'),
(780, 1, 'UPLOAD', 'Uploaded \'RobloxScreenShot20250706_152042024.png\'', '2026-07-08 09:39:13'),
(781, 1, 'UPLOAD', 'Uploaded \'RobloxScreenShot20260427_193052504.png\'', '2026-07-08 09:39:13'),
(782, 1, 'FOLDER_CREATION', 'Created folder: len 2f', '2026-07-08 09:59:32'),
(783, 1, 'PERMISSIONS', 'Updated permissions for user #1', '2026-07-08 10:46:05'),
(784, 1, 'DOWNLOAD', 'Downloaded \'RobloxScreenShot20250706_152042024.png\'', '2026-07-08 10:46:19'),
(785, 1, 'UPLOAD', 'Uploaded \'RobloxScreenShot20260427_193052504.png\'', '2026-07-08 11:04:24'),
(786, 1, 'FOLDER_CREATION', 'Created folder: new f', '2026-07-08 11:04:46'),
(787, 1, 'CHECKOUT', 'Checked out \'1.JPG\'', '2026-07-08 11:05:58'),
(788, 1, 'VERSION_BUMP', 'Committed file revision for \'RobloxScreenShot20260427_193052504.png\' — now version v2', '2026-07-08 11:08:07'),
(789, 1, 'UPLOAD', 'Uploaded \'DFD of DMS (2).png\'', '2026-07-08 11:10:16'),
(790, 1, 'UPLOAD', 'Uploaded \'rename_file.php\'', '2026-07-08 11:41:00'),
(791, 1, 'UPLOAD', 'Uploaded \'contact_us_form.php\'', '2026-07-08 11:47:36'),
(792, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-08 11:52:18'),
(793, 2, 'LOGIN', 'User \'pat\' logged in', '2026-07-08 11:52:53'),
(794, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-08 11:52:59'),
(795, 1, 'UPLOAD', 'Uploaded \'trash.php\'', '2026-07-08 11:53:11'),
(796, 1, 'FOLDER_CREATION', 'Created folder: inside folder folder', '2026-07-08 13:30:39'),
(797, 1, 'CHECKOUT', 'Checked out \'trash.php\'', '2026-07-08 13:38:00'),
(798, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'trash.php\' via access hierarchy rule.', '2026-07-08 13:38:46'),
(799, 1, 'CHECKOUT', 'Checked out \'trash.php\'', '2026-07-08 13:38:53'),
(800, 1, 'CHECKOUT', 'Checked out \'trash.php\'', '2026-07-08 13:39:35'),
(801, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'trash.php\' via access hierarchy rule.', '2026-07-08 13:40:20'),
(802, 1, 'RENAME_FILE', 'Renamed \'contact_us_form.php\' to \'contact_us_formm.php\'', '2026-07-08 13:40:49'),
(803, 1, 'RENAME_FILE', 'Renamed \'trash.php\' to \'trashh.php\'', '2026-07-08 13:47:34'),
(804, 1, 'RENAME_FILE', 'Renamed \'trashh.php\' to \'trashhh.php\'', '2026-07-08 13:51:31'),
(805, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'trashhh.php\' via access hierarchy rule.', '2026-07-08 13:51:44'),
(806, 2, 'UPLOAD', 'Uploaded \'Document Management System (3).pptx\'', '2026-07-08 14:10:40'),
(807, 1, 'RENAME_FILE', 'Renamed \'Document Management System (3).pptx\' to \'Document Management System (3o).pptx\'', '2026-07-08 14:33:32'),
(808, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'Document Management System (3o).pptx\' via access hierarchy rule.', '2026-07-08 14:33:36'),
(809, 1, 'CHECKOUT', 'Checked out \'Document Management System (3o).pptx\'', '2026-07-08 14:35:07'),
(810, 1, 'CHECKOUT', 'Checked out \'Document Management System (3o).pptx\'', '2026-07-08 14:35:12'),
(811, 1, 'CHECKOUT', 'Checked out \'Document Management System (3o).pptx\'', '2026-07-08 14:35:36'),
(812, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'Document Management System (3o).pptx\' via access hierarchy rule.', '2026-07-08 14:36:37'),
(813, 1, 'UPLOAD', 'Uploaded \'documents.php\'', '2026-07-08 14:42:46'),
(814, 1, 'UPLOAD', 'Uploaded \'folders.php\'', '2026-07-08 14:42:46'),
(815, 1, 'CHECKOUT', 'Checked out \'documents.php\'', '2026-07-08 14:58:26'),
(816, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'documents.php\' via access hierarchy rule.', '2026-07-08 15:11:21'),
(817, 1, 'RENAME_FILE', 'Renamed \'documents.php\' to \'documentss.php\'', '2026-07-08 15:14:30'),
(818, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-08 15:27:10'),
(819, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-08 15:27:15'),
(820, 3, 'LOGOUT', 'User \'cha\' logged out', '2026-07-08 15:27:23'),
(821, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-08 15:27:27'),
(822, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-08 15:28:10'),
(823, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-08 15:28:40'),
(824, 1, 'USER_CREATE', 'Created user \'Allen\'', '2026-07-08 15:29:21'),
(825, 1, 'PERMISSIONS', 'Updated permissions for user #3', '2026-07-08 15:30:33'),
(826, 2, 'LOGOUT', 'User \'pat\' logged out', '2026-07-08 15:30:40'),
(827, 3, 'LOGIN', 'User \'cha\' logged in', '2026-07-08 15:30:44'),
(828, 1, 'RENAME_FILE', 'Renamed \'contact_us_formm.php\' to \'contact_us_formsm.php\'', '2026-07-08 16:13:35'),
(829, 1, 'CHECKOUT', 'Checked out \'hp_hph200_h200_wired_stereo_gaming_1599010.jpg\'', '2026-07-08 16:48:48'),
(830, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'hp_hph200_h200_wired_stereo_gaming_1599010.jpg\' via access hierarchy rule.', '2026-07-08 16:48:50'),
(831, 1, 'RENAME_FILE', 'Renamed \'contact_us_formsm.php\' to \'contact_us_formspm.php\'', '2026-07-09 07:54:33'),
(832, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'contact_us_formspm.php\' via access hierarchy rule.', '2026-07-09 07:54:39'),
(833, 1, 'UPLOAD', 'Uploaded \'user_detail.php\'', '2026-07-09 08:13:11'),
(834, 1, 'RENAME_FILE', 'Renamed document ID 62 from \'user_detail.php\' to \'user_detailsss.php\'', '2026-07-09 08:14:09'),
(835, 1, 'VERSION_BUMP', 'Committed file revision for \'user_detailsss.php\' — now version v2', '2026-07-09 08:14:27'),
(836, 1, 'SOFT_DELETE', 'Moved \'user_detailsss.php\' to trash', '2026-07-09 08:16:54'),
(837, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'contact_us_formspm.php\' via access hierarchy rule.', '2026-07-09 08:39:38'),
(838, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'documentss.php\' via access hierarchy rule.', '2026-07-09 09:05:12'),
(839, 1, 'DOWNLOAD', 'Downloaded \'documentss.php\'', '2026-07-09 09:19:30'),
(840, 1, 'DOWNLOAD', 'Downloaded \'documentss.php\'', '2026-07-09 09:21:26'),
(841, 1, 'DOWNLOAD', 'Downloaded \'documentss.php\'', '2026-07-09 09:28:16'),
(842, 1, 'DOWNLOAD', 'Downloaded \'documentss.php\'', '2026-07-09 09:29:05'),
(843, 1, 'CHECKOUT', 'Checked out \'documentss.php\'', '2026-07-09 09:29:12'),
(844, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'documentss.php\' via access hierarchy rule.', '2026-07-09 09:29:14'),
(845, 1, 'CHECKOUT', 'Checked out \'documentss.php\'', '2026-07-09 09:29:17'),
(846, 1, 'RENAME_FILE', 'Renamed document ID 60 from \'documentss.php\' to \'documents.php\'', '2026-07-09 09:29:23'),
(847, 1, 'VERSION_BUMP', 'Committed file revision for \'documents.php\' — now version v2', '2026-07-09 09:29:47'),
(848, 1, 'DOWNLOAD', 'Downloaded \'documents.php\'', '2026-07-09 09:34:22'),
(849, 1, 'RENAME_FILE', 'Renamed document ID 57 from \'contact_us_formspm.php\' to \'conta.php\'', '2026-07-09 09:36:53'),
(850, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'conta.php\' via access hierarchy rule.', '2026-07-09 09:36:57'),
(851, 1, 'RENAME_FILE', 'Renamed document ID 60 from \'documents.php\' to \'documensssts.php\'', '2026-07-09 09:40:55'),
(852, 1, 'DOWNLOAD', 'Downloaded \'documensssts.php\'', '2026-07-09 09:41:00'),
(853, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'documensssts.php\' via access hierarchy rule.', '2026-07-09 09:41:04'),
(854, 1, 'CHECKOUT', 'Checked out \'DFD of DMS (2).png\'', '2026-07-09 09:41:12'),
(855, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-09 10:03:29'),
(856, 33, 'PASSWORD_RESET_REQUEST', 'Password reset requested', '2026-07-09 10:03:37'),
(857, 33, 'PASSWORD_RESET_REQUEST', 'Password reset requested', '2026-07-09 10:04:09'),
(858, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-09 10:04:51'),
(859, 1, 'RENAME_FILE', 'Renamed document ID 60 from \'documensssts.php\' to \'allen.php\'', '2026-07-09 10:05:24'),
(860, 1, 'RENAME_FILE', 'Renamed document ID 60 from \'allen.php\' to \'allenallen.php\'', '2026-07-09 10:05:41'),
(861, 1, 'DOWNLOAD', 'Downloaded \'allenallen.php\'', '2026-07-09 10:05:55'),
(862, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'allenallen.php\' via access hierarchy rule.', '2026-07-09 10:06:01'),
(863, 1, 'RENAME_FILE', 'Renamed document ID 57 from \'conta.php\' to \'contsssssa.php\'', '2026-07-09 10:09:00'),
(864, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'contsssssa.php\' via access hierarchy rule.', '2026-07-09 10:09:15'),
(865, 1, 'CHECKOUT', 'Checked out \'contsssssa.php\'', '2026-07-09 10:09:17'),
(866, 1, 'CHECKIN_CANCEL', 'Unlocked/Released padlock on \'contsssssa.php\' via access hierarchy rule.', '2026-07-09 11:11:12'),
(867, 1, 'RENAME_FILE', 'Renamed document ID 57 from \'contsssssa.php\' to \'contsssssza.php\'', '2026-07-09 11:11:18'),
(868, 1, 'FOLDER_SHARE_BATCH', 'Granted folder access to 1 users: folder_id=3', '2026-07-09 11:11:35'),
(869, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-09 13:28:50'),
(870, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-09 13:41:29'),
(871, 1, 'USER_CREATION', 'Approved request ID 17. Created account profile \'alexa17\' with role \'casual\'', '2026-07-09 13:43:54'),
(872, 42, 'LOGIN', 'User \'alexa17\' logged in', '2026-07-09 13:46:39'),
(873, 1, 'LOGOUT', 'User \'admin\' logged out', '2026-07-09 15:55:39'),
(874, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-09 15:56:40'),
(875, 1, 'FOLDER_CREATION', 'Created folder: Hey', '2026-07-09 16:25:59');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `size` int(11) NOT NULL DEFAULT 0,
  `uploaded_by` int(11) NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_trashed` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `folder_id`, `filename`, `storage_path`, `version`, `size`, `uploaded_by`, `is_locked`, `locked_by`, `is_deleted`, `created_at`, `is_trashed`, `deleted_at`, `is_private`) VALUES
(2, NULL, 'ss1.PNG', 'doc_6a4315e622a518.64355959.png', 1, 19876, 2, 0, NULL, 1, '2026-06-30 09:03:34', 0, NULL, 0),
(4, NULL, 'image_9d5ad14c.png', 'doc_6a447f053bd5f0.64283474.png', 2, 346722, 2, 0, NULL, 0, '2026-07-01 10:44:21', 0, NULL, 0),
(6, NULL, 'image_9d5ad14c (1).png', 'doc_6a448bb05a5dc4.03378141.png', 1, 346722, 1, 0, NULL, 1, '2026-07-01 11:38:24', 0, NULL, 0),
(7, NULL, 'DFD of DMS.png', 'doc_6a448bdfcebc57.90675644.png', 1, 106569, 2, 1, 1, 0, '2026-07-01 11:39:11', 0, NULL, 0),
(8, NULL, 'profpic2.png', 'doc_6a449dac882608.29146169.png', 1, 42539, 1, 0, NULL, 0, '2026-07-01 12:55:08', 0, NULL, 0),
(14, 1, 'imglogo.png', 'doc_rev_6a4c4ea86d1755.74913420.gif', 19, 1139680, 1, 0, NULL, 0, '2026-07-02 10:55:53', 0, NULL, 0),
(16, 2, 'Document Management System.pptx', 'doc_rev_6a47178691be86.02519262.jpg', 3, 49313, 1, 0, NULL, 0, '2026-07-02 13:57:35', 0, NULL, 0),
(19, 1, 'WIN_20260506_12_13_29_Pro.jpg', 'doc_rev_6a470b70e37e39.39369309.webp', 2, 102344, 1, 0, NULL, 1, '2026-07-02 14:46:17', 0, NULL, 0),
(20, NULL, '[틴즈엘teensEL] Turn It Up- Planetshakers(360P).mp4', 'doc_rev_6a470b196119b8.31221470.css', 2, 27457, 2, 0, NULL, 0, '2026-07-03 08:32:57', 0, NULL, 0),
(21, NULL, 'Untitled designhhhh.pdf', 'doc_6a472bbe46cb51.48739389.pdf', 1, 67939, 1, 0, NULL, 0, '2026-07-03 11:25:50', 0, NULL, 0),
(22, NULL, 'db.php', 'doc_6a475d62ebf5d7.35340409.php', 1, 1000, 1, 0, NULL, 0, '2026-07-03 14:57:38', 0, NULL, 0),
(23, NULL, 'students.csv', 'doc_6a4c4973eff701.62196643.csv', 1, 48, 1, 0, NULL, 0, '2026-07-07 08:33:55', 0, NULL, 0),
(24, NULL, 'students.csv', 'doc_6a4c4c04690d91.49334634.csv', 1, 48, 1, 0, NULL, 1, '2026-07-07 08:44:52', 0, NULL, 0),
(25, NULL, 'students.csv', 'doc_6a4c4c05c79e32.30289202.csv', 1, 48, 1, 0, NULL, 1, '2026-07-07 08:44:53', 0, NULL, 0),
(26, NULL, 'students.csv', 'doc_6a4c4c32278020.21876782.csv', 1, 48, 1, 0, NULL, 1, '2026-07-07 08:45:38', 0, NULL, 0),
(27, 3, '2359c3f8-611d-40c4-88ab-49d4695dfd5f.5b26b18ccda9066176d9e3346f969843.webp', 'doc_6a4c58e81c6749.42855072.webp', 1, 52294, 1, 0, NULL, 1, '2026-07-07 09:39:52', 0, NULL, 1),
(28, 3, '61ZB1f7oODL.jpg', 'doc_6a4c58e820d356.28607578.jpg', 1, 122785, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(29, 3, '71O0zctwPiL._AC_SL1200_.jpg', 'doc_6a4c58e8229de8.99009781.jpg', 1, 140358, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(30, 3, 'AT-ATR4750.jpg', 'doc_6a4c58e824d8d1.08174832.jpg', 1, 92088, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(31, 3, 'Best-compact-gaming-keyboard-runner-up-Wooting-One-Analogue-Keyboard.webp', 'doc_6a4c58e826c531.86234473.webp', 1, 908834, 1, 0, NULL, 1, '2026-07-07 09:39:52', 0, NULL, 1),
(32, 3, 'Digitizer-tablet-The-WACOM-Specifications-are-listed-below-456-BULLET-Active-Area.png', 'doc_6a4c58e8285cc6.73833755.png', 1, 22652, 1, 0, NULL, 1, '2026-07-07 09:39:52', 0, NULL, 1),
(33, 3, 'Epos_Now_2D_USB_Barcode_Scanner_-_Including_stand.jpg', 'doc_6a4c58e829e8e7.48408406.jpg', 1, 60966, 1, 0, NULL, 1, '2026-07-07 09:39:52', 0, NULL, 1),
(34, 3, 'generic-headphones.jpg', 'doc_6a4c58e82bdd26.55807977.jpg', 1, 14017, 1, 0, NULL, 1, '2026-07-07 09:39:52', 0, NULL, 1),
(35, 3, 'hp_hph200_h200_wired_stereo_gaming_1599010.jpg', 'doc_6a4c58e82d8532.55980287.jpg', 1, 283131, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(36, 3, 'IPF5100-2_1000x1000.jpg', 'doc_6a4c58e82f3193.28747005.jpg', 1, 18689, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(37, 3, 'light-pen.jpg', 'doc_6a4c58e8309919.71138874.jpg', 1, 31592, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(38, 3, 'logitech-webcam-v-uas14.jpg', 'doc_6a4c58e8324210.37511254.jpg', 1, 72406, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(39, 3, 'monitor.jpg', 'doc_6a4c58e833e8a2.63563301.jpg', 1, 14846, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(40, 3, 'MSI-GTX960-2G-2.webp', 'doc_6a4c58e835db47.08578108.webp', 1, 68492, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(41, 3, 'navigation-global-positioning-system-gps-device-navigat-navigation-global-positioning-system-gps-device-117441831.webp', 'doc_6a4c58e8377786.26421137.webp', 1, 18678, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(42, 3, 'orbit-reader20-prime-braille-solutions.jpg', 'doc_6a4c58e838ec49.49507135.jpg', 1, 53657, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(43, 3, 'SvzYpM3ZgkrWQNvWfyMU7g-1600-80.jpg', 'doc_6a4c58e83a4de5.20532195.jpg', 1, 75002, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(44, 3, 'WF-2010W.jpg', 'doc_6a4c58e83bb4d7.63780971.jpg', 1, 87962, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(45, 3, 'xsc.png', 'doc_6a4c58e83cf807.79643099.png', 1, 293255, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(46, 3, '1.JPG', 'doc_6a4c58e83e4ea4.71981841.JPG', 1, 116518, 1, 0, NULL, 0, '2026-07-07 09:39:52', 0, NULL, 1),
(47, 6, '14.09.2025_15.35.37_RECREC.png', 'doc_6a4c89c23578c5.37792008.png', 1, 106007, 1, 0, NULL, 1, '2026-07-07 13:08:18', 0, NULL, 1),
(48, NULL, '1.JPG', 'doc_6a4caa1a203ca6.06340154.JPG', 1, 87226, 1, 0, NULL, 0, '2026-07-07 15:26:18', 0, NULL, 0),
(49, 5, '5501574c-0853-4abe-9435-d339e244f924.jpg', 'doc_6a4cad6eec2ba8.13132851.jpg', 1, 34909, 2, 0, NULL, 0, '2026-07-07 15:40:30', 0, NULL, 1),
(50, 7, 'new.py', 'doc_6a4d9dea934689.28151246.py', 1, 703, 1, 0, NULL, 0, '2026-07-08 08:46:34', 0, NULL, 0),
(51, 3, 'allen.py', 'doc_6a4da12062c381.54792193.py', 1, 1117, 1, 0, NULL, 0, '2026-07-08 09:00:16', 0, NULL, 0),
(52, 3, 'RobloxScreenShot20250706_152042024.png', 'doc_6a4daa41885e25.85757790.png', 1, 810209, 1, 0, NULL, 0, '2026-07-08 09:39:13', 0, NULL, 1),
(53, 3, 'RobloxScreenShot20260427_193052504.png', 'doc_6a4daa418abd96.98204541.png', 1, 805810, 1, 0, NULL, 0, '2026-07-08 09:39:13', 0, NULL, 1),
(54, 9, 'RobloxScreenShot20260427_193052504.png', 'doc_rev_6a4dbf17b78018.81553486.png', 2, 810209, 1, 0, NULL, 0, '2026-07-08 11:04:24', 0, NULL, 1),
(55, 9, 'DFD of DMS (2).png', 'doc_6a4dbf9867d999.21035069.png', 1, 106569, 1, 1, 1, 0, '2026-07-08 11:10:16', 0, NULL, 0),
(56, 9, 'rename_file.php', 'doc_6a4dc6ccd2bd20.67081836.php', 1, 1390, 1, 0, NULL, 0, '2026-07-08 11:41:00', 0, NULL, 0),
(57, 9, 'contsssssza.php', 'doc_6a4dc8585d9056.12623726.php', 1, 10822, 1, 1, 1, 0, '2026-07-08 11:47:36', 0, NULL, 1),
(58, NULL, 'trashhh.php', 'doc_6a4dc9a7c6b380.37591421.php', 1, 6085, 1, 0, NULL, 0, '2026-07-08 11:53:11', 0, NULL, 0),
(59, NULL, 'Document Management System (3o).pptx', 'doc_6a4de9e057f469.07065806.pptx', 1, 49313, 2, 0, NULL, 0, '2026-07-08 14:10:40', 0, NULL, 0),
(60, NULL, 'allenallen.php', 'doc_rev_6a4ef98b2df308.28852502.php', 2, 530, 1, 0, NULL, 0, '2026-07-08 14:42:46', 0, NULL, 0),
(61, NULL, 'folders.php', 'doc_6a4df1669cf868.92398626.php', 1, 65906, 1, 0, NULL, 0, '2026-07-08 14:42:46', 0, NULL, 0),
(62, NULL, 'user_detailsss.php', 'doc_rev_6a4ee7e34c10f9.88069626.php', 2, 10424, 1, 0, NULL, 1, '2026-07-09 08:13:11', 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `document_shares`
--

CREATE TABLE `document_shares` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `shared_with_user_id` int(11) NOT NULL,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `can_download` tinyint(1) DEFAULT 1,
  `can_checkout` tinyint(1) DEFAULT 0,
  `can_add` tinyint(1) DEFAULT 0,
  `can_share` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_shares`
--

INSERT INTO `document_shares` (`id`, `document_id`, `shared_with_user_id`, `can_edit`, `can_delete`, `can_download`, `can_checkout`, `can_add`, `can_share`) VALUES
(2, 2, 3, 0, 0, 1, 0, 0, 0),
(5, 6, 3, 0, 0, 1, 0, 0, 0),
(7, 14, 33, 0, 0, 1, 0, 0, 0),
(8, 14, 3, 1, 1, 1, 1, 0, 0),
(9, 19, 33, 0, 0, 1, 0, 0, 0),
(10, 19, 3, 0, 0, 1, 0, 0, 0),
(11, 16, 4, 0, 0, 1, 0, 0, 0),
(12, 21, 3, 0, 0, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_versions`
--

INSERT INTO `document_versions` (`id`, `document_id`, `version_number`, `storage_path`, `file_size`, `updated_by`, `created_at`) VALUES
(1, 14, 4, 'doc_6a45d339ac63e9.84630328.png', 377241, 1, '2026-07-02 04:25:57'),
(2, 14, 4, 'doc_6a45d339ac63e9.84630328.png', 377241, 1, '2026-07-02 04:32:04'),
(3, 14, 7, 'doc_rev_6a45e9c4485867.70910679.png', 121679, 1, '2026-07-02 04:34:11'),
(4, 14, 8, 'doc_rev_6a45ea43b9c4e0.94094261.png', 1418416, 1, '2026-07-02 05:11:41'),
(5, 13, 1, 'doc_6a45d22fb3b917.61562470.png', 121679, 1, '2026-07-02 05:38:05'),
(6, 4, 2, 'doc_6a447f053bd5f0.64283474.png', 346722, 1, '2026-07-03 01:03:23'),
(7, 20, 1, 'doc_6a470339a21ca0.90085391.mp4', 19490075, 1, '2026-07-03 01:06:33'),
(8, 19, 1, 'doc_dir_6a460939149337.48571635.jpg', 120414, 1, '2026-07-03 01:06:58'),
(9, 19, 1, 'doc_dir_6a460939149337.48571635.jpg', 120414, 1, '2026-07-03 01:08:00'),
(10, 16, 1, 'doc_6a45fdcfa985c7.06582979.pptx', 10707918, 1, '2026-07-03 01:58:49'),
(11, 16, 2, 'doc_rev_6a471759470078.96065074.jpg', 46271, 1, '2026-07-03 01:59:34'),
(12, 14, 17, 'doc_rev_6a45f30dc78ec3.42425873.jpg', 493818, 1, '2026-07-03 02:43:11'),
(13, 14, 18, 'doc_rev_6a4721bfa44b25.09186383.jpg', 15742, 1, '2026-07-07 00:56:08'),
(14, 54, 1, 'doc_6a4dbe387cffd1.56433966.png', 805810, 1, '2026-07-08 03:08:07'),
(15, 62, 1, 'doc_6a4ee797e3fb68.20285886.php', 3310, 1, '2026-07-09 00:14:27'),
(16, 60, 1, 'doc_6a4df1669814e2.36514384.php', 33517, 1, '2026-07-09 01:29:47');

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('public','private') NOT NULL DEFAULT 'private',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_private` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`id`, `parent_id`, `name`, `type`, `created_by`, `created_at`, `is_private`) VALUES
(1, NULL, 'Picturess', 'private', 1, '2026-07-02 02:55:25', 0),
(2, NULL, 'PPT', 'private', 1, '2026-07-02 05:57:15', 0),
(3, NULL, 'Admin Private Files', 'private', 1, '2026-07-02 23:01:16', 1),
(4, NULL, 'FDWOUIFHA', 'private', 2, '2026-07-02 23:31:38', 0),
(5, NULL, 'Private Folder', 'private', 2, '2026-07-02 23:32:55', 1),
(6, NULL, 'Videos', 'private', 2, '2026-07-03 00:33:41', 0),
(7, NULL, 'Allen P', 'private', 1, '2026-07-06 07:16:00', 1),
(8, NULL, 'New P1', 'private', 1, '2026-07-07 00:47:31', 1),
(9, 3, 'also folder', 'private', 1, '2026-07-08 01:36:34', 1),
(10, 3, 'len 2f', 'private', 1, '2026-07-08 01:59:32', 1),
(11, 9, 'new f', 'private', 1, '2026-07-08 03:04:46', 1),
(12, 3, 'inside folder folder', 'private', 1, '2026-07-08 05:30:39', 1),
(13, NULL, 'Hey', 'public', 1, '2026-07-09 08:25:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `folder_shares`
--

CREATE TABLE `folder_shares` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `shared_with_user_id` int(11) NOT NULL,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_add` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `can_checkout` tinyint(1) DEFAULT 0,
  `can_download` tinyint(1) DEFAULT 0,
  `can_share` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `folder_shares`
--

INSERT INTO `folder_shares` (`id`, `folder_id`, `shared_with_user_id`, `can_edit`, `can_add`, `can_delete`, `can_checkout`, `can_download`, `can_share`) VALUES
(1, 1, 33, 1, 1, 1, 1, 1, 1),
(2, 1, 34, 1, 1, 1, 1, 1, 1),
(3, 5, 2, 1, 1, 1, 1, 1, 1),
(4, 3, 3, 1, 1, 1, 1, 1, 1),
(5, 4, 3, 1, 1, 1, 1, 1, 1),
(6, 3, 33, 1, 1, 1, 1, 1, 1),
(7, 9, 33, 1, 1, 1, 1, 1, 1),
(8, 10, 33, 1, 1, 1, 1, 1, 1),
(9, 12, 33, 1, 1, 1, 1, 1, 1),
(10, 11, 33, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'Account', 'Your profile has been updated.', NULL, 1, '2026-07-06 01:32:47'),
(2, 1, 'Branding', 'Logo and brand name updated.', NULL, 1, '2026-07-06 03:30:07'),
(3, 1, 'Branding', 'Logo and brand name updated.', NULL, 1, '2026-07-06 03:31:36'),
(4, 1, 'Branding', 'Logo and brand name updated.', NULL, 1, '2026-07-06 03:58:44'),
(5, 2, 'Account', 'Your profile has been updated.', NULL, 0, '2026-07-06 04:00:01'),
(6, 3, 'Account', 'Your profile has been updated.', NULL, 0, '2026-07-06 04:01:26'),
(7, 1, 'Account', 'Your profile has been updated.', NULL, 0, '2026-07-07 01:41:56'),
(8, 1, 'Branding', 'Logo and brand name updated.', NULL, 0, '2026-07-07 02:59:17'),
(9, 1, 'Account', 'Your profile has been updated.', NULL, 0, '2026-07-07 02:59:23'),
(10, 1, 'Registration', 'Prince Ace requested account approval.', '/DMS-allen3/DMS-allen/admin/admin_approvals.php', 0, '2026-07-07 03:26:22'),
(11, 4, 'Registration', 'Prince Ace requested account approval.', '/DMS-allen3/DMS-allen/admin/admin_approvals.php', 0, '2026-07-07 03:26:22'),
(12, 38, 'Account', 'Your profile has been updated.', NULL, 0, '2026-07-07 03:28:25'),
(13, 1, 'Registration', 'Test2 TESTTEST requested account approval.', '/DMS-LEN/dms-len/admin/admin_approvals.php', 0, '2026-07-08 00:54:33'),
(14, 4, 'Registration', 'Test2 TESTTEST requested account approval.', '/DMS-LEN/dms-len/admin/admin_approvals.php', 0, '2026-07-08 00:54:33'),
(15, 1, 'Registration', 'test1 TESTTTTT requested account approval.', '/DMS-LEN/dms-len/admin/admin_approvals.php', 0, '2026-07-08 01:05:40'),
(16, 4, 'Registration', 'test1 TESTTTTT requested account approval.', '/DMS-LEN/dms-len/admin/admin_approvals.php', 0, '2026-07-08 01:05:40'),
(17, 1, 'Registration', 'Alexa Moneth requested account approval.', '/DMS-LEN/dms-len/admin/admin_approvals.php', 0, '2026-07-09 05:37:50'),
(18, 4, 'Registration', 'Alexa Moneth requested account approval.', '/DMS-LEN/dms-len/admin/admin_approvals.php', 0, '2026-07-09 05:37:50');

-- --------------------------------------------------------

--
-- Table structure for table `org_settings`
--

CREATE TABLE `org_settings` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `brand_name` varchar(100) DEFAULT 'FILESTAC DMS',
  `logo_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `org_settings`
--

INSERT INTO `org_settings` (`id`, `admin_id`, `brand_name`, `logo_path`, `updated_at`) VALUES
(1, 1, 'FILESTAC Pat', 'assets/logo/org_1/logo.jpg', '2026-07-07 02:59:17');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `used`) VALUES
(1, 9, '8441b87ef0458bbc1c711966afe81047d3fa6f4c492bb435ae89ca20a0248906', '2026-07-06 04:26:43', 0),
(2, 38, 'e4688549e912a532accf2de16d84fb8099dc36df89aa3a4aa608ef1721059ff2', '2026-07-07 10:36:52', 0),
(3, 33, '1199f7d2f2af55259b0837e6172676bd7ce9a0f3958096ffdff551fd00eef175', '2026-07-07 10:38:28', 0),
(4, 33, 'f79506bb128e36279a44216861d2e48ca4f61e5cda928022930a5174a99c1fd3', '2026-07-07 11:50:52', 0),
(5, 33, 'af3cd783e088125733199207f4fd32deb965b574435d7297b0cdbed6e8033b24', '2026-07-08 03:33:24', 0),
(6, 33, '31a798c3fcc9c084be26c79e71251f038d4808beae712a3653a004c0921fcc14', '2026-07-09 05:03:33', 0),
(7, 33, '33860b8fa982cf4e66fee9f0743a7dd96ea17decf8028cb9ce210cea022808a1', '2026-07-09 05:04:05', 0);

-- --------------------------------------------------------

--
-- Table structure for table `registration_requests`
--

CREATE TABLE `registration_requests` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `reasons` text DEFAULT NULL,
  `sources` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `permitted_actions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registration_requests`
--

INSERT INTO `registration_requests` (`id`, `first_name`, `last_name`, `email`, `phone`, `reasons`, `sources`, `status`, `created_at`, `permitted_actions`) VALUES
(9, 'Len', 'Len', 'allengabrielsilvagaspar@gmail.com', '0973252237', '', '', 'approved', '2026-07-02 00:54:51', NULL),
(10, 'Patricia', 'Rebosura', 'pagod5841@gmail.com', '09675237582', 'Uploading Documents, Presentations / PPT, Code File Storage, Collaborating with others, Group Workplace Setup, Online Storage Layout', 'Facebook, Google Search, Friend Reference, Advertisement Banner', 'approved', '2026-07-02 01:14:03', NULL),
(11, 'Patty', 'Patty', 'patriciavernicerebosura77@gmail.com', '09098046552', 'Code File Storage, Group Workplace Setup', 'Friend Reference', 'approved', '2026-07-03 01:10:43', NULL),
(12, 'Charizma', 'Laogan', 'allengaspar@gmail.com', '09675237582', 'Group Workplace Setup, Secure Version Control', 'YouTube Video Review', 'approved', '2026-07-03 01:15:44', NULL),
(13, 'Try 1', 'Try 2', 'pot036331@gmail.com', '09098046552', 'Secure Version Control', 'Advertisement Banner', 'approved', '2026-07-03 01:21:43', NULL),
(14, 'Prince', 'Ace', 'prince.ace2377@gmail.com', '09675237582', 'Presentations / PPT, Collaborating with others', 'Advertisement Banner, YouTube Video Review', 'approved', '2026-07-07 03:26:22', NULL),
(15, 'Test2', 'TESTTEST', 'test2@gmail.com', '093732828', 'Collaborating with others, Automated Backup Systems', 'Tech Blog Newsletter', 'approved', '2026-07-08 00:54:33', NULL),
(16, 'test1', 'TESTTTTT', 'test2@gmail.com', '002935723', 'Code File Storage', 'Friend Reference', 'pending', '2026-07-08 01:05:40', NULL),
(17, 'Alexa', 'Moneth', 'firebaseprojecttest1@gmail.com', '0965732352', 'Uploading Documents, Presentations / PPT, Code File Storage, Collaborating with others, Group Workplace Setup, Online Storage Layout, Secure Version Control, Automated Backup Systems, Client File Sharing Portals', 'Friend Reference', 'approved', '2026-07-09 05:37:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','contributor','casual') NOT NULL DEFAULT 'casual',
  `status` enum('active','frozen') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(30) DEFAULT NULL,
  `theme` varchar(20) DEFAULT 'light',
  `admin_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`, `created_at`, `phone`, `theme`, `admin_id`, `is_deleted`) VALUES
(1, 'admin', 'admin@filestac.com', '$2y$10$zNkYu5.BgKbAFzsUgnZIpe6Pzy.TBm.2dg4gteJ6alhEigNtJBGWm', 'admin', 'active', '2026-06-29 16:25:18', '09944009180', 'light', NULL, 0),
(2, 'pat', 'pat@kiwidms.com', '$2y$10$zNkYu5.BgKbAFzsUgnZIpe6Pzy.TBm.2dg4gteJ6alhEigNtJBGWm', 'contributor', 'frozen', '2026-06-29 16:25:18', '09675237582', 'light', NULL, 0),
(3, 'cha', 'cha@kiwidms.com', '$2y$10$zNkYu5.BgKbAFzsUgnZIpe6Pzy.TBm.2dg4gteJ6alhEigNtJBGWm', 'casual', 'active', '2026-06-29 16:25:18', '09675237582', 'light', NULL, 0),
(4, 'Sir Jhon Rey', 'jr@gmail.com', '$2y$10$wipKX5iV2w95gd/YVgjHa.BMn73B.cUrF5VBmUn6E8r3SnLQ9RIuK', 'admin', 'active', '2026-06-29 17:21:49', NULL, 'light', NULL, 0),
(9, 'allen2', 'len.10212005@gmail.com', '$2y$10$9vokn8WN8g4zRQJxMq023eQfEATP9HyqPDghZ5y7a3njfpEQfofcq', 'casual', 'active', '2026-07-02 06:55:58', NULL, 'light', NULL, 0),
(33, 'len9', 'allengabrielsilvagaspar@gmail.com', '$2y$10$kqTqnAVqbiFq2YihQkbqh.8Oqkpl.uNYd9Y.K3Lo263N8R1mnq6Hu', 'casual', 'active', '2026-07-02 08:55:03', NULL, 'light', NULL, 0),
(34, 'patricia10', 'pagod5841@gmail.com', '$2y$10$7ySnP/IuYQzJgV5hN1RxTOJxM/okRGlbyJKawumXf.ssPMorCKSJW', 'casual', 'frozen', '2026-07-02 09:14:23', NULL, 'light', NULL, 1),
(35, 'patty11', 'patriciavernicerebosura77@gmail.com', '$2y$10$2FdDW54.ANErwL2zn90QqOwUVll1d7R39alwtJJqmHWj8WwE3rDUK', 'casual', 'active', '2026-07-03 09:11:27', NULL, 'light', NULL, 0),
(36, 'try 113', 'pot036331@gmail.com', '$2y$10$Dft0i8.AuakjOyOB.m9joe6LbtuYoh8oYlLyxrtBuJ6dABtWaG8AS', 'contributor', 'active', '2026-07-03 09:22:08', NULL, 'light', NULL, 0),
(37, 'charizma12', 'allengaspar@gmail.com', '$2y$10$iP4KeORc5BL9/vvw/klJJuTT0eLusYuw6m/oDrI5g34G.1u5OmQga', 'casual', 'frozen', '2026-07-06 14:53:29', NULL, 'light', NULL, 0),
(38, 'Prince Ace', 'prince.ace2377@gmail.com', '$2y$10$H4Erk9MninBMFOwuZcUeYOLPoZnyUi9TlMz3netZYnpUgnX8M8yUC', 'casual', 'active', '2026-07-07 11:27:14', '09824826402', 'light', NULL, 0),
(39, 'try1', 'try1@gmail.com', '$2y$10$3XiPrbxFI7nahBxLb2Efc.YVSXbzuKE7ygctr/frrwJEekcld8lsm', 'casual', 'active', '2026-07-08 08:20:16', NULL, 'light', 1, 0),
(40, 'test1', 'test2@gmail.com', '$2y$10$ojJt4aw/JFHa2cGeMXN/V.JpK9QbtUCbyC21t8pDrqnmf6FnnMsE6', 'casual', 'active', '2026-07-08 09:05:06', NULL, 'light', NULL, 0),
(42, 'alexa17', 'firebaseprojecttest1@gmail.com', '$2y$10$x6lNynCK9l5xgBeH6S3M0uKGxZEb.WjOr5tkLqfzEavazDpUg4pau', 'casual', 'active', '2026-07-09 13:43:54', NULL, 'light', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `can_add` tinyint(1) DEFAULT 1,
  `can_download` tinyint(1) DEFAULT 1,
  `can_share` tinyint(1) DEFAULT 1,
  `can_delete` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_checkout` tinyint(1) DEFAULT 0,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `can_add`, `can_download`, `can_share`, `can_delete`, `can_edit`, `can_checkout`, `updated_by`, `updated_at`) VALUES
(1, 1, 1, 0, 1, 1, 1, 1, 1, '2026-07-08 02:46:05'),
(3, 3, 0, 0, 0, 0, 0, 0, 1, '2026-07-08 07:30:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `locked_by` (`locked_by`);

--
-- Indexes for table `document_shares`
--
ALTER TABLE `document_shares`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_share` (`document_id`,`shared_with_user_id`),
  ADD KEY `shared_with_user_id` (`shared_with_user_id`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `folder_shares`
--
ALTER TABLE `folder_shares`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_folder_share` (`folder_id`,`shared_with_user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`);

--
-- Indexes for table `org_settings`
--
ALTER TABLE `org_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_token` (`token`);

--
-- Indexes for table `registration_requests`
--
ALTER TABLE `registration_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=876;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `document_shares`
--
ALTER TABLE `document_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `folder_shares`
--
ALTER TABLE `folder_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `org_settings`
--
ALTER TABLE `org_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `registration_requests`
--
ALTER TABLE `registration_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_shares`
--
ALTER TABLE `document_shares`
  ADD CONSTRAINT `document_shares_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_shares_ibfk_2` FOREIGN KEY (`shared_with_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
