<?

    abstract class AbstractTableNoCache extends AbstractTable
    {
        /**
         * Название (желательно кешированной) таблицы
         * @return string
         */
        static function get_table_cache_name(): string
        {
            return static::get_table_name();
        }
    }