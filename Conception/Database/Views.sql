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
        -- Classement des champs les plus modifiés par mois
        DENSE_RANK() OVER (PARTITION BY mois ORDER BY nb_modifications DESC) AS month_rank,
            -- Classement global
        RANK() OVER (ORDER BY nb_modifications DESC) AS global_rank,
            -- Percentile dans le mois
        PERCENT_RANK() OVER (PARTITION BY mois ORDER BY nb_modifications DESC) AS percentile_mois,
            -- Évolution vs mois précédent
        nb_modifications - LAG(nb_modifications) OVER (PARTITION BY field_updated ORDER BY mois) AS evolution_vs_last_month,
            -- Évolution en pourcentage
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
           dv.created_at AS created_at,
           dv.updated_at AS last_update,
           dv.state AS state,
           c.name AS name,
           c.manufacturer_name as manufacturer,
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
           c.warranty_end AS warranty_end
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
           dv.created_at AS created_at,
           dv.updated_at AS last_update,
           dv.state AS state,
           m.manufacturer_name AS manufacturer,
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