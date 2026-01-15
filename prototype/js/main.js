
//Global variable to track current slide index
let slideIndex = 1;

//Initialize slideshow when page loads
window.addEventListener('DOMContentLoaded', function() {
    showSlides(slideIndex);
});

//Navigate to next/previous slide
function plusSlides(n) {
    showSlides(slideIndex += n);
}

//Jump to specific slide
function currentSlide(n) {
    showSlides(slideIndex = n);
}

//Display the specified slide and update thumbnail highlights
function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    let thumbnails = document.getElementsByClassName("thumbnail");
    
    // Wrap around if index goes beyond bounds
    if (n > slides.length) {
        slideIndex = 1;
    }
    if (n < 1) {
        slideIndex = slides.length;
    }
    
    // Hide all slides
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    
    // Remove active class from all thumbnails
    for (i = 0; i < thumbnails.length; i++) {
        thumbnails[i].className = thumbnails[i].className.replace(" active", "");
    }
    
    // Show current slide
    if (slides[slideIndex - 1]) {
        slides[slideIndex - 1].style.display = "block";
    }
    
    // Highlight current thumbnail
    if (thumbnails[slideIndex - 1]) {
        thumbnails[slideIndex - 1].className += " active";
    }
}
//lobal variable to store map instance

let map;

//Global variable to store marker instance
let marker;

//Metropolitan College coordinates
const campusCoordinates = [38.0519, 23.7889]; // Marousi, Athens

//Initialize the map when page loads
window.addEventListener('DOMContentLoaded', function() {
    // Check if map container exists on this page
    if (document.getElementById('map')) {
        initMap();
    }
});

//Initialize Leaflet map with campus location
function initMap() {
    // Create map centered on campus coordinates
    map = L.map('map').setView(campusCoordinates, 15);
    
    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Add marker for campus location
    marker = L.marker(campusCoordinates).addTo(map);

}

//Center map on campus location (called by "Κεντράρισμα Χάρτη" button)
function centerMap() {
    if (map) {
        // Animate map to center on campus with zoom level 17
        map.setView(campusCoordinates, 17, {
            animate: true,
            duration: 1
        });
        
        // Open popup on marker
        marker.openPopup();
    }
}
