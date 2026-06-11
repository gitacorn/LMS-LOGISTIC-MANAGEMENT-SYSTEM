-- Test the stored procedure with different parameters to debug the issue

-- Test 1: Check if procedure exists and its parameters
SHOW CREATE PROCEDURE get_goods_out_statistics_data;

-- Test 2: Call with In Transit (should work)
CALL get_goods_out_statistics_data('In Transit', NULL, NULL);

-- Test 3: Call with Delivered (might have issues)
CALL get_goods_out_statistics_data('Delivered', NULL, NULL);

-- Test 4: Check what status IDs exist in the data
SELECT DISTINCT i_status_id, COUNT(*) as count
FROM country_to_port_europe_goods_out_master 
WHERE t_is_deleted = 0
GROUP BY i_status_id;

-- Test 5: Check if there are any delivered records
SELECT COUNT(*) as delivered_count
FROM country_to_port_europe_goods_out_master 
WHERE t_is_deleted = 0 AND i_status_id IN (5, 6);

-- Test 6: Check if there are any in-transit records  
SELECT COUNT(*) as in_transit_count
FROM country_to_port_europe_goods_out_master 
WHERE t_is_deleted = 0 AND i_status_id NOT IN (5, 6);
