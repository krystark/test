<?

/**
 * Класс для контроллеров, у которых есть пагинация
 */
class Paginator
{

    public int $count = 0;
    public int $limit = 12;
    public int $page = 1;
    public int $total_pages = 1;
    public int $offset = 0;
    public string $url_for_page;

    /**
     * Paginator constructor
     * @param int $count
     * @param int $limit
     */
    function __construct(int $count, int $limit = 12)
    {
        $this->limit = $limit;
        $url = remove_query_param(get_current_url(), "page");

        if (!empty($_GET["page"]) && intval($_GET["page"])) {
            $this->page = $_GET["page"];
        }
        $this->count = $count;
        $url_query = parse_url($url, PHP_URL_QUERY);
        if ($url_query) {
            $this->url_for_page = "$url&page=";
        } else {
            $this->url_for_page = "$url?page=";
        }
        $this->offset = ($this->page - 1) * $this->limit;
        $this->total_pages = ceil($this->count / $this->limit);
        if (($this->page > 1) && ($this->page > $this->total_pages)) {
            redirect($this->url_for_page . $this->total_pages);
        }
    }
}
