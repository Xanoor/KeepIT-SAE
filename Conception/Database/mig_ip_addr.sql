ALTER TABLE users_logs ADD ip_address VARBINARY(16) AFTER action_did;

ALTER TABLE `users` ADD last_ip_address VARBINARY(16);

DROP PROCEDURE IF EXISTS logs_password;

DELIMITER //

CREATE PROCEDURE logs_password(IN user_login VARCHAR(36), IN ip VARCHAR(39), IN success INTEGER)
BEGIN
    DECLARE exist_login VARCHAR(36);

    -- Vérifier qu'il s'agit bien d'un login valide
    SELECT login INTO exist_login FROM users WHERE login = user_login;

    IF exist_login IS NOT NULL THEN

        IF success >= 1 THEN
            INSERT INTO users_logs(log_date, login, action_did, ip_address)
            VALUES (NOW(),user_login, 'SUCCESS CONNECTION', INET6_ATON(ip));
            UPDATE users SET last_ip_address=ip WHERE login = user_login;
        ELSE
            INSERT INTO users_logs(log_date, login, action_did, ip_address)
            VALUES (NOW(),user_login, 'TRY CONNECTION', INET6_ATON(ip));
        END IF;

    END IF;
END
//


-- EXECUTER ENSUITE ACTION puis VIEWS