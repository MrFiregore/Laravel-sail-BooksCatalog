<?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithFaker;
    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Testing\TestResponse;
    use Tests\TestCase;

    class BookControllerTest extends TestCase
    {

        protected static $book_data;
        protected static $file;
        protected static $book_id;

        /**
         * @return void
         */
        public static function setUpBeforeClass(): void
        {
            self::$book_data = [
                'name'        => 'Libro de prueba',
                'edition'     => 'Edición de prueba',
                'description' => 'Descripción de prueba',
                'genres'      => [1, 2],
                'authors'     => [1],
                'photo'       => self::$file = UploadedFile::fake()->image('avatar.jpg'),
            ];
        }

        /**
         * A basic feature test example.
         *
         * @return void
         */
        public function test_create_book_with_valid_params()
        {

            $response_structure =
                [
                    'result'        => [
                        'status',
                        'code',
                        'msg',
                    ],
                    'response_data' => [
                        'book' => [
                            'id',
                            'name',
                            'photo',
                            'edition',
                            'description',
                            'genres'  => [
                                '*' => [
                                    'id',
                                    'name',
                                    'photo',
                                ],
                            ],
                            'authors' => [
                                '*' => [
                                    'id',
                                    'name',
                                    'photo',
                                ],
                            ],
                        ],
                    ],
                ];


            /**
             * @var TestResponse $response
             */
            $response = $this->post('/api/book', self::$book_data);
            Storage::disk('public_images')->assertExists("/books/" . self::$file->hashName());

            $json          = $response->json();
            self::$book_id = $json["response_data"]["book"]["id"];
            $response->assertJsonStructure($response_structure);
            $response->assertJsonPath('result.status', 'OK');
            $response->assertJsonPath('result.code', 0);
            $response->assertJsonPath('result.msg', '');
            $response->assertJsonPath('response_data.book.name', 'Libro de prueba');
            $response->assertJsonPath('response_data.book.edition', 'Edición de prueba');
            $response->assertJsonPath('response_data.book.description', 'Descripción de prueba');
            $response->assertJsonPath('response_data.book.photo', 'http://localhost/storage/imgs/books/' . self::$file->hashName());

            $response->assertJsonPath('response_data.book.genres.0.id', 1);
            $response->assertJsonPath('response_data.book.genres.0.name', 'Misterio');
            $response->assertJsonPath('response_data.book.genres.0.photo', 'http://localhost/storage/imgs/genre/Misterio.png');
            $response->assertJsonPath('response_data.book.genres.1.id', 2);
            $response->assertJsonPath('response_data.book.genres.1.name', 'Ficción');
            $response->assertJsonPath('response_data.book.genres.1.photo', 'http://localhost/storage/imgs/genre/Ficción.png');

            $response->assertJsonPath('response_data.book.authors.0.id', 1);
            $response->assertJsonPath('response_data.book.authors.0.name', 'Mar Benegas');
            $response->assertJsonPath('response_data.book.authors.0.photo', 'http://localhost/storage/imgs/author/Mar Benegas.jpg');
            $response->assertStatus(200);
        }

        /**
         * @depends test_create_book_with_valid_params
         */
        public function test_update_book_name($var)
        {

            $data = [
                'name' => 'Nombre actualizado'
            ];
            $response = $this->post('/api/book/' . self::$book_id,$data);

            $response->assertJsonPath('response_data.book.name',$data['name']);
            $response->assertStatus(200);

        }

        /**
         * @depends test_create_book_with_valid_params
         */
        public function test_update_book_description()
        {
            $data = [
                'description' => 'Descripción actualizada'
            ];
            $response = $this->post('/api/book/' . self::$book_id, $data);

            $response->assertJsonPath('response_data.book.description', $data['description']);
            $response->assertStatus(200);


        }

        /**
         * @depends test_create_book_with_valid_params
         */
        public function test_update_book_edition()
        {
            $data = [
                'edition' => 'Edición actualizada'
            ];
            $response = $this->post('/api/book/' . self::$book_id, $data);

            $response->assertJsonPath('response_data.book.edition', $data['edition']);
            $response->assertStatus(200);


        }

        /**
         * @depends test_create_book_with_valid_params
         */
        public function test_update_book_photo()
        {
            $data = [
                'photo' => $file = UploadedFile::fake()->image('avatar.jpg'),
            ];

            $response = $this->post('/api/book/' . self::$book_id, $data);
            Storage::disk('public_images')->assertExists("/books/" . $file->hashName());
            $response->assertJsonPath('response_data.book.photo', 'http://localhost/storage/imgs/books/' . $file->hashName());
            $response->assertStatus(200);
        }

        /**
         * @depends test_create_book_with_valid_params
         */
        public function test_update_book_genres()
        {
            $data = [
                'genres' => [
                    3
                ]
            ];

            $missing_data = [
                [
                    'id'    => 1,
                    'name'  => 'Misterio',
                    'photo' => 'http://localhost/storage/imgs/genre/Misterio.png'
                ],
                [
                    'id'    => 2,
                    'name'  => 'Ficción',
                    'photo' => 'http://localhost/storage/imgs/genre/Ficción.png'
                ]
            ];

            $response     = $this->post('/api/book/' . self::$book_id, $data);
            $response->assertJsonPath('response_data.book.genres.0.id', 3);
            $response->assertJsonPath('response_data.book.genres.0.name', 'Comedia');
            $response->assertJsonPath('response_data.book.genres.0.photo', 'http://localhost/storage/imgs/genre/Comedia.png');
            $response->assertJsonMissing($missing_data);
            $response->assertStatus(200);
        }

        /**
         * @depends test_create_book_with_valid_params
         */
        public function test_update_book_authors()
        {
            $data = [
                'authors' => [
                    2
                ]
            ];

            $missing_data = [
                'id'    => 1,
                'name'  => 'Mar Benegas',
                'photo' => 'http://localhost/storage/imgs/author/Mar Benegas.jpg'
            ];
            $response = $this->post('/api/book/' . self::$book_id, $data);
            $response->assertJsonPath('response_data.book.authors.0.id', 2);
            $response->assertJsonPath('response_data.book.authors.0.name', 'Cristina De Cos-Estrada');
            $response->assertJsonPath('response_data.book.authors.0.photo', 'http://localhost/storage/imgs/author/Cristina De Cos-Estrada.jpg');
            $response->assertJsonMissing($missing_data);
            $response->assertStatus(200);

        }

        /**
         * @depends test_create_book_with_valid_params
         */
        public function test_delete_book()
        {
            $response           = $this->delete('/api/book/' . self::$book_id);
            $json = $response->json();
            $response_structure = [
                'result' => [
                    'status',
                    'code',
                    'msg',
                ],
            ];
            $response->assertJsonStructure($response_structure);
            $response->assertJsonPath('result.status', 'OK');
            $response->assertJsonPath('result.code', 0);
            $response->assertJsonPath('result.msg', 'Book \'Nombre actualizado\' (' . self::$book_id . ') removed');
            $response->assertStatus(200);
        }

    }
