<?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithFaker;
    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Testing\TestResponse;
    use Tests\TestCase;

    class GenreControllerTest extends TestCase
    {

        protected static $genre_data;
        protected static $file;
        protected static $genre_id;

        /**
         * @return void
         */
        public static function setUpBeforeClass(): void
        {
            self::$genre_data = [
                'name'        => 'Género de prueba',
                'photo'       => self::$file = UploadedFile::fake()->image('avatar.jpg'),
            ];
        }

        /**
         * @return void
         */
        public function test_create_genre_with_valid_params()
        {

            $response_structure =
                [
                    'result'        => [
                        'status',
                        'code',
                        'msg',
                    ],
                    'response_data' => [
                        'genre' => [
                            'id',
                            'name',
                            'photo',
                            'books'  => [
                                '*' => [
                                    'id',
                                    'name',
                                    'photo',
                                    'edition',
                                    'description',
                                    'authors' => [
                                        '*' => [
                                            'id',
                                            'name',
                                            'photo',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];


            /**
             * @var TestResponse $response
             */
            $response = $this->post('/api/genre', self::$genre_data);
            Storage::disk('public_images')->assertExists("/genre/" . self::$file->hashName());

            $json          = $response->json();
            self::$genre_id = $json["response_data"]["genre"]["id"];
            $response->assertJsonStructure($response_structure);
            $response->assertJsonPath('result.status', 'OK');
            $response->assertJsonPath('result.code', 0);
            $response->assertJsonPath('result.msg', '');
            $response->assertJsonPath('response_data.genre.name', 'Género de prueba');
            $response->assertJsonPath('response_data.genre.photo', 'http://localhost/storage/imgs/genre/' . self::$file->hashName());
            $response->assertStatus(200);
        }

        /**
         * @depends test_create_genre_with_valid_params
         */
        public function test_update_genre_name()
        {

            $data = [
                'name' => 'Nombre actualizado'
            ];
            $response = $this->post('/api/genre/' . self::$genre_id, $data);

            $response->assertJsonPath('response_data.genre.name', $data['name']);
            $response->assertStatus(200);

        }


        /**
         * @depends test_create_genre_with_valid_params
         */
        public function test_update_genre_photo()
        {
            $data = [
                'photo' => $file = UploadedFile::fake()->image('avatar.jpg'),
            ];

            $response = $this->post('/api/genre/' . self::$genre_id, $data);
            Storage::disk('public_images')->assertExists("/genre/" . $file->hashName());
            $response->assertJsonPath('response_data.genre.photo', 'http://localhost/storage/imgs/genre/' . $file->hashName());
            $response->assertStatus(200);
        }



        /**
         * @depends test_create_genre_with_valid_params
         */
        public function test_delete_genre()
        {
            $response           = $this->delete('/api/genre/' . self::$genre_id);
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
            $response->assertJsonPath('result.msg', 'Genre \'Nombre actualizado\' (' . self::$genre_id . ') removed');
            $response->assertStatus(200);
        }

    }
