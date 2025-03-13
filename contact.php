<?php
// Include header (using relative path for portability)
include('includes/header.php');

// Initialize variables
$formSubmitted = false;
$formSuccess = false;
$formErrors = [];

// Check for session messages
if (isset($_SESSION['contact_form_status'])) {
    $formSubmitted = true;
    $formSuccess = $_SESSION['contact_form_status']['success'];
    $formErrors = $_SESSION['contact_form_status']['errors'] ?? [];
    
    // Clear the session data
    unset($_SESSION['contact_form_status']);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    if (empty($_POST['name'])) {
        $formErrors['name'] = "Name is required";
    }
    
    if (empty($_POST['email'])) {
        $formErrors['email'] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $formErrors['email'] = "Invalid email format";
    }
    
    if (empty($_POST['phone'])) {
        $formErrors['phone'] = "Phone number is required";
    }
    
    if (empty($_POST['message'])) {
        $formErrors['message'] = "Message is required";
    } elseif (strlen($_POST['message']) < 10) {
        $formErrors['message'] = "Message must be at least 10 characters";
    }
    
    // If no errors, process the form
    if (empty($formErrors)) {
        // In a real application, you would send an email or save to database here
        // For demonstration, we'll just set a success flag
        $formSubmitted = true;
        $formSuccess = true;
        
        // Optional: Send email notification
        // mail('your-email@example.com', 'Contact Form Submission', $_POST['message'], "From: {$_POST['email']}");
    } else {
        $formSubmitted = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Ndahari</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="public/css/contact.css">
</head>
<body>
    <!-- Toast Notifications Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Main Content Section -->
    <div class="container">
        <!-- Contact Section Header -->
        <div class="contact-header">
            <h1>Contact Us</h1>
            <p>We're here to help! Reach out to us for any inquiries or assistance.</p>
        </div>

        <!-- Contact Form and Contact Information Sections -->
        <div class="contact-sections">
            <!-- Contact Form Section -->
            <section class="contact-form">
                <h2>Send Us a Message</h2>
                
                <?php if ($formSubmitted): ?>
                    <div class="alert <?php echo $formSuccess ? 'alert-success' : 'alert-danger'; ?>">
                        <?php 
                        if ($formSuccess) {
                            echo '<i class="fas fa-check-circle"></i> Thank you for your message! We\'ll get back to you shortly.';
                        } else {
                            echo '<i class="fas fa-exclamation-circle"></i> ';
                            if (!empty($formErrors['database'])) {
                                echo htmlspecialchars($formErrors['database']);
                            } else {
                                echo 'Please correct the errors in the form.';
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <form id="contactForm" action="submit_contact_form.php" method="POST">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" class="form-control <?php echo isset($formErrors['name']) ? 'is-invalid' : ''; ?>" 
                               id="name" name="name" placeholder="Enter your full name" 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        <?php if (isset($formErrors['name'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['name']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control <?php echo isset($formErrors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" placeholder="Enter your email address" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <?php if (isset($formErrors['email'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control <?php echo isset($formErrors['phone']) ? 'is-invalid' : ''; ?>" 
                               id="phone" name="phone" placeholder="Enter your phone number" 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        <?php if (isset($formErrors['phone'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['phone']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="inquiry">Inquiry Type</label>
                        <select class="form-control" id="inquiry" name="inquiry">
                            <option value="general" <?php echo (isset($_POST['inquiry']) && $_POST['inquiry'] == 'general') ? 'selected' : ''; ?>>General Inquiry</option>
                            <option value="support" <?php echo (isset($_POST['inquiry']) && $_POST['inquiry'] == 'support') ? 'selected' : ''; ?>>Technical Support</option>
                            <option value="jobs" <?php echo (isset($_POST['inquiry']) && $_POST['inquiry'] == 'jobs') ? 'selected' : ''; ?>>Job Opportunities</option>
                            <option value="partnership" <?php echo (isset($_POST['inquiry']) && $_POST['inquiry'] == 'partnership') ? 'selected' : ''; ?>>Business Partnership</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea class="form-control <?php echo isset($formErrors['message']) ? 'is-invalid' : ''; ?>" 
                                  id="message" name="message" placeholder="How can we help you?"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        <?php if (isset($formErrors['message'])): ?>
                            <div class="invalid-feedback"><?php echo $formErrors['message']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span class="spinner"></span>
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </section>

            <!-- Contact Information Section -->
            <section class="contact-info">
                <h3>Other Ways to Reach Us</h3>
                <ul>
                    <li>
                        <div class="icon-wrapper">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <strong>Email</strong>
                            <a href="mailto:info@edujobsscholars.com">info@edujobsscholars.com</a>
                        </div>
                    </li>
                    
                    <li>
                        <div class="icon-wrapper">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <strong>Primary Phone</strong>
                            <a href="tel:+250787846344">+250 787 846 344</a>
                        </div>
                    </li>
                    
                    <li>
                        <div class="icon-wrapper">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <strong>Secondary Phone</strong>
                            <a href="tel:+250734733221">+250 734 733 221</a>
                        </div>
                    </li>
                    
                    <li>
                        <div class="icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <strong>Address</strong>
                            <span>Kigali, Rwanda</span>
                        </div>
                    </li>
                </ul>
            </section>
        </div>

        <!-- Map Section -->
        <div class="map-section">
            <h2>Find Us</h2>
            <div class="map-container" id="map">
                <!-- Map will be inserted here by JavaScript -->
                <div style="width: 100%; height: 100%; background: #eee; display: flex; align-items: center; justify-content: center; color: #666;">
                    <p>Loading map...</p>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item" data-faq="1">
                    <div class="faq-question">How do I apply for a job?</div>
                    <div class="faq-answer">
                        <p>Create an account, complete your profile, and apply directly to job listings that match your skills and preferences. Our system will guide you through the entire process.</p>
                    </div>
                </div>

                <div class="faq-item" data-faq="2">
                    <div class="faq-question">How do I post a job?</div>
                    <div class="faq-answer">
                        <p>Register as an employer, complete your company profile, and use the "Post a Job" option to create your listing. You can set requirements, compensation, and see applications directly through your dashboard.</p>
                    </div>
                </div>

                <div class="faq-item" data-faq="3">
                    <div class="faq-question">Is there a fee for using Ndahari?</div>
                    <div class="faq-answer">
                        <p>Job seekers can use Ndahari for free. Employers pay a small fee only when they successfully hire through our platform. There are no upfront costs or subscription fees.</p>
                    </div>
                </div>

                <div class="faq-item" data-faq="4">
                    <div class="faq-question">How are payments handled?</div>
                    <div class="faq-answer">
                        <p>Our secure payment system handles all transactions. Employers fund jobs upfront, and workers are paid promptly after job completion. We support various payment methods, including mobile money.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Home Button -->
        <div class="back-to-home">
            <a href="index.php">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>
    </div>

    <!-- Floating WhatsApp Icon -->
    <a href="https://wa.me/250787846344" target="_blank" rel="noopener noreferrer" class="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Floating Chat Icon -->
    <div class="chat-float" id="chatButton">
        <i class="fas fa-comments"></i>
    </div>

    <!-- Chat Box -->
    <div class="chat-box" id="chatBox">
        <div class="chat-header">
            <h4>Live Chat Support</h4>
            <button class="chat-close" id="chatClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="chat-message bot">Hello! How can I help you today?</div>
        </div>
        <div class="chat-input-container">
            <input type="text" class="chat-input" id="chatInput" placeholder="Type your message...">
            <button class="chat-send" id="chatSend"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <!-- JavaScript for Form Validation and Interactivity -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation and submission with loading state
            const contactForm = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitBtn');

            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    const nameInput = document.getElementById('name');
                    const emailInput = document.getElementById('email');
                    const phoneInput = document.getElementById('phone');
                    const messageInput = document.getElementById('message');
                    
                    let isValid = true;
                    
                    // Validation
                    if (nameInput.value.trim() === '') {
                        nameInput.classList.add('is-invalid');
                        if (!nameInput.nextElementSibling) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Please enter your name';
                            nameInput.after(feedback);
                        }
                        isValid = false;
                    } else {
                        nameInput.classList.remove('is-invalid');
                    }
                    
                    if (emailInput.value.trim() === '' || !isValidEmail(emailInput.value)) {
                        emailInput.classList.add('is-invalid');
                        if (!emailInput.nextElementSibling) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Please enter a valid email address';
                            emailInput.after(feedback);
                        }
                        isValid = false;
                    } else {
                        emailInput.classList.remove('is-invalid');
                    }
                    
                    if (phoneInput.value.trim() === '') {
                        phoneInput.classList.add('is-invalid');
                        if (!phoneInput.nextElementSibling) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Please enter your phone number';
                            phoneInput.after(feedback);
                        }
                        isValid = false;
                    } else {
                        phoneInput.classList.remove('is-invalid');
                    }
                    
                    if (messageInput.value.trim() === '' || messageInput.value.trim().length < 10) {
                        messageInput.classList.add('is-invalid');
                        if (!messageInput.nextElementSibling) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'Please enter a message of at least 10 characters';
                            messageInput.after(feedback);
                        }
                        isValid = false;
                    } else {
                        messageInput.classList.remove('is-invalid');
                    }
                    
                    // If client-side validation passes, show loading state
                    if (isValid) {
                        submitBtn.classList.add('loading');
                        // Form will submit normally
                    } else {
                        e.preventDefault();
                    }
                });
            }
            
            // Email validation helper
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            // Live validation on input
            const formInputs = document.querySelectorAll('.form-control');
            formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                        const feedback = this.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.remove();
                        }
                    }
                });
            });
            
            // Show toast notification
            function showToast(message, type = 'info') {
                const toastContainer = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.innerHTML = `
                    ${message}
                    <button class="toast-close">&times;</button>
                `;
                toastContainer.appendChild(toast);
                
                // Show the toast with a slight delay
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);
                
                // Auto-hide after 5 seconds
                const hideTimeout = setTimeout(() => {
                    hideToast(toast);
                }, 5000);
                
                // Close button functionality
                const closeBtn = toast.querySelector('.toast-close');
                closeBtn.addEventListener('click', () => {
                    clearTimeout(hideTimeout);
                    hideToast(toast);
                });
            }
            
            function hideToast(toast) {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
            
            // Chat functionality
            const chatButton = document.getElementById('chatButton');
            const chatBox = document.getElementById('chatBox');
            const chatClose = document.getElementById('chatClose');
            const chatInput = document.getElementById('chatInput');
            const chatSend = document.getElementById('chatSend');
            const chatMessages = document.getElementById('chatMessages');
            
            // Toggle chat box
            chatButton.addEventListener('click', function() {
                chatBox.classList.toggle('active');
                if (chatBox.classList.contains('active')) {
                    chatInput.focus();
                }
            });
            
            // Close chat box
            chatClose.addEventListener('click', function() {
                chatBox.classList.remove('active');
            });
            
            // Send message
            function sendMessage() {
                const message = chatInput.value.trim();
                if (message) {
                    // Add user message
                    const userMessageEl = document.createElement('div');
                    userMessageEl.className = 'chat-message user';
                    userMessageEl.textContent = message;
                    chatMessages.appendChild(userMessageEl);
                    
                    // Clear input
                    chatInput.value = '';
                    
                    // Scroll to bottom
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    
                    // Show typing indicator
                    const typingIndicator = document.createElement('div');
                    typingIndicator.className = 'typing-indicator';
                    typingIndicator.innerHTML = '<span></span><span></span><span></span>';
                    chatMessages.appendChild(typingIndicator);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    
                    // Simulate reply after delay
                    setTimeout(() => {
                        // Remove typing indicator
                        typingIndicator.remove();
                        
                        // Add bot reply
                        const botReplies = [
                            "Thank you for your message! Our team will get back to you shortly.",
                            "I understand your query. Let me connect you with a specialist.",
                            "Thanks for reaching out! How else can I assist you today?",
                            "That's a great question. We offer several solutions for that.",
                            "I'll make sure your inquiry gets to the right department."
                        ];
                        
                        const randomReply = botReplies[Math.floor(Math.random() * botReplies.length)];
                        
                        const botMessageEl = document.createElement('div');
                        botMessageEl.className = 'chat-message bot';
                        botMessageEl.textContent = randomReply;
                        chatMessages.appendChild(botMessageEl);
                        
                        // Scroll to bottom again
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 1500);
                }
            }
            
            chatSend.addEventListener('click', sendMessage);
            
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
            
            // FAQ Accordion
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                question.addEventListener('click', () => {
                    // Toggle active class
                    item.classList.toggle('active');
                });
            });
            
            // Map initialization
            function initMap() {
                // This would usually be replaced with actual map API code (Google Maps, Leaflet, etc.)
                // For this example, we'll just replace the placeholder
                const mapContainer = document.getElementById('map');
                
                if (mapContainer) {
                    mapContainer.innerHTML = `
                        <div style="width: 100%; height: 100%; background: #e9ecef; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #343a40; padding: 20px; text-align: center;">
                            <i class="fas fa-map-marked-alt" style="font-size: 3rem; margin-bottom: 20px; color: #4361ee;"></i>
                            <h3 style="margin-bottom: 10px;">Our Location</h3>
                            <p>Kigali, Rwanda</p>
                            <p>KG 574 St, Innovation City</p>
                            <button id="viewMapBtn" style="margin-top: 20px; padding: 8px 15px; background-color: #4361ee; color: white; border: none; border-radius: 5px; cursor: pointer;">View Full Map</button>
                        </div>
                    `;
                    
                    document.getElementById('viewMapBtn').addEventListener('click', function() {
                        // Open Google Maps in a new tab
                        window.open('https://maps.google.com/?q=Kigali,Rwanda', '_blank');
                    });
                }
            }
            
            // Initialize map
            initMap();
            
            // Example toast notification on page load
            setTimeout(() => {
                showToast('Welcome to our contact page! Feel free to reach out with any questions.', 'info');
            }, 1000);
        });
    </script>

    <?php
    // Include footer (using relative path for portability)
    include('includes/footer.php');
    ?>
</body>
</html>
