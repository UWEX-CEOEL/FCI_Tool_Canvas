BEGIN

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
	);


UPDATE lti_result a SET a.fci_state = 3 WHERE a.sis_enrollment_id in (SELECT EXT_COURSE_ID FROM fci_sis_enrollments WHERE LETTER_GRADE IN ("PR", "IP"));

END
