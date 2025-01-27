<?php

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Models\ComputerPart;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Database\DataAccess\Implementations\ComputerPartDAOImpl;
use Types\ValueType;

return [
    'random/part'=>function(): HTTPRenderer{
        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getRandom();

        if($part === null) throw new Exception('No parts are available!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },
    'parts'=>function(): HTTPRenderer{
        // IDの検証
        $id = ValidationHelper::integer($_GET['id']??null);

        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getById($id);

        if($part === null) throw new Exception('Specified part was not found!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },
    'update/part' => function(): HTMLRenderer {
        $part = null;
        $partDao = new ComputerPartDAOImpl();
        if(isset($_GET['id'])){
            $id = ValidationHelper::integer($_GET['id']);
            $part = $partDao->getById($id);
        }
        return new HTMLRenderer('component/update-computer-part',['part'=>$part]);
    },
    'form/update/part' => function(): HTTPRenderer {
        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method!');
            }

            $required_fields = [
                'name' => ValueType::STRING,
                'type' => ValueType::STRING,
                'brand' => ValueType::STRING,
                'modelNumber' => ValueType::STRING,
                'releaseDate' => ValueType::DATE,
                'description' => ValueType::STRING,
                'performanceScore' => ValueType::INT,
                'marketPrice' => ValueType::FLOAT,
                'rsm' => ValueType::FLOAT,
                'powerConsumptionW' => ValueType::FLOAT,
                'lengthM' => ValueType::FLOAT,
                'widthM' => ValueType::FLOAT,
                'heightM' => ValueType::FLOAT,
                'lifespan' => ValueType::INT,
            ];

            $partDao = new ComputerPartDAOImpl();

            // 入力に対する単純なバリデーション。実際のシナリオでは、要件を満たす完全なバリデーションが必要になることがあります。
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if(isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

            // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋アンパッキング
            $part = new ComputerPart(...$validatedData);

            error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

            // 新しい部品情報でデータベースの更新を試みます。
            // 別の方法として、createOrUpdateを実行することもできます。
            if(isset($validatedData['id'])) $success = $partDao->update($part);
            else $success = $partDao->create($part);

            if (!$success) {
                throw new Exception('Database update failed!');
            }

            return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
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

        return new HTMLRenderer('component/parts-by-type', ['parts'=>$parts]);
    },

    'random/computer' => function(): HTTPRenderer {
        // URL 例：/random/computer
        $computer = DatabaseHelper::getRandomComputer();

        return new HTMLRenderer('component/random-computer', ['computer' => $computer]);
    },

    'parts/newest' => function() : HTTPRenderer {
        // page, perpage, url = /parts/newest?page=2&perpage=10
        $page = ValidationHelper::integer($_GET['page'] ?? 1);
        $perpage = ValidationHelper::integer($_GET['perpage'] ?? 10);

        $parts = DatabaseHelper::getNewestComputerParts($page, $perpage);

        return new HTMLRenderer('component/newest-computer-parts',['parts' => $parts]);
    },

    'parts/performance' => function(): HTTPRenderer {
        // order, type,  url = /parts/performance?order=desc&type=CPU
        $type = ValidationHelper::type($_GET['type'] ?? null);
        $order = ValidationHelper::order($_GET['order'] ?? 'ASC');

        $parts = DatabaseHelper::getTopAndBottom50ComputerParts($order, $type);

        return new HTMLRenderer('component/performance-parts', ['parts' => $parts]);
    }
];