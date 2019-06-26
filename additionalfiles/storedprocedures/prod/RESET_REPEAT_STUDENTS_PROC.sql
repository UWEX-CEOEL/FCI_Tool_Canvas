BEGIN

UPDATE lti_result AS a, fci_result_history AS b
JOIN lti_result USING (result_id)
SET a.json = null,
a.grade = null,
b.reset_flag = 1
WHERE a.user_updated < (
  SELECT c.term_start_dt
  FROM fci_term AS c
  WHERE c.term_id LIKE SUBSTRING(a.sis_enrollment_id, -4)
);

END
