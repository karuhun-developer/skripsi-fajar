<?php

function numberToCurrency($value) {
    return number_format($value, 0, ',', '.');
}
function currencyToNumber($value) {
    return str_replace('.', '', $value);
}
?>
