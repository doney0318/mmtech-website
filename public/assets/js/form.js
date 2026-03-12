// ========== Contact Form Handling ==========
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData);
        
        // Show success message (in production, send to backend)
        alert('感谢您的留言！我们会尽快与您联系。');
        contactForm.reset();
        
        console.log('Form submitted:', data);
    });
}
