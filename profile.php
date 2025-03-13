<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file = $_FILES['profile_picture'];
            
            if (in_array($file['type'], $allowed_types)) {
                $upload_result = handle_file_upload($file, $_SESSION['user_id'], 'image');
                if ($upload_result) {
                    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                    $stmt->execute([$upload_result, $_SESSION['user_id']]);
                }
            }
        }

        // Update user information
        $stmt = $pdo->prepare("
            UPDATE users SET 
                first_name = ?,
                last_name = ?,
                email = ?,
                phone = ?,
                address = ?,
                city = ?,
                country = ?,
                bio = ?,
                skills = ?,
                experience = ?
            WHERE id = ?
        ");

        $stmt->execute([
            sanitize_input($_POST['first_name']),
            sanitize_input($_POST['last_name']),
            sanitize_input($_POST['email']),
            sanitize_input($_POST['phone']),
            sanitize_input($_POST['address']),
            sanitize_input($_POST['city']),
            sanitize_input($_POST['country']),
            sanitize_input($_POST['bio']),
            sanitize_input($_POST['skills']),
            sanitize_input($_POST['experience']),
            $_SESSION['user_id']
        ]);

        $success_message = "Profile updated successfully!";
    } catch (PDOException $e) {
        error_log("Error updating profile: " . $e->getMessage());
        $error_message = "An error occurred while updating your profile. Please try again.";
    }
}

// Get user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $error_message = "An error occurred while fetching your profile data.";
}

// Include header
require_once 'includes/header.php';
?>

<main class="profile-page">
    <div class="container">
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="profile-header">
            <h1>My Profile</h1>
            <p class="text-muted">Manage your personal information and preferences</p>
        </div>

        <div class="profile-content">
            <form action="profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="profile-section">
                    <h2>Profile Picture</h2>
                    <div class="profile-picture-section">
                        <div class="current-picture">
                            <img src="<?php echo $user['profile_picture'] ?? 'images/default-avatar.png'; ?>" 
                                 alt="Profile Picture" 
                                 class="profile-picture"
                                 onerror="this.src='images/default-avatar.png';">
                        </div>
                        <div class="picture-upload">
                            <label for="profile_picture" class="upload-label">
                                <i class="fas fa-camera"></i>
                                Change Picture
                            </label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="file-input">
                            <p class="upload-help">Maximum file size: 5MB. Supported formats: JPG, PNG, GIF</p>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Personal Information</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Location</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" 
                                   value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" 
                                   value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="country" 
                                   value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Professional Information</h2>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="skills">Skills (separate with commas)</label>
                        <input type="text" id="skills" name="skills" 
                               value="<?php echo htmlspecialchars($user['skills'] ?? ''); ?>"
                               placeholder="e.g., Customer Service, Sales, Marketing">
                    </div>
                    <div class="form-group">
                        <label for="experience">Work Experience</label>
                        <textarea id="experience" name="experience" rows="4"
                                placeholder="Brief summary of your work experience"><?php echo htmlspecialchars($user['experience'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.profile-page {
    padding: 2rem 0;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.profile-header {
    margin-bottom: 2rem;
    text-align: center;
}

.profile-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.profile-content {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.profile-section:last-child {
    border-bottom: none;
}

.profile-section h2 {
    font-size: 1.25rem;
    color: #333;
    margin-bottom: 1.5rem;
}

.profile-picture-section {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.current-picture {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-picture {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.picture-upload {
    flex-grow: 1;
}

.upload-label {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-label:hover {
    background: #e9ecef;
}

.upload-label i {
    margin-right: 0.5rem;
}

.file-input {
    display: none;
}

.upload-help {
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #007bff;
    outline: none;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
    text-decoration: none;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .profile-picture-section {
        flex-direction: column;
        text-align: center;
    }
    
    .current-picture {
        margin: 0 auto;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?> 