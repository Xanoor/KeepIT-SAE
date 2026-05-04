-- View

USE keepit;

-- MONITOR LINKED AT A COMPUTER
CREATE OR REPLACE VIEW vw_monitor_by_computer AS
    SELECT
        m.serial_number AS serial_monitor,
        m.connector_name AS connector,
        c.serial_number AS serial_computer,
        c.name AS computer_name,
        c.type_name AS computer_type
    FROM
        monitor m,
        computer c
    WHERE
        m.attached_to_computer = c.name;

-- ACTION/MONTH
CREATE OR REPLACE VIEW vw_action_on_device_by_month AS
    SELECT
        DATE_FORMAT(log_date, '%Y-%m') AS month_year,
        COUNT(*) AS actions_number
    FROM
        device_logs
    GROUP BY
        DATE_FORMAT(log_date, '%Y-%m');

-- RECAP LOGS MENSUEL
CREATE OR REPLACE VIEW vw_month_activity AS
    WITH monthly_field_activity AS (
        SELECT
            DATE_FORMAT(log_date, '%Y-%m') AS mois,
            field_updated,
            COUNT(*) AS nb_modifications,
            COUNT(DISTINCT serial_number) AS nb_devices,
            COUNT(DISTINCT login) AS nb_utilisateurs
        FROM device_logs
        GROUP BY DATE_FORMAT(log_date, '%Y-%m'), field_updated)
    SELECT
        mois,
        field_updated,
        nb_modifications,
        nb_devices,
        nb_utilisateurs,
        -- Classement des champs les plus modifiés par mois (Numérotation)
        DENSE_RANK() OVER (PARTITION BY mois ORDER BY nb_modifications DESC) AS month_rank,
            -- Classement global (Numérotation)
        RANK() OVER (ORDER BY nb_modifications DESC) AS global_rank,
            -- Permet de savoir si un champ est dans le top X% des modifications du mois
            -- Reçoit un score de 1 à 4 (1 = top 25% des champs les plus modifiés du mois)
        NTILE(4) OVER (PARTITION BY mois ORDER BY nb_modifications DESC) AS quartile_mois,
            -- Évolution vs mois précédent (Lag permet de prendre le résultat du mois précédent)
        nb_modifications - LAG(nb_modifications) OVER (PARTITION BY field_updated ORDER BY mois) AS evolution_vs_last_month,
            -- Évolution en pourcentage par rapport au mois précédent
        ROUND(
                (nb_modifications - LAG(nb_modifications) OVER (PARTITION BY field_updated ORDER BY mois))
                    / NULLIF(LAG(nb_modifications) OVER (PARTITION BY field_updated ORDER BY mois), 0) * 100,
                2
        ) AS evolution_pct
    FROM monthly_field_activity
    ORDER BY mois DESC, month_rank;

-- VIEW EXPORT DATA
CREATE OR REPLACE VIEW vw_export_computer AS
       SELECT
           dv.serial_number AS serial_number,
           dv.model AS model,
           dv.manufacturer_name as manufacturer,
           dv.created_at AS created_at,
           dv.updated_at AS updated_at,
           dv.state AS state,
           c.name AS name,
           c.cpu AS cpu,
           c.ram_mb AS ram_mb,
           c.disk_gb AS disk_gb,
           c.os_name AS os,
           c.domain AS domain,
           c.location AS location,
           c.building AS building,
           c.room AS room,
           c.mac_address AS macaddr,
           c.purchase_date AS purchase_date,
           c.warranty_end AS warranty_end,
           c.type_name AS type_name
       FROM
           computer c,
           devices dv
       WHERE
                c.serial_number = dv.serial_number
            AND dv.device_type LIKE 'Computer'
       ORDER BY
           dv.serial_number;

CREATE OR REPLACE VIEW vw_export_monitor AS
       SELECT
           dv.serial_number AS serial_number,
           dv.model AS model,
           dv.manufacturer_name AS manufacturer,
           dv.created_at AS created_at,
           dv.updated_at AS last_update,
           dv.state AS state,
           m.size_inch AS size_inch,
           m.resolution AS resolution,
           m.connector_name AS connector,
           m.attached_to_computer AS attached_to
       FROM
           monitor m,
           devices dv
       WHERE
                m.serial_number = dv.serial_number
            AND dv.device_type LIKE 'Monitor'
       ORDER BY
           dv.serial_number;

-- VIEW FROM DASHBORD

CREATE OR REPLACE VIEW vw_dashboard_five_devices_last_update AS
SELECT
    dv.serial_number AS serial_number,
    COALESCE(c.name, dv.model, dv.serial_number) AS display_name,
    dv.device_type AS device_type,
    dv.state AS state,
    dv.updated_at AS updated_at
FROM
    devices dv
    LEFT JOIN computer c ON c.serial_number = dv.serial_number
ORDER BY
    updated_at DESC,
    serial_number ASC
    LIMIT 5;

CREATE OR REPLACE VIEW vw_dashboard_five_users_last_connection AS
SELECT
    usr.last_name AS last_name,
    usr.first_name AS first_name,
    usr.role AS role,
    usr.last_login_at AS last_login_at
FROM
    users usr
ORDER BY
    last_login_at DESC,
    first_name ASC
    LIMIT 5;

CREATE OR REPLACE VIEW vw_inventory_search_table AS
SELECT devices.serial_number AS serial_number, name, model, device_type, created_at, updated_at, state
FROM devices
LEFT JOIN computer c 
    ON devices.serial_number = c.serial_number
LEFT JOIN monitor m
    ON devices.serial_number = m.serial_number;


CREATE OR REPLACE VIEW vw_export_inventaire AS
SELECT
        dv.serial_number AS serial_number,
        dv.model AS model,
        dv.manufacturer_name AS manufacturer_name,
        dV.device_type AS device_type,
        dv.created_at AS created_at,
        dv.updated_at AS updated_at,
        dv.state AS state,

        c.name AS name,
        c.location AS location,
        c.building AS building,
        c.room AS room,
        c.cpu AS cpu,
        c.ram_mb AS ram_mb,
        c.disk_gb AS disk_gb,
        c.domain AS domain,
        c.mac_address AS mac_address,
        c.purchase_date AS purchase_date,
        c.warranty_end AS warranty_end,
        c.os_name AS os_name,
        c.type_name AS type_name,

        m.size_inch AS size_inch,
        m.resolution AS resolution,
        m.connector_name AS connector_name,
        m.attached_to_computer AS attached_to_computer
FROM devices dv
LEFT JOIN computer c
    ON dv.serial_number = c.serial_number
LEFT JOIN monitor m
    ON dv.serial_number = m.serial_number;


CREATE OR REPLACE VIEW vw_users_logs AS
    SELECT
        ul.log_date AS log_date,
        ul.login AS login,
        ul.action_did AS action_did,
        INET6_NTOA(ul.ip_address) AS ip_address,
        ul.old_val AS old_val,
        ul.new_val AS new_val
    FROM
        users_logs ul
    ORDER BY
        log_date DESC;

CREATE OR REPLACE VIEW vw_ban_ip AS
    SELECT
        ipb.ban_date AS ban_date,
        INET6_NTOA(ipb.ip_address) AS ip_address,
        ipb.reason AS reason
    FROM
        ip_ban ipb
    ORDER BY
        ban_date DESC;