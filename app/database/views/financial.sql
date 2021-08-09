CREATE OR REPLACE VIEW `view_financial` AS
SELECT id
,(SELECT count(id) FROM sale WHERE DATE(sale.created_at) = DATE(NOW()) AND sale.system_user_id = syu.id) AS sale_today
,(SELECT sum((sale.price-sale.discount)*sale.quantity) FROM sale WHERE  DATE(sale.created_at) = DATE(NOW()) AND sale.system_user_id = syu.id) as sale_cash_today
-- ,(SELECT sum(mp.valor_quarto + mp.valor_consumo) FROM mapa_reserva AS mp WHERE DATE(mp.created_at) BETWEEN date_add(DATE(NOW()),interval -1 week) and DATE(NOW())) AS est_entrada_semanal_quarto, 
,(SELECT sum((sale.price-sale.discount)*sale.quantity) FROM sale WHERE DATE(sale.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW()) AND sale.system_user_id = syu.id) AS sale_cash_month
,(SELECT sum((sale.price-sale.discount)*sale.quantity) FROM sale WHERE DATE(sale.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-01-01") and DATE_FORMAT(NOW(), "%Y-12-31") AND sale.system_user_id = syu.id) AS sale_cash_year
,(SELECT sum(payable.price) FROM payable WHERE DATE(payable.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW()) AND payable.status = 0 AND payable.system_user_id = syu.id) as payable_cash_month
,(SELECT sum(payable.price) FROM payable WHERE DATE(payable.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-01-01") and DATE_FORMAT(NOW(), "%Y-12-31") AND payable.status = 0 AND payable.system_user_id = syu.id) as payable_cash_year
,(SELECT sum(employee.salary) FROM employee WHERE DATE(employee.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW()) AND employee.system_user_id = syu.id) as employee_salary
-- ,(SELECT sum(sd.valor_saida) FROM saida AS sd WHERE DATE(sd.created_at) BETWEEN date_add(DATE(NOW()),interval -1 week) and DATE(NOW())) AS est_saida_semanal, 
-- ,(SELECT sum(sd.valor_saida) FROM saida AS sd WHERE DATE(sd.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW())) AS est_saida_mensal,
-- ,(SELECT sum(sd.valor_saida) FROM saida AS sd WHERE DATE(sd.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-01-01") and DATE(NOW())) AS est_saida_anual,
-- ,(SELECT sum(en.qtd_nota * en.valor_venda_uni) FROM entrada AS en WHERE DATE(en.created_at) BETWEEN date_add(DATE(NOW()),interval -1 week) and DATE(NOW())) AS est_entrada_semanal,
-- ,(SELECT sum(en.qtd_nota * en.valor_venda_uni) FROM entrada AS en WHERE DATE(en.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW())) AS est_entrada_mensal,
-- ,(SELECT sum(en.qtd_nota * en.valor_venda_uni) FROM entrada AS en WHERE DATE(en.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-01-01") and DATE(NOW())) AS est_entrada_anual,
-- ,(SELECT sum(ven.qtd_venda * ven.valor_venda) FROM venda AS ven WHERE DATE(ven.created_at) BETWEEN date_add(DATE(NOW()),interval -1 week) and DATE(NOW())) AS venda_externa_semanal,
-- ,(SELECT sum(ven.qtd_venda * ven.valor_venda) FROM venda AS ven WHERE DATE(ven.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-%m-01") and DATE(NOW())) AS venda_externa_mensal,
-- ,(SELECT sum(ven.qtd_venda * ven.valor_venda) FROM venda AS ven WHERE DATE(ven.created_at) BETWEEN DATE_FORMAT(NOW(), "%Y-01-01") and DATE(NOW())) AS venda_externa_anual
 FROM system_user as syu
 WHERE id = 1