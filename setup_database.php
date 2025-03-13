<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'includes/db_connection.php';
$conn = get_db_connection();

// Function to check if a table exists
function tableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

// Function to run a query safely
function runQuery($conn, $query, $message) {
    echo "<p>Attempting to $message... ";
    if ($conn->query($query) === TRUE) {
        echo "Success!</p>";
        return true;
    } else {
        echo "Error: " . $conn->error . "</p>";
        return false;
    }
}

// Start HTML output
echo "<!DOCTYPE html>
<html>
<head>
    <title>Ndahari Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #0056b3; }
        .success { color: green; }
        .error { color: red; }
        pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Ndahari Platform Database Setup</h1>";

// Create employers table
if (!tableExists($conn, 'employers')) {
    $sql = "CREATE TABLE employers (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        phone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        company_description TEXT,
        industry VARCHAR(100),
        logo VARCHAR(255),
        website VARCHAR(255),
        location VARCHAR(255),
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
        verification_token VARCHAR(255)
    )";
    
    runQuery($conn, $sql, "create employers table");
}

// Create job_seekers table
if (!tableExists($conn, 'job_seekers')) {
    $sql = "CREATE TABLE job_seekers (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        phone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        profile_picture VARCHAR(255),
        skills TEXT,
        experience TEXT,
        education TEXT,
        availability JSON,
        preferred_job_types VARCHAR(255),
        preferred_locations VARCHAR(255),
        bio TEXT,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
        verification_token VARCHAR(255),
        last_login TIMESTAMP NULL
    )";
    
    runQuery($conn, $sql, "create job_seekers table");
}

// Create jobs table
if (!tableExists($conn, 'jobs')) {
    $sql = "CREATE TABLE jobs (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        employer_id INT(11) UNSIGNED NOT NULL,
        job_title VARCHAR(255) NOT NULL,
        job_description TEXT NOT NULL,
        job_type VARCHAR(50) NOT NULL,
        location VARCHAR(255) NOT NULL,
        salary_range VARCHAR(100) NOT NULL,
        requirements TEXT NOT NULL,
        responsibilities TEXT,
        application_deadline DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'active',
        is_featured TINYINT(1) DEFAULT 0,
        FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    runQuery($conn, $sql, "create jobs table");
}

// Create applications table
if (!tableExists($conn, 'applications')) {
    $sql = "CREATE TABLE applications (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        job_id INT(11) UNSIGNED NOT NULL,
        jobseeker_id INT(11) UNSIGNED NOT NULL,
        cover_letter TEXT,
        resume VARCHAR(255),
        additional_documents TEXT,
        application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('applied', 'reviewing', 'shortlisted', 'interviewed', 'offered', 'hired', 'rejected') DEFAULT 'applied',
        employer_notes TEXT,
        FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
        FOREIGN KEY (jobseeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE
    )";
    
    runQuery($conn, $sql, "create applications table");
}

// Create success_stories table
if (!tableExists($conn, 'success_stories')) {
    $sql = "CREATE TABLE success_stories (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        jobseeker_id INT(11) UNSIGNED NOT NULL,
        employer_id INT(11) UNSIGNED NOT NULL,
        job_title VARCHAR(255) NOT NULL,
        testimonial TEXT NOT NULL,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_featured TINYINT(1) DEFAULT 0,
        FOREIGN KEY (jobseeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE,
        FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
    )";
    
    runQuery($conn, $sql, "create success_stories table");
}

// Insert sample data if tables are empty
echo "<h2>Adding Sample Data</h2>";

// Check if employers table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM employers");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Add sample employers
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO employers (company_name, contact_person, email, phone, password, company_description, industry, location) VALUES
        ('Kigali Café', 'Jean Mugabo', 'info@kigalicafe.com', '+250781234567', '$password', 'A cozy café in the heart of Kigali offering local and international cuisine.', 'Hospitality', 'Kigali'),
        ('Tech Rwanda', 'Alice Uwase', 'hr@techrwanda.com', '+250789876543', '$password', 'Leading technology company specializing in software development and IT consulting.', 'Technology', 'Kigali'),
        ('Nyanza Retail Store', 'Patrick Ndimubandi', 'manager@nyanzaretail.com', '+250723456789', '$password', 'Family-owned retail store providing quality products at affordable prices.', 'Retail', 'Nyanza')";
    
    runQuery($conn, $sql, "add sample employers");
}

// Check if job_seekers table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM job_seekers");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Add sample job seekers
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO job_seekers (full_name, email, phone, password, skills, experience, education, preferred_job_types, preferred_locations, bio) VALUES
        ('Marie Ingabire', 'marie@example.com', '+250721234567', '$password', 'Customer Service, Communication, Food Service', '2 years as waitress at local restaurant', 'Bachelor in Hospitality Management', 'part-time,seasonal', 'Kigali', 'Enthusiastic hospitality professional seeking opportunities in customer-facing roles.'),
        ('Emmanuel Nziza', 'emmanuel@example.com', '+250732345678', '$password', 'Web Development, Python, JavaScript', 'Freelance developer for 3 years', 'Computer Science Degree', 'part-time,contract', 'Kigali,Huye', 'Passionate developer with experience in building responsive websites and applications.'),
        ('Claire Mukashema', 'claire@example.com', '+250743456789', '$password', 'Sales, Customer Relations, Inventory Management', 'Sales associate for 1 year', 'Business Administration Diploma', 'part-time,one-time', 'Nyanza,Musanze', 'Detail-oriented sales professional with excellent communication skills.')";
    
    runQuery($conn, $sql, "add sample job seekers");
}

// Check if jobs table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM jobs");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Get employer IDs
    $result = $conn->query("SELECT id FROM employers LIMIT 3");
    $employers = [];
    while ($row = $result->fetch_assoc()) {
        $employers[] = $row['id'];
    }
    
    if (count($employers) >= 3) {
        // Add sample jobs
        $today = date('Y-m-d');
        $deadline = date('Y-m-d', strtotime('+30 days'));
        
        $sql = "INSERT INTO jobs (employer_id, job_title, job_description, job_type, location, salary_range, requirements, responsibilities, application_deadline, is_featured) VALUES
            ({$employers[0]}, 'Weekend Barista', 'Looking for a talented barista to join our weekend team. Experience with espresso machines and latte art preferred.', 'part-time', 'Kigali', '50,000-70,000 RWF/month', 'Previous experience as barista, Customer service skills, Ability to work weekends', 'Prepare coffee drinks, Maintain cleanliness, Handle customer orders', '$deadline', 1),
            ({$employers[1]}, 'Junior Web Developer', 'Need a part-time web developer to assist with ongoing projects. Knowledge of HTML, CSS, and JavaScript required.', 'contract', 'Kigali', '100,000-150,000 RWF/month', 'HTML/CSS/JavaScript skills, Basic understanding of PHP or Python, Portfolio of previous work', 'Develop website features, Debug code, Implement responsive designs', '$deadline', 1),
            ({$employers[2]}, 'Retail Sales Associate', 'Seeking friendly and reliable sales associates for our busy retail store. Perfect for students or those seeking part-time work.', 'part-time', 'Nyanza', '40,000-60,000 RWF/month', 'Strong communication skills, Basic math abilities, Availability on weekends', 'Assist customers, Operate cash register, Restock merchandise', '$deadline', 1),
            ({$employers[0]}, 'Event Server', 'Need servers for upcoming corporate events. Experience in food service preferred but not required.', 'one-time', 'Kigali', '10,000-15,000 RWF/day', 'Professional appearance, Ability to stand for long periods, Team player', 'Serve food and beverages, Set up and clean event spaces, Interact with guests', '$deadline', 1),
            ({$employers[1]}, 'Data Entry Specialist', 'Looking for detail-oriented individuals to assist with data entry projects. Flexible hours available.', 'part-time', 'Kigali', '80,000-100,000 RWF/month', 'Fast typing skills, Attention to detail, Experience with Excel', 'Input data accurately, Verify information, Maintain databases', '$deadline', 1),
            ({$employers[2]}, 'Inventory Assistant', 'Seasonal position helping with inventory management during our busy period. Training provided.', 'seasonal', 'Nyanza', '45,000-55,000 RWF/month', 'Organizational skills, Basic computer knowledge, Ability to lift up to 10kg', 'Count stock, Update inventory records, Assist with deliveries', '$deadline', 1)";
        
        runQuery($conn, $sql, "add sample jobs");
    }
}

// Check if success_stories table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM success_stories");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Get job seeker and employer IDs
    $result = $conn->query("SELECT id FROM job_seekers LIMIT 3");
    $jobseekers = [];
    while ($row = $result->fetch_assoc()) {
        $jobseekers[] = $row['id'];
    }
    
    $result = $conn->query("SELECT id FROM employers LIMIT 3");
    $employers = [];
    while ($row = $result->fetch_assoc()) {
        $employers[] = $row['id'];
    }
    
    if (count($jobseekers) >= 3 && count($employers) >= 3) {
        // Add sample success stories
        $sql = "INSERT INTO success_stories (jobseeker_id, employer_id, job_title, testimonial, is_featured) VALUES
            ({$jobseekers[0]}, {$employers[0]}, 'Weekend Barista', 'Ndahari helped me find a perfect weekend job that fits around my studies. The platform was easy to use and I received a response within days of applying!', 1),
            ({$jobseekers[1]}, {$employers[1]}, 'Junior Web Developer', 'I was looking for flexible contract work to build my portfolio and Ndahari connected me with a great tech company. The part-time arrangement allows me to continue freelancing while gaining valuable experience.', 1),
            ({$jobseekers[2]}, {$employers[2]}, 'Retail Sales Associate', 'Finding local work in Nyanza was challenging until I discovered Ndahari. The platform matched me with a retail position close to home with hours that work perfectly for my schedule.', 1)";
        
        runQuery($conn, $sql, "add sample success stories");
    }
}

// Close connection
$conn->close();

echo "<h2>Setup Complete!</h2>
<p>You can now return to the <a href='index.php'>homepage</a>.</p>
</body>
</html>";
?>