<?php


include('init.php');


if (!empty($fm)) {
    if (isset($_GET["barcode"])) {
        $barcode = $_GET["barcode"];
    } else {
        echo json_encode(array('error' => array('message' => 'Please fill "barcode" first then try again.')));
        die();
    }
    $findCommand = $fm->newFindCommand('new_book_list');

    $findCommand->addFindCriterion('BARKOD', $barcode);
    $result = $findCommand->execute();

    if (FileMaker::isError($result)) {
        printf(json_encode(array('data' => array(), 'error' => array('message' => $result->getMessage()))));
        die();
    }
    $record = $result->getFirstRecord();
    $tempBook = array();
    foreach ($record->_impl->_fields as $indexS => $tempItem) {
        $attributeValue = array_values($tempItem);
        $tempBook[$indexS] = trim($attributeValue[0]);
    }
    $jsonData = array('data' => $tempBook);
    echo json_encode($jsonData);

} else {
    echo json_encode(array('error' => array('message' => 'FM not initialized')));
}

