<?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithFaker;
    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Testing\TestResponse;
    use Tests\TestCase;

    class AuthorControllerTest extends TestCase
    {

        protected static $author_data;
        protected static $file;
        protected static $author_id;

        /**
         * @return void
         */
        public static function setUpBeforeClass(): void
        {
            self::$author_data = [
                'name'        => 'Autor de prueba',
                'photo'       => self::$file = UploadedFile::fake()->image('avatar.jpg'),
            ];
        }

        /**
         * @return void
         */
        public function test_create_author_with_valid_params()
        {

            $response_structure =
                [
                    'result'        => [
                        'status',
                        'code',
                        'msg',
                    ],
                    'response_data' => [
                        'author' => [
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
                                    'genres' => [
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
            $response = $this->post('/api/author', self::$author_data);
            Storage::disk('public_images')->assertExists("/author/" . self::$file->hashName());

            $json          = $response->json();
            self::$author_id = $json["response_data"]["author"]["id"];
            $response->assertJsonStructure($response_structure);
            $response->assertJsonPath('result.status', 'OK');
            $response->assertJsonPath('result.code', 0);
            $response->assertJsonPath('result.msg', '');
            $response->assertJsonPath('response_data.author.name', 'Autor de prueba');
            $response->assertJsonPath('response_data.author.photo', 'http://localhost/storage/imgs/author/' . self::$file->hashName());
            $response->assertStatus(200);
        }

        /**
         * @depends test_create_author_with_valid_params
         */
        public function test_update_author_name()
        {

            $data = [
                'name' => 'Nombre actualizado'
            ];
            $response = $this->post('/api/author/' . self::$author_id, $data);

            $response->assertJsonPath('response_data.author.name', $data['name']);
            $response->assertStatus(200);

        }


        /**
         * @depends test_create_author_with_valid_params
         */
        public function test_update_author_photo()
        {
            $data = [
                'photo' => $file = UploadedFile::fake()->image('avatar.jpg'),
            ];

            $response = $this->post('/api/author/' . self::$author_id, $data);
            Storage::disk('public_images')->assertExists("/author/" . $file->hashName());
            $response->assertJsonPath('response_data.author.photo', 'http://localhost/storage/imgs/author/' . $file->hashName());
            $response->assertStatus(200);
        }



        /**
         * @depends test_create_author_with_valid_params
         */
        public function test_delete_author()
        {
            $response           = $this->delete('/api/author/' . self::$author_id);
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
            $response->assertJsonPath('result.msg', 'Author \'Nombre actualizado\' (' . self::$author_id . ') removed');
            $response->assertStatus(200);
        }

        /**
         * @depends test_delete_author
         */
        public function test_delete_author_invalid()
        {
            $response           = $this->delete('/api/author/' . self::$author_id);
            $response_structure = [
                'result' => [
                    'status',
                    'code',
                    'msg',
                ],
            ];
            $response->assertJsonStructure($response_structure);
            $response->assertJsonPath('result.status', 'ERROR');
            $response->assertJsonPath('result.code', -14);
            $response->assertJsonPath('result.msg', 'Ids not found (' . self::$author_id . ')');
            $response->assertStatus(200);
        }

    }
