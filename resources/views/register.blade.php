<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Appointment System</title>
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

        input, select, button {
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
    <form id="registerForm">
        <h2>Register</h2>

        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="username" placeholder="Username" required>
        
        <select name="preferred_timezone" required>
            <option value="">Select Timezone</option>
            <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
            <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
            <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
            <option value="UTC">Pacific/Auckland (UTC)</option>
        </select>

        <button type="submit">Register</button>
        <p style="text-align:center;">Already have an account? <a href="/login">Login</a></p>

        <p id="message" class="msg"></p>
        <p id="error" class="error"></p>
    </form>

    <script>
        const form = document.getElementById('registerForm');
        const msg = document.getElementById('message');
        const err = document.getElementById('error');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            msg.textContent = '';
            err.textContent = '';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const res = await fetch('http://127.0.0.1:8000/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const json = await res.json();

            if (res.ok) {
                console.log("Token:", json.token);
                localStorage.setItem('token', json.token);
                msg.textContent = 'Registration successful!';
                console.log('Redirecting...');
                setTimeout(() => window.location.href = '/dashboard', 1000);
            } else {
                err.textContent = json.message || 'Failed to register';
            }
        });
    </script>

</body>
</html>
