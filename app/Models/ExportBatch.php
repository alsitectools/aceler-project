<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ExportBatch extends Model
{
    protected $table = 'export_batches';
    public $timestamps = false;
    protected $fillable = ['file_name','created_by','created_at','rejected_at','file_content','file_hash'];

    // La copia del .fil (comprimida) no se serializa nunca a JSON
    protected $hidden = ['file_content'];

    /** Columnas del lote sin la copia del archivo, para listados */
    public const LIST_COLUMNS = ['id', 'file_name', 'created_by', 'created_at', 'rejected_at', 'file_hash'];

    /** Guarda la copia comprimida del .fil y su huella */
    public static function packFile(string $content): array
    {
        return [
            'file_content' => gzencode($content, 9),
            'file_hash' => hash('sha256', $content),
        ];
    }

    /**
     * Devuelve el .fil original, o null si el lote no tiene copia o no coincide con su huella.
     */
    public function unpackFile(): ?string
    {
        $packed = $this->getRawOriginal('file_content');
        if ($packed === null || $packed === '') {
            return null;
        }

        $content = @gzdecode($packed);
        if ($content === false || ($this->file_hash && !hash_equals($this->file_hash, hash('sha256', $content)))) {
            return null;
        }

        return $content;
    }
}
