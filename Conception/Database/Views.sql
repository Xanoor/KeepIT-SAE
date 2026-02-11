-- View

USE keepit;

-- MONITOR LINKED AT A COMPUTER
CREATE OR REPLACE VIEW vw_monitor_by_computer AS
    SELECT
        m.serial_number AS serial_monitor,
        m.connector_name AS connector,
        c.serial_number AS serial_computer,
        c.type_name AS computer_type
    FROM
        monitor m,
        computer c
    WHERE
        m.attached_to_serial = c.serial_number;

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