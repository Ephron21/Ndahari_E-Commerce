<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'includes/config.php';

// Check if we want to force adding sample jobs
$forceAdd = isset($_GET['force']) && $_GET['force'] == 'true';

// First, let's check the structure of the jobs table to see what columns are available
try {
    $stmt = $pdo->query("DESCRIBE jobs");
    $jobColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Available columns in jobs table: " . implode(", ", $jobColumns) . "</p>";
} catch (PDOException $e) {
    echo "Error checking jobs table structure: " . $e->getMessage();
    exit;
}

// Check if we already have jobs
$stmt = $pdo->query("SELECT COUNT(*) FROM jobs");
$jobCount = $stmt->fetchColumn();

if ($jobCount > 0 && !$forceAdd) {
    echo "Jobs already exist in the database. No sample jobs were added.<br>";
    echo "If you want to add sample jobs anyway, <a href='add_sample_jobs.php?force=true'>click here</a>.";
    exit;
}

// Check the structure of the employers table
try {
    $stmt = $pdo->query("DESCRIBE employers");
    $employerColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Available columns in employers table: " . implode(", ", $employerColumns) . "</p>";
} catch (PDOException $e) {
    echo "Error checking employers table structure: " . $e->getMessage();
}

// First, let's make sure we have some sample employers
$employers = [
    ['company_name' => 'Tech Innovations Ltd', 'contact_person' => 'John Smith', 'email' => 'careers@techinnovations.com', 'phone' => '0712345678', 'password' => password_hash('employer123', PASSWORD_DEFAULT), 'company_description' => 'Tech Innovations Ltd is a leading technology company specializing in software development, web applications, and mobile solutions. We create innovative digital products that help businesses thrive in the modern marketplace.', 'industry' => 'Technology', 'logo' => 'tech_innovations.png', 'website' => 'https://techinnovations.com', 'location' => 'Nairobi'],
    
    ['company_name' => 'Green Earth Sustainability', 'contact_person' => 'Emily Johnson', 'email' => 'jobs@greenearth.org', 'phone' => '0723456789', 'password' => password_hash('employer123', PASSWORD_DEFAULT), 'company_description' => 'Green Earth Sustainability is a non-profit organization dedicated to environmental conservation and sustainable development. We work with communities, businesses, and government agencies to implement eco-friendly practices and protect natural resources.', 'industry' => 'Environmental', 'logo' => 'green_earth.png', 'website' => 'https://greenearth.org', 'location' => 'Mombasa'],
    
    ['company_name' => 'Global Education Center', 'contact_person' => 'Michael Brown', 'email' => 'hr@globaledu.com', 'phone' => '0734567890', 'password' => password_hash('employer123', PASSWORD_DEFAULT), 'company_description' => 'Global Education Center is a premier educational institution offering a wide range of academic programs and professional development courses. We are committed to providing high-quality education that prepares students for success in a rapidly changing world.', 'industry' => 'Education', 'logo' => 'global_edu.png', 'website' => 'https://globaledu.com', 'location' => 'Kisumu'],
    
    ['company_name' => 'Health First Medical', 'contact_person' => 'Sarah Wilson', 'email' => 'careers@healthfirst.med', 'phone' => '0745678901', 'password' => password_hash('employer123', PASSWORD_DEFAULT), 'company_description' => 'Health First Medical is a comprehensive healthcare provider offering a wide range of medical services. Our team of dedicated professionals is committed to delivering exceptional patient care and promoting community health and wellness.', 'industry' => 'Healthcare', 'logo' => 'health_first.png', 'website' => 'https://healthfirst.med', 'location' => 'Nairobi'],
    
    ['company_name' => 'Creative Designs Agency', 'contact_person' => 'David Lee', 'email' => 'jobs@creativedesigns.com', 'phone' => '0756789012', 'password' => password_hash('employer123', PASSWORD_DEFAULT), 'company_description' => 'Creative Designs Agency is an award-winning creative studio specializing in branding, graphic design, and digital marketing. We help businesses establish strong visual identities and create compelling content that resonates with their target audience.', 'industry' => 'Design', 'logo' => 'creative_designs.png', 'website' => 'https://creativedesigns.com', 'location' => 'Nakuru']
];

// Add employers if they don't exist
$employerIds = [];
foreach ($employers as $employer) {
    // Check if employer already exists
    $stmt = $pdo->prepare("SELECT id FROM employers WHERE email = ?");
    $stmt->execute([$employer['email']]);
    $existingEmployer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingEmployer) {
        $employerIds[] = $existingEmployer['id'];
        echo "Employer '" . $employer['company_name'] . "' already exists.<br>";
    } else {
        // Insert new employer with all required fields
        $stmt = $pdo->prepare("
            INSERT INTO employers (
                company_name, contact_person, email, phone, password, 
                company_description, industry, logo, website, location, 
                registration_date, status
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'active')
        ");
        $stmt->execute([
            $employer['company_name'],
            $employer['contact_person'],
            $employer['email'],
            $employer['phone'],
            $employer['password'],
            $employer['company_description'],
            $employer['industry'],
            $employer['logo'],
            $employer['website'],
            $employer['location']
        ]);
        $employerIds[] = $pdo->lastInsertId();
        echo "Added new employer: " . $employer['company_name'] . "<br>";
    }
}

// If we don't have enough employers, use the first one for all jobs
if (count($employerIds) < 5) {
    $defaultEmployerId = $employerIds[0];
    while (count($employerIds) < 5) {
        $employerIds[] = $defaultEmployerId;
    }
}

// Sample jobs data - improved with better formatting and organization
$sampleJobs = [
    [
        'title' => 'Part-Time Web Developer',
        'description' => "Tech Innovations Ltd is seeking a skilled Part-Time Web Developer to join our dynamic team. This position offers flexible hours and the opportunity to work on cutting-edge projects for diverse clients.

The ideal candidate will have strong skills in front-end and back-end development, with particular expertise in PHP, JavaScript, and MySQL. This role is perfect for professionals looking to balance other commitments while working in an innovative tech environment.

Hours: 20 hours per week
Location: Nairobi (with some remote work options)
Contract: Initial 6-month contract with possibility of extension",

        'requirements' => "• Bachelor's degree in Computer Science, Web Development, or related field
• Minimum 2 years of experience in web development
• Proficient in PHP, JavaScript, HTML5, CSS3, and MySQL
• Experience with responsive design and mobile-first approaches
• Knowledge of modern frameworks (Laravel, React, or Vue.js)
• Strong problem-solving skills and attention to detail
• Excellent communication and time management abilities
• Portfolio demonstrating previous web development projects",

        'responsibilities' => "• Develop and maintain responsive websites and web applications
• Write clean, efficient, and well-documented code
• Collaborate with designers and other team members to implement visual elements
• Ensure cross-browser compatibility and optimize for maximum speed
• Troubleshoot and debug issues as they arise
• Stay updated with emerging technologies and industry trends
• Participate in code reviews and team meetings
• Document development processes and maintain technical documentation",

        'job_type' => 'IT & Software',
        'location' => 'Nairobi',
        'salary_range' => '30,000 - 45,000 KSH per month',
        'application_deadline' => date('Y-m-d', strtotime('+30 days')),
        'employer_id' => $employerIds[0],
        'status' => 'open',
        'posted_date' => date('Y-m-d', strtotime('-3 days'))
    ],
    [
        'title' => 'Environmental Educator (Weekends)',
        'description' => "Green Earth Sustainability is looking for an enthusiastic Environmental Educator to join our education team on weekends. This role involves teaching environmental conservation concepts to school groups, families, and the general public through engaging programs and activities.

This position is ideal for individuals passionate about environmental education who are looking for weekend work to complement their weekday commitments. You'll play a key role in raising awareness about environmental issues and inspiring positive action.

Hours: 16 hours per weekend (Saturday and Sunday)
Location: Mombasa Conservation Center
Contract: Ongoing part-time position",

        'requirements' => "• Degree in Environmental Science, Education, Biology, or related field
• Previous experience in education, interpretation, or public speaking
• Strong knowledge of environmental conservation principles
• Excellent communication and presentation skills
• Ability to engage with diverse audiences, including children and adults
• Passion for environmental protection and sustainability
• Fluency in English and Swahili
• Availability to work weekends consistently",

        'responsibilities' => "• Develop and deliver engaging environmental education programs
• Lead guided tours of conservation areas and educational exhibits
• Create interactive learning materials and activities
• Adapt presentation style to different age groups and knowledge levels
• Organize and facilitate special environmental events and workshops
• Collect feedback and evaluate program effectiveness
• Maintain educational equipment and materials
• Stay updated on environmental issues and conservation practices
• Collaborate with other team members to improve educational offerings",

        'job_type' => 'Education & Training',
        'location' => 'Mombasa',
        'salary_range' => '25,000 - 30,000 KSH per month',
        'application_deadline' => date('Y-m-d', strtotime('+25 days')),
        'employer_id' => $employerIds[1],
        'status' => 'open',
        'posted_date' => date('Y-m-d', strtotime('-5 days'))
    ],
    [
        'title' => 'Evening Mathematics Tutor',
        'description' => "Global Education Center is seeking a qualified Evening Mathematics Tutor to provide academic support to high school students. This position involves helping students understand mathematical concepts, complete homework assignments, and prepare for examinations.

The ideal candidate will have a strong background in mathematics and the ability to explain complex concepts in a clear, patient manner. This role is perfect for educators looking for additional teaching opportunities in the evenings.

Hours: 15 hours per week (Monday-Friday, 5:00 PM - 8:00 PM)
Location: Kisumu Learning Center
Contract: School term-based contract (renewable)",

        'requirements' => "• Bachelor's degree in Mathematics, Education, or related field
• Strong knowledge of high school mathematics curriculum
• Previous tutoring or teaching experience preferred
• Patient and encouraging teaching approach
• Excellent communication and interpersonal skills
• Ability to adapt teaching methods to different learning styles
• Reliable and punctual with excellent time management
• Passion for helping students succeed academically",

        'responsibilities' => "• Provide one-on-one and small group tutoring in mathematics
• Assess students' current understanding and identify areas for improvement
• Develop personalized learning plans to address specific needs
• Assist with homework completion and exam preparation
• Teach problem-solving strategies and study techniques
• Track and document student progress
• Communicate regularly with parents about student development
• Create and provide supplementary learning materials
• Maintain a positive and encouraging learning environment",

        'job_type' => 'Education & Training',
        'location' => 'Kisumu',
        'salary_range' => '20,000 - 25,000 KSH per month',
        'application_deadline' => date('Y-m-d', strtotime('+20 days')),
        'employer_id' => $employerIds[2],
        'status' => 'open',
        'posted_date' => date('Y-m-d', strtotime('-2 days'))
    ],
    [
        'title' => 'Weekend Nurse Assistant',
        'description' => "Health First Medical is seeking a compassionate and dedicated Weekend Nurse Assistant to join our healthcare team. This role involves providing essential support to nursing staff and ensuring patient comfort and care during weekend shifts.

This position is ideal for healthcare professionals looking for weekend work to supplement their income or gain additional experience in a clinical setting. You'll be an integral part of our patient care team, helping to maintain our high standards of healthcare delivery.

Hours: 24 hours per weekend (12-hour shifts on Saturday and Sunday)
Location: Health First Medical Center, Nairobi
Contract: Permanent part-time position",

        'requirements' => "• Certified Nurse Assistant qualification or equivalent
• Previous experience in a healthcare setting preferred
• Knowledge of basic patient care procedures
• Understanding of medical terminology and healthcare protocols
• Compassionate attitude and strong interpersonal skills
• Physical stamina and ability to assist with patient mobility
• Attention to detail in monitoring and recording patient information
• Reliable and punctual with ability to work 12-hour shifts
• Current BLS/CPR certification",

        'responsibilities' => "• Assist nursing staff with patient care activities
• Monitor and record vital signs (temperature, pulse, blood pressure)
• Help patients with personal hygiene, feeding, and mobility
• Respond promptly to patient calls and requests
• Maintain clean and orderly patient rooms
• Assist with patient intake and discharge procedures
• Document patient information accurately in medical records
• Report any changes in patient condition to nursing staff
• Follow all health and safety protocols
• Participate in team meetings and training sessions",

        'job_type' => 'Healthcare',
        'location' => 'Nairobi',
        'salary_range' => '35,000 - 40,000 KSH per month',
        'application_deadline' => date('Y-m-d', strtotime('+15 days')),
        'employer_id' => $employerIds[3],
        'status' => 'open',
        'posted_date' => date('Y-m-d', strtotime('-7 days'))
    ],
    [
        'title' => 'Graphic Design Assistant',
        'description' => "Creative Designs Agency is looking for a talented Graphic Design Assistant to join our creative team on a part-time basis. This role involves supporting senior designers with various projects and creating visual content for both digital and print media.

This position is perfect for design professionals looking to gain agency experience while maintaining a flexible schedule. You'll have the opportunity to work on diverse projects for a variety of clients across different industries.

Hours: 20 hours per week (flexible scheduling)
Location: Nakuru Design Studio
Contract: 6-month initial contract with possibility of extension",

        'requirements' => "• Diploma or degree in Graphic Design, Visual Arts, or related field
• Proficiency in Adobe Creative Suite (Photoshop, Illustrator, InDesign)
• Strong portfolio demonstrating design skills and creativity
• Knowledge of design principles, typography, and color theory
• Attention to detail and ability to follow brand guidelines
• Good time management and ability to meet deadlines
• Basic understanding of print production processes
• Familiarity with digital design for web and social media
• Excellent communication and teamwork skills",

        'responsibilities' => "• Create graphics for social media, websites, and marketing materials
• Assist senior designers with ongoing projects
• Prepare and format files for both digital and print production
• Design layouts for brochures, flyers, and other promotional materials
• Create and edit images, illustrations, and other visual elements
• Ensure all designs adhere to brand guidelines and specifications
• Participate in brainstorming sessions and contribute creative ideas
• Organize and maintain design files and assets
• Stay updated on design trends and techniques
• Receive and implement feedback from the creative team",

        'job_type' => 'Design & Creative',
        'location' => 'Nakuru',
        'salary_range' => '28,000 - 35,000 KSH per month',
        'application_deadline' => date('Y-m-d', strtotime('+10 days')),
        'employer_id' => $employerIds[4],
        'status' => 'open',
        'posted_date' => date('Y-m-d', strtotime('-1 day'))
    ]
];

// Insert sample jobs
$stmt = $pdo->prepare("
    INSERT INTO jobs (
        title, description, requirements, responsibilities, job_type, 
        location, salary_range, application_deadline, employer_id, 
        status, posted_date
    ) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$jobsAdded = 0;
foreach ($sampleJobs as $job) {
    // Check if this exact job already exists
    $checkStmt = $pdo->prepare("
        SELECT COUNT(*) FROM jobs 
        WHERE title = ? AND employer_id = ? AND job_type = ? AND location = ?
    ");
    $checkStmt->execute([
        $job['title'],
        $job['employer_id'],
        $job['job_type'],
        $job['location']
    ]);
    
    $exists = $checkStmt->fetchColumn() > 0;
    
    if ($exists && !$forceAdd) {
        echo "Job '" . $job['title'] . "' already exists. Skipping.<br>";
        continue;
    }
    
    try {
        $stmt->execute([
            $job['title'],
            $job['description'],
            $job['requirements'],
            $job['responsibilities'],
            $job['job_type'],
            $job['location'],
            $job['salary_range'],
            $job['application_deadline'],
            $job['employer_id'],
            $job['status'],
            $job['posted_date']
        ]);
        $jobsAdded++;
        echo "Added job: " . $job['title'] . "<br>";
    } catch (PDOException $e) {
        echo "Error adding job '" . $job['title'] . "': " . $e->getMessage() . "<br>";
    }
}

echo "<br><strong>Successfully added $jobsAdded sample jobs to the database!</strong>";
echo "<br><a href='index.php'>Return to homepage</a>";
?> 