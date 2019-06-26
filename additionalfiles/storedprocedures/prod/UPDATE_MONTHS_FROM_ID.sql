UPDATE tsugi.fci_month A SET
month_start_dt = DATE(CONCAT(substr(A.month_id,3,4),'-',substr(A.month_id,1,2),'-01')),
month_end_dt = LAST_DAY(DATE(CONCAT(substr(A.month_id,3,4),'-',substr(A.month_id,1,2),'-01'))),
sp_m1_start_dt = DATE_ADD(CONCAT(substr(A.month_id,3,4),'-',substr(A.month_id,1,2),'-01'), INTERVAL 1 DAY),
day10_dt = DATE_ADD(CONCAT(substr(A.month_id,3,4),'-',substr(A.month_id,1,2),'-01'), INTERVAL 11 DAY),
day17_dt = DATE_ADD(CONCAT(substr(A.month_id,3,4),'-',substr(A.month_id,1,2),'-01'), INTERVAL 16 DAY)
