



-- CREATE TABLE `users` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
--   `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
--   `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
--   `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
--   `created_at` timestamp NULL DEFAULT NULL,
--   `updated_at` timestamp NULL DEFAULT NULL,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `users_email_unique` (`email`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `sm_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户名称',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户邮箱',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` varchar(300) NOT NULL DEFAULT '' COMMENT '角色: s_role.id',
  `nickname` varchar(30) NOT NULL DEFAULT '' COMMENT '昵称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态[1:启用, 0:禁用]',
  `note` varchar(200) NOT NULL DEFAULT '' COMMENT '备注信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统管理员账号表';

CREATE TABLE `sm_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `role_name` varchar(100) NOT NULL DEFAULT '' COMMENT '角色名称',
  `menu_ids` varchar(1000) NOT NULL DEFAULT '' COMMENT '对api的访问权限，{1,2,3}',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态[1:启用, 0:禁用]',
  `note` varchar(200) NOT NULL DEFAULT '' COMMENT '备注信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统管理员角色表';

CREATE TABLE `sm_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级id',
  `menu_name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称描述: 增加账号',
  `api_name` varchar(100) NOT NULL DEFAULT '' COMMENT '访问 api 链接名称：addAccount',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '图标',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态[1:启用, 0:禁用]',
  `note` varchar(200) NOT NULL DEFAULT '' COMMENT '备注信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_api_name` (`api_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统管理导航表';




CREATE TABLE `c_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户名称',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账户邮箱',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  -- `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '在线状态[1:在线, 0:未在线]',
  `address` varchar(200) NOT NULL DEFAULT '' COMMENT '注册地址',
  `user_type` enum('client', 'simulation') COLLATE utf8_unicode_ci DEFAULT 'simulation' COMMENT '账号类别, client:玩家真实账号。 simulation:玩家模拟账号。',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='玩家账号表';












