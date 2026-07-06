<?php
// Test script to verify MIME type detection
$file = 'test.txt';
file_put_contents($file, 'This is a text file');

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$realMimeType = finfo_file($finfo, $file);
finfo_close($finfo);

echo "File: $file\n";
echo "Real MIME type: $realMimeType\n";
echo "Is image/jpeg? " . ($realMimeType === 'image/jpeg' ? 'yes' : 'no') . "\n";
echo "Is allowed (if jpeg/png allowed)? " . (in_array($realMimeType, ['image/jpeg', 'image/png'], true) ? 'yes' : 'no') . "\n";

// Clean up
unlink($file);
