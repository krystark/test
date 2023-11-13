<?

    /**
     * Класс таблицы статусов
     */
    class Status extends AbstractTableNoCache
    {

        /**
         * Исходное название таблицы
         * @return string
         */
        static function get_table_name(): string
        {
            return "NV_STATUS";
        }

        /**
         * Сортировка для таблицы
         * @return array
         */
        static function get_order_by(): array
        {
            return ["NV_STATUS_SORT"];
        }

        public ?string
            $short = null,
            $eng = null;

        public ?int $sort = null;

        /**
         * Status constructor.
         * @param array $row
         * @throws Exception
         */
        function __construct(array $row)
        {
            if (!empty($row)) {
                parent::__construct($row);
                $this->short = $row["NV_STATUS_SHORT"];
                $this->eng = $row["NV_STATUS_ENG"];
                $this->sort = $row["NV_STATUS_SORT"];
            }
        }
    }
