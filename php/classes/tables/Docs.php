<?

/**
 * Класс таблицы DOCS
 */

class Docs extends AbstractTable
{

    /**
     * Название (желательно кешированной) таблицы
     * @return string
     */
    static function get_table_cache_name(): string
    {
        return "NV_VIEW_DOCS";
    }

    /**
     * Исходное название таблицы
     * @return string
     */
    static function get_table_name(): string
    {
        return "NV_DOCS";
    }

    /**
     * Сортировка для таблицы
     * @return array
     */
    static function get_order_by(): array
    {
        return ["NV_DOCS_SORT DESC"];
    }

    public ?int $house_id = null;

    public ?string
        $full = null,
        $file = null,
        $date_display = null;

    public ?DateTime $date = null;


    /**
     * Docs constructor.
     * @param array $row
     * @throws Exception
     */
    function __construct(array $row)
    {
        if (!empty($row)) {
            parent::__construct($row);
            $this->house_id = $row["ID_NV_HOUSE"];
            $this->full = $row["NV_DOCS_FULL"];
            $this->date = new DateTime($row["NV_DOCS_DATE"]);
            $this->date_display = $this->date->format("d.m.Y");
            $this->file = db_photo_urls_to_one_img($row["NV_DOCS_FILE"], false);
        }
    }
    public static function get_data_docs_by_eng(string $eng): array
    {
        $result = [];
        $docs = self::get_by_eng_array([$eng]);

        if(!empty($docs)){
            foreach ($docs as $doc) {
                $result[] = [
                    "full" => $doc->full,
                    "file" => $doc->file
                ];
            }
        }
        return $result;
    }

}
