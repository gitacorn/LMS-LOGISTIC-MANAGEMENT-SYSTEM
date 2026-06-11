-- Stored Procedure for Goods Out Statistics
DROP PROCEDURE IF EXISTS get_goods_out_statistics_data;

CREATE PROCEDURE get_goods_out_statistics_data()
BEGIN
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
        AND ctpem.i_status_id NOT IN (5, 6);
END;
