<!DOCTYPE html>
<html>
<head>
    <title>Edit Clock</title>
    <style>
        form {
            margin-bottom: 16px;
        }
        label {
            display: inline-block;
            width: 100px;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Edit Clock</h2>
    <?php
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        echo "<p class='error'>Invalid clock ID</p>";
    } else {
        $apiUrl = "http://localhost/ClocksAPI.php/clocks/{$id}";
        $clock = json_decode(file_get_contents($apiUrl), true);
        if (!$clock) {
            echo "<p class='error'>Clock not found</p>";
        } else {
            $nombre = $clock['nombre'] ?? '';
            $marca = $clock['marca'] ?? '';
            $precio = $clock['precio'] ?? '';
            $material = $clock['material'] ?? '';
            $nombreErr = $marcaErr = $precioErr = $materialErr = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $isValid = true;
                if (empty($_POST['nombre'])) {
                    $nombreErr = 'Name is required';
                    $isValid = false;
                } else {
                    $nombre = $_POST['nombre'];
                }
                if (empty($_POST['marca'])) {
                    $marcaErr = 'Brand is required';
                    $isValid = false;
                } else {
                    $marca = $_POST['marca'];
                }
                if (empty($_POST['precio'])) {
                    $precioErr = 'Price is required';
                    $isValid = false;
                } else {
                    $precio = $_POST['precio'];
                    if (!is_numeric($precio)) {
                        $precioErr = 'Price must be a numeric value';
                        $isValid = false;
                    }
                }
                if (empty($_POST['material'])) {
                    $materialErr = 'Material is required';
                    $isValid = false;
                } else {
                    $material = $_POST['material'];
                }
                if ($isValid) {
                    // Update the clock data via the backend API
                    $updateUrl = "http://localhost/ClocksAPI.php/clocks/{$id}";
                    $data = [
                        'nombre' => $nombre,
                        'marca' => $marca,
                        'precio' => $precio,
                        'material' => $material
                    ];
                    $options = [
                        'http' => [
                            'method' => 'PUT',
                            'header' => 'Content-type: application/x-www-form-urlencoded',
                            'content' => http_build_query($data)
                        ]
                    ];
                    $context = stream_context_create($options);
                    $result = file_get_contents($updateUrl, false, $context);
                    if ($result) {
                        echo "<p class='success'>Clock updated successfully!</p>";
                    } else {
                        echo "<p class='error'>Failed to update clock</p>";
                    }
                }
            }
    ?>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>">
        <label for="nombre">Name:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
        <span class="error"><?php echo $nombreErr; ?></span>
        <label for="marca">Brand:</label>
        <input type="text" name="marca" id="marca" value="<?php echo htmlspecialchars($marca); ?>" required>
        <span class="error"><?php echo $marcaErr; ?></span>
        <label for="precio">Price:</label>
        <input type="number" name="precio" id="precio" value="<?php echo htmlspecialchars($precio); ?>" required>
        <span class="error"><?php echo $precioErr; ?></span>
        <label for="material">Material:</label>
        <input type="text" name="material" id="material" value="<?php echo htmlspecialchars($material); ?>" required>
        <span class="error"><?php echo $materialErr; ?></span>
        <button type="submit">Update Clock</button>
    </form>
    <?php
        }
    }
    ?>
    <a href="index.php">Back to Clocks</a>
</body>
</html>
