<?php echo $data['exceptionClass']; ?>: <?php echo $data['message']; ?> <?php echo PHP_EOL . PHP_EOL ?>
<?php echo $data['exception']->getTraceAsString(); ?> <?php echo PHP_EOL ?>