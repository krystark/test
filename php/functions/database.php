<?
    /* ==========================================================================
        DATABASE FUNCTIONS
       ========================================================================== */

    /** @var mysqli $mysqli */
    $mysqli = null;

    /**
     * Подключение к БД
     * @author Rushaker
     */
    function db_connect()
    {
        global $mysqli;

        $mysqli = new mysqli($_ENV["DB_HOST"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);

        if ($mysqli->connect_errno) {
            printf("Ошибка подключения к базе: %s\n", $mysqli->connect_error);
            exit();
        }

        if (!$mysqli->set_charset("utf8mb4")) {
            printf("Ошибка загрузки кодировки utf8: %s\n", $mysqli->error);
            printf("Текущая кодировка: %s\n", $mysqli->character_set_name());
        }
    }

    /**
     * Отключение от БД
     * @author Rushaker
     */
    function db_disconnect()
    {
        global $mysqli;

        $mysqli->close();
    }

    /**
     * Выполнение запроса в БД
     * @param string $query
     * @return bool|mysqli_result
     * @author Rushaker
     */
    function db_query(string $query)
    {
        global $mysqli;

        return $mysqli->query($query);
    }

    /**
     * Получение последнего id из БД
     * @return int|string
     * @author Rushaker
     */
    function db_last_insert_id()
    {
        global $mysqli;

        return $mysqli->insert_id;
    }

    /**
     * Получение строки результата запроса в виде ассоциативного массива
     * @param mysqli_result $res
     * @return array|null
     * @author Rushaker
     */
    function db_fetch_assoc(mysqli_result $res): ?array
    {
        return $res->fetch_assoc();
    }

    /**
     * Получение количества строк в выполненном запросе
     * @param mysqli_result $res
     * @return int
     * @author Rushaker
     */
    function db_num_rows(mysqli_result $res): int
    {
        return $res->num_rows;
    }

    /**
     * Вывод ошибки БД
     * @param string $query
     * @return string
     * @author Rushaker
     */
    function db_error(string $query = ""): string
    {
        //$q_err = "<br /><br />Ошибка в запросе:<br />" . $query . "<br /><br />";
        //return $q_err;
        global $mysqli;
        return $mysqli->error . " \n\n" . $query;
    }

    /**
     * Экранирует строку для использования в БД
     * @param string $string
     * @return string
     * @author Rushaker
     */
    function db_real_escape_string(string $string): string
    {
        global $mysqli;
        return $mysqli->real_escape_string($string);
    }

    /**
     * Выполняет группу запросов
     * @param string $query
     * @return array
     * @throws Exception
     * @author Rushaker
     */
    function db_multi_query(string $query): array
    {
        global $mysqli;

        $results = [];
        $resId = 0;

        if ($mysqli->multi_query($query)) {
            do {
                if ($result = $mysqli->store_result()) {
                    while ($row = $result->fetch_row()) {
                        $results[$resId][] = $row;
                    }
                    $result->free();
                }
                if (!$mysqli->more_results()) {
                    break;
                }
                $resId++;
            } while ($mysqli->next_result());
        } else {
            $error_list = print_r($mysqli->error_list, true);
            throw new Exception("Ошибка запроса: {$error_list}");
        }
        return $results;
    }


    /**
     * Выполнение кэшированного запроса к БД через Memcached, который сбрасывается функцией
     * @param string $query
     * @return mixed
     * @author Rushaker
     */
    function db_cache_query_result(string $query)
    {
        global $memcached;
        $rows = [];
        if ($memcached) {
            $md5_query = "nvm_db_all_" . md5($query);
            $rows = $memcached->get($md5_query);
        }

        if ($rows) {
            //echo "stored";
        } else {
            //echo "not stored";
            $res = db_query($query);
            if ($res && db_num_rows($res)) {
                $rows = db_fetch_all($res);
            } else {
                $rows = [];
            }

            if ($memcached) {
                $memcached->set($md5_query, $rows);
            }
        }
        return $rows;
    }

    /**
     * Выполнение кэшированного запроса к БД с одной строкой через Memcached, который сбрасывается функцией
     * @param string $query
     * @return mixed
     * @author Rushaker
     */
    function db_cache_query_one(string $query)
    {
        global $memcached;
        $row = [];

        if ($memcached) {
            $md5_query = "nvm_db_one_" . md5($query);
            $row = $memcached->get($md5_query);
        }

        if ($row) {
            //echo "stored";
        } else {
            //echo "not stored";
            $res = db_query($query);
            if ($res && db_num_rows($res)) {
                $row = db_fetch_assoc($res);
            } else {
                $row = [];
            }

            if ($memcached) {
                $memcached->set($md5_query, $row);
            }
        }
        return $row;
    }

    /**
     * Очищает весь кэш Мemcached
     * @author Rushaker
     */
    function db_cache_flush()
    {
        global $memcached;

        if ($memcached) {
            $memcached->flush();
        }
    }


    /**
     * Получение массива всех результата запроса
     * @param mysqli_result $res
     * @return mixed
     * @author Rushaker
     */
    function db_fetch_all(mysqli_result $res)
    {
        return $res->fetch_all(MYSQLI_ASSOC);
    }
