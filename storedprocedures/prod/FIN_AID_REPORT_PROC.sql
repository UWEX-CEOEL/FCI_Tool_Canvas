SELECT u.lms_username AS USERNAME, u.lms_defined_id AS ORG_DEFINED_ID, u.lms_rolename AS ROLE_NAME, c.lms_course_code AS ORG_UNIT_CODE, null AS ORG_UNIT_ID, DATE_FORMAT(r.created_at, "%m/%d/%Y %H:%i:%s") AS DATE_SUBMITTED, null AS DATE_POSTED, null AS TIME_STARTED, null AS TIME_COMPLETED, m.created_at AS STUDENT_ENROLLMENT_DATE
INTO OUTFILE '/tmp/report.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '
'
FROM `lti_user` AS u INNER JOIN `lti_result` AS r ON u.user_id=r.user_id INNER JOIN `lti_link` AS l ON r.link_id=l.link_id INNER JOIN `lti_context` AS c ON l.context_id=c.context_id INNER JOIN `lti_membership` AS m ON u.user_id=m.user_id
