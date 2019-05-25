-- --------------------------------------------------------
-- Host:                         localhost
-- Versi server:                 10.1.38-MariaDB-cll-lve - MariaDB Server
-- OS Server:                    Linux
-- HeidiSQL Versi:               10.1.0.5574
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;


-- Membuang struktur basisdata untuk wintenbot
CREATE DATABASE IF NOT EXISTS `wintenbot` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `wintenbot`;

-- membuang struktur untuk table wintenbot.anti_malfiles
CREATE TABLE IF NOT EXISTS `anti_malfiles`
(
    `id`           int(11)     NOT NULL AUTO_INCREMENT,
    `file_id`      varchar(50) NOT NULL,
    `blocked_by`   varchar(20) NOT NULL,
    `blocked_from` varchar(20) NOT NULL,
    `created_at`   timestamp   NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   timestamp   NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`, `file_id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 4
  DEFAULT CHARSET = latin1;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table wintenbot.fbans
CREATE TABLE IF NOT EXISTS `fbans`
(
    `id`          int(11)     NOT NULL AUTO_INCREMENT,
    `user_id`     varchar(20) NOT NULL DEFAULT '0',
    `reason_ban`  text,
    `banned_by`   varchar(20) NOT NULL DEFAULT '0',
    `banned_from` varchar(25) NOT NULL DEFAULT '0',
    `created_at`  timestamp   NULL     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  timestamp   NULL     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`, `user_id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 48
  DEFAULT CHARSET = latin1;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table wintenbot.fbans_admin
CREATE TABLE IF NOT EXISTS `fbans_admin`
(
    `id`            int(11)     NOT NULL AUTO_INCREMENT,
    `user_id`       varchar(20) NOT NULL DEFAULT '0',
    `username`      varchar(130)         DEFAULT NULL,
    `promoted_by`   varchar(20)          DEFAULT NULL,
    `promoted_from` varchar(20)          DEFAULT '0',
    `is_banned`     tinyint(1)           DEFAULT '0',
    `created_at`    timestamp   NULL     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    timestamp   NULL     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 22
  DEFAULT CHARSET = latin1;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table wintenbot.group_settings
CREATE TABLE IF NOT EXISTS `group_settings`
(
    `id`                               int(10) unsigned                                             NOT NULL AUTO_INCREMENT,
    `chat_id`                          varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `chat_title`                       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         DEFAULT '',
    `is_admin`                         tinyint(1)                                                            DEFAULT '0',
    `enable_bot`                       tinyint(1)                                                   NOT NULL DEFAULT '1',
    `enable_badword_filter`            tinyint(1)                                                            DEFAULT '1',
    `enable_url_filtering`             tinyint(1)                                                   NOT NULL DEFAULT '1',
    `enable_human_verification`        tinyint(1)                                                   NOT NULL DEFAULT '0',
    `enable_federation_ban`            tinyint(1)                                                   NOT NULL DEFAULT '1',
    `enable_restriction`               tinyint(1)                                                            DEFAULT '0',
    `enable_security`                  tinyint(1)                                                            DEFAULT '1',
    `enable_unified_welcome`           tinyint(1)                                                            DEFAULT '1',
    `enable_warn_username`             tinyint(1) unsigned                                                   DEFAULT '0',
    `last_welcome_message_id`          varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          DEFAULT '',
    `last_tags_message_id`             varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          DEFAULT '',
    `last_setting_message_id`          varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          DEFAULT '',
    `last_warning_username_message_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci          DEFAULT '',
    `rules_link`                       text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `rules_text`                       text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `warning_username_limit`           int(20)                                                               DEFAULT '7',
    `welcome_message`                  text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `welcome_button`                   text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `members_count`                    int(11)                                                               DEFAULT '0',
    `created_at`                       timestamp                                                    NULL     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`                       timestamp                                                    NULL     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `Index 2` (`chat_id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 538
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_swedish_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table wintenbot.spells
CREATE TABLE IF NOT EXISTS `spells`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `typo`       varchar(100)   DEFAULT NULL,
    `fix`        varchar(100)   DEFAULT NULL,
    `chat_id`    varchar(20)    DEFAULT NULL,
    `user_id`    varchar(10)    DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 40
  DEFAULT CHARSET = latin1;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table wintenbot.tags
CREATE TABLE IF NOT EXISTS `tags`
(
    `id`         int(10) unsigned                        NOT NULL AUTO_INCREMENT,
    `tag`        varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `content`    text COLLATE utf8mb4_unicode_ci         NOT NULL,
    `btn_data`   text COLLATE utf8mb4_unicode_ci,
    `type_data`  varchar(10) COLLATE utf8mb4_unicode_ci  NOT NULL DEFAULT 'text',
    `id_data`    varchar(100) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `id_chat`    varchar(20) COLLATE utf8mb4_unicode_ci  NOT NULL,
    `id_user`    varchar(10) COLLATE utf8mb4_unicode_ci  NOT NULL,
    `created_at` timestamp                               NULL     DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp                               NULL     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1130
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table wintenbot.urllists
CREATE TABLE IF NOT EXISTS `urllists`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `url`        varchar(255)   DEFAULT NULL,
    `class`      varchar(10)    DEFAULT NULL,
    `user_id`    varchar(10)    DEFAULT NULL,
    `chat_id`    varchar(25)    DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 33
  DEFAULT CHARSET = latin1;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table wintenbot.wordlists
CREATE TABLE IF NOT EXISTS `wordlists`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `word`        varchar(70) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
    `class`       varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
    `id_telegram` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
    `id_grup`     varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
    `created_at`  timestamp        NOT NULL                              DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  timestamp        NOT NULL                              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 381
  DEFAULT CHARSET = latin1;

-- Pengeluaran data tidak dipilih.

/*!40101 SET SQL_MODE = IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS = IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
