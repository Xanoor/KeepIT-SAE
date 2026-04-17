SET GLOBAL event_scheduler = ON;

DELIMITER //

DROP PROCEDURE IF EXISTS clean_inf_month_logs//

-- LIMIT + WHILE FOR MASSIVE LOGS
CREATE PROCEDURE clean_inf_month_logs()
BEGIN
	DELETE FROM device_logs WHERE log_date < DATE_SUB(NOW(), INTERVAL 1 MONTH);
	DELETE FROM constant_logs WHERE log_date < DATE_SUB(NOW(), INTERVAL 1 MONTH);
	DELETE FROM users_logs WHERE log_date < DATE_SUB(NOW(), INTERVAL 1 MONTH);
END//
	

DROP EVENT IF EXISTS clean_logs_daily//

-- EVERY DAY AT 2a.m.
CREATE EVENT clean_logs_daily
ON SCHEDULE EVERY 1 DAY
STARTS CURDATE() + INTERVAL 26 HOUR
DO CALL clean_inf_month_logs()//


DROP PROCEDURE IF EXISTS add_test_logs//

CREATE PROCEDURE add_test_logs()
BEGIN

	DECLARE sn VARCHAR(25);

	SELECT serial_number INTO sn FROM devices ORDER BY updated_at DESC LIMIT 1;
	
	INSERT INTO device_logs(log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
	VALUES (DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH), 'SYSTEM', sn, 'COMPUTER', 'UPDATE', 'logs', 'attention', 'test');
	
END//

DELIMITER ;

-- CALL add_test_logs();


-- SELECT * FROM device_logs ORDER BY log_date DESC;

-- ALTER EVENT clean_logs_daily DISABLE;
-- ALTER EVENT clean_logs_daily ENABLE;

-- SELECT * FROM information_schema.EVENTS;
-- SHOW EVENTS;