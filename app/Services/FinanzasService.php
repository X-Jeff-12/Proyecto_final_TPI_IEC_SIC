<?php
namespace App\Services;

/** Adaptación a PHP del motor finanzas.py del proyecto original.
 * Convención: CAUE positivo = beneficio anual equivalente, no costo positivo.
 * Advertencia: el modelo tributario preserva el SUPUESTO original de ahorro fiscal ante pérdidas.
 */
class FinanzasService
{
    public static function pf(float $i, int $n): float { return 1 / pow(1+$i, $n); }
    public static function pa(float $i, int $n): float { return abs($i)<1e-12 ? $n : (1-pow(1+$i,-$n))/$i; }
    public static function ap(float $i, int $n): float { return abs($i)<1e-12 ? 1/$n : $i*pow(1+$i,$n)/(pow(1+$i,$n)-1); }
    public static function af(float $i, int $n): float { return abs($i)<1e-12 ? 1/$n : $i/(pow(1+$i,$n)-1); }
    public static function fp(float $i, int $n): float { return pow(1+$i,$n); }
    public static function fa(float $i, int $n): float { return abs($i)<1e-12 ? $n : (pow(1+$i,$n)-1)/$i; }
    public static function vpn(float $i, array $flujos): float { $s=0; foreach($flujos as $t=>$f){$s+=(float)$f/pow(1+$i,$t);}return $s; }
    public static function tir(array $flujos): ?float {
        if(!count($flujos)||min($flujos)>=0||max($flujos)<=0)return null;
        $lo=-0.9;$hi=10.0; $fl=self::vpn($lo,$flujos);$fh=self::vpn($hi,$flujos);
        if($fl*$fh>0)return null;
        for($k=0;$k<300;$k++){
            $m=($lo+$hi)/2; $fm=self::vpn($m,$flujos);
            if(abs($fm)<1e-9)return $m;
            if($fl*$fm<0){$hi=$m;}else{$lo=$m;$fl=$fm;}
        }
        return ($lo+$hi)/2;
    }
    public static function caue(float $i,array $f): float {return self::vpn($i,$f)*self::ap($i,count($f)-1);}
    public static function bc(float $i,array $b,array $c): ?float {
        $pb=self::vpn($i,$b);$pc=self::vpn($i,$c);
        return abs($pc)<1e-12?null:$pb/$pc;
    }
    public static function depreciacion(string $metodo,float $c,float $s,int $n): array {
        if($n<1||$s>$c||$c<0||$s<0)throw new \InvalidArgumentException('Costo, salvamento o vida útil inválidos');
        $out=[];
        if($metodo==='lr')return array_fill(0,$n,($c-$s)/$n);
        if($metodo==='sda'){
            $total=$n*($n+1)/2;
            for($t=0;$t<$n;$t++)$out[]=($c-$s)*($n-$t)/$total;
            return $out;
        }
        $libro=$c;
        for($t=0;$t<$n;$t++){
            $d=max(min($libro*2/$n,$libro-$s),($libro-$s)/($n-$t));
            $out[]=$d;$libro-=$d;
        }
        return $out;
    }
    public static function cuadro(float $c,array $dep): array {
        $l=$c;$a=0;$r=[];
        foreach($dep as $t=>$d){$a+=$d;$l-=$d;$r[]=['anio'=>$t+1,'dep'=>$d,'acum'=>$a,'libro'=>$l];}
        return $r;
    }
    public static function flujoAntes(float $p,float $s,int $n,float $ing,float $costo): array {
        $f=array_merge([-$p],array_fill(0,$n,$ing-$costo));$f[$n]+=$s;return $f;
    }
    public static function flujoDespues(float $p,float $s,int $n,float $ing,float $costo,array $deps,float $t): array {
        $flujo=[-$p];$filas=[];$libro=$p;
        for($k=1;$k<=$n;$k++){
            $d=$deps[$k-1];$libro-=$d;$uai=$ing-$costo-$d;$imp=$uai*$t;$fne=$ing-$costo-$imp;
            $extra=$k===$n?$s-($s-$libro)*$t:0;
            $filas[]=['anio'=>$k,'ingreso'=>$ing,'costo'=>$costo,'dep'=>$d,'uai'=>$uai,'imp'=>$imp,'fne'=>$fne,'salv'=>$extra];
            $flujo[]=$fne+$extra;
        }
        return [$flujo,$filas];
    }
    public static function vidaEconomica(float $p,int $n,float $i,float $costos,float $caida): array {
        $filas=[];$min=INF;$mejor=null;
        for($k=1;$k<=$n;$k++){
            $vm=$p*pow(1-$caida,$k);$caue=$p*self::ap($i,$k)-$vm*self::af($i,$k)+$costos;
            $filas[]=['anio'=>$k,'valor_mercado'=>$vm,'caue'=>$caue];
            if($caue<$min){$min=$caue;$mejor=$k;}
        }
        return [$filas,$mejor];
    }
    public static function evaluar(array $alt,array $serv,array $cfg): array {
        $i=(float)($cfg['tmar']??12)/100;$t=(float)($cfg['isr']??30)/100;
        $p=(float)$alt['costo_inicial'];$s=(float)$alt['valor_salvamento'];$n=(int)$alt['vida_util'];
        if($n<1||$p<0||$s<0||$s>$p||$i<=-1)throw new \InvalidArgumentException('Alternativa inválida');
        $precio=(float)$serv['precio'];$cv=(float)$serv['md']+(float)$serv['mod'];
        $lav=(float)$alt['lavados_mes']*12;$ahorro=(float)($alt['ahorro_anual']??0);
        $ing=$lav*$precio+$ahorro;
        $costo=$lav*$cv+(float)$alt['energia_anual']+(float)$alt['mantenimiento_anual'];
        $deps=[];
        foreach(['lr'=>'Línea recta','sda'=>'Suma de dígitos','ddb'=>'Saldo decreciente doble'] as $clave=>$nombre){
            $d=self::depreciacion($clave,$p,$s,$n);
            $deps[$clave]=['nombre'=>$nombre,'dep'=>$d,'cuadro'=>self::cuadro($p,$d)];
        }
        $metodo=$cfg['metodo_dep']??'lr';if(!isset($deps[$metodo]))$metodo='lr';
        $ai=self::flujoAntes($p,$s,$n,$ing,$costo);
        [$di,$filas]=self::flujoDespues($p,$s,$n,$ing,$costo,$deps[$metodo]['dep'],$t);
        $benAI=array_merge([0],array_fill(0,$n,$ing));$benAI[$n]+=$s;
        $cosAI=array_merge([$p],array_fill(0,$n,$costo));
        $benDI=array_merge([0],array_fill(0,$n,$ing));$benDI[$n]+=$filas[$n-1]['salv'];
        $cosDI=array_merge([$p],array_map(fn($f)=>$costo+$f['imp'],$filas));
        [$ve,$veAnio]=self::vidaEconomica($p,$n,$i,$costo,(float)($alt['dep_mercado']??0.15));
        $metDep=$deps[$metodo]['dep'];
        // Punto de equilibrio: pendiente incremental del VPN por un lavado adicional al mes.
        $obtener=function(float $num)use($p,$s,$n,$precio,$cv,$ahorro,$alt,$metDep,$t,$i){
            $ing=$num*12*$precio+$ahorro;$c=$num*12*$cv+(float)$alt['energia_anual']+(float)$alt['mantenimiento_anual'];
            [$fl,]=self::flujoDespues($p,$s,$n,$ing,$c,$metDep,$t);
            return self::vpn($i,$fl);
        };
        $v0=$obtener(0);$pend=$obtener(1)-$v0;
        return ['alt'=>$alt,'serv'=>$serv,'i'=>$i,'t'=>$t,'n'=>$n,'ahorro'=>$ahorro,
            'equilibrio_lavados'=>$pend<=0?null:max(0,-$v0/$pend),
            'ingreso'=>$ing,'costo'=>$costo,'cv_unit'=>$cv,'lavados_anio'=>$lav,
            'deps'=>$deps,'metodo'=>$metodo,'flujo_ai'=>$ai,'flujo_di'=>$di,'filas_di'=>$filas,
            'ai'=>['vpn'=>self::vpn($i,$ai),'tir'=>self::tir($ai),'caue'=>self::caue($i,$ai),'bc'=>self::bc($i,$benAI,$cosAI)],
            'di'=>['vpn'=>self::vpn($i,$di),'tir'=>self::tir($di),'caue'=>self::caue($i,$di),'bc'=>self::bc($i,$benDI,$cosDI)],
            'vida_eco'=>$ve,'vida_eco_anio'=>$veAnio];
    }
    private static function gcd(int $a,int $b): int {while($b!==0){[$a,$b]=[$b,$a%$b];}return $a;}
    public static function comparar(array $evals): ?array {
        if(!count($evals))return null;
        $h=1;
        foreach($evals as $e){$n=$e['n'];$h=(int)($h*$n/self::gcd($h,$n));}
        // Prevent a huge horizon from unexpected input.
        if($h>300)throw new \InvalidArgumentException('El horizonte común supera 300 años; reduzca las vidas útiles');
        foreach($evals as &$e){
            foreach(['ai','di'] as $tipo){
                $ciclo=$e['flujo_'.$tipo];$n=$e['n'];$f=array_fill(0,$h+1,0.0);
                for($k=0;$k<$h/$n;$k++)for($j=0;$j<=$n;$j++)$f[$k*$n+$j]+=$ciclo[$j];
                $e['vpn_hc_'.$tipo]=self::vpn($e['i'],$f);
            }
        }unset($e);
        usort($evals,fn($a,$b)=>$b['vpn_hc_di']<=>$a['vpn_hc_di']);
        $mejorVPN=$evals[0];$mejorCAUE=$evals;
        usort($mejorCAUE,fn($a,$b)=>$b['di']['caue']<=>$a['di']['caue']);
        return ['horizonte'=>$h,'mejor_vpn'=>$mejorVPN,'mejor_caue'=>$mejorCAUE[0],
            'viables'=>array_values(array_filter($evals,fn($e)=>$e['vpn_hc_di']>0)),
            'ganador'=>$mejorVPN['vpn_hc_di']>0?$mejorVPN:null,'evals'=>$evals];
    }
}
