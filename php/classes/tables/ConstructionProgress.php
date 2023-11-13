<?php

/**
 * Класс таблицы CONSTRUCTION_PROGRESS
 */
class ConstructionProgress extends AbstractTable
{

    /**
     * Название (желательно кешированной) таблицы
     * @return string
     */
    static function get_table_cache_name(): string
    {
        return "NV_VIEW_CONSTRUCTION_PROGRESS";
    }

    /**
     * Исходное название таблицы
     * @return string
     */
    static function get_table_name(): string
    {
        return "NV_CONSTRUCTION_PROGRESS";
    }

    /**
     * Сортировка для таблицы
     * @return array
     */
    static function get_order_by(): array
    {
        return ["NV_CONSTRUCTION_PROGRESS_DATE DESC"];
    }

    public ?int $house_id = null;

    public ?string
        $short = null,
        $date_display = null,
        $date_year = null,
        $date_month_ru = null,
        $youtube = null;

    public ?array $image_urls = null;

    public ?DateTime $date = null;


    /**
     * ConstructionProgress constructor.
     * @param array $row
     * @throws Exception
     */
    function __construct(array $row)
    {
        if (!empty($row)) {
            parent::__construct($row);
            $this->house_id = $row["ID_NV_HOUSE"];
            $this->short = $row["NV_CONSTRUCTION_PROGRESS_SHORT"];
            $this->date = new DateTime($row["NV_CONSTRUCTION_PROGRESS_DATE"]);
            $this->date_display = $this->date->format("d.m.Y");
            $this->date_year = $this->date->format("Y");
            $this->date_month_ru = get_month_from_date_ru($row["NV_CONSTRUCTION_PROGRESS_DATE"]);
            $this->image_urls = db_photo_urls_to_array($row["NV_CONSTRUCTION_PROGRESS_IMAGES"]);
            $this->youtube = $row["NV_CONSTRUCTION_PROGRESS_YOUTUBE"];
        }
    }


    /**
     * Получить массив данных для блока "Ход строительства"
     * @param string $eng
     * @param $year
     * @param $month
     * @return array
     * @throws Exception
     */
    public static function get_data_progress_by_eng(string $eng, $year = null, $month = null): array
    {
        $result = [];
        $all_progress = [];
        $construction_progress_arr = self::get_by_eng_array([$eng]);

        if(!empty($construction_progress_arr)){
            //получаем массив со всеми данными (года/месяца/фото)
            foreach ($construction_progress_arr as $progress) {
                $num_mon = getdate($progress->date->getTimestamp())['mon']; // Порядковый номер месяца
                $all_progress[$progress->date_year][$num_mon] =  $progress->image_urls;
            }
            //формируем массив с годами
            $years = array_keys($all_progress);
            sort($years);
            if($year && in_array($year, $years)){
                $select_year = $year;
                //формируем массив всех месяцев выбранного года
                $months = array_keys($all_progress[$year]);
                sort($months);
                if($month && in_array($month, $months)){
                    $select_month = $month;
                    //формируем массив фотографий выбранного месяца
                    $photos = $all_progress[$year][$month];
                }else{
                    $select_month = end($months);
                    //формируем массив фотографий последнего месяца выбранного года
                    $photos = $all_progress[$year][$select_month];
                }
            }else{
                $select_year = end($years);
                //формируем массив всех месяцев последнего года
                $months = array_keys($all_progress[$select_year]);
                sort($months);

                $select_month = end($months);

                //формируем массив фотографий последнего месяца
                $photos = $all_progress[$select_year][$select_month];
            }

            $result = [
                "years" => $years,
                "months" => $months,
                "photos" => $photos,
                "select_year" => $select_year,
                "select_month" => $select_month,
                "all_progress" => $all_progress,
            ];

        }
        return $result;
    }

}
