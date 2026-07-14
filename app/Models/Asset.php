<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Asset extends Model
{
    protected $fillable = [
        'asset_item_id',
        'room_id',
        'pic_id',
        'serial_number',
        'source_fund',
        'acquisition_year',
        'price',
        'condition',
        'status',
        'barcode_token',
        'bast_date',
    ];

    public function itemInfo()
    {
        return $this->belongsTo(AssetItem::class, 'asset_item_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function getQrCode()
    {
        $renderer = new ImageRenderer(
            new RendererStyle(150), // Ukuran 150px
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        // Menghasilkan string SVG
        return $writer->writeString($this->serial_number);
    }
}
