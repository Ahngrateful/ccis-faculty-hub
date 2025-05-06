<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College of Computing & Information Sciences | Faculty Management</title>
    <style>
        :root {
            --primary-dark: #006834;
            /* Dark green */
            --primary-light: #75D979;
            /* Light green */
            --accent: #FFDE26;
            /* Yellow */
            --white: #FFFFFF;
            --light-gray: #F5F5F5;
            --dark-gray: #333333;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light-gray);
            color: var(--dark-gray);
        }

        header {
            background-color: var(--primary-dark);
            color: var(--white);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo {
            height: 60px;
            margin-right: 15px;
        }

        .header-text h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .header-text p {
            margin: 5px 0 0;
            font-size: 1rem;
            opacity: 0.9;
        }

        nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .login-btn {
            background-color: var(--accent);
            color: var(--primary-dark);
            font-weight: bold;
        }

        .login-btn:hover {
            background-color: #FFE766;
        }

        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero {
            background-color: var(--white);
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
            background-image: linear-gradient(to right, var(--primary-dark), var(--primary-light));
            color: var(--white);
        }

        .hero h2 {
            font-size: 2.2rem;
            margin-top: 0;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 1.5rem;
        }

        .search-bar {
            display: flex;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-bar input {
            flex-grow: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 4px 0 0 4px;
            font-size: 1rem;
        }

        .search-bar button {
            background-color: var(--accent);
            color: var(--primary-dark);
            border: none;
            padding: 0 20px;
            border-radius: 0 4px 4px 0;
            font-weight: bold;
            cursor: pointer;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .feature-card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .feature-card h3 {
            color: var(--primary-dark);
            margin-top: 0;
        }

        .feature-card .icon {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        footer {
            background-color: var(--primary-dark);
            color: var(--white);
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-dark);
            margin: 0.5rem 0;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
                padding: 1rem;
            }

            .logo-container {
                flex-direction: column;
                margin-bottom: 1rem;
            }

            .logo {
                margin-right: 0;
                margin-bottom: 10px;
            }

            nav ul {
                flex-direction: column;
                align-items: center;
            }

            nav ul li {
                margin: 5px 0;
            }

            .search-bar {
                flex-direction: column;
            }

            .search-bar input {
                border-radius: 4px;
                margin-bottom: 5px;
            }

            .search-bar button {
                border-radius: 4px;
                padding: 12px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="logo-container">
            <img src="assets/CCIS-Logo-Official.png" alt="CCIS Logo">
            <div class="header-text">
                <h1>College of Computing & Information Sciences</h1>
                <p>Faculty Profile Management System</p>
            </div>
        </div>

        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Faculty Directory</a></li>
                <li><a href="#">Departments</a></li>
                <li><a href="#">Research</a></li>
                <li><a href="admin/admin-login.php" class="login-btn">Faculty Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h2>Connecting Knowledge, Expertise, and Innovation</h2>
            <p>Discover and manage faculty profiles, research publications, and academic contributions across our college.</p>
            <div class="search-bar">
                <input type="text" placeholder="Search faculty members, research areas, or courses...">
                <button>Search</button>
            </div>
        </section>

        <div class="stats">
            <div class="stat-card">
                <div class="icon">👨‍🏫</div>
                <div class="number">142</div>
                <p>Faculty Members</p>
            </div>
            <div class="stat-card">
                <div class="icon">📚</div>
                <div class="number">15</div>
                <p>Academic Programs</p>
            </div>
            <div class="stat-card">
                <div class="icon">🔬</div>
                <div class="number">326</div>
                <p>Research Projects</p>
            </div>
            <div class="stat-card">
                <div class="icon">🏆</div>
                <div class="number">48</div>
                <p>Awards This Year</p>
            </div>
        </div>

        <section class="features">
            <div class="feature-card">
                <div class="icon">👤</div>
                <h3>Faculty Profiles</h3>
                <p>Comprehensive profiles showcasing academic backgrounds, research interests, publications, and contact information for all faculty members.</p>
            </div>
            <div class="feature-card">
                <div class="icon">📊</div>
                <h3>Research Analytics</h3>
                <p>Track research productivity, citations, and collaborations across departments and research centers.</p>
            </div>
            <div class="feature-card">
                <div class="icon">🤝</div>
                <h3>Collaboration Tools</h3>
                <p>Connect with colleagues for interdisciplinary research opportunities and academic partnerships.</p>
            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="icon">📅</div>
                <h3>Academic Calendar</h3>
                <p>View important dates, deadlines, and events across all departments in the college.</p>
            </div>
            <div class="feature-card">
                <div class="icon">📝</div>
                <h3>Publication Management</h3>
                <p>Easily upload and manage your research publications, conference papers, and academic works.</p>
            </div>
            <div class="feature-card">
                <div class="icon">🔍</div>
                <h3>Expertise Finder</h3>
                <p>Discover faculty expertise for media inquiries, research collaborations, or student mentorship.</p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 College of Computing & Information Sciences | Faculty Profile Management System</p>
        <p>Contact: ccisfaculty@university.edu | Phone: (123) 456-7890</p>
    </footer>
</body>

</html>