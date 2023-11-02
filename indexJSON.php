<?php
// Проверяем, загружено ли расширение XSL
extension_loaded('xsl') or die('XSL extension not loaded');

// Загружаем XML и XSL файлы
$xml = new DOMDocument;
$xml->load('index.json'); // Загружаем JSON

$xsl = new DOMDocument;
$xsl->load('index.xsl');

// Получаем параметры из GET запроса
$stateFilter = isset($_GET['stateFilter']) ? $_GET['stateFilter'] : '';
$personFilter = isset($_GET['personFilter']) ? $_GET['personFilter'] : '';

// Создаем XSLT процессор и передаем параметры
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);

$proc->setParameter('', 'stateFilter', $stateFilter);
$proc->setParameter('', 'personFilter', $personFilter);

// Загружаем JSON и преобразуем его в XML
$json = file_get_contents('index.json');
$xml = simplexml_load_string($json);

// Если нажата кнопка "Lisa"
if (isset($_POST['lisa'])) {
    // Валидируем POST данные
    $requiredFields = ['number', 'name', 'state', 'cost', 'responsible_person', 'additional_information'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            // Выводим сообщение об ошибке
            echo 'Please fill in all of the required fields.';
            return;
        }
    }

    // Получаем данные из POST запроса
    $number = $_POST['number'];
    $name = $_POST['name'];
    $state = $_POST['state'];
    $cost = $_POST['cost'];
    $responsiblePerson = $_POST['responsible_person'];
    $additionalInformation = $_POST['additional_information'];

    // Добавляем данные в XML документ
    try {
        $xml = simplexml_load_file('index.json');
        $newAsset = $xml->addChild('asset');

        $newAsset->addChild('number', $number);
        $newAsset->addChild('name', $name);
        $newAsset->addChild('state', $state);
        $newAsset->addChild('cost', $cost);
        $newAsset->addChild('responsible_person', $responsiblePerson);
        $newAsset->addChild('additional_information', $additionalInformation);

        $json = json_encode($xml, JSON_PRETTY_PRINT);
        file_put_contents('index.json', $json);
    } catch (Exception $e) {
        // Выводим сообщение об ошибке
        echo 'An error occurred while adding the data to the JSON document: ' . $e->getMessage();
    }

    // Перенаправляем пользователя обратно на главную страницу
    header('Location: index.php');
}

// Если нажата кнопка "Delete"
if (isset($_POST['delete'])) {
    $rowIndex = isset($_POST['rowIndex']) ? intval($_POST['rowIndex']) : -1;

    if ($rowIndex >= 0) {
        // Загружаем JSON файл
        $json = file_get_contents('index.json');
        $data = json_decode($json, true);

        // Удаляем выбранную строку из JSON
        unset($data['asset'][$rowIndex]);

        // Сохраняем обновленный JSON
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents('index.json', $json);
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
    <a href="index.php" class="navbar-item">Index PHP</a>
    <a href="index.json" class="navbar-item">Index JSON</a>
</nav>
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
        <?php
        if ($data) {
            foreach ($data as $index => $row) {
                echo '<tr>';
                echo '<td>' . $row['number'] . '</td>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td>' . $row['state'] . '</td>';
                echo '<td>' . $row['cost'] . '</td>';
                echo '<td>' . $row['responsible_person'] . '</td>';
                echo '<td>' . $row['additional_information'] . '</td>';
                echo '<td><input type="submit" name="delete" value="Delete" data-row-index="' . $index . '"></td>';
                echo '</tr>';
            }
        }
        ?>
    </form>
    </tbody>
</table>
</body>
</html>
