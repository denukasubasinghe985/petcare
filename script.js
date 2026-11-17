let csrfToken = "";


// Communicate with the PHP backend

async function request(url, formData = null) {

    const options = {
        credentials: "same-origin"
    };

    if (formData) {

        formData.set("csrf", csrfToken);

        options.method = "POST";
        options.body = formData;
    }

    const response = await fetch(url, options);

    const data = await response.json();

    if (!response.ok || !data.success) {

        throw new Error(
            data.message || "Something went wrong."
        );
    }

    return data;
}


// Display a form message

function showMessage(text, success = false) {

    const message = document.getElementById("message");

    if (message) {

        message.textContent = text;

        message.className = success
            ? "message success"
            : "message";

    } else {

        alert(text);
    }
}


// Set minimum appointment date

const dateInput = document.querySelector('input[type="date"]');

if (dateInput) {

    const today = new Date();

    const year = today.getFullYear();

    const month = String(
        today.getMonth() + 1
    ).padStart(2, "0");

    const day = String(
        today.getDate()
    ).padStart(2, "0");

    dateInput.min = `${year}-${month}-${day}`;
}


// Registration form

const registerForm = document.getElementById("registerForm");

if (registerForm) {

    registerForm.addEventListener("submit", async function(event) {

        event.preventDefault();

        const password = registerForm.elements.password.value;

        const confirmPassword =
            registerForm.elements.confirm_password.value;

        if (password !== confirmPassword) {

            showMessage("Passwords do not match.");

            return;
        }

        if (password.length < 8) {

            showMessage("Password must have at least 8 characters.");

            return;
        }

        try {

            const data = await request(
                "php/register.php",
                new FormData(registerForm)
            );

            alert(data.message);

            window.location.href = "login.html";

        } catch (error) {

            showMessage(error.message);
        }
    });
}


// Login form

const loginForm = document.getElementById("loginForm");

if (loginForm) {

    loginForm.addEventListener("submit", async function(event) {

        event.preventDefault();

        try {

            await request(
                "php/login.php",
                new FormData(loginForm)
            );

            window.location.href = "dashboard.html";

        } catch (error) {

            showMessage(error.message);
        }
    });
}


// Appointment booking form

const appointmentForm =
    document.getElementById("appointmentForm");

if (appointmentForm) {

    appointmentForm.addEventListener(
        "submit",
        async function(event) {

            event.preventDefault();

            if (!appointmentForm.elements.pet_name.value.trim()) {

                showMessage("Please enter the pet name.");

                return;
            }

            try {

                const data = await request(
                    "php/create.php",
                    new FormData(appointmentForm)
                );

                showMessage(data.message, true);

                appointmentForm.reset();

                await loadAppointments();

            } catch (error) {

                showMessage(error.message);
            }
        }
    );
}


// Read appointments from JSON API

async function loadAppointments() {

    const table = document.getElementById("appointmentRows");

    if (!table) {
        return;
    }

    const tableMessage =
        document.getElementById("tableMessage");

    const data = await request("php/read.php");

    table.replaceChildren();

    tableMessage.textContent =
        data.appointments.length === 0
            ? "No appointments available."
            : "";

    data.appointments.forEach(function(appointment) {

        const row = document.createElement("tr");

        const fields = [
            "pet_name",
            "service",
            "date",
            "status"
        ];

        fields.forEach(function(field) {

            const cell = document.createElement("td");

            cell.textContent = appointment[field];

            row.appendChild(cell);
        });


        // Edit link

        const actions = document.createElement("td");

        const edit = document.createElement("a");

        edit.href = "edit.html?id=" + appointment.id;

        edit.textContent = "Edit";

        actions.appendChild(edit);


        // Delete button

        const deleteButton = document.createElement("button");

        deleteButton.textContent = "Delete";

        deleteButton.className = "delete-button";

        deleteButton.addEventListener("click", function() {

            deleteAppointment(appointment.id);
        });

        actions.appendChild(deleteButton);

        row.appendChild(actions);

        table.appendChild(row);
    });
}


// Delete appointment

async function deleteAppointment(id) {

    if (!confirm("Are you sure you want to delete this appointment?")) {

        return;
    }

    const formData = new FormData();

    formData.append("id", id);

    try {

        const data = await request(
            "php/delete.php",
            formData
        );

        showMessage(data.message, true);

        await loadAppointments();

    } catch (error) {

        showMessage(error.message);
    }
}


// Edit appointment form

const editForm = document.getElementById("editForm");

async function loadEditForm() {

    const parameters = new URLSearchParams(
        window.location.search
    );

    const id = parameters.get("id");

    const data = await request(
        "php/read.php?id=" + encodeURIComponent(id)
    );

    const appointment = data.appointment;

    editForm.elements.id.value = appointment.id;

    editForm.elements.pet_name.value = appointment.pet_name;

    editForm.elements.service.value = appointment.service;

    editForm.elements.date.value = appointment.date;
}

if (editForm) {

    editForm.addEventListener("submit", async function(event) {

        event.preventDefault();

        try {

            const data = await request(
                "php/update.php",
                new FormData(editForm)
            );

            alert(data.message);

            window.location.href = "dashboard.html";

        } catch (error) {

            showMessage(error.message);
        }
    });
}


// Logout functionality

const logoutButton = document.getElementById("logoutButton");

if (logoutButton) {

    logoutButton.addEventListener("click", async function() {

        try {

            await request(
                "php/logout.php",
                new FormData()
            );

            window.location.href = "login.html";

        } catch (error) {

            showMessage(error.message);
        }
    });
}


// Initialize the website

async function initialize() {

    const buttons = document.querySelectorAll(
        'form button[type="submit"]'
    );

    buttons.forEach(button => button.disabled = true);

    try {

        const session = await request("php/session.php");

        csrfToken = session.csrf;

        if (appointmentForm || editForm) {

            if (!session.logged_in) {

                window.location.href = "login.html";

                return;
            }
        }

        const welcome = document.getElementById("welcome");

        if (welcome) {

            welcome.textContent = "Welcome, " + session.name;
        }

        if (appointmentForm) {

            await loadAppointments();
        }

        if (editForm) {

            await loadEditForm();
        }

        buttons.forEach(button => button.disabled = false);

    } catch (error) {

        showMessage(error.message);
    }
}

initialize();