<?php

    namespace App\Http\Controllers;

    use App\Models\Author;
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
                    'name'  => 'required|string',
                    'photo' => 'required|image',
                ]
            );
            /**
             * @var \Illuminate\Http\UploadedFile $photo
             */
            $photo     = $validator["photo"];
            $photo_url = \Storage::disk("public_images")->url($photo->store('author', ['disk' => 'public_images']));
            $author    = Author::create([
                                            'name'  => $validator['name'],
                                            'photo' => $photo_url,
                                        ]);
            $author->save();
            return $this->sendResponse(['data' => ['author' => Author::with(['books', 'books.genres'])->find($author->id)->toArray()]]);
        }

        /**
         * @param   \App\Http\Requests\DooRequest   $request
         * @param   \App\Models\Author              $author
         *
         * @return void
         */
        public function single(DooRequest $request, Author $author)
        {
            return $this->sendResponse(['data' => ['author' => Author::with($request->all ? ['books', 'books.genres'] : [])->find($author->id)->toArray()]]);
        }

        /**
         * @param   \Illuminate\Http\Request   $request
         * @param   \App\Models\Author         $author
         *
         * @return void
         */
        public function update(Request $request, Author $author)
        {
            $validator = $this->queryValidator(
                $request,
                [
                    'name'  => 'string',
                    'photo' => 'image',
                ]
            );

            if (isset($validator['name'])) {
                $author->name = $validator['name'];
            }


            if (isset($validator['photo'])) {
                /**
                 * @var \Illuminate\Http\UploadedFile $photo
                 */
                $photo         = $validator["photo"];
                $photo_url     = \Storage::disk("public_images")->url($photo->store('author', ['disk' => 'public_images']));
                $author->photo = $photo_url;
            }


            $author->save();
            return $this->sendResponse(['data' => ['author' => Author::with($request->all ? ['books', 'books.genres'] : [])->find($author->id)->toArray()]]);
        }

        public function delete(DooRequest $request, Author $author)
        {
            $author_id = $author->id;
            $author_name = $author->name;

            return $author->delete()
                ? $this->sendResponse(['msg' => "Author '$author_name' ($author_id) removed"])
                : $this->sendErrorResponse(
                    ['msg' => "Author '$author_name' ($author_id) can't be removed"]
                );
        }
    }
