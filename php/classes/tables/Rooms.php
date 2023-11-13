<?

    /**
     * Класс таблицы количества комнат
     */
    class Rooms extends AbstractTableNoCache
    {

        /**
         * Исходное название таблицы
         * @return string
         */
        static function get_table_name(): string
        {
            return "NV_ROOMS";
        }

        /**
         * Сортировка для таблицы
         * @return array
         */
        static function get_order_by(): array
        {
            return ["NV_ROOMS_CODE"];
        }

        public ?string
            $short = null,
            $full = null,
            $code = null;

        public ?int $count = null;

        /**
         * HouseFlatRooms constructor.
         * @param array $row
         * @throws Exception
         */
        function __construct(array $row)
        {
            if (!empty($row)) {
                parent::__construct($row);
                $this->short = $row["NV_ROOMS_SHORT"];
                $this->full = $row["NV_ROOMS_FULL"];
                $this->code = $row["NV_ROOMS_CODE"];
                $this->count = $row["NV_ROOMS_COUNT"];
            }
        }

        /**
         * @param string $code
         * @return mixed|null
         * @throws Exception
         */
        public static function get_by_code(string $code)
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            return static::get_one_by_query("SELECT * FROM $table_cache_name WHERE {$table_name}_CODE = '$code' LIMIT 1");
        }

        /**
         * @param string[] $code_array
         * @return array
         * @throws Exception
         */
        public static function get_by_code_array(array $code_array): array
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            $db_codes = implode("', '", $code_array);
            return static::get_all_by_query("SELECT * FROM $table_cache_name WHERE {$table_name}_CODE IN ('$db_codes') ORDER BY " . implode(",", static::get_order_by()));
        }

        /**
         * @param int $count
         * @return mixed|null
         * @throws Exception
         */
        public static function get_by_count(int $count)
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            return static::get_one_by_query("SELECT * FROM $table_cache_name WHERE {$table_name}_COUNT = '$count' LIMIT 1");
        }

        /**
         * @param int[] $count_array
         * @return array
         * @throws Exception
         */
        public static function get_by_count_array(array $count_array): array
        {
            $table_name = static::get_table_name();
            $table_cache_name = static::get_table_cache_name();
            $db_counts = implode("', '", $count_array);
            return static::get_all_by_query("SELECT * FROM $table_cache_name WHERE {$table_name}_COUNT IN ('$db_counts') ORDER BY " . implode(",", static::get_order_by()));
        }

        /**
         * Получает массив количества комнат на основе id домов
         * @param House[]|null $houses
         * @return Rooms[]
         * @throws Exception
         */
        public static function get_rooms_in_houses(array $houses = null): array
        {
            $table_house_flat_cache_name = HouseFlatGrouped::get_table_cache_name();
            $query = "SELECT br.ID_NV_ROOMS, MIN(br.NV_ROOMS_SHORT) AS NV_ROOMS_SHORT, MIN(br.NV_ROOMS_FULL) AS NV_ROOMS_FULL, MIN(br.NV_ROOMS_CODE) AS NV_ROOMS_CODE, MIN(br.NV_ROOMS_COUNT) AS NV_ROOMS_COUNT FROM {$table_house_flat_cache_name} bh LEFT JOIN NV_ROOMS br on bh.ID_NV_ROOMS = br.ID_NV_ROOMS";
            if (!empty($houses)) {
                $id_houses = "";
                foreach ($houses as $house) {
                    if ($id_houses) {
                        $id_houses .= ",";
                    }
                    $id_houses .= $house->id;
                }
                $query .= " WHERE bh.ID_NV_HOUSE IN ($id_houses)";
            }
            $query .= " GROUP BY br.ID_NV_ROOMS ORDER BY " . implode(",", static::get_order_by());
            return static::get_all_by_query($query);
        }

        /**
         * Преобразует массив с данными комнат в массив данных чекбоксов
         * @param Rooms[] $rooms
         * @return array
         */
        public static function map_rooms_to_checkboxes(array $rooms): array
        {
            return array_map(function (Rooms $room) {
                $one = [
                    "value" => $room->code,
                    "text" => $room->full
                ];
                if (!empty($GLOBALS["seo"]->search_query["rooms"]) && in_array($room->code, $GLOBALS["seo"]->search_query["rooms"])) {
                    $one["checked"] = true;
                }
                return $one;
            }, $rooms);
        }

        /**
         * Преобразует массив с данными комнат в массив данных чекбоксов
         * @param Rooms[] $rooms
         * @param string $code
         * @return Rooms|null
         */
        public static function search_room_by_code(array $rooms, string $code): ?Rooms
        {
            foreach ($rooms as $room) {
                if ($room->code == $code) {
                    return $room;
                }
            }

            return null;
        }
    }
