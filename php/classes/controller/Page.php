<?

    /**
     * Класс данных для страницы
     */
    abstract class Page
    {
        public int $code;
        public Seo $seo;
        public string
            $eng,
            $scripts,
            $styles,
            $view;

        /**
         * @param int $code
         * @param string $eng
         * @param Url $url
         * @throws Exception
         */
        public function __construct(int $code, string $eng, Url $url)
        {
            $this->eng = $eng;
            $this->code = $code;
            $this->scripts = external_scripts(actual_bundle_path("dist/js", $eng));
            $this->styles = styles_by_mode($eng);
            $this->view = "pages/$eng.tpl";
            $this->seo = $this->fill_data($url);
        }

        /**
         * Возвращает код ответа и основной файл верстки страницы
         * @param Url $url
         * @return Page
         * @throws Exception
         */
        public static function get_page(Url $url): Page
        {
            try {
                // Страница не найдена
                if ($url->lvl1 == "404") {
                    return Error404Page::code404();
                } elseif ($url->lvl1 == "search") {
                    return SearchPage::code200($url);
                } elseif ($url->lvl1 == "blog") {
                    if (!empty($url->lvl2)) {
                        return BlogDetailPage::code200($url->lvl2, $url);
                    } else {
                        return BlogPage::code200($url);
                    }
                } elseif ($url->lvl1 == "job") {
                    return JobPage::code200($url);
                } elseif ($url->lvl1 == "stock") {
                    return PromotionPage::code200($url);
                } elseif (!$url->lvl1) {
                    return HomePage::code200($url);
                } elseif ($url->lvl1 == "proekty") {
                    if ($url->lvl3) {
                        return DetailPage::code200($url);
                    } elseif (file_exists(ROOT . "/views/pages/proekty/{$url->lvl2}.tpl")) {
                        return ObjectPage::code200($url);
                    } else {
                        return Error404Page::code404();
                    }
                } elseif (file_exists(ROOT . "/views/pages/{$url->lvl1}.tpl")) {
                    return Lvl1Page::code200($url);
                } else {
                    // Страница не найдена
                    return Error404Page::code404();
                }
            } catch (Exception $e) {
                return Error415Page::code415();
            }
        }

        /**
         * Заполняем данные и возвращаем класс SEO
         * @param Url $url
         * @return Seo
         * @throws Exception
         */
        protected function fill_data(Url $url): Seo
        {
            return Seo::for_page($url);
        }
    }
