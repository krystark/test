<?

    /**
     * Class Filter
     */
    abstract class Filter
    {
        /**
         * Получает условия AND для where части запроса
         * @return string
         */
        abstract function get_where(): string;
    }