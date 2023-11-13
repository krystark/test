<?php

/**
 * Класс для фильтрации Статей
 */
class ArticleFilter extends Filter
{

    public ?bool $is_active = null;

    /**
     * Получает условия AND для where части запроса
     * @return string
     */
    function get_where(): string
    {
        $query = "";

        //Фильтр "Актуально?"
        if ($this->is_active) {
            $query .= " AND (NV_ARTICLE_IS_ACTIVE = 'Да')";
        }


        return $query;
    }

    static function get_by_get(array $get)
    {
        // TODO: Implement get_by_get() method.
    }
}