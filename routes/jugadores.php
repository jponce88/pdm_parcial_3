<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->addBodyParsingMiddleware();

$app->post('/api/v1/jugadores', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    if (empty($data)) {
        throw new Exception("No se recibieron datos en el cuerpo de la solicitud.");
    }

    $requiredFields = ['nombres', 'apellidos', 'fechaNacimiento', 'genero', 'posicion', 'idEquipo'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new Exception("Falta el campo '$field' en los datos recibidos.");
            }
        }

    $sql = "INSERT INTO jugadores (nombres, apellidos, fechaNacimiento, genero, posicion, idEquipo) VALUES (:nombres, :apellidos, :fechaNacimiento, :genero, :posicion, :idEquipo)";
    try {
        $db = DB::connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nombres', $data['nombres']);
        $stmt->bindParam(':apellidos', $data['apellidos']);
        $stmt->bindParam(':fechaNacimiento', $data['fechaNacimiento']);
        $stmt->bindParam(':genero', $data['genero']);
        $stmt->bindParam(':posicion', $data['posicion']);
        $stmt->bindParam(':idEquipo', $data['idEquipo']);
        $stmt->execute();
        $data['idJugador'] = $db->lastInsertId();
        $response->getBody()->write(json_encode($data));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500);
    }
    return $response->withHeader('Content-Type', 'application/json');
});

// Ruta para recuperar un jugador en específico
$app->get('/api/v1/jugadores/{id}', function (Request $request, Response $response, $args) {
    $idJugador = (int)$args['id'];
    $sql = "SELECT * FROM jugadores WHERE idJugador = :idJugador";
    try {
        $db = DB::connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':idJugador', $idJugador);
        $stmt->execute();
        $jugadores = $stmt->fetch();
        if ($jugadores) {
            $response->getBody()->write(json_encode($jugadores));
        } else {
            $response->getBody()->write(json_encode(['error' => 'El jugador indicado no fue encontrado']));
            return $response->withStatus(404);
        }
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500);
    }
    return $response->withHeader('Content-Type', 'application/json');
});
