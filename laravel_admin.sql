/*
Navicat MySQL Data Transfer

Source Server         : phpstydy
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : laravel_admin

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-06-07 20:12:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_app
-- ----------------------------
DROP TABLE IF EXISTS `admin_app`;
CREATE TABLE `admin_app` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '行为ID',
  `name` varchar(50) NOT NULL COMMENT '行为名称',
  `describe` varchar(255) NOT NULL COMMENT '行为的描述',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '设置禁用状态 1为开启，2为禁用',
  `img_src` varchar(255) NOT NULL COMMENT '行为的图标',
  `type` tinyint(4) NOT NULL COMMENT '触发行为的事件1.click 。。。',
  `exists_power_stone` tinyint(4) NOT NULL COMMENT '判断是否是元力或者是元石 1为元力   2为元石',
  `power_stone` float(10,5) NOT NULL COMMENT '设置行为奖励多少元力或者元石',
  `check_code` varchar(255) DEFAULT NULL COMMENT '检测行为状态码',
  `url` varchar(255) NOT NULL COMMENT '跳转的地址',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_app
-- ----------------------------
INSERT INTO `admin_app` VALUES ('1', '签到', '签到', '1', '111.jpg', '1', '2', '50.00000', '<script src=\"jquery.js\">', 'localhost', '2018-06-06 10:08:56', '2018-06-06 11:33:51');
INSERT INTO `admin_app` VALUES ('2', '分享', '分享', '1', '2222.jpg', '1', '2', '10.00000', null, '', null, null);
INSERT INTO `admin_app` VALUES ('3', '签到', '签到', '1', '333.jgp', '1', '1', '2.00000', null, '', null, null);

-- ----------------------------
-- Table structure for admin_main_property
-- ----------------------------
DROP TABLE IF EXISTS `admin_main_property`;
CREATE TABLE `admin_main_property` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `total_stone` float(11,5) NOT NULL DEFAULT '0.00000' COMMENT '元石',
  `total_power` int(11) NOT NULL COMMENT '元力',
  `Reserved_one` varchar(30) DEFAULT NULL COMMENT '预留字段1',
  `Reserved_to` varchar(30) DEFAULT NULL COMMENT '预留字段2',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  KEY `total_stone` (`total_stone`,`total_power`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户的资产主表';

-- ----------------------------
-- Records of admin_main_property
-- ----------------------------

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `uri` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
INSERT INTO `admin_menu` VALUES ('1', '0', '1', 'Index', 'fa-bar-chart', '/', null, null);
INSERT INTO `admin_menu` VALUES ('2', '0', '2', 'Admin', 'fa-tasks', '', null, null);
INSERT INTO `admin_menu` VALUES ('3', '2', '3', 'Users', 'fa-users', 'auth/users', null, null);
INSERT INTO `admin_menu` VALUES ('4', '2', '4', 'Roles', 'fa-user', 'auth/roles', null, null);
INSERT INTO `admin_menu` VALUES ('5', '2', '5', 'Permission', 'fa-ban', 'auth/permissions', null, null);
INSERT INTO `admin_menu` VALUES ('6', '2', '6', 'Menu', 'fa-bars', 'auth/menu', null, null);
INSERT INTO `admin_menu` VALUES ('7', '2', '7', 'Operation log', 'fa-history', 'auth/logs', null, null);

-- ----------------------------
-- Table structure for admin_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_operation_log`;
CREATE TABLE `admin_operation_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `input` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_operation_log_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_operation_log
-- ----------------------------
INSERT INTO `admin_operation_log` VALUES ('1', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-25 07:19:09', '2018-05-25 07:19:09');
INSERT INTO `admin_operation_log` VALUES ('2', '1', 'admin', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:19:22', '2018-05-25 07:19:22');
INSERT INTO `admin_operation_log` VALUES ('3', '1', 'admin/auth/users', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:19:41', '2018-05-25 07:19:41');
INSERT INTO `admin_operation_log` VALUES ('4', '1', 'admin', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:19:46', '2018-05-25 07:19:46');
INSERT INTO `admin_operation_log` VALUES ('5', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-25 07:37:07', '2018-05-25 07:37:07');
INSERT INTO `admin_operation_log` VALUES ('6', '1', 'admin', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:37:11', '2018-05-25 07:37:11');
INSERT INTO `admin_operation_log` VALUES ('7', '1', 'admin/auth/users', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:38:01', '2018-05-25 07:38:01');
INSERT INTO `admin_operation_log` VALUES ('8', '1', 'admin/auth/roles', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:38:05', '2018-05-25 07:38:05');
INSERT INTO `admin_operation_log` VALUES ('9', '1', 'admin/auth/permissions', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:38:08', '2018-05-25 07:38:08');
INSERT INTO `admin_operation_log` VALUES ('10', '1', 'admin/auth/permissions', 'GET', '127.0.0.1', '[]', '2018-05-25 07:38:53', '2018-05-25 07:38:53');
INSERT INTO `admin_operation_log` VALUES ('11', '1', 'admin/auth/permissions', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:38:59', '2018-05-25 07:38:59');
INSERT INTO `admin_operation_log` VALUES ('12', '1', 'admin/auth/permissions', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:39:01', '2018-05-25 07:39:01');
INSERT INTO `admin_operation_log` VALUES ('13', '1', 'admin/auth/menu', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:39:03', '2018-05-25 07:39:03');
INSERT INTO `admin_operation_log` VALUES ('14', '1', 'admin', 'GET', '127.0.0.1', '{\"_pjax\":\"#pjax-container\"}', '2018-05-25 07:42:28', '2018-05-25 07:42:28');
INSERT INTO `admin_operation_log` VALUES ('15', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-25 07:55:43', '2018-05-25 07:55:43');
INSERT INTO `admin_operation_log` VALUES ('16', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-25 09:57:18', '2018-05-25 09:57:18');
INSERT INTO `admin_operation_log` VALUES ('17', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-25 13:18:35', '2018-05-25 13:18:35');
INSERT INTO `admin_operation_log` VALUES ('18', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-28 17:29:37', '2018-05-28 17:29:37');
INSERT INTO `admin_operation_log` VALUES ('19', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-28 17:29:47', '2018-05-28 17:29:47');
INSERT INTO `admin_operation_log` VALUES ('20', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-28 17:29:49', '2018-05-28 17:29:49');
INSERT INTO `admin_operation_log` VALUES ('21', '1', 'admin', 'GET', '127.0.0.1', '[]', '2018-05-28 17:29:50', '2018-05-28 17:29:50');

-- ----------------------------
-- Table structure for admin_operator
-- ----------------------------
DROP TABLE IF EXISTS `admin_operator`;
CREATE TABLE `admin_operator` (
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `identification` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_operator
-- ----------------------------
INSERT INTO `admin_operator` VALUES ('telecom', 'GAME', 'utf8');

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `http_method` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
INSERT INTO `admin_permissions` VALUES ('1', 'All permission', '*', '', '*', null, null);
INSERT INTO `admin_permissions` VALUES ('2', 'Dashboard', 'dashboard', 'GET', '/', null, null);
INSERT INTO `admin_permissions` VALUES ('3', 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', null, null);
INSERT INTO `admin_permissions` VALUES ('4', 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', null, null);
INSERT INTO `admin_permissions` VALUES ('5', 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', null, null);

-- ----------------------------
-- Table structure for admin_power
-- ----------------------------
DROP TABLE IF EXISTS `admin_power`;
CREATE TABLE `admin_power` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `appid` int(11) NOT NULL COMMENT 'api接口的行为字段',
  `resid` int(11) NOT NULL COMMENT '规则id',
  `actid` int(11) NOT NULL COMMENT '行为id',
  `power` int(11) NOT NULL DEFAULT '0' COMMENT '元力',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `power_balance` int(11) NOT NULL COMMENT '统计元力的总数',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`appid`,`resid`,`actid`,`power`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_power
-- ----------------------------

-- ----------------------------
-- Table structure for admin_real_check
-- ----------------------------
DROP TABLE IF EXISTS `admin_real_check`;
CREATE TABLE `admin_real_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `real_name` varchar(10) NOT NULL COMMENT '真实姓名',
  `IDCard` varchar(30) NOT NULL COMMENT '身份证',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='实名验证表';

-- ----------------------------
-- Records of admin_real_check
-- ----------------------------

-- ----------------------------
-- Table structure for admin_reception_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_reception_users`;
CREATE TABLE `admin_reception_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(190) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '邮箱',
  `code` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` char(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '手机',
  `invitation_once` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '邀请的次数',
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `capacity` tinyint(4) NOT NULL DEFAULT '2' COMMENT '识别后台用户1和前台用户2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `Inviter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=1000001 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_reception_users
-- ----------------------------

-- ----------------------------
-- Table structure for admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_roles
-- ----------------------------
INSERT INTO `admin_roles` VALUES ('1', 'Administrator', 'administrator', '2018-05-25 07:18:41', '2018-05-25 07:18:41');

-- ----------------------------
-- Table structure for admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_role_menu
-- ----------------------------
INSERT INTO `admin_role_menu` VALUES ('1', '2', null, null);

-- ----------------------------
-- Table structure for admin_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_role_permissions
-- ----------------------------
INSERT INTO `admin_role_permissions` VALUES ('1', '1', null, null);

-- ----------------------------
-- Table structure for admin_role_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_users`;
CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_role_users
-- ----------------------------
INSERT INTO `admin_role_users` VALUES ('1', '1', null, null);

-- ----------------------------
-- Table structure for admin_stone
-- ----------------------------
DROP TABLE IF EXISTS `admin_stone`;
CREATE TABLE `admin_stone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `appid` int(11) NOT NULL,
  `resid` int(11) NOT NULL COMMENT '规则id',
  `actid` int(11) NOT NULL COMMENT '行为id',
  `stone` float(11,5) NOT NULL DEFAULT '0.00000' COMMENT '元石',
  `created_at` timestamp NULL DEFAULT NULL,
  `stone_balance` float(11,5) NOT NULL DEFAULT '0.00000',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`appid`,`resid`,`actid`,`stone`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_stone
-- ----------------------------
-- ----------------------------
-- Table structure for admin_stone_total
-- ----------------------------
DROP TABLE IF EXISTS `admin_stone_total`;
CREATE TABLE `admin_stone_total` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `total_stone` decimal(16,5) NOT NULL COMMENT '元石总产值',
  `day_stone` varchar(10) NOT NULL COMMENT '昨日产生元石',
  `remain_stone` decimal(16,5) NOT NULL COMMENT '剩余的元石量',
  `Reserved` decimal(11,5) DEFAULT NULL COMMENT '待领取元石',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='元石总量表';

-- ----------------------------
-- Records of admin_stone_total
-- ----------------------------
INSERT INTO `admin_stone_total` VALUES ('1', '5000000000.00000', '10', '4999000000.00000', null, '2018-06-05 21:38:10', '2018-06-05 21:38:10');

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(190) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
INSERT INTO `admin_users` VALUES ('1', 'admin', '$2y$10$Wipv8dflF8l22f6H9bTFCOHx4wNCG4Zx9CiPlIqP4hkqm5FfDidYy', 'Administrator', null, null, '2018-05-25 07:18:41', '2018-05-25 07:18:41');

-- ----------------------------
-- Table structure for admin_user_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_permissions`;
CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_user_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for admin_visit
-- ----------------------------
DROP TABLE IF EXISTS `admin_visit`;
CREATE TABLE `admin_visit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `u_id` int(11) NOT NULL COMMENT '用户id',
  `ip` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_id` (`u_id`),
  UNIQUE KEY `u_id_2` (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin_visit
-- ----------------------------

DROP TABLE IF EXISTS `admin_qkl_stoneenc`;
CREATE TABLE `admin_qkl_stoneenc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(10) unsigned NOT NULL  COMMENT '用户id',
  `stoneenc`  varchar(128) NOT NULL COMMENT '加密字符串',
   `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  key (stoneenc)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='加密元石去重';