
<?php

require_once __DIR__ . '/../database.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

try {
    //Estado de la BD
    if ($method === 'GET' && $path === '/api/health') {

        echo json_encode(['status' => 'ok']);

        exit;
    }
    //Peticion de traer usuarios a la BD por metodo GET
    if ($method === 'GET' && $path === '/api/users') {

        $pdo = db();

        $query = $pdo->query(
            'SELECT id, name, email, created_at FROM users ORDER BY id DESC'
        );

        $users = $query->fetchAll();

        echo json_encode($users);

        exit;
    }

    
    if ($method === 'POST' && $path === '/api/users') {
        //Revisa peticion
        $body = json_decode(
            file_get_contents('php://input'),
            true
        );
        //Devuelve error si el JSON no es correcto
        if (!is_array($body) || json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON no válido']);
            exit;
        }
        //Valida los campos
        if (
            !isset($body['name'], $body['email']) ||
            !is_string($body['name']) ||
            !is_string($body['email'])
        ) {
            http_response_code(422);
            echo json_encode(['error' => 'Nombre y correo obligatorios']);
            exit;
        }

        $name = trim($body['name']);
        $email = trim($body['email']);

        if (
            $name === '' ||
            strlen($name) > 100 ||
            strlen($email) > 255 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            http_response_code(422);
            echo json_encode(['error' => 'Nombre o correo no válido']);
            exit;
        }

        $pdo = db();

        try {
            $query = $pdo->prepare(
                'INSERT INTO users (name, email) VALUES (:name, :email)'
            );

            $query->execute([
                'name' => $name,
                'email' => $email
            ]);

            



        } catch (PDOException $error) {

            if (($error->errorInfo[1] ?? null) === 1062) {
                http_response_code(409);
                echo json_encode(['error' => 'El correo ya está registrado']);
                exit;
            }

            throw $error;
        }

        http_response_code(201);

        echo json_encode([
            'id' => (int) $pdo->lastInsertId(),
            'name' => $name,
            'email' => $email
        ]);

        exit;
    }

//-----------------------------------

    if (preg_match('#^/api/users/([0-9]+)$#', $path, $matches)) {

        $id = (int) $matches[1];

        if ($id < 1) {
            http_response_code(400);
            echo json_encode(['error' => 'ID no válido']);
            exit;
        }

        // ACTUALIZAR USUARIO

        if ($method === 'PUT') {

            $body = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (
                !is_array($body) ||
                !isset($body['name'], $body['email']) ||
                !is_string($body['name']) ||
                !is_string($body['email'])
            ) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos no válidos']);
                exit;
            }

            $name = trim($body['name']);
            $email = trim($body['email']);

            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Nombre o correo no válido']);
                exit;
            }

            $pdo = db();

            $query = $pdo->prepare(
                'SELECT id FROM users WHERE id = :id'
            );

            $query->execute(['id' => $id]);

            if (!$query->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Usuario no encontrado']);
                exit;
            }

            try {

                $query = $pdo->prepare(
                    'UPDATE users SET name = :name, email = :email WHERE id = :id'
                );

                $query->execute([
                    'name' => $name,
                    'email' => $email,
                    'id' => $id
                ]);

            } catch (PDOException $error) {

                if (($error->errorInfo[1] ?? null) === 1062) {
                    http_response_code(409);
                    echo json_encode(['error' => 'El correo ya está registrado']);
                    exit;
                }

                throw $error;
            }

            echo json_encode([
                'id' => $id,
                'name' => $name,
                'email' => $email
            ]);

            exit;
        }

        // ELIMINAR USUARIO

        if ($method === 'DELETE') {

            $pdo = db();

            $query = $pdo->prepare(
                'DELETE FROM users WHERE id = :id'
            );

            $query->execute(['id' => $id]);

            if ($query->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Usuario no encontrado']);
                exit;
            }

            http_response_code(204);
            exit;
        }
    }



    http_response_code(404);

    echo json_encode(['error' => 'Ruta no encontrada']);

} catch (Throwable $error) {

    http_response_code(500);

    echo json_encode(['error' => 'Error interno del servidor']);

}
