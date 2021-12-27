<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Author
 *
 * @property int $id
 * @property string $name
 * @property string|null $photo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 * @property-read int|null $books_count
 * @method static \Illuminate\Database\Eloquent\Builder|Author newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Author newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Author query()
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperAuthor
 */
class Author extends Model
{
    use HasFactory;

    protected $table = 'author';

    protected $fillable = [
        "id",
        "name",
        "photo",
    ];
    protected $hidden = [
        'updated_at',
        'created_at',
        'pivot'
    ];

    /**
     * One to Many - Author <-> Books
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany|\App\Models\Book[]
     */
    public function books(){
        return $this->belongsToMany(Book::class, 'book_author');
    }
}
