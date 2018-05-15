<?

class Mirapolis
{

    public $baseUrl = 'http://YOUR_URL.vr.mirapolis.ru/mira';

    public $appid = 'system';
    // идентификатор приложения
    public $secretkey = 'YOR_KEY';

    // ключ системы
    function signet($url, $params)
    {
        // url REST-запроса и массив параметров
        $appid = $this->appid; // идентификатор приложения
        $secretkey = $this->secretkey; // ключ системы

        $ret_params = $params; // массив передаваемых параметров
        ksort($ret_params); // сортировка параметров по названию
        $ret_params['appid'] = $appid; // помещение в конец массива параметра appid

        $signstring = "$url?"; // формирование строки для подписи начиная с url
        foreach ($ret_params as $key => $val) {
            if (($val != "") || (gettype($val) != "string")) {
                $signstring .= "$key=$val&"; // добавление в строку для подписи очередного параметра
            }
        }
        $signstring .= "secretkey=$secretkey"; // дополнение строки для подписи параметром secretkey
        $ret_params['sign'] = strtoupper(md5($signstring)); // формирование ключа и добавление его в
        // массив параметров
        return $ret_params;
    }

    function sendrequest($url, $parameters = array(), $method = 'POST', $ret_crange = 0)
    {
        // дополнение массива параметров значениями appid и sign (используется выше описанная функция signit)
        $curl_data = $this->signet($url, $parameters);
        $ch = curl_init(); // инициализация дескриптора запроса
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8'); // задание кодировки запроса
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возврат результата
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // делает возможным переход на страницу ошибки
        curl_setopt($ch, CURLOPT_HEADER, $ret_crange); // делает возможным возвращение заголовка ContentRange
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); // задание метода запроса
        $query = http_build_query($curl_data); // построение строки параметров
        switch ($method) {
            case "PUT": // для PUT необходимо передавать длину строки параметров
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Content-Length: " . strlen($query)
                ));
            case "POST": // параметры PUT и POST передаются в теле запроса
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                break;
            case "GET": // для GET и DELETE параметры указываются в заголовке
            case "DELETE":
                $url .= "?$query";
        }
        curl_setopt($ch, CURLOPT_URL, $url); // задание url запроса
        $curl_response = curl_exec($ch); // выполнение запроса
        $response = json_decode($curl_response, true); // парсинг результатов
        if (!$response)
            $response = $curl_response; // если результат не json
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // получение кода результата
        curl_close($ch);
        // анализ ответа
        if ($code != 200) {
            throw new Exception("Неправильный HTTP-код: " . $code);
        } else
            if (is_array($response) && isset($response["errorMessage"])) {
                throw new Exception("Возвращена ошибка: " . $response["errorMessage"]);
            } else {
                return $response;
            }
    }

    function measuresMembersRegbyemail($measureId, $email)
    {
        $url = $this->baseUrl . "/service/v2/measures/$measureId/members/regbyemail/$email";
        return $this->sendrequest($url);
    }
}
