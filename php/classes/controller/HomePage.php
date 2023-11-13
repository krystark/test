<?

    /**
     * Класс данных для главной страницы
     */
    class HomePage extends Page
    {
        /**
         * @param Url $url
         * @return HomePage
         * @throws Exception
         */
        public static function code200(Url $url): HomePage
        {
            return new HomePage(200, "home", $url);
        }
    }
