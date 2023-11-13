<?

    abstract class AbstractTable
    {
        public ?string $id = null;

        /**
         * @return string
         */
        public static function get_class_name(): string
        {
            return get_called_class();
        }

        /**
         * Название (желательно кешированной) таблицы
         * @return string
         */
        abstract static function get_table_cache_name(): string;

        /**
         * Исходное название таблицы
         * @return string
         */
        abstract static function get_table_name(): string;

        /**
         * Сортировка для таблицы
         * @return array
         */
        abstract static function get_order_by(): array;

        /**
         * Table constructor.
         * @param array $row
         */
        function __construct(array $row)
        {
            if (!empty($row)) {
                $this->id = $row["ID_" . static::get_table_name()];
            }
        }

        /**
         * Получает одну запись из таблицы по запросу
         * @param string $query
         * @return mixed|null
         * @throws Exception
         */
        protected static function get_one_by_query(string $query)
        {
            $row = db_cache_query_one($query);
            //if ($_SESSION["ID_RP_USER"] == "158") {
            //    echo "gobq_after_query - " . self::get_class_name() . " " . date("H:i:s") . "<br>\n";
            //}
            if ($row) {
                $return = new static($row);
                //if ($_SESSION["ID_RP_USER"] == "158") {
                //    echo "gobq_after_class - " . self::get_class_name() . " " . date("H:i:s") . "<br>\n";
                //}
                return $return;
            } else {
                return null;
            }
        }

        /**
         * Получает все записи из таблицы по запросу. !! В запрос необходимо добавить get_order_by()
         * @param string $query
         * @return array
         * @throws Exception
         */
        protected static function get_all_by_query(string $query): array
        {
            $return = [];
            $res = db_cache_query_result($query);
            //if ($_SESSION["ID_RP_USER"] == "158") {
            //    echo "gabq_after_query - " . self::get_class_name() . " " . date("H:i:s") . "<br>\n";
            //}
            if ($res) {
                foreach ($res as $row) {
                    $return[] = new static($row);
                }
            }
            //if ($_SESSION["ID_RP_USER"] == "158") {
            //    echo "gabq_after_class - " . self::get_class_name() . " " . date("H:i:s") . "<br>\n";
            //}
            return $return;
        }

        /**
         * @param int $id
         * @return mixed|null
         * @throws Exception
         */
        public static function get_by_id(int $id)
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            return static::get_one_by_query("SELECT * FROM $table_cache_name WHERE ID_$table_name = $id LIMIT 1");
        }

        /**
         * @param int[] $id_array
         * @return array
         * @throws Exception
         */
        public static function get_by_id_array(array $id_array): array
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            $db_ids = implode(", ", $id_array);
            return static::get_all_by_query("SELECT * FROM $table_cache_name WHERE ID_$table_name IN ($db_ids) ORDER BY " . implode(",", static::get_order_by()));
        }

        /**
         * @param string $eng
         * @return mixed|null
         * @throws Exception
         */
        public static function get_by_eng(string $eng)
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            return static::get_one_by_query("SELECT * FROM $table_cache_name WHERE {$table_name}_ENG = '$eng' LIMIT 1");
        }

        /**
         * @param string $short
         * @return mixed|null
         * @throws Exception
         */
        public static function get_by_short(string $short)
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            return static::get_one_by_query("SELECT * FROM $table_cache_name WHERE {$table_name}_SHORT = '$short' LIMIT 1");
        }

        /**
         * @param string[] $eng_array
         * @return array
         * @throws Exception
         */
        public static function get_by_eng_array(array $eng_array): array
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            $db_engs = implode("', '", $eng_array);
            return static::get_all_by_query("SELECT * FROM $table_cache_name WHERE {$table_name}_ENG IN ('$db_engs') ORDER BY " . implode(",", static::get_order_by()));
        }

        /**
         * @param Filter[] $filters
         * @param array $order_by
         * @param array $select
         * @return array
         * @throws Exception
         */
        public static function get_all(array $filters = [], array $order_by = [], array $select = []): array
        {
            $table_cache_name = static::get_table_cache_name();
            $select_part = $select ? implode(", ", $select) : "*";
            $query = "SELECT $select_part FROM $table_cache_name WHERE (1 = 1)";
            foreach ($filters as $filter) {
                $query .= $filter->get_where();
            }
            if ($order_by) {
                $query .= " ORDER BY " . implode(",", $order_by);
            } else {
                $query .= " ORDER BY " . implode(",", static::get_order_by());
            }
            if (isset($order_by["die"])) {
                die($query);
            }
            return static::get_all_by_query($query);
        }

        /**
         * @param Filter[] $filters
         * @return int
         * @throws Exception
         */
        public static function get_count(array $filters = []): int
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            $query = "SELECT COUNT(*) as {$table_name}_COUNT FROM $table_cache_name WHERE (1 = 1)";
            foreach ($filters as $filter) {
                $query .= $filter->get_where();
            }
            //die($query);
            $row = db_cache_query_one($query);
            if ($row) {
                return $row["{$table_name}_COUNT"];
            } else {
                return 0;
            }
        }

        /**
         * @param int $limit
         * @param int $offset
         * @param Filter[] $filters
         * @param array $order_by
         * @param array $select
         * @return array
         * @throws Exception
         */
        public static function get_by_limit(int $limit, int $offset = 0, array $filters = [], array $order_by = [], array $select = []): array
        {
            $table_cache_name = static::get_table_cache_name();
            $select_part = $select ? implode(", ", $select) : "*";
            $query = "SELECT $select_part FROM $table_cache_name WHERE (1 = 1)";
            foreach ($filters as $filter) {
                $query .= $filter->get_where();
            }
            if ($order_by) {
                $query .= " ORDER BY " . implode(",", $order_by) . " LIMIT $offset, $limit";
            } else {
                $query .= " ORDER BY " . implode(",", static::get_order_by()) . " LIMIT $offset, $limit";
            }
            $query = str_replace("WHERE (1 = 1) ORDER BY", "ORDER BY", $query);
            //echo $query . "<br>";
            //if ($_SESSION["ID_RP_USER"] == "158") {
            //    die($query);
            //}
            //die($query);
            return static::get_all_by_query($query);
        }

        /**
         * @param Filter[] $filters
         * @param array $order_by
         * @return mixed|null
         * @throws Exception
         */
        public static function get_one(array $filters = [], array $order_by = [])
        {
            $table_cache_name = static::get_table_cache_name();
            $query = "SELECT * FROM $table_cache_name WHERE (1 = 1)";
            foreach ($filters as $filter) {
                $query .= $filter->get_where();
            }
            if ($order_by) {
                $query .= " ORDER BY " . implode(",", $order_by);
            } else {
                $query .= " ORDER BY " . implode(",", static::get_order_by());
            }
            $query .= " LIMIT 0, 1";
            //echo $query . "<br>";
            //if (static::get_class_name() == "Calc") {
            //    die($query);
            //}
            return static::get_one_by_query($query);
        }
    }
