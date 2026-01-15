

CREATE DATABASE IF NOT EXISTS university_system;
USE university_system;

--  TABLE: ROLES 

CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL,
    registration_code VARCHAR(20) NOT NULL,
    INDEX idx_registration_code (registration_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  TABLE: USERS 

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id),
    INDEX idx_email (email),
    INDEX idx_role_id (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  TABLE: COURSES 

CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    description TEXT,
    professor_id INT NOT NULL,
    semester VARCHAR(20),
    academic_year VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professor_id) REFERENCES users(user_id),
    INDEX idx_professor_id (professor_id),
    INDEX idx_course_code (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  TABLE: ASSIGNMENTS 

CREATE TABLE assignments (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    INDEX idx_course_id (course_id),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  TABLE: SUBMISSIONS 

CREATE TABLE submissions (
    submission_id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    submission_text TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'submitted',
    FOREIGN KEY (assignment_id) REFERENCES assignments(assignment_id),
    FOREIGN KEY (student_id) REFERENCES users(user_id),
    INDEX idx_assignment_id (assignment_id),
    INDEX idx_student_id (student_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  TABLE: GRADES 

CREATE TABLE grades (
    grade_id INT PRIMARY KEY AUTO_INCREMENT,
    submission_id INT NOT NULL,
    grade DECIMAL(5,2),
    feedback TEXT,
    graded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    graded_by INT NOT NULL,
    FOREIGN KEY (submission_id) REFERENCES submissions(submission_id),
    FOREIGN KEY (graded_by) REFERENCES users(user_id),
    INDEX idx_submission_id (submission_id),
    INDEX idx_graded_by (graded_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  SAMPLE DATA 

-- Insert roles
INSERT INTO roles (role_name, registration_code) VALUES 
('Student', 'STUD2025'),
('Professor', 'PROF2025');

-- Insert test users (Password: test123 for all)
INSERT INTO users (username, email, password, role_id) VALUES 
('Γιώργος Παπαδόπουλος', 'student@test.gr', '$2y$10$2.boazve.PP1xRrgSK1CZOlzhWlpeQt59cbDtm3Md8kSSnwNOeUG.', 1),
('Μαρία Κωνσταντίνου', 'student2@test.gr', '$2y$10$2.boazve.PP1xRrgSK1CZOlzhWlpeQt59cbDtm3Md8kSSnwNOeUG.', 1),
('Δρ. Κατερίνα Ζώντου', 'prof@test.gr', '$2y$10$2.boazve.PP1xRrgSK1CZOlzhWlpeQt59cbDtm3Md8kSSnwNOeUG.', 2);

-- Insert courses
INSERT INTO courses (course_code, course_name, description, professor_id, semester, academic_year) VALUES 
('CN5005', 'Data Structures & Algorithms', 'Μελέτη δομών δεδομένων και αλγορίθμων με εφαρμογές σε Java', 3, 'Fall', '2024-2025'),
('CN5006', 'Web & Mobile App Development', 'Ανάπτυξη web και mobile εφαρμογών με PHP, HTML, CSS, JavaScript', 3, 'Fall', '2024-2025'),
('CN5007', 'Database Systems', 'Σχεδιασμός και διαχείριση βάσεων δεδομένων με MySQL/Oracle', 3, 'Fall', '2024-2025');

-- Insert assignments
INSERT INTO assignments (course_id, title, description, due_date) VALUES 
(1, 'Assignment 1: Binary Search Trees', 'Υλοποίηση Binary Search Tree με όλες τις βασικές λειτουργίες (insert, delete, search, traversal) σε Java.', '2025-12-31 23:59:59'),
(1, 'Assignment 2: AVL Trees', 'Υλοποίηση AVL Tree με αυτόματη εξισορρόπηση και ανάλυση πολυπλοκότητας.', '2026-01-15 23:59:59'),
(2, 'Assignment 1: University System', 'Δημιουργία συστήματος login, signup και dashboard για πανεπιστήμιο.', '2025-12-20 23:59:59'),
(2, 'Assignment 2: Complete System', 'Ολοκλήρωση του συστήματος με λειτουργίες για φοιτητές και καθηγητές.', '2026-01-16 23:59:59'),
(3, 'Assignment 1: Database Design', 'Σχεδιασμός βάσης δεδομένων για σύστημα βιβλιοθήκης με ERD και normalization.', '2025-12-25 23:59:59');

-- Insert submissions (students submitting assignments)
INSERT INTO submissions (assignment_id, student_id, submission_text, status, submitted_at) VALUES 
(1, 1, 'Ολοκληρώθηκε η υλοποίηση του Binary Search Tree με όλες τις λειτουργίες:\n- Insert: O(log n)\n- Delete: O(log n)\n- Search: O(log n)\n- Inorder Traversal\n- Preorder Traversal\n- Postorder Traversal\n\nΟ κώδικας είναι πλήρως σχολιασμένος και περιλαμβάνει JUnit tests.', 'submitted', '2025-12-20 15:30:00'),
(1, 2, 'Binary Search Tree implementation με recursion:\n- Δημιούργησα τη Node class\n- Υλοποίησα insert, delete, search methods\n- Πρόσθεσα traversal methods\n- Έφτιαξα test cases\n\nΌλα τα tests περνάνε επιτυχώς!', 'submitted', '2025-12-21 10:15:00'),
(3, 1, 'Υλοποίηση University System με PHP/MySQL:\n\nΧαρακτηριστικά Part 1:\n- Login/Signup με role-based access\n- Session management\n- Password hashing\n- Responsive UI με Bootstrap\n- Interactive map με Leaflet.js\n\nΗ εφαρμογή τρέχει χωρίς προβλήματα και έχει δοκιμαστεί σε πολλές συσκευές.', 'submitted', '2025-12-19 18:45:00'),
(5, 2, 'Database Design για Βιβλιοθήκη:\n\nΠίνακες:\n- Books (book_id, title, author, isbn, year)\n- Members (member_id, name, email, join_date)\n- Loans (loan_id, book_id, member_id, loan_date, return_date)\n- Categories (category_id, name)\n\nΈχω κάνει normalization μέχρι 3NF και έχω σχεδιάσει το ERD diagram.', 'submitted', '2025-12-24 12:00:00');

-- Insert grades (professor grading submissions)
INSERT INTO grades (submission_id, grade, feedback, graded_by, graded_at) VALUES 
(1, 85.00, 'Πολύ καλή δουλειά! Η υλοποίηση είναι σωστή και ο κώδικας καλά οργανωμένος. Τα σχόλια είναι επαρκή και τα tests comprehensive.\n\nΣημεία βελτίωσης:\n- Θα μπορούσες να προσθέσεις balance checking\n- Θα ήταν χρήσιμο ένα visualization method\n\nΣυνολικά άριστη εργασία!', 3, '2025-12-22 10:00:00'),
(2, 78.00, 'Καλή υλοποίηση με σωστή χρήση recursion. Ο κώδικας λειτουργεί σωστά.\n\nΘετικά:\n- Clean code structure\n- Good use of recursion\n- Tests working\n\nΠροτάσεις:\n- Προσθήκη error handling\n- Περισσότερα edge case tests\n- Documentation θα μπορούσε να είναι πιο λεπτομερής', 3, '2025-12-22 11:30:00'),
(3, 92.00, 'Εξαιρετική εργασία! Το σύστημα είναι πλήρως λειτουργικό και καλά σχεδιασμένο.\n\nΔυνατά σημεία:\n- Excellent UI/UX design\n- Proper security measures (password hashing, SQL injection prevention)\n- Clean and well-commented code\n- Responsive design works perfectly\n- Interactive map is a nice touch\n\nΜικρές παρατηρήσεις:\n- Θα μπορούσες να προσθέσεις input validation στο client-side\n- Error messages θα μπορούσαν να είναι πιο descriptive\n\nΣυνέχισε έτσι!', 3, '2025-12-20 09:15:00');

--  ADDITIONAL SAMPLE DATA 

-- More submissions for testing
INSERT INTO submissions (assignment_id, student_id, submission_text, status, submitted_at) VALUES 
(2, 1, 'AVL Tree implementation με automatic balancing:\n- Left rotation\n- Right rotation\n- Left-Right rotation\n- Right-Left rotation\n\nBalance factor checking και height calculation.\nΟ κώδικας περιλαμβάνει πλήρη documentation.', 'submitted', '2026-01-10 14:20:00');

--  END OF SCRIPT 

SELECT 'Database created successfully with Part 2 sample data!' AS Status;
