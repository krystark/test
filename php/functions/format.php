<?

    /**
     * Возвращает число в виде строки без десятичных знаков и с пробелом перед тысячей/миллионом
     * @param float $number
     * @return string
     */
    function int_format(float $number): string
    {
        $true_int = round($number);
        return number_format($true_int, 0, ",", " ");
    }

    /**
     * Возвращает число в виде строки с 2мя десятичными знаками и с пробелом перед тысячей/миллионом
     * @param float|null $float $float
     * @param bool $trim_zeroes
     * @param int $decimals
     * @param string $thousands_separator
     * @return string
     */
    function float_format(?float $float, bool $trim_zeroes = false, int $decimals = 2, string $thousands_separator = " "): string
    {
        if ($float) {
            $float_format = number_format($float, $decimals, ",", $thousands_separator);
            if ($trim_zeroes) {
                $float_format = rtrim(rtrim($float_format, "0"), ",");
            }
        } else {
            $float_format = "0";
        }
        return $float_format;
    }

    /**
     * Возвращает число в виде строки со знаком рубля, с 2мя десятичными знаками и с пробелом перед тысячей/миллионом
     * @param float|null $float $float
     * @param bool $trim_zeroes
     * @return string
     */
    function currency_format(?float $float, bool $trim_zeroes = true): string
    {
        return float_format($float, $trim_zeroes) . " ₽";
    }

    /**
     * Получаем цифры из строки
     * @param string $string
     * @return string
     */
    function numbers_from_str(string $string): string
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

    /**
     * Нормализация номера телефона
     * @param string $phone_in
     * @param bool $is_first_8
     * @return string
     */
    function normalize_phone(string $phone_in, bool $is_first_8 = false): string
    {
        $prefix = 7;
        if ($is_first_8) {
            $prefix = 8;
        }

        $phone = numbers_from_str($phone_in);
        if (strlen($phone) >= 6) {
            if ((strlen($phone) == 11) && (substr($phone, 0, 1) == "8")) {
                $phone = $prefix . substr($phone, 1);
            } elseif (strlen($phone) == 10) {
                $phone = $prefix . $phone;
            }
        } else {
            $phone = "";
        }
        return $phone;
    }

    /**
     * Строка телефона в корректном формате
     * @param string $phone_in
     * @return string
     */
    function format_phone(string $phone_in): string
    {
        $phone = normalize_phone($phone_in);
        $prefix = "+7";
        $code = substr($phone, 1, 3);
        $part1 = substr($phone, 4, 3);
        $part2 = substr($phone, 7, 2);
        $part3 = substr($phone, 9, 2);

        return "{$prefix}({$code}){$part1}-{$part2}-{$part3}";
    }

    /**
     * Получаем boolean из строки Да/Нет
     * @param string $db_yes_no
     * @return bool
     * @throws Exception
     */
    function bool_from_db(string $db_yes_no): bool
    {
        if ($db_yes_no == "Да") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получаем Да/Нет из boolean
     * @param bool $bool
     * @return string
     * @throws Exception
     */
    function bool_to_db(bool $bool): string
    {
        if ($bool) {
            return "Да";
        } else {
            return "Нет";
        }
    }

    /**
     * @param string|null $text
     * @return string
     */
    function db_break_spaces(?string $text): string
    {
        if ($text) {
            return preg_replace("/\n/siu", "<br>", $text);
        } else {
            return "";
        }
    }

    /**
     * Делит два числа, возвращает 0, если второе число 0
     * @param float $one
     * @param float $two
     * @return float
     */
    function zero_div(float $one, float $two): float
    {
        if ($two) {
            return $one / $two;
        } else {
            return 0.0;
        }
    }

    /**
     * Проверка корректности телефонного номера
     * @param string $phone
     * @return bool
     */
    function phone_is_correct(string $phone): bool
    {
        return ((strlen($phone) == 11) && ($phone[1] != "8") && (
                count(array_unique(str_split(preg_replace("/[^0-9]/", "", $phone)))) > 3
            ));
    }

    /**
     * Получаем ip адрес
     * @return mixed|string
     */
    function get_user_ip()
    {
        if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            if (strpos($_SERVER["HTTP_X_FORWARDED_FOR"], ",") > 0) {
                $addr = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
                return trim($addr[0]);
            } else {
                return $_SERVER["HTTP_X_FORWARDED_FOR"];
            }
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    /**
     * Преобразовывает строку изображения из базы в путь к фото
     * @param string|null $photo_urls
     * @param bool $logo_if_empty
     * @return string
     */
    function db_photo_urls_to_one_img(?string $photo_urls, bool $logo_if_empty = true): string
    {
        $return = $logo_if_empty ? "/img/no-img.jpg" : "";
        if (!empty($photo_urls) && ($photo_urls[0] == "[")) {
            $images = json_decode($photo_urls, true);
            if ($images) {
                $return = $images[0];
            }
        }
        return $return;
    }

    /**
     * Преобразовывает строку изображения из базы в массив фото
     * @param string|null $photo_urls
     * @param bool $logo_if_empty
     * @param bool $minus_first
     * @return array
     */
    function db_photo_urls_to_array(?string $photo_urls, bool $logo_if_empty = true, bool $minus_first = false): array
    {
        $return = [];
        if (!empty($photo_urls) && ($photo_urls[0] == "[")) {
            $images = json_decode($photo_urls, true);
            if ($images) {
                $return = $images;
                if ($minus_first) {
                    array_shift($return);
                }
            }
        }
        if (!$return && $logo_if_empty) {
            $return = ["/img/no-img.jpg"];
        }
        return $return;
    }

    /**
     * Декодирование данных из поля админки типа "rich"
     * @param $json
     * @return string|null
     */
    function quill_decode($json): ?string
    {
        if (empty(trim($json))) {
            return "";
        }
        try {
            $lexer = new nadar\quill\Lexer(urldecode($json));
            return $lexer->render();
        } catch (Exception $e) {
            return $json;
        }
    }

    /**
     * @param string|null $quill_text
     * @param string|null $reserve_text
     * @param bool|null $remove_gap
     * @return string|null
     */
    function check_empty_quill(?string $quill_text, ?string $reserve_text, ?bool $remove_gap = false): ?string
    {
        if (!$quill_text || !quill_decode($quill_text) || ($quill_text == "%7B%22ops%22%3A%5B%7B%22insert%22%3A%22%5Cn%22%7D%5D%7D")) {
            $result = $reserve_text;
        } else {
            $result = quill_decode($quill_text);
        }
        if ($remove_gap === true) {
            $result = mb_ereg_replace("<span[^>]*>", "", $result);
            $result = mb_ereg_replace("<\/span>", "", $result);
            $result = mb_ereg_replace("<br\s*\/?>", "", $result);
            $result = mb_ereg_replace("\s*<p[^>]*>\s*<\/p>", "", $result);
            $result = mb_ereg_replace("\s+", " ", $result);
        }
        return $result;
    }

    /**
     * @param string|null $text_in
     * @return string
     */
    function strip_seo(?string $text_in): string
    {
        if ($text_in) {
            $text = strip_tags($text_in);
            $text = str_replace("&nbsp;", " ", $text);
            $text = preg_replace('/"([^"]+)"/siu', "«$1»", $text);
            $text = str_replace('"', "", $text);
            $text = preg_replace("/\s+/siu", " ", $text);
            $text = trim($text);
        } else {
            $text = "";
        }
        return $text;
    }

    /**
     * @param string|null $text_in
     * @return string
     */
    function strip_rich(?string $text_in): string
    {
        if ($text_in) {
            $text = preg_replace('/\s*<p>\s*<br\s*\/?>\s*<\/p>\s*/siu', "", $text_in);
            $text = preg_replace("/\s+/siu", " ", $text);
            $text = trim($text);
        } else {
            $text = "";
        }
        return $text;
    }

    /**
     * Возвращает путь к webp изображения
     * @param string $img_path
     * @return string
     */
    function imgpath_to_webp(string $img_path): string
    {
        return preg_replace("/\.(jpe?g|png)$/", ".webp", $img_path);
    }

    /**
     * Вывод русского названия месяца из даты
     * @param $date
     * @return string
     */
    function get_month_from_date_ru($date): string
    {
        $date = date("d.m.Y", strtotime($date));
        $_monthsList = array(
            ".01." => "январь",
            ".02." => "февраль",
            ".03." => "март",
            ".04." => "апрель",
            ".05." => "май",
            ".06." => "июнь",
            ".07." => "июль",
            ".08." => "август",
            ".09." => "сентябрь",
            ".10." => "октябрь",
            ".11." => "ноябрь",
            ".12." => "декабрь"
        );

        //заменяем число месяца на название:
        $_mD = date(".m.", strtotime($date));
        $date = $_monthsList[$_mD];
        return $date;
    }

    /**
     * Вывод отформатированных данных
     * @param $data
     * @param $die
     * @return void
     */
    function debug($data, $die = false)
    {
        echo "<pre>" . print_r($data, 1) . "</pre>";
        if($die){
            die();
        }
    }

