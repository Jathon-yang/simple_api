/*
Navicat MySQL Data Transfer

Source Server         : 本地虚拟主机
Source Server Version : 50621
Source Host           : 192.168.1.201:3306
Source Database       : test_api

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2015-02-15 16:13:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for api_app
-- ----------------------------
DROP TABLE IF EXISTS `api_app`;
CREATE TABLE `api_app` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `appid` int(11) NOT NULL COMMENT '应用ID',
  `appkey` char(32) NOT NULL COMMENT '应用密钥',
  `name` varchar(20) NOT NULL COMMENT '应用名称',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='应用列表';

-- ----------------------------
-- Records of api_app
-- ----------------------------
INSERT INTO `api_app` VALUES ('1', '1', '123456', '官方应用');

-- ----------------------------
-- Table structure for api_oauth
-- ----------------------------
DROP TABLE IF EXISTS `api_oauth`;
CREATE TABLE `api_oauth` (
  `uid` int(11) NOT NULL,
  `appid` int(10) unsigned NOT NULL COMMENT '应用ID',
  `access_token` char(32) NOT NULL COMMENT '授权值',
  `refresh_token` char(32) NOT NULL COMMENT '用于刷新access_token',
  `a_time` int(10) unsigned DEFAULT NULL COMMENT 'access_token生成时间',
  `r_time` int(10) unsigned DEFAULT NULL COMMENT 'refresh_token生成时间',
  PRIMARY KEY (`uid`),
  KEY `access_token` (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户授权表';

-- ----------------------------
-- Records of api_oauth
-- ----------------------------
