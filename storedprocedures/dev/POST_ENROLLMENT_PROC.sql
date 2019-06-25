BEGIN

DECLARE termOne varchar(45);
DECLARE termTwo varchar(45);
DECLARE termThree varchar(45);
DECLARE termFour varchar(45);

SET termOne = (SELECT a.term_id FROM `fci_term` AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_1, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_1, 3, 4));
SET termTwo = (SELECT a.term_id FROM `fci_term` AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_2, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_2, 3, 4));
SET termThree = (SELECT a.term_id FROM `fci_term` AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_3, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_3, 3, 4));
SET termFour = (SELECT a.term_id FROM `fci_term` AS a WHERE DATE_FORMAT((NOW() - INTERVAL 1 MONTH), '%m') = SUBSTR(a.term_month_3, 1, 2) AND DATE_FORMAT((NOW() - INTERVAL 1 MONTH), '%Y') = SUBSTR(a.term_month_3, 3, 4));


-- IF ((DATE_FORMAT(NOW(),'%H-%i') >= 07-15) AND (DATE_FORMAT(NOW(),'%H-%i') <= 08-15)) THEN

-- UPDATE CONTEXT RECORDS WITH SIS_COURSE_CODE
	UPDATE tsugi.lti_context a SET a.sis_course_code =
    (SELECT b.external_id
		FROM fci_course_xwalk b
		WHERE a.lms_course_code LIKE
		Concat('%',b.subject,REPLACE(REPLACE(REPLACE(b.course_number,'X',''),'x',''),'-','%SEC'),'%')
		OR a.lms_course_code LIKE
		Concat('%',b.subject,'_',REPLACE(REPLACE(REPLACE(b.course_number,'X',''),'x',''),'-','%SEC'),'%')
		OR a.lms_course_code = b.course_number
		AND
		(
			a.sis_course_code LIKE termOne
	 	 OR a.sis_course_code LIKE termTwo
	 	 OR a.sis_course_code LIKE termThree
	 	 OR a.sis_course_code LIKE termFour
		)
		LIMIT 1
	 )
	 WHERE a.sis_course_code LIKE termOne
	 OR a.sis_course_code LIKE termTwo
	 OR a.sis_course_code LIKE termThree
	 OR a.sis_course_code LIKE termFour;

-- UPDATE RESULT RECORDS WITH SIS_ENROLLMENT_ID
	UPDATE lti_result r SET r.sis_enrollment_id =(
	  SELECT MAX(b.ext_course_id)
		FROM lti_user a,
			fci_sis_enrollments b,
			lti_link d,
			lti_context e
		WHERE
			(e.sis_course_code=b.course_id OR
			 e.sis_course_code=Concat(b.course_id,'-',b.class_section) OR
			 e.lms_course_code LIKE Concat('%',REPLACE(REPLACE(b.course_id,'X',''),'x',''),'%')
			 )AND
			(a.lms_defined_id IN(b.ext_student_id2,b.ext_student_id3)) AND
			 b.transfer IS NULL AND
			 r.user_id=a.user_id AND
			 r.link_id=d.link_id AND
			 d.context_id=e.context_id
			 AND (
				 SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termOne
				 OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termTwo
				 OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termThree
				 OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termFour
			 )
		 GROUP BY a.lms_defined_id,b.course_id
		 LIMIT 1
		)
	 WHERE EXISTS (
		 SELECT 'X' FROM
		 lti_user a,
		 fci_sis_enrollments b,
		 lti_link d,
		 lti_context e
		 		 WHERE
		 (e.sis_course_code=b.course_id OR
		 e.sis_course_code=Concat(b.course_id,'-',b.class_section) OR
		 e.lms_course_code LIKE Concat('%',REPLACE(REPLACE(b.course_id,'X',''),'x',''),'%')
		 ) AND
		 (a.lms_defined_id IN(b.ext_student_id2,b.ext_student_id3)) AND
		 b.transfer IS NULL AND
		 r.user_id=a.user_id AND
		 r.link_id=d.link_id AND
		 d.context_id=e.context_id
		 AND
		 (
			 SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termOne
			 OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termTwo
			 OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termThree
			 OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termFour
		 )
		 GROUP BY a.lms_defined_id,b.course_id
	)
	AND
	(
		SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termOne
		OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termTwo
		OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termThree
		OR SUBSTR(r.sis_enrollment_id, -4, 4) LIKE termFour
	);

-- UPDATE RESULT RECORDS WITH FCI_STATE

	UPDATE lti_result a SET a.fci_state = (
		SELECT
		CASE WHEN t.term_start_dt IS NULL THEN '0'
		WHEN NOW() BETWEEN CONVERT(t.term_start_dt,DATETIME) AND CONVERT(Date_add(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 1 month),DATETIME) THEN '1'
		WHEN NOW() BETWEEN CONVERT(t.term_start_dt,DATETIME) AND CONVERT(Date_add(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 2 month),DATETIME) THEN '2'
		WHEN NOW() BETWEEN CONVERT(t.term_start_dt,DATETIME) AND CONVERT(Date_add(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 3 month),DATETIME) THEN '3'
		WHEN Sysdate() < t.term_start_dt THEN '4'
		WHEN Sysdate() > Date_add(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 3 month) THEN '5'
		ELSE '0' end
		FROM fci_term t WHERE t.term_id=Substr(a.sis_enrollment_id,-4,4)
		LIMIT 1
	)
WHERE SUBSTR(a.sis_enrollment_id, -4, 4) LIKE termOne OR SUBSTR(a.sis_enrollment_id, -4, 4) LIKE termTwo OR SUBSTR(a.sis_enrollment_id, -4, 4) LIKE termThree OR SUBSTR(a.sis_enrollment_id, -4, 4) LIKE termFour;


update lti_result a set a.fci_state = 3 where a.sis_enrollment_id in (select EXT_COURSE_ID from fci_sis_enrollments where LETTER_GRADE IN ("PR", "IP"));


-- END IF;

END
