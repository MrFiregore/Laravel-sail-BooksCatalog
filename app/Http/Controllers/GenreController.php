<?php

    namespace App\Http\Controllers;

    use App\Models\Author;
    use App\Models\Genre;
    use App\Http\Requests\DooRequest;

    class GenreController extends Controller
    {
        /**
         * @param   \App\Http\Requests\DooRequest   $request
         *
         * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\JsonResponse
         */
        public function all(DooRequest $request)
        {
            $books = Genre::with($request->all ? ['books', 'books.author'] : [])->get()->all();
            return $this->sendResponse(['data' => ['genres' => $books]]);
        }

    }
