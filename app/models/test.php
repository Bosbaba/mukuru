<?php
$rand = 100;
$rate = 10;
$surcharge = 5;
$surcharge_rate = ((100 * $rate) - ($rate * $surcharge)) / 100;

echo "SRate = $surcharge_rate \n";

$dollar = $rand * $rate;
echo "Dollar = $dollar \n";
$dollar2 = $rand * $surcharge_rate;
echo "Dollar2 = $dollar2 \n";

$perc = ($dollar-$dollar2)/$rate;
echo $perc;
