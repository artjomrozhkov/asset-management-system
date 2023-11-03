<?php
// Путь к файлу JSON с данными
$jsonFile = 'index.json';

// Инициализация массива для хранения данных об активах
$assets = [];

// Загрузка существующих JSON-данных, если они доступны
if (file_exists($jsonFile)) {
    $json = file_get_contents($jsonFile);
    $data = json_decode($json, true);

    // Проверка наличия ключа "asset"
    if (isset($data['asset'])) {
        $assets = $data['asset'];
    }
}

// Обработка данных, отправленных через POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lisa'])) {
        // Валидация данных из формы
        $requiredFields = ['number', 'name', 'state', 'cost', 'responsible_person', 'additional_information'];
        $formData = [];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                echo 'Please fill in all of the required fields.';
                return;
            }

            $formData[$field] = $_POST[$field];
        }

        // Добавление данных в массив активов
        $assets[] = $formData;

        // Перепаковка данных с ключом "asset"
        $data['asset'] = $assets;

        // Сохранение обновленных данных в JSON-файл
        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));

        // Перенаправление пользователя обратно на главную страницу
        header('Location: indexJSON.php');
        exit();
    }
    if (isset($_POST['rowIndex'])) {
        $rowIndex = isset($_POST['rowIndex']) ? intval($_POST['rowIndex']) : -1;

        if ($rowIndex >= 0 && isset($assets[$rowIndex])) {
            // Удаление выбранной строки из массива активов
            unset($assets[$rowIndex]);

            // Перепаковка данных с ключом "asset"
            $data['asset'] = array_values($assets);

            // Сохранение обновленных данных в JSON-файл
            file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
        }

        // Перенаправление пользователя обратно на главную страницу
        header('Location: indexJSON.php');
        exit();
    }

    // Фильтр по состоянию (State)
    $stateFilter = isset($_POST['stateFilter']) ? $_POST['stateFilter'] : '';
    if (!empty($stateFilter)) {
        $filteredAssets = [];
        foreach ($assets as $asset) {
            if ($asset['state'] === $stateFilter) {
                $filteredAssets[] = $asset;
            }
        }
        $assets = $filteredAssets;
    }

    // Фильтр по ответственному лицу (Responsible Person)
    $personFilter = isset($_POST['personFilter']) ? $_POST['personFilter'] : '';
    if (!empty($personFilter)) {
        $filteredAssets = [];
        foreach ($assets as $asset) {
            if (stripos($asset['responsible_person'], $personFilter) !== false) {
                $filteredAssets[] = $asset;
            }
        }
        $assets = $filteredAssets;
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="index.css"/>
</head>
<body>
<nav class="navbar">
    <a href="index.php" class="navbar-item">Index XML</a>
    <a href="indexJSON.php" class="navbar-item">Index JSON</a>
</nav>
<h1>Index JSON</h1>
<form method="POST">
    <center>
        <label for="stateFilter">Filter by State:</label>
        <select id="stateFilter" name="stateFilter">
            <option value="">All</option>
            <option value="New">New</option>
            <option value="Used">Used</option>
        </select>
        <input type="submit" value="OK" />

        <label for="personFilter">Filter by Responsible Person:</label>
        <input type="text" id="personFilter" name="personFilter" placeholder="Search for Responsible Person..." />
        <input type="submit" value="OK" />
    </center>
</form>
<table border="1">
    <thead>
    <tr>
        <th>Number</th>
        <th>Name</th>
        <th>State</th>
        <th>Cost</th>
        <th>Responsible Person</th>
        <th>Additional Information</th>
        <th>Edit</th>
    </tr>
    </thead>
    <tbody>
    <form method="POST">
        <tr>
            <td>
                <input type="text" id="number" name="number" />
            </td>
            <td>
                <input type="text" id="name" name="name" />
            </td>
            <td>
                <select id="state" name="state">
                    <option value="New">New</option>
                    <option value="Used">Used</option>
                </select>
            </td>
            <td>
                <input type="text" id="cost" name="cost" />
            </td>
            <td>
                <input type="text" id="responsible_person" name="responsible_person" />
            </td>
            <td>
                <input type="text" id="additional_information" name="additional_information" />
            </td>
            <td>
                <input type="submit" id="lisa" name="lisa" value="Lisa" />
            </td>
        </tr>
    </form>
    <?php
    // Отображение данных из массива активов
    foreach ($assets as $key => $item) {
        echo "<tr>";
        echo "<td>{$item['number']}</td>";
        echo "<td>{$item['name']}</td>";
        echo "<td>{$item['state']}</td>";
        echo "<td>{$item['cost']}</td>";
        echo "<td>{$item['responsible_person']}</td>";
        echo "<td>{$item['additional_information']}</td>";
        echo '<td><form method="POST"><input type="hidden" name="rowIndex" value="' . $key . '"/>' .
            '<input type="submit" name="delete" value="Delete" /></form></td>';
        echo "</tr>";
    }
    ?>

    </tbody>
</table>
</body>
</html>
