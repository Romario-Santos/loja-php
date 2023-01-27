SELECT *FROM tb_products WHERE idproduct IN(
SELECT a.idproduct
FROM tb_products a
INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
where b.idcategory = 3
);


SELECT *FROM tb_products WHERE idproduct NOT IN(
SELECT a.idproduct
FROM tb_products a
INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
where b.idcategory = 3
);