<?php

    namespace App\Http\Controllers;

    use App\Models\Author;
    use App\Models\Book;
    use App\Http\Requests\DooRequest;
    use Illuminate\Http\Request;

    class AuthorController extends Controller
    {

        /**
         * @param   \App\Http\Requests\DooRequest   $request
         *
         * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
         */
        public function all(DooRequest $request)
        {
            $authors = Author::with($request->all ? ['books', 'books.genres'] : [])->get()->all();
            return $this->sendResponse(['data' => ['authors' => $authors]]);
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
                    'photo'       => 'required|image',
                ]
            );
            /**
             * @var \Illuminate\Http\UploadedFile $photo
             */
            $photo     = $validator["photo"];
            $photo_url = \Storage::disk("public_images")->url($photo->store('author', ['disk' => 'public_images']));
            $author      = Author::create([
                                          'name'        => $validator['name'],
                                          'photo'       => $photo_url,
                                      ]);
            $author->save();
            return $this->sendResponse(['data' => ['author' => Author::with(['books', 'books.genres'])->find($author->id)->toArray()]]);
        }
    }
