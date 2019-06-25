BEGIN

DECLARE termOne varchar(45);
DECLARE termTwo varchar(45);
DECLARE termThree varchar(45);
DECLARE termFour varchar(45);

SET termOne = (SELECT a.term_id FROM tsugi.fci_term AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_1, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_1, 3, 4));
SET termTwo = (SELECT a.term_id FROM tsugi.fci_term AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_2, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_2, 3, 4));
SET termThree = (SELECT a.term_id FROM tsugi.fci_term AS a WHERE DATE_FORMAT(NOW(), '%m') = SUBSTR(a.term_month_3, 1, 2) AND DATE_FORMAT(NOW(), '%Y') = SUBSTR(a.term_month_3, 3, 4));
SET termFour = (SELECT a.term_id FROM tsugi.fci_term AS a WHERE DATE_FORMAT((NOW() - INTERVAL 1 MONTH), '%m') = SUBSTR(a.term_month_3, 1, 2) AND DATE_FORMAT((NOW() - INTERVAL 1 MONTH), '%Y') = SUBSTR(a.term_month_3, 3, 4));

	UPDATE tsugi.lti_result a SET a.fci_state = (
		SELECT
		CASE WHEN t.term_start_dt IS NULL THEN '0'
		WHEN NOW() BETWEEN CONVERT(t.term_start_dt,DATETIME) AND CONVERT(Date_add(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 1 month),DATETIME) THEN '1'
		WHEN NOW() BETWEEN CONVERT(t.term_start_dt,DATETIME) AND CONVERT(Date_add(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 2 month),DATETIME) THEN '2'
		WHEN NOW() BETWEEN CONVERT(t.term_start_dt,DATETIME) AND CONVERT(Date_add(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 3 month),DATETIME) THEN '3'
		WHEN Sysdate() < t.term_start_dt THEN '4'
		WHEN Sysdate() > Date_add(Date_add(t.term_start_dt,INTERVAL -1 day),INTERVAL 3 month) THEN '5'
		ELSE '0' end
		FROM tsugi.fci_term t WHERE t.term_id=Substr(a.sis_enrollment_id,-4,4)
		LIMIT 1
	)
WHERE SUBSTR(a.sis_enrollment_id, -4, 4) LIKE termOne OR SUBSTR(a.sis_enrollment_id, -4, 4) LIKE termTwo OR SUBSTR(a.sis_enrollment_id, -4, 4) LIKE termThree OR SUBSTR(a.sis_enrollment_id, -4, 4) LIKE termFour;


END
