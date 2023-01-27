<?php

function formatPrice(float $vlprice){
    if(!$vlprice > 0) $vlprice = 0;
    return number_format($vlprice,2,",",".");
}