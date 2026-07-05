<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $category
 * @property int $price_per_month
 * @property int $total_stock
 * @property int $available_stock
 * @property int $rented_stock
 * @property int $maintenance_stock
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Maintenance> $maintenances
 * @property-read int|null $maintenances_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAvailableStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMaintenanceStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePricePerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRentedStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTotalStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
    'name',
    'description',
    'category',
    'price_per_month',
    'total_stock',
    'available_stock',
    'rented_stock',
    'maintenance_stock',
    'image'
];

public function rent($qty)
{
    $qty = (int) $qty;

    if ($qty <= 0) {
        return false;
    }

    if ($qty > $this->available_stock) {
        return false;
    }

    $this->available_stock -= $qty;
    $this->rented_stock += $qty;
    $this->save();

    return true;
}

public function maintenance($qty)
{
    $qty = (int) $qty;

    if ($qty <= 0) {
        return false;
    }

    if ($qty > $this->available_stock) {
        return false;
    }

    $this->available_stock -= $qty;
    $this->maintenance_stock += $qty;
    $this->save();

    return true;
}

public function returnStock($qty)
{
    $qty = (int) $qty;

    if ($qty <= 0) {
        return false;
    }

    $qty = min($qty, $this->rented_stock);

    $this->available_stock += $qty;
    $this->rented_stock -= $qty;
    $this->save();

    return true;
}

public function maintenances()
{
    return $this->hasMany(Maintenance::class);
}
public function tambahStok($jumlah)
{
    $this->increment('available_stock', $jumlah);
}

public function kurangiStokDisewa($jumlah)
{
    $jumlah = min((int) $jumlah, $this->rented_stock);

    if ($jumlah > 0) {
        $this->decrement('rented_stock', $jumlah);
    }
}
}
