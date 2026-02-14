<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales System API - Documentation</title>
    <link rel="stylesheet" href="/vendor/swagger-api/swagger-ui.css">
    <style>
        html { box-sizing: border-box; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="/vendor/swagger-api/swagger-ui-bundle.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "/api/docs",
                dom_id: "#swagger-ui",
                deepLinking: true,
                presets: [SwaggerUIBundle.presets.apis],
                plugins: [],
                syntaxHighlight: { activated: true, theme: 'agate' },
                oauth2RedirectUrl: window.location.origin + "/oauth2-callback"
            });
            window.ui = ui;
        };
    </script>
</body>
</html>
