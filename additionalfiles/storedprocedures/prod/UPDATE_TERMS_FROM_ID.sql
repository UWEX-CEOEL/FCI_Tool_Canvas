UPDATE tsugi.fci_term A SET
A.term_name = DATE_FORMAT(CONCAT('20',SUBSTR(term_id,1,1)-4,SUBSTR(term_id,2,1),'-',SUBSTR(term_id,3,2),'-01'),'%M %Y'),
A.term_month_1 =CONCAT(LPAD(SUBSTR(term_id,3,2),2,'0'),'20',SUBSTR(term_id,1,1)-4,SUBSTR(term_id,2,1)),
A.term_month_2 =DATE_FORMAT(DATE_ADD(CONCAT('20',SUBSTR(term_id,1,1)-4,SUBSTR(term_id,2,1),'-',LPAD(SUBSTR(term_id,3,2),2,'0'),'-01'),INTERVAL 1 MONTH),'%m%Y'),
A.term_month_3 =DATE_FORMAT(DATE_ADD(CONCAT('20',SUBSTR(term_id,1,1)-4,SUBSTR(term_id,2,1),'-',LPAD(SUBSTR(term_id,3,2),2,'0'),'-01'),INTERVAL 2 MONTH),'%m%Y'),
A.term_start_dt = (SELECT B.sp_m1_start_dt FROM tsugi.fci_month B WHERE B.month_id = CONCAT(LPAD(SUBSTR(A.term_id,3,2),2,'0'),'20',SUBSTR(A.term_id,1,1)-4,SUBSTR(A.term_id,2,1))),
A.term_end_dt = (SELECT B.month_end_dt FROM tsugi.fci_month B WHERE B.month_id = DATE_FORMAT(DATE_ADD(CONCAT('20',SUBSTR(term_id,1,1)-4,SUBSTR(term_id,2,1),'-',LPAD(SUBSTR(term_id,3,2),2,'0'),'-01'),INTERVAL 2 MONTH),'%m%Y')),
A.sp_fci_m1_due_dt = (SELECT B.day10_dt FROM tsugi.fci_month B WHERE B.month_id = CONCAT(LPAD(SUBSTR(A.term_id,3,2),2,'0'),'20',SUBSTR(A.term_id,1,1)-4,SUBSTR(A.term_id,2,1))),
A.sp_fci_m2_due_dt = (SELECT B.day17_dt FROM tsugi.fci_month B WHERE B.month_id = DATE_FORMAT(DATE_ADD(CONCAT('20',SUBSTR(term_id,1,1)-4,SUBSTR(term_id,2,1),'-',LPAD(SUBSTR(term_id,3,2),2,'0'),'-01'),INTERVAL 1 MONTH),'%m%Y')),
A.sp_fci_m3_due_dt = (SELECT B.day17_dt FROM tsugi.fci_month B WHERE B.month_id = DATE_FORMAT(DATE_ADD(CONCAT('20',SUBSTR(term_id,1,1)-4,SUBSTR(term_id,2,1),'-',LPAD(SUBSTR(term_id,3,2),2,'0'),'-01'),INTERVAL 2 MONTH),'%m%Y'))
