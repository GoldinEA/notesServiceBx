<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("API Docs");

$openapi = \OpenApi\Generator::scan([
    $_SERVER["DOCUMENT_ROOT"] . "/local/modules/notes.test/"
]);
$json = $openapi->toJson();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notes API Docs</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body { margin: 0; padding: 20px; font-family: -apple-system, sans-serif; }
        #swagger-ui { max-width: 1400px; margin: 0 auto; }
    </style>
</head>
<body>
<div id="swagger-ui"></div>

<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
<script>
    window.onload = function() {
        const spec = <?= $json ?>;

        SwaggerUIBundle({
            spec: spec,
            dom_id: '#swagger-ui',
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
                docExpansion: "list",
            tryItOutEnabled: true,
            filter: true,
            maxDisplayedTags: 10
        });
    };
</script>
</body>
</html>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
