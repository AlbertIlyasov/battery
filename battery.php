<?php

$lastLevel = null;
$lastTime = time();
while (true) {
    $level = getLevel();
    if ($lastLevel != $level) {
	$currentTime = time();
        writeLog($level, $currentTime - $lastTime);
        $lastLevel = $level;
	$lastTime = $currentTime;
        //exec('xrandr --output eDP --brightness 0.9');
    }
    sleep(10);
}

function writeLog(int $level, int $duration): void
{
    $data = implode(';', [
        date('Y-m-d H:i:s'),
        $level,
	round($duration/60, 1),
    ]) . PHP_EOL;
    echo $data;
    file_put_contents(__DIR__ . '/battery.log', $data, FILE_APPEND);
}

function getLevel(): ?int
{
    //xrandr --output eDP --brightness 0.6
    $device = '/org/freedesktop/UPower/devices/battery_BAT0';

    $cmd = 'upower -i ' . $device;
    exec($cmd, $info);

    //echo date('r');
    //print_r($info);

    $info   = implode("\n", $info);
    $regExp = '/^\s*percentage:\s*(\d+)%\s*$/m';
    //$regExp = '/\s*percentage:\s*(\d+)%\s*/';

    preg_match($regExp, $info, $b);
    //print_r($b);

    $level = $b[1] ?? null;

    return $level && is_numeric($level) ? $level : null;
}

