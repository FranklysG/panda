-- CREATE OR REPLACE VIEW `view_financial` AS
SELECT syu.id,
syu.system_unit_id
,(SELECT count(id) FROM sale WHERE DATE(sale.created_at) = DATE(NOW()) AND sale.system_user_id = syu.system_unit_id) AS sale_today
,(SELECT sum(((sale_inventory.price)-(sale_inventory.discount))*sale_inventory.amount) FROM sale_inventory WHERE DATE(sale_inventory.created_at) = DATE(NOW()) AND sale_inventory.system_user_id = syu.id) as sale_cash_today
,(SELECT sum(((sale_inventory.price)-(sale_inventory.discount))*sale_inventory.amount) FROM sale_inventory WHERE DATE(sale_inventory.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW()) AND sale_inventory.system_user_id = syu.id) AS sale_cash_month
,(SELECT sum(((sale_inventory.price)-(sale_inventory.discount))*sale_inventory.amount) FROM sale_inventory WHERE DATE(sale_inventory.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-01-01") and DATE_FORMAT(NOW(), "%Y-12-31") AND sale_inventory.system_user_id = syu.id) AS sale_cash_year
,(SELECT sum(payable.price) FROM payable WHERE DATE(payable.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW()) AND payable.status = 0 AND payable.system_user_id = syu.id) as payable_cash_month
,(SELECT sum(payable.price) FROM payable WHERE DATE(payable.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-01-01") and DATE_FORMAT(NOW(), "%Y-12-31") AND payable.status = 0 AND payable.system_user_id = syu.id) as payable_cash_year
,(SELECT sum(exes.price) FROM exes WHERE DATE(exes.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW()) AND exes.system_user_id = syu.id) as exes_cash_month 
,(SELECT sum(exes.price) FROM exes WHERE DATE(exes.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-01-01") and DATE_FORMAT(NOW(), "%Y-12-31") AND exes.system_user_id = syu.id) as exes_cash_year
,(SELECT sum(employee.salary) FROM employee WHERE DATE(employee.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW()) AND employee.system_user_id = syu.id) as employee_salary
FROM system_user as syu
 -- Where syu.id = 3