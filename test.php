<?php

// https://github.com/ryakad/pandoc-php/blob/master/src/Pandoc/Pandoc.php


$tmpDir = sys_get_temp_dir();
is_writable($tmpDir));
$tmpFile = sprintf("%s/%s", $tmpDir, uniqid("pandoc"));
exec('which pandoc', $output, $returnVar);
if ($returnVar === 0) { $executable = $output[0]; }
file_put_contents($this->tmpFile, $content);
$command = sprintf(
	'%s --from=%s --to=%s %s',
	$this->executable,
	$from,
	$to,
	$this->tmpFile
);
exec(escapeshellcmd($command), $output);
return implode("\n", $output);

?>
