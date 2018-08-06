


-- 3.1注册流程

-- •	流程图

-- •	场景：注册
-- •	需求说明
-- •	注册产生的字段：ID，账号，密码，余额，手机号，注册时间，注册地址，登录时间，在线状态
-- •	用注册的账号名称和状态，返回给客户端

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

-- create index IDX_uid on c_login_log (uid);
CREATE TABLE `c_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID:users.id',
  `log_type` enum('login','logout') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'login' COMMENT '类别， login:登录账号。logout:退出账号',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='玩家登录日志';

CREATE TABLE `c_money` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID:users.id',
  `money` float(10,3) NOT NULL DEFAULT '0.000' COMMENT '余额',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_id` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='玩家余额表[钱包]';












-- CREATE TABLE `system_account` (
--   `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
--   `role_id` varchar(300) NOT NULL DEFAULT '' COMMENT '角色',
--   `account_name` varchar(100) NOT NULL DEFAULT '' COMMENT '登录账号',
--   `passwd` varchar(100) NOT NULL DEFAULT '' COMMENT '密码',
--   `email` varchar(50) NOT NULL DEFAULT '' COMMENT 'email',
--   `phone` varchar(20) NOT NULL DEFAULT '' COMMENT 'phone',
--   `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '状态[1:启用, 2:禁用]',
--   `note` varchar(200) NOT NULL DEFAULT '' COMMENT '备注信息',
--   `created_at` int(11) NOT NULL DEFAULT '0',
--   `updated_at` int(11) NOT NULL DEFAULT '0',
--   `nickname` varchar(30) NOT NULL DEFAULT '' COMMENT '昵称',
--   `school_id` int(11) NOT NULL DEFAULT '0' COMMENT '所在学校:t_school_organization.id',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;








