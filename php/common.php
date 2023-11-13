<?
    mb_internal_encoding("UTF-8");
    date_default_timezone_set("Europe/Moscow");
    Dotenv\Dotenv::createImmutable(ROOT)->load();
    session_start();

    const AESKEY = "##########";

    /**
     * Автоматически загружает классы в директории и поддиректории /php/classes
     * @param $class_name
     */
    function autoload_class($class_name)
    {
        $ext = ".php";
        $folder = ROOT . "/php/classes";
        $class_name = str_replace("_" , DIRECTORY_SEPARATOR, $class_name);

        $file_name = $folder . DIRECTORY_SEPARATOR . $class_name;
        $path = $file_name . $ext;

        if (is_readable($file_name . $ext)) {
            include $file_name . $ext;
            return;
        }

        $sub_folders = new DirectoryIterator($folder);

        foreach ($sub_folders as $sub_folder) {
            if ($sub_folder->isDir() && !$sub_folder->isDot()) {
                $file_name = $folder . DIRECTORY_SEPARATOR . $sub_folder . DIRECTORY_SEPARATOR . $class_name;
                $path = $file_name . $ext;

                if (is_readable($path)) {
                    include $path;
                    return;
                }
            }
        }
    }

    spl_autoload_register("autoload_class");

    /**
     * Автоматически подключает php файлы в директории
     * @param string $dir
     */
    function auto_require_dir(string $dir)
    {
        foreach (scandir($dir) as $filename) {
            $path = $dir . "/" . $filename;
            if (is_file($path)) {
                require $path;
            }
        }
    }

    auto_require_dir(ROOT . "/php/functions");
