<?php

    namespace App\Console\Commands;

    use App\Models\Author;
    use App\Models\Book;
    use App\Models\Genre;
    use Illuminate\Console\Command;
    use Illuminate\Contracts\Filesystem\FileExistsException;
    use Illuminate\Http\File;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\Storage;
    use Symfony\Component\Console\Input\InputOption;

    class LoadInitialData extends Command implements LoadInitialDataInterface
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'load_initial_data {--force : Recreate database scheme}';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Create and store common initial data as default genres, author and books';

        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter $from
         */
        protected $from;

        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter $to
         */
        protected $to;

        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->from = \Storage::disk("public_local_imgs");
            $this->to = \Storage::disk("public_images");
            parent::__construct();
        }

        /**
         * Execute the console command.
         *
         * @return int
         */
        public function handle()
        {

            /**
             * Create database
             */
            if($this->option('force')){
                $this->call('migrate:fresh', ['--force']);
                $this->call('storage:link');
            }

            foreach (self::GENRES as $genre) {
                if (!Genre::whereName($genre)->count()) {
                    $this->addGenre($genre);
                }
            }

            foreach (self::AUTHORS as $author) {
                if (!Author::whereName($author)->count()){
                    $this->addAuthor($author);
                }
            }

            foreach (self::BOOKS as $book_name => $book_data) {
                if (!Book::whereName($book_name)->count()) {
                    $this->addBook($book_name, $book_data);
                }
            }
            $this->info('The command was successful!');
            return Command::SUCCESS;
        }

        public function addBook(string $book_name, array $book_data){
            $book = Book::create(
                [
                    'name'        => $book_name,
                    'edition'     => $book_data[2],
                    'photo'       => $this->storeAndGetUrl("/book/$book_name.jpg"),
                    'description' => $book_data[3],
                ]
            );
            $book->authors()->attach($book_data[1]);
            $book->genres()->attach($book_data[0]);
            $book->save();
        }

        private function addAuthor(string $author){
            Author::create(
                [
                    "name" => $author,
                    "photo" => $this->storeAndGetUrl("/author/$author.jpg"),
                ]
            );
        }

        private function addGenre(string $genre)
        {
            Genre::create(
                [
                    "name"  => $genre,
                    "photo" => $this->storeAndGetUrl("/genre/$genre.png"),
                ]
            );
        }

        private function storeAndGetUrl(string $path){
            try {
                $this->to->writeStream($path, $this->from->readStream($path));
            }
            catch (FileExistsException $e) {
            }

            return $this->to->url($path);
        }

        /**
         * Get the console command options.
         *
         * @return array
         */
        protected function getOptions()
        {
            return [
                ['force', false, InputOption::VALUE_NONE, 'Force recreate the database'],
            ];
        }
    }
