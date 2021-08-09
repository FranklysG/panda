CREATE OR REPLACE VIEW `view_inventory` AS
SELECT min(inv.id) as id,
inv.system_user_id,
inv.product_id,
prod.sku as product_sku,
prod.`name` as product_name,
prod.image as product_image,
sum(inv.amount) as amount,
avg(inv.price) as price,
max(inv.final_price) as final_price,
inv.created_at,
inv.updated_at
FROM inventory as inv
INNER JOIN product as prod on prod.id = inv.product_id
WHERE inv.amount > 0
GROUP BY inv.product_id