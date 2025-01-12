<?php

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    'random/part'=>function(): HTTPRenderer{
        $part = DatabaseHelper::getRandomComputerPart();

        return new HTMLRenderer('component/random-part', ['part'=>$part]);
    },
    'parts'=>function(): HTTPRenderer{
        // IDの検証
        $id = ValidationHelper::integer($_GET['id']??null);

        $part = DatabaseHelper::getComputerPartById($id);
        return new HTMLRenderer('component/parts', ['part'=>$part]);
    },
    'api/random/part'=>function(): HTTPRenderer{
        $part = DatabaseHelper::getRandomComputerPart();
        return new JSONRenderer(['part'=>$part]);
    },
    'api/parts'=>function(): JSONRenderer{
        $id = ValidationHelper::integer($_GET['id']??null);
        $part = DatabaseHelper::getComputerPartById($id);
        return new JSONRenderer(['part'=>$part]);
    },

    // 課題: クライアントサーバでのレンダリング(6)
    'types' => function(): HTTPRenderer {
        // types, page, perpage, url = /types?type=CPU&page=2&perpage=10
        $type = ValidationHelper::type($_GET['type'] ?? null);
        $page = ValidationHelper::integer($_GET['page'] ?? 1);
        $perpage = ValidationHelper::integer($_GET['perpage'] ?? 10);

        $parts = DatabaseHelper::getComputerPartByType($type, $page, $perpage);

        return new HTMLRenderer('component/partsByType', ['parts'=>$parts]);
    },

    'random/computer' => function(): HTTPRenderer {
        // URL 例：/random/computer
        $computer = DatabaseHelper::getRandomComputer();

        return new HTMLRenderer('component/randomComputer', ['computer' => $computer]);
    }
];