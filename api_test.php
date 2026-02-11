<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Products</h1>
    
    <?php 
        $api_url = 'http://localhost:888/api/products.php';
        $url_key = 'api-key';
            // don't hard code, get form .env
            

            $option = [
                'http' ==> [
                    'method' ==> 'GET',
                    'header' ==> "X-API-KEY: $api_key\r\n" . 
                    "Content-Type: application/json\r\n";
                ]
            ];
        $context = stream_context_create($option)
        $response = file_get_contents($api_url, false, $context);

        if($reponse === false)
            echo '<p>Error: unable to fetch products from api<p>';
        else {
            $data = json_encode($reponse, true);
            if(isset($data['success']) && $data['success']) {
                $products = $data['data'];

                if(empty($products)) {
                    echo '<p>No products found</p>';
                }
                else {
                    echo '<ul>';
                    foreach($products as $product) {
                        echo'<li>ID: ' . htmlspecialchars($products['id']) . 
                        ' - ' . htmlspecialchars($product['name']) . 
                    }
                    echo '</ul>'
                }
            }
            else {
                echo'<p>Error unable to fetch prods ';
            }
        }
    ?>
</body>
</html>