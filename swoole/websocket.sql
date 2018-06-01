/*
 Navicat Premium Data Transfer

 Source Server         : 3310
 Source Server Type    : MySQL
 Source Server Version : 50721
 Source Host           : localhost:3306
 Source Schema         : live

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 17/04/2018 17:00:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for msg
-- ----------------------------
DROP TABLE IF EXISTS `msg`;
CREATE TABLE `msg`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息记录表',
  `appid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用ID',
  `from` int(11) NULL DEFAULT NULL COMMENT '应用对应发送者用户ID',
  `to` int(11) NULL DEFAULT NULL COMMENT '应用对应接收者用户ID',
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '完整数据',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '具体内容',
  `date` datetime(0) NULL DEFAULT NULL COMMENT '日期',
  `res` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '结果',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'appid应用标识',
  `userid` int(11) NULL DEFAULT NULL COMMENT '应用用户',
  `sid` int(11) NULL DEFAULT NULL COMMENT '当前连接的websocket_id',
  `token` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'token',
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '用户附加属性',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
