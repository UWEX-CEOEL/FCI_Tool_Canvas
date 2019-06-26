BEGIN

DECLARE termOne varchar(45);
DECLARE termTwo varchar(45);
DECLARE termThree varchar(45);
DECLARE termFour varchar(45);

SET termOne = (SELECT a.term_id FROM tsugi.fci_term AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_1, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_1, 3, 4));
SET termTwo = (SELECT a.term_id FROM tsugi.fci_term AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_2, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_2, 3, 4));
SET termThree = (SELECT a.term_id FROM tsugi.fci_term AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_3, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_3, 3, 4));
SET termFour = (SELECT a.term_id FROM tsugi.fci_term AS a WHERE DATE_FORMAT((NOW() - INTERVAL 1 MONTH), '%m') = SUBSTR(a.term_month_3, 1, 2) AND DATE_FORMAT((NOW() - INTERVAL 1 MONTH), '%Y') = SUBSTR(a.term_month_3, 3, 4));

-- UPDATE RESULT RECORDS WITH SIS_ENROLLMENT_ID
	UPDATE tsugi.lti_result r SET r.sis_enrollment_id =(
	  SELECT MAX(b.ext_course_id)
		FROM tsugi.lti_user a,
			tsugi.fci_sis_enrollments b,
			tsugi.lti_link d,
			tsugi.lti_context e
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
				 SUBSTR(b.ext_course_id, -4, 4) LIKE termOne
				 OR SUBSTR(b.ext_course_id, -4, 4) LIKE termTwo
				 OR SUBSTR(b.ext_course_id, -4, 4) LIKE termThree
				 OR SUBSTR(b.ext_course_id, -4, 4) LIKE termFour
			 )
		 GROUP BY a.lms_defined_id,b.course_id
		 LIMIT 1
		)
	 WHERE EXISTS (
		 SELECT 'X' FROM
		 tsugi.lti_user a,
		 tsugi.fci_sis_enrollments b,
		 tsugi.lti_link d,
		 tsugi.lti_context e
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
			 SUBSTR(b.ext_course_id, -4, 4) LIKE termOne
			 OR SUBSTR(b.ext_course_id, -4, 4) LIKE termTwo
			 OR SUBSTR(b.ext_course_id, -4, 4) LIKE termThree
			 OR SUBSTR(b.ext_course_id, -4, 4) LIKE termFour
		 )
		 GROUP BY a.lms_defined_id,b.course_id
	);

END
