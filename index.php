<?php
extension_loaded('xsl') or die('XSL extension not loaded');

$xml = new DOMDocument;
$xml->load('index.xml');

$xsl = new DOMDocument;
$xsl->load('index.xsl');

$stateFilter = isset($_GET['stateFilter']) ? $_GET['stateFilter'] : '';
$personFilter = isset($_GET['personFilter']) ? $_GET['personFilter'] : '';

$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);

$proc->setParameter('', 'stateFilter', $stateFilter);
$proc->setParameter('', 'personFilter', $personFilter);

$xml = simplexml_load_file('index.xml');
$json = json_encode($xml, JSON_PRETTY_PRINT);

file_put_contents('index.json', $json);

if (isset($_POST['lisa'])) {
    // Validate the POST data
    $requiredFields = ['number', 'name', 'state', 'cost', 'responsible_person', 'additional_information'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            // Display an error message to the user
            echo 'Please fill in all of the required fields.';
            return;
        }
    }

    // Get the data from the POST array
    $number = $_POST['number'];
    $name = $_POST['name'];
    $state = $_POST['state']; // Getting the selected state value
    $cost = $_POST['cost'];
    $responsiblePerson = $_POST['responsible_person'];
    $additionalInformation = $_POST['additional_information'];

    // Add the data to the XML document
    try {
        $xml = simplexml_load_file('index.xml');
        $newAsset = $xml->addChild('asset');

        $newAsset->addChild('number', $number);
        $newAsset->addChild('name', $name);
        $newAsset->addChild('state', $state);
        $newAsset->addChild('cost', $cost);
        $newAsset->addChild('responsible_person', $responsiblePerson);
        $newAsset->addChild('additional_information', $additionalInformation);

        $xml->asXML('index.xml');
    } catch (Exception $e) {
        // Display an error message to the user
        echo 'An error occurred while adding the data to the XML document: ' . $e->getMessage();
    }

    // Redirect the user back to the main page
    header('Location: index.php');
}

if (isset($_POST['delete'])) {
    $rowIndex = isset($_POST['rowIndex']) ? intval($_POST['rowIndex']) : -1;

    if ($rowIndex >= 0) {
        // Load the XML file
        $xml = simplexml_load_file('index.xml');

        // Remove the selected row from the XML
        unset($xml->asset[$rowIndex]);

        // Save the updated XML
        $xml->asXML('index.xml');
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
    <a href="index.php" class="navbar-item">Index PHP</a>
    <a href="indexJSON.php" class="navbar-item">Index JSON</a>
</nav>
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








