# BooksCatalog

<p align="center"><img src="/resources/imgs/preview.PNG" alt="BooksCatalog"> </p>

Store your books

## Requisites
- Docker compose

## How to run?

To start the Laravel Application run the following command:

``
./vendor/bin/sail up -d
``


First create the database and data examples:

``
./vendor/bin/sail artisan load_initial_data
``


Then go to http://localhost to see the Books catalog application.

To stop all containers:

``
./vendor/bin/sail down
``

To run all test use :
``
./vendor/bin/sail artisan test
``
