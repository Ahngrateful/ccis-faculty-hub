<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>
    College of Computing & Information Sciences | Faculty Management
  </title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    :root {
      --primary-color: #006834;
      --secondary-color: #75d979;
      --accent-color: #ffde26;
      --light-gray: #f9f9f9;
      --medium-gray: #eaeaea;
      --dark-gray: #555;
      --white: #ffffff;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: var(--light-gray);
      color: var(--dark-gray);
      line-height: 1.6;
    }

    header {
      background-color: var(--primary-color);
      color: var(--white);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      z-index: 100;
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
      transition: all 0.3s;
    }

    nav ul li a:hover {
      background-color: rgba(255, 255, 255, 0.2);
    }

    .login-btn {
      background-color: var(--accent-color);
      color: var(--primary-color);
      font-weight: bold;
    }

    .login-btn:hover {
      background-color: #ffe766;
    }

    main {
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .hero {
      background-color: var(--white);
      border-radius: 8px;
      padding: 3rem 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      text-align: center;
      background-image: linear-gradient(to right,
          var(--primary-color),
          var(--secondary-color));
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
      background-color: var(--accent-color);
      color: var(--primary-color);
      border: none;
      padding: 0 20px;
      border-radius: 0 4px 4px 0;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s;
    }

    .search-bar button:hover {
      background-color: #ffe766;
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
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .feature-card h3 {
      color: var(--primary-color);
      margin-top: 0;
    }

    .feature-card .icon {
      background-color: var(--secondary-color);
      color: var(--primary-color);
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
      background-color: var(--primary-color);
      color: var(--white);
      text-align: center;
      padding: 1.5rem;
      margin-top: 2rem;
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin: 2rem 0;
    }

    .stat-card {
      background-color: var(--white);
      border-radius: 8px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    .stat-card .icon {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: var(--primary-color);
    }

    .stat-card .number {
      font-size: 2.5rem;
      font-weight: bold;
      color: var(--primary-color);
      margin: 0.5rem 0;
    }

    .action-button {
      display: inline-flex;
      align-items: center;
      padding: 10px 20px;
      background-color: var(--primary-color);
      color: var(--white);
      border-radius: 4px;
      transition: all 0.3s;
      text-decoration: none;
      font-weight: 500;
      margin-top: 1rem;
    }

    .action-button:hover {
      background-color: var(--secondary-color);
      color: var(--primary-color);
    }

    .action-button i {
      margin-right: 8px;
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

      .stats {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 480px) {
      .stats {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <header>
    <div class="logo-container">
      <img
        src="{{ asset('assets/CCIS-Logo-Official.png') }}"
        alt="College Logo"
        class="logo" />
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
        <li>
          <a href="login.php" class="login-btn">Faculty Login</a>
        </li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h2>Connecting Knowledge, Expertise, and Innovation</h2>
      <p>
        Discover and manage faculty profiles, research publications, and
        academic contributions across our college.
      </p>
      <div class="search-bar">
        <input
          type="text"
          placeholder="Search faculty members, research areas, or courses..." />
        <button><i class="fas fa-search"></i> Search</button>
      </div>
    </section>

    <div class="stats">
      <div class="stat-card">
        <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="number">142</div>
        <p>Faculty Members</p>
        <a href="#" class="action-button"><i class="fas fa-eye"></i> View All</a>
      </div>
      <div class="stat-card">
        <div class="icon"><i class="fas fa-book"></i></div>
        <div class="number">15</div>
        <p>Academic Programs</p>
        <a href="#" class="action-button"><i class="fas fa-list"></i> Explore</a>
      </div>
      <div class="stat-card">
        <div class="icon"><i class="fas fa-flask"></i></div>
        <div class="number">326</div>
        <p>Research Projects</p>
        <a href="#" class="action-button"><i class="fas fa-search"></i> Discover</a>
      </div>
      <div class="stat-card">
        <div class="icon"><i class="fas fa-trophy"></i></div>
        <div class="number">48</div>
        <p>Awards This Year</p>
        <a href="#" class="action-button"><i class="fas fa-medal"></i> See More</a>
      </div>
    </div>

    <section class="features">
      <div class="feature-card">
        <div class="icon"><i class="fas fa-user-tie"></i></div>
        <h3>Faculty Profiles</h3>
        <p>
          Comprehensive profiles showcasing academic backgrounds, research
          interests, publications, and contact information for all faculty
          members.
        </p>
        <a href="#" class="action-button"><i class="fas fa-arrow-right"></i> Learn More</a>
      </div>
      <div class="feature-card">
        <div class="icon"><i class="fas fa-chart-line"></i></div>
        <h3>Research Analytics</h3>
        <p>
          Track research productivity, citations, and collaborations across
          departments and research centers.
        </p>
        <a href="#" class="action-button"><i class="fas fa-arrow-right"></i> Learn More</a>
      </div>
      <div class="feature-card">
        <div class="icon"><i class="fas fa-handshake"></i></div>
        <h3>Collaboration Tools</h3>
        <p>
          Connect with colleagues for interdisciplinary research opportunities
          and academic partnerships.
        </p>
        <a href="#" class="action-button"><i class="fas fa-arrow-right"></i> Learn More</a>
      </div>
    </section>

    <section class="features">
      <div class="feature-card">
        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
        <h3>Academic Calendar</h3>
        <p>
          View important dates, deadlines, and events across all departments
          in the college.
        </p>
        <a href="#" class="action-button"><i class="fas fa-arrow-right"></i> Learn More</a>
      </div>
      <div class="feature-card">
        <div class="icon"><i class="fas fa-file-alt"></i></div>
        <h3>Publication Management</h3>
        <p>
          Easily upload and manage your research publications, conference
          papers, and academic works.
        </p>
        <a href="#" class="action-button"><i class="fas fa-arrow-right"></i> Learn More</a>
      </div>
      <div class="feature-card">
        <div class="icon"><i class="fas fa-search-plus"></i></div>
        <h3>Expertise Finder</h3>
        <p>
          Discover faculty expertise for media inquiries, research
          collaborations, or student mentorship.
        </p>
        <a href="#" class="action-button"><i class="fas fa-arrow-right"></i> Learn More</a>
      </div>
    </section>
  </main>

  <footer>
    <p>
      &copy; 2025 University of Makati FPMS v1.0 |
      <a href="#" style="color: var(--accent-color)">Help</a> |
      <a href="#" style="color: var(--accent-color)">Contact Support</a>
    </p>
    <p>Contact: ccisfaculty@university.edu | Phone: (123) 456-7890</p>
  </footer>
</body>

</html>