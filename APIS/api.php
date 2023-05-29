<?php
class ClocksAPI
{
    private $db;

    public function __construct()
    {
        $this->db = new mysqli('localhost', 'franco', '12345', 'relojes');
        $this->routes();
    }

    private function routes()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['PATH_INFO'] ?? '/';

        if ($method === 'GET' && $path === '/clocks') {
            $this->getClocks();
        } elseif ($method === 'GET' && preg_match('/\/clocks\/(\d+)/', $path, $matches)) {
            $clockId = $matches[1];
            $this->getClock($clockId);
        } elseif ($method === 'POST' && $path === '/clocks') {
            $this->createClock();
        } elseif ($method === 'PUT' && preg_match('/\/clocks\/(\d+)/', $path, $matches)) {
            $clockId = $matches[1];
            $this->updateClock($clockId);
        } elseif ($method === 'DELETE' && preg_match('/\/clocks\/(\d+)/', $path, $matches)) {
            $clockId = $matches[1];
            $this->deleteClock($clockId);
        } else {
            $this->notFound();
        }
    }

    private function getClocks()
    {
        $sql = "SELECT * FROM clocks";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            $clocks = [];
            while ($row = $result->fetch_assoc()) {
                $clocks[] = $row;
            }
            $this->sendResponse($clocks, 200);
        } else {
            $this->sendResponse([], 200);
        }
    }

    private function getClock($clockId)
    {
        $clockId = $this->db->real_escape_string($clockId);
        $sql = "SELECT * FROM clocks WHERE id = '$clockId'";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            $clock = $result->fetch_assoc();
            $this->sendResponse($clock, 200);
        } else {
            $this->sendResponse(['error' => 'Clock not found'], 404);
        }
    }

    private function createClock()
    {
        $data = $_POST;
        $nombre = $data['nombre'] ?? '';
        $marca = $data['marca'] ?? '';
        $precio = $data['precio'] ?? '';
        $material = $data['material'] ?? '';
        $nombre = $this->db->real_escape_string($nombre);
        $marca = $this->db->real_escape_string($marca);
        $precio = $this->db->real_escape_string($precio);
        $material = $this->db->real_escape_string($material);
        $sql = "INSERT INTO clocks (nombre, marca, precio, material) VALUES ('$nombre', '$marca', '$precio', '$material')";

        if ($this->db->query($sql) === TRUE) {
            $clockId = $this->db->insert_id;
            $clock = ['id' => $clockId, 'nombre' => $nombre, 'marca' => $marca, 'precio' => $precio, 'material' => $material];
            $this->sendResponse($clock, 201);
        } else {
            $this->sendResponse(['error' => 'Failed to create clock'], 500);
        }
    }

    private function updateClock($clockId)
    {
        $data = $_POST;
        $nombre = $data['nombre'] ?? '';
        $marca = $data['marca'] ?? '';
        $precio = $data['precio'] ?? '';
        $material = $data['material'] ?? '';
        $clockId = $this->db->real_escape_string($clockId);
        $nombre = $this->db->real_escape_string($nombre);
        $marca = $this->db->real_escape_string($marca);
        $precio = $this->db->real_escape_string($precio);
        $material = $this->db->real_escape_string($material);
        $sql = "UPDATE clocks SET nombre = '$nombre', marca = '$marca', precio = '$precio', material = '$material' WHERE id = '$clockId'";

        if ($this->db->query($sql) === TRUE) {
            $clock = ['id' => $clockId, 'nombre' => $nombre, 'marca' => $marca, 'precio' => $precio, 'material' => $material];
            $this->sendResponse($clock, 200);
        } else {
            $this->sendResponse(['error' => 'Failed to update clock'], 500);
        }
    }

    private function deleteClock($clockId)
    {
        $clockId = $this->db->real_escape_string($clockId);
        $sql = "DELETE FROM clocks WHERE id = '$clockId'";

        if ($this->db->query($sql) === TRUE) {
            $this->sendResponse(['message' => 'Clock deleted'], 200);
        } else {
            $this->sendResponse(['error' => 'Failed to delete clock'], 500);
        }
    }

    private function notFound()
    {
        $this->sendResponse(['error' => 'Endpoint not found'], 404);
    }

    private function sendResponse($data, $statusCode)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}

$api = new ClocksAPI();
