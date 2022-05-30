<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Погода</title>
</head>
<body>
    <div class="wrapper">
        <form class="form" method="post" action="index.php">
            <?php 
                function translit($input, $noloss=false) { //транслит строки
                    if($noloss == true) {
                        $replace1 = array('Ъ'=>'"\'', 'Ь'=>'\'\'', 'Э'=>'E\'', 'э'=>'e\'');
                        $input = strtr((string)$input, $replace1);
                    }
                    $replace = array(
                    'а'=>'a', 'б'=>'b', 'в'=>'v',
                    'г'=>'g', 'д'=>'d', 'е'=>'e', 'ё'=>'yo', 'ж'=>'zh', 'з'=>'z', 'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n',
                    'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'х'=>'h', 'ц'=>'ts', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'sch',
                    'ъ'=>'"', 'ы'=>'y', 'ь'=>'', 'э'=>'e', 'ю'=>'yu', 'я'=>'ya');
                    return strtr((string)$input, $replace);
                }
                if (isset($_POST['selectWeather'])) {
                    $city = mb_strtolower($_POST['selectCity']);
                    $city = translit($city);
                    include 'simple_html_dom.php';
                    $file = 'https://pogoda.mail.ru/prognoz/'.$city.'/';
                    $file_headers = @get_headers($file);
                    if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') $exists = false;
                        else $exists = true;
                    if (!$exists) {
                        $city = str_replace('-', '_', $city);
                        $file = 'https://pogoda.mail.ru/prognoz/'.$city.'/';
                        $file_headers = @get_headers($file);
                        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') $exists = false;
                            else $exists = true;
                    }
                    if (isset($city) && $exists) {
                        $description;
                        $error = '';
                        $html = file_get_html('https://pogoda.mail.ru/prognoz/'.$city.'/');
                        $str = $html->find('.information__content__temperature');
                        $str = trim($str[0]->plaintext);
                        $temperature = $str;
                        $str = $html->find('.information__content__additional__item');
                        $str = $html->find('.information__content__additional__item');
                        $temperature = $temperature.' - '.trim($str[0]->plaintext);
                        $status = trim($str[1]->plaintext);
                        foreach($str as $desc) {
                            foreach ($desc->find('span') as $title) {
                                if (!empty($title->title))
                                    $description .= $title->title.'<br>';
                            }
                        }
                    } else {
                        $error = '<span style="color: red;">Город не найден</span>';
                    }
                }
            ?>
            <div class="input-city">
                <h2>Город:</h2>
                <input name="selectCity" type="text" value="<?php echo $_POST['selectCity']; ?>">
            </div>
            <div class="weather-city">
                <h2>Погода:</h2>
                <div class="part-weather-city">
                    <h3>Температура:</h3>
                    <input type="text" disabled value="<?php echo $temperature ?>">
                </div>
                <div class="part-weather-city">
                    <h3>Сейчас:</h3>
                    <input type="text" disabled value="<?php echo $status ?>">
                </div>
                <div class="part-weather-city">
                    <h3>Информация:</h3>
                    <?php echo $description; ?>
                </div>
            </div>
            <div class="button-weather">
                <button type="submit" name="selectWeather">Узнать погоду</button>
                <?php 
                    echo $error;
                ?>
            </div>
        </form>
    </div>
</body>
</html>