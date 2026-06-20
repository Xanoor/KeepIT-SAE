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

DELIMITER //

DROP TABLE IF EXISTS ip_ban//

CREATE TABLE IF NOT EXISTS ip_ban (
    ip_address VARBINARY(16) PRIMARY KEY,--
    ip_version TINYINT NOT NULL,
    ban_date DATETIME DEFAULT CURRENT_TIMESTAMP,--
    reason VARCHAR(100),--
    INDEX idx_ip_ban_ip_version (ip_version),
    INDEX idx_ip_ban_ban_date (ip_address)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'TABLE OF IP BANNED'//

-- INET6_NTOA (DECODE) and INET6_ATON (ENCODE) take IPV6 or IPV4

-- SELECT INET6_NTOA(INET6_ATON('fe80::4e59:5c08:7a13:ce20')), INET6_NTOA(INET6_ATON('10.188.119.17'));
-- SELECT UNHEX(HEX(INET6_ATON('fe80::4e59:5c08:7a13:ce20'))), INET6_ATON('fe80::4e59:5c08:7a13:ce20'), HEX(INET6_ATON('10.188.119.17')), INET6_ATON('10.188.119.17')//


DROP PROCEDURE IF EXISTS insert_ban_ip//

CREATE PROCEDURE insert_ban_ip(IN ip VARCHAR(39), IN message VARCHAR(100), OUT result BOOLEAN)
BEGIN
    DECLARE exist_ip VARBINARY(16);

    -- Vérifier que l'adresse ip n'existe pas déjà
    SELECT ip_address INTO exist_ip FROM ip_ban WHERE ip_address = INET6_ATON(ip);

    IF exist_ip IS NULL THEN
        IF IS_IPV4(ip) THEN
            INSERT INTO ip_ban(ip_address, ip_version, reason)
            VALUES (INET6_ATON(ip), 4, message);
            SET result = 0;
        ELSEIF  IS_IPV6(ip) THEN
            INSERT INTO ip_ban(ip_address, ip_version, reason)
            VALUES (INET6_ATON(ip), 6, message);
            SET result = 0;
        ELSE
            SET result = 1;
        END IF;
    ELSE
        UPDATE ip_ban SET reason=message WHERE ip_address = INET6_ATON(ip);
        SET result = 0;
    END IF;
END
//
-- CALL insert_ban_ip('192.168.25.19', 'THE LENGTH IS 100 CHARS', @result);
-- SELECT @result as result;


DROP PROCEDURE IF EXISTS delete_ban_ip//

CREATE PROCEDURE delete_ban_ip(IN ip VARCHAR(39), OUT result BOOLEAN)
BEGIN
    DECLARE exist_ip VARBINARY(16);

    -- Vérifier que l'adresse ip n'existe pas déjà
    SELECT ip_address INTO exist_ip FROM ip_ban WHERE ip_address = INET6_ATON(ip);

    IF exist_ip IS NOT NULL THEN
        DELETE FROM ip_ban WHERE ip_address = exist_ip;
        DELETE FROM users_logs WHERE ip_address = exist_ip AND action_did = 'TRY CONNECTION';
        SET result = 0;
    ELSE
        DELETE FROM users_logs WHERE ip_address = INET6_ATON(ip) AND action_did = 'TRY CONNECTION';
        SET result = 1;
    END IF;
END
//
-- CALL delete_ban_ip('192.168.25.19',@result);
-- SELECT @result as result;


DELIMITER //

CREATE OR REPLACE TRIGGER users_logs_ban
    AFTER INSERT ON users_logs
    FOR EACH ROW
BEGIN

    DECLARE nb_try INTEGER;

    IF NEW.action_did = 'TRY CONNECTION' THEN

        SELECT
            COUNT(*) INTO nb_try
        FROM
            users_logs
        WHERE
            action_did = 'TRY CONNECTION' AND
            login = new.login AND
            ip_address = NEW.ip_address AND
            log_date >= DATE_SUB(NOW(), INTERVAL 30 MINUTE);

        IF nb_try > 7 THEN
            CALL insert_ban_ip(INET6_NTOA(NEW.ip_address), 'YOUR ACCOUNT HAS BEEN TEMPORARILY BANNED DUE TO TOO MANY FAILED LOGIN ATTEMPTS.', @result);
        END IF;

    END IF;

END
//

DELIMITER ;