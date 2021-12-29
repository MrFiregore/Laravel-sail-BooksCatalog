<?php

    namespace App\Http\Controllers;

    use App\Models\Genre;
    use App\Http\Requests\DooRequest;
    use Illuminate\Http\Request;

    class GenreController extends Controller
    {
        /**
         * @param   \App\Http\Requests\DooRequest   $request
         *
         * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
         */
        public function all(DooRequest $request)
        {
            $books = Genre::with($request->all ? ['books', 'books.authors'] : [])->get()->all();
            return $this->sendResponse(['data' => ['genres' => $books]]);
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
                    'name'  => 'required|string',
                    'photo' => 'required|image',
                ]
            );
            /**
             * @var \Illuminate\Http\UploadedFile $photo
             */
            $photo     = $validator["photo"];
            $photo_url = \Storage::disk("public_images")->url($photo->store('genre', ['disk' => 'public_images']));
            $genre    = Genre::create([
                                            'name'  => $validator['name'],
                                            'photo' => $photo_url,
                                        ]);
            $genre->save();
            return $this->sendResponse(['data' => ['genre' => Genre::with(['books', 'books.authors'])->find($genre->id)->toArray()]]);
        }

        /**
         * @param   \App\Http\Requests\DooRequest   $request
         * @param   \App\Models\Genre              $genre
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function single(DooRequest $request, Genre $genre)
        {
            return $this->sendResponse(['data' => ['genre' => Genre::with($request->all ? ['books', 'books.authors'] : [])->find($genre->id)->toArray()]]);
        }

        /**
         * @param   \Illuminate\Http\Request   $request
         * @param   \App\Models\Genre         $genre
         *
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function update(Request $request, Genre $genre)
        {
            $validator = $this->queryValidator(
                $request,
                [
                    'name'  => 'string',
                    'photo' => 'image',
                ]
            );

            if (isset($validator['name'])) {
                $genre->name = $validator['name'];
            }


            if (isset($validator['photo'])) {
                /**
                 * @var \Illuminate\Http\UploadedFile $photo
                 */
                $photo         = $validator["photo"];
                $photo_url     = \Storage::disk("public_images")->url($photo->store('genre', ['disk' => 'public_images']));
                $genre->photo = $photo_url;
            }


            $genre->save();
            return $this->sendResponse(['data' => ['genre' => Genre::with($request->all ? ['books', 'books.authors'] : [])->find($genre->id)->toArray()]]);
        }

        public function delete(DooRequest $request, Genre $genre)
        {
            $genre_id   = $genre->id;
            $genre_name = $genre->name;

            return $genre->delete()
                ? $this->sendResponse(['msg' => "Genre '$genre_name' ($genre_id) removed"])
                : $this->sendErrorResponse(
                    ['msg' => "Genre '$genre_name' ($genre_id) can't be removed"]
                );
        }
    }
