<?php
header('Content-Type: application/json');

require_once('../lib/config.php');

$picture = new Picture();

if (!empty($_POST['file']) && preg_match('/^[a-z0-9_]+\.jpg$/', $_POST['file'])) {
    $file = $_POST['file'];
} elseif ($config['file_format_date']) {
    $file = date('Ymd_His').'.jpg';
} else {
    $file = md5(time()).'.jpg';
}

$filename_tmp = $config['foldersAbs']['tmp'] . DIRECTORY_SEPARATOR . $file;

if (!isset($_POST['style'])) {
    die(json_encode([
        'error' => 'No style provided'
    ]));
} elseif ($_POST['style'] === 'photo') {
    $picture->setFilename($filename_tmp);
    $picture->takePicture();
} elseif ($_POST['style'] === 'collage') {
    if (!is_numeric($_POST['collageNumber'])) {
        die(json_encode([
            'error' => 'No or invalid collage number provided',
        ]));
    }

    $number = $_POST['collageNumber'] + 0;

    if ($number > 3) {
        die(json_encode([
            'error' => 'Collage consists only of ' . $config['collage_limit'] . ' pictures',
        ]));
    }

    $basename = substr($filename_tmp, 0, -4);
    $filename = $basename . '-' . $number . '.jpg';

    $picture->setFilename($filename);
    $picture->takePicture();

    die(json_encode([
        'success' => 'collage',
        'file' => $file,
        'current' => $number,
        'limit' => $config['collage_limit'],
    ]));
} else {
    die(json_encode([
        'error' => 'Invalid photo style provided',
    ]));
}

// send imagename to frontend
echo json_encode([
    'success' => 'image',
    'file' => $file,
]);
