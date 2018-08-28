<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AddressModel extends Model
{
    protected $table = 'wx_address';

    protected $dateFormat = "Y-m-d H:i:s";

}