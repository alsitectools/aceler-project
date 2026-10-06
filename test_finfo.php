<?php
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$filename = __FILE__;
$mime = finfo_file($finfo, $filename);
echo "MIME type of $filename: $mime\n";
finfo_close($finfo);

// Test with a text file
$textFile = tempnam(sys_get_temp_dir(), 'test') . '.txt';
file_put_contents($textFile, "This is a test file");
$mimeText = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $textFile);
echo "MIME type of text file: $mimeText\n";
unlink($textFile);

// Test with a jpeg header (fake)
$fakeJpg = tempnam(sys_get_temp_dir(), 'test') . '.jpg';
file_put_contents($fakeJpg, "This is not a real JPEG but has jpg extension");
$mimeFake = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fakeJpg);
echo "MIME type of fake jpg: $mimeFake\n";
unlink($fakeJpg);
?>