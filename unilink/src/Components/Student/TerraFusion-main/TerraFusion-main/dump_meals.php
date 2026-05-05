<?php
try {
    $p = new PDO('mysql:host=localhost;dbname=terra_fusion', 'root', '');
    $s = $p->query('DESCRIBE order_details');
    echo "Field | Type | Null | Key | Default | Extra\n";
    echo "---|---|---|---|---|---\n";
    while ($r = $s->fetch(PDO::FETCH_ASSOC)) {
        echo "{$r['Field']} | {$r['Type']} | {$r['Null']} | {$r['Key']} | {$r['Default']} | {$r['Extra']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
