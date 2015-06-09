<?php

require_once('../BigDataUtils/TimedList.php');

$tl = new TimedList(5, function ($object) { echo 'Removing: '.$object.PHP_EOL; });


echo 'Time: 0'.PHP_EOL;
$tl->Add(0);
$tl->Add(1);
$tl->Add(2);

echo 'Time: 1'.PHP_EOL;
$tl->Now(1);

$tl->Add(3);
$tl->Add(4);

echo 'Time: 4'.PHP_EOL;
$tl->Now(4);

$tl->Add(5);

echo 'Time: 5'.PHP_EOL;
$tl->Now(5); // 0, 1, 2 shall be removed.

echo 'Time: 7'.PHP_EOL;
$tl->Now(7); // 3, 4 shall be removed.

echo 'Time: 8'.PHP_EOL;
$tl->Now(8);

$tl->Add(6);

echo 'Time: 20'.PHP_EOL;
$tl->Now(20); // 5, 6 shall be removed.
