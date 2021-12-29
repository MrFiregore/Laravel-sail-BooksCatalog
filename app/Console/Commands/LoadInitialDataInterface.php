<?php

    namespace App\Console\Commands;

    interface LoadInitialDataInterface
    {
        const GENRES  = [
            "Misterio",// 1
            "Ficción",// 2
            "Comedia",// 3
            "Cómics",// 4
            "Drama",// 5
            "Terror",// 6
            "Romance",// 7
            "Cuentos",// 8
            "Ciencia",// 9
        ];
        const AUTHORS = [
            "Mar Benegas", //1
            "Cristina De Cos-Estrada", //2
            "Miguel de Cervantes",//3
            "Gabriel García Márquez"//4
        ];
        const BOOKS   = [
            "mi gran libro de experimentos" => [ // 1
                                                 [9, 3],//genres
                                                 [1,2],//author
                                                 "Susaeta; N.º 1 edición (21 mayo 2021)", //edition
                                                 "Este libro pertenece a la categoría Infantil y juvenil de SUSAETA EDICIONES. Tiene 120 páginas y forma parte de la colección El Gran Libro De.... Edad recomendada: 8 años. Resumen: Convierte un huevo en una pelota o unos fideos en verdaderos bailarines, fabrica tu propia nave espacial o tu bosque mágico de cristales, consigue que llueva o que la arena no se moje, manda mensajes en tinta invisible que solo otro científico como tú logre leer en secreto... ¡No es magia, es ciencia! ¡Atrévete a convertirte en un pequeño gran científico! --Este texto se refiere a la edición spiral_bound."//description
            ],
            "cien años de soledad" => [
                [2, 3, 7, 8],
                [4],
                "",
                "Cien años de soledad es una novela del escritor colombiano Gabriel García Márquez, ganador del Premio Nobel de Literatura en 1982. Es considerada una obra maestra de la literatura hispanoamericana y universal, así como una de las obras más traducidas y leídas en español."
            ],
            "don quijote" => [
                [2,3,7,8],
                [3],
                "El ingenioso hidalgo don Quijote de la Mancha",
                "Don Quijote de la Mancha es una novela escrita por el español Miguel de Cervantes Saavedra. Publicada su primera parte con el título de El ingenioso hidalgo don Quijote de la Mancha a comienzos de 1605, es la obra más destacada de la literatura española y una de las principales de la literatura universal, además de ser la más leída después de la Biblia. En 1615 apareció su continuación con el título de Segunda parte del ingenioso caballero don Quijote de la Mancha. El Quijote de 1605 se publicó dividido en cuatro partes; pero al aparecer el Quijote de 1615 en calidad de Segunda parte de la obra, quedó revocada de hecho la partición en cuatro secciones del volumen publicado diez años antes por Cervantes"
            ],

        ];
    }
