<?

    /**
     * Класс url
     */
    class Url
    {
        public ?string
            $lvl1 = null,
            $lvl2 = null,
            $lvl3 = null,
            $lvl4 = null,
            $domain = null,
            $full = null,
            $full_no_query;

        /**
         * Url constructor.
         */
        public function __construct(?string $url_in = null)
        {
            if (empty($url_in)) {
                $this->full = get_current_url();
                $this->full_no_query = get_current_url(false);
                $this->domain = get_host($url_in);
            } else {
                $this->full = $url_in;
                $url = explode("?", $url_in, 5);
                $this->full_no_query = $url[0];
                $this->domain = "https://{$_SERVER["HTTP_HOST"]}";
            }
            $url_pieces = explode("/", $this->full_no_query);
            $this->lvl1 = $url_pieces[1];
            if (count($url_pieces) > 2) {
                $this->lvl2 = $url_pieces[2];
            }
            if (count($url_pieces) > 3) {
                $this->lvl3 = $url_pieces[3];
            }
            if (count($url_pieces) > 4) {
                $this->lvl4 = $url_pieces[4];
            }
        }
    }
