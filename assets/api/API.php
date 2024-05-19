<?php

//опасно = [11,12,13,14,15,17,18,19,20,23,24,25,26,27,28,29,34,35 ... +2] - 20 штук опасного контента 
//нежелательно = [55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74] - 20 штук нежелательного контента
//безопасно = [36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,1,2,3,4,5] - 20 штук безопасного контента

$videos = include_once ('/xampp/htdocs/videohosting/assets/database/getVideos.php');
$banwords = include_once ('/xampp/htdocs/videohosting/assets/database/getContent.php');
$genre = include_once ('/xampp/htdocs/videohosting/assets/database/getGenre.php');
if(!(isset($videos)&&isset($banwords))){
    return;
} else {
    $max_distance = 1;
    $content_status = array();
    for($i=0; $i<count($videos); $i++){
        $content_status[$i] = 'safe';
        $name_words = explode(" ", strtolower($videos[$i]['videoname']));
        $desc_words = explode(" ", strtolower($videos[$i]['description']));
        $all_words = array_merge($name_words, $desc_words);

        foreach ($all_words as $word) { // Перебор всех слов в названии и описании видео
            foreach ($banwords as $banword) { // Перебор всех запрещенных слов

                $distance = levenshtein($word, strtolower($banword['content'])); // Вычисление расстояния Левенштейна между текущим словом и запрещенным словом

                if (stripos($word, $banword['content']) !== false) {
                    // Проверка на точное совпадение текущего слова с запрещенным словом (без учета регистра)
                    $content_status[$i] = "blocked";
                    break 2;
                }

                if ($distance <= $max_distance) {
                    // Проверка на совпадение текущего слова с запрещенным словом с учетом расстояния Левенштейна
                    $content_status[$i] = "blocked";
                    break 2; // прерываем вложенный цикл
                }

                $word_length = strlen($word);
                $banword_length = strlen($banword['content']);

                if ($word_length < $banword_length) {
                    // Проверка на то, что длина текущего слова не меньше длины запрещенного слова
                    continue;
                }

                $prefix_length = min($banword_length, $max_distance);
                $prefix = substr($word, 0, $prefix_length);
                $prefix_distance = levenshtein($prefix, substr($banword['content'], 0, $prefix_length));

                if ($prefix_distance > $max_distance) {
                    // Проверка на совпадение префикса текущего слова с префиксом запрещенного слова с учетом расстояния Левенштейна
                    continue;
                }

                $suffix_length = min($banword_length, $max_distance);
                if ($suffix_length > $word_length) {
                    $suffix_length = $word_length;
                }
                $suffix = substr($word, $word_length - $suffix_length);
                $suffix_distance = levenshtein($suffix, substr($banword['content'], $banword_length - $suffix_length, $suffix_length)); // изменил эту строку

                if ($suffix_distance > $max_distance) {
                    // Проверка на совпадение суффикса текущего слова с суффиксом запрещенного слова с учетом расстояния Левенштейна
                    continue;
                }

                $infix_length = $word_length - $prefix_length - $suffix_length;
                $infix = substr($word, $prefix_length, $infix_length);
                $infix_distance = levenshtein($infix, substr($banword['content'], $prefix_length, $infix_length));

                if ($infix_distance <= $max_distance) {
                    // Проверка на совпадение инфикса текущего слова с инфиксом запрещенного слова с учетом расстояния Левенштейна
                    $content_status[$i] = "blocked";
                    break 2; // прерываем вложенный цикл
                }
            }
        }
    }


    $data = array();
    for($i=0; $i<count($videos); $i++){
        if($videos[$i]['genre'] != 'контент'){
            $content_status[$i] = "safe";}
        for($j=0; $j<count($genre); $j++){
            if($videos[$i]['genre'] == $genre[$j]['жанр']) $content_status[$i] = "dangerous";
    }
    $data[$i]=[
        'name' => $videos[$i]['videoname'],
        'description' => $videos[$i]['description'],
        'author' => $videos[$i]['author'],
        'genre' => $videos[$i]['genre'],
        'status' => $content_status[$i]
    ];
}
    return $data;
    // echo json_encode($data);
}

?>