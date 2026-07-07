-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2026 at 03:20 AM
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
(615, 1, 'LOGIN', 'User \'admin\' logged in', '2026-07-06 09:19:46');

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
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `folder_id`, `filename`, `storage_path`, `version`, `size`, `uploaded_by`, `is_locked`, `locked_by`, `is_deleted`, `created_at`, `is_trashed`, `deleted_at`) VALUES
(2, NULL, 'ss1.PNG', 'doc_6a4315e622a518.64355959.png', 1, 19876, 2, 0, NULL, 1, '2026-06-30 09:03:34', 0, NULL),
(4, NULL, 'image_9d5ad14c.png', 'doc_6a447f053bd5f0.64283474.png', 2, 346722, 2, 0, NULL, 0, '2026-07-01 10:44:21', 0, NULL),
(6, NULL, 'image_9d5ad14c (1).png', 'doc_6a448bb05a5dc4.03378141.png', 1, 346722, 1, 0, NULL, 1, '2026-07-01 11:38:24', 0, NULL),
(7, NULL, 'DFD of DMS.png', 'doc_6a448bdfcebc57.90675644.png', 1, 106569, 2, 1, 1, 0, '2026-07-01 11:39:11', 0, NULL),
(8, NULL, 'profpic2.png', 'doc_6a449dac882608.29146169.png', 1, 42539, 1, 0, NULL, 0, '2026-07-01 12:55:08', 0, NULL),
(14, 1, 'imglogo.png', 'doc_rev_6a4721bfa44b25.09186383.jpg', 18, 15742, 1, 0, NULL, 0, '2026-07-02 10:55:53', 0, NULL),
(16, 2, 'Document Management System.pptx', 'doc_rev_6a47178691be86.02519262.jpg', 3, 49313, 1, 0, NULL, 0, '2026-07-02 13:57:35', 0, NULL),
(19, 1, 'WIN_20260506_12_13_29_Pro.jpg', 'doc_rev_6a470b70e37e39.39369309.webp', 2, 102344, 1, 0, NULL, 1, '2026-07-02 14:46:17', 0, NULL),
(20, NULL, '[틴즈엘teensEL] Turn It Up- Planetshakers(360P).mp4', 'doc_rev_6a470b196119b8.31221470.css', 2, 27457, 2, 0, NULL, 0, '2026-07-03 08:32:57', 0, NULL),
(21, NULL, 'Untitled designhhhh.pdf', 'doc_6a472bbe46cb51.48739389.pdf', 1, 67939, 1, 1, 1, 0, '2026-07-03 11:25:50', 0, NULL),
(22, NULL, 'db.php', 'doc_6a475d62ebf5d7.35340409.php', 1, 1000, 1, 0, NULL, 0, '2026-07-03 14:57:38', 0, NULL);

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
  `can_checkout` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_shares`
--

INSERT INTO `document_shares` (`id`, `document_id`, `shared_with_user_id`, `can_edit`, `can_delete`, `can_download`, `can_checkout`) VALUES
(2, 2, 3, 0, 0, 1, 0),
(5, 6, 3, 0, 0, 1, 0),
(7, 14, 33, 0, 0, 1, 0),
(8, 14, 3, 1, 1, 1, 1),
(9, 19, 33, 0, 0, 1, 0),
(10, 19, 3, 0, 0, 1, 0),
(11, 16, 4, 0, 0, 1, 0),
(12, 21, 3, 0, 0, 1, 0);

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
(12, 14, 17, 'doc_rev_6a45f30dc78ec3.42425873.jpg', 493818, 1, '2026-07-03 02:43:11');

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('public','private') NOT NULL DEFAULT 'private',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_private` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`id`, `name`, `type`, `created_by`, `created_at`, `is_private`) VALUES
(1, 'Picturess', 'private', 1, '2026-07-02 02:55:25', 0),
(2, 'PPT', 'private', 1, '2026-07-02 05:57:15', 0),
(3, 'Admin Private Files', 'private', 1, '2026-07-02 23:01:16', 1),
(4, 'FDWOUIFHA', 'private', 2, '2026-07-02 23:31:38', 0),
(5, 'Private Folder', 'private', 2, '2026-07-02 23:32:55', 1),
(6, 'Videos', 'private', 2, '2026-07-03 00:33:41', 0);

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
(3, 5, 2, 1, 1, 1, 1, 1, 1);

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
(12, 'Charizma', 'Laogan', 'allengaspar@gmail.com', '09675237582', 'Group Workplace Setup, Secure Version Control', 'YouTube Video Review', 'pending', '2026-07-03 01:15:44', NULL),
(13, 'Try 1', 'Try 2', 'pot036331@gmail.com', '09098046552', 'Secure Version Control', 'Advertisement Banner', 'approved', '2026-07-03 01:21:43', NULL);

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
(1, 'admin', 'admin@kiwidms.com', '$2y$10$zNkYu5.BgKbAFzsUgnZIpe6Pzy.TBm.2dg4gteJ6alhEigNtJBGWm', 'admin', 'active', '2026-06-29 16:25:18', NULL, 'light', NULL, 0),
(2, 'pat', 'pat@kiwidms.com', '$2y$10$zNkYu5.BgKbAFzsUgnZIpe6Pzy.TBm.2dg4gteJ6alhEigNtJBGWm', 'contributor', 'active', '2026-06-29 16:25:18', NULL, 'light', NULL, 0),
(3, 'cha', 'cha@kiwidms.com', '$2y$10$zNkYu5.BgKbAFzsUgnZIpe6Pzy.TBm.2dg4gteJ6alhEigNtJBGWm', 'casual', 'active', '2026-06-29 16:25:18', NULL, 'light', NULL, 0),
(4, 'Sir Jhon Rey', 'jr@gmail.com', '$2y$10$wipKX5iV2w95gd/YVgjHa.BMn73B.cUrF5VBmUn6E8r3SnLQ9RIuK', 'admin', 'active', '2026-06-29 17:21:49', NULL, 'light', NULL, 0),
(9, 'allen2', 'len.10212005@gmail.com', '$2y$10$9vokn8WN8g4zRQJxMq023eQfEATP9HyqPDghZ5y7a3njfpEQfofcq', 'casual', 'active', '2026-07-02 06:55:58', NULL, 'light', NULL, 0),
(33, 'len9', 'allengabrielsilvagaspar@gmail.com', '$2y$10$kqTqnAVqbiFq2YihQkbqh.8Oqkpl.uNYd9Y.K3Lo263N8R1mnq6Hu', 'casual', 'active', '2026-07-02 08:55:03', NULL, 'light', NULL, 0),
(34, 'patricia10', 'pagod5841@gmail.com', '$2y$10$7ySnP/IuYQzJgV5hN1RxTOJxM/okRGlbyJKawumXf.ssPMorCKSJW', 'casual', 'active', '2026-07-02 09:14:23', NULL, 'light', NULL, 0),
(35, 'patty11', 'patriciavernicerebosura77@gmail.com', '$2y$10$2FdDW54.ANErwL2zn90QqOwUVll1d7R39alwtJJqmHWj8WwE3rDUK', 'casual', 'active', '2026-07-03 09:11:27', NULL, 'light', NULL, 0),
(36, 'try 113', 'pot036331@gmail.com', '$2y$10$Dft0i8.AuakjOyOB.m9joe6LbtuYoh8oYlLyxrtBuJ6dABtWaG8AS', 'contributor', 'active', '2026-07-03 09:22:08', NULL, 'light', NULL, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=616;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `document_shares`
--
ALTER TABLE `document_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `folder_shares`
--
ALTER TABLE `folder_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `org_settings`
--
ALTER TABLE `org_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registration_requests`
--
ALTER TABLE `registration_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
