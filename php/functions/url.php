<?

    /**
     * Редирект
     * @param string $location
     * @param int $code
     */
    function redirect(string $location, int $code = 303)
    {
        header("Location: https://" . get_host() . $location, true, $code);
        exit;
    }

    /**
     * @param string $url
     * @param string $param
     * @return string
     */
    function remove_query_param(string $url, string $param): string
    {
        $url = preg_replace("/(&|\?)" . preg_quote($param) . "=[^&]*$/", "", $url);
        return preg_replace("/(&|\?)" . preg_quote($param) . "=[^&]*&/", "$1", $url);
    }

    /**
     * Возвращает текущий URL
     * @param bool $with_query
     * @param bool $with_host
     * @param bool $check_https
     * @return string
     */
    function get_current_url(bool $with_query = true, bool $with_host = false, bool $check_https = false): string
    {
        if ($with_host) {
            if ($check_https) {
                $https = false;
                if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                    $https = true;
                } elseif (!empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" || !empty($_SERVER["HTTP_X_FORWARDED_SSL"]) && $_SERVER["HTTP_X_FORWARDED_SSL"] == "on") {
                    $https = true;
                }
            } else {
                $https = true;
            }
            $REQUEST_PROTOCOL = $https ? "https" : "http";

            $current_url = $REQUEST_PROTOCOL . "://{$_SERVER["HTTP_HOST"]}";
        } else {
            $current_url = "";
        }
        if ($with_query) {
            $current_url .= $_SERVER["REQUEST_URI"];
        } else {
            $url = explode("?", $_SERVER["REQUEST_URI"], 5);
            $current_url .= $url[0];
        }
        return $current_url;
    }

    /**
     * Возвращает главный URL
     * @param string|null $url
     * @return string
     */
    function get_host(?string $url = null): string
    {
        if (!$url) {
            $url = get_current_url(false, true);
        }
        $parsed = parse_url($url);
        return $parsed["host"];
    }
