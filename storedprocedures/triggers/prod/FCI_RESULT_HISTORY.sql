BEGIN

DECLARE enrollmentCount INT default 1;
DECLARE studentEnrollments INT default 1;
DECLARE resetFlag BOOL default false;

SET enrollmentCount = (
  SELECT COUNT(*)
  FROM fci_sis_enrollments
  WHERE ext_student_id2 LIKE (
      SELECT DISTINCT lms_defined_id
      FROM lti_user
      WHERE user_id = NEW.user_id)
  AND course_id LIKE (
      SELECT DISTINCT c.sis_course_code
      FROM lti_context AS c INNER JOIN lti_link AS l USING (context_id)
      WHERE l.link_id = NEW.link_id)
);

SET studentEnrollments = (
    SELECT max(student_enrollments)
    FROM fci_result_history
    WHERE result_id = NEW.result_id
);

SET resetFlag = (enrollmentCount > studentEnrollments);



IF (OLD.json <> NEW.json) THEN
	   INSERT INTO 
	   tsugi.fci_result_history(
	   	   result_id,
           link_id,
           user_id,
           sourcedid,
           json,
           updated_at,
           instructor_id,
           user_updated,
           instructor_updated,
           saved_timestamp,
           reset_flag,
           student_enrollments
	   )
	   VALUES(
           NEW.result_id,
	       NEW.link_id,
           NEW.user_id,
           NEW.sourcedid,
           NEW.json,
           NEW.updated_at,
           NEW.instructor_id,
           NEW.user_updated,
           NEW.instructor_updated,
	   	   SYSDATE(),
           resetFlag,
           enrollmentCount
       );
	END IF;  
    
END
