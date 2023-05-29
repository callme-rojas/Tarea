<!DOCTYPE html>
<html>
<head>
    <title>Clocks Management</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        form {
            margin-bottom: 16px;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>RELOJES</h2>
    <?php
    $nombreErr = $marcaErr = $precioErr = $materialErr = '';
    $nombre = $marca = $precio = $material = '';
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
            // Add your code to send the data to the backend API and handle the response
            // For simplicity, we'll just display a success message here
            echo "<p class='success'>Clock added successfully!</p>";
            // Reset form values
            $nombre = $marca = $precio = $material = '';
        }
    }
    ?>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
        <span class="error">
            <?php echo $nombreErr; ?>
        </span>
        <label for="marca">Marca:</label>
        <input type="text" name="marca" id="marca" value="<?php echo htmlspecialchars($marca); ?>" required>
        <span class="error">
            <?php echo $marcaErr; ?>
        </span>
        <label for="precio">Precio:</label>
        <input type="number" name="precio" id="precio" value="<?php echo htmlspecialchars($precio); ?>" required>
        <span class="error">
            <?php echo $precioErr; ?>
        </span>
        <label for="material">Material:</label>
        <input type="text" name="material" id="material" value="<?php echo htmlspecialchars($material); ?>" required>
        <span class="error">
            <?php echo $materialErr; ?>
        </span>
        <button type="submit">Guardar</button>
    </form>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Precio</th>
            <th>Material</th>
            <th>Action</th>
        </tr>
        <?php
        // Fetch clock data from the backend API
        $apiUrl = 'http://localhost/ClocksAPI.php/clocks';
        $clocks = json_decode(file_get_contents($apiUrl), true);
        foreach ($clocks as $clock) {
            echo "<tr>";
            echo "<td>{$clock['id']}</td>";
            echo "<td>{$clock['nombre']}</td>";
            echo "<td>{$clock['marca']}</td>";
            echo "<td>{$clock['precio']}</td>";
            echo "<td>{$clock['material']}</td>";
            echo "<td>
                    <a href='ClocksAPI.php/clocks/{$clock['id']}' onclick='return confirm(\"Are you sure you want to delete this clock?\")'>Delete</a> |
                    <a href='edit.php?id={$clock['id']}'>Edit</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>