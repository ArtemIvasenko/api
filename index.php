<?php
require_once 'blockchain.php';
$data = json_decode($res); // Актуальные курсы валют
$route = $_GET['route'];
$result = "";
if ($_SERVER['REQUEST_METHOD'] == "GET")
    $method = $_GET['method'];
elseif ($_SERVER['REQUEST_METHOD'] == "POST")
    $method = $_POST['method'];

$commission = 0.02; // Указываем комиссию сервиса


if ($_POST['token'] == 'eyJhbGciOiJIUzI1NiJ9eyJSb2xlIjoiQWRtaW4iLCJJc3N1ZXIiOiJJc3N1ZXIi'){

    switch ($method) {
        case 'rates': // Вывод продажи и покупки
            if (empty($_GET['currency'])) { // Есди не указана валюта
                $answer = rates($data, $commission);
            } else { // Если указана валюта
                $answer = rates($data, $commission, $_GET['currency']);
            }

            if ($answer != 'error') // Если нет ошибки, возвращаем результат
                $result = ['status'=>'success', 'code'=>200, 'data'=>$answer];
            else // Возвращаем ошибку
                $result = ['status'=>'error', 'code'=>403, 'message'=>'Invalid token'];
            break;

        case 'convert': //Конвертация валюты
            $answer = convert($data, $commission, $_POST['currency_from'], $_POST['currency_to'], $_POST['value']);
            $result = ['status'=>'success', 'code'=>200, 'data'=>$answer];
            break;

        default: // Если метод не определен, то возвращаем ошибку
            $result = ['status'=>'error', 'code'=>403, 'message'=>'Invalid token'];
            break;

    }

} else {
    $result = ['status'=>'error', 'code'=>403, 'message'=>'Invalid token'];
}


function rates($data, $commission, $currency = false) { // Функция вывода продажи и покупки
    $buy = 1 + $commission; // Комсиия покупки
    $sell = 1 - $commission; // Комиссия продажи

    if ($currency == false) { // Если не указана валюта, то предоставляем весь список
        foreach ($data as $sumbol => $info) {
            $rates[$sumbol]['buy'] = $info->buy * $buy; // Добавляем свой коэффициент
            $rates[$sumbol]['sell'] = $info->sell * $sell; // Добавляем свой коэффициент
        }

        //Сортировка от меньшему к  большего
        $likes_count = array_column($rates, 'buy');
        array_multisort($likes_count, SORT_ASC, $rates);

        return $rates;

    } else { // Если указана конкретная валюта
        if (!empty($data->$currency)) {
            $rates[$currency]['buy'] = $data->$currency->buy * $buy;
            $rates[$currency]['sell'] = $data->$currency->sell * $sell;
            return $rates;
        } else {
            return 'error'; // Если нет такой валюты, то возвращаем ошибку
        }
    }

}

function convert($data, $commission,$currency_from, $currency_to, $value) {
    $buy = 1 + $commission; // Комсиия покупки
    $sell = 1 - $commission; // Комиссия продажи

    if ($currency_from == 'BTC') {
        $rate = $data->$currency_to->sell * $sell;
    } else {
        if(empty($data->$currency_from)){
            return 'error';
        }
        $rate = $data->$currency_from->buy * $buy;
        $rate = 1 / $rate;
    }
    $converted_value = $rate * $value;

    if ($currency_from == 'BTC') {
        $converted_value = round($converted_value, 2);
    } else {
        $converted_value = round($converted_value, 10);
    }


    return ['currency_from'=>$currency_from, 'currency_to'=>$currency_to, 'value'=>$value, 'converted_value'=>$converted_value, 'rate'=>$rate];
}

//Вывод результата
echo json_encode($result);


