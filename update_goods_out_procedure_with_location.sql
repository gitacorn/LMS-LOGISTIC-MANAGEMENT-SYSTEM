DELIMITER //

DROP PROCEDURE IF EXISTS get_goods_out_statistics_data//

CREATE PROCEDURE get_goods_out_statistics_data(
    IN p_location    VARCHAR(10),
    IN p_status      VARCHAR(50),
    IN p_from_date   DATE,
    IN p_to_date     DATE
)
BEGIN

    IF p_location = 'USA' THEN
        SELECT
            COALESCE(SUM(d.v_units), 0) AS Unit_Count,
            COALESCE(SUM(CASE WHEN m.e_box_pallet_type='Pallet' THEN m.i_total_no_of_pallets ELSE 0 END),0) AS `Total Pallets`,
            COALESCE(SUM(CASE WHEN m.e_box_pallet_type='Box'    THEN m.i_total_no_of_pallets ELSE 0 END),0) AS `Total Boxes`,
            COALESCE(SUM(d.d_price), 0) AS ShipmentValue,
            COALESCE(SUM(d.d_price), 0) AS `Total Transaction`
        FROM us_warehouse_to_amazon_master m
        LEFT JOIN us_warehouse_to_amazon_details d ON d.i_us_warehouse_to_amazon_master_id=m.i_id AND d.t_is_deleted=0
        WHERE m.t_is_deleted=0
          AND (
              (p_status='In Transit' AND m.i_status_id NOT IN(5,6))
              OR (p_status='Delivered' AND m.i_status_id IN(5,6))
              OR (p_status IS NULL OR p_status='' OR p_status NOT IN('In Transit','Delivered')) AND m.i_status_id NOT IN(5,6)
          )
          AND (
              p_from_date IS NULL
              OR (m.dt_delivery_date IS NOT NULL AND m.dt_delivery_date >= p_from_date)
              OR (m.dt_delivery_date IS NULL AND DATE(m.dt_created_at) >= p_from_date)
          )
          AND (
              p_to_date IS NULL
              OR (m.dt_delivery_date IS NOT NULL AND m.dt_delivery_date <= p_to_date)
              OR (m.dt_delivery_date IS NULL AND DATE(m.dt_created_at) <= p_to_date)
          );
    ELSE
        SELECT
            COALESCE(SUM(d.v_units), 0) AS Unit_Count,
            COALESCE(SUM(CASE WHEN d.e_dimension='Pallet' THEN d.i_no_of_pallet_box ELSE 0 END),0) AS `Total Pallets`,
            COALESCE(SUM(CASE WHEN d.e_dimension='Box'    THEN d.i_no_of_pallet_box ELSE 0 END),0) AS `Total Boxes`,
            COALESCE(SUM(d.v_price), 0) AS ShipmentValue,
            COALESCE(SUM(d.v_price), 0) AS `Total Transaction`
        FROM country_to_port_europe_goods_out_master m
        LEFT JOIN country_to_port_europe_goods_out_detail d ON d.i_country_to_port_europe_goods_master_id=m.i_id AND d.t_is_deleted=0
        WHERE m.t_is_deleted=0
          AND (
              (p_status='In Transit' AND m.i_status_id NOT IN(5,6))
              OR (p_status='Delivered' AND m.i_status_id IN(5,6))
              OR (p_status IS NULL OR p_status='' OR p_status NOT IN('In Transit','Delivered')) AND m.i_status_id NOT IN(5,6)
          )
          AND (
              p_from_date IS NULL
              OR (m.dt_delivery_date IS NOT NULL AND m.dt_delivery_date >= p_from_date)
              OR (m.dt_delivery_date IS NULL AND DATE(m.dt_created_at) >= p_from_date)
          )
          AND (
              p_to_date IS NULL
              OR (m.dt_delivery_date IS NOT NULL AND m.dt_delivery_date <= p_to_date)
              OR (m.dt_delivery_date IS NULL AND DATE(m.dt_created_at) <= p_to_date)
          );
    END IF;

END//

DELIMITER ;
