<?php

const GET_SUMMARY_OF_SALES_SERVICES = "
SELECT Sum(( amount * cost ))
FROM   salesproductslist
WHERE  sale_id = ?
       AND type = 'service' ";

const GET_SALES_BY_OPERATOR = "
SELECT id
FROM   saleslist
WHERE  employee_id = ?
       AND status = 'done'
       AND ( action = 'sell'
              OR action = 'sellReturn' ) ";