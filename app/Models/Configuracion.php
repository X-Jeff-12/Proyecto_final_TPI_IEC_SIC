<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Configuracion extends Model {
 protected $table='config';
 protected $primaryKey='k'; public $incrementing=false; protected $keyType='string';
 public $timestamps=false;
 protected $guarded=[];
}
