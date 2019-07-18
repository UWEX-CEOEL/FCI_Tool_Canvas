-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 18, 2019 at 08:19 AM
-- Server version: 5.7.26-log
-- PHP Version: 7.1.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tsugi_canvas`
--

-- --------------------------------------------------------

--
-- Table structure for table `attend`
--

DROP TABLE IF EXISTS `attend`;
CREATE TABLE `attend` (
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attend` date NOT NULL,
  `ipaddr` varchar(64) DEFAULT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blob_file`
--

DROP TABLE IF EXISTS `blob_file`;
CREATE TABLE `blob_file` (
  `file_id` int(11) NOT NULL,
  `file_sha256` char(64) NOT NULL,
  `context_id` int(11) DEFAULT NULL,
  `file_name` varchar(2048) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `contenttype` varchar(256) DEFAULT NULL,
  `path` varchar(2048) DEFAULT NULL,
  `content` longblob,
  `json` text,
  `created_at` datetime NOT NULL,
  `accessed_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci2sam_export_vw`
--

DROP TABLE IF EXISTS `fci2sam_export_vw`;
CREATE TABLE `fci2sam_export_vw` (
  `sis_enrollment_id` varchar(100) DEFAULT NULL,
  `M1` datetime DEFAULT NULL,
  `M2` datetime DEFAULT NULL,
  `M3` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_active_sp_report`
--

DROP TABLE IF EXISTS `fci_active_sp_report`;
CREATE TABLE `fci_active_sp_report` (
  `LMS_CAMPUS` varchar(3) DEFAULT NULL,
  `STUDENT_NAME` text,
  `STUDENT_ENROLLED_TERM` varchar(45) DEFAULT NULL,
  `QUESTION` varchar(2) DEFAULT NULL,
  `COURSE_TITLE` text,
  `INSTRUCTOR_NAME` mediumtext,
  `STUDENT_RESPONSE` json DEFAULT NULL,
  `STUDENT_SUBMISSION_TIME` datetime DEFAULT NULL,
  `INSTRUCTOR_FEEDBACK` json DEFAULT NULL,
  `INSTRUCTOR_RESPONSE_TIME` datetime DEFAULT NULL,
  `TIME2RESPONSE_DAYS` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_active_sp_report_all`
--

DROP TABLE IF EXISTS `fci_active_sp_report_all`;
CREATE TABLE `fci_active_sp_report_all` (
  `LMS_CAMPUS` varchar(3) DEFAULT NULL,
  `STUDENT_NAME` text,
  `STUDENT_ENROLLED_TERM` varchar(45) DEFAULT NULL,
  `QUESTION` varchar(2) DEFAULT NULL,
  `COURSE_TITLE` text,
  `INSTRUCTOR_NAME` mediumtext,
  `STUDENT_RESPONSE` json DEFAULT NULL,
  `STUDENT_SUBMISSION_TIME` datetime DEFAULT NULL,
  `INSTRUCTOR_FEEDBACK` json DEFAULT NULL,
  `INSTRUCTOR_RESPONSE_TIME` datetime DEFAULT NULL,
  `ENROLLED_DT` date DEFAULT NULL,
  `PROGRAM_ID` varchar(45) DEFAULT NULL,
  `COURSE_NAME` varchar(45) DEFAULT NULL,
  `TIME2RESPONSE_DAYS` varchar(25) DEFAULT NULL,
  `WITHDRAWN` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_active_sp_report_no_withdraw`
--

DROP TABLE IF EXISTS `fci_active_sp_report_no_withdraw`;
CREATE TABLE `fci_active_sp_report_no_withdraw` (
  `LMS_CAMPUS` varchar(3) DEFAULT NULL,
  `STUDENT_NAME` text,
  `STUDENT_ENROLLED_TERM` varchar(45) DEFAULT NULL,
  `QUESTION` varchar(2) DEFAULT NULL,
  `COURSE_TITLE` text,
  `INSTRUCTOR_NAME` mediumtext,
  `STUDENT_RESPONSE` json DEFAULT NULL,
  `INSTRUCTOR_FEEDBACK` json DEFAULT NULL,
  `STUDENT_SUBMISSION_TIME` datetime DEFAULT NULL,
  `INSTRUCTOR_RESPONSE_TIME` datetime DEFAULT NULL,
  `TIME2RESPONSE_DAYS` varchar(25) DEFAULT NULL,
  `ENROLLED_DATE` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_active_sp_report_v2`
--

DROP TABLE IF EXISTS `fci_active_sp_report_v2`;
CREATE TABLE `fci_active_sp_report_v2` (
  `LMS_CAMPUS` varchar(3) DEFAULT NULL,
  `STUDENT_NAME` text,
  `STUDENT_ENROLLED_TERM` varchar(45) DEFAULT NULL,
  `QUESTION` varchar(2) DEFAULT NULL,
  `COURSE_TITLE` text,
  `INSTRUCTOR_NAME` mediumtext,
  `STUDENT_RESPONSE` json DEFAULT NULL,
  `INSTRUCTOR_FEEDBACK` json DEFAULT NULL,
  `STUDENT_SUBMISSION_TIME` datetime DEFAULT NULL,
  `INSTRUCTOR_RESPONSE_TIME` datetime DEFAULT NULL,
  `TIME2RESPONSE_DAYS` varchar(25) DEFAULT NULL,
  `ENROLLED_DATE` date DEFAULT NULL,
  `WITHDRAW_DT` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_analytics_vw`
--

DROP TABLE IF EXISTS `fci_analytics_vw`;
CREATE TABLE `fci_analytics_vw` (
  `LMS_CAMPUS` varchar(3) DEFAULT NULL,
  `STUDENT_NAME` text,
  `STUDENT_ENROLLED_TERM` varchar(45) DEFAULT NULL,
  `QUESTION` varchar(2) DEFAULT NULL,
  `COURSE_TITLE` text,
  `INSTRUCTOR_NAME` mediumtext,
  `STUDENT_SUBMISSION_TIME` datetime DEFAULT NULL,
  `INSTRUCTOR_RESPONSE_TIME` datetime DEFAULT NULL,
  `TIME2RESPONSE_DAYS` varchar(25) DEFAULT NULL,
  `ENROLLED_DT` date DEFAULT NULL,
  `PROGRAM_ID` varchar(45) DEFAULT NULL,
  `COURSE_NAME` varchar(45) DEFAULT NULL,
  `GRADE` varchar(45) DEFAULT NULL,
  `WITHDRAWN` int(1) DEFAULT NULL,
  `EXT_STUDENT_ID1` varchar(45) DEFAULT NULL,
  `EXT_STUDENT_ID3` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_course_xwalk`
--

DROP TABLE IF EXISTS `fci_course_xwalk`;
CREATE TABLE `fci_course_xwalk` (
  `COURSE_LONG_NAME` varchar(255) DEFAULT '',
  `EXTERNAL_ID` varchar(100) NOT NULL DEFAULT '',
  `SUBJECT` varchar(100) NOT NULL DEFAULT '',
  `COURSE_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_enrollment_debug_vw`
--

DROP TABLE IF EXISTS `fci_enrollment_debug_vw`;
CREATE TABLE `fci_enrollment_debug_vw` (
  `user_id` int(11) DEFAULT NULL,
  `displayname` text,
  `lms_defined_id` varchar(100) DEFAULT NULL,
  `sis_course_code` varchar(45) DEFAULT NULL,
  `ext_course` varchar(146) DEFAULT NULL,
  `title` text,
  `lms_course_code` varchar(100) DEFAULT NULL,
  `fci_type` varchar(2) DEFAULT NULL,
  `json` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_fin_aid_report_vw`
--

DROP TABLE IF EXISTS `fci_fin_aid_report_vw`;
CREATE TABLE `fci_fin_aid_report_vw` (
  `USERNAME` varchar(100) DEFAULT NULL,
  `ORG_DEFINED_ID` varchar(100) DEFAULT NULL,
  `ROLE_NAME` varchar(100) DEFAULT NULL,
  `ORG_UNIT_CODE` varchar(100) DEFAULT NULL,
  `ORG_UNIT_ID` text,
  `DATE_SUBMITTED` varchar(19) DEFAULT NULL,
  `DATE_POSTED` binary(0) DEFAULT NULL,
  `TIME_STARTED` binary(0) DEFAULT NULL,
  `TIME_COMPLETED` binary(0) DEFAULT NULL,
  `INSTITUTION` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_link_history`
--

DROP TABLE IF EXISTS `fci_link_history`;
CREATE TABLE `fci_link_history` (
  `link_id` int(11) NOT NULL,
  `link_sha256` char(64) DEFAULT NULL,
  `link_key` text,
  `json` mediumtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `saved_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_month`
--

DROP TABLE IF EXISTS `fci_month`;
CREATE TABLE `fci_month` (
  `month_id` varchar(45) NOT NULL,
  `month_start_dt` date DEFAULT NULL,
  `month_end_dt` date DEFAULT NULL,
  `sp_m1_start_dt` date DEFAULT NULL,
  `day10_dt` date DEFAULT NULL,
  `day17_dt` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_result_history`
--

DROP TABLE IF EXISTS `fci_result_history`;
CREATE TABLE `fci_result_history` (
  `result_id` int(11) NOT NULL,
  `link_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sourcedid` text,
  `json` mediumtext,
  `updated_at` datetime DEFAULT NULL,
  `user_updated` datetime DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `instructor_updated` datetime DEFAULT NULL,
  `saved_timestamp` datetime NOT NULL,
  `reset_flag` tinyint(1) DEFAULT '0',
  `student_enrollments` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_rp_student_helper_vw`
--

DROP TABLE IF EXISTS `fci_rp_student_helper_vw`;
CREATE TABLE `fci_rp_student_helper_vw` (
  `result_id` int(11) DEFAULT NULL,
  `link_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `result_url` text,
  `sourcedid` text,
  `service_id` int(11) DEFAULT NULL,
  `ipaddr` varchar(64) DEFAULT NULL,
  `grade` float DEFAULT NULL,
  `note` mediumtext,
  `server_grade` float DEFAULT NULL,
  `json` mediumtext,
  `entity_version` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `retrieved_at` datetime DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `user_updated` datetime DEFAULT NULL,
  `instructor_updated` datetime DEFAULT NULL,
  `fci_type` varchar(2) DEFAULT NULL,
  `sis_enrollment_id` varchar(100) DEFAULT NULL,
  `fci_state` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_sis_bio_demo`
--

DROP TABLE IF EXISTS `fci_sis_bio_demo`;
CREATE TABLE `fci_sis_bio_demo` (
  `EXT_STUDENT_ID1` varchar(45) NOT NULL,
  `EXT_STUDENT_ID2` varchar(45) DEFAULT NULL,
  `EXT_STUDENT_ID3` varchar(45) DEFAULT NULL,
  `EXT_STUDENT_ID4` varchar(45) DEFAULT NULL,
  `FIRST_NAME` varchar(45) DEFAULT NULL,
  `LAST_NAME` varchar(45) DEFAULT NULL,
  `EMAIL_ADDR` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_sis_enrollments`
--

DROP TABLE IF EXISTS `fci_sis_enrollments`;
CREATE TABLE `fci_sis_enrollments` (
  `EXT_STUDENT_ID1` varchar(45) NOT NULL,
  `EXT_STUDENT_ID2` varchar(45) DEFAULT NULL,
  `EXT_STUDENT_ID3` varchar(45) DEFAULT NULL,
  `EXT_STUDENT_ID4` varchar(45) DEFAULT NULL,
  `PROG_START_DT` date DEFAULT NULL,
  `EXT_CAMPUS_ID` varchar(45) DEFAULT NULL,
  `EXT_SITE_ID` varchar(45) DEFAULT NULL,
  `PROGRAM_ID` varchar(45) DEFAULT NULL,
  `COURSE_ID` varchar(45) NOT NULL,
  `CLASS_SECTION` varchar(45) DEFAULT NULL,
  `COURSE_NAME` varchar(45) DEFAULT NULL,
  `EXT_COURSE_ID` varchar(100) NOT NULL,
  `COURSE_START_DT` date DEFAULT NULL,
  `COURSE_END_DT` date DEFAULT NULL,
  `REPEAT` varchar(45) DEFAULT NULL,
  `ENROLLED_DT` date DEFAULT NULL,
  `WITHDRAW_DT` date DEFAULT NULL,
  `ATTENDANCE` varchar(45) DEFAULT NULL,
  `COMPLETED` varchar(45) DEFAULT NULL,
  `LETTER_GRADE` varchar(45) DEFAULT NULL,
  `PROGRAM_APPLICABLE` varchar(45) DEFAULT NULL,
  `SAP_REVIEW_END_DT` date DEFAULT NULL,
  `TRANSFER` varchar(45) DEFAULT NULL,
  `REMEDIAL` varchar(45) DEFAULT NULL,
  `EXTRACT_DATE` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_state_byterm_audit`
--

DROP TABLE IF EXISTS `fci_state_byterm_audit`;
CREATE TABLE `fci_state_byterm_audit` (
  `TERM` varchar(4) DEFAULT NULL,
  `FCI_STATE` varchar(2) DEFAULT NULL,
  `count(*)` bigint(21) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fci_term`
--

DROP TABLE IF EXISTS `fci_term`;
CREATE TABLE `fci_term` (
  `term_id` varchar(45) NOT NULL,
  `term_name` varchar(45) DEFAULT NULL,
  `term_start_dt` date DEFAULT NULL,
  `term_end_dt` date DEFAULT NULL,
  `term_month_1` varchar(45) DEFAULT NULL,
  `term_month_2` varchar(45) DEFAULT NULL,
  `term_month_3` varchar(45) DEFAULT NULL,
  `sp_fci_m1_due_dt` date DEFAULT NULL,
  `sp_fci_m2_due_dt` date DEFAULT NULL,
  `sp_fci_m3_due_dt` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `key_request`
--

DROP TABLE IF EXISTS `key_request`;
CREATE TABLE `key_request` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(512) NOT NULL,
  `notes` text,
  `admin` text,
  `state` smallint(6) DEFAULT NULL,
  `lti` tinyint(4) DEFAULT NULL,
  `json` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lms_plugins`
--

DROP TABLE IF EXISTS `lms_plugins`;
CREATE TABLE `lms_plugins` (
  `plugin_id` int(11) NOT NULL,
  `plugin_path` varchar(255) NOT NULL,
  `version` bigint(20) NOT NULL,
  `title` varchar(2048) DEFAULT NULL,
  `json` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lms_role_permissions`
--

DROP TABLE IF EXISTS `lms_role_permissions`;
CREATE TABLE `lms_role_permissions` (
  `lms_role_number` int(4) NOT NULL,
  `lms_role_name` varchar(100) NOT NULL,
  `give_feedback` tinyint(1) NOT NULL,
  `modify_question` tinyint(1) NOT NULL,
  `readonly_view` tinyint(1) NOT NULL,
  `individual_view` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lti_context`
--

DROP TABLE IF EXISTS `lti_context`;
CREATE TABLE `lti_context` (
  `context_id` int(11) NOT NULL,
  `context_sha256` char(64) NOT NULL,
  `context_key` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `key_id` int(11) NOT NULL,
  `path` text,
  `title` text,
  `lessons` mediumtext,
  `json` mediumtext,
  `settings` mediumtext,
  `settings_url` text,
  `ext_memberships_id` text,
  `ext_memberships_url` text,
  `memberships_url` text,
  `lineitems_url` text,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `lms_course_code` varchar(100) DEFAULT NULL,
  `sis_course_code` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-02 06:00:00',
  `canvas_course_code` varchar(100) DEFAULT NULL,
  `canvas_section_code` varchar(100) DEFAULT NULL,
  `canvas_sis_course` varchar(100) DEFAULT NULL,
  `canvas_sis_section` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `lti_context`
--
DROP TRIGGER IF EXISTS `LTI_CONTEXT_BEFORE_UPDATE`;
DELIMITER $$
CREATE TRIGGER `LTI_CONTEXT_BEFORE_UPDATE` BEFORE UPDATE ON `lti_context` FOR EACH ROW BEGIN
	DECLARE SIS_COURSE_CODE VARCHAR(100);
    DECLARE CONTEXT_ID		INT;
 
-- IF (NEW.sis_course_code IS NULL) THEN 
-- 	SELECT b.external_id from fci_course_xwalk b
-- where NEW.lms_course_code LIKE -- --concat('%',b.SUBJECT,REPLACE(REPLACE(REPLACE(b.COURSE_NUMBER,'X',''),'x',''),'-','%SEC'),'%')
-- 	        or NEW.lms_course_code LIKE concat('%',b.SUBJECT,'_',REPLACE(REPLACE(REPLACE(b.COURSE_NUMBER,'X',''),'x',''),'-','%SEC'),'%')
-- 			or NEW.lms_course_code = b.COURSE_NUMBER
-- 		into @SIS_COURSE_CODE;
 --        SET NEW.sis_course_code := @SIS_COURSE_CODE;
-- END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `lti_domain`
--

DROP TABLE IF EXISTS `lti_domain`;
CREATE TABLE `lti_domain` (
  `domain_id` int(11) NOT NULL,
  `key_id` int(11) NOT NULL,
  `context_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `domain` varchar(128) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `consumer_key` text,
  `secret` text,
  `json` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lti_key`
--

DROP TABLE IF EXISTS `lti_key`;
CREATE TABLE `lti_key` (
  `key_id` int(11) NOT NULL,
  `key_sha256` char(64) NOT NULL,
  `key_key` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `secret` text,
  `new_secret` text,
  `ack` text,
  `user_id` int(11) DEFAULT NULL,
  `consumer_profile` mediumtext,
  `new_consumer_profile` mediumtext,
  `tool_profile` mediumtext,
  `new_tool_profile` mediumtext,
  `json` mediumtext,
  `settings` mediumtext,
  `settings_url` text,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-02 06:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lti_link`
--

DROP TABLE IF EXISTS `lti_link`;
CREATE TABLE `lti_link` (
  `link_id` int(11) NOT NULL,
  `link_sha256` char(64) NOT NULL,
  `link_key` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `context_id` int(11) NOT NULL,
  `path` text,
  `title` text,
  `json` mediumtext,
  `settings` mediumtext,
  `settings_url` text,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-02 06:00:00',
  `instructor_id` int(11) DEFAULT NULL,
  `fci_type` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lti_membership`
--

DROP TABLE IF EXISTS `lti_membership`;
CREATE TABLE `lti_membership` (
  `membership_id` int(11) NOT NULL,
  `context_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `role` smallint(6) DEFAULT NULL,
  `role_override` smallint(6) DEFAULT NULL,
  `json` mediumtext,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-02 06:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lti_nonce`
--

DROP TABLE IF EXISTS `lti_nonce`;
CREATE TABLE `lti_nonce` (
  `nonce` char(128) NOT NULL,
  `key_id` int(11) NOT NULL,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lti_result`
--

DROP TABLE IF EXISTS `lti_result`;
CREATE TABLE `lti_result` (
  `result_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `result_url` text,
  `sourcedid` text,
  `service_id` int(11) DEFAULT NULL,
  `ipaddr` varchar(64) DEFAULT NULL,
  `grade` float DEFAULT NULL,
  `note` mediumtext,
  `server_grade` float DEFAULT NULL,
  `json` mediumtext,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-02 06:00:00',
  `retrieved_at` datetime DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `user_updated` datetime DEFAULT NULL,
  `instructor_updated` datetime DEFAULT NULL,
  `fci_type` varchar(2) DEFAULT NULL,
  `sis_enrollment_id` varchar(100) DEFAULT NULL,
  `fci_state` varchar(2) DEFAULT NULL,
  `current_term` varchar(100) DEFAULT NULL,
  `current_section_term` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `lti_result`
--
DROP TRIGGER IF EXISTS `LTI_RESULT_BEFORE_INSERT`;
DELIMITER $$
CREATE TRIGGER `LTI_RESULT_BEFORE_INSERT` BEFORE INSERT ON `lti_result` FOR EACH ROW BEGIN
	DECLARE MAX_EXTCOURSEID VARCHAR(100);
	DECLARE MIN_FCISTATE 	VARCHAR(100);
    DECLARE RESULT_ID		INT;
    
IF (NEW.sis_enrollment_id IS NULL) THEN
	SELECT 
		MAX(b.EXT_COURSE_ID),
		MIN(
		CASE WHEN t.term_start_dt IS NULL THEN '0'
		WHEN sysdate() BETWEEN t.term_start_dt AND DATE_ADD(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 1 MONTH) THEN '1' 
		WHEN sysdate() BETWEEN t.term_start_dt AND DATE_ADD(DATE_ADD(t.term_start_dt,INTERVAL -1 DAY),INTERVAL 2 MONTH) THEN '2' 
		WHEN sysdate() BETWEEN t.term_start_dt AND DATE_ADD(DATE_ADD(t.term_start_dt,INTERVAL -1 DAY),INTERVAL 3 MONTH) THEN '3' 
		WHEN sysdate() < t.term_start_dt THEN '4'
		WHEN sysdate() > DATE_ADD(DATE_ADD(t.term_start_dt,INTERVAL -1 DAY),INTERVAL 3 MONTH) THEN '5'
		ELSE '0' END
		)
		AS fci_state
	 FROM 
	 lti_user a,
	 fci_sis_enrollments b, 
	 lti_link d, 
	 lti_context e,
	 fci_term t 
	 WHERE 
	 (e.sis_course_code=b.course_id OR
	 e.sis_course_code=concat(b.course_id,'-',b.class_section) OR
	 e.lms_course_code LIKE concat('%',replace(replace(b.course_id,'X',''),'x',''),'%')
	 ) AND
	 a.lms_defined_id IN(b.EXT_STUDENT_ID2,b.EXT_STUDENT_ID3) AND 
	 b.transfer IS NULL AND
	 NEW.user_id=a.user_id AND
	 NEW.link_id=d.link_id AND
	 d.context_id=e.context_id AND
	 t.term_id=substr(b.EXT_COURSE_ID,-4,4)
	 GROUP BY a.lms_defined_id,b.course_id
     INTO @MAX_EXTCOURSEID,@MIN_FCISTATE
     ;
     
     IF (@MAX_EXTCOURSEID IS NOT NULL) THEN
	   	SET NEW.sis_enrollment_id := @MAX_EXTCOURSEID;
	 END IF;
     IF (@MIN_FCISTATE IS NOT NULL) THEN
        SET NEW.fci_state := @MIN_FCISTATE;
	 END IF;
END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `lti_service`
--

DROP TABLE IF EXISTS `lti_service`;
CREATE TABLE `lti_service` (
  `service_id` int(11) NOT NULL,
  `service_sha256` char(64) NOT NULL,
  `service_key` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `key_id` int(11) NOT NULL,
  `format` varchar(1024) DEFAULT NULL,
  `json` mediumtext,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-02 06:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lti_user`
--

DROP TABLE IF EXISTS `lti_user`;
CREATE TABLE `lti_user` (
  `user_id` int(11) NOT NULL,
  `user_sha256` char(64) NOT NULL,
  `user_key` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `key_id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `displayname` text,
  `email` text,
  `locale` char(63) DEFAULT NULL,
  `image` text,
  `subscribe` smallint(6) DEFAULT NULL,
  `json` mediumtext,
  `login_at` datetime DEFAULT NULL,
  `login_count` int(11) DEFAULT NULL,
  `ipaddr` varchar(64) DEFAULT NULL,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-02 06:00:00',
  `lms_defined_id` varchar(100) DEFAULT NULL,
  `lms_username` varchar(100) DEFAULT NULL,
  `lms_rolename` varchar(100) DEFAULT NULL,
  `canvas_user_id` varchar(100) DEFAULT NULL,
  `canvas_sis_user` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mail_bulk`
--

DROP TABLE IF EXISTS `mail_bulk`;
CREATE TABLE `mail_bulk` (
  `bulk_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `context_id` int(11) NOT NULL,
  `subject` varchar(256) DEFAULT NULL,
  `body` text,
  `json` text,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mail_sent`
--

DROP TABLE IF EXISTS `mail_sent`;
CREATE TABLE `mail_sent` (
  `sent_id` int(11) NOT NULL,
  `context_id` int(11) NOT NULL,
  `link_id` int(11) DEFAULT NULL,
  `user_to` int(11) DEFAULT NULL,
  `user_from` int(11) DEFAULT NULL,
  `subject` varchar(256) DEFAULT NULL,
  `body` text,
  `json` text,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `peer_assn`
--

DROP TABLE IF EXISTS `peer_assn`;
CREATE TABLE `peer_assn` (
  `assn_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `due_at` datetime NOT NULL,
  `json` text,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `peer_flag`
--

DROP TABLE IF EXISTS `peer_flag`;
CREATE TABLE `peer_flag` (
  `flag_id` int(11) NOT NULL,
  `submit_id` int(11) NOT NULL,
  `grade_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `note` text,
  `response` text,
  `handled` tinyint(1) NOT NULL DEFAULT '0',
  `respond_id` int(11) NOT NULL,
  `json` text,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `peer_grade`
--

DROP TABLE IF EXISTS `peer_grade`;
CREATE TABLE `peer_grade` (
  `grade_id` int(11) NOT NULL,
  `submit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` double DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `note` text,
  `json` text,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `peer_submit`
--

DROP TABLE IF EXISTS `peer_submit`;
CREATE TABLE `peer_submit` (
  `submit_id` int(11) NOT NULL,
  `assn_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `json` text,
  `note` text,
  `reflect` text,
  `regrade` tinyint(4) DEFAULT NULL,
  `inst_points` double DEFAULT NULL,
  `inst_note` text,
  `inst_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `peer_text`
--

DROP TABLE IF EXISTS `peer_text`;
CREATE TABLE `peer_text` (
  `text_id` int(11) NOT NULL,
  `assn_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `data` text,
  `json` text,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `profile_id` int(11) NOT NULL,
  `profile_sha256` char(64) NOT NULL,
  `profile_key` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `key_id` int(11) NOT NULL,
  `displayname` text,
  `email` text,
  `image` text,
  `locale` char(63) DEFAULT NULL,
  `subscribe` smallint(6) DEFAULT NULL,
  `json` mediumtext,
  `login_at` datetime DEFAULT NULL,
  `entity_version` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-02 06:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `repeat_students_vw`
--

DROP TABLE IF EXISTS `repeat_students_vw`;
CREATE TABLE `repeat_students_vw` (
  `displayname` text,
  `title` text,
  `fci_type` varchar(2) DEFAULT NULL,
  `user_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `students_not_in_fci_sis_bio_demo_vw`
--

DROP TABLE IF EXISTS `students_not_in_fci_sis_bio_demo_vw`;
CREATE TABLE `students_not_in_fci_sis_bio_demo_vw` (
  `displayname` text,
  `email` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Student View`
--

DROP TABLE IF EXISTS `Student View`;
CREATE TABLE `Student View` (
  `displayname` text,
  `email` text,
  `fci_type` varchar(2) DEFAULT NULL,
  `user_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attend`
--
ALTER TABLE `attend`
  ADD UNIQUE KEY `link_id` (`link_id`,`user_id`,`attend`),
  ADD KEY `attend_ibfk_2` (`user_id`);

--
-- Indexes for table `blob_file`
--
ALTER TABLE `blob_file`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `blob_indx_1` (`file_sha256`) USING HASH,
  ADD KEY `blob_ibfk_1` (`context_id`);

--
-- Indexes for table `fci_course_xwalk`
--
ALTER TABLE `fci_course_xwalk`
  ADD PRIMARY KEY (`EXTERNAL_ID`,`SUBJECT`,`COURSE_NUMBER`),
  ADD KEY `fci_course_xwalk_index` (`EXTERNAL_ID`,`SUBJECT`,`COURSE_NUMBER`);

--
-- Indexes for table `fci_link_history`
--
ALTER TABLE `fci_link_history`
  ADD PRIMARY KEY (`link_id`,`saved_timestamp`);

--
-- Indexes for table `fci_month`
--
ALTER TABLE `fci_month`
  ADD PRIMARY KEY (`month_id`);

--
-- Indexes for table `fci_result_history`
--
ALTER TABLE `fci_result_history`
  ADD PRIMARY KEY (`result_id`,`saved_timestamp`);

--
-- Indexes for table `fci_sis_bio_demo`
--
ALTER TABLE `fci_sis_bio_demo`
  ADD PRIMARY KEY (`EXT_STUDENT_ID1`);

--
-- Indexes for table `fci_sis_enrollments`
--
ALTER TABLE `fci_sis_enrollments`
  ADD PRIMARY KEY (`EXT_STUDENT_ID1`,`EXT_COURSE_ID`,`COURSE_ID`) USING BTREE;

--
-- Indexes for table `fci_term`
--
ALTER TABLE `fci_term`
  ADD PRIMARY KEY (`term_id`);

--
-- Indexes for table `key_request`
--
ALTER TABLE `key_request`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `key_request_fk_1` (`user_id`);

--
-- Indexes for table `lms_plugins`
--
ALTER TABLE `lms_plugins`
  ADD PRIMARY KEY (`plugin_id`),
  ADD UNIQUE KEY `plugin_path` (`plugin_path`);

--
-- Indexes for table `lti_context`
--
ALTER TABLE `lti_context`
  ADD PRIMARY KEY (`context_id`),
  ADD UNIQUE KEY `key_id` (`key_id`,`context_sha256`);

--
-- Indexes for table `lti_domain`
--
ALTER TABLE `lti_domain`
  ADD PRIMARY KEY (`domain_id`),
  ADD UNIQUE KEY `key_id` (`key_id`,`context_id`,`domain`,`port`),
  ADD KEY `lti_domain_ibfk_2` (`context_id`);

--
-- Indexes for table `lti_key`
--
ALTER TABLE `lti_key`
  ADD PRIMARY KEY (`key_id`),
  ADD UNIQUE KEY `key_sha256` (`key_sha256`),
  ADD UNIQUE KEY `key_sha256_2` (`key_sha256`);

--
-- Indexes for table `lti_link`
--
ALTER TABLE `lti_link`
  ADD PRIMARY KEY (`link_id`),
  ADD UNIQUE KEY `link_sha256` (`link_sha256`,`context_id`),
  ADD KEY `lti_link_ibfk_1` (`context_id`);

--
-- Indexes for table `lti_membership`
--
ALTER TABLE `lti_membership`
  ADD PRIMARY KEY (`membership_id`),
  ADD UNIQUE KEY `context_id` (`context_id`,`user_id`),
  ADD KEY `lti_membership_ibfk_2` (`user_id`);

--
-- Indexes for table `lti_nonce`
--
ALTER TABLE `lti_nonce`
  ADD UNIQUE KEY `key_id` (`key_id`,`nonce`),
  ADD KEY `nonce_indx_1` (`nonce`) USING HASH;

--
-- Indexes for table `lti_result`
--
ALTER TABLE `lti_result`
  ADD PRIMARY KEY (`result_id`),
  ADD UNIQUE KEY `link_id` (`link_id`,`user_id`),
  ADD UNIQUE KEY `sis_enrollments` (`result_id`,`link_id`,`sis_enrollment_id`),
  ADD KEY `lti_result_ibfk_2` (`user_id`),
  ADD KEY `lti_result_ibfk_3` (`service_id`);

--
-- Indexes for table `lti_service`
--
ALTER TABLE `lti_service`
  ADD PRIMARY KEY (`service_id`),
  ADD UNIQUE KEY `key_id` (`key_id`,`service_sha256`);

--
-- Indexes for table `lti_user`
--
ALTER TABLE `lti_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `key_id` (`key_id`,`user_sha256`);

--
-- Indexes for table `mail_bulk`
--
ALTER TABLE `mail_bulk`
  ADD PRIMARY KEY (`bulk_id`),
  ADD KEY `mail_bulk_ibfk_1` (`context_id`),
  ADD KEY `mail_bulk_ibfk_2` (`user_id`);

--
-- Indexes for table `mail_sent`
--
ALTER TABLE `mail_sent`
  ADD PRIMARY KEY (`sent_id`),
  ADD KEY `mail_sent_ibfk_1` (`context_id`),
  ADD KEY `mail_sent_ibfk_2` (`link_id`),
  ADD KEY `mail_sent_ibfk_3` (`user_to`),
  ADD KEY `mail_sent_ibfk_4` (`user_from`);

--
-- Indexes for table `peer_assn`
--
ALTER TABLE `peer_assn`
  ADD PRIMARY KEY (`assn_id`),
  ADD UNIQUE KEY `link_id` (`link_id`);

--
-- Indexes for table `peer_flag`
--
ALTER TABLE `peer_flag`
  ADD PRIMARY KEY (`flag_id`),
  ADD UNIQUE KEY `submit_id` (`submit_id`,`grade_id`,`user_id`);

--
-- Indexes for table `peer_grade`
--
ALTER TABLE `peer_grade`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `submit_id` (`submit_id`,`user_id`);

--
-- Indexes for table `peer_submit`
--
ALTER TABLE `peer_submit`
  ADD PRIMARY KEY (`submit_id`),
  ADD UNIQUE KEY `assn_id` (`assn_id`,`user_id`),
  ADD KEY `peer_submit_ibfk_1` (`user_id`);

--
-- Indexes for table `peer_text`
--
ALTER TABLE `peer_text`
  ADD PRIMARY KEY (`text_id`),
  ADD KEY `peer_text_ibfk_1` (`assn_id`),
  ADD KEY `peer_text_ibfk_2` (`user_id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `profile_sha256` (`profile_sha256`),
  ADD UNIQUE KEY `profile_id` (`profile_id`,`profile_sha256`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blob_file`
--
ALTER TABLE `blob_file`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `key_request`
--
ALTER TABLE `key_request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_plugins`
--
ALTER TABLE `lms_plugins`
  MODIFY `plugin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti_context`
--
ALTER TABLE `lti_context`
  MODIFY `context_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti_domain`
--
ALTER TABLE `lti_domain`
  MODIFY `domain_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti_key`
--
ALTER TABLE `lti_key`
  MODIFY `key_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti_link`
--
ALTER TABLE `lti_link`
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti_membership`
--
ALTER TABLE `lti_membership`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti_result`
--
ALTER TABLE `lti_result`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti_service`
--
ALTER TABLE `lti_service`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti_user`
--
ALTER TABLE `lti_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mail_bulk`
--
ALTER TABLE `mail_bulk`
  MODIFY `bulk_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mail_sent`
--
ALTER TABLE `mail_sent`
  MODIFY `sent_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peer_assn`
--
ALTER TABLE `peer_assn`
  MODIFY `assn_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peer_flag`
--
ALTER TABLE `peer_flag`
  MODIFY `flag_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peer_grade`
--
ALTER TABLE `peer_grade`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peer_submit`
--
ALTER TABLE `peer_submit`
  MODIFY `submit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peer_text`
--
ALTER TABLE `peer_text`
  MODIFY `text_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attend`
--
ALTER TABLE `attend`
  ADD CONSTRAINT `attend_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `lti_link` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attend_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `lti_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `blob_file`
--
ALTER TABLE `blob_file`
  ADD CONSTRAINT `blob_ibfk_1` FOREIGN KEY (`context_id`) REFERENCES `lti_context` (`context_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `key_request`
--
ALTER TABLE `key_request`
  ADD CONSTRAINT `key_request_fk_1` FOREIGN KEY (`user_id`) REFERENCES `lti_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lti_context`
--
ALTER TABLE `lti_context`
  ADD CONSTRAINT `lti_context_ibfk_1` FOREIGN KEY (`key_id`) REFERENCES `lti_key` (`key_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lti_domain`
--
ALTER TABLE `lti_domain`
  ADD CONSTRAINT `lti_domain_ibfk_1` FOREIGN KEY (`key_id`) REFERENCES `lti_key` (`key_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lti_domain_ibfk_2` FOREIGN KEY (`context_id`) REFERENCES `lti_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lti_link`
--
ALTER TABLE `lti_link`
  ADD CONSTRAINT `lti_link_ibfk_1` FOREIGN KEY (`context_id`) REFERENCES `lti_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lti_membership`
--
ALTER TABLE `lti_membership`
  ADD CONSTRAINT `lti_membership_ibfk_1` FOREIGN KEY (`context_id`) REFERENCES `lti_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lti_membership_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `lti_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lti_result`
--
ALTER TABLE `lti_result`
  ADD CONSTRAINT `lti_result_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `lti_link` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lti_result_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `lti_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lti_result_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `lti_service` (`service_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lti_service`
--
ALTER TABLE `lti_service`
  ADD CONSTRAINT `lti_service_ibfk_1` FOREIGN KEY (`key_id`) REFERENCES `lti_key` (`key_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lti_user`
--
ALTER TABLE `lti_user`
  ADD CONSTRAINT `lti_user_ibfk_1` FOREIGN KEY (`key_id`) REFERENCES `lti_key` (`key_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mail_bulk`
--
ALTER TABLE `mail_bulk`
  ADD CONSTRAINT `mail_bulk_ibfk_1` FOREIGN KEY (`context_id`) REFERENCES `lti_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mail_bulk_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `lti_user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `mail_sent`
--
ALTER TABLE `mail_sent`
  ADD CONSTRAINT `mail_sent_ibfk_1` FOREIGN KEY (`context_id`) REFERENCES `lti_context` (`context_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mail_sent_ibfk_2` FOREIGN KEY (`link_id`) REFERENCES `lti_link` (`link_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `mail_sent_ibfk_3` FOREIGN KEY (`user_to`) REFERENCES `lti_user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `mail_sent_ibfk_4` FOREIGN KEY (`user_from`) REFERENCES `lti_user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `peer_assn`
--
ALTER TABLE `peer_assn`
  ADD CONSTRAINT `peer_assn_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `lti_link` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `peer_flag`
--
ALTER TABLE `peer_flag`
  ADD CONSTRAINT `peer_flag_ibfk_1` FOREIGN KEY (`submit_id`) REFERENCES `peer_submit` (`submit_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `peer_grade`
--
ALTER TABLE `peer_grade`
  ADD CONSTRAINT `peer_grade_ibfk_1` FOREIGN KEY (`submit_id`) REFERENCES `peer_submit` (`submit_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `peer_submit`
--
ALTER TABLE `peer_submit`
  ADD CONSTRAINT `peer_submit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `lti_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peer_submit_ibfk_2` FOREIGN KEY (`assn_id`) REFERENCES `peer_assn` (`assn_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `peer_text`
--
ALTER TABLE `peer_text`
  ADD CONSTRAINT `peer_text_ibfk_1` FOREIGN KEY (`assn_id`) REFERENCES `peer_assn` (`assn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peer_text_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `lti_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
