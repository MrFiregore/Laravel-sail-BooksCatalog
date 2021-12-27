<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Book
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $photo
 * @property string|null $edition
 * @property string|null $description
 * @property int|null $author_id
 * @property-read \App\Models\Author|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Genre[] $genres
 * @property-read int|null $genres_count
 * @method static \Illuminate\Database\Eloquent\Builder|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book query()
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereEdition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperBook
 */
class Book extends Model
{
    use HasFactory;

    protected $table = 'book';

    protected $hidden = [
        'updated_at',
        'created_at',
        'author_id',
        'pivot'
    ];

    protected $fillable = [
        "id",
        "name",
        "photo",
        "edition",
        "description",
        "author",
        "genres",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Author[]
     */
    public function authors(){
        return $this->belongsToMany(Author::class, 'book_author');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|\App\Models\Genre[]
     */
    public function genres(){
        return $this->belongsToMany(Genre::class, 'genre_book');
    }
}
