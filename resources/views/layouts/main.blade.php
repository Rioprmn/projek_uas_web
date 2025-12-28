<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir App</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }
        .wrapper {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 220px;
            background: #1f2937;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            margin-top: 0;
            font-size: 18px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 0;
        }
        .sidebar a:hover {
            background: #374151;
            padding-left: 10px;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
        }
        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>Kasir App</h2>

        <p>👤 {{ auth()->user()->name }}</p>

        <a href="/dashboard">Dashboard</a>
        <a href="/transactions/create">Kasir</a>
        <a href="/products">Produk</a>

        <form action="/logout" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    <div class="content">
        @yield('content')
    </div>
</div>

</body>
</html>
