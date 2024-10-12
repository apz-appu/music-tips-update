
// Select all navigation links
const navLinks = document.querySelectorAll('.nav-link');

// Add a scroll event listener
window.addEventListener('scroll', () => {
    // Get the current scroll position
    let scrollPos = window.scrollY + window.innerHeight / 3; // Adjust the view height offset

    // Loop through each section
    document.querySelectorAll('section').forEach(section => {
        // Get the section's top position and its height
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        
        // Check if the scroll position is within the section
        if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
            // Get the corresponding navigation link
            const currentLink = document.querySelector(`.navigator a[href="#${section.id}"]`);
            
            // Remove the active class from all links
            navLinks.forEach(link => link.classList.remove('active'));
            
            // Add the active class to the current link
            if (currentLink) {
                currentLink.classList.add('active');
            }
        }
    });
});
