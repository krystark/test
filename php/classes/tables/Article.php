<?

/**
 * Класс таблицы Article
 */

class Article extends AbstractTable
{

    /**
     * Название (желательно кешированной) таблицы
     * @return string
     */
    static function get_table_cache_name(): string
    {
        return "NV_ARTICLE";
    }

    /**
     * Исходное название таблицы
     * @return string
     */
    static function get_table_name(): string
    {
        return "NV_ARTICLE";
    }

    /**
     * Сортировка для таблицы
     * @return array
     */
    static function get_order_by(): array
    {
        return ["NV_ARTICLE_SORT DESC"];
    }

    const DEFAULT_IMAGE = "/img/og-image.jpg";

    public ?DateTime $date = null;

    public ?string
        $title = null,
        $preview = null,
        $description = null,
        $image = null,
        $eng = null,
        $url = null,
        $date_display = null;

    public ?bool $is_active = null;


    /**
     * Article constructor
     * @param array $row
     * @throws Exception
     */
    public function __construct(array $row)
    {
        if (!empty($row)) {
            parent::__construct($row);
            $this->date = new DateTime($row["NV_ARTICLE_DATE"]);
            $this->is_active = $row["NV_ARTICLE_IS_ACTIVE"] == "Да";
            $this->title = $row["NV_ARTICLE_TITLE"];
            $this->preview = $row["NV_ARTICLE_PREVIEW"];
            $this->description = quill_decode($row["NV_ARTICLE_DESCRIPTION"]);
            $this->image = db_photo_urls_to_one_img($row["NV_ARTICLE_IMAGE"]) ?: self::DEFAULT_IMAGE;
            $this->eng = $row["NV_ARTICLE_ENG"];
            $this->url = "/blog/{$this->eng}";
            $this->date_display = $this->date->format("d.m.Y");
        }
    }

}
