<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | Elevorce Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Rubik', sans-serif;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      overflow: hidden;
    }
    .welcome-container {
      max-width: 700px;
      padding: 30px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.3);
      animation: fadeIn 1.2s ease-in-out;
    }
    .welcome-container h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .welcome-container p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }
    .btn-custom {
      background: #fff;
      color: #667eea;
      font-weight: 600;
      padding: 12px 30px;
      border-radius: 30px;
      text-decoration: none;
      transition: all 0.3s ease-in-out;
    }
    .btn-custom:hover {
      background: #667eea;
      color: #fff;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="welcome-container">
    <h1>🚀 Welcome to Elevorce Admin</h1>
    <p>Manage your projects, employees, and workflows seamlessly with Elevorce Admin Dashboard.</p>
    <a href="{{ url('admin/dashboard') }}" class="btn-custom">Explore Dashboard</a>
  </div>
</body>
</html>
