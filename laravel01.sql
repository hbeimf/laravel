-- MySQL dump 10.13  Distrib 5.6.38, for Linux (x86_64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	5.6.38

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `c_login_log`
--

DROP TABLE IF EXISTS `c_login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID:users.id',
  `log_type` enum('login','logout') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'login' COMMENT '类别， login:登录账号。logout:退出账号',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='玩家登录日志';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c_login_log`
--

LOCK TABLES `c_login_log` WRITE;
/*!40000 ALTER TABLE `c_login_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `c_login_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c_money`
--

DROP TABLE IF EXISTS `c_money`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c_money` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID:users.id',
  `money` float(10,3) NOT NULL DEFAULT '0.000' COMMENT '余额',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_id` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='玩家余额表[钱包]';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c_money`
--

LOCK TABLES `c_money` WRITE;
/*!40000 ALTER TABLE `c_money` DISABLE KEYS */;
/*!40000 ALTER TABLE `c_money` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c_users`
--

DROP TABLE IF EXISTS `c_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户名称',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户邮箱',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '注册地址',
  `user_type` enum('client','simulation') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'simulation' COMMENT '账号类别, client:玩家真实账号。 simulation:玩家模拟账号。',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='玩家账号表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c_users`
--

LOCK TABLES `c_users` WRITE;
/*!40000 ALTER TABLE `c_users` DISABLE KEYS */;
INSERT INTO `c_users` VALUES (1,'test1','123456@qq.com','','$2y$10$MRGK4tHHyC4S8/6y3fXiT.SPW7JKhg3t4ImaX9ZWgxeuZ3S4pCeiW',NULL,'','simulation','2018-08-05 19:10:23','2018-08-05 19:10:23');
/*!40000 ALTER TABLE `c_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2016_06_01_000001_create_oauth_auth_codes_table',1),(4,'2016_06_01_000002_create_oauth_access_tokens_table',1),(5,'2016_06_01_000003_create_oauth_refresh_tokens_table',1),(6,'2016_06_01_000004_create_oauth_clients_table',1),(7,'2016_06_01_000005_create_oauth_personal_access_clients_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_access_tokens`
--

LOCK TABLES `oauth_access_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_access_tokens` DISABLE KEYS */;
INSERT INTO `oauth_access_tokens` VALUES ('00af0d47de0b49a40e3dd9b907fea2baf23e4a8ada4df28a72afc68cf43e8aad4b0ae9749a63da99',1,3,'MyApp','[]',0,'2018-08-05 19:26:38','2018-08-05 19:26:38','2019-08-06 03:26:38'),('02417e67289283da9456fc6942edd733294d5e32e3dd03395ebe7c2ac15864affd975a5f1fa21bdb',1,3,'MyApp','[]',0,'2018-08-05 19:37:51','2018-08-05 19:37:51','2019-08-06 03:37:51'),('07924fd622af125c1a27fd2b6311fbb6eb83111cc96328551f02144894c449786be67b00cc430091',1,3,'MyApp','[]',0,'2018-08-05 19:33:34','2018-08-05 19:33:34','2019-08-06 03:33:34'),('1e2bf94a09503d65e4f87adc3bb6921587c3f2df3822250a5ce037357414fccb388e81299bb7f7c7',1,3,'MyApp','[]',0,'2018-08-05 19:44:29','2018-08-05 19:44:29','2019-08-06 03:44:29'),('22e35523c4d0329d7301c3b7e4f0b43fcffccf0cd81ccb41dfcb43b5f6723f2174e1b734c0141b5d',1,3,'MyApp','[]',0,'2018-08-05 19:14:29','2018-08-05 19:14:29','2019-08-06 03:14:29'),('31b0bd1f9c817dd70dafa7221113fce91f35f1417e213ded3669ee13558063ffa5e2d9db94d7a1b2',1,3,'MyApp','[]',0,'2018-08-05 19:41:09','2018-08-05 19:41:09','2019-08-06 03:41:09'),('35f7a7d5c86f88959d7f58c7b136f402d20150e5165e17fc3f1abdedc52b64e4c478dbe259b71f10',1,3,'MyApp','[]',0,'2018-08-05 19:38:35','2018-08-05 19:38:35','2019-08-06 03:38:35'),('3dc2204b95a421d3d645a46c7f6e51cca0dee7ee5f03314e40c0121ee6be4d0a014d34acf2486412',1,3,'MyApp','[]',0,'2018-08-05 17:38:34','2018-08-05 17:38:34','2019-08-06 01:38:34'),('43a07e6745dc919ab4fd7781e9173b76a4ab6d753772c06361bd51f5b823031bdd1585265bd60c55',1,3,'MyApp','[]',0,'2018-08-05 20:08:42','2018-08-05 20:08:42','2019-08-06 04:08:42'),('48e4945541f67207ba49217e02dd1d35c2d6c19a58571e7647df0aefd30424dd31bfd8654cc92ea5',1,3,'MyApp','[]',0,'2018-08-05 19:31:56','2018-08-05 19:31:56','2019-08-06 03:31:56'),('4bbc00dd44aca1d919aaafcb2e4bbbf1ad54bc59c299d339e97964fac8d52b1920ea2cc005140052',1,3,'MyApp','[]',0,'2018-08-05 19:44:43','2018-08-05 19:44:43','2019-08-06 03:44:43'),('5282b5c6f02510d1f6d0f3277c390aa4002f356f2e1f2614fde1e3202eb8cd3e1d03eea8348ac6b2',1,3,'MyApp','[]',0,'2018-08-05 20:10:59','2018-08-05 20:10:59','2019-08-06 04:10:59'),('570c0918243565ca6ac2064cf1a8072c8cbe8480fe7a9b3b80a8b3b709ec17ae93b762db6617ac4b',1,3,'MyApp','[]',0,'2018-08-05 19:15:48','2018-08-05 19:15:48','2019-08-06 03:15:48'),('585eb06675b48c4c47373a88030be2603e3709596f43c3b0e90bfad767f2cdc20b4a075ea75a0d76',1,3,'MyApp','[]',0,'2018-08-05 19:31:24','2018-08-05 19:31:24','2019-08-06 03:31:24'),('587327bc7939286a4c2d5ecaf85a6442e1967bc3fa789ac869c675c8c1c63b267ad93a292377156f',1,3,'MyApp','[]',0,'2018-08-05 19:38:13','2018-08-05 19:38:13','2019-08-06 03:38:13'),('6bf9fc08888b0f28dfb42a32e0dd97f8ebfd79306a2cd0182ce8bb98518c9150a9cee0cbbfb57434',1,3,'MyApp','[]',0,'2018-08-05 19:35:56','2018-08-05 19:35:56','2019-08-06 03:35:56'),('758c5d838303478f7a6a5ac03ee48fe50845abccc557f354a66603ccfa53c7c5e1c1f190de6fb784',1,3,'MyApp','[]',0,'2018-08-05 19:39:37','2018-08-05 19:39:37','2019-08-06 03:39:37'),('7b76373c821a3f078f56ab3ff77848479a5592ae6405dfa13541cb26340b99a5b4a80cb2f058681c',1,3,'MyApp','[]',0,'2018-08-05 18:55:17','2018-08-05 18:55:17','2019-08-06 02:55:17'),('8915a44e3e7d1ac558c6075ae8348bd3fdf2abbd80b6315fcffcafe681820a740bd9e818b3ff5e96',1,3,'MyApp','[]',0,'2018-08-05 19:30:06','2018-08-05 19:30:06','2019-08-06 03:30:06'),('8ceba8c7a1ffb92995c7235522a347d2e3b6d798557743155effb60135d1ebabfee1852dcbb6a2a5',1,3,'MyApp','[]',0,'2018-08-05 19:27:38','2018-08-05 19:27:38','2019-08-06 03:27:38'),('9127fccf0ef6821afb647dac91a009d7ed323c6338db72eee6e970009f39437b61759448f21fac43',1,3,'MyApp','[]',0,'2018-08-05 19:36:52','2018-08-05 19:36:52','2019-08-06 03:36:52'),('a29efe7c102f1e2bec9e560f7984826631bf72f9af9e354acb88dfa6a72553ffce42152dd5106d90',1,3,'MyApp','[]',0,'2018-08-05 19:27:19','2018-08-05 19:27:19','2019-08-06 03:27:19'),('c242e2ceb4f36787405b34676156c7da2f314add6752dbf75d26d83f9e4ef6de204013f8b59a61d8',1,3,'MyApp','[]',0,'2018-08-05 19:34:21','2018-08-05 19:34:21','2019-08-06 03:34:21'),('c9c0b929b772acbb8496d3a6584409437c6238e3ffd9e556d3b97d2b33ccddf1cc0a67fb52c3dd67',1,3,'MyApp','[]',0,'2018-08-05 19:27:52','2018-08-05 19:27:52','2019-08-06 03:27:52'),('ca0ceb5c8b48571e8669877a98c6cddce6097d4fee8a4ac274edbf3a2b01b19f782441c8145f1dcb',1,3,'MyApp','[]',0,'2018-08-05 19:10:23','2018-08-05 19:10:23','2019-08-06 03:10:23'),('d7c2f238dc0fcd2c96fb4d2af4b8a62e01472625cd1bd09fa4169e4519b536e4289853a48747f2c8',1,3,'MyApp','[]',0,'2018-08-05 19:39:52','2018-08-05 19:39:52','2019-08-06 03:39:52'),('efcc8b3402a604109840793c60afc3b61b7c5c6a2988333d38a6ab8f31c2b939b753c3dfda911c64',1,3,'MyApp','[]',0,'2018-08-05 19:18:19','2018-08-05 19:18:19','2019-08-06 03:18:19'),('f3146ab66c86bdd1e0e6a2be80d4b9ecb849d420049e7f533ab6c322992755ff8fdc13d4fa32054e',1,3,'MyApp','[]',0,'2018-08-05 20:08:08','2018-08-05 20:08:08','2019-08-06 04:08:08'),('f352ef39e20440c73807a77ea0e42d8bd0634d81ade70e9184a9fb568dbaec389c8eee1f57763044',1,3,'MyApp','[]',0,'2018-08-05 19:19:36','2018-08-05 19:19:36','2019-08-06 03:19:36'),('f5a877fd9862b92674f34de01afb3a65773ddb67c9502272eebfb46918c4b4e95edeb2eb1a725449',1,3,'MyApp','[]',0,'2018-08-05 19:37:15','2018-08-05 19:37:15','2019-08-06 03:37:15'),('faf0903a6dd83eb361dc1bfc909c6abe445bb94eb029bf314532bbb68a5d3f16100db0d61b9222ac',1,3,'MyApp','[]',0,'2018-08-05 19:33:34','2018-08-05 19:33:34','2019-08-06 03:33:34');
/*!40000 ALTER TABLE `oauth_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_auth_codes`
--

LOCK TABLES `oauth_auth_codes` WRITE;
/*!40000 ALTER TABLE `oauth_auth_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_auth_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_clients`
--

LOCK TABLES `oauth_clients` WRITE;
/*!40000 ALTER TABLE `oauth_clients` DISABLE KEYS */;
INSERT INTO `oauth_clients` VALUES (1,NULL,'Laravel Personal Access Client','2JZI9NKcNnrotlgjO6SDJSlbBO0k8sJXbGD9HqBN','http://localhost',1,0,0,'2018-08-03 22:32:34','2018-08-03 22:32:34'),(2,NULL,'Laravel Password Grant Client','4ohPVctyJ69nZkqfujAsRIj70obrfUFnPadb1Q4w','http://localhost',0,1,0,'2018-08-03 22:32:34','2018-08-03 22:32:34'),(3,NULL,'Laravel Personal Access Client','qs1meU9IBWk3S5DbWca39Go8qTbHPBz2IJzVQH8C','http://localhost',1,0,0,'2018-08-03 22:32:45','2018-08-03 22:32:45'),(4,NULL,'Laravel Password Grant Client','i2U7XScYO0hqGaV9e6vTX5w4QZzURl3vidUcTVGX','http://localhost',0,1,0,'2018-08-03 22:32:45','2018-08-03 22:32:45');
/*!40000 ALTER TABLE `oauth_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_personal_access_clients_client_id_index` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_personal_access_clients`
--

LOCK TABLES `oauth_personal_access_clients` WRITE;
/*!40000 ALTER TABLE `oauth_personal_access_clients` DISABLE KEYS */;
INSERT INTO `oauth_personal_access_clients` VALUES (1,1,'2018-08-03 22:32:34','2018-08-03 22:32:34'),(2,3,'2018-08-03 22:32:45','2018-08-03 22:32:45');
/*!40000 ALTER TABLE `oauth_personal_access_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_refresh_tokens`
--

LOCK TABLES `oauth_refresh_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sm_menu`
--

DROP TABLE IF EXISTS `sm_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级id',
  `menu_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称描述: 增加账号',
  `api_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '访问 api 链接名称：addAccount',
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态[1:启用, 0:禁用]',
  `note` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_api_name` (`api_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统管理导航表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sm_menu`
--

LOCK TABLES `sm_menu` WRITE;
/*!40000 ALTER TABLE `sm_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `sm_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sm_role`
--

DROP TABLE IF EXISTS `sm_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `menu_ids` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '对api的访问权限，{1,2,3}',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态[1:启用, 0:禁用]',
  `note` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统管理员角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sm_role`
--

LOCK TABLES `sm_role` WRITE;
/*!40000 ALTER TABLE `sm_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `sm_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sm_users`
--

DROP TABLE IF EXISTS `sm_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户名称',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户邮箱',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色: s_role.id',
  `nickname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态[1:启用, 0:禁用]',
  `note` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统管理员账号表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sm_users`
--

LOCK TABLES `sm_users` WRITE;
/*!40000 ALTER TABLE `sm_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `sm_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-06 12:14:17
