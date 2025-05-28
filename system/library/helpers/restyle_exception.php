<?php
// PicoPHP Custom Exception Handler

if (php_sapi_name() !== 'cli') {
    set_exception_handler(function ($e) {
        http_response_code(500);

        $title = "PicoPHP - Error Exception";
        $message = htmlspecialchars($e->getMessage());
        $file = htmlspecialchars($e->getFile());
        $line = $e->getLine();

        // Ambil stack trace sebagai array
        $traceArray = $e->getTrace();

        // Bangun HTML ol-li untuk trace
        $traceHtml = "<ol>";
        foreach ($traceArray as $index => $trace) {
            $func = isset($trace['function']) ? $trace['function'] : '[unknown function]';
            $class = isset($trace['class']) ? $trace['class'] : '';
            $type = isset($trace['type']) ? $trace['type'] : '';
            $fileTrace = isset($trace['file']) ? htmlspecialchars($trace['file']) : '[internal function]';
            $lineTrace = isset($trace['line']) ? $trace['line'] : '';

            $fullFunc = $class . $type . $func;

            $traceHtml .= "<li><strong>{$fullFunc}()</strong><br>";
            $traceHtml .= "File: {$fileTrace}";
            if ($lineTrace !== '') {
                $traceHtml .= " (baris {$lineTrace})";
            }
            $traceHtml .= "</li>";
        }
        $traceHtml .= "</ol>";

        echo <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{$title}</title>
    <style>
        body {
            background: #2c3e50;
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; padding: 0;
        }
        header {
            background: #e74c3c;
            padding: 20px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            box-shadow: 0 3px 6px rgba(0,0,0,0.3);
        }
        main {
            padding: 30px 40px;
            max-width: 900px;
            margin: 30px auto;
            background: #34495e;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        }
        h1 {
            margin-top: 0;
            color: #e74c3c;
        }
        .message {
            font-size: 1.2rem;
            margin-bottom: 15px;
            background: #c0392b;
            padding: 15px;
            border-radius: 5px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.3);
        }
        .details {
            font-size: 0.95rem;
            color: #bdc3c7;
            background: #2c3e50;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin-top: 20px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.3);
        }
        ol {
            padding-left: 20px;
        }
        ol li {
            margin-bottom: 12px;
            line-height: 1.4;
        }
        ol li strong {
            color: #e67e22;
        }
        footer {
            text-align: center;
            margin-top: 40px;
            padding: 15px;
            font-size: 0.85rem;
            color: #95a5a6;
        }
        a {
            color: #e67e22;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>PicoPHP Exception Handler</header>
    <main>
        <h1>Terjadi Kesalahan!</h1>
        <div class="message">{$message}</div>
        <div class="details">
            <strong>File:</strong> {$file} (baris {$line})<br><br>
            <strong>Stack Trace:</strong><br>
            {$traceHtml}
        </div>
    </main>
    <footer>
        &copy; 2025 PicoPHP Framework — <a href="https://github.com/insansimple/PicoPHP" target="_blank">Visit GitHub</a>
    </footer>
</body>
</html>
HTML;
    });
}
