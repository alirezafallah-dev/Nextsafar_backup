-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: nextsafar_wp
-- ------------------------------------------------------
-- Server version	8.4.11-0ubuntu0.26.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ai_trip_plans`
--

DROP TABLE IF EXISTS `ns_ai_trip_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ai_trip_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `destination` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `days` int unsigned NOT NULL DEFAULT '3',
  `budget_level` enum('economy','medium','luxury') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'medium',
  `interests` json DEFAULT NULL,
  `travelers` int unsigned NOT NULL DEFAULT '2',
  `start_date` date DEFAULT NULL,
  `trip_title` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `trip_summary` text COLLATE utf8mb4_unicode_520_ci,
  `days_plan` json NOT NULL,
  `total_budget_min` bigint DEFAULT NULL,
  `total_budget_max` bigint DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT 'USD',
  `suggested_hotels` json DEFAULT NULL,
  `suggested_tours` json DEFAULT NULL,
  `suggested_restaurants` json DEFAULT NULL,
  `model_used` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `tokens_used` int unsigned DEFAULT '0',
  `generation_time_ms` int unsigned DEFAULT '0',
  `status` enum('pending','completed','failed','expired') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'pending',
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_520_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT NULL,
  `last_viewed_at` datetime DEFAULT NULL,
  `view_count` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_status` (`status`),
  KEY `idx_expires` (`expires_at`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ai_trip_plans`
--

LOCK TABLES `ns_ai_trip_plans` WRITE;
/*!40000 ALTER TABLE `ns_ai_trip_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ai_trip_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_api_sync_log`
--

DROP TABLE IF EXISTS `ns_api_sync_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_api_sync_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'searchapi',
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` enum('running','completed','failed','partial') COLLATE utf8mb4_unicode_520_ci DEFAULT 'running',
  `records_fetched` int DEFAULT '0',
  `records_created` int DEFAULT '0',
  `records_updated` int DEFAULT '0',
  `records_failed` int DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_520_ci,
  `started_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `source` (`source`),
  KEY `entity_type` (`entity_type`),
  KEY `status` (`status`),
  KEY `started_at` (`started_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_api_sync_log`
--

LOCK TABLES `ns_api_sync_log` WRITE;
/*!40000 ALTER TABLE `ns_api_sync_log` DISABLE KEYS */;
INSERT INTO `ns_api_sync_log` VALUES (1,'news_aggregator','travelnews','completed',0,0,0,0,NULL,'2026-10-08 20:35:33','2026-10-08 20:35:33');
/*!40000 ALTER TABLE `ns_api_sync_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_commentmeta`
--

DROP TABLE IF EXISTS `ns_commentmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_commentmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_commentmeta`
--

LOCK TABLES `ns_commentmeta` WRITE;
/*!40000 ALTER TABLE `ns_commentmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_commentmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_comments`
--

DROP TABLE IF EXISTS `ns_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_comments` (
  `comment_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_author_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_karma` int NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'comment',
  `comment_parent` bigint unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_comments`
--

LOCK TABLES `ns_comments` WRITE;
/*!40000 ALTER TABLE `ns_comments` DISABLE KEYS */;
INSERT INTO `ns_comments` VALUES (1,1,'A WordPress Commenter','wapuu@wordpress.example','https://wordpress.org/','','2026-10-08 20:01:53','2026-10-08 20:01:53','Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href=\"https://gravatar.com/\">Gravatar</a>.',0,'1','','comment',0,0);
/*!40000 ALTER TABLE `ns_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_hotel_availability`
--

DROP TABLE IF EXISTS `ns_hotel_availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_hotel_availability` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hotel_post_id` bigint unsigned DEFAULT NULL,
  `hotel_external_id` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `room_type` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT 'standard',
  `available` tinyint(1) DEFAULT '1',
  `rooms_left` int DEFAULT '0',
  `source` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT 'searchapi',
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hotel_post_id` (`hotel_post_id`),
  KEY `hotel_external_id` (`hotel_external_id`),
  KEY `check_in` (`check_in`),
  KEY `check_out` (`check_out`),
  KEY `expires_at` (`expires_at`),
  KEY `idx_post_dates` (`hotel_post_id`,`check_in`,`check_out`),
  KEY `idx_ext_dates` (`hotel_external_id`,`check_in`,`check_out`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_hotel_availability`
--

LOCK TABLES `ns_hotel_availability` WRITE;
/*!40000 ALTER TABLE `ns_hotel_availability` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_hotel_availability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_hotel_prices`
--

DROP TABLE IF EXISTS `ns_hotel_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_hotel_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hotel_post_id` bigint unsigned DEFAULT NULL COMMENT 'WordPress post ID (if hotel is in our database)',
  `hotel_external_id` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'External ID (for online hotels like Parto)',
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `room_type` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT 'standard',
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT 'USD',
  `source` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT 'searchapi',
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hotel_post_id` (`hotel_post_id`),
  KEY `hotel_external_id` (`hotel_external_id`),
  KEY `check_in` (`check_in`),
  KEY `check_out` (`check_out`),
  KEY `expires_at` (`expires_at`),
  KEY `idx_post_dates` (`hotel_post_id`,`check_in`,`check_out`),
  KEY `idx_ext_dates` (`hotel_external_id`,`check_in`,`check_out`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_hotel_prices`
--

LOCK TABLES `ns_hotel_prices` WRITE;
/*!40000 ALTER TABLE `ns_hotel_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_hotel_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_links`
--

DROP TABLE IF EXISTS `ns_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_links` (
  `link_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint unsigned NOT NULL DEFAULT '1',
  `link_rating` int NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `link_rss` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_links`
--

LOCK TABLES `ns_links` WRITE;
/*!40000 ALTER TABLE `ns_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_nextsafar_geo`
--

DROP TABLE IF EXISTS `ns_nextsafar_geo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_nextsafar_geo` (
  `post_id` bigint unsigned NOT NULL,
  `post_type` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `lat` decimal(10,8) NOT NULL DEFAULT '0.00000000',
  `lng` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `place_id` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_520_ci,
  `phone` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `website` varchar(500) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `external_id` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `source` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT 'manual',
  `last_sync` datetime DEFAULT NULL,
  `location_point` point NOT NULL /*!80003 SRID 0 */,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`post_id`),
  SPATIAL KEY `location_point` (`location_point`),
  KEY `post_type` (`post_type`),
  KEY `city` (`city`),
  KEY `country` (`country`),
  KEY `external_id` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_nextsafar_geo`
--

LOCK TABLES `ns_nextsafar_geo` WRITE;
/*!40000 ALTER TABLE `ns_nextsafar_geo` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_nextsafar_geo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_booking_documents`
--

DROP TABLE IF EXISTS `ns_ns_booking_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_booking_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `passenger_id` bigint unsigned NOT NULL,
  `document_type` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `document_label` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `attachment_id` bigint unsigned NOT NULL,
  `file_url` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_520_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_at` datetime DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_passenger_id` (`passenger_id`),
  KEY `idx_document_type` (`document_type`),
  KEY `idx_attachment_id` (`attachment_id`),
  KEY `idx_verified` (`verified`),
  CONSTRAINT `fk_doc_booking` FOREIGN KEY (`booking_id`) REFERENCES `ns_ns_bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doc_passenger` FOREIGN KEY (`passenger_id`) REFERENCES `ns_ns_booking_passengers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_booking_documents`
--

LOCK TABLES `ns_ns_booking_documents` WRITE;
/*!40000 ALTER TABLE `ns_ns_booking_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_booking_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_booking_passengers`
--

DROP TABLE IF EXISTS `ns_ns_booking_passengers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_booking_passengers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `type` enum('adult','child','infant') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'adult',
  `index_in_booking` int NOT NULL DEFAULT '0',
  `first_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `first_name_en` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `last_name_en` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `passport_number` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `nationality` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `passport_issue_date` date DEFAULT NULL,
  `passport_expiry_date` date DEFAULT NULL,
  `extra_data` longtext COLLATE utf8mb4_unicode_520_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_type` (`type`),
  KEY `idx_national_id` (`national_id`),
  KEY `idx_passport_number` (`passport_number`),
  CONSTRAINT `fk_passenger_booking` FOREIGN KEY (`booking_id`) REFERENCES `ns_ns_bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_booking_passengers`
--

LOCK TABLES `ns_ns_booking_passengers` WRITE;
/*!40000 ALTER TABLE `ns_ns_booking_passengers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_booking_passengers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_bookings`
--

DROP TABLE IF EXISTS `ns_ns_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `booking_type` enum('visa','hotel','flight','tour') COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `item_id` bigint unsigned NOT NULL DEFAULT '0',
  `status` enum('pending','awaiting_payment','paid','processing','completed','cancelled','refunded','failed') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'pending',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'IRR',
  `passenger_count` int NOT NULL DEFAULT '1',
  `passenger_info` longtext COLLATE utf8mb4_unicode_520_ci,
  `booking_data` longtext COLLATE utf8mb4_unicode_520_ci,
  `payment_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_520_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_booking_code` (`booking_code`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_booking_type` (`booking_type`),
  KEY `idx_status` (`status`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_bookings`
--

LOCK TABLES `ns_ns_bookings` WRITE;
/*!40000 ALTER TABLE `ns_ns_bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_exchange_rates`
--

DROP TABLE IF EXISTS `ns_ns_exchange_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_exchange_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `currency_code` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `currency_name_fa` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `rate_to_rial` decimal(15,2) NOT NULL DEFAULT '0.00',
  `source` enum('manual','auto','default') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'manual',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `valid_until` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_520_ci,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_currency` (`currency_code`),
  KEY `idx_active` (`is_active`),
  KEY `idx_valid_until` (`valid_until`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_exchange_rates`
--

LOCK TABLES `ns_ns_exchange_rates` WRITE;
/*!40000 ALTER TABLE `ns_ns_exchange_rates` DISABLE KEYS */;
INSERT INTO `ns_ns_exchange_rates` VALUES (1,'usd','دلار آمریکا',1050000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(3,'aed','درهم امارات',285000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(5,'eur','یورو',1150000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(7,'try','لیر ترکیه',30000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(9,'omr','ریال عمان',2730000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(11,'gbp','پوند انگلیس',1330000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(13,'cny','یوان چین',145000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(15,'inr','روپیه هند',12600.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(17,'thb','بات تایلند',29000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33'),(19,'myr','رینگیت مالزی',222000.00,'default',1,NULL,'نرخ پیش‌فرض اولیه',NULL,'2026-10-09 00:05:33','2026-10-09 00:05:33');
/*!40000 ALTER TABLE `ns_ns_exchange_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_favorites`
--

DROP TABLE IF EXISTS `ns_ns_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `object_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_type_obj` (`user_id`,`type`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_favorites`
--

LOCK TABLES `ns_ns_favorites` WRITE;
/*!40000 ALTER TABLE `ns_ns_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_hotel_matches`
--

DROP TABLE IF EXISTS `ns_ns_hotel_matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_hotel_matches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `site_post_id` bigint unsigned DEFAULT NULL,
  `provider` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `provider_property_id` varchar(190) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `provider_name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `confidence` decimal(3,2) NOT NULL DEFAULT '0.00',
  `match_method` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `last_seen_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_provider_prop` (`provider`,`provider_property_id`),
  KEY `idx_site` (`site_post_id`),
  KEY `idx_last_seen` (`last_seen_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_hotel_matches`
--

LOCK TABLES `ns_ns_hotel_matches` WRITE;
/*!40000 ALTER TABLE `ns_ns_hotel_matches` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_hotel_matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_news_ai_log`
--

DROP TABLE IF EXISTS `ns_ns_news_ai_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_news_ai_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint NOT NULL DEFAULT '0',
  `provider` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `model` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tokens_used` int NOT NULL DEFAULT '0',
  `cost_usd` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'success',
  `error_message` text COLLATE utf8mb4_unicode_520_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_news_ai_log`
--

LOCK TABLES `ns_ns_news_ai_log` WRITE;
/*!40000 ALTER TABLE `ns_ns_news_ai_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_news_ai_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_news_duplicates`
--

DROP TABLE IF EXISTS `ns_ns_news_duplicates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_news_duplicates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `url_hash` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `guid` text COLLATE utf8mb4_unicode_520_ci,
  `guid_hash` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `title_hash` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `fuzzy_hash` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `source_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `post_id` bigint NOT NULL DEFAULT '0',
  `fetched_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `url_hash` (`url_hash`),
  KEY `guid_hash` (`guid_hash`),
  KEY `title_hash` (`title_hash`),
  KEY `fuzzy_hash` (`fuzzy_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_news_duplicates`
--

LOCK TABLES `ns_ns_news_duplicates` WRITE;
/*!40000 ALTER TABLE `ns_ns_news_duplicates` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_news_duplicates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_news_filter_log`
--

DROP TABLE IF EXISTS `ns_ns_news_filter_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_news_filter_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `news_title` varchar(500) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `source_name` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `score` int DEFAULT '0',
  `decision` enum('publish','draft','delete') COLLATE utf8mb4_unicode_520_ci DEFAULT 'publish',
  `reason` text COLLATE utf8mb4_unicode_520_ci,
  `positive_matches` text COLLATE utf8mb4_unicode_520_ci,
  `negative_matches` text COLLATE utf8mb4_unicode_520_ci,
  `ai_checked` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_decision` (`decision`),
  KEY `idx_created` (`created_at`),
  KEY `idx_score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_news_filter_log`
--

LOCK TABLES `ns_ns_news_filter_log` WRITE;
/*!40000 ALTER TABLE `ns_ns_news_filter_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_news_filter_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_news_keywords`
--

DROP TABLE IF EXISTS `ns_ns_news_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_news_keywords` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'positive',
  `weight` int NOT NULL DEFAULT '10',
  `category` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'general',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=608 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_news_keywords`
--

LOCK TABLES `ns_ns_news_keywords` WRITE;
/*!40000 ALTER TABLE `ns_ns_news_keywords` DISABLE KEYS */;
INSERT INTO `ns_ns_news_keywords` VALUES (1,'گردشگری','positive',25,'core',1),(2,'سفر','positive',15,'core',1),(3,'تور','positive',20,'core',1),(4,'تور مسافرتی','positive',25,'core',1),(5,'مسافر','positive',15,'core',1),(6,'هتل','positive',20,'core',1),(7,'اقامت','positive',15,'core',1),(8,'ویلا','positive',20,'core',1),(9,'بوم‌گردی','positive',25,'eco',1),(10,'بومگردی','positive',20,'eco',1),(11,'طبیعت‌گردی','positive',20,'eco',1),(12,'طبیعتگردی','positive',15,'eco',1),(13,'گردشگری سلامت','positive',15,'health',1),(14,'صنعت گردشگری','positive',20,'industry',1),(15,'پرواز','positive',20,'transport',1),(16,'بلیط','positive',15,'transport',1),(17,'فرودگاه','positive',15,'transport',1),(18,'ایرلاین','positive',20,'transport',1),(19,'ساحل','positive',15,'nature',1),(20,'جزیره','positive',15,'nature',1),(21,'دریاچه','positive',15,'nature',1),(22,'جنگل','positive',10,'nature',1),(23,'موزه','positive',10,'culture',1),(24,'جاذبه','positive',20,'culture',1),(25,'جاذبه گردشگری','positive',25,'culture',1),(26,'ثبت ملی','positive',15,'culture',1),(27,'میراث فرهنگی','positive',20,'culture',1),(28,'توریست','positive',20,'core',1),(29,'توریسم','positive',25,'core',1),(30,'کوهنوردی','positive',15,'eco',1),(31,'کمپ','positive',10,'eco',1),(32,'چادر','positive',10,'eco',1),(33,'کاروان','positive',15,'transport',1),(34,'اقامتگاه','positive',15,'core',1),(35,'مهمان‌خانه','positive',15,'core',1),(36,'هاستل','positive',15,'core',1),(37,'ریزورت','positive',20,'core',1),(38,'مسافرت','positive',15,'core',1),(39,'رستوران','positive',10,'food',1),(40,'کافه','positive',10,'food',1),(41,'غذای محلی','positive',15,'food',1),(42,'سوغات','positive',10,'food',1),(43,'ویزا','positive',20,'core',1),(44,'پاسپورت','positive',15,'core',1),(45,'توریستی','positive',20,'core',1),(46,'سیاحتی','positive',20,'core',1),(47,'قطار','positive',15,'transport',1),(48,'اتوبوس','positive',10,'transport',1),(49,'کشتی کروز','positive',20,'cruise',1),(50,'کشتی','positive',10,'cruise',1),(51,'کروز','positive',20,'cruise',1),(52,'بند','positive',10,'cruise',1),(53,'اسکله','positive',10,'transport',1),(54,'ترمینال','positive',10,'transport',1),(55,'ایستگاه قطار','positive',15,'transport',1),(56,'تاکسی','positive',5,'transport',1),(57,'اجاره خودرو','positive',15,'transport',1),(58,'ترانسفر','positive',10,'transport',1),(59,'هتل بوتیک','positive',20,'boutique',1),(60,'هتل آپارتمان','positive',15,'core',1),(61,'سوئیت','positive',15,'core',1),(62,'پانسیون','positive',10,'core',1),(63,'متل','positive',10,'core',1),(64,'خانه روستایی','positive',15,'eco',1),(65,'کلبه','positive',15,'eco',1),(66,'کلبه جنگلی','positive',20,'eco',1),(67,'اقامتگاه بوم‌گردی','positive',25,'eco',1),(68,'کمپینگ','positive',15,'eco',1),(69,'کاروانسرا','positive',20,'heritage',1),(70,'هتل سنتی','positive',20,'heritage',1),(71,'خانه اجاره‌ای','positive',15,'core',1),(72,'ویلا اجاره‌ای','positive',20,'core',1),(73,'شمال','positive',10,'place',1),(74,'کیش','positive',15,'place',1),(75,'قشم','positive',15,'place',1),(76,'مشهد','positive',10,'place',1),(77,'اصفهان','positive',10,'place',1),(78,'شیراز','positive',10,'place',1),(79,'یزد','positive',10,'place',1),(80,'تبریز','positive',10,'place',1),(81,'رامسر','positive',15,'place',1),(82,'چابهار','positive',20,'place',1),(83,'همدان','positive',10,'place',1),(84,'کرمان','positive',10,'place',1),(85,'کرمانشاه','positive',10,'place',1),(86,'لرستان','positive',10,'place',1),(87,'کردستان','positive',10,'place',1),(88,'گیلان','positive',10,'place',1),(89,'مازندران','positive',10,'place',1),(90,'رشت','positive',10,'place',1),(91,'ساری','positive',10,'place',1),(92,'بندرعباس','positive',10,'place',1),(93,'اهواز','positive',10,'place',1),(94,'ارومیه','positive',10,'place',1),(95,'زنجان','positive',10,'place',1),(96,'اردبیل','positive',10,'place',1),(97,'سمنان','positive',10,'place',1),(98,'بوشهر','positive',10,'place',1),(99,'هرمز','positive',15,'place',1),(100,'لارستان','positive',15,'place',1),(101,'تخت جمشید','positive',25,'heritage',1),(102,'نقش جهان','positive',20,'heritage',1),(103,'پاسارگاد','positive',20,'heritage',1),(104,'تخت سلیمان','positive',20,'heritage',1),(105,'بام ایران','positive',15,'nature',1),(106,'دماوند','positive',15,'nature',1),(107,'سبلان','positive',15,'nature',1),(108,'علم‌الکوه','positive',15,'nature',1),(109,'کویر لوت','positive',20,'nature',1),(110,'کویر مصر','positive',15,'nature',1),(111,'جنگل‌های هیرکانی','positive',25,'nature',1),(112,'دبی','positive',15,'place',1),(113,'استانبول','positive',15,'place',1),(114,'آنتالیا','positive',15,'place',1),(115,'تفلیس','positive',15,'place',1),(116,'باکو','positive',15,'place',1),(117,'ارمنستان','positive',15,'place',1),(118,'تایلند','positive',15,'place',1),(119,'بالی','positive',15,'place',1),(120,'پاریس','positive',15,'place',1),(121,'لندن','positive',15,'place',1),(122,'رم','positive',15,'place',1),(123,'بارسلونا','positive',15,'place',1),(124,'آمستردام','positive',15,'place',1),(125,'پراگ','positive',15,'place',1),(126,'وین','positive',15,'place',1),(127,'بوداپست','positive',15,'place',1),(128,'مادرید','positive',15,'place',1),(129,'لیسبون','positive',15,'place',1),(130,'توکیو','positive',15,'place',1),(131,'سئول','positive',15,'place',1),(132,'سنگاپور','positive',15,'place',1),(133,'کوالالامپور','positive',15,'place',1),(134,'هانوی','positive',15,'place',1),(135,'کاتماندو','positive',15,'place',1),(136,'مراکش','positive',15,'place',1),(137,'قاهره','positive',15,'place',1),(138,'کیپ‌تاون','positive',15,'place',1),(139,'ریودوژانیرو','positive',15,'place',1),(140,'نیویورک','positive',15,'place',1),(141,'لس‌آنجلس','positive',15,'place',1),(142,'لاس‌وگاس','positive',15,'place',1),(143,'مالدیو','positive',20,'place',1),(144,'موریس','positive',20,'place',1),(145,'سیشل','positive',20,'place',1),(146,'زنگبار','positive',20,'place',1),(147,'میکونوس','positive',20,'place',1),(148,'سانتورینی','positive',20,'place',1),(149,'کاپادوکیا','positive',20,'place',1),(150,'پوکت','positive',15,'place',1),(151,'سامویی','positive',15,'place',1),(152,'باتومی','positive',15,'place',1),(153,'ایروان','positive',15,'place',1),(154,'دوحه','positive',15,'place',1),(155,'ابوظبی','positive',15,'place',1),(156,'مسقط','positive',15,'place',1),(157,'غواصی','positive',20,'adventure',1),(158,'اسنورکلینگ','positive',15,'adventure',1),(159,'اسکی','positive',15,'winter',1),(160,'اسنوبرد','positive',15,'winter',1),(161,'پیست اسکی','positive',20,'winter',1),(162,'اسکی روی آب','positive',15,'adventure',1),(163,'پاراسل','positive',15,'adventure',1),(164,'پاراگلایدر','positive',15,'adventure',1),(165,'بانجی جامپینگ','positive',15,'adventure',1),(166,'سافاری','positive',20,'adventure',1),(167,'آفرود','positive',15,'adventure',1),(168,' ATV','positive',15,'adventure',1),(169,'کوهپیمایی','positive',15,'eco',1),(170,'صخره‌نوردی','positive',15,'adventure',1),(171,'قایقرانی','positive',15,'adventure',1),(172,'رفتینگ','positive',15,'adventure',1),(173,'ماهیگیری','positive',10,'adventure',1),(174,'دوچرخه‌سواری','positive',10,'eco',1),(175,'پیاده‌گردی','positive',15,'eco',1),(176,'تور مجازی','positive',15,'digital',1),(177,'فتوگرافی','positive',10,'culture',1),(178,'عکاسی','positive',10,'culture',1),(179,'ساحلی','positive',15,'nature',1),(180,'کوهستانی','positive',10,'nature',1),(181,'تاریخی','positive',10,'culture',1),(182,'باستانی','positive',10,'culture',1),(183,'طبیعی','positive',10,'nature',1),(184,'لوکس','positive',10,'general',1),(185,'اقتصادی','positive',5,'general',1),(186,'ارزان','positive',5,'general',1),(187,'دیجیتال نومد','positive',20,'digital',1),(188,'نومد دیجیتال','positive',20,'digital',1),(189,'سفر کاری','positive',15,'business',1),(190,'گردشگری دیجیتال','positive',20,'digital',1),(191,'گردشگری ماجراجویانه','positive',20,'adventure',1),(192,'گردشگری پایدار','positive',20,'eco',1),(193,'گردشگری حلال','positive',15,'culture',1),(194,'گردشگری غذایی','positive',20,'food',1),(195,'توریسم غذایی','positive',20,'food',1),(196,'گردشگری فرهنگی','positive',20,'culture',1),(197,'سفر خانوادگی','positive',15,'family',1),(198,'ماه عسل','positive',20,'romantic',1),(199,'سفر دونفره','positive',15,'romantic',1),(200,'سفر انفرادی','positive',10,'solo',1),(201,'سفر گروهی','positive',10,'group',1),(202,'تور لحظه آخری','positive',20,'deal',1),(203,'تخفیف هتل','positive',15,'deal',1),(204,'رزرو آنلاین','positive',15,'digital',1),(205,'سفرنامه','positive',15,'blog',1),(206,'ولاگ سفر','positive',15,'blog',1),(207,'راهنمای سفر','positive',20,'guide',1),(208,'جشنواره','positive',15,'festival',1),(209,'فستیوال','positive',15,'festival',1),(210,'نمایشگاه گردشگری','positive',25,'festival',1),(211,'نمایشگاه توریسم','positive',25,'festival',1),(212,'کارناوال','positive',15,'festival',1),(213,'نوروز','positive',10,'festival',1),(214,'عید','positive',10,'festival',1),(215,'تعطیلات','positive',15,'festival',1),(216,'تعطیلات نوروزی','positive',20,'festival',1),(217,'آشپزی محلی','positive',15,'food',1),(218,'دمنوش','positive',10,'food',1),(219,'چایخانه','positive',10,'food',1),(220,'سنتی','positive',10,'culture',1),(221,'صنایع دستی','positive',15,'shopping',1),(222,'بازار محلی','positive',15,'shopping',1),(223,'بازارچه','positive',10,'shopping',1),(224,'خرید','positive',10,'shopping',1),(225,'پاساژ','positive',5,'shopping',1),(226,'مال','positive',5,'shopping',1),(227,'اسپا','positive',15,'wellness',1),(228,'ماساژ','positive',10,'wellness',1),(229,'حمام ترکی','positive',15,'wellness',1),(230,'یوگا','positive',15,'wellness',1),(231,'مدیتیشن','positive',15,'wellness',1),(232,'آبگرم','positive',15,'wellness',1),(233,'چشمه آبگرم','positive',20,'wellness',1),(234,'tourism','positive',25,'en',1),(235,'tourist','positive',20,'en',1),(236,'travel','positive',15,'en',1),(237,'trip','positive',15,'en',1),(238,'vacation','positive',15,'en',1),(239,'holiday','positive',15,'en',1),(240,'journey','positive',15,'en',1),(241,'tour','positive',20,'en',1),(242,'hotel','positive',20,'en',1),(243,'resort','positive',20,'en',1),(244,'hostel','positive',15,'en',1),(245,'lodge','positive',15,'en',1),(246,'flight','positive',20,'en',1),(247,'airport','positive',15,'en',1),(248,'airline','positive',20,'en',1),(249,'aviation','positive',15,'en',1),(250,'destination','positive',15,'en',1),(251,'attraction','positive',15,'en',1),(252,'landmark','positive',15,'en',1),(253,'sightseeing','positive',15,'en',1),(254,'backpacking','positive',15,'en',1),(255,'camping','positive',10,'en',1),(256,'beach','positive',10,'en',1),(257,'island','positive',15,'en',1),(258,'mountain','positive',10,'en',1),(259,'hiking','positive',15,'en',1),(260,'museum','positive',10,'en',1),(261,'heritage','positive',15,'en',1),(262,'unesco','positive',20,'en',1),(263,'overtourism','positive',20,'en',1),(264,'ecotourism','positive',25,'en',1),(265,'agritourism','positive',20,'en',1),(266,'boutique hotel','positive',20,'en',1),(267,'all inclusive','positive',20,'en',1),(268,'cruise ship','positive',20,'en',1),(269,'river cruise','positive',20,'en',1),(270,'safari','positive',20,'en',1),(271,'wildlife','positive',15,'en',1),(272,'national park','positive',20,'en',1),(273,'wellness retreat','positive',20,'en',1),(274,'spa resort','positive',20,'en',1),(275,'ski resort','positive',20,'en',1),(276,'scuba diving','positive',20,'en',1),(277,'snorkeling','positive',15,'en',1),(278,'surfing','positive',15,'en',1),(279,'kite surfing','positive',15,'en',1),(280,'windsurfing','positive',15,'en',1),(281,'paragliding','positive',15,'en',1),(282,'skydiving','positive',15,'en',1),(283,'hot air balloon','positive',20,'en',1),(284,'road trip','positive',15,'en',1),(285,'campervan','positive',15,'en',1),(286,'RV','positive',15,'en',1),(287,'glamping','positive',20,'en',1),(288,'digital nomad','positive',20,'en',1),(289,'remote work','positive',15,'en',1),(290,'workation','positive',20,'en',1),(291,'honeymoon','positive',20,'en',1),(292,'family travel','positive',15,'en',1),(293,'solo travel','positive',15,'en',1),(294,'luxury travel','positive',20,'en',1),(295,'budget travel','positive',15,'en',1),(296,'last minute','positive',15,'en',1),(297,'travel insurance','positive',15,'en',1),(298,'visa free','positive',20,'en',1),(299,'e-visa','positive',15,'en',1),(300,'passport','positive',15,'en',1),(301,'immigration','positive',10,'en',1),(302,'customs','positive',10,'en',1),(303,'layover','positive',10,'en',1),(304,'stopover','positive',10,'en',1),(305,'first class','positive',15,'en',1),(306,'business class','positive',15,'en',1),(307,'economy class','positive',10,'en',1),(308,'low cost airline','positive',15,'en',1),(309,'ryanair','positive',10,'en',1),(310,'emirates','positive',15,'en',1),(311,'qatar airways','positive',15,'en',1),(312,'turkish airlines','positive',15,'en',1),(313,'airbnb','positive',20,'en',1),(314,'booking.com','positive',15,'en',1),(315,'tripadvisor','positive',15,'en',1),(316,'expedia','positive',15,'en',1),(317,'michelin star','positive',20,'en',1),(318,'street food','positive',15,'en',1),(319,'food tour','positive',20,'en',1),(320,'wine tasting','positive',15,'en',1),(321,'gastronomy','positive',20,'en',1),(322,'culinary','positive',15,'en',1),(323,'travel blogger','positive',15,'en',1),(324,'travel vlog','positive',15,'en',1),(325,'wanderlust','positive',15,'en',1),(326,'itinerary','positive',15,'en',1),(327,'off the beaten path','positive',20,'en',1),(328,'hidden gem','positive',20,'en',1),(329,'bucket list','positive',15,'en',1),(330,'sustainable travel','positive',20,'en',1),(331,'responsible tourism','positive',20,'en',1),(332,'carbon neutral','positive',15,'en',1),(333,'voluntourism','positive',15,'en',1),(334,'medical tourism','positive',20,'en',1),(335,'dark tourism','positive',10,'en',1),(336,'religious tourism','positive',15,'en',1),(337,'pilgrimage','positive',15,'en',1),(338,'carnival','positive',15,'en',1),(339,'oktoberfest','positive',15,'en',1),(340,'diwali','positive',10,'en',1),(341,'cherry blossom','positive',15,'en',1),(342,'northern lights','positive',20,'en',1),(343,'aurora borealis','positive',20,'en',1),(344,'santorini','positive',15,'en',1),(345,'maldives','positive',20,'en',1),(346,'patagonia','positive',20,'en',1),(347,'machu picchu','positive',25,'en',1),(348,'great wall','positive',20,'en',1),(349,'taj mahal','positive',25,'en',1),(350,'grand canyon','positive',20,'en',1),(351,'جنگ','negative',-30,'military',1),(352,'حمله','negative',-30,'military',1),(353,'حمله نظامی','negative',-30,'military',1),(354,'بمباران','negative',-30,'military',1),(355,'موشک','negative',-30,'military',1),(356,'پهپاد','negative',-20,'military',1),(357,'سلاح','negative',-20,'military',1),(358,'تفنگ','negative',-25,'military',1),(359,'تیراندازی','negative',-25,'military',1),(360,'ترور','negative',-30,'military',1),(361,'تروریسم','negative',-30,'military',1),(362,'تروریست','negative',-30,'military',1),(363,'انفجار','negative',-30,'military',1),(364,'بمب','negative',-30,'military',1),(365,'گروگان','negative',-30,'military',1),(366,'ربایش','negative',-25,'military',1),(367,'کودتا','negative',-25,'military',1),(368,'شبه‌نظامی','negative',-25,'military',1),(369,'پنتاگون','negative',-15,'military',1),(370,'ارتش','negative',-15,'military',1),(371,'سپاه','negative',-15,'military',1),(372,'ناو','negative',-15,'military',1),(373,'تانک','negative',-20,'military',1),(374,'تحریم','negative',-20,'politics',1),(375,'کشته','negative',-25,'accident',1),(376,'زخمی','negative',-20,'accident',1),(377,'تصادف','negative',-20,'accident',1),(378,'حادثه','negative',-15,'accident',1),(379,'سقوط','negative',-25,'accident',1),(380,'سقوط هواپیما','negative',-30,'accident',1),(381,'واژگونی','negative',-20,'accident',1),(382,'تصادف زنجیره‌ای','negative',-25,'accident',1),(383,'غرق','negative',-25,'accident',1),(384,'غرق شدن','negative',-25,'accident',1),(385,'آتش‌سوزی','negative',-25,'accident',1),(386,'آتش سوزی','negative',-25,'accident',1),(387,'حریق','negative',-20,'accident',1),(388,'مصدوم','negative',-20,'accident',1),(389,'جان باخت','negative',-25,'accident',1),(390,'فوتی','negative',-25,'accident',1),(391,'ناپدید','negative',-20,'accident',1),(392,'مفقود','negative',-20,'accident',1),(393,'ناپدید شدن','negative',-20,'accident',1),(394,'فوتبال','negative',-30,'sports',1),(395,'لیگ','negative',-25,'sports',1),(396,'ورزشگاه','negative',-15,'sports',1),(397,'تیم ملی','negative',-25,'sports',1),(398,'جام جهانی','negative',-25,'sports',1),(399,'جام ملت‌ها','negative',-25,'sports',1),(400,'پرسپولیس','negative',-25,'sports',1),(401,'استقلال','negative',-25,'sports',1),(402,'دربی','negative',-25,'sports',1),(403,'لیگ برتر','negative',-25,'sports',1),(404,'والیبال','negative',-25,'sports',1),(405,'بسکتبال','negative',-25,'sports',1),(406,'کشتی','negative',-15,'sports',1),(407,'بوکس','negative',-20,'sports',1),(408,'وزنه‌برداری','negative',-20,'sports',1),(409,'مدال','negative',-15,'sports',1),(410,'المپیک','negative',-20,'sports',1),(411,'مربی','negative',-15,'sports',1),(412,'بازیکن','negative',-20,'sports',1),(413,'گلزن','negative',-20,'sports',1),(414,'پنالتی','negative',-20,'sports',1),(415,'داور','negative',-15,'sports',1),(416,'سیل','negative',-20,'disaster',1),(417,'زلزله','negative',-25,'disaster',1),(418,'زمین‌لرزه','negative',-25,'disaster',1),(419,'توفان','negative',-20,'disaster',1),(420,'طوفان','negative',-20,'disaster',1),(421,'گردباد','negative',-20,'disaster',1),(422,'سونامی','negative',-30,'disaster',1),(423,'آتشفشان','negative',-25,'disaster',1),(424,'خشکسالی','negative',-20,'disaster',1),(425,'رانش زمین','negative',-20,'disaster',1),(426,'بهمن','negative',-20,'disaster',1),(427,'کولاک','negative',-15,'disaster',1),(428,'یخبندان','negative',-15,'disaster',1),(429,'مخاطرات جوی','negative',-15,'disaster',1),(430,'دانشگاه','negative',-20,'education',1),(431,'کنکور','negative',-25,'education',1),(432,'مدرسه','negative',-20,'education',1),(433,'آموزش','negative',-10,'education',1),(434,'دانشجو','negative',-15,'education',1),(435,'دانش‌آموز','negative',-15,'education',1),(436,'امتحانات','negative',-20,'education',1),(437,'معلم','negative',-10,'education',1),(438,'استاد','negative',-10,'education',1),(439,'پایان‌نامه','negative',-20,'education',1),(440,'کنکور سراسری','negative',-25,'education',1),(441,'قبولی','negative',-15,'education',1),(442,'مجلس','negative',-25,'politics',1),(443,'دولت','negative',-15,'politics',1),(444,'انتخابات','negative',-30,'politics',1),(445,'سیاسی','negative',-20,'politics',1),(446,'استیضاح','negative',-25,'politics',1),(447,'وزیر','negative',-20,'politics',1),(448,'رئیس جمهور','negative',-20,'politics',1),(449,'پارلمان','negative',-20,'politics',1),(450,'نماینده','negative',-15,'politics',1),(451,'لایحه','negative',-20,'politics',1),(452,'قانون','negative',-15,'politics',1),(453,'مجلس شورای','negative',-25,'politics',1),(454,'دیپلمات','negative',-10,'politics',1),(455,'سفیر','negative',-10,'politics',1),(456,'دیپلماسی','negative',-10,'politics',1),(457,'مذاکره','negative',-15,'politics',1),(458,'توافق','negative',-15,'politics',1),(459,'تحریم‌ها','negative',-20,'politics',1),(460,'دلار','negative',-25,'finance',1),(461,'تورم','negative',-25,'finance',1),(462,'بورس','negative',-20,'finance',1),(463,'سهام','negative',-30,'finance',1),(464,'طلا','negative',-15,'finance',1),(465,'نفت','negative',-15,'finance',1),(466,'بنزین','negative',-15,'finance',1),(467,'گرانی','negative',-20,'finance',1),(468,'رکود','negative',-25,'finance',1),(469,'رکود اقتصادی','negative',-25,'finance',1),(470,'نرخ ارز','negative',-20,'finance',1),(471,'ارز','negative',-15,'finance',1),(472,'سکه','negative',-15,'finance',1),(473,'بانک مرکزی','negative',-15,'finance',1),(474,'بدهی','negative',-20,'finance',1),(475,'ورشکستگی','negative',-25,'finance',1),(476,'بحران اقتصادی','negative',-30,'finance',1),(477,'بودجه','negative',-10,'finance',1),(478,'مالیات','negative',-15,'finance',1),(479,'بیمارستان','negative',-15,'health',1),(480,'پزشکی','negative',-15,'health',1),(481,'جراحی','negative',-20,'health',1),(482,'دارو','negative',-15,'health',1),(483,'کرونا','negative',-15,'health',1),(484,'کووید','negative',-20,'health',1),(485,'ویروس','negative',-20,'health',1),(486,'پاندمی','negative',-25,'health',1),(487,'اپیدمی','negative',-25,'health',1),(488,'واکسن','negative',-15,'health',1),(489,'مرگ','negative',-20,'health',1),(490,'سرطان','negative',-20,'health',1),(491,'شیمی‌درمانی','negative',-20,'health',1),(492,'بیماری','negative',-15,'health',1),(493,'عفونت','negative',-20,'health',1),(494,'وبا','negative',-25,'health',1),(495,'دادگاه','negative',-20,'crime',1),(496,'حبس','negative',-25,'crime',1),(497,'جرم','negative',-25,'crime',1),(498,'جنایت','negative',-30,'crime',1),(499,'قتل','negative',-30,'crime',1),(500,'دزدی','negative',-25,'crime',1),(501,'سرقت','negative',-25,'crime',1),(502,'کلاهبرداری','negative',-25,'crime',1),(503,'اختلاس','negative',-25,'crime',1),(504,'رشوه','negative',-25,'crime',1),(505,'فساد','negative',-20,'crime',1),(506,'اعتیاد','negative',-20,'crime',1),(507,'مواد مخدر','negative',-25,'crime',1),(508,'قاچاق','negative',-25,'crime',1),(509,'قاچاق انسان','negative',-30,'crime',1),(510,'تجاوز','negative',-30,'crime',1),(511,'خشونت','negative',-25,'crime',1),(512,'خشونت خانگی','negative',-25,'crime',1),(513,'دستگیری','negative',-20,'crime',1),(514,'بازداشت','negative',-20,'crime',1),(515,'زندانی','negative',-20,'crime',1),(516,'زندان','negative',-20,'crime',1),(517,'احکام','negative',-15,'crime',1),(518,'محکومیت','negative',-20,'crime',1),(519,'اعتصاب','negative',-20,'politics',1),(520,'تظاهرات','negative',-25,'politics',1),(521,'شورش','negative',-30,'politics',1),(522,'آشوب','negative',-30,'politics',1),(523,'اغتشاش','negative',-25,'politics',1),(524,'ناآرامی','negative',-20,'politics',1),(525,'بلوا','negative',-25,'politics',1),(526,'شکایت','negative',-15,'crime',1),(527,'کره شمالی','negative',-20,'politics',1),(528,'اسرائیل','negative',-15,'military',1),(529,'صهیونیست','negative',-15,'military',1),(530,'پوتین','negative',-15,'politics',1),(531,'ترامپ','negative',-15,'politics',1),(532,'بایدن','negative',-15,'politics',1),(533,'ناتو','negative',-15,'military',1),(534,'اتحادیه اروپا','negative',-10,'politics',1),(535,'سینما','negative',-15,'entertainment',1),(536,'فیلم','negative',-15,'entertainment',1),(537,'سریال','negative',-15,'entertainment',1),(538,'بازیگر','negative',-15,'entertainment',1),(539,'خواننده','negative',-15,'entertainment',1),(540,'کنسرت','negative',-15,'entertainment',1),(541,'اسکار','negative',-20,'entertainment',1),(542,'جشنواره فیلم','negative',-15,'entertainment',1),(543,'سلبریتی','negative',-15,'entertainment',1),(544,'شایعه','negative',-15,'entertainment',1),(545,'رسوایی','negative',-20,'entertainment',1),(546,'طلاق','negative',-15,'entertainment',1),(547,'ازدواج','negative',-10,'entertainment',1),(548,'گوشی','negative',-10,'tech',1),(549,'موبایل','negative',-10,'tech',1),(550,'آیفون','negative',-15,'tech',1),(551,'سامسونگ','negative',-15,'tech',1),(552,'اپل','negative',-10,'tech',1),(553,'هوش مصنوعی','negative',-10,'tech',1),(554,'بلاکچین','negative',-15,'tech',1),(555,'کریپتو','negative',-15,'tech',1),(556,'بیت‌کوین','negative',-20,'tech',1),(557,'اتریوم','negative',-15,'tech',1),(558,'war','negative',-30,'en_neg',1),(559,'attack','negative',-30,'en_neg',1),(560,'bombing','negative',-30,'en_neg',1),(561,'terrorist','negative',-30,'en_neg',1),(562,'terrorism','negative',-30,'en_neg',1),(563,'killed','negative',-25,'en_neg',1),(564,'dead','negative',-20,'en_neg',1),(565,'injured','negative',-20,'en_neg',1),(566,'crash','negative',-25,'en_neg',1),(567,'plane crash','negative',-30,'en_neg',1),(568,'earthquake','negative',-25,'en_neg',1),(569,'tsunami','negative',-30,'en_neg',1),(570,'hurricane','negative',-25,'en_neg',1),(571,'tornado','negative',-25,'en_neg',1),(572,'wildfire','negative',-20,'en_neg',1),(573,'flood','negative',-20,'en_neg',1),(574,'drought','negative',-15,'en_neg',1),(575,'protest','negative',-15,'en_neg',1),(576,'riot','negative',-25,'en_neg',1),(577,'strikes','negative',-15,'en_neg',1),(578,'scam','negative',-20,'en_neg',1),(579,'fraud','negative',-20,'en_neg',1),(580,'robbery','negative',-25,'en_neg',1),(581,'murder','negative',-30,'en_neg',1),(582,'arrest','negative',-20,'en_neg',1),(583,'prison','negative',-20,'en_neg',1),(584,'lawsuit','negative',-15,'en_neg',1),(585,'recession','negative',-20,'en_neg',1),(586,'inflation','negative',-15,'en_neg',1),(587,'bankruptcy','negative',-25,'en_neg',1),(588,'pollution','negative',-15,'en_neg',1),(589,'climate crisis','negative',-15,'en_neg',1),(590,'outbreak','negative',-20,'en_neg',1),(591,'pandemic','negative',-25,'en_neg',1),(592,'epidemic','negative',-20,'en_neg',1),(593,'quarantine','negative',-15,'en_neg',1),(594,'lockdown','negative',-20,'en_neg',1),(595,'travel ban','negative',-25,'en_neg',1),(596,'travel warning','negative',-25,'en_neg',1),(597,'travel advisory','negative',-20,'en_neg',1),(598,'cancellation','negative',-15,'en_neg',1),(599,'flight cancellation','negative',-25,'en_neg',1),(600,'bankrupt airline','negative',-25,'en_neg',1),(601,'hotel closure','negative',-20,'en_neg',1),(602,'overbooked','negative',-15,'en_neg',1),(603,'bed bugs','negative',-25,'en_neg',1),(604,'food poisoning','negative',-25,'en_neg',1),(605,'lost luggage','negative',-20,'en_neg',1),(606,'pickpocket','negative',-20,'en_neg',1),(607,'mugging','negative',-25,'en_neg',1);
/*!40000 ALTER TABLE `ns_ns_news_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_news_sources`
--

DROP TABLE IF EXISTS `ns_ns_news_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_news_sources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `url` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'rss',
  `group_name` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'general',
  `priority` int NOT NULL DEFAULT '5',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_fetch` datetime DEFAULT NULL,
  `last_success` datetime DEFAULT NULL,
  `total_fetched` bigint NOT NULL DEFAULT '0',
  `total_duplicates` bigint NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_news_sources`
--

LOCK TABLES `ns_ns_news_sources` WRITE;
/*!40000 ALTER TABLE `ns_ns_news_sources` DISABLE KEYS */;
INSERT INTO `ns_ns_news_sources` VALUES (1,'GNews.io','https://gnews.io/api/v4/search','api','api',5,1,NULL,NULL,0,0,NULL),(2,'NewsData.io','https://newsdata.io/api/1/latest','api','api',5,1,NULL,NULL,0,0,NULL),(3,'Currents API','https://api.currentsapi.services/v1/search','api','api',5,1,NULL,NULL,0,0,NULL),(4,'Skift','https://skift.com/feed/','rss','industry',10,1,NULL,NULL,0,0,NULL),(5,'Travel and Tour World','https://www.travelandtourworld.com/feed/','rss','industry',8,1,NULL,NULL,0,0,NULL);
/*!40000 ALTER TABLE `ns_ns_news_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_otp_codes`
--

DROP TABLE IF EXISTS `ns_ns_otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_otp_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `code_hash` char(64) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `purpose` enum('login','register') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'login',
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `ip` varchar(45) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `phone_expires` (`phone`,`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_otp_codes`
--

LOCK TABLES `ns_ns_otp_codes` WRITE;
/*!40000 ALTER TABLE `ns_ns_otp_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_otp_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_payments`
--

DROP TABLE IF EXISTS `ns_ns_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `gateway` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'zarinpal',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'IRR',
  `status` enum('pending','redirecting','success','failed','cancelled','refunded') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `gateway_ref_id` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `gateway_response` longtext COLLATE utf8mb4_unicode_520_ci,
  `callback_data` longtext COLLATE utf8mb4_unicode_520_ci,
  `description` text COLLATE utf8mb4_unicode_520_ci,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_transaction_id` (`transaction_id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_gateway` (`gateway`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_payments`
--

LOCK TABLES `ns_ns_payments` WRITE;
/*!40000 ALTER TABLE `ns_ns_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_pending_users`
--

DROP TABLE IF EXISTS `ns_ns_pending_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_pending_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_pending_users`
--

LOCK TABLES `ns_ns_pending_users` WRITE;
/*!40000 ALTER TABLE `ns_ns_pending_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_pending_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_sessions`
--

DROP TABLE IF EXISTS `ns_ns_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token_hash` char(64) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `last_seen` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_sessions`
--

LOCK TABLES `ns_ns_sessions` WRITE;
/*!40000 ALTER TABLE `ns_ns_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_ns_transactions`
--

DROP TABLE IF EXISTS `ns_ns_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_ns_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `balance_after` decimal(12,2) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'pending',
  `ref_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ref` (`ref_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_ns_transactions`
--

LOCK TABLES `ns_ns_transactions` WRITE;
/*!40000 ALTER TABLE `ns_ns_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_ns_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_options`
--

DROP TABLE IF EXISTS `ns_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_options` (
  `option_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_options`
--

LOCK TABLES `ns_options` WRITE;
/*!40000 ALTER TABLE `ns_options` DISABLE KEYS */;
INSERT INTO `ns_options` VALUES (1,'cron','a:20:{i:1791491853;a:1:{s:25:\"nextsafar_news_retry_hook\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:8:\"ns_15min\";s:4:\"args\";a:0:{}s:8:\"interval\";i:900;}}}i:1791492033;a:1:{s:19:\"ns_ai_process_queue\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1791493313;a:1:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1791493314;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1791494205;a:1:{s:27:\"wpseo_indexable_index_batch\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:15:\"fifteen_minutes\";s:4:\"args\";a:0:{}s:8:\"interval\";i:900;}}}i:1791494209;a:1:{s:26:\"rediscache_discard_metrics\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1791495113;a:1:{s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1791495333;a:2:{s:24:\"ns_enrich_locations_cron\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}s:25:\"nextsafar_currency_hourly\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1791495395;a:1:{s:26:\"nextsafar_news_hourly_tick\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1791496913;a:1:{s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1791532922;a:1:{s:21:\"wp_update_user_counts\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1791576113;a:2:{s:30:\"wp_site_health_scheduled_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}s:32:\"recovery_mode_clean_expired_keys\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1791576114;a:1:{s:41:\"wp_privacy_personal_data_cleanup_requests\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1791576122;a:2:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1791576125;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1791577005;a:3:{s:29:\"wpseo_detect_default_seo_data\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:13:\"wpseo-reindex\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:31:\"wpseo_permalink_structure_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1791578133;a:3:{s:14:\"ns_cleanup_otp\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:30:\"nextsafar_exchange_update_cron\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:19:\"ns_cleanup_ai_trips\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1792094573;a:1:{s:30:\"wp_delete_temp_updater_backups\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}i:1792095405;a:1:{s:28:\"wpseo_expiring_store_cleanup\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}s:7:\"version\";i:2;}','on'),(2,'siteurl','http://cms.nextsafar.local','on'),(3,'home','http://cms.nextsafar.local','on'),(4,'blogname','NextSafar CMS','on'),(5,'blogdescription','','on'),(6,'users_can_register','0','on'),(7,'admin_email','admin@nextsafar.com','on'),(8,'start_of_week','1','on'),(9,'use_balanceTags','0','on'),(10,'use_smilies','1','on'),(11,'require_name_email','1','on'),(12,'comments_notify','1','on'),(13,'posts_per_rss','10','on'),(14,'rss_use_excerpt','0','on'),(15,'mailserver_url','mail.example.com','on'),(16,'mailserver_login','login@example.com','on'),(17,'mailserver_pass','','on'),(18,'mailserver_port','110','on'),(19,'default_category','1','on'),(20,'default_comment_status','open','on'),(21,'default_ping_status','open','on'),(22,'default_pingback_flag','0','on'),(23,'posts_per_page','10','on'),(24,'date_format','F j, Y','on'),(25,'time_format','g:i a','on'),(26,'links_updated_date_format','F j, Y g:i a','on'),(27,'comment_moderation','0','on'),(28,'moderation_notify','1','on'),(29,'permalink_structure','/%year%/%monthnum%/%day%/%postname%/','on'),(30,'rewrite_rules','a:386:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:17:\"^wp-sitemap\\.xml$\";s:23:\"index.php?sitemap=index\";s:17:\"^wp-sitemap\\.xsl$\";s:36:\"index.php?sitemap-stylesheet=sitemap\";s:23:\"^wp-sitemap-index\\.xsl$\";s:34:\"index.php?sitemap-stylesheet=index\";s:48:\"^wp-sitemap-([a-z]+?)-([a-z\\d_-]+?)-(\\d+?)\\.xml$\";s:75:\"index.php?sitemap=$matches[1]&sitemap-subtype=$matches[2]&paged=$matches[3]\";s:34:\"^wp-sitemap-([a-z]+?)-(\\d+?)\\.xml$\";s:47:\"index.php?sitemap=$matches[1]&paged=$matches[2]\";s:8:\"hotel/?$\";s:25:\"index.php?post_type=hotel\";s:38:\"hotel/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?post_type=hotel&feed=$matches[1]\";s:33:\"hotel/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?post_type=hotel&feed=$matches[1]\";s:25:\"hotel/page/([0-9]{1,})/?$\";s:43:\"index.php?post_type=hotel&paged=$matches[1]\";s:10:\"airport/?$\";s:27:\"index.php?post_type=airport\";s:40:\"airport/feed/(feed|rdf|rss|rss2|atom)/?$\";s:44:\"index.php?post_type=airport&feed=$matches[1]\";s:35:\"airport/(feed|rdf|rss|rss2|atom)/?$\";s:44:\"index.php?post_type=airport&feed=$matches[1]\";s:27:\"airport/page/([0-9]{1,})/?$\";s:45:\"index.php?post_type=airport&paged=$matches[1]\";s:14:\"destination/?$\";s:31:\"index.php?post_type=destination\";s:44:\"destination/feed/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?post_type=destination&feed=$matches[1]\";s:39:\"destination/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?post_type=destination&feed=$matches[1]\";s:31:\"destination/page/([0-9]{1,})/?$\";s:49:\"index.php?post_type=destination&paged=$matches[1]\";s:13:\"restaurant/?$\";s:30:\"index.php?post_type=restaurant\";s:43:\"restaurant/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?post_type=restaurant&feed=$matches[1]\";s:38:\"restaurant/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?post_type=restaurant&feed=$matches[1]\";s:30:\"restaurant/page/([0-9]{1,})/?$\";s:48:\"index.php?post_type=restaurant&paged=$matches[1]\";s:11:\"hospital/?$\";s:28:\"index.php?post_type=hospital\";s:41:\"hospital/feed/(feed|rdf|rss|rss2|atom)/?$\";s:45:\"index.php?post_type=hospital&feed=$matches[1]\";s:36:\"hospital/(feed|rdf|rss|rss2|atom)/?$\";s:45:\"index.php?post_type=hospital&feed=$matches[1]\";s:28:\"hospital/page/([0-9]{1,})/?$\";s:46:\"index.php?post_type=hospital&paged=$matches[1]\";s:7:\"tour/?$\";s:24:\"index.php?post_type=tour\";s:37:\"tour/feed/(feed|rdf|rss|rss2|atom)/?$\";s:41:\"index.php?post_type=tour&feed=$matches[1]\";s:32:\"tour/(feed|rdf|rss|rss2|atom)/?$\";s:41:\"index.php?post_type=tour&feed=$matches[1]\";s:24:\"tour/page/([0-9]{1,})/?$\";s:42:\"index.php?post_type=tour&paged=$matches[1]\";s:7:\"visa/?$\";s:24:\"index.php?post_type=visa\";s:37:\"visa/feed/(feed|rdf|rss|rss2|atom)/?$\";s:41:\"index.php?post_type=visa&feed=$matches[1]\";s:32:\"visa/(feed|rdf|rss|rss2|atom)/?$\";s:41:\"index.php?post_type=visa&feed=$matches[1]\";s:24:\"visa/page/([0-9]{1,})/?$\";s:42:\"index.php?post_type=visa&paged=$matches[1]\";s:14:\"travelguide/?$\";s:31:\"index.php?post_type=travelguide\";s:44:\"travelguide/feed/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?post_type=travelguide&feed=$matches[1]\";s:39:\"travelguide/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?post_type=travelguide&feed=$matches[1]\";s:31:\"travelguide/page/([0-9]{1,})/?$\";s:49:\"index.php?post_type=travelguide&paged=$matches[1]\";s:13:\"travelnews/?$\";s:30:\"index.php?post_type=travelnews\";s:43:\"travelnews/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?post_type=travelnews&feed=$matches[1]\";s:38:\"travelnews/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?post_type=travelnews&feed=$matches[1]\";s:30:\"travelnews/page/([0-9]{1,})/?$\";s:48:\"index.php?post_type=travelnews&paged=$matches[1]\";s:53:\"(uncategorized)/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:36:\"(uncategorized)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:18:\"(uncategorized)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:14:\"category/(.+)$\";s:45:\"index.php?wpseo_category_redirect=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:33:\"hotel/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:43:\"hotel/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:63:\"hotel/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:58:\"hotel/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:58:\"hotel/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:39:\"hotel/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:22:\"hotel/([^/]+)/embed/?$\";s:38:\"index.php?hotel=$matches[1]&embed=true\";s:26:\"hotel/([^/]+)/trackback/?$\";s:32:\"index.php?hotel=$matches[1]&tb=1\";s:46:\"hotel/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:44:\"index.php?hotel=$matches[1]&feed=$matches[2]\";s:41:\"hotel/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:44:\"index.php?hotel=$matches[1]&feed=$matches[2]\";s:34:\"hotel/([^/]+)/page/?([0-9]{1,})/?$\";s:45:\"index.php?hotel=$matches[1]&paged=$matches[2]\";s:41:\"hotel/([^/]+)/comment-page-([0-9]{1,})/?$\";s:45:\"index.php?hotel=$matches[1]&cpage=$matches[2]\";s:30:\"hotel/([^/]+)(?:/([0-9]+))?/?$\";s:44:\"index.php?hotel=$matches[1]&page=$matches[2]\";s:22:\"hotel/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:32:\"hotel/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:52:\"hotel/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:47:\"hotel/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:47:\"hotel/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:28:\"hotel/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:35:\"airport/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:45:\"airport/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:65:\"airport/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:60:\"airport/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:60:\"airport/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:41:\"airport/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:24:\"airport/([^/]+)/embed/?$\";s:49:\"index.php?airport_category=$matches[1]&embed=true\";s:28:\"airport/([^/]+)/trackback/?$\";s:34:\"index.php?airport=$matches[1]&tb=1\";s:48:\"airport/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:55:\"index.php?airport_category=$matches[1]&feed=$matches[2]\";s:43:\"airport/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:55:\"index.php?airport_category=$matches[1]&feed=$matches[2]\";s:36:\"airport/([^/]+)/page/?([0-9]{1,})/?$\";s:56:\"index.php?airport_category=$matches[1]&paged=$matches[2]\";s:43:\"airport/([^/]+)/comment-page-([0-9]{1,})/?$\";s:47:\"index.php?airport=$matches[1]&cpage=$matches[2]\";s:32:\"airport/([^/]+)(?:/([0-9]+))?/?$\";s:46:\"index.php?airport=$matches[1]&page=$matches[2]\";s:24:\"airport/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:34:\"airport/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:54:\"airport/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:49:\"airport/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:49:\"airport/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:30:\"airport/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:39:\"destination/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:49:\"destination/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:69:\"destination/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"destination/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"destination/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:45:\"destination/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:28:\"destination/([^/]+)/embed/?$\";s:44:\"index.php?destination=$matches[1]&embed=true\";s:32:\"destination/([^/]+)/trackback/?$\";s:38:\"index.php?destination=$matches[1]&tb=1\";s:52:\"destination/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?destination=$matches[1]&feed=$matches[2]\";s:47:\"destination/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?destination=$matches[1]&feed=$matches[2]\";s:40:\"destination/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?destination=$matches[1]&paged=$matches[2]\";s:47:\"destination/([^/]+)/comment-page-([0-9]{1,})/?$\";s:51:\"index.php?destination=$matches[1]&cpage=$matches[2]\";s:36:\"destination/([^/]+)(?:/([0-9]+))?/?$\";s:50:\"index.php?destination=$matches[1]&page=$matches[2]\";s:28:\"destination/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:38:\"destination/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:58:\"destination/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"destination/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"destination/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:34:\"destination/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:38:\"restaurant/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:48:\"restaurant/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:68:\"restaurant/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:63:\"restaurant/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:63:\"restaurant/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:44:\"restaurant/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:27:\"restaurant/([^/]+)/embed/?$\";s:43:\"index.php?restaurant=$matches[1]&embed=true\";s:31:\"restaurant/([^/]+)/trackback/?$\";s:37:\"index.php?restaurant=$matches[1]&tb=1\";s:51:\"restaurant/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?restaurant=$matches[1]&feed=$matches[2]\";s:46:\"restaurant/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?restaurant=$matches[1]&feed=$matches[2]\";s:39:\"restaurant/([^/]+)/page/?([0-9]{1,})/?$\";s:50:\"index.php?restaurant=$matches[1]&paged=$matches[2]\";s:46:\"restaurant/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?restaurant=$matches[1]&cpage=$matches[2]\";s:35:\"restaurant/([^/]+)(?:/([0-9]+))?/?$\";s:49:\"index.php?restaurant=$matches[1]&page=$matches[2]\";s:27:\"restaurant/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\"restaurant/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\"restaurant/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"restaurant/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"restaurant/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\"restaurant/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:36:\"hospital/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:46:\"hospital/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:66:\"hospital/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:61:\"hospital/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:61:\"hospital/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:42:\"hospital/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:25:\"hospital/([^/]+)/embed/?$\";s:41:\"index.php?hospital=$matches[1]&embed=true\";s:29:\"hospital/([^/]+)/trackback/?$\";s:35:\"index.php?hospital=$matches[1]&tb=1\";s:49:\"hospital/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?hospital=$matches[1]&feed=$matches[2]\";s:44:\"hospital/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?hospital=$matches[1]&feed=$matches[2]\";s:37:\"hospital/([^/]+)/page/?([0-9]{1,})/?$\";s:48:\"index.php?hospital=$matches[1]&paged=$matches[2]\";s:44:\"hospital/([^/]+)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?hospital=$matches[1]&cpage=$matches[2]\";s:33:\"hospital/([^/]+)(?:/([0-9]+))?/?$\";s:47:\"index.php?hospital=$matches[1]&page=$matches[2]\";s:25:\"hospital/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:35:\"hospital/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:55:\"hospital/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:50:\"hospital/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:50:\"hospital/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:31:\"hospital/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:32:\"tour/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:42:\"tour/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:62:\"tour/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:57:\"tour/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:57:\"tour/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:38:\"tour/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:21:\"tour/([^/]+)/embed/?$\";s:37:\"index.php?tour=$matches[1]&embed=true\";s:25:\"tour/([^/]+)/trackback/?$\";s:31:\"index.php?tour=$matches[1]&tb=1\";s:45:\"tour/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?tour=$matches[1]&feed=$matches[2]\";s:40:\"tour/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?tour=$matches[1]&feed=$matches[2]\";s:33:\"tour/([^/]+)/page/?([0-9]{1,})/?$\";s:44:\"index.php?tour=$matches[1]&paged=$matches[2]\";s:40:\"tour/([^/]+)/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?tour=$matches[1]&cpage=$matches[2]\";s:29:\"tour/([^/]+)(?:/([0-9]+))?/?$\";s:43:\"index.php?tour=$matches[1]&page=$matches[2]\";s:21:\"tour/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:31:\"tour/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:51:\"tour/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:46:\"tour/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:46:\"tour/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:27:\"tour/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:32:\"visa/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:42:\"visa/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:62:\"visa/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:57:\"visa/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:57:\"visa/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:38:\"visa/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:21:\"visa/([^/]+)/embed/?$\";s:46:\"index.php?visa_category=$matches[1]&embed=true\";s:25:\"visa/([^/]+)/trackback/?$\";s:31:\"index.php?visa=$matches[1]&tb=1\";s:45:\"visa/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?visa_category=$matches[1]&feed=$matches[2]\";s:40:\"visa/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?visa_category=$matches[1]&feed=$matches[2]\";s:33:\"visa/([^/]+)/page/?([0-9]{1,})/?$\";s:53:\"index.php?visa_category=$matches[1]&paged=$matches[2]\";s:40:\"visa/([^/]+)/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?visa=$matches[1]&cpage=$matches[2]\";s:29:\"visa/([^/]+)(?:/([0-9]+))?/?$\";s:43:\"index.php?visa=$matches[1]&page=$matches[2]\";s:21:\"visa/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:31:\"visa/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:51:\"visa/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:46:\"visa/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:46:\"visa/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:27:\"visa/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:39:\"travelguide/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:49:\"travelguide/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:69:\"travelguide/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"travelguide/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"travelguide/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:45:\"travelguide/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:28:\"travelguide/([^/]+)/embed/?$\";s:53:\"index.php?travelguide_category=$matches[1]&embed=true\";s:32:\"travelguide/([^/]+)/trackback/?$\";s:38:\"index.php?travelguide=$matches[1]&tb=1\";s:52:\"travelguide/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?travelguide_category=$matches[1]&feed=$matches[2]\";s:47:\"travelguide/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?travelguide_category=$matches[1]&feed=$matches[2]\";s:40:\"travelguide/([^/]+)/page/?([0-9]{1,})/?$\";s:60:\"index.php?travelguide_category=$matches[1]&paged=$matches[2]\";s:47:\"travelguide/([^/]+)/comment-page-([0-9]{1,})/?$\";s:51:\"index.php?travelguide=$matches[1]&cpage=$matches[2]\";s:36:\"travelguide/([^/]+)(?:/([0-9]+))?/?$\";s:50:\"index.php?travelguide=$matches[1]&page=$matches[2]\";s:28:\"travelguide/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:38:\"travelguide/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:58:\"travelguide/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"travelguide/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"travelguide/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:34:\"travelguide/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:38:\"travelnews/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:48:\"travelnews/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:68:\"travelnews/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:63:\"travelnews/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:63:\"travelnews/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:44:\"travelnews/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:27:\"travelnews/([^/]+)/embed/?$\";s:43:\"index.php?travelnews=$matches[1]&embed=true\";s:31:\"travelnews/([^/]+)/trackback/?$\";s:37:\"index.php?travelnews=$matches[1]&tb=1\";s:51:\"travelnews/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?travelnews=$matches[1]&feed=$matches[2]\";s:46:\"travelnews/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?travelnews=$matches[1]&feed=$matches[2]\";s:39:\"travelnews/([^/]+)/page/?([0-9]{1,})/?$\";s:50:\"index.php?travelnews=$matches[1]&paged=$matches[2]\";s:46:\"travelnews/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?travelnews=$matches[1]&cpage=$matches[2]\";s:35:\"travelnews/([^/]+)(?:/([0-9]+))?/?$\";s:49:\"index.php?travelnews=$matches[1]&page=$matches[2]\";s:27:\"travelnews/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\"travelnews/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\"travelnews/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"travelnews/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"travelnews/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\"travelnews/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:55:\"hotel-category/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:53:\"index.php?hotel_category=$matches[1]&feed=$matches[2]\";s:50:\"hotel-category/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:53:\"index.php?hotel_category=$matches[1]&feed=$matches[2]\";s:31:\"hotel-category/([^/]+)/embed/?$\";s:47:\"index.php?hotel_category=$matches[1]&embed=true\";s:43:\"hotel-category/([^/]+)/page/?([0-9]{1,})/?$\";s:54:\"index.php?hotel_category=$matches[1]&paged=$matches[2]\";s:25:\"hotel-category/([^/]+)/?$\";s:36:\"index.php?hotel_category=$matches[1]\";s:50:\"hotel-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?hotel_tag=$matches[1]&feed=$matches[2]\";s:45:\"hotel-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?hotel_tag=$matches[1]&feed=$matches[2]\";s:26:\"hotel-tag/([^/]+)/embed/?$\";s:42:\"index.php?hotel_tag=$matches[1]&embed=true\";s:38:\"hotel-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:49:\"index.php?hotel_tag=$matches[1]&paged=$matches[2]\";s:20:\"hotel-tag/([^/]+)/?$\";s:31:\"index.php?hotel_tag=$matches[1]\";s:18:\"airport/([^/]+)/?$\";s:38:\"index.php?airport_category=$matches[1]\";s:52:\"airport-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?airport_tag=$matches[1]&feed=$matches[2]\";s:47:\"airport-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?airport_tag=$matches[1]&feed=$matches[2]\";s:28:\"airport-tag/([^/]+)/embed/?$\";s:44:\"index.php?airport_tag=$matches[1]&embed=true\";s:40:\"airport-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?airport_tag=$matches[1]&paged=$matches[2]\";s:22:\"airport-tag/([^/]+)/?$\";s:33:\"index.php?airport_tag=$matches[1]\";s:61:\"destination-category/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?destination_category=$matches[1]&feed=$matches[2]\";s:56:\"destination-category/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?destination_category=$matches[1]&feed=$matches[2]\";s:37:\"destination-category/([^/]+)/embed/?$\";s:53:\"index.php?destination_category=$matches[1]&embed=true\";s:49:\"destination-category/([^/]+)/page/?([0-9]{1,})/?$\";s:60:\"index.php?destination_category=$matches[1]&paged=$matches[2]\";s:31:\"destination-category/([^/]+)/?$\";s:42:\"index.php?destination_category=$matches[1]\";s:56:\"destination-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:54:\"index.php?destination_tag=$matches[1]&feed=$matches[2]\";s:51:\"destination-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:54:\"index.php?destination_tag=$matches[1]&feed=$matches[2]\";s:32:\"destination-tag/([^/]+)/embed/?$\";s:48:\"index.php?destination_tag=$matches[1]&embed=true\";s:44:\"destination-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:55:\"index.php?destination_tag=$matches[1]&paged=$matches[2]\";s:26:\"destination-tag/([^/]+)/?$\";s:37:\"index.php?destination_tag=$matches[1]\";s:60:\"restaurant-category/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:58:\"index.php?restaurant_category=$matches[1]&feed=$matches[2]\";s:55:\"restaurant-category/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:58:\"index.php?restaurant_category=$matches[1]&feed=$matches[2]\";s:36:\"restaurant-category/([^/]+)/embed/?$\";s:52:\"index.php?restaurant_category=$matches[1]&embed=true\";s:48:\"restaurant-category/([^/]+)/page/?([0-9]{1,})/?$\";s:59:\"index.php?restaurant_category=$matches[1]&paged=$matches[2]\";s:30:\"restaurant-category/([^/]+)/?$\";s:41:\"index.php?restaurant_category=$matches[1]\";s:55:\"restaurant-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:53:\"index.php?restaurant_tag=$matches[1]&feed=$matches[2]\";s:50:\"restaurant-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:53:\"index.php?restaurant_tag=$matches[1]&feed=$matches[2]\";s:31:\"restaurant-tag/([^/]+)/embed/?$\";s:47:\"index.php?restaurant_tag=$matches[1]&embed=true\";s:43:\"restaurant-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:54:\"index.php?restaurant_tag=$matches[1]&paged=$matches[2]\";s:25:\"restaurant-tag/([^/]+)/?$\";s:36:\"index.php?restaurant_tag=$matches[1]\";s:58:\"hospital-category/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:56:\"index.php?hospital_category=$matches[1]&feed=$matches[2]\";s:53:\"hospital-category/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:56:\"index.php?hospital_category=$matches[1]&feed=$matches[2]\";s:34:\"hospital-category/([^/]+)/embed/?$\";s:50:\"index.php?hospital_category=$matches[1]&embed=true\";s:46:\"hospital-category/([^/]+)/page/?([0-9]{1,})/?$\";s:57:\"index.php?hospital_category=$matches[1]&paged=$matches[2]\";s:28:\"hospital-category/([^/]+)/?$\";s:39:\"index.php?hospital_category=$matches[1]\";s:53:\"hospital-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:51:\"index.php?hospital_tag=$matches[1]&feed=$matches[2]\";s:48:\"hospital-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:51:\"index.php?hospital_tag=$matches[1]&feed=$matches[2]\";s:29:\"hospital-tag/([^/]+)/embed/?$\";s:45:\"index.php?hospital_tag=$matches[1]&embed=true\";s:41:\"hospital-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:52:\"index.php?hospital_tag=$matches[1]&paged=$matches[2]\";s:23:\"hospital-tag/([^/]+)/?$\";s:34:\"index.php?hospital_tag=$matches[1]\";s:54:\"tour-category/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?tour_category=$matches[1]&feed=$matches[2]\";s:49:\"tour-category/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?tour_category=$matches[1]&feed=$matches[2]\";s:30:\"tour-category/([^/]+)/embed/?$\";s:46:\"index.php?tour_category=$matches[1]&embed=true\";s:42:\"tour-category/([^/]+)/page/?([0-9]{1,})/?$\";s:53:\"index.php?tour_category=$matches[1]&paged=$matches[2]\";s:24:\"tour-category/([^/]+)/?$\";s:35:\"index.php?tour_category=$matches[1]\";s:49:\"tour-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?tour_tag=$matches[1]&feed=$matches[2]\";s:44:\"tour-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?tour_tag=$matches[1]&feed=$matches[2]\";s:25:\"tour-tag/([^/]+)/embed/?$\";s:41:\"index.php?tour_tag=$matches[1]&embed=true\";s:37:\"tour-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:48:\"index.php?tour_tag=$matches[1]&paged=$matches[2]\";s:19:\"tour-tag/([^/]+)/?$\";s:30:\"index.php?tour_tag=$matches[1]\";s:15:\"visa/([^/]+)/?$\";s:35:\"index.php?visa_category=$matches[1]\";s:49:\"visa-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?visa_tag=$matches[1]&feed=$matches[2]\";s:44:\"visa-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?visa_tag=$matches[1]&feed=$matches[2]\";s:25:\"visa-tag/([^/]+)/embed/?$\";s:41:\"index.php?visa_tag=$matches[1]&embed=true\";s:37:\"visa-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:48:\"index.php?visa_tag=$matches[1]&paged=$matches[2]\";s:19:\"visa-tag/([^/]+)/?$\";s:30:\"index.php?visa_tag=$matches[1]\";s:22:\"travelguide/([^/]+)/?$\";s:42:\"index.php?travelguide_category=$matches[1]\";s:56:\"travelguide-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:54:\"index.php?travelguide_tag=$matches[1]&feed=$matches[2]\";s:51:\"travelguide-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:54:\"index.php?travelguide_tag=$matches[1]&feed=$matches[2]\";s:32:\"travelguide-tag/([^/]+)/embed/?$\";s:48:\"index.php?travelguide_tag=$matches[1]&embed=true\";s:44:\"travelguide-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:55:\"index.php?travelguide_tag=$matches[1]&paged=$matches[2]\";s:26:\"travelguide-tag/([^/]+)/?$\";s:37:\"index.php?travelguide_tag=$matches[1]\";s:54:\"news-category/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:58:\"index.php?travelnews_category=$matches[1]&feed=$matches[2]\";s:49:\"news-category/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:58:\"index.php?travelnews_category=$matches[1]&feed=$matches[2]\";s:30:\"news-category/([^/]+)/embed/?$\";s:52:\"index.php?travelnews_category=$matches[1]&embed=true\";s:42:\"news-category/([^/]+)/page/?([0-9]{1,})/?$\";s:59:\"index.php?travelnews_category=$matches[1]&paged=$matches[2]\";s:24:\"news-category/([^/]+)/?$\";s:41:\"index.php?travelnews_category=$matches[1]\";s:55:\"travelnews-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:53:\"index.php?travelnews_tag=$matches[1]&feed=$matches[2]\";s:50:\"travelnews-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:53:\"index.php?travelnews_tag=$matches[1]&feed=$matches[2]\";s:31:\"travelnews-tag/([^/]+)/embed/?$\";s:47:\"index.php?travelnews_tag=$matches[1]&embed=true\";s:43:\"travelnews-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:54:\"index.php?travelnews_tag=$matches[1]&paged=$matches[2]\";s:25:\"travelnews-tag/([^/]+)/?$\";s:36:\"index.php?travelnews_tag=$matches[1]\";s:46:\"tourism/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:46:\"index.php?tourism=$matches[1]&feed=$matches[2]\";s:41:\"tourism/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:46:\"index.php?tourism=$matches[1]&feed=$matches[2]\";s:22:\"tourism/(.+?)/embed/?$\";s:40:\"index.php?tourism=$matches[1]&embed=true\";s:34:\"tourism/(.+?)/page/?([0-9]{1,})/?$\";s:47:\"index.php?tourism=$matches[1]&paged=$matches[2]\";s:16:\"tourism/(.+?)/?$\";s:29:\"index.php?tourism=$matches[1]\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:13:\"favicon\\.ico$\";s:19:\"index.php?favicon=1\";s:12:\"sitemap\\.xml\";s:23:\"index.php?sitemap=index\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:58:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:68:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:88:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:83:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:83:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:64:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:53:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/embed/?$\";s:91:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/trackback/?$\";s:85:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&tb=1\";s:77:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:72:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:65:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&paged=$matches[5]\";s:72:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&cpage=$matches[5]\";s:61:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&page=$matches[5]\";s:47:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:57:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:77:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:53:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&cpage=$matches[4]\";s:51:\"([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]\";s:38:\"([0-9]{4})/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&cpage=$matches[2]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";}','on'),(31,'hack_file','0','on'),(32,'blog_charset','UTF-8','on'),(33,'moderation_keys','','off'),(34,'active_plugins','a:4:{i:0;s:33:\"classic-editor/classic-editor.php\";i:1;s:33:\"nextsafar-core/nextsafar-core.php\";i:2;s:27:\"redis-cache/redis-cache.php\";i:3;s:24:\"wordpress-seo/wp-seo.php\";}','on'),(35,'category_base','','on'),(36,'ping_sites','https://rpc.pingomatic.com/','on'),(37,'comment_max_links','2','on'),(38,'gmt_offset','0','on'),(39,'default_email_category','1','on'),(40,'recently_edited','','off'),(41,'template','twentytwentyfive','on'),(42,'stylesheet','twentytwentyfive','on'),(43,'comment_registration','0','on'),(44,'html_type','text/html','on'),(45,'use_trackback','0','on'),(46,'default_role','subscriber','on'),(47,'db_version','61833','on'),(48,'uploads_use_yearmonth_folders','1','on'),(49,'upload_path','','on'),(50,'blog_public','0','on'),(51,'default_link_category','2','on'),(52,'show_on_front','posts','on'),(53,'tag_base','','on'),(54,'show_avatars','1','on'),(55,'avatar_rating','G','on'),(56,'upload_url_path','','on'),(57,'thumbnail_size_w','150','on'),(58,'thumbnail_size_h','150','on'),(59,'thumbnail_crop','1','on'),(60,'medium_size_w','300','on'),(61,'medium_size_h','300','on'),(62,'avatar_default','mystery','on'),(63,'large_size_w','1024','on'),(64,'large_size_h','1024','on'),(65,'image_default_link_type','none','on'),(66,'image_default_size','','on'),(67,'image_default_align','','on'),(68,'close_comments_for_old_posts','0','on'),(69,'close_comments_days_old','14','on'),(70,'thread_comments','1','on'),(71,'thread_comments_depth','5','on'),(72,'page_comments','0','on'),(73,'comments_per_page','50','on'),(74,'default_comments_page','newest','on'),(75,'comment_order','asc','on'),(76,'sticky_posts','a:0:{}','on'),(77,'widget_categories','a:0:{}','on'),(78,'widget_text','a:0:{}','on'),(79,'widget_rss','a:0:{}','on'),(80,'uninstall_plugins','a:1:{s:24:\"wordpress-seo/wp-seo.php\";s:14:\"__return_false\";}','off'),(81,'timezone_string','','on'),(82,'page_for_posts','0','on'),(83,'page_on_front','0','on'),(84,'default_post_format','0','on'),(85,'link_manager_enabled','0','on'),(86,'finished_splitting_shared_terms','1','on'),(87,'site_icon','0','on'),(88,'medium_large_size_w','768','on'),(89,'medium_large_size_h','0','on'),(90,'wp_page_for_privacy_policy','3','on'),(91,'show_comments_cookies_opt_in','1','on'),(92,'admin_email_lifespan','1807041713','on'),(93,'disallowed_keys','','off'),(94,'comment_previously_approved','1','on'),(95,'auto_plugin_theme_update_emails','a:0:{}','off'),(96,'auto_update_core_dev','enabled','on'),(97,'auto_update_core_minor','enabled','on'),(98,'auto_update_core_major','enabled','on'),(99,'wp_force_deactivated_plugins','a:0:{}','on'),(100,'wp_attachment_pages_enabled','0','on'),(101,'wp_notes_notify','1','on'),(102,'initial_db_version','61833','on'),(103,'ns_user_roles','a:9:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:62:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:20:\"wpseo_manage_options\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:36:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:15:\"wpseo_bulk_edit\";b:1;s:28:\"wpseo_edit_advanced_metadata\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}s:13:\"wpseo_manager\";a:2:{s:4:\"name\";s:11:\"SEO Manager\";s:12:\"capabilities\";a:38:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:15:\"wpseo_bulk_edit\";b:1;s:28:\"wpseo_edit_advanced_metadata\";b:1;s:20:\"wpseo_manage_options\";b:1;s:23:\"view_site_health_checks\";b:1;}}s:12:\"wpseo_editor\";a:2:{s:4:\"name\";s:10:\"SEO Editor\";s:12:\"capabilities\";a:36:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:15:\"wpseo_bulk_edit\";b:1;s:28:\"wpseo_edit_advanced_metadata\";b:1;}}s:16:\"nextsafar_editor\";a:2:{s:4:\"name\";s:16:\"NextSafar Editor\";s:12:\"capabilities\";a:10:{s:4:\"read\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:12:\"upload_files\";b:1;s:10:\"edit_pages\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:17:\"edit_others_pages\";b:1;}}s:16:\"nextsafar_author\";a:2:{s:4:\"name\";s:16:\"NextSafar Author\";s:12:\"capabilities\";a:4:{s:4:\"read\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:12:\"upload_files\";b:1;}}}','on'),(104,'fresh_site','1','off'),(105,'user_count','1','off'),(106,'widget_block','a:6:{i:2;a:1:{s:7:\"content\";s:19:\"<!-- wp:search /-->\";}i:3;a:1:{s:7:\"content\";s:154:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Recent Posts</h2><!-- /wp:heading --><!-- wp:latest-posts /--></div><!-- /wp:group -->\";}i:4;a:1:{s:7:\"content\";s:227:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Recent Comments</h2><!-- /wp:heading --><!-- wp:latest-comments {\"displayAvatar\":false,\"displayDate\":false,\"displayExcerpt\":false} /--></div><!-- /wp:group -->\";}i:5;a:1:{s:7:\"content\";s:146:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Archives</h2><!-- /wp:heading --><!-- wp:archives /--></div><!-- /wp:group -->\";}i:6;a:1:{s:7:\"content\";s:150:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Categories</h2><!-- /wp:heading --><!-- wp:categories /--></div><!-- /wp:group -->\";}s:12:\"_multiwidget\";i:1;}','auto'),(107,'sidebars_widgets','a:4:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:3:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";}s:9:\"sidebar-2\";a:2:{i:0;s:7:\"block-5\";i:1;s:7:\"block-6\";}s:13:\"array_version\";i:3;}','auto'),(108,'widget_pages','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(109,'widget_calendar','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(110,'widget_archives','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(111,'widget_media_audio','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(112,'widget_media_image','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(113,'widget_media_gallery','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(114,'widget_media_video','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(115,'widget_meta','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(116,'widget_search','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(117,'widget_recent-posts','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(118,'widget_recent-comments','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(119,'widget_tag_cloud','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(120,'widget_nav_menu','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(121,'widget_custom_html','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(125,'theme_mods_twentytwentyfive','a:1:{s:18:\"custom_css_post_id\";i:-1;}','auto'),(128,'recovery_keys','a:0:{}','off'),(139,'can_compress_scripts','1','on'),(156,'recently_activated','a:0:{}','off'),(159,'finished_updating_comment_type','1','auto'),(164,'WPLANG','fa_IR','auto'),(165,'new_admin_email','admin@nextsafar.com','auto'),(180,'yoast_migrations_free','a:1:{s:7:\"version\";s:4:\"28.6\";}','auto'),(181,'wpseo','a:124:{s:8:\"tracking\";b:0;s:16:\"toggled_tracking\";b:0;s:22:\"license_server_version\";s:5:\"false\";s:15:\"ms_defaults_set\";b:0;s:40:\"ignore_search_engines_discouraged_notice\";b:1;s:19:\"indexing_first_time\";b:0;s:16:\"indexing_started\";b:0;s:15:\"indexing_reason\";s:20:\"taxonomy_made_public\";s:29:\"indexables_indexing_completed\";b:1;s:13:\"index_now_key\";s:0:\"\";s:7:\"version\";s:4:\"28.6\";s:16:\"previous_version\";s:0:\"\";s:20:\"disableadvanced_meta\";b:1;s:30:\"enable_headless_rest_endpoints\";b:1;s:17:\"ryte_indexability\";b:0;s:11:\"baiduverify\";s:0:\"\";s:12:\"googleverify\";s:0:\"\";s:8:\"msverify\";s:0:\"\";s:12:\"yandexverify\";s:0:\"\";s:12:\"ahrefsverify\";s:0:\"\";s:9:\"site_type\";s:0:\"\";s:20:\"has_multiple_authors\";s:0:\"\";s:16:\"environment_type\";s:0:\"\";s:23:\"content_analysis_active\";b:1;s:23:\"keyword_analysis_active\";b:1;s:34:\"inclusive_language_analysis_active\";b:0;s:21:\"enable_admin_bar_menu\";b:1;s:26:\"enable_cornerstone_content\";b:1;s:18:\"enable_xml_sitemap\";b:1;s:24:\"enable_text_link_counter\";b:1;s:16:\"enable_index_now\";b:1;s:19:\"enable_ai_generator\";b:1;s:22:\"ai_enabled_pre_default\";b:0;s:22:\"show_onboarding_notice\";b:1;s:18:\"first_activated_on\";s:10:\"1791490605\";s:13:\"myyoast-oauth\";b:0;s:26:\"semrush_integration_active\";b:1;s:14:\"semrush_tokens\";a:0:{}s:20:\"semrush_country_code\";s:2:\"us\";s:19:\"permalink_structure\";s:36:\"/%year%/%monthnum%/%day%/%postname%/\";s:8:\"home_url\";s:26:\"http://cms.nextsafar.local\";s:18:\"dynamic_permalinks\";b:0;s:17:\"category_base_url\";s:0:\"\";s:12:\"tag_base_url\";s:0:\"\";s:21:\"custom_taxonomy_slugs\";a:0:{}s:29:\"enable_enhanced_slack_sharing\";b:1;s:23:\"enable_metabox_insights\";b:1;s:23:\"enable_link_suggestions\";b:1;s:26:\"algolia_integration_active\";b:0;s:14:\"import_cursors\";a:0:{}s:13:\"workouts_data\";a:1:{s:13:\"configuration\";a:1:{s:13:\"finishedSteps\";a:0:{}}}s:28:\"configuration_finished_steps\";a:4:{i:0;s:15:\"optimizeSeoData\";i:1;s:18:\"siteRepresentation\";i:2;s:14:\"socialProfiles\";i:3;s:19:\"personalPreferences\";}s:36:\"dismiss_configuration_workout_notice\";b:1;s:34:\"dismiss_premium_deactivated_notice\";b:0;s:19:\"importing_completed\";a:0:{}s:26:\"wincher_integration_active\";b:1;s:14:\"wincher_tokens\";a:0:{}s:36:\"wincher_automatically_add_keyphrases\";b:0;s:18:\"wincher_website_id\";s:0:\"\";s:18:\"first_time_install\";b:0;s:34:\"should_redirect_after_install_free\";b:0;s:34:\"activation_redirect_timestamp_free\";s:10:\"1791490605\";s:18:\"remove_feed_global\";b:0;s:27:\"remove_feed_global_comments\";b:0;s:25:\"remove_feed_post_comments\";b:0;s:19:\"remove_feed_authors\";b:0;s:22:\"remove_feed_categories\";b:0;s:16:\"remove_feed_tags\";b:0;s:29:\"remove_feed_custom_taxonomies\";b:0;s:22:\"remove_feed_post_types\";b:0;s:18:\"remove_feed_search\";b:0;s:21:\"remove_atom_rdf_feeds\";b:0;s:17:\"remove_shortlinks\";b:0;s:21:\"remove_rest_api_links\";b:0;s:20:\"remove_rsd_wlw_links\";b:0;s:19:\"remove_oembed_links\";b:0;s:16:\"remove_generator\";b:0;s:20:\"remove_emoji_scripts\";b:0;s:24:\"remove_powered_by_header\";b:0;s:22:\"remove_pingback_header\";b:0;s:28:\"clean_campaign_tracking_urls\";b:0;s:16:\"clean_permalinks\";b:0;s:32:\"clean_permalinks_extra_variables\";s:0:\"\";s:14:\"search_cleanup\";b:0;s:20:\"search_cleanup_emoji\";b:0;s:23:\"search_cleanup_patterns\";b:0;s:22:\"search_character_limit\";i:50;s:20:\"deny_search_crawling\";b:0;s:21:\"deny_wp_json_crawling\";b:0;s:20:\"deny_adsbot_crawling\";b:0;s:19:\"deny_ccbot_crawling\";b:0;s:29:\"deny_google_extended_crawling\";b:0;s:20:\"deny_gptbot_crawling\";b:0;s:27:\"redirect_search_pretty_urls\";b:0;s:29:\"least_readability_ignore_list\";a:0:{}s:27:\"least_seo_score_ignore_list\";a:0:{}s:23:\"most_linked_ignore_list\";a:0:{}s:24:\"least_linked_ignore_list\";a:0:{}s:28:\"indexables_page_reading_list\";a:5:{i:0;b:0;i:1;b:0;i:2;b:0;i:3;b:0;i:4;b:0;}s:25:\"indexables_overview_state\";s:21:\"dashboard-not-visited\";s:28:\"last_known_public_post_types\";a:11:{i:0;s:4:\"post\";i:1;s:4:\"page\";i:2;s:5:\"hotel\";i:3;s:7:\"airport\";i:4;s:11:\"destination\";i:5;s:10:\"restaurant\";i:6;s:8:\"hospital\";i:7;s:4:\"tour\";i:8;s:4:\"visa\";i:9;s:11:\"travelguide\";i:10;s:10:\"travelnews\";}s:28:\"last_known_public_taxonomies\";a:22:{i:0;s:8:\"category\";i:1;s:8:\"post_tag\";i:2;s:11:\"post_format\";i:3;s:14:\"hotel_category\";i:4;s:9:\"hotel_tag\";i:5;s:16:\"airport_category\";i:6;s:11:\"airport_tag\";i:7;s:20:\"destination_category\";i:8;s:15:\"destination_tag\";i:9;s:19:\"restaurant_category\";i:10;s:14:\"restaurant_tag\";i:11;s:17:\"hospital_category\";i:12;s:12:\"hospital_tag\";i:13;s:13:\"tour_category\";i:14;s:8:\"tour_tag\";i:15;s:13:\"visa_category\";i:16;s:8:\"visa_tag\";i:17;s:20:\"travelguide_category\";i:18;s:15:\"travelguide_tag\";i:19;s:19:\"travelnews_category\";i:20;s:14:\"travelnews_tag\";i:21;s:7:\"tourism\";}s:23:\"last_known_no_unindexed\";a:4:{s:40:\"wpseo_total_unindexed_post_type_archives\";i:1791490678;s:31:\"wpseo_unindexed_post_link_count\";i:1791490678;s:31:\"wpseo_unindexed_term_link_count\";i:1791490678;s:27:\"wpseo_total_unindexed_posts\";i:1791491747;}s:14:\"new_post_types\";a:9:{i:2;s:5:\"hotel\";i:3;s:7:\"airport\";i:4;s:11:\"destination\";i:5;s:10:\"restaurant\";i:6;s:8:\"hospital\";i:7;s:4:\"tour\";i:8;s:4:\"visa\";i:9;s:11:\"travelguide\";i:10;s:10:\"travelnews\";}s:14:\"new_taxonomies\";a:19:{i:3;s:14:\"hotel_category\";i:4;s:9:\"hotel_tag\";i:5;s:16:\"airport_category\";i:6;s:11:\"airport_tag\";i:7;s:20:\"destination_category\";i:8;s:15:\"destination_tag\";i:9;s:19:\"restaurant_category\";i:10;s:14:\"restaurant_tag\";i:11;s:17:\"hospital_category\";i:12;s:12:\"hospital_tag\";i:13;s:13:\"tour_category\";i:14;s:8:\"tour_tag\";i:15;s:13:\"visa_category\";i:16;s:8:\"visa_tag\";i:17;s:20:\"travelguide_category\";i:18;s:15:\"travelguide_tag\";i:19;s:19:\"travelnews_category\";i:20;s:14:\"travelnews_tag\";i:21;s:7:\"tourism\";}s:34:\"show_new_content_type_notification\";b:1;s:44:\"site_kit_configuration_permanently_dismissed\";b:0;s:18:\"site_kit_connected\";b:0;s:37:\"site_kit_tracking_setup_widget_loaded\";s:3:\"yes\";s:41:\"site_kit_tracking_first_interaction_stage\";s:7:\"install\";s:40:\"site_kit_tracking_last_interaction_stage\";s:7:\"install\";s:52:\"site_kit_tracking_setup_widget_temporarily_dismissed\";s:2:\"no\";s:52:\"site_kit_tracking_setup_widget_permanently_dismissed\";s:2:\"no\";s:31:\"google_site_kit_feature_enabled\";b:0;s:25:\"ai_free_sparks_started_on\";s:4:\"null\";s:15:\"enable_llms_txt\";b:0;s:15:\"last_updated_on\";b:0;s:17:\"default_seo_title\";a:0:{}s:21:\"default_seo_meta_desc\";a:0:{}s:18:\"first_activated_by\";s:1:\"1\";s:34:\"enable_schema_aggregation_endpoint\";b:0;s:38:\"schema_aggregation_endpoint_enabled_on\";s:4:\"null\";s:16:\"enable_task_list\";b:1;s:13:\"enable_schema\";b:1;}','auto'),(182,'wpseo_titles','a:127:{s:17:\"forcerewritetitle\";b:0;s:9:\"separator\";s:7:\"sc-dash\";s:16:\"title-home-wpseo\";s:42:\"%%sitename%% %%page%% %%sep%% %%sitedesc%%\";s:18:\"title-author-wpseo\";s:51:\"%%name%%, نویسنده در %%sitename%% %%page%%\";s:19:\"title-archive-wpseo\";s:38:\"%%date%% %%page%% %%sep%% %%sitename%%\";s:18:\"title-search-wpseo\";s:84:\"شما برای %%searchphrase%% جستجو کردید %%page%% %%sep%% %%sitename%%\";s:15:\"title-404-wpseo\";s:45:\"صفحه پیدا نشد %%sep%% %%sitename%%\";s:25:\"social-title-author-wpseo\";s:8:\"%%name%%\";s:26:\"social-title-archive-wpseo\";s:8:\"%%date%%\";s:31:\"social-description-author-wpseo\";s:0:\"\";s:32:\"social-description-archive-wpseo\";s:0:\"\";s:29:\"social-image-url-author-wpseo\";s:0:\"\";s:30:\"social-image-url-archive-wpseo\";s:0:\"\";s:28:\"social-image-id-author-wpseo\";i:0;s:29:\"social-image-id-archive-wpseo\";i:0;s:19:\"metadesc-home-wpseo\";s:0:\"\";s:21:\"metadesc-author-wpseo\";s:0:\"\";s:22:\"metadesc-archive-wpseo\";s:0:\"\";s:9:\"rssbefore\";s:0:\"\";s:8:\"rssafter\";s:79:\"نوشته %%POSTLINK%% اولین بار در %%BLOGLINK%%. پدیدار شد.\";s:20:\"noindex-author-wpseo\";b:0;s:28:\"noindex-author-noposts-wpseo\";b:1;s:21:\"noindex-archive-wpseo\";b:1;s:14:\"disable-author\";b:0;s:12:\"disable-date\";b:0;s:19:\"disable-post_format\";b:0;s:18:\"disable-attachment\";b:1;s:20:\"breadcrumbs-404crumb\";s:38:\"خطای 404: صفحه پیدا نشد\";s:29:\"breadcrumbs-display-blog-page\";b:1;s:20:\"breadcrumbs-boldlast\";b:0;s:25:\"breadcrumbs-archiveprefix\";s:30:\"بایگانی‌ها برای\";s:18:\"breadcrumbs-enable\";b:1;s:16:\"breadcrumbs-home\";s:8:\"خانه\";s:18:\"breadcrumbs-prefix\";s:0:\"\";s:24:\"breadcrumbs-searchprefix\";s:39:\"شما جستجو نمودید برای\";s:15:\"breadcrumbs-sep\";s:2:\"»\";s:12:\"website_name\";s:13:\"NextSafar CMS\";s:11:\"person_name\";s:0:\"\";s:11:\"person_logo\";s:0:\"\";s:22:\"alternate_website_name\";s:0:\"\";s:12:\"company_logo\";s:0:\"\";s:12:\"company_name\";s:13:\"NextSafar CMS\";s:22:\"company_alternate_name\";s:0:\"\";s:17:\"company_or_person\";s:7:\"company\";s:25:\"company_or_person_user_id\";i:0;s:17:\"stripcategorybase\";b:1;s:26:\"open_graph_frontpage_title\";s:12:\"%%sitename%%\";s:25:\"open_graph_frontpage_desc\";s:0:\"\";s:26:\"open_graph_frontpage_image\";s:0:\"\";s:24:\"publishing_principles_id\";i:0;s:25:\"ownership_funding_info_id\";i:0;s:29:\"actionable_feedback_policy_id\";i:0;s:21:\"corrections_policy_id\";i:0;s:16:\"ethics_policy_id\";i:0;s:19:\"diversity_policy_id\";i:0;s:28:\"diversity_staffing_report_id\";i:0;s:15:\"org-description\";s:0:\"\";s:9:\"org-email\";s:0:\"\";s:9:\"org-phone\";s:0:\"\";s:14:\"org-legal-name\";s:0:\"\";s:17:\"org-founding-date\";s:0:\"\";s:20:\"org-number-employees\";s:0:\"\";s:10:\"org-vat-id\";s:0:\"\";s:10:\"org-tax-id\";s:0:\"\";s:7:\"org-iso\";s:0:\"\";s:8:\"org-duns\";s:0:\"\";s:11:\"org-leicode\";s:0:\"\";s:9:\"org-naics\";s:0:\"\";s:10:\"title-post\";s:39:\"%%title%% %%page%% %%sep%% %%sitename%%\";s:13:\"metadesc-post\";s:0:\"\";s:12:\"noindex-post\";b:0;s:23:\"display-metabox-pt-post\";b:1;s:23:\"post_types-post-maintax\";i:0;s:21:\"schema-page-type-post\";s:7:\"WebPage\";s:24:\"schema-article-type-post\";s:7:\"Article\";s:17:\"social-title-post\";s:9:\"%%title%%\";s:23:\"social-description-post\";s:0:\"\";s:21:\"social-image-url-post\";s:0:\"\";s:20:\"social-image-id-post\";i:0;s:10:\"title-page\";s:39:\"%%title%% %%page%% %%sep%% %%sitename%%\";s:13:\"metadesc-page\";s:0:\"\";s:12:\"noindex-page\";b:0;s:23:\"display-metabox-pt-page\";b:1;s:23:\"post_types-page-maintax\";i:0;s:21:\"schema-page-type-page\";s:7:\"WebPage\";s:24:\"schema-article-type-page\";s:4:\"None\";s:17:\"social-title-page\";s:9:\"%%title%%\";s:23:\"social-description-page\";s:0:\"\";s:21:\"social-image-url-page\";s:0:\"\";s:20:\"social-image-id-page\";i:0;s:16:\"title-attachment\";s:39:\"%%title%% %%page%% %%sep%% %%sitename%%\";s:19:\"metadesc-attachment\";s:0:\"\";s:18:\"noindex-attachment\";b:0;s:29:\"display-metabox-pt-attachment\";b:1;s:29:\"post_types-attachment-maintax\";i:0;s:27:\"schema-page-type-attachment\";s:7:\"WebPage\";s:30:\"schema-article-type-attachment\";s:4:\"None\";s:18:\"title-tax-category\";s:68:\"بایگانی‌های %%term_title%% %%page%% %%sep%% %%sitename%%\";s:21:\"metadesc-tax-category\";s:0:\"\";s:28:\"display-metabox-tax-category\";b:1;s:20:\"noindex-tax-category\";b:0;s:25:\"social-title-tax-category\";s:38:\"بایگانی‌های %%term_title%%\";s:31:\"social-description-tax-category\";s:0:\"\";s:29:\"social-image-url-tax-category\";s:0:\"\";s:28:\"social-image-id-tax-category\";i:0;s:26:\"taxonomy-category-ptparent\";i:0;s:18:\"title-tax-post_tag\";s:68:\"بایگانی‌های %%term_title%% %%page%% %%sep%% %%sitename%%\";s:21:\"metadesc-tax-post_tag\";s:0:\"\";s:28:\"display-metabox-tax-post_tag\";b:1;s:20:\"noindex-tax-post_tag\";b:0;s:25:\"social-title-tax-post_tag\";s:38:\"بایگانی‌های %%term_title%%\";s:31:\"social-description-tax-post_tag\";s:0:\"\";s:29:\"social-image-url-tax-post_tag\";s:0:\"\";s:28:\"social-image-id-tax-post_tag\";i:0;s:26:\"taxonomy-post_tag-ptparent\";i:0;s:21:\"title-tax-post_format\";s:68:\"بایگانی‌های %%term_title%% %%page%% %%sep%% %%sitename%%\";s:24:\"metadesc-tax-post_format\";s:0:\"\";s:31:\"display-metabox-tax-post_format\";b:1;s:23:\"noindex-tax-post_format\";b:1;s:28:\"social-title-tax-post_format\";s:38:\"بایگانی‌های %%term_title%%\";s:34:\"social-description-tax-post_format\";s:0:\"\";s:32:\"social-image-url-tax-post_format\";s:0:\"\";s:31:\"social-image-id-tax-post_format\";i:0;s:29:\"taxonomy-post_format-ptparent\";i:0;s:14:\"person_logo_id\";i:0;s:15:\"company_logo_id\";i:0;s:29:\"open_graph_frontpage_image_id\";i:0;}','auto'),(183,'wpseo_social','a:20:{s:13:\"facebook_site\";s:0:\"\";s:13:\"instagram_url\";s:0:\"\";s:12:\"linkedin_url\";s:0:\"\";s:11:\"myspace_url\";s:0:\"\";s:16:\"og_default_image\";s:0:\"\";s:19:\"og_default_image_id\";s:0:\"\";s:18:\"og_frontpage_title\";s:0:\"\";s:17:\"og_frontpage_desc\";s:0:\"\";s:18:\"og_frontpage_image\";s:0:\"\";s:21:\"og_frontpage_image_id\";s:0:\"\";s:9:\"opengraph\";b:1;s:13:\"pinterest_url\";s:0:\"\";s:15:\"pinterestverify\";s:0:\"\";s:7:\"twitter\";b:1;s:12:\"twitter_site\";s:0:\"\";s:17:\"twitter_card_type\";s:19:\"summary_large_image\";s:11:\"youtube_url\";s:0:\"\";s:13:\"wikipedia_url\";s:0:\"\";s:17:\"other_social_urls\";a:0:{}s:12:\"mastodon_url\";s:0:\"\";}','auto'),(184,'wpseo_llmstxt','a:7:{s:23:\"llms_txt_selection_mode\";s:4:\"auto\";s:13:\"about_us_page\";i:0;s:12:\"contact_page\";i:0;s:10:\"terms_page\";i:0;s:19:\"privacy_policy_page\";i:0;s:9:\"shop_page\";i:0;s:20:\"other_included_pages\";a:0:{}}','auto'),(185,'wpseo_tracking_only','a:3:{s:25:\"task_list_first_opened_on\";s:4:\"28.6\";s:22:\"task_first_actioned_on\";s:0:\"\";s:36:\"frontend_inspector_first_actioned_on\";s:0:\"\";}','auto'),(206,'_transient_timeout_settings_errors','1791490643','off'),(207,'_transient_settings_errors','a:1:{i:0;a:4:{s:7:\"setting\";s:11:\"redis-cache\";s:4:\"code\";s:6:\"dropin\";s:7:\"message\";s:21:\"Object cache enabled.\";s:4:\"type\";s:7:\"updated\";}}','off'),(208,'nextsafar_geo_table_version','1.0.1','auto'),(209,'ns_booking_table_version','1.0.0','auto'),(210,'ns_payment_table_version','1.0.0','auto'),(211,'ns_booking_passenger_table_version','1.0.0','auto'),(212,'ns_booking_document_table_version','1.0.0','auto'),(213,'nextsafar_version','2.5.0','auto'),(214,'nextsafar_activated_at','2026-10-08 20:35:33','auto'),(215,'ns_ai_trip_table_version','1.1.0','auto'),(217,'ns_currency_table_version','1.0.0','auto'),(219,'nextsafar_news_tables_version','3.1.0','auto'),(220,'nextsafar_gallery_migrated_v1','1791491734','auto'),(221,'nextsafar_db_version','1.1.0','auto');
/*!40000 ALTER TABLE `ns_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_postmeta`
--

DROP TABLE IF EXISTS `ns_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_postmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_postmeta`
--

LOCK TABLES `ns_postmeta` WRITE;
/*!40000 ALTER TABLE `ns_postmeta` DISABLE KEYS */;
INSERT INTO `ns_postmeta` VALUES (1,2,'_wp_page_template','default'),(2,3,'_wp_page_template','default');
/*!40000 ALTER TABLE `ns_postmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_posts`
--

DROP TABLE IF EXISTS `ns_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_posts` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_excerpt` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `to_ping` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pinged` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_parent` bigint unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `menu_order` int NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`),
  KEY `type_status_author` (`post_type`,`post_status`,`post_author`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_posts`
--

LOCK TABLES `ns_posts` WRITE;
/*!40000 ALTER TABLE `ns_posts` DISABLE KEYS */;
INSERT INTO `ns_posts` VALUES (1,1,'2026-10-08 20:01:53','2026-10-08 20:01:53','<!-- wp:paragraph -->\n<p>Welcome to WordPress. This is your first post. Edit or delete it, then start writing!</p>\n<!-- /wp:paragraph -->','Hello world!','','publish','open','open','','hello-world','','','2026-10-08 20:01:53','2026-10-08 20:01:53','',0,'http://cms.nextsafar.local/?p=1',0,'post','',1),(2,1,'2026-10-08 20:01:53','2026-10-08 20:01:53','<!-- wp:paragraph -->\n<p>This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\">\n<!-- wp:paragraph -->\n<p>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin\' caught in the rain.)</p>\n<!-- /wp:paragraph -->\n</blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>...or something like this:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\">\n<!-- wp:paragraph -->\n<p>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</p>\n<!-- /wp:paragraph -->\n</blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>As a new WordPress user, you should go to <a href=\"http://cms.nextsafar.local/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!</p>\n<!-- /wp:paragraph -->','Sample Page','','publish','closed','open','','sample-page','','','2026-10-08 20:01:53','2026-10-08 20:01:53','',0,'http://cms.nextsafar.local/?page_id=2',0,'page','',0),(3,1,'2026-10-08 20:01:53','2026-10-08 20:01:53','<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Who we are</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Our website address is: http://cms.nextsafar.local.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Comments</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Media</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Cookies</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>If you visit our login page, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Embedded content from other websites</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Who we share your data with</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you request a password reset, your IP address will be included in the reset email.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">How long we retain your data</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">What rights you have over your data</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Where your data is sent</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Visitor comments may be checked through an automated spam detection service.</p>\n<!-- /wp:paragraph -->\n','Privacy Policy','','draft','closed','open','','privacy-policy','','','2026-10-08 20:01:53','2026-10-08 20:01:53','',0,'http://cms.nextsafar.local/?page_id=3',0,'page','',0),(4,0,'2026-10-08 20:01:54','2026-10-08 20:01:54','<!-- wp:page-list /-->','Navigation','','publish','closed','closed','','navigation','','','2026-10-08 20:01:54','2026-10-08 20:01:54','',0,'http://cms.nextsafar.local/2026/10/08/navigation/',0,'wp_navigation','',0),(5,1,'2026-10-08 20:02:05','0000-00-00 00:00:00','','Auto Draft','','auto-draft','open','open','','','','','2026-10-08 20:02:05','0000-00-00 00:00:00','',0,'http://cms.nextsafar.local/?p=5',0,'post','',0);
/*!40000 ALTER TABLE `ns_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_price_history`
--

DROP TABLE IF EXISTS `ns_price_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_price_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hotel_post_id` bigint unsigned DEFAULT NULL,
  `hotel_external_id` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT 'USD',
  `source` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT 'searchapi',
  `recorded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hotel_post_id` (`hotel_post_id`),
  KEY `hotel_external_id` (`hotel_external_id`),
  KEY `check_in` (`check_in`),
  KEY `recorded_at` (`recorded_at`),
  KEY `idx_post_date` (`hotel_post_id`,`check_in`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_price_history`
--

LOCK TABLES `ns_price_history` WRITE;
/*!40000 ALTER TABLE `ns_price_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_price_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_term_relationships`
--

DROP TABLE IF EXISTS `ns_term_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_term_relationships` (
  `object_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_term_relationships`
--

LOCK TABLES `ns_term_relationships` WRITE;
/*!40000 ALTER TABLE `ns_term_relationships` DISABLE KEYS */;
INSERT INTO `ns_term_relationships` VALUES (1,1,0);
/*!40000 ALTER TABLE `ns_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_term_taxonomy`
--

DROP TABLE IF EXISTS `ns_term_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_term_taxonomy` (
  `term_taxonomy_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `parent` bigint unsigned NOT NULL DEFAULT '0',
  `count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_term_taxonomy`
--

LOCK TABLES `ns_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `ns_term_taxonomy` DISABLE KEYS */;
INSERT INTO `ns_term_taxonomy` VALUES (1,1,'category','',0,1);
/*!40000 ALTER TABLE `ns_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_termmeta`
--

DROP TABLE IF EXISTS `ns_termmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_termmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_termmeta`
--

LOCK TABLES `ns_termmeta` WRITE;
/*!40000 ALTER TABLE `ns_termmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_termmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_terms`
--

DROP TABLE IF EXISTS `ns_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_terms` (
  `term_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `slug` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `term_group` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_terms`
--

LOCK TABLES `ns_terms` WRITE;
/*!40000 ALTER TABLE `ns_terms` DISABLE KEYS */;
INSERT INTO `ns_terms` VALUES (1,'Uncategorized','uncategorized',0);
/*!40000 ALTER TABLE `ns_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_usermeta`
--

DROP TABLE IF EXISTS `ns_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_usermeta` (
  `umeta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_usermeta`
--

LOCK TABLES `ns_usermeta` WRITE;
/*!40000 ALTER TABLE `ns_usermeta` DISABLE KEYS */;
INSERT INTO `ns_usermeta` VALUES (1,1,'nickname','admin'),(2,1,'first_name',''),(3,1,'last_name',''),(4,1,'description',''),(5,1,'rich_editing','true'),(6,1,'syntax_highlighting','true'),(7,1,'infinite_scrolling','true'),(8,1,'comment_shortcuts','false'),(9,1,'admin_color','modern'),(10,1,'use_ssl','0'),(11,1,'show_admin_bar_front','true'),(12,1,'locale',''),(13,1,'ns_capabilities','a:1:{s:13:\"administrator\";b:1;}'),(14,1,'ns_user_level','10'),(15,1,'dismissed_wp_pointers',''),(16,1,'show_welcome_panel','0'),(17,1,'session_tokens','a:1:{s:64:\"1184f163ec0f73bf8da03d3491eff8c54a0ebf5a68382dccdc4a8acabc7e7c85\";a:4:{s:10:\"expiration\";i:1792699322;s:2:\"ip\";s:9:\"127.0.0.1\";s:2:\"ua\";s:109:\"Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36\";s:5:\"login\";i:1791489722;}}'),(18,1,'ns_dashboard_quick_press_last_post_id','5'),(19,1,'community-events-location','a:1:{s:2:\"ip\";s:9:\"127.0.0.0\";}'),(20,1,'ns_yoast_notifications','a:3:{i:0;a:2:{s:7:\"message\";s:257:\"شما نوع جدیدی از محتوا را اضافه کرده اید. توصیه می کنیم <a href=\"http://cms.nextsafar.local/wp-admin/admin.php?page=wpseo_page_settings\">تنظیمات ظاهری جستجو</a> مربوطه را مرور کنید.\";s:7:\"options\";a:11:{s:4:\"type\";s:7:\"warning\";s:2:\"id\";s:25:\"content-types-made-public\";s:7:\"user_id\";i:1;s:5:\"nonce\";N;s:8:\"priority\";d:0.8;s:9:\"data_json\";a:0:{}s:13:\"dismissal_key\";N;s:12:\"capabilities\";s:20:\"wpseo_manage_options\";s:16:\"capability_check\";s:3:\"all\";s:14:\"yoast_branding\";b:0;s:13:\"resolve_nonce\";s:0:\"\";}}i:1;a:2:{s:7:\"message\";s:599:\"<p>ما نیاز داریم بعضی از اطلاعات سئوی سایت شما را مجددا بررسی کنیم ، چون در حالت مشاهده طبقه بندی ها تغییراتی ایجاد شده است. لطفا برای انجام این کار با فعالسازی بهینه سازی سئو به ما کمک کنید. پیش بینی می کنیم این کار در کمتر از چند دقیقه انجام شود</p><a class=\"button\" href=\"http://cms.nextsafar.local/wp-admin/admin.php?page=wpseo_tools&start-indexation=true\">شروع بهینه سازی اطلاعات</a>\";s:7:\"options\";a:11:{s:4:\"type\";s:7:\"warning\";s:2:\"id\";s:13:\"wpseo-reindex\";s:7:\"user_id\";i:1;s:5:\"nonce\";N;s:8:\"priority\";d:0.8;s:9:\"data_json\";a:0:{}s:13:\"dismissal_key\";N;s:12:\"capabilities\";s:20:\"wpseo_manage_options\";s:16:\"capability_check\";s:3:\"all\";s:14:\"yoast_branding\";b:0;s:13:\"resolve_nonce\";s:0:\"\";}}i:2;a:2:{s:7:\"message\";s:421:\"<p>به نظر می‌رسد تازه کار هستید. Yoast SEO به شما کمک می‌کند وب‌سایت خود را به‌راحتی برای موتورهای جستجو بهینه کنید. می‌خواهید سایتتان سالم بماند و راحت‌تر پیدا شود؟ در زیر نام‌نویسی کنید تا ایمیل‌های کاربردی برای شروع دریافت کنید!</p>\";s:7:\"options\";a:11:{s:4:\"type\";s:7:\"warning\";s:2:\"id\";s:23:\"wpseo-ping-other-admins\";s:7:\"user_id\";i:1;s:5:\"nonce\";N;s:8:\"priority\";i:1;s:9:\"data_json\";a:0:{}s:13:\"dismissal_key\";N;s:12:\"capabilities\";a:1:{i:0;s:20:\"wpseo_manage_options\";}s:16:\"capability_check\";s:3:\"all\";s:14:\"yoast_branding\";b:0;s:13:\"resolve_nonce\";s:10:\"be48d8be49\";}}}'),(21,1,'_yoast_wpseo_introductions','a:1:{s:30:\"schema-aggregator-announcement\";a:2:{s:7:\"is_seen\";b:1;s:7:\"seen_on\";i:1791490633;}}'),(22,1,'_yoast_wpseo_bulk_editor_tour_opt_in_notification_seen','1'),(23,1,'_yoast_alerts_dismissed','a:1:{s:26:\"webinar-promo-notification\";b:1;}'),(24,1,'roc_dismissed_pro_release_notice','1');
/*!40000 ALTER TABLE `ns_usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_users`
--

DROP TABLE IF EXISTS `ns_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_users` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_status` int NOT NULL DEFAULT '0',
  `display_name` varchar(250) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_users`
--

LOCK TABLES `ns_users` WRITE;
/*!40000 ALTER TABLE `ns_users` DISABLE KEYS */;
INSERT INTO `ns_users` VALUES (1,'admin','$wp$2y$10$uji43XufHCJozeOviOL5..ibKlA7MH2z9OueW1mMa6DSF.EaABF7y','admin','admin@nextsafar.com','http://cms.nextsafar.local','2026-10-08 20:01:53','',0,'admin');
/*!40000 ALTER TABLE `ns_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_yoast_expiring_store`
--

DROP TABLE IF EXISTS `ns_yoast_expiring_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_yoast_expiring_store` (
  `key_name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `exp` datetime NOT NULL,
  PRIMARY KEY (`key_name`),
  KEY `exp_index` (`exp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_yoast_expiring_store`
--

LOCK TABLES `ns_yoast_expiring_store` WRITE;
/*!40000 ALTER TABLE `ns_yoast_expiring_store` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_yoast_expiring_store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_yoast_indexable`
--

DROP TABLE IF EXISTS `ns_yoast_indexable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_yoast_indexable` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `permalink` longtext COLLATE utf8mb4_unicode_520_ci,
  `permalink_hash` varchar(40) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `object_id` bigint DEFAULT NULL,
  `object_type` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `object_sub_type` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `author_id` bigint DEFAULT NULL,
  `post_parent` bigint DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_520_ci,
  `description` mediumtext COLLATE utf8mb4_unicode_520_ci,
  `breadcrumb_title` text COLLATE utf8mb4_unicode_520_ci,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT NULL,
  `is_protected` tinyint(1) DEFAULT '0',
  `has_public_posts` tinyint(1) DEFAULT NULL,
  `number_of_pages` int unsigned DEFAULT NULL,
  `canonical` longtext COLLATE utf8mb4_unicode_520_ci,
  `primary_focus_keyword` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `primary_focus_keyword_score` int DEFAULT NULL,
  `readability_score` int DEFAULT NULL,
  `is_cornerstone` tinyint(1) DEFAULT '0',
  `is_robots_noindex` tinyint(1) DEFAULT '0',
  `is_robots_nofollow` tinyint(1) DEFAULT '0',
  `is_robots_noarchive` tinyint(1) DEFAULT '0',
  `is_robots_noimageindex` tinyint(1) DEFAULT '0',
  `is_robots_nosnippet` tinyint(1) DEFAULT '0',
  `twitter_title` text COLLATE utf8mb4_unicode_520_ci,
  `twitter_image` longtext COLLATE utf8mb4_unicode_520_ci,
  `twitter_description` longtext COLLATE utf8mb4_unicode_520_ci,
  `twitter_image_id` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `twitter_image_source` text COLLATE utf8mb4_unicode_520_ci,
  `open_graph_title` text COLLATE utf8mb4_unicode_520_ci,
  `open_graph_description` longtext COLLATE utf8mb4_unicode_520_ci,
  `open_graph_image` longtext COLLATE utf8mb4_unicode_520_ci,
  `open_graph_image_id` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `open_graph_image_source` text COLLATE utf8mb4_unicode_520_ci,
  `open_graph_image_meta` mediumtext COLLATE utf8mb4_unicode_520_ci,
  `link_count` int DEFAULT NULL,
  `incoming_link_count` int DEFAULT NULL,
  `prominent_words_version` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `blog_id` bigint NOT NULL DEFAULT '1',
  `language` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `region` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `schema_page_type` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `schema_article_type` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `has_ancestors` tinyint(1) DEFAULT '0',
  `estimated_reading_time_minutes` int DEFAULT NULL,
  `version` int DEFAULT '1',
  `object_last_modified` datetime DEFAULT NULL,
  `object_published_at` datetime DEFAULT NULL,
  `inclusive_language_score` int DEFAULT NULL,
  `seo_title_score` int DEFAULT NULL,
  `meta_description_score` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_type_and_sub_type` (`object_type`,`object_sub_type`),
  KEY `object_id_and_type` (`object_id`,`object_type`),
  KEY `permalink_hash_and_object_type` (`permalink_hash`,`object_type`),
  KEY `subpages` (`post_parent`,`object_type`,`post_status`,`object_id`),
  KEY `prominent_words` (`prominent_words_version`,`object_type`,`object_sub_type`,`post_status`),
  KEY `published_sitemap_index` (`object_published_at`,`is_robots_noindex`,`object_type`,`object_sub_type`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_yoast_indexable`
--

LOCK TABLES `ns_yoast_indexable` WRITE;
/*!40000 ALTER TABLE `ns_yoast_indexable` DISABLE KEYS */;
INSERT INTO `ns_yoast_indexable` VALUES (1,'http://cms.nextsafar.local/author/admin/','40:fa0888d03555baaed899fb392c25865d',1,'user',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,'https://secure.gravatar.com/avatar/5958af39c21cee4fa6c5158a2ccdacfe6485bb8af5e57f8f7718c8923d38d9a6?s=500&d=mm&r=g',NULL,NULL,'gravatar-image',NULL,NULL,'https://secure.gravatar.com/avatar/5958af39c21cee4fa6c5158a2ccdacfe6485bb8af5e57f8f7718c8923d38d9a6?s=500&d=mm&r=g',NULL,'gravatar-image',NULL,NULL,NULL,NULL,'2026-10-08 20:16:45','2026-10-08 16:46:45',1,NULL,NULL,NULL,NULL,0,NULL,2,'2026-10-08 20:01:53','2026-10-08 20:01:53',NULL,NULL,NULL),(2,'http://cms.nextsafar.local/','27:d10ffae9676b7fb77d5e882ea929ac09',NULL,'home-page',NULL,NULL,NULL,'%%sitename%% %%page%% %%sep%% %%sitedesc%%','','خانه',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,1,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'%%sitename%%','','','0',NULL,NULL,NULL,NULL,NULL,'2026-10-08 20:16:45','2026-10-08 16:46:45',1,NULL,NULL,NULL,NULL,0,NULL,2,'2026-10-08 20:01:53','2026-10-08 20:01:53',NULL,NULL,NULL),(3,NULL,NULL,1,'term','category',NULL,NULL,NULL,NULL,'Uncategorized',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2026-10-08 20:17:25','2026-10-08 20:17:58',1,NULL,NULL,NULL,NULL,0,NULL,0,'2026-10-08 20:01:53','2026-10-08 20:01:53',NULL,NULL,NULL),(4,'http://cms.nextsafar.local/?page_id=3','37:92a68e416b882f705db0d92b8b9463d7',3,'post','page',1,0,NULL,NULL,'Privacy Policy','draft',0,0,NULL,NULL,NULL,NULL,NULL,0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-08 20:17:25','2026-10-08 16:47:25',1,NULL,NULL,NULL,NULL,0,NULL,2,'2026-10-08 20:01:53','2026-10-08 20:01:53',0,0,0),(5,'http://cms.nextsafar.local/sample-page/','39:2a1fc14dbc352d103b6ed717ca942ea6',2,'post','page',1,0,NULL,NULL,'Sample Page','publish',NULL,0,NULL,NULL,NULL,NULL,NULL,0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'2026-10-08 20:17:25','2026-10-08 16:47:25',1,NULL,NULL,NULL,NULL,0,NULL,2,'2026-10-08 20:01:53','2026-10-08 20:01:53',0,0,0),(6,'http://cms.nextsafar.local/2026/10/08/hello-world/','50:e3bbdd14a79c21ee05d5125f70c668f1',1,'post','post',1,0,NULL,NULL,'Hello world!','publish',NULL,0,NULL,NULL,NULL,NULL,NULL,0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2026-10-08 20:17:25','2026-10-08 16:47:25',1,NULL,NULL,NULL,NULL,0,NULL,2,'2026-10-08 20:01:53','2026-10-08 20:01:53',0,0,0),(7,NULL,NULL,NULL,'system-page','404',NULL,NULL,'صفحه پیدا نشد %%sep%% %%sitename%%',NULL,'خطای 404: صفحه پیدا نشد',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,1,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-08 20:17:25','2026-10-08 16:47:25',1,NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,NULL,NULL),(8,NULL,NULL,NULL,'system-page','search-result',NULL,NULL,'شما برای %%searchphrase%% جستجو کردید %%page%% %%sep%% %%sitename%%',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,1,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-08 20:17:25','2026-10-08 16:47:25',1,NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,NULL,NULL),(9,NULL,NULL,NULL,'date-archive',NULL,NULL,NULL,'%%date%% %%page%% %%sep%% %%sitename%%','',NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,0,1,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-08 20:17:25','2026-10-08 16:47:25',1,NULL,NULL,NULL,NULL,0,NULL,1,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ns_yoast_indexable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_yoast_indexable_hierarchy`
--

DROP TABLE IF EXISTS `ns_yoast_indexable_hierarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_yoast_indexable_hierarchy` (
  `indexable_id` int unsigned NOT NULL,
  `ancestor_id` int unsigned NOT NULL,
  `depth` int unsigned DEFAULT NULL,
  `blog_id` bigint NOT NULL DEFAULT '1',
  PRIMARY KEY (`indexable_id`,`ancestor_id`),
  KEY `indexable_id` (`indexable_id`),
  KEY `ancestor_id` (`ancestor_id`),
  KEY `depth` (`depth`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_yoast_indexable_hierarchy`
--

LOCK TABLES `ns_yoast_indexable_hierarchy` WRITE;
/*!40000 ALTER TABLE `ns_yoast_indexable_hierarchy` DISABLE KEYS */;
INSERT INTO `ns_yoast_indexable_hierarchy` VALUES (1,0,0,1),(3,0,0,1),(4,0,0,1),(5,0,0,1),(6,0,0,1);
/*!40000 ALTER TABLE `ns_yoast_indexable_hierarchy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_yoast_migrations`
--

DROP TABLE IF EXISTS `ns_yoast_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_yoast_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ns_yoast_migrations_version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_yoast_migrations`
--

LOCK TABLES `ns_yoast_migrations` WRITE;
/*!40000 ALTER TABLE `ns_yoast_migrations` DISABLE KEYS */;
INSERT INTO `ns_yoast_migrations` VALUES (1,'20171228151840'),(2,'20171228151841'),(3,'20190529075038'),(4,'20191011111109'),(5,'20200408101900'),(6,'20200420073606'),(7,'20200428123747'),(8,'20200428194858'),(9,'20200429105310'),(10,'20200430075614'),(11,'20200430150130'),(12,'20200507054848'),(13,'20200513133401'),(14,'20200609154515'),(15,'20200616130143'),(16,'20200617122511'),(17,'20200702141921'),(18,'20200728095334'),(19,'20201202144329'),(20,'20201216124002'),(21,'20201216141134'),(22,'20210817092415'),(23,'20211020091404'),(24,'20230417083836'),(25,'20260105111111'),(26,'20260325155530'),(27,'20260709144332');
/*!40000 ALTER TABLE `ns_yoast_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_yoast_primary_term`
--

DROP TABLE IF EXISTS `ns_yoast_primary_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_yoast_primary_term` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint DEFAULT NULL,
  `term_id` bigint DEFAULT NULL,
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `blog_id` bigint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `post_taxonomy` (`post_id`,`taxonomy`),
  KEY `post_term` (`post_id`,`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_yoast_primary_term`
--

LOCK TABLES `ns_yoast_primary_term` WRITE;
/*!40000 ALTER TABLE `ns_yoast_primary_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `ns_yoast_primary_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ns_yoast_seo_links`
--

DROP TABLE IF EXISTS `ns_yoast_seo_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ns_yoast_seo_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `post_id` bigint unsigned DEFAULT NULL,
  `target_post_id` bigint unsigned DEFAULT NULL,
  `type` varchar(8) DEFAULT NULL,
  `indexable_id` int unsigned DEFAULT NULL,
  `target_indexable_id` int unsigned DEFAULT NULL,
  `height` int unsigned DEFAULT NULL,
  `width` int unsigned DEFAULT NULL,
  `size` int unsigned DEFAULT NULL,
  `language` varchar(32) DEFAULT NULL,
  `region` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `link_direction` (`post_id`,`type`),
  KEY `indexable_link_direction` (`indexable_id`,`type`),
  KEY `url_index` (`url`),
  KEY `target_indexable_id_index` (`target_indexable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ns_yoast_seo_links`
--

LOCK TABLES `ns_yoast_seo_links` WRITE;
/*!40000 ALTER TABLE `ns_yoast_seo_links` DISABLE KEYS */;
INSERT INTO `ns_yoast_seo_links` VALUES (1,'http://cms.nextsafar.local/wp-admin/',2,NULL,'internal',5,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ns_yoast_seo_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@nextsafar.com',NULL,'$2y$12$Vp8Xt.JIFXkS.rolTHGGHOUnJS8J6QiILC99lKpw7pDXGxfsVjfj6',NULL,'2026-10-08 13:57:21','2026-10-08 13:57:21');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-09  0:07:34
