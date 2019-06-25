BEGIN
	DECLARE SIS_COURSE_CODE VARCHAR(100);
    DECLARE CONTEXT_ID		INT;
 
-- IF (NEW.sis_course_code IS NULL) THEN 
-- 	SELECT b.external_id from fci_course_xwalk b
-- where NEW.lms_course_code LIKE -- --concat('%',b.SUBJECT,REPLACE(REPLACE(REPLACE(b.COURSE_NUMBER,'X',''),'x',''),'-','%SEC'),'%')
-- 	        or NEW.lms_course_code LIKE concat('%',b.SUBJECT,'_',REPLACE(REPLACE(REPLACE(b.COURSE_NUMBER,'X',''),'x',''),'-','%SEC'),'%')
-- 			or NEW.lms_course_code = b.COURSE_NUMBER
-- 		into @SIS_COURSE_CODE;
 --        SET NEW.sis_course_code := @SIS_COURSE_CODE;
-- END IF;
END
