<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $store->name }} — Closed</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Lato, sans-serif;
            background: #f5f6fa;
            color: #212529;
        }
        .card {
            max-width: 420px;
            margin: 1.5rem;
            padding: 2rem;
            background: #fff;
            border: 1px solid #e6e8ef;
            border-radius: 10px;
            text-align: center;
        }
        h1 { font-size: 1.35rem; margin-bottom: 0.5rem; color: #6868AC; }
        p { color: #6c757d; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $store->name }}</h1>
        <p>This store is not accepting reviews right now.</p>
    </div>
</body>
</html>
