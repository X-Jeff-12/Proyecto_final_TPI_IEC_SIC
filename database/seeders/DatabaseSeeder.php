<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder {
 public function run(): void {
   $data=json_decode(file_get_contents(database_path('seeders/data/origen_sqlite.json')),true,512,JSON_THROW_ON_ERROR);
   // Semillas no destructivas: nunca sobrescribe modificaciones del usuario.
   DB::transaction(function()use($data){
     foreach($data as $table=>$rows){
       if (DB::table($table)->exists()) { $this->command?->warn("Saltando {$table}: ya contiene datos"); continue; }
       foreach(array_chunk($rows,100) as $batch){if(count($batch)) DB::table($table)->insert($batch);}
       if(DB::getDriverName()==='pgsql' && count($rows) && array_key_exists('id',$rows[0])){
         DB::statement("SELECT setval(pg_get_serial_sequence('{$table}','id'), (SELECT COALESCE(MAX(id), 1) FROM \"{$table}\"), true)");
       }
     }
   });
   $this->command?->info('Datos originales importados. ¡Ojo! Muchos son ESTIMADOS o SUPUESTOS.');
 }
}
