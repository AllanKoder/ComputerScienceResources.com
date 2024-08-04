<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTag extends Model
{
    use HasFactory;

    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}
