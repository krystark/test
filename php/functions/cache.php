<?
    /**
     * Очистка временных файлов в папке /cache/
     * @return void
     */
    function clearFileCacheSite()
    {
        $folder_path = ROOT . "/cache/pages";
        $files = glob($folder_path . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $folder_path = ROOT . "/cache/templates";
        $files = glob($folder_path . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Очистка всего кеша
     * @return void
     */
    function updateCache()
    {
        clearFileCacheSite();
        db_cache_flush();
    }
