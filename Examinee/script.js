

// Function to fetch courses from the server
async function fetchCourses() {
    try {
        const response = await fetch('/get_courses.php');
        console.log('Response:', response); // Log the full response object

        // Log the content type of the response
        const contentType = response.headers.get('Content-Type');
        console.log('Content-Type:', contentType);

        if (!response.ok) {
            throw new Error('Failed to fetch courses');
        }

        // Check if the content type is JSON
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            console.log('Data:', data);
            return data;
        } else {
            throw new Error('Response is not JSON');
        }
    } catch (error) {
        console.error('Error fetching courses:', error);
        throw new Error('Error fetching courses');
    }
}


// Function to create course cards
async function createCourseCards() {
    const container = document.getElementById('coursesContainer');
    if (!container) {
        console.error('Container not found!');
        return;
    }
    
    // Show loading state
    container.innerHTML = '<div class="loading">Loading courses...</div>';
    
    try {
        const courses = await fetchCourses();
        console.log('Received courses:', courses);
        
        if (!Array.isArray(courses) || courses.length === 0) {
            container.innerHTML = `
                <div class="no-courses">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-4">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <p>No courses available at the moment</p>
                </div>`;
            return;
        }
        
        container.innerHTML = ''; // Clear loading state
        
        // Create course cards
        courses.forEach(course => {
            const card = document.createElement('div');
            card.className = 'course-card';
            
            // Format the creation date
            const createdDate = new Date(course.created_at).toLocaleDateString();
            
            card.innerHTML = `
                <div class="course-header">
                    <h2 class="course-title">${course.name || 'Unnamed Course'}</h2>
                    <span class="enrollment-status ${course.enrolled ? 'enrolled' : 'not-enrolled'}">
                        ${course.enrolled ? 'Enrolled' : 'Not Enrolled'}
                    </span>
                </div>
                <div class="course-body">
                    <p class="course-description">${course.description || 'No description available'}</p>
                    <div class="course-meta">
                        <span>Created: ${createdDate}</span>
                        <span>Exams: ${course.exam_count || 0}</span>
                    </div>
                    <a href="course-details.html?id=${course.id}" class="view-course-btn">View Course Details</a>
                </div>
            `;
            
            // Add animation delay for staggered appearance
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            container.appendChild(card);
            
            // Trigger animation
            setTimeout(() => {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    } catch (error) {
        console.error('Error creating course cards:', error);
        container.innerHTML = `
            <div class="error">
                <p>Error loading courses. Please try again later.</p>
                <button onclick="createCourseCards()" class="view-course-btn mt-4">Retry</button>
            </div>`;
    }
}

// Initialize the page
document.addEventListener('DOMContentLoaded', createCourseCards);
