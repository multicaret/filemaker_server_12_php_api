<?php

include('init.php');

if (!empty($fm)) {
    $findCommand = $fm->newFindCommand('new_book_list');
//    $findCommand->setRange(0, 2);
    $result = $findCommand->execute();
    if (FileMaker::isError($result)) {
        echo($result->getMessage());
        return;
    }
    $record = $result->getLastRecord();
    echo json_encode($record->getFields());
} else {
    echo json_encode(array('error' => array('message' => 'FM not initialized')));
}
