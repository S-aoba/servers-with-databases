<?php

use Helpers\ValidationHelper;
use Models\ComputerPart;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Database\DataAccess\DAOFactory;
use Helpers\Authenticate;
use Models\User;
use Response\FlashData;
use Response\Render\JSONRenderer;
use Response\Render\RedirectRenderer;
use Types\ValueType;

return [
    'register'=>function(): HTTPRenderer{
        if(Authenticate::isLoggedIn()){
            FlashData::setFlashData('error', 'Cannot register as you are already logged in.');
            return new RedirectRenderer('random/part');
        }

        return new HTMLRenderer('page/register');
    },
    'form/register' => function(): HTTPRenderer {
        // ユーザが現在ログインしている場合、登録ページにアクセスすることはできません。
        if(Authenticate::isLoggedIn()){
            FlashData::setFlashData('error', 'Cannot register as you are already logged in.');
            return new RedirectRenderer('random/part');
        }

        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'username' => ValueType::STRING,
                'email' => ValueType::EMAIL,
                'password' => ValueType::PASSWORD,
                'confirm_password' => ValueType::PASSWORD,
                'company' => ValueType::STRING,
            ];

            $userDao = DAOFactory::getUserDAO();

            // シンプルな検証
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if($validatedData['confirm_password'] !== $validatedData['password']){
                FlashData::setFlashData('error', 'Invalid Password!');
                return new RedirectRenderer('register');
            }

            // Eメールは一意でなければならないので、Eメールがすでに使用されていないか確認します
            if($userDao->getByEmail($validatedData['email'])){
                FlashData::setFlashData('error', 'Email is already in use!');
                return new RedirectRenderer('register');
            }

            // 新しいUserオブジェクトを作成します
            $user = new User(
                username: $validatedData['username'],
                email: $validatedData['email'],
                company: $validatedData['company']
            );

            // データベースにユーザーを作成しようとします
            $success = $userDao->create($user, $validatedData['password']);

            if (!$success) throw new Exception('Failed to create new user!');

            // ユーザーログイン
            Authenticate::loginAsUser($user);

            FlashData::setFlashData('success', 'Account successfully created.');
            return new RedirectRenderer('random/part');
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Invalid Data.');
            return new RedirectRenderer('register');
        } catch (Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'An error occurred.');
            return new RedirectRenderer('register');
        }
    },
    'logout'=>function(): HTTPRenderer{
        if(!Authenticate::isLoggedIn()){
            FlashData::setFlashData('error', 'Already logged out.');
            return new RedirectRenderer('random/part');
        }

        Authenticate::logoutUser();
        FlashData::setFlashData('success', 'Logged out.');
        return new RedirectRenderer('random/part');
    },
    'login'=>function(): HTTPRenderer{
        if(Authenticate::isLoggedIn()){
            FlashData::setFlashData('error', 'You are already logged in.');
            return new RedirectRenderer('random/part');
        }

        return new HTMLRenderer('page/login');
    },
    'form/login'=>function(): HTTPRenderer{
        if(Authenticate::isLoggedIn()){
            FlashData::setFlashData('error', 'You are already logged in.');
            return new RedirectRenderer('random/part');
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'email' => ValueType::EMAIL,
                'password' => ValueType::STRING,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            Authenticate::authenticate($validatedData['email'], $validatedData['password']);

            FlashData::setFlashData('success', 'Logged in successfully.');
            return new RedirectRenderer('update/part');
        } catch (AuthenticationFailureException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Failed to login, wrong email and/or password.');
            return new RedirectRenderer('login');
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Invalid Data.');
            return new RedirectRenderer('login');
        } catch (Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'An error occurred.');
            return new RedirectRenderer('login');
        }
    },
    'random/part'=>function(): HTTPRenderer{
        $partDao = DAOFactory::getComputerPartDAO();
        $part = $partDao->getRandom();

        if($part === null) throw new Exception('No parts are available!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },
    'parts'=>function(): HTTPRenderer{
        // IDの検証
        $id = ValidationHelper::integer($_GET['id']??null);

        $partDao = DAOFactory::getComputerPartDAO();
        $part = $partDao->getById($id);

        if($part === null) throw new Exception('Specified part was not found!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },
    'update/part' => function(): HTMLRenderer {
        $part = null;
        $partDao = DAOFactory::getComputerPartDAO();
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

            $partDao = DAOFactory::getComputerPartDAO();

            // 入力に対する単純な認証です。実際のシナリオでは、要件を満たす完全な認証が必要になることがあります。
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if(isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

            // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋スプレッド演算子を用いて、配列の要素を別々の変数や関数の引数として展開
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
            // エラーログはPHPのログやstdoutから見ることができます。
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
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