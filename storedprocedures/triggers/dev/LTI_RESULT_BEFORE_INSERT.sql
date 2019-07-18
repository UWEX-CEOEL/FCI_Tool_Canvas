BEGIN
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
