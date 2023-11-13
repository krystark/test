<?

    /**
     * Простой curl запрос GET
     * @param string $url
     * @return string
     */
    function simple_get_curl(string $url): string
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1",
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_MAXREDIRS => 10
        ]);

        $out = curl_exec($curl);
        curl_close($curl);

        return $out;
    }

    /**
     * Сохранение файла из url через curl
     * @param string $from_url
     * @param string $to_file_name
     * @return bool|string
     */
    function download_file_curl(string $from_url, string $to_file_name) {
        $fp = fopen($to_file_name, "w+");
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $from_url,
            CURLOPT_FILE => $fp,
            CURLOPT_POST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        $out = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $out;
    }