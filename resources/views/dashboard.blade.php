<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Appointment System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            max-width: 1050px;
            margin: 0 auto;
            padding: 20px;
        }

        .user-info {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .user-info h2 {
            color: #1e40af;
            margin-bottom: 15px;
        }

        .user-info p {
            margin-bottom: 10px;
            font-size: 16px;
        }

        h1 { 
            text-align: center; 
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 30px;
        }

        input[type="text"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        input::placeholder {
            color: #9ca3af; 
        }

        .hint {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: -5px;
            margin-left: 5px;  
            display: block;
        }

        select[multiple] {
            height: 120px;
        }

        button {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #1e40af;
        }

        table {
            width: 100%;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        th {
            background: #2563eb;
            color: white;
            font-weight: bold;
            padding: 12px;
        }

        td {
            padding: 12px;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #1e40af;
        }

        .checkbox-list {
            display: grid;
            grid-template-columns: 1fr 1fr; 
            gap: 8px 16px;
        }

        .checkbox-list label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            padding: 6px 8px;
            border-radius: 6px;
        }

        .checkbox-list input[type="checkbox"] {
            width: auto;
            height: auto;
            margin: 0;
        }

        .participants-box {
            background: #ffffff;
            border: 1px solid #e6edf8;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .participants-box h3 {
            margin: 0 0 10px 0;
            font-size: 1rem;
            color: #1e40af;
            font-weight: 600;
        }

        /* keep checkbox list inside the box */
        .participants-box .checkbox-list {
            margin: 0;
        }

        .error-message {
            color: #dc2626;
            margin-top: 10px;
            text-align: center;
        }

        button[onclick="logout()"] {
            width: auto;
            padding: 10px 20px;
            background: #dc2626;
        }

        button[onclick="logout()"]:hover {
            background: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard</h1>
        <div class="user-info">
            <h2>User Information</h2>
            <p id="userName">Name: Loading...</p>
            <p id="userUsername">Username: Loading...</p>
            <div class="token-box">
                <h1>My Appointments</h1>

                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Participants</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentsTable">
                        <tr>
                            <td colspan="4">Loading...</td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-container">
                    <h2>Create New Appointment</h2>
                    <form id="appointmentForm">
                        <input type="text" id="title" placeholder="Title" required>
                        <span class="hint">Start time</span>
                        <input type="datetime-local" id="start" placeholder="Start Date" required>
                        <span class="hint">End time</span>
                        <input type="datetime-local" id="end" placeholder="End Date" required>
                        
                        <div class="participants-box">
                            <h3>Meeting Participants</h3>
                            <div id="users" class="checkbox-list" aria-label="Invite users">
                                <!-- checkboxes will be injected here by fetchUsers() -->
                            </div>
                        </div>
                        
                        <button type="submit">Create Appointment</button>
                        <p id="formMessage" class="error-message"></p>
                    </form>
                </div>

            </div>
        </div>
        <button onclick="logout()">Logout</button>
    </div>

    <script>
        async function fetchUserData() {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            try {
                const res = await fetch('http://127.0.0.1:8000/api/me', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    const data = await res.json();
                    console.log('User Data:', data);
                    document.getElementById('userName').textContent = `Name: ${data.user.name}`;
                    document.getElementById('userUsername').textContent = `Username: ${data.user.username}`;
                } else {
                    window.location.href = '/login';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function logout() {
            const token = localStorage.getItem('token');
            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                localStorage.removeItem('token');
                window.location.href = '/login';
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function fetchAppointments() {
            const token = localStorage.getItem('token');
            const response = await fetch('/api/appointments', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            const tableBody = document.getElementById('appointmentsTable');
            tableBody.innerHTML = ''; // Clear loading message

            if (response.ok) {
                console.log('Appointments Data:', data);
                data.forEach(appointment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${appointment.title}</td>
                        <td>${new Date(appointment.start).toLocaleString()}</td>
                        <td>${new Date(appointment.end).toLocaleString()}</td>
                        <td>${appointment.users.map(user => user.name).join(', ')}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="4">${data.message}</td>`;
                tableBody.appendChild(row);
            }
        }

        async function fetchUsers() {
            const token = localStorage.getItem('token');
            const response = await fetch('/api/users', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const users = await response.json();
            const usersContainer = document.getElementById('users');
            usersContainer.innerHTML = ''; // Clear existing

            users.forEach(user => {
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'users[]';
                checkbox.value = user.id;
                checkbox.id = `user_${user.id}`;

                const label = document.createElement('label');
                label.htmlFor = checkbox.id;
                label.appendChild(checkbox);
                label.appendChild(document.createTextNode(user.name));

                usersContainer.appendChild(label);
            });
        }

        async function createAppointment(event) {
            event.preventDefault();
            const title = document.getElementById('title').value;
            const start = document.getElementById('start').value;
            const end = document.getElementById('end').value;
            const users = Array.from(document.querySelectorAll('input[name="users[]"]:checked')).map(el => el.value);
            const token = localStorage.getItem('token');

            const response = await fetch('/api/appointments', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title, start, end, users })
            });

            const data = await response.json();
            const formMessage = document.getElementById('formMessage');

            if (response.ok) {
                formMessage.textContent = 'Appointment created successfully!';
                document.getElementById('appointmentForm').reset();
                document.querySelectorAll('input[name="users[]"]').forEach(cb => cb.checked = false);
                fetchAppointments(); 
            } else {
                formMessage.textContent = data.message || 'Failed to create appointment';
            }
        }

        document.addEventListener('DOMContentLoaded', fetchUserData);
        document.getElementById('appointmentForm').addEventListener('submit', createAppointment);
        document.addEventListener('DOMContentLoaded', fetchUsers);
        document.addEventListener('DOMContentLoaded', fetchAppointments);
    </script>
</body>
</html>