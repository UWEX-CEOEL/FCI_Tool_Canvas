BEGIN
	IF (OLD.json <> NEW.json) THEN
	   INSERT INTO 
	   tsugi.fci_link_history(
	   link_id,
	   link_sha256,
	   link_key,
	   json,
	   created_at,
       instructor_id,
	   saved_timestamp
	   )
	   VALUES(
	   NEW.link_id,
	   NEW.link_sha256,
	   NEW.link_key,
	   NEW.json,
	   NEW.created_at,
       NEW.instructor_id,
	   SYSDATE());
	END IF;   
END
