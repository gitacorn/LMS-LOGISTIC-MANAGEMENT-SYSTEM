-- Update Goods Out Statistics Procedure - Add Status Parameter
-- Run this SQL in your MySQL database to add status filtering

DELIMITER //

DROP PROCEDURE IF EXISTS get_goods_out_statistics_data//

CREATE PROCEDURE get_goods_out_statistics_data(IN status_filter VARCHAR(50))
BEGIN
    DECLARE delivered_status_id INT DEFAULT 5;
    DECLARE cancelled_status_id INT DEFAULT 6;
    
    SELECT  
        SUM(ctped.v_units) AS Unit_Count,
        SUM(CASE WHEN ctped.e_dimension = 'Pallet' THEN ctped.i_no_of_pallet_box ELSE 0 END) AS `Total Pallets`,
        SUM(CASE WHEN ctped.e_dimension = 'Box'    THEN ctped.i_no_of_pallet_box ELSE 0 END) AS `Total Boxes`,
        SUM(ctped.v_price) AS ShipmentValue,
        SUM(ctped.v_price) AS 'Total Transaction'
    FROM country_to_port_europe_goods_out_master ctpem
    LEFT JOIN country_to_port_europe_goods_out_detail ctped 
        ON ctped.i_country_to_port_europe_goods_master_id = ctpem.i_id 
        AND ctped.t_is_deleted = 0
    WHERE 
        ctpem.t_is_deleted = 0
        AND (
            -- When status_filter is 'In Transit', exclude delivered and cancelled
            (status_filter = 'In Transit' AND ctpem.i_status_id NOT IN (delivered_status_id, cancelled_status_id))
            OR
            -- When status_filter is 'Delivered', include only delivered records
            (status_filter = 'Delivered' AND ctpem.i_status_id = delivered_status_id)
            OR
            -- When status_filter is NULL or empty, use default behavior (exclude delivered and cancelled)
            (status_filter IS NULL OR status_filter = '' OR status_filter = 'All' AND ctpem.i_status_id NOT IN (delivered_status_id, cancelled_status_id))
        );
END//

DELIMITER ;
