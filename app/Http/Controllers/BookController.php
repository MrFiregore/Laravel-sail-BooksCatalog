<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\DooRequest;
    use App\Models\Author;
    use App\Models\Book;
    use App\Models\Genre;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Http\Request;

    class BookController extends Controller
    {

        /**
         * @param   \App\Http\Requests\DooRequest   $request
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function all(DooRequest $request)
        {
            $books = Book::with($request->all ? ['genres', 'authors'] : []);
            if($request->genres ?? false){
                $books->whereHas("genres",  function (Builder $q) use ($request){
                    $q->whereIn('genre_id', $request->genres);
                });
            }
            return $this->sendResponse(['data' => ['books' => $books->get()->all()]]);
        }

        /**
         * @param   \App\Http\Requests\DooRequest   $request
         * @param   \App\Models\Book                $book
         *
         * @return void
         */
        public function single(DooRequest $request, Book $book)
        {
            return $this->sendResponse(['data' => ['book' => Book::with($request->all ? ['genres', 'authors'] : [])->find($book->id)->toArray()]]);
        }

        /**
         * @param   \App\Http\Requests\DooRequest   $request
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function create(Request $request)
        {
            $validator = $this->queryValidator(
                $request,
                [
                    'name'        => 'required|string',
                    'genres'      => 'required|exists:App\Models\Genre,id',
                    'authors'      => 'required|exists:App\Models\Author,id',
                    'edition'     => 'required|string',
                    'photo'       => 'required|image',
                    'description' => 'required|string',
                ]
            );
            /**
             * @var \Illuminate\Http\UploadedFile $photo
             */
            $photo     = $validator["photo"];
            $photo_url = \Storage::disk("public_images")->url($photo->store('book', ['disk' => 'public_images']));
            $book = new Book();
            $book->name = $validator['name'];
            $book->edition = $validator['edition'];
            $book->description = $validator['description'];
            $book->photo = $photo_url;
            $book->save();

            $book->authors()->attach($validator['authors']);
            $book->genres()->attach($validator['genres']);
            return $this->sendResponse(['data' => ['book' => Book::with(['genres', 'authors'])->find($book->id)->toArray()]]);
        }

        /**
         * @param   \Illuminate\Http\Request   $request
         * @param   \App\Models\Book           $book
         *
         * @return void
         */
        public function update(Request $request, Book $book)
        {
            $validator = $this->queryValidator(
                $request,
                [
                    'name'        => 'string',
                    'genres'      => 'exists:App\Models\Genre,id',
                    'authors'      => 'exists:App\Models\Author,id',
                    'edition'     => 'string',
                    'photo'       => 'image',
                    'description' => 'string',
                ]
            );

            if (isset($validator['name'])) {
                $book->name = $validator['name'];
            }

            if (isset($validator['genres'])){
                $genres = $book->genres();
                $genres->sync([]);
                $genres->attach($validator['genres']);
            }

            if (isset($validator['photo'])) {
                /**
                 * @var \Illuminate\Http\UploadedFile $photo
                 */
                $photo     = $validator["photo"];
                $photo_url = \Storage::disk("public_images")->url($photo->store('book', ['disk' => 'public_images']));
                $book->photo = $photo_url;
            }

            if (isset($validator['edition'])) {
                $book->edition = $validator['edition'];
            }
            if (isset($validator['authors'])) {
                $authors = $book->authors();
                $authors->sync([]);
                $authors->attach($validator['authors']);
            }

            if (isset($validator['description'])) {
                $book->description = $validator['description'];
            }

            if (isset($validator['name'])) {
                $book->name = $validator['name'];
            }

            $book->save();
            return $this->sendResponse(['data' => ['book' => Book::with(['genres', 'authors'])->find($book->id)->toArray()]]);
        }

        public function delete(DooRequest $request, Book $book){
            $book_name = $book->name;
            $book_id = $book->id;

            return $book->delete() ? $this->sendResponse(['msg' => "Book '$book_name' ($book_id) removed"]) : $this->sendErrorResponse(['msg' => "Book '$book_name' ($book_id) can't be removed"]);
        }
    }
