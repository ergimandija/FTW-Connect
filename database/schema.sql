-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 22, 2026 at 09:54 AM
-- Server version: 8.0.26-16
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `h117701_ftw_connect_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pinned_message_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `name`, `description`, `picture`, `created_at`, `pinned_message_id`) VALUES
(1, NULL, NULL, NULL, '2026-04-10 12:48:50', NULL),
(2, NULL, NULL, NULL, '2026-04-10 13:27:06', NULL),
(3, 'TestGroup', 'A Testgroup', 'assets/img/groups/grp_69e7daccd23be0.69130211.png', '2026-04-21 20:15:08', NULL),
(4, 'The Admin Group', 'this is the group with the adminsss', 'assets/img/groups/grp_69ff38ef102bb2.66948690.jpg', '2026-05-09 13:38:56', NULL),
(5, 'Backlog', 'desc', NULL, '2026-05-12 12:22:09', NULL),
(6, 'group11', '', NULL, '2026-05-26 16:37:52', NULL),
(7, NULL, NULL, NULL, '2026-06-08 21:43:33', NULL),
(9, NULL, NULL, NULL, '2026-06-08 21:47:46', NULL),
(10, NULL, NULL, NULL, '2026-06-08 21:49:20', NULL),
(11, NULL, NULL, NULL, '2026-06-09 13:17:31', NULL),
(12, 'test1', 'test', NULL, '2026-06-09 14:18:44', NULL),
(13, NULL, NULL, NULL, '2026-06-19 13:24:18', NULL),
(14, 'TheTestGroup', 'a test group', 'assets/img/groups/grp_6a354ab75fe876.32038270.png', '2026-06-19 13:57:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat_user`
--

CREATE TABLE `chat_user` (
  `chat_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT '0',
  `nickname` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `chat_user`
--

INSERT INTO `chat_user` (`chat_id`, `user_id`, `role`, `type`, `archived`, `nickname`) VALUES
(1, 1, NULL, 'private', 0, NULL),
(1, 2, NULL, 'private', 0, NULL),
(2, 1, NULL, 'private', 0, NULL),
(2, 3, NULL, 'private', 0, NULL),
(3, 4, 'admin', 'group', 0, NULL),
(4, 1, 'admin', 'group', 0, NULL),
(5, 1, 'admin', 'group', 0, NULL),
(6, 13, 'admin', 'group', 0, NULL),
(7, 1, NULL, 'private', 0, NULL),
(7, 15, NULL, 'private', 0, NULL),
(9, 2, NULL, 'private', 0, NULL),
(9, 15, NULL, 'private', 0, NULL),
(10, 3, NULL, 'private', 0, NULL),
(10, 15, NULL, 'private', 0, NULL),
(11, 6, NULL, 'private', 0, NULL),
(11, 16, NULL, 'private', 0, NULL),
(12, 18, 'admin', 'group', 0, NULL),
(12, 19, 'member', 'group', 0, NULL),
(13, 22, NULL, 'private', 0, NULL),
(13, 30, NULL, 'private', 0, NULL),
(14, 1, 'admin', 'group', 0, NULL),
(14, 2, 'member', 'group', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  `post_id` int DEFAULT NULL,
  `parent_comment_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `content`, `created_at`, `user_id`, `post_id`, `parent_comment_id`) VALUES
(2, 'TestTheTest', '2026-04-28 11:13:28', 6, 3, NULL),
(4, 'this is also a test', '2026-04-28 16:49:53', 6, 3, 2),
(6, 'and i am commenting on it', '2026-05-09 12:56:49', 4, 6, NULL),
(7, 'i am also commenting right now', '2026-05-09 12:57:00', 4, 6, 6),
(8, 'i am trying to comment', '2026-05-09 12:58:09', 4, 6, NULL),
(19, 'I like to comment', '2026-05-12 16:42:31', 6, 7, NULL),
(20, 'and to speak to myself', '2026-05-12 16:42:42', 6, 7, 19),
(21, 'I like to comment multiple times', '2026-05-12 16:42:53', 6, 7, NULL),
(22, 'the post simply goes through but the file was rejected', '2026-05-26 13:27:47', 6, 8, NULL),
(23, 'the uploaded file with unaccepted mime type wasnt saved in the directory for post images and the image path attribute in the database is NULL. Test OK', '2026-05-26 13:45:45', 6, 8, 22),
(24, 'i am commenting on your post', '2026-06-09 11:09:32', 16, 6, 6),
(25, 'i am testing something', '2026-06-09 16:28:45', 6, 3, NULL),
(26, 'i liked ur post it is a very good one', '2026-06-09 16:29:44', 6, 2, NULL),
(30, 'why tho', '2026-06-10 15:51:57', 16, 17, NULL),
(31, 'yeah why', '2026-06-19 13:21:02', 1, 17, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_message`
--

CREATE TABLE `contact_message` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `contact_message`
--

INSERT INTO `contact_message` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'peibzhrg', 'ouezrbfg@eoizrtoz.com', 'gwt8pzözore', 'froezbhp', '2026-06-09 14:12:33'),
(2, 'adrian', 'pilleradrian0330@gmail.com', 'hi', 'hi me', '2026-06-09 17:21:20'),
(3, 'Adrian Piller', 'pilleradrian0330@gmail.com', 'hi', 'hello there', '2026-06-09 17:25:13'),
(4, 'pilleradrian', 'test@test.com', 'hi 3', 'hello there me again', '2026-06-09 17:28:24'),
(5, 'adrian', 'adrian@example.com', 'hellllloooo', 'test', '2026-06-09 17:32:13'),
(6, 'adrian', 'adrian@example.com', 'hellllloooo', 'test', '2026-06-09 17:35:29');

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

CREATE TABLE `email_verifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `Invitation`
--

CREATE TABLE `Invitation` (
  `id` int NOT NULL,
  `chat_id` int NOT NULL,
  `invited_by` int NOT NULL,
  `invited_user` int NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `Invitation`
--

INSERT INTO `Invitation` (`id`, `chat_id`, `invited_by`, `invited_user`, `status`, `created_at`) VALUES
(1, 12, 18, 19, 'accepted', '2026-06-09 14:28:49'),
(2, 14, 1, 2, 'accepted', '2026-06-19 13:57:27'),
(3, 14, 1, 3, 'pending', '2026-06-19 13:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int NOT NULL,
  `chat_id` int DEFAULT NULL,
  `sender_id` int DEFAULT NULL,
  `content` varbinary(4096) DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `picturePath` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `chat_id`, `sender_id`, `content`, `sent_at`, `picturePath`) VALUES
(30, 1, 1, 0x7764496a6469514a4a466b4d484342716b783070344364712f31694e39582b597a55786759417639774f526e6973645454717647364158387865356669435168, '2026-05-10 19:55:59', NULL),
(31, 1, 1, 0x6373677431424e546f586b5532323833544963767171362f2b6a42464f6e4a5348303173535969474f333634324c5048574376313738594d534c612f694b514b, '2026-05-10 20:03:58', NULL),
(32, 1, 1, 0x784168496e5248424743345661666778595038507061507044476f564a4c6338646a536578694e68305432365756504f613338384b7848624a4c565135446531, '2026-05-10 20:04:15', NULL),
(33, 1, 2, 0x3833496d5132685a4165444f30302b354c6161534151346c626c4f5832375853337051614c576e314d2b46736553584145614d684b4468534b774f697a645564, '2026-05-10 20:05:33', NULL),
(34, 1, 1, 0x496b64525758524d7a79542f4e755952704d72704c517467324956784d3243583273413443666f63726f773d, '2026-05-17 17:17:09', NULL),
(35, 1, 2, 0x574549596f7831717156412b665174726449534b6444334e50664c50454b347548646e575250576753584d3d, '2026-05-17 17:18:14', NULL),
(36, 1, 1, 0x2b6b44464434344756754571536d43786450726a64547837374379592f766678454f6f4444674d4b6f6157586264466e50337433706c694b5130586379427934, '2026-05-17 17:57:16', NULL),
(37, 2, 1, 0x3071623454717241426444794279394c5a7a2f774c5a4564456e76305148703942572f5a317a754857566b3d, '2026-05-17 18:06:07', NULL),
(39, 1, 1, 0x2b33767563676f627343356573396b57694d5a566762576b4c506430344f3845336b6a69363679594f757353734f6b57766553303258702b4c41344e57397564303046475554465052544e37472f5567336179674b42734c3738566d3342683744723759367254497365553d, '2026-05-23 21:12:13', NULL),
(41, 1, 1, 0x62756570447266366c6777304e643733354b473852684c7a30364b6531467241716f443054586f397735413d, '2026-06-03 08:06:58', NULL),
(42, 1, 1, 0x3772446663364e46567053756736427a7445764547635a72647153447474695273306246353448466837673d, '2026-06-07 14:23:58', NULL),
(43, 1, 1, 0x6b465970357779313751364d514a4e47793862446461502f733137482b36594c37713248756e61373079303d, '2026-06-07 14:25:11', NULL),
(44, 1, 1, 0x6f7243416b616c76624c4d692f39437a79575438765573336d57634b6d374474547770774a7a6636316f593d, '2026-06-07 14:25:49', NULL),
(45, 1, 1, 0x6d63675173564b513251716663715777665231305a753173726e73475a2b574d6c67626d625541724659553d, '2026-06-07 14:26:08', NULL),
(46, 1, 1, 0x4e7a46775045754737524366306d68574f4e4e4158445964635976452b326a4e636e4d34437743344846513d, '2026-06-07 14:26:29', 'its fine.jpg'),
(47, 1, 1, 0x3172786f6a374e6255527a67446c4e31434d306c44456f655041344754614a386979626575774f546e6e513d, '2026-06-07 14:43:11', 'its fine.jpg'),
(48, 1, 1, 0x617934584746545253556d4955494f59336d564b4e4e6b675830333862767a35614c504b7354516d694e77482b71594a737a32507a354c56586c7537434f4247, '2026-06-07 14:47:00', 'hazardStripe.png'),
(49, 1, 1, 0x6f566e516377334a7a766b666e6d4750553344454f6f2b426a543543634d522f78695a436c482f6b75316a6365307a5a6c79445530724a5534554736514e5472, '2026-06-07 14:49:54', 'hazardStripe.png'),
(50, 1, 1, 0x30395948574d61586875446844543079374c517644495272314f636758392f62575a5a4d307273476753343d, '2026-06-07 14:51:27', 'dj_icon.png'),
(51, 1, 1, 0x73542f51746a6142466145713166517052747573613173365370696647507775794f2f33413744507878733d, '2026-06-07 14:52:00', 'its fine.jpg'),
(52, 1, 1, 0x6973794a6a5875324b544677636a7a79432f427a37442f63636d552f613568563251432f66363070334149704445754c517438484f6b6264776c7871794b5159, '2026-06-07 14:52:31', 'its fine.jpg'),
(53, 1, 1, 0x76524e684178765a6a2f306465643236565877524f5a2b426a65527a2b787239596174764c366d735048464348726e3870646d7961703234756c694a37333452, '2026-06-07 14:52:59', NULL),
(54, 1, 1, 0x39356e5563753978755961586634656c2b456b48687a4f366453624c46777454476a56637a5063635a54633d, '2026-06-07 15:01:31', 'its fine.jpg'),
(55, 7, 15, 0x517053633862656a4b41445a654f4e754d46554c4432543361466561337a385556395477342f7a68337a453d, '2026-06-08 21:47:17', NULL),
(56, 10, 15, 0x686b555a356d7a35796c4b53574b7742464b6f41546c7838614d4549576b6258355067426a5a67527650493d, '2026-06-08 21:49:24', NULL),
(57, 11, 16, 0x427545376d77682f6b544a7a654d476f6f74313833374949507a5a524c51416d4f486a78494b30306452453d, '2026-06-09 13:17:38', NULL),
(58, 11, 6, 0x73464b556c333934363363786846366e70614c73745466686268646a4b6e46333976504c62665468685a633d, '2026-06-09 13:44:54', NULL),
(59, 12, 18, 0x536c6137486a61674e342b66694c73436d44737733706856456739787072587839473159456a584837674d3d, '2026-06-09 14:25:54', NULL),
(60, 13, 30, 0x6178566137332b7551655151332b7371556a4a31475a714771494172534b6a2b776438756e7137486161513d, '2026-06-19 13:24:23', NULL),
(61, 4, 1, 0x694c516b2f5667524b59714d385244445034787575335053666e304b317233577770643652476c524550733d, '2026-06-19 13:49:48', NULL),
(62, 4, 1, 0x744a31615246424a6477674730784c6a506d654d6949393668446b4f6c53306c2f4864634f424a48476772346f747036736537476734497950694150754c4b75376158632b57614164734e4b544b54616e526d6c4d6f4c65446b774c39656f484e6e46695537326d726f6f3d, '2026-06-19 13:49:58', NULL),
(63, 14, 1, 0x724b754a63494e69484b41506c6341596e4d574469566f44344e63304f2f445668614e6f6731364d644e6d6f4b66304c5a5a4637437754642f7a434b34685234, '2026-06-19 13:58:04', NULL),
(64, 1, 1, 0x6a66426d4f6177336b744c2b593944574f7336656265506247414661685975387657525166335355514d733d, '2026-06-19 14:36:20', NULL),
(65, 1, 1, 0x5954464432616438537a682b64587632616644496b5a4b62316a7278664148544e496f5a525033755435593d, '2026-06-19 15:02:24', NULL),
(66, 1, 2, 0x7679334f4b42447976566556494e70585169424870756346326e624c75713555564d7163776761686f634175616d4e4e49676e2f786a665978416e5a56596842, '2026-06-19 20:13:15', NULL),
(67, 1, 1, 0x302b4b6d586f76635a6272432f777a632b57586f3631465a4e56736b314175626d375864587064635a59375471546b386b6c6b37637061476378646355657a61, '2026-06-19 20:13:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `message_reaction`
--

CREATE TABLE `message_reaction` (
  `message_id` int NOT NULL,
  `user_id` int NOT NULL,
  `emoji` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `message_reaction`
--

INSERT INTO `message_reaction` (`message_id`, `user_id`, `emoji`) VALUES
(59, 19, '❤️');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `title`, `content`, `image_url`, `created_at`, `user_id`) VALUES
(1, 'Post', 'first post', 'assets/img/posts/1776805775_madelyn.jpg', '2026-04-21 21:09:35', 6),
(2, 'Post', 'first post', 'assets/img/posts/1776806462_madelyn.jpg', '2026-04-21 21:21:02', 6),
(3, 'Post', 'Test', 'assets/uploads/posts/post_69e7f021173118.63348803.jpg', '2026-04-21 21:46:09', 6),
(4, 'Post', 'test2', 'assets/uploads/posts/post_69e7f07655c001.28472426.png', '2026-04-21 21:47:34', 6),
(6, 'Post', 'I am doing a test post', NULL, '2026-05-09 12:53:44', 4),
(7, 'First Post with title', 'I am creating the first post that has a title', 'assets/uploads/posts/post_6a03586b9d97f0.64773232.png', '2026-05-12 16:42:19', 6),
(8, 'test post with wrong file', 'i am testing the file validation', NULL, '2026-05-26 13:27:04', 6),
(13, 'test', 'test', NULL, '2026-06-09 15:52:17', 22),
(14, '10th post', 'i need 10 posts to test the new \"show more\"-button', NULL, '2026-06-09 16:00:54', 6),
(15, '11th post', 'i need one more tho', NULL, '2026-06-09 16:01:15', 6),
(16, 'this is the real 11th post', 'the other one was disguised as the 11th but was the 10th in reality', NULL, '2026-06-09 16:02:44', 6),
(17, 'post (joke)', 'i need a post with the word post', NULL, '2026-06-09 16:26:09', 6),
(20, 'This is my first Post', 'Have you ever though about thinking?', 'assets/uploads/posts/post_6a354223af3141.63345356.jpeg', '2026-06-19 13:20:36', 1);

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(32, 7, 6, '2026-05-26 13:26:20'),
(33, 6, 6, '2026-05-26 15:10:37'),
(34, 7, 16, '2026-06-09 11:09:09'),
(36, 2, 6, '2026-06-09 16:29:33'),
(38, 1, 6, '2026-06-09 16:46:07'),
(39, 17, 16, '2026-06-10 15:51:53'),
(40, 20, 1, '2026-06-19 13:20:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pwdHash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `profilePicturePath` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `failed_attempts` int DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `public_key` text,
  `last_seen` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `pwdHash`, `role`, `status`, `profilePicturePath`, `reset_token`, `reset_expires`, `failed_attempts`, `locked_until`, `created_at`, `public_key`, `last_seen`) VALUES
(1, 'The Tester', 'test1@test.com', '$2a$12$wdzRg82i0C9jAZYNSzLT5ePwQ1HJ35uRNfaQuPACvrBpXHyoVc7ty', 'user', 'active', 'assets/uploads/profiles/user_1_1779561987.jpg', NULL, NULL, 0, NULL, '2026-04-10 12:24:27', 'BFiP8nak1eadMDQTtuBumy/SX0aJ0QRZranfCIrdrKGsaZq5aKco+ACKJYZDz7mOGP2EmqgZihZlFmaoz00O01s=', '2026-06-19 20:11:43'),
(2, 'Test2', 'test2@gmail.com', '$2a$12$TI2XvADLwROfrJBR7KOFQeadphnLGP/ILfWqX2xMhzfSAYV30G9Ra', 'user', 'active', NULL, NULL, NULL, 0, NULL, '2026-04-10 12:41:40', NULL, '2026-06-19 20:12:55'),
(3, 'Test3', 'test3@gmail.com', '$2a$12$wdzRg82i0C9jAZYNSzLT5ePwQ1HJ35uRNfaQuPACvrBpXHyoVc7ty', 'user', 'active', NULL, NULL, NULL, 0, NULL, '2026-04-10 13:23:39', NULL, '2026-06-09 08:21:43'),
(4, 'Ergi Mandija', 'ergi.mandija@outlook.com', '$2y$10$13BqjHGUDBWS8PrvFTKBYOhoROeLwqNpoJJfdR/DBBnb5QBuziIKW', NULL, 'active', NULL, NULL, NULL, 1, NULL, '2026-04-14 15:33:21', NULL, '2026-06-09 14:49:25'),
(6, 'Frank', 'frankvoithofer@hotmail.com', '$2y$10$MgLD21gaTCPHV8xOhegJ1e1ytXrYwgTwvP3oyr1TnNHnb42K5oE.e', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-04-21 21:08:58', NULL, '2026-06-21 22:08:58'),
(9, 'adrian', 'adrian@example.com', '$2y$10$RmO06K63UCiRkpA66lShKeeR7R/TDFxgJOBIT/heb2QSkRgpImakS', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-05-17 00:37:29', NULL, '2026-06-09 14:49:26'),
(10, 'adrian1', 'adrian1@example.com', '$2y$10$vGPpp32gH8tmXX7DJONCAOhaYViTcmdvrguylcGHWzDkP./wLIT62', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-05-17 00:39:44', NULL, '2026-06-09 14:49:25'),
(13, 'adrian', 'adrian@example.at', '$2y$10$/YNd8XroGInZtwOkeGCeLOt0AQNU9lPpfJP.fVDNA1mAihA/FKOFe', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-05-26 16:29:10', NULL, '2026-06-09 14:49:26'),
(15, 'if25b032', 'if25b032@technikum-wient.at', '$2y$10$KqeZfENe8FVw3bAgIguVZ.BW5oHdhS0YWAIreqJa6PYunNCnUvEHa', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-06-08 21:42:20', NULL, '2026-06-09 14:49:26'),
(16, 'Leonie Mitterhauser', 'if25b033@technikum-wien.at', '$2y$10$5zRez7mvV4/kbylwtkoaROKjsDLOnNVoCY0lRoRMxZ3qeevoT1OWu', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-06-09 11:08:12', NULL, '2026-06-09 14:49:26'),
(18, 'test', 'test@test.com', '$2y$10$XbXIPrsgFbKkqJ4KeQ33juiYmOyni.NIbReCt.DRLSuQZ/6DESJpW', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-06-09 14:16:11', NULL, '2026-06-09 14:49:26'),
(19, 'test2', 'test2@test.com', '$2y$10$dVFvVjw.Qov0ByhoWOXWnutIDYCOBl2I.KLbOWfvUKcfp3tgmsmzS', NULL, 'active', NULL, NULL, NULL, 1, NULL, '2026-06-09 14:26:27', NULL, '2026-06-19 13:58:24'),
(22, 'David', 'sivare4019@bncinema.com', '$2y$12$WqHurW59EBVoB6.WGtXeX..MkiiwGZQviy9J0p8Sz9ewmKnePxKvm', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-06-09 15:04:08', NULL, '2026-06-09 15:06:20'),
(27, 'test', 'bhk18990@laoia.com', '$2y$12$4EBTtLU3RkDzvg4jemF9f.gtcGUrTGymdiU2hQjigs/uXVkW.CtzO', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-06-09 15:19:49', NULL, '2026-06-09 15:20:21'),
(28, 'test', 'diliveg519@brixozu.com', '$2y$12$Xs44I520GxGgmL8Pfzj8ReXsV6ID3cQwrBV6Mc1BPId4B9xO.Ne72', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-06-09 15:20:41', NULL, '2026-06-21 16:18:36'),
(29, 'test', 'heresem183@brixozu.com', '$2y$12$EJSXZ0Elu.pi/OlxVSb3tuiEzOKQBNzHlqxNvPfGaVYkNnh774ZI6', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-06-09 15:26:45', NULL, '2026-06-09 15:26:51'),
(30, 'Ergi Mandija', 'ergimandija1@gmail.com', '$2y$10$WHU7/4Z/3OS1.UXWbngL/uLei/vcmIDx32Xuhgf0j238EzlBE5mau', NULL, 'active', NULL, NULL, NULL, 0, NULL, '2026-06-19 13:22:47', NULL, '2026-06-19 13:44:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_user`
--
ALTER TABLE `chat_user`
  ADD PRIMARY KEY (`chat_id`,`user_id`),
  ADD KEY `fkcuus` (`user_id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkcous` (`user_id`),
  ADD KEY `fkcopo` (`post_id`),
  ADD KEY `fk_comment_parent` (`parent_comment_id`);

--
-- Indexes for table `contact_message`
--
ALTER TABLE `contact_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_verifications_token_hash` (`token_hash`),
  ADD KEY `idx_email_verifications_user_id` (`user_id`);

--
-- Indexes for table `Invitation`
--
ALTER TABLE `Invitation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_invite` (`chat_id`,`invited_user`),
  ADD KEY `fkinvby` (`invited_by`),
  ADD KEY `fkinvuser` (`invited_user`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkmech` (`chat_id`),
  ADD KEY `fkmeus` (`sender_id`);

--
-- Indexes for table `message_reaction`
--
ALTER TABLE `message_reaction`
  ADD PRIMARY KEY (`message_id`,`user_id`,`emoji`),
  ADD KEY `fk_react_usr` (`user_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkpous` (`user_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `contact_message`
--
ALTER TABLE `contact_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Invitation`
--
ALTER TABLE `Invitation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_user`
--
ALTER TABLE `chat_user`
  ADD CONSTRAINT `fkcuch` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fkcuus` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `fk_comment_parent` FOREIGN KEY (`parent_comment_id`) REFERENCES `comment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fkcopo` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fkcous` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD CONSTRAINT `fk_email_verifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Invitation`
--
ALTER TABLE `Invitation`
  ADD CONSTRAINT `fkinchat` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fkinvby` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fkinvuser` FOREIGN KEY (`invited_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fkmech` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fkmeus` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message_reaction`
--
ALTER TABLE `message_reaction`
  ADD CONSTRAINT `fk_react_msg` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_react_usr` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fkpous` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
