<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Appointment System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 120px;
        }

        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            width: 320px;
            margin-top: 100px;
        }

        h1 { 
            text-align: center; 
            font-size: 2.5rem;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input, button {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        button:hover {
            background: #1e40af;
        }

        .msg {
            color: green;
            text-align: center;
        }
        
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>User Appointment Management System</h1>
    <form id="loginForm">
        @csrf
        <h2>Login</h2>

        <input type="text" name="username" placeholder="Username" required>

        <button type="submit">Login</button>
        <p style="text-align:center;">Don’t have an account? <a href="/register">Register</a></p>

        <p id="message" class="msg"></p>
        <p id="error" class="error"></p>
    </form>

    <script>
        const form = document.getElementById('loginForm');
        const msg = document.getElementById('message');
        const err = document.getElementById('error');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            msg.textContent = '';
            err.textContent = '';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const res = await fetch('/api/login', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(data)
            });

            const json = await res.json();

            if (res.ok) {
                console.log("Token:", json.token);
                localStorage.setItem('token', json.token);
                msg.textContent = 'Login successful!';
                console.log('Redirecting...');
                setTimeout(() => window.location.href = '/dashboard', 1000);
            } else {
                err.textContent = json.message || 'Invalid username';
            }
        });
    </script>

</body>
</html>
