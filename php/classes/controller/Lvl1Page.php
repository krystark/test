<?

    /**
     * Класс данных для обычной страницы первого уровня
     */
    class Lvl1Page extends Page
    {
        /**
         * @param Url $url
         * @return Lvl1Page
         * @throws Exception
         */
        public static function code200(Url $url): Lvl1Page
        {
            return new Lvl1Page(200, $url->lvl1, $url);
        }
    }
