BEGIN NOT ATOMIC
CREATE TABLE `area` (
  `id_area` bigint(20) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(50) NOT NULL,
  `area_url` varchar(50) NOT NULL,
  `color_font` varchar(6) NOT NULL,
  `color_background` varchar(6) NOT NULL,
  PRIMARY KEY (`id_area`),
  KEY `area_id_area_IDX` (`id_area`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `center` (
  `id_center` bigint(20) NOT NULL AUTO_INCREMENT,
  `center_code` varchar(6) NOT NULL,
  `hospital` varchar(200) DEFAULT NULL,
  `id_pi` bigint(20) DEFAULT NULL,
  `center_sha_pw` text NOT NULL,
  PRIMARY KEY (`id_center`),
  KEY `center_id_center_IDX` (`id_center`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `country` (
  `country_code` varchar(6) NOT NULL,
  `country` varchar(150) NOT NULL,
  PRIMARY KEY (`country_code`),
  KEY `country_country_code_IDX` (`country_code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `country_other` (
  `id_contry_other` bigint(20) NOT NULL AUTO_INCREMENT,
  `country_other_code` varchar(5) NOT NULL,
  `country_other` varchar(200) NOT NULL,
  `country_other_desc` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_contry_other`),
  KEY `country_other_id_contry_other_IDX` (`id_contry_other`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `diagnosis` (
  `id_dia` bigint(20) NOT NULL AUTO_INCREMENT,
  `dia_name` varchar(150) NOT NULL,
  `dia_short` varchar(15) NOT NULL,
  `group_name` varchar(30) NOT NULL,
  `orderby` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_dia`),
  KEY `NewTable_id_dia_IDX` (`id_dia`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `error_log` (
  `id_error` bigint(20) NOT NULL AUTO_INCREMENT,
  `error_code` varchar(100) DEFAULT NULL,
  `error_file` varchar(300) DEFAULT NULL,
  `error_line` int(11) DEFAULT NULL,
  `error_type` varchar(20) NOT NULL,
  `error_description` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `id_user` bigint(20) DEFAULT NULL,
  `id_area` bigint(20) DEFAULT NULL,
  `ludati` datetime NOT NULL,
  PRIMARY KEY (`id_error`),
  KEY `error_log_id_error_IDX` (`id_error`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `field` (
  `field_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) NOT NULL,
  `field_type` smallint(6) NOT NULL,
  `field_description` varchar(1000) DEFAULT NULL,
  `table_name` varchar(100) NOT NULL,
  `limit_min` float DEFAULT NULL,
  `limit_max` float DEFAULT NULL,
  `is_extra_field` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`field_id`),
  KEY `Field_field_id_IDX` (`field_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `field_form` (
  `form_id` bigint(20) NOT NULL,
  `field_id` bigint(20) NOT NULL,
  `page_number` smallint(6) NOT NULL DEFAULT 0,
  `order_id` int(11) DEFAULT NULL,
  `required` smallint(6) NOT NULL DEFAULT 1,
  PRIMARY KEY (`form_id`,`field_id`),
  KEY `field_form_form_id_IDX` (`form_id`,`field_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `form` (
  `form_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `form_type` varchar(100) NOT NULL,
  `form_class` varchar(50) NOT NULL,
  `form_title` varchar(250) NOT NULL,
  `is_visit_related` smallint(6) NOT NULL DEFAULT 1,
  PRIMARY KEY (`form_id`),
  KEY `form_form_id_IDX` (`form_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `form_status` (
  `form_id` bigint(20) NOT NULL,
  `id_paz` bigint(20) NOT NULL DEFAULT 0,
  `id_visita` bigint(20) NOT NULL DEFAULT 0,
  `is_completed` smallint(6) NOT NULL DEFAULT 0,
  `page` smallint(6) NOT NULL DEFAULT 0,
  `author` bigint(20) NOT NULL,
  `ludati` datetime NOT NULL,
  PRIMARY KEY (`form_id`,`id_paz`,`id_visita`),
  KEY `form_status_form_id_IDX` (`form_id`,`id_paz`,`id_visita`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `form_visit_type` (
  `form_id` bigint(20) NOT NULL,
  `visit_type_id` bigint(20) NOT NULL,
  `is_required` smallint(6) NOT NULL DEFAULT 1,
  `order_id` smallint(6) NOT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `dependencies` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`form_id`,`visit_type_id`),
  KEY `form_visit_type_form_id_IDX` (`form_id`,`visit_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `language` (
  `id_language` bigint(20) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(6) NOT NULL,
  `english` varchar(100) NOT NULL,
  `translated` varchar(100) NOT NULL,
  `languageiso` varchar(6) NOT NULL,
  `is_right` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_language`),
  KEY `language_id_language_IDX` (`id_language`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `language_translation` (
  `id_translation` bigint(20) NOT NULL AUTO_INCREMENT,
  `label_text` varchar(100) NOT NULL,
  `languageiso` varchar(6) NOT NULL,
  `area_text` varchar(50) NOT NULL,
  `translation` text DEFAULT NULL,
  PRIMARY KEY (`id_translation`),
  KEY `language_translation_id_translation_IDX` (`id_translation`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `mail_archive` (
  `id_mail` bigint(20) NOT NULL AUTO_INCREMENT,
  `mail_sender` varchar(150) NOT NULL,
  `mail_subject` varchar(250) NOT NULL,
  `mail_body` text NOT NULL,
  `mail_addresses` text DEFAULT NULL,
  `author` bigint(20) NOT NULL,
  `ludati` datetime NOT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `mail_archive_id_mail_IDX` (`id_mail`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `page_template` (
  `id_template` bigint(20) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(100) NOT NULL,
  `page_url` varchar(50) NOT NULL,
  `id_area` bigint(20) NOT NULL,
  `need_login` smallint(6) NOT NULL,
  PRIMARY KEY (`id_template`),
  KEY `page_template_id_template_IDX` (`id_template`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `patient` (
  `id_paz` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_center` bigint(20) NOT NULL,
  `patient_id` varchar(100) NOT NULL,
  `export_id` varchar(100) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `date_onset` datetime DEFAULT NULL,
  `date_diagnosis` datetime DEFAULT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `date_birth` text NOT NULL,
  `country_birth` text NOT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `author` bigint(20) NOT NULL,
  `ludati` datetime NOT NULL,
  `age_base` decimal(10,0) DEFAULT NULL,
  `id_end_reason` bigint(20) DEFAULT NULL,
  `end_specify` varchar(1000) DEFAULT NULL,
  `end_author` bigint(20) DEFAULT NULL,
  `end_ludati` datetime DEFAULT NULL,
  `country_birth_other` text DEFAULT NULL,
  `ethnicity` bigint(20) DEFAULT NULL,
  `ethnicity_other` varchar(200) DEFAULT NULL,
  `date_first_visit` datetime DEFAULT NULL,
  `id_diagnosis` bigint(20) DEFAULT NULL,
  `dia_is_provisional` smallint(6) NOT NULL DEFAULT 0,
  `end_note` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_paz`),
  KEY `patient_id_paz_IDX` (`id_paz`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `patient_criteria` (
  `id_paz` bigint(20) NOT NULL,
  `criteria_type` bigint(20) NOT NULL,
  `author` bigint(20) NOT NULL,
  `ludati` datetime NOT NULL,
  `extra_fields` text DEFAULT NULL,
  PRIMARY KEY (`id_paz`,`criteria_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `patient_deleted` (
  `id_paz` bigint(20) NOT NULL,
  `note` text DEFAULT NULL,
  `patient_json` text NOT NULL,
  `author` bigint(20) NOT NULL,
  `ludati` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `session` (
  `id_area` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `ip_address` varchar(100) NOT NULL,
  `date_login` datetime DEFAULT NULL,
  `date_logout` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`id_area`,`id_user`,`ip_address`),
  KEY `session_id_area_IDX` (`id_area`,`id_user`,`ip_address`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `session_archive` (
  `id_session` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_area` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `ip_address` varchar(100) NOT NULL,
  `date_login` datetime DEFAULT NULL,
  `date_logout` datetime DEFAULT NULL,
  PRIMARY KEY (`id_session`),
  KEY `session_id_session_IDX` (`id_session`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `session_fail` (
  `id_fail` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `id_area` bigint(20) NOT NULL,
  `is_unlock` smallint(6) NOT NULL DEFAULT 0,
  `password_sha` varchar(200) NOT NULL,
  `ludati` datetime NOT NULL,
  PRIMARY KEY (`id_fail`),
  KEY `session_fail_id_fail_IDX` (`id_fail`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `id_user` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `role` varchar(100) NOT NULL,
  `id_center` bigint(20) NOT NULL DEFAULT 0,
  `enabled` smallint(6) NOT NULL DEFAULT 1,
  `password` varchar(200) DEFAULT NULL,
  `pswdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  KEY `contact_id_med_IDX` (`id_user`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `visit` (
  `id_visita` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_paz` bigint(20) NOT NULL,
  `is_lock` smallint(6) NOT NULL DEFAULT 0,
  `date_visit` datetime NOT NULL,
  `lock_author` bigint(20) DEFAULT NULL,
  `lock_ludati` datetime DEFAULT NULL,
  `unlock_ludati` datetime DEFAULT NULL,
  `unlock_author` bigint(20) DEFAULT NULL,
  `unlock_reason` text DEFAULT NULL,
  `unlock_note` text DEFAULT NULL,
  `is_check` smallint(6) NOT NULL DEFAULT 0,
  `check_author` bigint(20) DEFAULT NULL,
  `check_ludati` datetime DEFAULT NULL,
  `check_note` text DEFAULT NULL,
  `author` bigint(20) DEFAULT NULL,
  `ludati` datetime DEFAULT NULL,
  `visit_type_id` bigint(20) NOT NULL,
  `lock_reason` text DEFAULT NULL,
  PRIMARY KEY (`id_visita`),
  KEY `visit_id_visita_IDX` (`id_visita`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `visit_deleted` (
  `id_visita` bigint(20) NOT NULL,
  `note` text DEFAULT NULL,
  `visit_json` text NOT NULL,
  `author` bigint(20) NOT NULL,
  `ludati` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `visit_output` (
  `id_output` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_visita` bigint(20) NOT NULL,
  `result` text DEFAULT NULL,
  `recipients` text DEFAULT NULL,
  `extra_fields` text DEFAULT NULL,
  `author` bigint(20) NOT NULL,
  `ludati` datetime NOT NULL,
  PRIMARY KEY (`id_output`),
  KEY `visit_output_id_output_IDX` (`id_output`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `visit_type` (
  `visit_type_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `visit_type` varchar(100) NOT NULL,
  `visit_day` int(11) NOT NULL,
  `always_show` smallint(6) NOT NULL DEFAULT 0,
  `is_extra` smallint(6) NOT NULL DEFAULT 0,
  `visit_day_lower` int(11) NOT NULL DEFAULT 0,
  `visit_day_upper` int(11) NOT NULL DEFAULT 0,
  `visit_type_code` varchar(100) NOT NULL,
  `has_output` smallint(6) NOT NULL DEFAULT 0,
  `has_randomization` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`visit_type_id`),
  KEY `visit_type_visit_type_id_IDX` (`visit_type_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `visit_randomization` (
  `id_visita` bigint(20) DEFAULT NULL,
  `id_random` bigint(20) NOT NULL AUTO_INCREMENT,
  `arm` smallint(6) NOT NULL,
  `author` bigint(20) DEFAULT NULL,
  `ludati` datetime DEFAULT NULL,
  `extra_fields` text DEFAULT NULL,
  `id_paz` bigint(20) NOT NULL,
  PRIMARY KEY (`id_random`),
  KEY `visit_randomization_id_random_IDX` (`id_random`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

END;
