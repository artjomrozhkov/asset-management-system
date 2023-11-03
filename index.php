<?php
extension_loaded('xsl') or die('XSL extension not loaded');

$xml = new DOMDocument;
$xml->load('index.xml');

$xsl = new DOMDocument;
$xsl->load('index.xsl');

$jsonFile = 'index.json';
$jsonData = [];

$stateFilter = isset($_GET['stateFilter']) ? $_GET['stateFilter'] : '';
$personFilter = isset($_GET['personFilter']) ? $_GET['personFilter'] : '';

$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);

$proc->setParameter('', 'stateFilter', $stateFilter);
$proc->setParameter('', 'personFilter', $personFilter);

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

    // Добавление данных в XML-документ
    try {
        $xmlFile = new DOMDocument();
        $xmlFile->load('index.xml');

        $newAsset = $xmlFile->createElement('asset');
        foreach ($formData as $key => $value) {
            $newElement = $xmlFile->createElement($key, $value);
            $newAsset->appendChild($newElement);
        }

        $xmlFile->documentElement->appendChild($newAsset);
        $xmlFile->save('index.xml');
    } catch (Exception $e) {
        // Вывод сообщения об ошибке
        echo 'An error occurred while adding the data to the XML document: ' . $e->getMessage();
    }

    // Перенаправление пользователя обратно на главную страницу
    header('Location: index.php');
    exit();
}

if (isset($_POST['delete'])) {
    $rowIndex = isset($_POST['rowIndex']) ? intval($_POST['rowIndex']) : -1;

    if ($rowIndex >= 0) {
        // Load the XML file
        $xmlFile = new DOMDocument();
        $xmlFile->load('index.xml');

        // Remove the selected row from the XML
        $assets = $xmlFile->getElementsByTagName('asset');
        if ($rowIndex < $assets->length) {
            $removedAsset = $assets->item($rowIndex);
            $xmlFile->documentElement->removeChild($removedAsset);
            $xmlFile->save('index.xml');
        }
    }
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="index.css"/>
</head>
<body>
<nav class="navbar">
    <a href="index.php" class="navbar-item">Index XML</a>
    <a href="indexJSON.php" class="navbar-item">Index JSON</a>
</nav>
<h1>Index XML</h1>
<form>
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
    echo $proc->transformToXML($xml);
    ?>
    </tbody>
</table>
</body>
</html>








