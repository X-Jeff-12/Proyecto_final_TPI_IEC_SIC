<?php
namespace Tests\Unit;
use PHPUnit\Framework\TestCase;
use App\Services\FinanzasService as F;
class FinanzasServiceTest extends TestCase {
 public function test_valor_presente():void{$this->assertEqualsWithDelta(0,F::vpn(.10,[-100,110]),1e-8);}
 public function test_tir_simple():void{$this->assertEqualsWithDelta(.10,F::tir([-100,110]),1e-7);}
 public function test_depreciacion_linea_recta():void{$this->assertEquals([20.,20.,20.,20.,20.],F::depreciacion('lr',100,0,5));}
 public function test_depreciacion_de_tres_metodos_llega_al_salvamento():void{
  foreach(['lr','sda','ddb'] as $metodo){$d=F::depreciacion($metodo,500,50,5);$this->assertEqualsWithDelta(450,array_sum($d),1e-6);}
 }
 public function test_flujo_antes_impuesto():void{$this->assertEquals([-100.,20.,20.,30.],F::flujoAntes(100,10,3,50,30));}
 public function test_equivalencia_anual():void{$this->assertEqualsWithDelta(F::vpn(.1,[-100,50,50])*F::ap(.1,2),F::caue(.1,[-100,50,50]),1e-7);}
}
