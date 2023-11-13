<?

    /**
     * Класс электронной почты
     */
    class Email
    {
        /**
         * Email для отображения
         * @var string
         */
        public string $title;
        /**
         * Email для ссылок href
         * @var string
         */
        public string $link;

        /**
         * Функция преобразовывает email в ссылку для верстки
         * @param string $email_in
         * @return string
         */
        protected function email_to_href(string $email_in): string
        {
            return "mailto:$email_in";
        }

        /**
         * Email constructor
         * @param string $email
         */
        function __construct(string $email)
        {
            $this->title = preg_replace("/\s+/siu", "", $email);
            $this->link = $this->email_to_href($email);
        }
    }
