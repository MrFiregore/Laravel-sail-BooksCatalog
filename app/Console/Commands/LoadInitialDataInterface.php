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
            "Mar Benegas", // 1
            "Cristina De Cos-Estrada" //2
        ];
        const BOOKS   = [
            "mi gran libro de experimentos" => [ // 1
                                                 [9, 3],//genres
                                                 [1,2],//author
                                                 "Susaeta; N.º 1 edición (21 mayo 2021)", //edition
                                                 "Este libro pertenece a la categoría Infantil y juvenil de SUSAETA EDICIONES. Tiene 120 páginas y forma parte de la colección El Gran Libro De.... Edad recomendada: 8 años. Resumen: Convierte un huevo en una pelota o unos fideos en verdaderos bailarines, fabrica tu propia nave espacial o tu bosque mágico de cristales, consigue que llueva o que la arena no se moje, manda mensajes en tinta invisible que solo otro científico como tú logre leer en secreto... ¡No es magia, es ciencia! ¡Atrévete a convertirte en un pequeño gran científico! --Este texto se refiere a la edición spiral_bound."//description
            ],
        ];
    }
