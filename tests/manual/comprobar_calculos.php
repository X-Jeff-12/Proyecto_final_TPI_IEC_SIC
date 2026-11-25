<?php
require_once __DIR__.'/../../app/Services/FinanzasService.php';
use App\Services\FinanzasService as F;
$checks = [
 'VPN 10%, -100 +110'=>abs(F::vpn(.10,[-100,110]))<1e-8,
 'TIR 10%, -100 +110'=>abs(F::tir([-100,110])-.10)<1e-7,
 'Depreciación LR suma 450'=>abs(array_sum(F::depreciacion('lr',500,50,5))-450)<1e-8,
 'Depreciación SDA suma 450'=>abs(array_sum(F::depreciacion('sda',500,50,5))-450)<1e-8,
 'Depreciación DDB suma 450'=>abs(array_sum(F::depreciacion('ddb',500,50,5))-450)<1e-8,
 'Flujo correcto'=>F::flujoAntes(100,10,3,50,30)===[-100.,20.,20.,30.],
];
foreach($checks as $test=>$ok)echo ($ok?'OK   ':'ERROR').' '.$test.PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
