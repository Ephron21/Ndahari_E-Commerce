document.addEventListener('DOMContentLoaded', function() {
    // Toggle between job seeker and employer forms
    const jobSeekerBtn = document.getElementById('job-seeker-btn');
    const employerBtn = document.getElementById('employer-btn');
    const jobSeekerForm = document.getElementById('job-seeker-form');
    const employerForm = document.getElementById('employer-form');
    
    jobSeekerBtn.addEventListener('click', function() {
        jobSeekerForm.style.display = 'block';
        employerForm.style.display = 'none';
        jobSeekerBtn.classList.add('active');
        employerBtn.classList.remove('active');
    });
    
    employerBtn.addEventListener('click', function() {
        jobSeekerForm.style.display = 'none';
        employerForm.style.display = 'block';
        employerBtn.classList.add('active');
        jobSeekerBtn.classList.remove('active');
    });
    
    // Helper function for form validation
    function showError(element, message) {
        element.classList.add('is-invalid');
        const feedback = element.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        }
    }
    
    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }
    
    // Job Seeker Form Validation
    jobSeekerForm.addEventListener('submit', function(event) {
        event.preventDefault();
        clearErrors();
        
        let isValid = true;
        
        // Password match validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }
        
        // Validate file size for uploads
        const idDocument = document.getElementById('id_document');
        const resume = document.getElementById('resume');
        const profileImage = document.getElementById('profile_image');
        
        if (idDocument.files[0] && idDocument.files[0].size > 2 * 1024 * 1024) {
            showError(idDocument, 'ID Document file size exceeds 2MB limit');
            isValid = false;
        }
        
        if (resume.files[0] && resume.files[0].size > 2 * 1024 * 1024) {
            showError(resume, 'Resume file size exceeds 2MB limit');
            isValid = false;
        }
        
        if (profileImage.files[0] && profileImage.files[0].size > 1 * 1024 * 1024) {
            showError(profileImage, 'Profile Image file size exceeds 1MB limit');
            isValid = false;
        }
        
        // Validate phone number format
        const phoneRegex = /^\+?[0-9]{10,15}$/;
        const phone = document.getElementById('phone');
        
        if (!phoneRegex.test(phone.value)) {
            showError(phone, 'Please enter a valid phone number (10-15 digits)');
            isValid = false;
        }
        
        // Validate at least one availability option is selected
        const availabilityCheckboxes = document.querySelectorAll('input[name="availability[]"]:checked');
        if (availabilityCheckboxes.length === 0) {
            // Fixed this line - find the first availability checkbox and get its parent container
            const firstAvailabilityCheckbox = document.querySelector('input[name="availability[]"]');
            if (firstAvailabilityCheckbox) {
                const container = firstAvailabilityCheckbox.closest('.form-group');
                showError(firstAvailabilityCheckbox, 'Please select at least one availability option');
                // Add the invalid-feedback message correctly
                if (container) {
                    const feedback = container.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.textContent = 'Please select at least one availability option';
                    }
                }
            }
            isValid = false;
        }
        
        if (isValid) {
            // Show loading spinner
            this.querySelector('.spinner-border').style.display = 'inline-block';
            
            // Submit the form using AJAX
            const formData = new FormData(this);
            
            fetch('process_registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                this.querySelector('.spinner-border').style.display = 'none';
                
                // Display message
                const messageContainer = document.getElementById('form-messages');
                if (data.status === 'success') {
                    messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    // Reset form on success
                    this.reset();
                    // Redirect to login page after short delay
                    setTimeout(() => {
                        window.location.href = 'signin.php';
                    }, 2000);
                } else {
                    messageContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    // Handle specific field errors
                    if (data.errors) {
                        for (const field in data.errors) {
                            const element = document.getElementById(field);
                            if (element) {
                                showError(element, data.errors[field]);
                            }
                        }
                    }
                }
            })
            .catch(error => {
                // Hide loading spinner
                this.querySelector('.spinner-border').style.display = 'none';
                document.getElementById('form-messages').innerHTML = `<div class="alert alert-danger">An error occurred. Please try again later.</div>`;
                console.error('Error:', error);
            });
        }
    });
    
    // Employer Form Validation
    employerForm.addEventListener('submit', function(event) {
        event.preventDefault();
        clearErrors();
        
        let isValid = true;
        
        // Password match validation
        const companyPassword = document.getElementById('company_password');
        const companyConfirmPassword = document.getElementById('company_confirm_password');
        
        if (companyPassword.value !== companyConfirmPassword.value) {
            showError(companyConfirmPassword, 'Passwords do not match');
            isValid = false;
        }
        
        // Validate file size for company logo
        const companyLogo = document.getElementById('company_logo');
        if (companyLogo.files[0] && companyLogo.files[0].size > 2 * 1024 * 1024) {
            showError(companyLogo, 'Company logo file size exceeds 2MB limit');
            isValid = false;
        }
        
        // Validate company registration number format
        const regNumber = document.getElementById('registration_number');
        const regNumberRegex = /^[A-Z0-9]{5,15}$/;
        
        if (!regNumberRegex.test(regNumber.value)) {
            showError(regNumber, 'Please enter a valid registration number (5-15 alphanumeric characters)');
            isValid = false;
        }
        
        // Validate phone number format
        const companyPhone = document.getElementById('company_phone');
        const phoneRegex = /^\+?[0-9]{10,15}$/;
        
        if (!phoneRegex.test(companyPhone.value)) {
            showError(companyPhone, 'Please enter a valid phone number (10-15 digits)');
            isValid = false;
        }
        
        // Validate website URL
        const website = document.getElementById('company_website');
        const urlRegex = /^(https?:\/\/)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/;
        
        if (website.value && !urlRegex.test(website.value)) {
            showError(website, 'Please enter a valid website URL');
            isValid = false;
        }
        
        if (isValid) {
            // Show loading spinner
            this.querySelector('.spinner-border').style.display = 'inline-block';
            
            // Submit the form using AJAX
            const formData = new FormData(this);
            
            fetch('process_company_registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                this.querySelector('.spinner-border').style.display = 'none';
                
                // Display message
                const messageContainer = document.getElementById('employer-form-messages');
                if (data.status === 'success') {
                    messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    // Reset form on success
                    this.reset();
                    // Redirect to employer dashboard after short delay
                    setTimeout(() => {
                        window.location.href = 'employer_dashboard.php';
                    }, 2000);
                } else {
                    messageContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    // Handle specific field errors
                    if (data.errors) {
                        for (const field in data.errors) {
                            const element = document.getElementById(field);
                            if (element) {
                                showError(element, data.errors[field]);
                            }
                        }
                    }
                }
            })
            .catch(error => {
                // Hide loading spinner
                this.querySelector('.spinner-border').style.display = 'none';
                document.getElementById('employer-form-messages').innerHTML = `<div class="alert alert-danger">An error occurred. Please try again later.</div>`;
                console.error('Error:', error);
            });
        }
    });
    
    // Initialize forms - show job seeker form by default
    jobSeekerBtn.click();
});