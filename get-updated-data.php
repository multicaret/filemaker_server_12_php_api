<?php

include('init.php');

$allKeys = array(
    "BARKOD" => true,
    "STOK" => true,
    "created_at" => true,
    "updated_at" => true,
    "status" => true,
    "LİSTE SATIŞ FİYATI___" => true
);

$objectTemplate = array(
    "isbn" => array("BARKOD"),
    "created_at" => array("created_at"),
    "updated_at" => array("updated_at"),
    "status" => array("status"),
    "price" => array("LİSTE SATIŞ FİYATI___"),
    "quantity" => array("STOK"),
);


if (!empty($fm)) {

    $findCommand = $fm->newFindCommand('update_data');

    if (isset($_GET["per_page"])) {
        $perPage = $_GET["per_page"];
    } else {
        $perPage = 100;
    }
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
    } else {
        $page = 1;
    }

    $start_from = ($page - 1) * $perPage;
    $findCommand->setRange($start_from, intval($perPage));
    $result = $findCommand->execute();
    if (FileMaker::isError($result)) {
        printf(json_encode(array('data' => array(), 'error' => array('message' => $result->getMessage()))));
        die();
    }
    $records = $result->getRecords();
    $tmpRecords = array();
    foreach ($records as $record) {
        $tempBook = array();
        foreach ($record->_impl->_fields as $indexS => $tempItem) {
            $attributeValue = array_values($tempItem);
            if (!empty($allKeys)) {
                if (!is_null(keyValidation($indexS, $allKeys)) && $allKeys[$indexS]) {
                    $tempBook[$indexS] = trim(array_shift($attributeValue));
                }
            }
        }
        array_push($tmpRecords, $tempBook);
    }
    $jsonData = array(
        'data' => array(),
        'page' => $page,
        'range' => $findCommand->getRange()
    );

    if (isset($allKeys) && !empty($objectTemplate) && count($tmpRecords) > 0) {
        foreach ($tmpRecords as $rawItem) {
            $finalBook = array();
            foreach ($objectTemplate as $itemKey => $objectKey) {
                foreach ($objectKey as $irshadColumnKey) {
                    if ($allKeys[$irshadColumnKey]) {
                        $finalBook[$itemKey] .= ' ' . $rawItem[$irshadColumnKey];
                    }
                }
                $finalBook[$itemKey] = trim($finalBook[$itemKey]);
            }
            array_push($jsonData['data'], $finalBook);
        }
    }
    echo json_encode($jsonData);
} else {
    echo json_encode(array('error' => array('message' => 'FM not initialized')));
}

