<?
    const ROOT = __DIR__ . "/..";


    include ROOT . "/vendor/autoload.php";
    include ROOT . "/php/common.php";

    db_connect();

    if (file_exists(ROOT . "/.env")) {
        Dotenv\Dotenv::createImmutable(ROOT)->load();
    } else {
        $_ENV["MODE"] = "prod";
    }

    if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) || $_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {return;}

    if (empty($_POST["action"]) && empty($_POST["room"])) {return;}

    $result = "";

    // Инициализируем шаблонизатор
    $smarty = init_smarty();

    switch ($_POST["action"]) {
        case "build":

//            $cur_month = "";
//
//            if (in_array($_POST["month"], get_build_months_list($_POST["eng"], $_POST["year"]))) {
//                $cur_month = $_POST["month"];
//            } else {
//                $cur_month = "1";
//            }

            $progress = ConstructionProgress::get_data_progress_by_eng($_POST["eng"], $_POST["year"], $_POST["month"]);


            $builds = [
                "eng" => $_POST["eng"],
                "images" => $progress["photos"],
                "months" => [
                    "current" => $progress["select_month"],
                    "items" => $progress["months"],
                ],
                "years" => [
                    "current" => $progress["select_year"],
                    "items" => $progress["years"],
                ]
            ];

            $result = $smarty->assign("builds", $builds)->display(ROOT."/views/blocks/ajax/build.tpl");
            break;
    }

    if ($result) {
        echo $result;
    }

    db_disconnect();