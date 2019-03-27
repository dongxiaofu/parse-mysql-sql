/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50725
 Source Host           : localhost:3306
 Source Schema         : myblog

 Target Server Type    : MySQL
 Target Server Version : 50725
 File Encoding         : 65001

 Date: 27/03/2019 22:28:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cg_admin
-- ----------------------------
DROP TABLE IF EXISTS `cg_admin`;
CREATE TABLE `cg_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `pwd` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cg_category
-- ----------------------------
DROP TABLE IF EXISTS `cg_category`;
CREATE TABLE `cg_category` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '上级栏目ID',
  `title` varchar(200) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序，序号越大，越靠前',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cg_comment
-- ----------------------------
DROP TABLE IF EXISTS `cg_comment`;
CREATE TABLE `cg_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '作者是管理员，存储管理员ID；若是游客评论，该值为0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论的上级ID，若是对文章的评论，该值为0；否则，为被评论的评论的ID',
  `passage_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被评论的文章',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `ip` varchar(255) NOT NULL DEFAULT '' COMMENT '作者ip',
  `content` text NOT NULL COMMENT '评论内容',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论发表时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='评论表';

-- ----------------------------
-- Table structure for cg_content
-- ----------------------------
DROP TABLE IF EXISTS `cg_content`;
CREATE TABLE `cg_content` (
  `coid` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL DEFAULT '1' COMMENT '文章ID',
  `content` text COMMENT '文章内容',
  PRIMARY KEY (`coid`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cg_friend_link
-- ----------------------------
DROP TABLE IF EXISTS `cg_friend_link`;
CREATE TABLE `cg_friend_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(250) NOT NULL DEFAULT '' COMMENT 'url',
  `siteName` varchar(250) NOT NULL DEFAULT '' COMMENT '网站名称',
  `isShow` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示：0--不显示，1--显示',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序，倒序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='友情链接';

-- ----------------------------
-- Table structure for cg_passage
-- ----------------------------
DROP TABLE IF EXISTS `cg_passage`;
CREATE TABLE `cg_passage` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT '1' COMMENT '文章所属栏目ID',
  `title` varchar(200) NOT NULL DEFAULT '',
  `description` text NOT NULL COMMENT '文章摘要',
  `author` varchar(200) NOT NULL DEFAULT 'cg' COMMENT '默认作者',
  `source` varchar(200) NOT NULL DEFAULT '原创' COMMENT '文章来源',
  `create_time` varchar(14) NOT NULL DEFAULT '0' COMMENT '发布时间',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序，序号越大，越靠前',
  `is_suggest` enum('1','0') NOT NULL DEFAULT '0' COMMENT '是否推荐，0--不推荐，1--推荐',
  `view` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '阅读数',
  PRIMARY KEY (`aid`),
  KEY `cid` (`cid`),
  CONSTRAINT `cg_passage_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `cg_category` (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
