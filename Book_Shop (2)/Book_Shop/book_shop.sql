-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2025 at 04:56 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `book_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`author_id`, `first_name`, `last_name`, `created_at`) VALUES
(1, 'George', 'Orwell', '2025-09-12 14:31:49'),
(2, 'Harper', 'Lee', '2025-09-12 14:31:49'),
(3, 'F. Scott', 'Fitzgerald', '2025-09-12 14:31:49'),
(4, 'Jane', 'Austen', '2025-09-12 14:31:49'),
(5, 'J.D.', 'Salinger', '2025-09-12 14:31:49'),
(6, 'Herman', 'Melville', '2025-09-12 14:31:49'),
(7, 'J.R.R.', 'Tolkien', '2025-09-12 14:31:49'),
(8, 'J.K.', 'Rowling', '2025-09-12 14:31:49'),
(9, 'Dan', 'Brown', '2025-09-12 14:31:49'),
(10, 'Stieg', 'Larsson', '2025-09-12 14:31:49'),
(11, 'Gillian', 'Flynn', '2025-09-12 14:31:49'),
(12, 'Andy', 'Weir', '2025-09-12 14:31:49'),
(13, 'Frank', 'Herbert', '2025-09-12 14:31:49'),
(14, 'Isaac', 'Asimov', '2025-09-12 14:31:49'),
(15, 'William', 'Gibson', '2025-09-12 14:31:49'),
(16, 'Orson Scott', 'Card', '2025-09-12 14:31:49'),
(17, 'Aldous', 'Huxley', '2025-09-12 14:31:49'),
(18, 'Ray', 'Bradbury', '2025-09-12 14:31:49'),
(19, 'Stephen', 'King', '2025-09-12 14:31:49'),
(20, 'Shirley', 'Jackson', '2025-09-12 14:31:49'),
(21, 'Bram', 'Stoker', '2025-09-12 14:31:49'),
(22, 'Mary', 'Shelley', '2025-09-12 14:31:49'),
(23, 'Paulo', 'Coelho', '2025-09-12 14:31:49'),
(24, 'Yuval Noah', 'Harari', '2025-09-12 14:31:49'),
(25, 'Tara', 'Westover', '2025-09-12 14:31:49'),
(26, 'Mark', 'Manson', '2025-09-12 14:31:49'),
(27, 'James', 'Clear', '2025-09-12 14:31:49'),
(28, 'Charles', 'Duhigg', '2025-09-12 14:31:49'),
(29, 'Daniel', 'Kahneman', '2025-09-12 14:31:49'),
(30, 'Andrew', 'Hunt', '2025-09-12 14:31:49'),
(31, 'Robert C.', 'Martin', '2025-09-12 14:31:49'),
(32, 'Eric', 'Ries', '2025-09-12 14:31:49'),
(33, 'Don', 'Norman', '2025-09-12 14:31:49'),
(34, 'Patrick', 'Rothfuss', '2025-09-12 14:31:49'),
(35, 'Brandon', 'Sanderson', '2025-09-12 14:31:49'),
(36, 'John', 'Green', '2025-09-12 14:31:49'),
(37, 'Suzanne', 'Collins', '2025-09-12 14:31:49'),
(38, 'Markus', 'Zusak', '2025-09-12 14:31:49'),
(39, 'Jame', 'Smith', '2025-09-12 14:31:49'),
(41, 'Jame ', 'Smith', '2025-09-15 04:56:35');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author_id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL CHECK (`stock_quantity` >= 0),
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rating_manual` decimal(2,1) DEFAULT 0.0,
  `reviews_manual` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author_id`, `isbn`, `price`, `stock_quantity`, `description`, `image_url`, `created_at`, `rating_manual`, `reviews_manual`) VALUES
(1, '1984', 1, '9780451524935', 12.00, 25, 'Dystopian classic about surveillance and rebellion in a totalitarian state.', 'https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(2, 'Animal Farm', 1, '9780451526342', 9.00, 20, 'A barnyard allegory of revolution and power, exposing how ideals can be corrupted.', 'https://covers.openlibrary.org/b/isbn/9780451526342-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(3, 'To Kill a Mockingbird', 2, '9780061120084', 14.00, 20, 'A young girl confronts injustice and prejudice in the American South.', 'https://covers.openlibrary.org/b/isbn/9780061120084-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(4, 'The Great Gatsby', 3, '9780743273565', 12.00, 20, 'A Jazz Age tale of wealth, longing, and the American Dream gone sour.', 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(5, 'Pride and Prejudice', 4, '9781503290563', 11.00, 20, 'A witty romance where pride and first impressions give way to love.', 'https://covers.openlibrary.org/b/isbn/9781503290563-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(6, 'The Catcher in the Rye', 5, '9780316769488', 13.00, 20, 'Holden Caulfield wanders New York searching for honesty and meaning.', 'https://covers.openlibrary.org/b/isbn/9780316769488-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(8, 'The Hobbit', 7, '9780547928227', 14.00, 20, 'A hobbit is pulled into an adventure with dwarves, dragons, and treasure.', 'https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(9, 'The Fellowship of the Ring', 7, '9780547928210', 14.00, 20, 'The Fellowship begins a perilous journey to destroy the One Ring.', 'https://covers.openlibrary.org/b/isbn/9780547928210-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(10, 'The Two Towers', 7, '9780547928203', 14.00, 20, 'The quest continues as the Fellowship is tested and scattered.', 'https://covers.openlibrary.org/b/isbn/9780547928203-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(11, 'The Return of the King', 7, '9780547928197', 14.00, 20, 'The final stand against Sauron decides the fate of Middle-earth.', 'https://covers.openlibrary.org/b/isbn/9780547928197-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(12, 'Harry Potter and the Sorcerer\'s Stone', 8, '9780590353427', 18.00, 20, 'A boy discovers he is a wizard and enters a world of magic and danger.', 'https://covers.openlibrary.org/b/isbn/9780590353427-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(13, 'Harry Potter and the Chamber of Secrets', 8, '9780439064873', 18.00, 20, 'Mysterious forces stalk Hogwarts as a hidden chamber is opened.', 'https://covers.openlibrary.org/b/isbn/9780439064873-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(14, 'Harry Potter and the Prisoner of Azkaban', 8, '9780439136365', 18.00, 20, 'Time-turners and secrets from the past shape Harry’s third year.', 'https://covers.openlibrary.org/b/isbn/9780439136365-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(15, 'The Da Vinci Code', 9, '9780307474278', 13.00, 20, 'A symbologist races to solve a murder and unlock a Renaissance code.', 'https://covers.openlibrary.org/b/isbn/9780307474278-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(16, 'Angels & Demons', 9, '9780743493468', 12.00, 20, 'Ancient conspiracies and science collide beneath Rome’s landmarks.', 'https://covers.openlibrary.org/b/isbn/9780743493468-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(17, 'The Girl with the Dragon Tattoo', 10, '9780307454546', 14.00, 20, 'A journalist investigates a decades-old disappearance on a haunted island.', 'https://covers.openlibrary.org/b/isbn/9780307454546-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(18, 'Gone Girl', 11, '9780307588371', 14.00, 20, 'A missing wife, unreliable narrators, and media frenzy twist a marriage.', 'https://covers.openlibrary.org/b/isbn/9780307588371-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(19, 'The Martian', 12, '9780553418026', 13.00, 20, 'Stranded on Mars, an astronaut uses ingenuity to survive alone.', 'https://covers.openlibrary.org/b/isbn/9780553418026-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(20, 'Dune', 13, '9780441172719', 15.00, 20, 'Noble houses vie for power on a desert world where spice rules all.', 'https://covers.openlibrary.org/b/isbn/9780441172719-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(21, 'Foundation', 14, '9780553293357', 12.00, 20, 'A psychohistorian predicts the fall of empire and plants a plan to save it.', 'https://covers.openlibrary.org/b/isbn/9780553293357-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(22, 'Neuromancer', 15, '9780441569595', 15.00, 20, 'Cyberpunk noir where a washed-up hacker takes one last dangerous job.', 'https://covers.openlibrary.org/b/isbn/9780441569595-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(23, 'Ender\'s Game', 16, '9780812550702', 12.00, 20, 'A gifted child is trained to lead humanity in an alien war.', 'https://covers.openlibrary.org/b/isbn/9780812550702-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(24, 'Brave New World', 17, '9780060850524', 12.00, 20, 'A provocative dystopia exploring technology, conditioning, and happiness.', 'https://covers.openlibrary.org/b/isbn/9780060850524-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(25, 'Fahrenheit 451', 18, '9781451673319', 12.00, 20, 'Books are outlawed and a fireman begins to question the flames.', 'https://covers.openlibrary.org/b/isbn/9781451673319-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(26, 'The Shining', 19, '9780307743657', 13.00, 20, 'An isolated hotel awakens dark forces in a family caretaker.', 'https://covers.openlibrary.org/b/isbn/9780307743657-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(27, 'It', 19, '9781501142970', 16.00, 20, 'An epic of fear and friendship as evil returns to a small town.', 'https://covers.openlibrary.org/b/isbn/9781501142970-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(28, 'The Haunting of Hill House', 20, '9780143039983', 12.00, 20, 'A perfect haunted-house tale where dread seeps from every wall.', 'https://covers.openlibrary.org/b/isbn/9780143039983-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(29, 'Dracula', 21, '9780486411095', 10.00, 20, 'The original vampire saga of desire, superstition, and terror.', 'https://covers.openlibrary.org/b/isbn/9780486411095-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(30, 'Frankenstein', 22, '9780486282114', 10.00, 20, 'A scientist’s creation becomes a timeless meditation on humanity.', 'https://covers.openlibrary.org/b/isbn/9780486282114-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(31, 'The Alchemist', 23, '9780061122415', 12.00, 20, 'A shepherd seeks his Personal Legend across deserts and dreams.', 'https://covers.openlibrary.org/b/isbn/9780061122415-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(32, 'Sapiens', 24, '9780062316110', 18.00, 20, 'A bold history of humankind—how shared myths built societies.', 'https://covers.openlibrary.org/b/isbn/9780062316110-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(33, 'Educated', 25, '9780399590504', 16.00, 20, 'A memoir of survival and self-invention from an off-grid childhood.', 'https://covers.openlibrary.org/b/isbn/9780399590504-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(34, 'The Subtle Art of Not Giving a F*ck', 26, '9780062457714', 16.00, 20, 'Counterintuitive, punchy advice on values and living with purpose.', 'https://covers.openlibrary.org/b/isbn/9780062457714-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(35, 'Atomic Habits', 27, '9780735211292', 18.00, 20, 'Tiny changes, remarkable results: a practical system for habits.', 'https://covers.openlibrary.org/b/isbn/9780735211292-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(36, 'The Power of Habit', 28, '9780812981605', 16.00, 20, 'Why habits form and how to reshape them for better outcomes.', 'https://covers.openlibrary.org/b/isbn/9780812981605-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(37, 'Thinking, Fast and Slow', 29, '9780374533557', 18.00, 20, 'A landmark tour of human bias and the two systems of thinking.', 'https://covers.openlibrary.org/b/isbn/9780374533557-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(38, 'The Pragmatic Programmer', 30, '9780135957059', 28.00, 20, 'Timeless tips for becoming a better, pragmatic software developer.', 'https://covers.openlibrary.org/b/isbn/9780135957059-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(39, 'Clean Code', 31, '9780132350884', 28.00, 20, 'A guide to writing readable, maintainable code that lasts.', 'https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(40, 'The Lean Startup', 32, '9780307887894', 18.00, 20, 'Build-measure-learn: a framework for creating products customers love.', 'https://covers.openlibrary.org/b/isbn/9780307887894-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(41, 'The Design of Everyday Things', 33, '9780465050659', 18.00, 20, 'Design principles that make everyday things understandable and usable.', 'https://covers.openlibrary.org/b/isbn/9780465050659-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(42, 'The Name of the Wind', 34, '9780756404741', 16.00, 20, 'A gifted musician-mage begins a sweeping, lyrical fantasy tale.', 'https://covers.openlibrary.org/b/isbn/9780756404741-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(43, 'Mistborn: The Final Empire', 35, '9780765311788', 15.00, 20, 'Heists, ash-covered skies, and a crew challenging a dark lord.', 'https://covers.openlibrary.org/b/isbn/9780765311788-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(44, 'The Way of Kings', 35, '9780765365279', 20.00, 20, 'A sprawling epic where storms, spren, and shattered oaths collide.', 'https://covers.openlibrary.org/b/isbn/9780765365279-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(45, 'The Fault in Our Stars', 36, '9780525478812', 13.00, 20, 'A sharp, tender love story between two teens facing illness.', 'https://covers.openlibrary.org/b/isbn/9780525478812-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(46, 'The Hunger Games', 37, '9780439023481', 13.00, 20, 'A televised fight to the death sparks a rebellion in Panem.', 'https://covers.openlibrary.org/b/isbn/9780439023481-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(47, 'New Book Title', 37, '9780439023498', 13.00, 20, 'Victors return to a deadly arena as revolution brews.', 'https://covers.openlibrary.org/b/isbn/9780439023498-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(48, 'Mockingjay', 37, '9780439023511', 13.00, 20, 'War erupts and a symbol of hope leads the final uprising.', 'https://covers.openlibrary.org/b/isbn/9780439023511-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(49, 'The Book Thief', 38, '9780375842207', 14.00, 20, 'A girl steals books to survive the darkness of Nazi Germany.', 'https://covers.openlibrary.org/b/isbn/9780375842207-L.jpg', '2025-09-12 14:31:49', 0.0, 0),
(50, 'The Kite Runner', 39, '9781594631931', 14.00, 20, 'Friendship and betrayal shape lives from Kabul to California.', 'https://covers.openlibrary.org/b/isbn/9781594631931-L.jpg', '2025-09-12 14:31:49', 0.0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `book_categories`
--

CREATE TABLE `book_categories` (
  `book_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_categories`
--

INSERT INTO `book_categories` (`book_id`, `category_id`) VALUES
(1, 2),
(2, 1),
(3, 1),
(4, 1),
(5, 7),
(6, 1),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 9),
(13, 9),
(14, 9),
(15, 5),
(16, 5),
(17, 5),
(18, 5),
(19, 8),
(20, 8),
(21, 8),
(22, 8),
(23, 8),
(24, 2),
(25, 2),
(26, 4),
(27, 4),
(28, 4),
(29, 4),
(30, 4),
(31, 1),
(32, 6),
(33, 6),
(34, 6),
(35, 6),
(36, 6),
(37, 6),
(38, 6),
(39, 6),
(40, 6),
(41, 6),
(42, 3),
(43, 3),
(44, 3),
(45, 9),
(46, 2),
(47, 2),
(48, 2),
(49, 9),
(50, 1);

-- --------------------------------------------------------

--
-- Table structure for table `book_discounts`
--

CREATE TABLE `book_discounts` (
  `book_discount_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `discount_type` enum('percentage','fixed_amount') NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_discounts`
--

INSERT INTO `book_discounts` (`book_discount_id`, `book_id`, `discount_id`, `discount_type`, `value`, `start_date`, `end_date`, `is_active`, `created_at`) VALUES
(1, 50, NULL, 'percentage', 10.00, '2025-09-12', '2025-10-12', 1, '2025-09-12 14:40:54'),
(2, 23, NULL, 'percentage', 10.00, '0000-00-00', '0000-00-00', 0, '2025-09-12 14:40:54'),
(3, 33, NULL, 'percentage', 10.00, '2025-09-12', '2025-10-12', 1, '2025-09-12 14:40:54'),
(4, 4, NULL, 'percentage', 10.00, '2025-09-12', '2025-10-12', 1, '2025-09-12 14:40:54'),
(5, 16, NULL, 'percentage', 10.00, '2025-09-12', '2025-10-12', 1, '2025-09-12 14:40:54'),
(6, 5, NULL, 'percentage', 10.00, '2025-09-12', '2025-10-12', 1, '2025-09-12 14:40:54'),
(7, 44, NULL, 'percentage', 10.00, '2025-09-12', '2025-10-12', 1, '2025-09-12 14:40:54'),
(8, 19, NULL, 'percentage', 10.00, '2025-09-12', '2025-10-12', 1, '2025-09-12 14:40:54'),
(16, 21, NULL, 'percentage', 20.00, '2025-09-12', '2025-10-03', 1, '2025-09-12 14:40:54'),
(17, 34, NULL, 'percentage', 10.00, '0000-00-00', '0000-00-00', 0, '2025-09-12 14:40:54'),
(18, 37, NULL, 'percentage', 20.00, '2025-09-12', '2025-10-03', 1, '2025-09-12 14:40:54'),
(19, 26, NULL, 'percentage', 20.00, '2025-09-12', '2025-10-03', 1, '2025-09-12 14:40:54'),
(20, 32, NULL, 'percentage', 20.00, '2025-09-12', '2025-10-03', 1, '2025-09-12 14:40:54'),
(23, 1, NULL, 'percentage', 30.00, '2025-09-12', '2025-09-19', 1, '2025-09-12 14:40:54'),
(26, 49, NULL, 'fixed_amount', 3.00, '2025-09-12', '2025-09-26', 1, '2025-09-12 14:40:54'),
(27, 27, NULL, 'fixed_amount', 3.00, '2025-09-12', '2025-09-26', 1, '2025-09-12 14:40:54'),
(28, 47, NULL, 'fixed_amount', 3.00, '2025-09-19', '2025-09-26', 0, '2025-09-12 14:40:54'),
(29, 29, NULL, 'fixed_amount', 3.00, '2025-09-12', '2025-09-26', 1, '2025-09-12 14:40:54'),
(30, 47, NULL, 'percentage', 20.00, '2025-09-12', '2025-10-03', 1, '2025-09-12 14:40:54'),
(33, 30, NULL, 'fixed_amount', 5.00, '2025-09-12', '2025-09-19', 1, '2025-09-12 14:40:54'),
(34, 14, NULL, 'fixed_amount', 5.00, '2025-09-12', '2025-09-19', 1, '2025-09-12 14:40:54'),
(35, 47, NULL, 'percentage', 15.00, '2025-09-16', '2025-10-16', 0, '2025-09-15 06:50:07'),
(36, 50, NULL, 'percentage', 15.00, '0000-00-00', '0000-00-00', 1, '2025-09-15 13:42:19'),
(38, 2, NULL, 'percentage', 10.00, '2025-09-17', '2025-09-20', 1, '2025-09-17 16:33:48');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `user_id`, `book_id`, `quantity`, `added_at`) VALUES
(52, 74, 49, 1, '2025-09-17 07:40:06'),
(60, 8, 50, 1, '2025-09-18 13:53:01');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Classic'),
(2, 'Dystopian'),
(3, 'Fantasy'),
(4, 'Horror'),
(5, 'Mystery'),
(6, 'Non-fiction'),
(7, 'Romance'),
(8, 'Science Fiction'),
(9, 'Young Adult');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(2, 'Thant Phyo Maung', 'tpm@gmail.com', 'Book', 'Hello BookNest', '2025-09-12 16:06:04');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` int(11) NOT NULL,
  `discount_type` enum('percentage','fixed_amount') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `percent_off` decimal(6,2) DEFAULT NULL,
  `fixed_off` decimal(10,2) DEFAULT NULL,
  `free_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `applies_first_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `applies_automatically` tinyint(1) NOT NULL DEFAULT 0,
  `min_subtotal` decimal(10,2) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `discount_code` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`discount_id`, `discount_type`, `value`, `created_at`, `percent_off`, `fixed_off`, `free_shipping`, `applies_first_purchase`, `applies_automatically`, `min_subtotal`, `name`, `description`, `start_date`, `end_date`, `is_active`, `discount_code`) VALUES
(1, 'percentage', 10.00, '2025-09-12 16:14:58', 10.00, NULL, 1, 1, 1, NULL, 'Welcome Offer', '10% off + Free shipping for first-time customers.', '2025-09-12', '2026-09-12', 1, 'WELCOME10'),
(3, 'fixed_amount', 0.00, '2025-09-12 16:14:59', NULL, NULL, 1, 0, 0, NULL, 'Free Shipping Weekend', 'Enjoy free shipping on all orders this weekend.', '2025-09-19', '2025-09-21', 1, 'FREESHIP'),
(6, 'percentage', 10.00, '2025-09-17 16:18:33', NULL, NULL, 0, 0, 0, NULL, 'SET', 'Setember discount event', '2025-09-17', '2025-09-18', 1, 'Set20');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `shipping_method_id` int(11) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `city` varchar(120) NOT NULL,
  `street` varchar(200) NOT NULL,
  `postal_code` varchar(30) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'PENDING',
  `order_status` enum('pending','paid','delivered','canceled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `full_name`, `discount_id`, `order_date`, `shipping_method_id`, `phone`, `city`, `street`, `postal_code`, `subtotal`, `shipping_fee`, `total`, `payment_status`, `order_status`) VALUES
(1, 11, 'Yamin Akari', 1, '2025-09-12 16:30:47', 1, '09788699322', 'Yangon', 'Yangon', '', 52.56, 0.00, 52.56, 'PAID', 'pending'),
(2, 11, 'Yamin Akari', 1, '2025-09-12 18:06:32', 2, '09788699322', 'Yangon', 'Yangon', '', 14.00, 9.99, 23.99, 'PAID', 'pending'),
(3, 11, 'Yamin Akari', NULL, '2025-09-14 13:11:50', 1, '09788699322', 'Yangon', 'Yangon', '', 13.00, 4.99, 17.99, 'PAID', 'delivered'),
(4, 11, 'Yamin Akari', NULL, '2025-09-14 13:15:31', 1, '09788699322', 'Yangon', 'Yangon', '', 7.00, 4.99, 11.99, 'PAID', 'delivered'),
(5, 11, 'Yamin Akari', NULL, '2025-09-14 09:02:26', 1, '09788699322', 'Yangon', 'Yangon', '', 14.00, 4.99, 18.99, 'PAID', 'pending'),
(6, 11, 'Yamin Akari', NULL, '2025-09-14 09:08:24', 2, '09788699322', 'Yangon', 'Yangon', '', 14.00, 4.99, 18.99, 'PAID', 'pending'),
(7, 44, 'Amelia Nguyen', 1, '2025-09-14 16:44:34', 1, '09788010001', 'Mandalay', 'Main street', '11111', 17.00, 4.99, 21.99, 'UNPAID', 'pending'),
(8, 45, 'Liam Patel', NULL, '2025-09-14 15:44:34', 1, '09788010002', 'Naypyitaw', 'Main street', '11111', 14.00, 4.99, 18.99, 'PAID', 'pending'),
(9, 46, 'Noah Kim', NULL, '2025-09-14 14:44:34', 1, '09788010003', 'Yangon', 'Yangon', '11111', 16.00, 4.99, 20.99, 'PAID', 'pending'),
(10, 47, 'Ava Johnson', 3, '2025-09-14 13:44:34', 1, '09788010004', 'Yangon', 'Yangon', '11111', 18.00, 4.99, 22.99, 'PAID', 'pending'),
(11, 48, 'Oliver Garcia', NULL, '2025-09-14 12:44:34', 1, '09788010005', 'Yangon', 'Yangon', '11111', 20.00, 0.00, 20.00, 'PAID', 'pending'),
(12, 49, 'Sophia Rossi', NULL, '2025-09-14 11:44:34', 1, '09788010006', 'Yangon', 'Yangon', '11111', 22.00, 4.99, 26.99, 'PAID', 'pending'),
(13, 50, 'Elijah Chen', NULL, '2025-09-14 10:44:34', 1, '09788010007', 'Yangon', 'Yangon', '11111', 19.00, 4.99, 23.99, 'PAID', 'pending'),
(14, 51, 'Isabella Martin', NULL, '2025-09-14 09:44:34', 1, '09788010008', 'Yangon', 'Yangon', '11111', 21.00, 4.99, 25.99, 'PAID', 'pending'),
(15, 52, 'Lucas Ahmed', NULL, '2025-09-14 08:44:34', 1, '09788010009', 'Yangon', 'Yangon', '11111', 23.00, 4.99, 27.99, 'PAID', 'pending'),
(16, 53, 'Mia Santos', NULL, '2025-09-14 07:44:34', 1, '09788010010', 'Yangon', 'Yangon', '11111', 25.00, 0.00, 25.00, 'PAID', 'pending'),
(17, 54, 'Ethan Park', NULL, '2025-09-14 06:44:34', 1, '09788010011', 'Yangon', 'Yangon', '11111', 27.00, 4.99, 31.99, 'PAID', 'pending'),
(18, 55, 'Charlotte Wilson', NULL, '2025-09-14 05:44:34', 1, '09788010012', 'Yangon', 'Yangon', '11111', 24.00, 4.99, 28.99, 'PAID', 'pending'),
(19, 56, 'Henry Lopez', NULL, '2025-09-14 04:44:34', 1, '09788010013', 'Yangon', 'Yangon', '11111', 26.00, 4.99, 30.99, 'PAID', 'pending'),
(20, 57, 'Grace Lee', NULL, '2025-09-14 03:44:34', 1, '09788010014', 'Yangon', 'Yangon', '11111', 28.00, 4.99, 32.99, 'PAID', 'pending'),
(21, 58, 'Jack Brown', NULL, '2025-09-14 02:44:34', 1, '09788010015', 'Yangon', 'Yangon', '11111', 30.00, 0.00, 30.00, 'PAID', 'pending'),
(22, 59, 'Chloe Davis', NULL, '2025-09-14 01:44:34', 1, '09788010016', 'Yangon', 'Yangon', '11111', 32.00, 4.99, 36.99, 'PAID', 'pending'),
(23, 60, 'Benjamin Thomas', NULL, '2025-09-14 00:44:34', 1, '09788010017', 'Yangon', 'Yangon', '11111', 29.00, 4.99, 33.99, 'PAID', 'pending'),
(24, 61, 'Lily Walker', NULL, '2025-09-13 23:44:34', 1, '09788010018', 'Yangon', 'Yangon', '11111', 13.00, 4.99, 17.99, 'PAID', 'pending'),
(25, 62, 'Daniel Young', NULL, '2025-09-13 22:44:34', 1, '09788010019', 'Yangon', 'Yangon', '11111', 15.00, 4.99, 19.99, 'PAID', 'pending'),
(26, 63, 'Emma Hall', NULL, '2025-09-13 21:44:34', 1, '09788010020', 'Yangon', 'Yangon', '11111', 17.00, 0.00, 17.00, 'PAID', 'pending'),
(27, 64, 'Matthew Allen', NULL, '2025-09-13 20:44:34', 1, '09788010021', 'Yangon', 'Yangon', '11111', 19.00, 4.99, 23.99, 'PAID', 'pending'),
(28, 65, 'Zoe Clark', NULL, '2025-09-13 19:44:34', 1, '09788010022', 'Yangon', 'Yangon', '11111', 16.00, 4.99, 20.99, 'PAID', 'pending'),
(29, 66, 'Samuel Lewis', NULL, '2025-09-13 18:44:34', 1, '09788010023', 'Yangon', 'Yangon', '11111', 18.00, 4.99, 22.99, 'PAID', 'pending'),
(30, 67, 'Aria King', NULL, '2025-09-13 17:44:34', 1, '09788010024', 'Yangon', 'Yangon', '11111', 20.00, 4.99, 24.99, 'PAID', 'pending'),
(31, 68, 'David Wright', NULL, '2025-09-13 16:44:34', 1, '09788010025', 'Yangon', 'Yangon', '11111', 22.00, 0.00, 22.00, 'PAID', 'pending'),
(32, 69, 'Nora Scott', NULL, '2025-09-13 15:44:34', 1, '09788010026', 'Yangon', 'Yangon', '11111', 24.00, 4.99, 28.99, 'PAID', 'pending'),
(33, 70, 'Joseph Green', NULL, '2025-09-13 14:44:34', 1, '09788010027', 'Yangon', 'Yangon', '11111', 21.00, 4.99, 25.99, 'PAID', 'pending'),
(34, 71, 'Maya Turner', NULL, '2025-09-13 13:44:34', 1, '09788010028', 'Yangon', 'Yangon', '11111', 23.00, 4.99, 27.99, 'PAID', 'pending'),
(35, 72, 'Andrew Baker', NULL, '2025-09-13 12:44:34', 1, '09788010029', 'Yangon', 'Yangon', '11111', 25.00, 4.99, 29.99, 'PAID', 'pending'),
(36, 73, 'Ella Rivera', NULL, '2025-09-13 11:44:34', 1, '09788010030', 'Yangon', 'Yangon', '11111', 27.00, 0.00, 27.00, 'PAID', 'pending'),
(38, 10, 'Ko ko', 1, '2025-09-15 09:38:23', NULL, '09401511937', 'Yangon', 'Main Street', '', 21.24, 0.00, 21.24, 'PAID', 'paid'),
(39, 11, 'Yamin Akari', NULL, '2025-09-15 10:14:00', NULL, '09788699322', 'Yangon', 'Main Street', '', 13.00, 9.99, 22.99, 'PAID', 'paid'),
(40, 74, 'San Pwint', 1, '2025-09-15 10:28:53', NULL, '09402030402', 'Yangon', 'Main Street', '', 16.20, 0.00, 16.20, 'PAID', 'paid'),
(41, 10, 'Ko ko', NULL, '2025-09-18 00:07:21', NULL, '09788699322', 'Yangon', 'Main Street', '', 24.00, 4.99, 28.99, 'PAID', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `book_id`, `quantity`, `price_at_purchase`, `cost`) VALUES
(1, 1, 33, 1, 14.40, 14.40),
(2, 1, 42, 1, 16.00, 16.00),
(3, 1, 10, 2, 14.00, 28.00),
(4, 2, 29, 2, 7.00, 14.00),
(5, 3, 46, 1, 13.00, 13.00),
(6, 4, 29, 1, 7.00, 7.00),
(7, 5, 17, 1, 14.00, 14.00),
(8, 6, 17, 1, 14.00, 14.00),
(9, 38, 49, 1, 11.00, 11.00),
(10, 38, 50, 1, 12.60, 12.60),
(11, 39, 45, 1, 13.00, 13.00),
(12, 40, 44, 1, 18.00, 18.00),
(13, 41, 49, 1, 11.00, 11.00),
(14, 41, 45, 1, 13.00, 13.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token_hash`, `expires_at`, `created_at`, `used_at`) VALUES
(1, 10, 'f62ffe4b6fb1f669f73d366c745c9dbac4da0f589332d4e775f56dcda17c987a', '2025-09-15 19:43:34', '2025-09-15 23:13:34', '2025-09-15 23:13:51'),
(2, 10, '5e8575cdbb47a581d31c5254390a960f341ea69da5bd7c265347743ca0ae1b88', '2025-09-16 17:40:10', '2025-09-16 21:10:10', NULL),
(3, 10, 'e94caab02aab9d916b5b1b9788b843e8656d00cd2089ec09fa07ac4fd62ddec7', '2025-09-17 09:20:21', '2025-09-17 12:50:21', NULL),
(4, 10, '9b61b0bb6efb2ad8c990b1813a88d217dd108c94a69245a75ab8d2539948602e', '2025-09-18 07:10:16', '2025-09-18 10:40:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `amount`, `payment_method`, `transaction_id`, `payment_date`) VALUES
(1, 1, 52.56, 'COD', 'COD_68c44ab7a5d10', '2025-09-12 12:00:47'),
(2, 2, 23.99, 'KBZ Pay', 'KBZ_PAY_68c4612902dd9', '2025-09-12 13:36:33'),
(3, 3, 17.99, 'COD', 'COD_68c6bf163ee5a', '2025-09-14 08:41:50'),
(4, 4, 11.99, 'KBZ Pay', 'KBZ_PAY_68c6bff37100b', '2025-09-14 08:45:31'),
(5, 5, 18.99, 'PayPal', 'PAYPAL_68c6c3ea3d35f', '2025-09-14 09:02:26'),
(6, 6, 18.99, 'COD', 'COD_68c6c550c2e3b', '2025-09-14 09:08:24'),
(7, 38, 21.24, 'KBZ Pay', 'KBZ_PAY_68c81dd7e247e', '2025-09-15 09:38:23'),
(8, 39, 22.99, 'PayPal', 'PAYPAL_68c82630bfcc2', '2025-09-15 10:14:00'),
(9, 40, 16.20, 'COD', 'COD_68c829ada8962', '2025-09-15 10:28:53'),
(10, 41, 28.99, 'COD', 'COD_68cb8c8153189', '2025-09-18 00:07:21');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `book_id`, `rating`, `comment`, `created_at`) VALUES
(3, 22, 1, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-25 16:33:03'),
(5, 24, 1, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-09 16:33:03'),
(6, 25, 1, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(7, 26, 1, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-25 16:33:03'),
(8, 27, 1, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(9, 28, 1, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-09 16:33:03'),
(10, 29, 1, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(11, 30, 1, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-25 16:33:03'),
(12, 31, 1, 3, 'The setting is practically a character—so vivid and memorable.', '2025-08-31 16:33:03'),
(13, 13, 1, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(14, 32, 1, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(15, 33, 1, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-17 16:33:03'),
(16, 34, 1, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(18, 36, 1, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(19, 37, 1, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-17 16:33:03'),
(20, 38, 1, 3, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(21, 39, 1, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-31 16:33:03'),
(22, 40, 1, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(23, 41, 1, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-17 16:33:03'),
(24, 14, 1, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-25 16:33:03'),
(25, 42, 1, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(26, 15, 1, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(27, 16, 1, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-09 16:33:03'),
(28, 17, 1, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(29, 18, 1, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-25 16:33:03'),
(30, 19, 1, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(31, 20, 1, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-09 16:33:03'),
(32, 21, 1, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(33, 22, 2, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(34, 23, 2, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-09 16:33:03'),
(35, 24, 2, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(36, 25, 2, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-25 16:33:03'),
(37, 26, 2, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(38, 27, 2, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-08-09 16:33:03'),
(39, 28, 2, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(40, 29, 2, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-25 16:33:03'),
(41, 30, 2, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-31 16:33:03'),
(42, 31, 2, 4, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(43, 13, 2, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-25 16:33:03'),
(44, 32, 2, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-17 16:33:03'),
(45, 33, 2, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(46, 34, 2, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-31 16:33:03'),
(47, 35, 2, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(48, 36, 2, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-17 16:33:03'),
(49, 37, 2, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(50, 38, 2, 4, 'Action beats are cinematic. I could picture every scene.', '2025-08-31 16:33:03'),
(51, 39, 2, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(52, 40, 2, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-17 16:33:03'),
(53, 41, 2, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(54, 14, 2, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(55, 42, 2, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-31 16:33:03'),
(56, 15, 2, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-09 16:33:03'),
(57, 16, 2, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(58, 17, 2, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-25 16:33:03'),
(59, 18, 2, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(60, 19, 2, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-09 16:33:03'),
(61, 20, 2, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(62, 21, 2, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-25 16:33:03'),
(63, 22, 3, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-09 16:33:03'),
(64, 23, 3, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(65, 24, 3, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-25 16:33:03'),
(66, 25, 3, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-31 16:33:03'),
(67, 26, 3, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-09 16:33:03'),
(68, 27, 3, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-07-17 16:33:03'),
(69, 28, 3, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-25 16:33:03'),
(70, 29, 3, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-31 16:33:03'),
(71, 30, 3, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(72, 31, 3, 5, 'The setting is practically a character—so vivid and memorable.', '2025-07-17 16:33:03'),
(73, 13, 3, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(74, 32, 3, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(75, 33, 3, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-31 16:33:03'),
(76, 34, 3, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(77, 35, 3, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-17 16:33:03'),
(78, 36, 3, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(79, 37, 3, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-31 16:33:03'),
(80, 38, 3, 5, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(81, 39, 3, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-17 16:33:03'),
(82, 40, 3, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(83, 41, 3, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-31 16:33:03'),
(84, 14, 3, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-09 16:33:03'),
(85, 42, 3, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(86, 15, 3, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(87, 16, 3, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-25 16:33:03'),
(88, 17, 3, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(89, 18, 3, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-09 16:33:03'),
(90, 19, 3, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(91, 20, 3, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-25 16:33:03'),
(92, 21, 3, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(93, 22, 4, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(94, 23, 4, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-25 16:33:03'),
(95, 24, 4, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(96, 25, 4, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-09 16:33:03'),
(97, 26, 4, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(98, 27, 4, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-06-25 16:33:03'),
(99, 28, 4, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-31 16:33:03'),
(100, 29, 4, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(101, 30, 4, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-17 16:33:03'),
(102, 31, 4, 1, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(103, 13, 4, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-09 16:33:03'),
(104, 32, 4, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-31 16:33:03'),
(105, 33, 4, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(106, 34, 4, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-17 16:33:03'),
(107, 35, 4, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(108, 36, 4, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-31 16:33:03'),
(109, 37, 4, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(110, 38, 4, 1, 'Action beats are cinematic. I could picture every scene.', '2025-07-17 16:33:03'),
(111, 39, 4, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(112, 40, 4, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-31 16:33:03'),
(113, 41, 4, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(114, 14, 4, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(115, 42, 4, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-17 16:33:03'),
(116, 15, 4, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-25 16:33:03'),
(117, 16, 4, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(118, 17, 4, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-09 16:33:03'),
(119, 18, 4, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(120, 19, 4, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-25 16:33:03'),
(121, 20, 4, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(122, 21, 4, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-09 16:33:03'),
(123, 22, 5, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-25 16:33:03'),
(124, 23, 5, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(125, 24, 5, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-09 16:33:03'),
(126, 25, 5, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(127, 26, 5, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-25 16:33:03'),
(128, 27, 5, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(129, 28, 5, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(130, 29, 5, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(131, 30, 5, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(132, 31, 5, 2, 'The setting is practically a character—so vivid and memorable.', '2025-08-31 16:33:03'),
(133, 13, 5, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(134, 32, 5, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(135, 33, 5, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-17 16:33:03'),
(136, 34, 5, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(137, 35, 5, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-31 16:33:03'),
(138, 36, 5, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(139, 37, 5, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-17 16:33:03'),
(140, 38, 5, 2, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(141, 39, 5, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-31 16:33:03'),
(142, 40, 5, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(143, 41, 5, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-17 16:33:03'),
(144, 14, 5, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-25 16:33:03'),
(145, 42, 5, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(146, 15, 5, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(147, 16, 5, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-09 16:33:03'),
(148, 17, 5, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(149, 18, 5, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-25 16:33:03'),
(150, 19, 5, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(151, 20, 5, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-09 16:33:03'),
(152, 21, 5, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(153, 22, 6, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(154, 23, 6, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-09 16:33:03'),
(155, 24, 6, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(156, 25, 6, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-25 16:33:03'),
(157, 26, 6, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(158, 27, 6, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(159, 28, 6, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(160, 29, 6, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(161, 30, 6, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-31 16:33:03'),
(162, 31, 6, 3, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(163, 13, 6, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-25 16:33:03'),
(164, 32, 6, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-17 16:33:03'),
(165, 33, 6, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(166, 34, 6, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-31 16:33:03'),
(167, 35, 6, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(168, 36, 6, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-17 16:33:03'),
(169, 37, 6, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(170, 38, 6, 3, 'Action beats are cinematic. I could picture every scene.', '2025-08-31 16:33:03'),
(171, 39, 6, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(172, 40, 6, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-17 16:33:03'),
(173, 41, 6, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(174, 14, 6, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(175, 42, 6, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-31 16:33:03'),
(176, 15, 6, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-09 16:33:03'),
(177, 16, 6, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(178, 17, 6, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-25 16:33:03'),
(179, 18, 6, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(180, 19, 6, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-09 16:33:03'),
(181, 20, 6, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(182, 21, 6, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-25 16:33:03'),
(213, 22, 8, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(214, 23, 8, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-25 16:33:03'),
(215, 24, 8, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(216, 25, 8, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(217, 26, 8, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(218, 27, 8, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(219, 28, 8, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-31 16:33:03'),
(220, 29, 8, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(221, 30, 8, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-17 16:33:03'),
(222, 31, 8, 5, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(223, 13, 8, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-09 16:33:03'),
(224, 32, 8, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-31 16:33:03'),
(225, 33, 8, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(226, 34, 8, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-17 16:33:03'),
(227, 35, 8, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(228, 36, 8, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-31 16:33:03'),
(229, 37, 8, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(230, 38, 8, 5, 'Action beats are cinematic. I could picture every scene.', '2025-07-17 16:33:03'),
(231, 39, 8, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(232, 40, 8, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-31 16:33:03'),
(233, 41, 8, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(234, 14, 8, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(235, 42, 8, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-17 16:33:03'),
(236, 15, 8, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-25 16:33:03'),
(237, 16, 8, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(238, 17, 8, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-09 16:33:03'),
(239, 18, 8, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(240, 19, 8, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-25 16:33:03'),
(241, 20, 8, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(242, 21, 8, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-09 16:33:03'),
(243, 22, 9, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-25 16:33:03'),
(244, 23, 9, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(245, 24, 9, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(246, 25, 9, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(247, 26, 9, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(248, 27, 9, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(249, 28, 9, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(250, 29, 9, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(251, 30, 9, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(252, 31, 9, 1, 'The setting is practically a character—so vivid and memorable.', '2025-08-31 16:33:03'),
(253, 13, 9, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(254, 32, 9, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(255, 33, 9, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-17 16:33:03'),
(256, 34, 9, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(257, 35, 9, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-31 16:33:03'),
(258, 36, 9, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(259, 37, 9, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-17 16:33:03'),
(260, 38, 9, 1, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(261, 39, 9, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-31 16:33:03'),
(262, 40, 9, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(263, 41, 9, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-17 16:33:03'),
(264, 14, 9, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-25 16:33:03'),
(265, 42, 9, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(266, 15, 9, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(267, 16, 9, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-09 16:33:03'),
(268, 17, 9, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(269, 18, 9, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-25 16:33:03'),
(270, 19, 9, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(271, 20, 9, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-09 16:33:03'),
(272, 21, 9, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(273, 22, 10, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(274, 23, 10, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(275, 24, 10, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(276, 25, 10, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(277, 26, 10, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(278, 27, 10, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(279, 28, 10, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(280, 29, 10, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(281, 30, 10, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-31 16:33:03'),
(282, 31, 10, 2, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(283, 13, 10, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-25 16:33:03'),
(284, 32, 10, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-17 16:33:03'),
(285, 33, 10, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(286, 34, 10, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-31 16:33:03'),
(287, 35, 10, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(288, 36, 10, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-17 16:33:03'),
(289, 37, 10, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(290, 38, 10, 2, 'Action beats are cinematic. I could picture every scene.', '2025-08-31 16:33:03'),
(291, 39, 10, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(292, 40, 10, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-17 16:33:03'),
(293, 41, 10, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(294, 14, 10, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(295, 42, 10, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-31 16:33:03'),
(296, 15, 10, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-09 16:33:03'),
(297, 16, 10, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(298, 17, 10, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-25 16:33:03'),
(299, 18, 10, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(300, 19, 10, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-09 16:33:03'),
(301, 20, 10, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(302, 21, 10, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-25 16:33:03'),
(303, 22, 11, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(304, 23, 11, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(305, 24, 11, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(306, 25, 11, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-31 16:33:03'),
(307, 26, 11, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(308, 27, 11, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-07-17 16:33:03'),
(309, 28, 11, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(310, 29, 11, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-31 16:33:03'),
(311, 30, 11, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(312, 31, 11, 3, 'The setting is practically a character—so vivid and memorable.', '2025-07-17 16:33:03'),
(313, 13, 11, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(314, 32, 11, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(315, 33, 11, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-31 16:33:03'),
(316, 34, 11, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(317, 35, 11, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-17 16:33:03'),
(318, 36, 11, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(319, 37, 11, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-31 16:33:03'),
(320, 38, 11, 3, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(321, 39, 11, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-17 16:33:03'),
(322, 40, 11, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(323, 41, 11, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-31 16:33:03'),
(324, 14, 11, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-09 16:33:03'),
(325, 42, 11, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(326, 15, 11, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(327, 16, 11, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-25 16:33:03'),
(328, 17, 11, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(329, 18, 11, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-09 16:33:03'),
(330, 19, 11, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(331, 20, 11, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-25 16:33:03'),
(332, 21, 11, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(333, 22, 12, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(334, 23, 12, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(335, 24, 12, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(336, 25, 12, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(337, 26, 12, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(338, 27, 12, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(339, 28, 12, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-31 16:33:03'),
(340, 29, 12, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(341, 30, 12, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-17 16:33:03'),
(342, 31, 12, 4, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(343, 13, 12, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-09 16:33:03'),
(344, 32, 12, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-31 16:33:03'),
(345, 33, 12, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(346, 34, 12, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-17 16:33:03'),
(347, 35, 12, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(348, 36, 12, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-31 16:33:03'),
(349, 37, 12, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(350, 38, 12, 4, 'Action beats are cinematic. I could picture every scene.', '2025-07-17 16:33:03'),
(351, 39, 12, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(352, 40, 12, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-31 16:33:03'),
(353, 41, 12, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(354, 14, 12, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(355, 42, 12, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-17 16:33:03'),
(356, 15, 12, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-25 16:33:03'),
(357, 16, 12, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(358, 17, 12, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-09 16:33:03'),
(359, 18, 12, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(360, 19, 12, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-25 16:33:03'),
(361, 20, 12, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(362, 21, 12, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(363, 22, 13, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(364, 23, 13, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(365, 24, 13, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(366, 25, 13, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(367, 26, 13, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(368, 27, 13, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(369, 28, 13, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(370, 29, 13, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(371, 30, 13, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(372, 31, 13, 5, 'The setting is practically a character—so vivid and memorable.', '2025-08-31 16:33:03'),
(373, 13, 13, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(374, 32, 13, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(375, 33, 13, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-17 16:33:03'),
(376, 34, 13, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(377, 35, 13, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-31 16:33:03'),
(378, 36, 13, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(379, 37, 13, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-17 16:33:03'),
(380, 38, 13, 5, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(381, 39, 13, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-31 16:33:03'),
(382, 40, 13, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(383, 41, 13, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-17 16:33:03'),
(384, 14, 13, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-25 16:33:03'),
(385, 42, 13, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(386, 15, 13, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(387, 16, 13, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-09 16:33:03'),
(388, 17, 13, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(389, 18, 13, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-25 16:33:03'),
(390, 19, 13, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(391, 20, 13, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(392, 21, 13, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(393, 22, 14, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(394, 23, 14, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(395, 24, 14, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(396, 25, 14, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(397, 26, 14, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(398, 27, 14, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(399, 28, 14, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(400, 29, 14, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(401, 30, 14, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-31 16:33:03'),
(402, 31, 14, 1, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(403, 13, 14, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-25 16:33:03'),
(404, 32, 14, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-17 16:33:03'),
(405, 33, 14, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(406, 34, 14, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-31 16:33:03'),
(407, 35, 14, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(408, 36, 14, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-17 16:33:03'),
(409, 37, 14, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(410, 38, 14, 1, 'Action beats are cinematic. I could picture every scene.', '2025-08-31 16:33:03'),
(411, 39, 14, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(412, 40, 14, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-17 16:33:03'),
(413, 41, 14, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(414, 14, 14, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(415, 42, 14, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-31 16:33:03'),
(416, 15, 14, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-09 16:33:03'),
(417, 16, 14, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(418, 17, 14, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-25 16:33:03'),
(419, 18, 14, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(420, 19, 14, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(421, 20, 14, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(422, 21, 14, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(423, 22, 15, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(424, 23, 15, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(425, 24, 15, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(426, 25, 15, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-31 16:33:03'),
(427, 26, 15, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(428, 27, 15, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-07-17 16:33:03'),
(429, 28, 15, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(430, 29, 15, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-31 16:33:03'),
(431, 30, 15, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(432, 31, 15, 2, 'The setting is practically a character—so vivid and memorable.', '2025-07-17 16:33:03'),
(433, 13, 15, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(434, 32, 15, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(435, 33, 15, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-31 16:33:03'),
(436, 34, 15, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(437, 35, 15, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-17 16:33:03'),
(438, 36, 15, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(439, 37, 15, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-31 16:33:03'),
(440, 38, 15, 2, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(441, 39, 15, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-17 16:33:03'),
(442, 40, 15, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(443, 41, 15, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-31 16:33:03'),
(444, 14, 15, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-09 16:33:03'),
(445, 42, 15, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(446, 15, 15, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(447, 16, 15, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-25 16:33:03'),
(448, 17, 15, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(449, 18, 15, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(450, 19, 15, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(451, 20, 15, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(452, 21, 15, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(453, 22, 16, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(454, 23, 16, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(455, 24, 16, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(456, 25, 16, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(457, 26, 16, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(458, 27, 16, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(459, 28, 16, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-31 16:33:03'),
(460, 29, 16, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(461, 30, 16, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-17 16:33:03'),
(462, 31, 16, 3, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(463, 13, 16, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-09 16:33:03'),
(464, 32, 16, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-31 16:33:03'),
(465, 33, 16, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(466, 34, 16, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-17 16:33:03'),
(467, 35, 16, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(468, 36, 16, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-31 16:33:03'),
(469, 37, 16, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(470, 38, 16, 3, 'Action beats are cinematic. I could picture every scene.', '2025-07-17 16:33:03'),
(471, 39, 16, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(472, 40, 16, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-31 16:33:03'),
(473, 41, 16, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(474, 14, 16, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(475, 42, 16, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-17 16:33:03'),
(476, 15, 16, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-25 16:33:03'),
(477, 16, 16, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(478, 17, 16, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(479, 18, 16, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(480, 19, 16, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(481, 20, 16, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(482, 21, 16, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(483, 22, 17, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(484, 23, 17, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(485, 24, 17, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(486, 25, 17, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(487, 26, 17, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(488, 27, 17, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(489, 28, 17, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(490, 29, 17, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(491, 30, 17, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(492, 31, 17, 4, 'The setting is practically a character—so vivid and memorable.', '2025-08-31 16:33:03'),
(493, 13, 17, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(494, 32, 17, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(495, 33, 17, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-17 16:33:03'),
(496, 34, 17, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(497, 35, 17, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-31 16:33:03'),
(498, 36, 17, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(499, 37, 17, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-17 16:33:03'),
(500, 38, 17, 4, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(501, 39, 17, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-31 16:33:03'),
(502, 40, 17, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(503, 41, 17, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-17 16:33:03'),
(504, 14, 17, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-25 16:33:03'),
(505, 42, 17, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(506, 15, 17, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(507, 16, 17, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(508, 17, 17, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(509, 18, 17, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03'),
(510, 19, 17, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(511, 20, 17, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(512, 21, 17, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(513, 22, 18, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(514, 23, 18, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(515, 24, 18, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(516, 25, 18, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03');
INSERT INTO `reviews` (`review_id`, `user_id`, `book_id`, `rating`, `comment`, `created_at`) VALUES
(517, 26, 18, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(518, 27, 18, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(519, 28, 18, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(520, 29, 18, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(521, 30, 18, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-31 16:33:03'),
(522, 31, 18, 5, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(523, 13, 18, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-25 16:33:03'),
(524, 32, 18, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-17 16:33:03'),
(525, 33, 18, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(526, 34, 18, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-31 16:33:03'),
(527, 35, 18, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(528, 36, 18, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-17 16:33:03'),
(529, 37, 18, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(530, 38, 18, 5, 'Action beats are cinematic. I could picture every scene.', '2025-08-31 16:33:03'),
(531, 39, 18, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(532, 40, 18, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-17 16:33:03'),
(533, 41, 18, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(534, 14, 18, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(535, 42, 18, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-31 16:33:03'),
(536, 15, 18, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(537, 16, 18, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(538, 17, 18, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(539, 18, 18, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(540, 19, 18, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(541, 20, 18, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(542, 21, 18, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(543, 22, 19, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(544, 23, 19, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(545, 24, 19, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(546, 25, 19, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-31 16:33:03'),
(547, 26, 19, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(548, 27, 19, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-07-17 16:33:03'),
(549, 28, 19, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(550, 29, 19, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-31 16:33:03'),
(551, 30, 19, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(552, 31, 19, 1, 'The setting is practically a character—so vivid and memorable.', '2025-07-17 16:33:03'),
(553, 13, 19, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(554, 32, 19, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(555, 33, 19, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-31 16:33:03'),
(556, 34, 19, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(557, 35, 19, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-17 16:33:03'),
(558, 36, 19, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(559, 37, 19, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-31 16:33:03'),
(560, 38, 19, 1, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(561, 39, 19, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-17 16:33:03'),
(562, 40, 19, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(563, 41, 19, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-31 16:33:03'),
(564, 14, 19, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-08 16:33:03'),
(565, 42, 19, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(566, 15, 19, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(567, 16, 19, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-24 16:33:03'),
(568, 17, 19, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(569, 18, 19, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(570, 19, 19, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(571, 20, 19, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(572, 21, 19, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(573, 22, 20, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(574, 23, 20, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(575, 24, 20, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(576, 25, 20, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(577, 26, 20, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(578, 27, 20, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(579, 28, 20, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-31 16:33:03'),
(580, 29, 20, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(581, 30, 20, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-17 16:33:03'),
(582, 31, 20, 2, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(583, 13, 20, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-08 16:33:03'),
(584, 32, 20, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-31 16:33:03'),
(585, 33, 20, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(586, 34, 20, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-17 16:33:03'),
(587, 35, 20, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(588, 36, 20, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-31 16:33:03'),
(589, 37, 20, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(590, 38, 20, 2, 'Action beats are cinematic. I could picture every scene.', '2025-07-17 16:33:03'),
(591, 39, 20, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(592, 40, 20, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-31 16:33:03'),
(593, 41, 20, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(594, 14, 20, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(595, 42, 20, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-17 16:33:03'),
(596, 15, 20, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-24 16:33:03'),
(597, 16, 20, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(598, 17, 20, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(599, 18, 20, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(600, 19, 20, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(601, 20, 20, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(602, 21, 20, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(603, 22, 21, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(604, 23, 21, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(605, 24, 21, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(606, 25, 21, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(607, 26, 21, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(608, 27, 21, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(609, 28, 21, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(610, 29, 21, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(611, 30, 21, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(612, 31, 21, 3, 'The setting is practically a character—so vivid and memorable.', '2025-08-31 16:33:03'),
(613, 13, 21, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(614, 32, 21, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(615, 33, 21, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-17 16:33:03'),
(616, 34, 21, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(617, 35, 21, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-31 16:33:03'),
(618, 36, 21, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(619, 37, 21, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-17 16:33:03'),
(620, 38, 21, 3, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(621, 39, 21, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-31 16:33:03'),
(622, 40, 21, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(623, 41, 21, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-17 16:33:03'),
(624, 14, 21, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-24 16:33:03'),
(625, 42, 21, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(626, 15, 21, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(627, 16, 21, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(628, 17, 21, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(629, 18, 21, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03'),
(630, 19, 21, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(631, 20, 21, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(632, 21, 21, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(633, 22, 22, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(634, 23, 22, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(635, 24, 22, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(636, 25, 22, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(637, 26, 22, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(638, 27, 22, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(639, 28, 22, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(640, 29, 22, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(641, 30, 22, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-31 16:33:03'),
(642, 31, 22, 4, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(643, 13, 22, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-24 16:33:03'),
(644, 32, 22, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-17 16:33:03'),
(645, 33, 22, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(646, 34, 22, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-31 16:33:03'),
(647, 35, 22, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(648, 36, 22, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-17 16:33:03'),
(649, 37, 22, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(650, 38, 22, 4, 'Action beats are cinematic. I could picture every scene.', '2025-08-31 16:33:03'),
(651, 39, 22, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(652, 40, 22, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-17 16:33:03'),
(653, 41, 22, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(654, 14, 22, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(655, 42, 22, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-30 16:33:03'),
(656, 15, 22, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(657, 16, 22, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(658, 17, 22, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(659, 18, 22, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(660, 19, 22, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(661, 20, 22, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(662, 21, 22, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(663, 22, 23, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(664, 23, 23, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(665, 24, 23, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(666, 25, 23, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-31 16:33:03'),
(667, 26, 23, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(668, 27, 23, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-07-17 16:33:03'),
(669, 28, 23, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(670, 29, 23, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-31 16:33:03'),
(671, 30, 23, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(672, 31, 23, 5, 'The setting is practically a character—so vivid and memorable.', '2025-07-17 16:33:03'),
(673, 13, 23, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(674, 32, 23, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(675, 33, 23, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-31 16:33:03'),
(676, 34, 23, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(677, 35, 23, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-17 16:33:03'),
(678, 36, 23, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(679, 37, 23, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-31 16:33:03'),
(680, 38, 23, 5, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(681, 39, 23, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-17 16:33:03'),
(682, 40, 23, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(683, 41, 23, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-30 16:33:03'),
(684, 14, 23, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-08 16:33:03'),
(685, 42, 23, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(686, 15, 23, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(687, 16, 23, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-24 16:33:03'),
(688, 17, 23, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(689, 18, 23, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(690, 19, 23, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(691, 20, 23, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(692, 21, 23, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(693, 22, 24, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(694, 23, 24, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(695, 24, 24, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(696, 25, 24, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(697, 26, 24, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(698, 27, 24, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(699, 28, 24, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-31 16:33:03'),
(700, 29, 24, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(701, 30, 24, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-17 16:33:03'),
(702, 31, 24, 1, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(703, 13, 24, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-08 16:33:03'),
(704, 32, 24, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-31 16:33:03'),
(705, 33, 24, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(706, 34, 24, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-17 16:33:03'),
(707, 35, 24, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(708, 36, 24, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-31 16:33:03'),
(709, 37, 24, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(710, 38, 24, 1, 'Action beats are cinematic. I could picture every scene.', '2025-07-17 16:33:03'),
(711, 39, 24, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(712, 40, 24, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-30 16:33:03'),
(713, 41, 24, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(714, 14, 24, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(715, 42, 24, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-16 16:33:03'),
(716, 15, 24, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-24 16:33:03'),
(717, 16, 24, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(718, 17, 24, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(719, 18, 24, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(720, 19, 24, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(721, 20, 24, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(722, 21, 24, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(723, 22, 25, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(724, 23, 25, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(725, 24, 25, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(726, 25, 25, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(727, 26, 25, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(728, 27, 25, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(729, 28, 25, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(730, 29, 25, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(731, 30, 25, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(732, 31, 25, 2, 'The setting is practically a character—so vivid and memorable.', '2025-08-31 16:33:03'),
(733, 13, 25, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(734, 32, 25, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(735, 33, 25, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-17 16:33:03'),
(736, 34, 25, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(737, 35, 25, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-31 16:33:03'),
(738, 36, 25, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(739, 37, 25, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-17 16:33:03'),
(740, 38, 25, 2, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(741, 39, 25, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-30 16:33:03'),
(742, 40, 25, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(743, 41, 25, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-16 16:33:03'),
(744, 14, 25, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-24 16:33:03'),
(745, 42, 25, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(746, 15, 25, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(747, 16, 25, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(748, 17, 25, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(749, 18, 25, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03'),
(750, 19, 25, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(751, 20, 25, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(752, 21, 25, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(753, 22, 26, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(754, 23, 26, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(755, 24, 26, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(756, 25, 26, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(757, 26, 26, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(758, 27, 26, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(759, 28, 26, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(760, 29, 26, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(761, 30, 26, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-31 16:33:03'),
(762, 31, 26, 3, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(763, 13, 26, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-24 16:33:03'),
(764, 32, 26, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-17 16:33:03'),
(765, 33, 26, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(766, 34, 26, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-31 16:33:03'),
(767, 35, 26, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(768, 36, 26, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-17 16:33:03'),
(769, 37, 26, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(770, 38, 26, 3, 'Action beats are cinematic. I could picture every scene.', '2025-08-30 16:33:03'),
(771, 39, 26, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(772, 40, 26, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-16 16:33:03'),
(773, 41, 26, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(774, 14, 26, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(775, 42, 26, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-30 16:33:03'),
(776, 15, 26, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(777, 16, 26, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(778, 17, 26, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(779, 18, 26, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(780, 19, 26, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(781, 20, 26, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(782, 21, 26, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(783, 22, 27, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(784, 23, 27, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(785, 24, 27, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(786, 25, 27, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-31 16:33:03'),
(787, 26, 27, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(788, 27, 27, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-07-17 16:33:03'),
(789, 28, 27, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(790, 29, 27, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-31 16:33:03'),
(791, 30, 27, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(792, 31, 27, 4, 'The setting is practically a character—so vivid and memorable.', '2025-07-17 16:33:03'),
(793, 13, 27, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(794, 32, 27, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(795, 33, 27, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-31 16:33:03'),
(796, 34, 27, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(797, 35, 27, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-17 16:33:03'),
(798, 36, 27, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(799, 37, 27, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-30 16:33:03'),
(800, 38, 27, 4, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(801, 39, 27, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-16 16:33:03'),
(802, 40, 27, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(803, 41, 27, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-30 16:33:03'),
(804, 14, 27, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-08 16:33:03'),
(805, 42, 27, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(806, 15, 27, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(807, 16, 27, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-24 16:33:03'),
(808, 17, 27, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(809, 18, 27, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(810, 19, 27, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(811, 20, 27, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(812, 21, 27, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(813, 22, 28, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(814, 23, 28, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(815, 24, 28, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(816, 25, 28, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(817, 26, 28, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(818, 27, 28, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(819, 28, 28, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-31 16:33:03'),
(820, 29, 28, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(821, 30, 28, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-17 16:33:03'),
(822, 31, 28, 5, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(823, 13, 28, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-08 16:33:03'),
(824, 32, 28, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-31 16:33:03'),
(825, 33, 28, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(826, 34, 28, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-17 16:33:03'),
(827, 35, 28, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(828, 36, 28, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-30 16:33:03'),
(829, 37, 28, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(830, 38, 28, 5, 'Action beats are cinematic. I could picture every scene.', '2025-07-16 16:33:03'),
(831, 39, 28, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(832, 40, 28, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-30 16:33:03'),
(833, 41, 28, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(834, 14, 28, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(835, 42, 28, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-16 16:33:03'),
(836, 15, 28, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-24 16:33:03'),
(837, 16, 28, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(838, 17, 28, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(839, 18, 28, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(840, 19, 28, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(841, 20, 28, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(842, 21, 28, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(843, 22, 29, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(844, 23, 29, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(845, 24, 29, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(846, 25, 29, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(847, 26, 29, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(848, 27, 29, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(849, 28, 29, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(850, 29, 29, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(851, 30, 29, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(852, 31, 29, 1, 'The setting is practically a character—so vivid and memorable.', '2025-08-31 16:33:03'),
(853, 13, 29, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(854, 32, 29, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(855, 33, 29, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-17 16:33:03'),
(856, 34, 29, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(857, 35, 29, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-30 16:33:03'),
(858, 36, 29, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(859, 37, 29, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-16 16:33:03'),
(860, 38, 29, 1, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(861, 39, 29, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-30 16:33:03'),
(862, 40, 29, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(863, 41, 29, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-16 16:33:03'),
(864, 14, 29, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-24 16:33:03'),
(865, 42, 29, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(866, 15, 29, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(867, 16, 29, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(868, 17, 29, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(869, 18, 29, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03'),
(870, 19, 29, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(871, 20, 29, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(872, 21, 29, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(873, 22, 30, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(874, 23, 30, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(875, 24, 30, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(876, 25, 30, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(877, 26, 30, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(878, 27, 30, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(879, 28, 30, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(880, 29, 30, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(881, 30, 30, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-31 16:33:03'),
(882, 31, 30, 2, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(883, 13, 30, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-24 16:33:03'),
(884, 32, 30, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-17 16:33:03'),
(885, 33, 30, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(886, 34, 30, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-30 16:33:03'),
(887, 35, 30, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(888, 36, 30, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-16 16:33:03'),
(889, 37, 30, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(890, 38, 30, 2, 'Action beats are cinematic. I could picture every scene.', '2025-08-30 16:33:03'),
(891, 39, 30, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(892, 40, 30, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-16 16:33:03'),
(893, 41, 30, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(894, 14, 30, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(895, 42, 30, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-30 16:33:03'),
(896, 15, 30, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(897, 16, 30, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(898, 17, 30, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(899, 18, 30, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(900, 19, 30, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(901, 20, 30, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(902, 21, 30, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(903, 22, 31, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(904, 23, 31, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(905, 24, 31, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(906, 25, 31, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-31 16:33:03'),
(907, 26, 31, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(908, 27, 31, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-07-17 16:33:03'),
(909, 28, 31, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(910, 29, 31, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-31 16:33:03'),
(911, 30, 31, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(912, 31, 31, 3, 'The setting is practically a character—so vivid and memorable.', '2025-07-17 16:33:03'),
(913, 13, 31, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(914, 32, 31, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(915, 33, 31, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-30 16:33:03'),
(916, 34, 31, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(917, 35, 31, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-16 16:33:03'),
(918, 36, 31, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(919, 37, 31, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-30 16:33:03'),
(920, 38, 31, 3, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(921, 39, 31, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-16 16:33:03'),
(922, 40, 31, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(923, 41, 31, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-30 16:33:03'),
(924, 14, 31, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-08 16:33:03'),
(925, 42, 31, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(926, 15, 31, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(927, 16, 31, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-24 16:33:03'),
(928, 17, 31, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(929, 18, 31, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(930, 19, 31, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(931, 20, 31, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(932, 21, 31, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(933, 22, 32, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(934, 23, 32, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(935, 24, 32, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(936, 25, 32, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(937, 26, 32, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(938, 27, 32, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(939, 28, 32, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-31 16:33:03'),
(940, 29, 32, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(941, 30, 32, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-17 16:33:03'),
(942, 31, 32, 4, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(943, 13, 32, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-08 16:33:03'),
(944, 32, 32, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-30 16:33:03'),
(945, 33, 32, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(946, 34, 32, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-16 16:33:03'),
(947, 35, 32, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(948, 36, 32, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-30 16:33:03'),
(949, 37, 32, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(950, 38, 32, 4, 'Action beats are cinematic. I could picture every scene.', '2025-07-16 16:33:03'),
(951, 39, 32, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(952, 40, 32, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-30 16:33:03'),
(953, 41, 32, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(954, 14, 32, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(955, 42, 32, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-16 16:33:03'),
(956, 15, 32, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-24 16:33:03'),
(957, 16, 32, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(958, 17, 32, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(959, 18, 32, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(960, 19, 32, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(961, 20, 32, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(962, 21, 32, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(963, 22, 33, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(964, 23, 33, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(965, 24, 33, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(966, 25, 33, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(967, 26, 33, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(968, 27, 33, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-08-31 16:33:03'),
(969, 28, 33, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(970, 29, 33, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-17 16:33:03'),
(971, 30, 33, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(972, 31, 33, 5, 'The setting is practically a character—so vivid and memorable.', '2025-08-30 16:33:03'),
(973, 13, 33, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(974, 32, 33, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(975, 33, 33, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-16 16:33:03'),
(976, 34, 33, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(977, 35, 33, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-30 16:33:03'),
(978, 36, 33, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(979, 37, 33, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-16 16:33:03'),
(980, 38, 33, 5, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(981, 39, 33, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-30 16:33:03'),
(982, 40, 33, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(983, 41, 33, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-16 16:33:03'),
(984, 14, 33, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-24 16:33:03'),
(985, 42, 33, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(986, 15, 33, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(987, 16, 33, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(988, 17, 33, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(989, 18, 33, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03'),
(990, 19, 33, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(991, 20, 33, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(992, 21, 33, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(993, 22, 34, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(994, 23, 34, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(995, 24, 34, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03');
INSERT INTO `reviews` (`review_id`, `user_id`, `book_id`, `rating`, `comment`, `created_at`) VALUES
(996, 25, 34, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(997, 26, 34, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-31 16:33:03'),
(998, 27, 34, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(999, 28, 34, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-17 16:33:03'),
(1000, 29, 34, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(1001, 30, 34, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-30 16:33:03'),
(1002, 31, 34, 1, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(1003, 13, 34, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-24 16:33:03'),
(1004, 32, 34, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-16 16:33:03'),
(1005, 33, 34, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(1006, 34, 34, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-30 16:33:03'),
(1007, 35, 34, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(1008, 36, 34, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-16 16:33:03'),
(1009, 37, 34, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(1010, 38, 34, 1, 'Action beats are cinematic. I could picture every scene.', '2025-08-30 16:33:03'),
(1011, 39, 34, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(1012, 40, 34, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-16 16:33:03'),
(1013, 41, 34, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(1014, 14, 34, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(1015, 42, 34, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-30 16:33:03'),
(1016, 15, 34, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(1017, 16, 34, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(1018, 17, 34, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(1019, 18, 34, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(1020, 19, 34, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(1021, 20, 34, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(1022, 21, 34, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(1023, 22, 35, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(1024, 23, 35, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(1025, 24, 35, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(1026, 25, 35, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-31 16:33:03'),
(1027, 26, 35, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(1028, 27, 35, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-07-17 16:33:03'),
(1029, 28, 35, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(1030, 29, 35, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-30 16:33:03'),
(1031, 30, 35, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(1032, 31, 35, 2, 'The setting is practically a character—so vivid and memorable.', '2025-07-16 16:33:03'),
(1033, 13, 35, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(1034, 32, 35, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(1035, 33, 35, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-30 16:33:03'),
(1036, 34, 35, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(1037, 35, 35, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-16 16:33:03'),
(1038, 36, 35, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(1039, 37, 35, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-30 16:33:03'),
(1040, 38, 35, 2, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(1041, 39, 35, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-16 16:33:03'),
(1042, 40, 35, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(1043, 41, 35, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-30 16:33:03'),
(1044, 14, 35, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-08 16:33:03'),
(1045, 42, 35, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(1046, 15, 35, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(1047, 16, 35, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-24 16:33:03'),
(1048, 17, 35, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(1049, 18, 35, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(1050, 19, 35, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(1051, 20, 35, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(1052, 21, 35, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(1053, 22, 36, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(1054, 23, 36, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(1055, 24, 36, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-31 16:33:03'),
(1056, 25, 36, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(1057, 26, 36, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-17 16:33:03'),
(1058, 27, 36, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(1059, 28, 36, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-30 16:33:03'),
(1060, 29, 36, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(1061, 30, 36, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-16 16:33:03'),
(1062, 31, 36, 3, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(1063, 13, 36, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-08 16:33:03'),
(1064, 32, 36, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-30 16:33:03'),
(1065, 33, 36, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(1066, 34, 36, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-16 16:33:03'),
(1067, 35, 36, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(1068, 36, 36, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-30 16:33:03'),
(1069, 37, 36, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(1070, 38, 36, 3, 'Action beats are cinematic. I could picture every scene.', '2025-07-16 16:33:03'),
(1071, 39, 36, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(1072, 40, 36, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-30 16:33:03'),
(1073, 41, 36, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(1074, 14, 36, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(1075, 42, 36, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-16 16:33:03'),
(1076, 15, 36, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-24 16:33:03'),
(1077, 16, 36, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(1078, 17, 36, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(1079, 18, 36, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(1080, 19, 36, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(1081, 20, 36, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(1082, 21, 36, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(1083, 22, 37, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(1084, 23, 37, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-31 16:33:03'),
(1085, 24, 37, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(1086, 25, 37, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-17 16:33:03'),
(1087, 26, 37, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(1088, 27, 37, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-08-30 16:33:03'),
(1089, 28, 37, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(1090, 29, 37, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-16 16:33:03'),
(1091, 30, 37, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(1092, 31, 37, 4, 'The setting is practically a character—so vivid and memorable.', '2025-08-30 16:33:03'),
(1093, 13, 37, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(1094, 32, 37, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(1095, 33, 37, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-16 16:33:03'),
(1096, 34, 37, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(1097, 35, 37, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-30 16:33:03'),
(1098, 36, 37, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(1099, 37, 37, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-16 16:33:03'),
(1100, 38, 37, 4, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(1101, 39, 37, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-30 16:33:03'),
(1102, 40, 37, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(1103, 41, 37, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-16 16:33:03'),
(1104, 14, 37, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-24 16:33:03'),
(1105, 42, 37, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(1106, 15, 37, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(1107, 16, 37, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(1108, 17, 37, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(1109, 18, 37, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03'),
(1110, 19, 37, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(1111, 20, 37, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(1112, 21, 37, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(1113, 22, 38, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-31 16:33:03'),
(1114, 23, 38, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(1115, 24, 38, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-17 16:33:03'),
(1116, 25, 38, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(1117, 26, 38, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-30 16:33:03'),
(1118, 27, 38, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(1119, 28, 38, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-16 16:33:03'),
(1120, 29, 38, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(1121, 30, 38, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-30 16:33:03'),
(1122, 31, 38, 5, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(1123, 13, 38, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-24 16:33:03'),
(1124, 32, 38, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-16 16:33:03'),
(1125, 33, 38, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(1126, 34, 38, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-30 16:33:03'),
(1127, 35, 38, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(1128, 36, 38, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-16 16:33:03'),
(1129, 37, 38, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(1130, 38, 38, 5, 'Action beats are cinematic. I could picture every scene.', '2025-08-30 16:33:03'),
(1131, 39, 38, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(1132, 40, 38, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-16 16:33:03'),
(1133, 41, 38, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(1134, 14, 38, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(1135, 42, 38, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-30 16:33:03'),
(1136, 15, 38, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(1137, 16, 38, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(1138, 17, 38, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(1139, 18, 38, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(1140, 19, 38, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(1141, 20, 38, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(1142, 21, 38, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(1143, 22, 39, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(1144, 23, 39, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-17 16:33:03'),
(1145, 24, 39, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(1146, 25, 39, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-30 16:33:03'),
(1147, 26, 39, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(1148, 27, 39, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-07-16 16:33:03'),
(1149, 28, 39, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(1150, 29, 39, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-30 16:33:03'),
(1151, 30, 39, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(1152, 31, 39, 1, 'The setting is practically a character—so vivid and memorable.', '2025-07-16 16:33:03'),
(1153, 13, 39, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(1154, 32, 39, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(1155, 33, 39, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-30 16:33:03'),
(1156, 34, 39, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(1157, 35, 39, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-16 16:33:03'),
(1158, 36, 39, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(1159, 37, 39, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-30 16:33:03'),
(1160, 38, 39, 1, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(1161, 39, 39, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-16 16:33:03'),
(1162, 40, 39, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(1163, 41, 39, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-30 16:33:03'),
(1164, 14, 39, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-08 16:33:03'),
(1165, 42, 39, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(1166, 15, 39, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(1167, 16, 39, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-24 16:33:03'),
(1168, 17, 39, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(1169, 18, 39, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(1170, 19, 39, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(1171, 20, 39, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(1172, 21, 39, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-31 16:33:03'),
(1173, 22, 40, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-17 16:33:03'),
(1174, 23, 40, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(1175, 24, 40, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-30 16:33:03'),
(1176, 25, 40, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(1177, 26, 40, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-16 16:33:03'),
(1178, 27, 40, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(1179, 28, 40, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-30 16:33:03'),
(1180, 29, 40, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(1181, 30, 40, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-16 16:33:03'),
(1182, 31, 40, 2, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(1183, 13, 40, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-08 16:33:03'),
(1184, 32, 40, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-30 16:33:03'),
(1185, 33, 40, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(1186, 34, 40, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-16 16:33:03'),
(1187, 35, 40, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(1188, 36, 40, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-30 16:33:03'),
(1189, 37, 40, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(1190, 38, 40, 2, 'Action beats are cinematic. I could picture every scene.', '2025-07-16 16:33:03'),
(1191, 39, 40, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(1192, 40, 40, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-30 16:33:03'),
(1193, 41, 40, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(1194, 14, 40, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(1195, 42, 40, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-16 16:33:03'),
(1196, 15, 40, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-24 16:33:03'),
(1197, 16, 40, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(1198, 17, 40, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(1199, 18, 40, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(1200, 19, 40, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(1201, 20, 40, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-31 16:33:03'),
(1202, 21, 40, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(1203, 22, 41, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(1204, 23, 41, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-30 16:33:03'),
(1205, 24, 41, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(1206, 25, 41, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-16 16:33:03'),
(1207, 26, 41, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(1208, 27, 41, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-08-30 16:33:03'),
(1209, 28, 41, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(1210, 29, 41, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-16 16:33:03'),
(1211, 30, 41, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(1212, 31, 41, 3, 'The setting is practically a character—so vivid and memorable.', '2025-08-30 16:33:03'),
(1213, 13, 41, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(1214, 32, 41, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(1215, 33, 41, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-16 16:33:03'),
(1216, 34, 41, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(1217, 35, 41, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-30 16:33:03'),
(1218, 36, 41, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(1219, 37, 41, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-16 16:33:03'),
(1220, 38, 41, 3, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(1221, 39, 41, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-30 16:33:03'),
(1222, 40, 41, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(1223, 41, 41, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-16 16:33:03'),
(1224, 14, 41, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-24 16:33:03'),
(1225, 42, 41, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(1226, 15, 41, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(1227, 16, 41, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(1228, 17, 41, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(1229, 18, 41, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03'),
(1230, 19, 41, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-31 16:33:03'),
(1231, 20, 41, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(1232, 21, 41, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-17 16:33:03'),
(1233, 22, 42, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-30 16:33:03'),
(1234, 23, 42, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(1235, 24, 42, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-16 16:33:03'),
(1236, 25, 42, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(1237, 26, 42, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-30 16:33:03'),
(1238, 27, 42, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(1239, 28, 42, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-16 16:33:03'),
(1240, 29, 42, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(1241, 30, 42, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-30 16:33:03'),
(1242, 31, 42, 4, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(1243, 13, 42, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-24 16:33:03'),
(1244, 32, 42, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-16 16:33:03'),
(1245, 33, 42, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(1246, 34, 42, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-30 16:33:03'),
(1247, 35, 42, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(1248, 36, 42, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-16 16:33:03'),
(1249, 37, 42, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(1250, 38, 42, 4, 'Action beats are cinematic. I could picture every scene.', '2025-08-30 16:33:03'),
(1251, 39, 42, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(1252, 40, 42, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-16 16:33:03'),
(1253, 41, 42, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(1254, 14, 42, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(1255, 42, 42, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-30 16:33:03'),
(1256, 15, 42, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(1257, 16, 42, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(1258, 17, 42, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(1259, 18, 42, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-31 16:33:03'),
(1260, 19, 42, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(1261, 20, 42, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-17 16:33:03'),
(1262, 21, 42, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(1263, 22, 43, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(1264, 23, 43, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-16 16:33:03'),
(1265, 24, 43, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(1266, 25, 43, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-30 16:33:03'),
(1267, 26, 43, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(1268, 27, 43, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-07-16 16:33:03'),
(1269, 28, 43, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(1270, 29, 43, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-30 16:33:03'),
(1271, 30, 43, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(1272, 31, 43, 5, 'The setting is practically a character—so vivid and memorable.', '2025-07-16 16:33:03'),
(1273, 13, 43, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(1274, 32, 43, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(1275, 33, 43, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-30 16:33:03'),
(1276, 34, 43, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(1277, 35, 43, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-16 16:33:03'),
(1278, 36, 43, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(1279, 37, 43, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-30 16:33:03'),
(1280, 38, 43, 5, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(1281, 39, 43, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-16 16:33:03'),
(1282, 40, 43, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(1283, 41, 43, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-30 16:33:03'),
(1284, 14, 43, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-08 16:33:03'),
(1285, 42, 43, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(1286, 15, 43, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(1287, 16, 43, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-24 16:33:03'),
(1288, 17, 43, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-31 16:33:03'),
(1289, 18, 43, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(1290, 19, 43, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-17 16:33:03'),
(1291, 20, 43, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(1292, 21, 43, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-30 16:33:03'),
(1293, 22, 44, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-16 16:33:03'),
(1294, 23, 44, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(1295, 24, 44, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-30 16:33:03'),
(1296, 25, 44, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(1297, 26, 44, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-16 16:33:03'),
(1298, 27, 44, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(1299, 28, 44, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-30 16:33:03'),
(1300, 29, 44, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(1301, 30, 44, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-16 16:33:03'),
(1302, 31, 44, 1, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(1303, 13, 44, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-08 16:33:03'),
(1304, 32, 44, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-30 16:33:03'),
(1305, 33, 44, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(1306, 34, 44, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-16 16:33:03'),
(1307, 35, 44, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(1308, 36, 44, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-30 16:33:03'),
(1309, 37, 44, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(1310, 38, 44, 1, 'Action beats are cinematic. I could picture every scene.', '2025-07-16 16:33:03'),
(1311, 39, 44, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(1312, 40, 44, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-30 16:33:03'),
(1313, 41, 44, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(1314, 14, 44, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(1315, 42, 44, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-16 16:33:03'),
(1316, 15, 44, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-24 16:33:03'),
(1317, 16, 44, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-31 16:33:03'),
(1318, 17, 44, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(1319, 18, 44, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-17 16:33:03'),
(1320, 19, 44, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(1321, 20, 44, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-30 16:33:03'),
(1322, 21, 44, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(1323, 22, 45, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(1324, 23, 45, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-30 16:33:03'),
(1325, 24, 45, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(1326, 25, 45, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-16 16:33:03'),
(1327, 26, 45, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(1328, 27, 45, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-08-30 16:33:03'),
(1329, 28, 45, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(1330, 29, 45, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-16 16:33:03'),
(1331, 30, 45, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(1332, 31, 45, 2, 'The setting is practically a character—so vivid and memorable.', '2025-08-30 16:33:03'),
(1333, 13, 45, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(1334, 32, 45, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(1335, 33, 45, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-16 16:33:03'),
(1336, 34, 45, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(1337, 35, 45, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-30 16:33:03'),
(1338, 36, 45, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(1339, 37, 45, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-16 16:33:03'),
(1340, 38, 45, 2, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(1341, 39, 45, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-30 16:33:03'),
(1342, 40, 45, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(1343, 41, 45, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-16 16:33:03'),
(1344, 14, 45, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-24 16:33:03'),
(1345, 42, 45, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(1346, 15, 45, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-31 16:33:03'),
(1347, 16, 45, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(1348, 17, 45, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-17 16:33:03'),
(1349, 18, 45, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03'),
(1350, 19, 45, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-30 16:33:03'),
(1351, 20, 45, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(1352, 21, 45, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-16 16:33:03'),
(1353, 22, 46, 4, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-30 16:33:03'),
(1354, 23, 46, 1, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(1355, 24, 46, 4, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-16 16:33:03'),
(1356, 25, 46, 1, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(1357, 26, 46, 3, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-30 16:33:03'),
(1358, 27, 46, 5, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(1359, 28, 46, 2, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-16 16:33:03'),
(1360, 29, 46, 4, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(1361, 30, 46, 1, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-30 16:33:03'),
(1362, 31, 46, 3, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(1363, 13, 46, 5, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-24 16:33:03'),
(1364, 32, 46, 1, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-16 16:33:03'),
(1365, 33, 46, 3, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(1366, 34, 46, 5, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-30 16:33:03'),
(1367, 35, 46, 2, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(1368, 36, 46, 4, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-16 16:33:03'),
(1369, 37, 46, 1, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(1370, 38, 46, 3, 'Action beats are cinematic. I could picture every scene.', '2025-08-30 16:33:03'),
(1371, 39, 46, 1, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(1372, 40, 46, 3, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-16 16:33:03'),
(1373, 41, 46, 5, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(1374, 14, 46, 2, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-31 16:33:03'),
(1375, 42, 46, 2, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-30 16:33:03'),
(1376, 15, 46, 4, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(1377, 16, 46, 1, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-17 16:33:03'),
(1378, 17, 46, 4, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(1379, 18, 46, 1, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-30 16:33:03'),
(1380, 19, 46, 3, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(1381, 20, 46, 5, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-16 16:33:03'),
(1382, 21, 46, 2, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03'),
(1383, 22, 47, 5, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-08 16:33:03'),
(1384, 23, 47, 2, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-07-16 16:33:03'),
(1385, 24, 47, 5, 'Great momentum and set pieces. I finished it in two sittings.', '2025-06-24 16:33:03'),
(1386, 25, 47, 2, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-30 16:33:03'),
(1387, 26, 47, 4, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-08 16:33:03'),
(1388, 27, 47, 1, 'A character-driven take on the genre. Quietly powerful.', '2025-07-16 16:33:03'),
(1389, 28, 47, 3, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-06-24 16:33:03'),
(1390, 29, 47, 5, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-30 16:33:03'),
(1391, 30, 47, 2, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-08 16:33:03'),
(1392, 31, 47, 4, 'The setting is practically a character—so vivid and memorable.', '2025-07-16 16:33:03'),
(1393, 13, 47, 1, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-31 16:33:03'),
(1394, 32, 47, 2, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-06-24 16:33:03'),
(1395, 33, 47, 4, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-30 16:33:03'),
(1396, 34, 47, 1, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-08 16:33:03'),
(1397, 35, 47, 3, 'Some tropes, but used smartly. Would recommend to friends.', '2025-07-16 16:33:03'),
(1398, 36, 47, 5, 'Gripping from page one. I stayed up way too late finishing.', '2025-06-24 16:33:03'),
(1399, 37, 47, 2, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-30 16:33:03'),
(1400, 38, 47, 4, 'Action beats are cinematic. I could picture every scene.', '2025-08-08 16:33:03'),
(1401, 39, 47, 2, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-07-16 16:33:03'),
(1402, 40, 47, 4, 'Clever premise, executed with heart. I smiled a lot.', '2025-06-24 16:33:03'),
(1403, 41, 47, 1, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-30 16:33:03'),
(1404, 14, 47, 3, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-08 16:33:03'),
(1405, 42, 47, 3, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-08 16:33:03'),
(1406, 15, 47, 5, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-07-17 16:33:03'),
(1407, 16, 47, 2, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-06-24 16:33:03'),
(1408, 17, 47, 5, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-30 16:33:03'),
(1409, 18, 47, 2, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-08 16:33:03'),
(1410, 19, 47, 4, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-07-16 16:33:03'),
(1411, 20, 47, 1, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-06-24 16:33:03'),
(1412, 21, 47, 3, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-30 16:33:03'),
(1413, 22, 48, 1, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-07-16 16:33:03'),
(1414, 23, 48, 3, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-06-24 16:33:03'),
(1415, 24, 48, 1, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-30 16:33:03'),
(1416, 25, 48, 3, 'Leans more on mood than jump scares—I loved that approach.', '2025-08-08 16:33:03'),
(1417, 26, 48, 5, 'Editing could be tighter, but the last 100 pages flew by.', '2025-07-16 16:33:03'),
(1418, 27, 48, 2, 'A character-driven take on the genre. Quietly powerful.', '2025-06-24 16:33:03'),
(1419, 28, 48, 4, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-30 16:33:03'),
(1420, 29, 48, 1, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-08-08 16:33:03'),
(1421, 30, 48, 3, 'Heavy themes handled with care. I appreciated the nuance.', '2025-07-16 16:33:03'),
(1422, 31, 48, 5, 'The setting is practically a character—so vivid and memorable.', '2025-06-24 16:33:03'),
(1423, 13, 48, 2, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-08-08 16:33:03'),
(1424, 32, 48, 3, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-30 16:33:03'),
(1425, 33, 48, 5, 'Quiet dread that builds scene by scene. The craft shows.', '2025-08-08 16:33:03'),
(1426, 34, 48, 2, 'Reads fast, clever structure, and a neat final reveal.', '2025-07-16 16:33:03'),
(1427, 35, 48, 4, 'Some tropes, but used smartly. Would recommend to friends.', '2025-06-24 16:33:03'),
(1428, 36, 48, 1, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-30 16:33:03'),
(1429, 37, 48, 3, 'Warm, human, and eerie—an unexpected combo that works.', '2025-08-08 16:33:03'),
(1430, 38, 48, 5, 'Action beats are cinematic. I could picture every scene.', '2025-07-16 16:33:03'),
(1431, 39, 48, 3, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-06-24 16:33:03'),
(1432, 40, 48, 5, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-30 16:33:03'),
(1433, 41, 48, 2, 'The twist wasn’t a shock, but the journey was excellent.', '2025-08-08 16:33:03'),
(1434, 14, 48, 4, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-07-17 16:33:03'),
(1435, 42, 48, 4, 'Short, sharp chapters kept me turning pages all weekend.', '2025-07-16 16:33:03'),
(1436, 15, 48, 1, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-06-24 16:33:03'),
(1437, 16, 48, 3, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-30 16:33:03'),
(1438, 17, 48, 1, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-08-08 16:33:03'),
(1439, 18, 48, 3, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-07-16 16:33:03'),
(1440, 19, 48, 5, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-06-24 16:33:03'),
(1441, 20, 48, 2, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-30 16:33:03'),
(1442, 21, 48, 4, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-08-08 16:33:03'),
(1443, 22, 49, 2, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-06-24 16:33:03'),
(1444, 23, 49, 4, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-30 16:33:03'),
(1445, 24, 49, 2, 'Great momentum and set pieces. I finished it in two sittings.', '2025-08-08 16:33:03'),
(1446, 25, 49, 4, 'Leans more on mood than jump scares—I loved that approach.', '2025-07-16 16:33:03'),
(1447, 26, 49, 1, 'Editing could be tighter, but the last 100 pages flew by.', '2025-06-24 16:33:03'),
(1448, 27, 49, 3, 'A character-driven take on the genre. Quietly powerful.', '2025-08-30 16:33:03'),
(1449, 28, 49, 5, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-08-08 16:33:03'),
(1450, 29, 49, 2, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-07-16 16:33:03'),
(1451, 30, 49, 4, 'Heavy themes handled with care. I appreciated the nuance.', '2025-06-24 16:33:03'),
(1452, 31, 49, 1, 'The setting is practically a character—so vivid and memorable.', '2025-08-30 16:33:03'),
(1453, 13, 49, 3, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-07-17 16:33:03'),
(1454, 32, 49, 4, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-08-08 16:33:03'),
(1455, 33, 49, 1, 'Quiet dread that builds scene by scene. The craft shows.', '2025-07-16 16:33:03'),
(1456, 34, 49, 3, 'Reads fast, clever structure, and a neat final reveal.', '2025-06-24 16:33:03'),
(1457, 35, 49, 5, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-30 16:33:03'),
(1458, 36, 49, 2, 'Gripping from page one. I stayed up way too late finishing.', '2025-08-08 16:33:03'),
(1459, 37, 49, 4, 'Warm, human, and eerie—an unexpected combo that works.', '2025-07-16 16:33:03'),
(1460, 38, 49, 1, 'Action beats are cinematic. I could picture every scene.', '2025-06-24 16:33:03'),
(1461, 39, 49, 4, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-30 16:33:03'),
(1462, 40, 49, 1, 'Clever premise, executed with heart. I smiled a lot.', '2025-08-08 16:33:03'),
(1463, 41, 49, 3, 'The twist wasn’t a shock, but the journey was excellent.', '2025-07-16 16:33:03'),
(1464, 14, 49, 5, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-06-24 16:33:03'),
(1465, 42, 49, 5, 'Short, sharp chapters kept me turning pages all weekend.', '2025-06-24 16:33:03'),
(1466, 15, 49, 2, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-30 16:33:03'),
(1467, 16, 49, 4, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-08-08 16:33:03'),
(1468, 17, 49, 2, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-07-16 16:33:03'),
(1469, 18, 49, 4, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-06-24 16:33:03');
INSERT INTO `reviews` (`review_id`, `user_id`, `book_id`, `rating`, `comment`, `created_at`) VALUES
(1470, 19, 49, 1, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-30 16:33:03'),
(1471, 20, 49, 3, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-08-08 16:33:03'),
(1472, 21, 49, 5, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-07-16 16:33:03'),
(1473, 22, 50, 3, 'A thoughtful look at fear and faith. I keep thinking about it.', '2025-08-30 16:33:03'),
(1474, 23, 50, 5, 'Creepy, cozy, and oddly tender. Perfect rainy-day read.', '2025-08-08 16:33:03'),
(1475, 24, 50, 3, 'Great momentum and set pieces. I finished it in two sittings.', '2025-07-16 16:33:03'),
(1476, 25, 50, 5, 'Leans more on mood than jump scares—I loved that approach.', '2025-06-24 16:33:03'),
(1477, 26, 50, 2, 'Editing could be tighter, but the last 100 pages flew by.', '2025-08-30 16:33:03'),
(1478, 27, 50, 4, 'A character-driven take on the genre. Quietly powerful.', '2025-08-08 16:33:03'),
(1479, 28, 50, 1, 'Charming side cast and sharp humor. Didn’t expect to laugh this much.', '2025-07-16 16:33:03'),
(1480, 29, 50, 3, 'Gentle start, then it sinks the hooks in. Very satisfying arc.', '2025-06-24 16:33:03'),
(1481, 30, 50, 5, 'Heavy themes handled with care. I appreciated the nuance.', '2025-08-30 16:33:03'),
(1482, 31, 50, 2, 'The setting is practically a character—so vivid and memorable.', '2025-08-08 16:33:03'),
(1483, 13, 50, 4, 'A brisk, spooky read with classic vibes—surprisingly funny in places.', '2025-06-24 16:33:03'),
(1484, 32, 50, 5, 'Not perfect, but I had a great time. Exactly the vibe I wanted.', '2025-07-16 16:33:03'),
(1485, 33, 50, 2, 'Quiet dread that builds scene by scene. The craft shows.', '2025-06-24 16:33:03'),
(1486, 34, 50, 4, 'Reads fast, clever structure, and a neat final reveal.', '2025-08-30 16:33:03'),
(1487, 35, 50, 1, 'Some tropes, but used smartly. Would recommend to friends.', '2025-08-08 16:33:03'),
(1488, 36, 50, 3, 'Gripping from page one. I stayed up way too late finishing.', '2025-07-16 16:33:03'),
(1489, 37, 50, 5, 'Warm, human, and eerie—an unexpected combo that works.', '2025-06-24 16:33:03'),
(1490, 38, 50, 2, 'Action beats are cinematic. I could picture every scene.', '2025-08-30 16:33:03'),
(1491, 39, 50, 5, 'A few lulls, but the emotional payoff is absolutely worth it.', '2025-08-08 16:33:03'),
(1492, 40, 50, 2, 'Clever premise, executed with heart. I smiled a lot.', '2025-07-16 16:33:03'),
(1493, 41, 50, 4, 'The twist wasn’t a shock, but the journey was excellent.', '2025-06-24 16:33:03'),
(1494, 14, 50, 1, 'Beautiful prose. The middle is a bit slow, but the ending really lands.', '2025-08-30 16:33:03'),
(1495, 42, 50, 1, 'Short, sharp chapters kept me turning pages all weekend.', '2025-08-30 16:33:03'),
(1496, 15, 50, 3, 'Good world-building and a satisfying twist. I would read a sequel.', '2025-08-08 16:33:03'),
(1497, 16, 50, 5, 'Started strong, sagged a little, then finished big. Worth sticking with.', '2025-07-16 16:33:03'),
(1498, 17, 50, 3, 'Short chapters made it a fast, engaging read. Great on a commute.', '2025-06-24 16:33:03'),
(1499, 18, 50, 5, 'Characters felt real and messy—in a good way. Loved the dialogue.', '2025-08-30 16:33:03'),
(1500, 19, 50, 2, 'Moody atmosphere and slow burn tension. Not horror, but very eerie.', '2025-08-08 16:33:03'),
(1501, 20, 50, 4, 'Plot is clever without feeling showy. I highlighted a bunch of lines.', '2025-07-16 16:33:03'),
(1502, 21, 50, 1, 'Fun and pacey. Predictable in spots, but still super entertaining.', '2025-06-24 16:33:03');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(2, 'admin'),
(1, 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `method_id` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `name` varchar(120) NOT NULL,
  `carrier` varchar(120) DEFAULT NULL,
  `type` enum('flat','per_item') NOT NULL DEFAULT 'flat',
  `base_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `per_item` decimal(10,2) NOT NULL DEFAULT 0.00,
  `free_over` decimal(10,2) DEFAULT NULL,
  `eta_min_days` int(11) DEFAULT 2,
  `eta_max_days` int(11) DEFAULT 5,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_methods`
--

INSERT INTO `shipping_methods` (`method_id`, `code`, `name`, `carrier`, `type`, `base_rate`, `per_item`, `free_over`, `eta_min_days`, `eta_max_days`, `is_active`, `sort_order`) VALUES
(1, 'STD', 'Standard Delivery', 'In-house', 'flat', 3.99, 0.00, 50.00, 2, 5, 1, 10),
(2, 'EXP', 'Express Delivery', 'In-house', 'flat', 7.99, 0.00, NULL, 1, 2, 1, 20);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `profile_image_url` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `gender`, `email`, `phone`, `city`, `age`, `profile_image_url`, `password_hash`, `created_at`, `role_id`, `updated_at`) VALUES
(7, 'Thant Phyo', 'Maung', 'male', 'thantphyomaung186@gmail.com', '09265860770', 'Magway', 21, 'admin\\uploads\\avatars\\u_3_e5e020de.png', '$2y$10$6eXpyECSM67ZRORLbdS5CO5/vfB1LM.oRa.ff/2LCQBgu16ADkM6e', '2025-09-07 14:41:17', 2, '2025-09-08 08:49:20'),
(8, 'Ko', 'Thant', 'male', 'kothant@gmail.com', '09401511937', 'Yangon', 22, 'uploads/avatars/u_1757323708_660055d7.png', '$2y$10$8TGQuaq08V3bvUb6rvsmP.iRDgwJfyL2LVIF/ZbJ227KI9gYYVfCO', '2025-09-08 09:28:28', 1, '2025-09-17 15:11:33'),
(10, 'Ko', 'ko', 'male', 'koko@gmail.com', '09265850770', 'Mandalay', 21, 'uploads/avatars/u_1757347152_912af6f9.png', '$2y$10$lIUgbTEuUmmJ8dH8RPgRXeA2iWNepe4sleFiQgmAfpFF/KlxwAcje', '2025-09-08 15:59:12', 1, '2025-09-15 16:43:51'),
(11, 'Yamin', 'Akari', 'female', 'yamin@gmail.com', '09788699322', 'Yangon', 19, 'uploads/avatars/u_1757516466_87e1d4fa.png', '$2y$10$mnJNW6wzBc2uOiDBrrWxaeTiSDChL7Lt.4CsBnKYrbr9zndTPa0dC', '2025-09-10 15:01:06', 1, '2025-09-15 03:39:30'),
(13, 'Ava', 'Nguyen', 'female', 'ava.nguyen@booknest.local', '09788010013', 'Mandalay', 31, 'uploads/avatars/u_13_c8c5cfb1.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(14, 'Liam', 'Patel', 'male', 'liam.patel@booknest.local', '09788010014', 'Naypyidaw', 32, 'uploads/avatars/u_14_3b5259ba.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:43'),
(15, 'Zoe', 'Chen', 'male', 'zoe.chen@booknest.local', '09788010015', 'Mawlamyine', 33, 'uploads/avatars/u_15_d8bae672.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(16, 'Mateo', 'Rossi', 'female', 'mateo.rossi@booknest.local', '09788010016', 'Bago', 34, 'uploads/avatars/u_16_b042fd74.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(17, 'Hana', 'Kim', 'female', 'hana.kim@booknest.local', '09788010017', 'Pathein', 35, 'uploads/avatars/u_17_cc10ec2e.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:23:07'),
(18, 'Lucas', 'Silva', 'male', 'lucas.silva@booknest.local', '09788010018', 'Taunggyi', 36, 'uploads/avatars/u_18_61722cbc.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(19, 'Amira', 'Hassan', 'female', 'amira.hassan@booknest.local', '09788010019', 'Pyin Oo Lwin', 37, 'uploads/avatars/u_19_2b1605f5.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(20, 'Noah', 'Johnson', 'male', 'noah.johnson@booknest.local', '09788010020', 'Monywa', 38, 'uploads/avatars/u_20_394d0dbd.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:58:22'),
(21, 'Sofia', 'Garcia', 'male', 'sofia.garcia@booknest.local', '09788010021', 'Sittwe', 39, 'uploads/avatars/u_21_5b6ada61.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(22, 'Arjun', 'Mehta', 'female', 'arjun.mehta@booknest.local', '09788010022', 'Hpa-An', 40, 'uploads/avatars/u_22_75ff6c62.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(23, 'Mia', 'Ivanova', 'female', 'mia.ivanova@booknest.local', '09788010023', 'Dawei', 41, 'uploads/avatars/u_23_c1a0039e.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:23:34'),
(24, 'Ben', 'Cohen', 'male', 'ben.cohen@booknest.local', '09788010024', 'Yangon', 42, 'uploads/avatars/u_24_a3d9d807.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(25, 'Yuki', 'Tanaka', 'female', 'yuki.tanaka@booknest.local', '09788010025', 'Mandalay', 43, 'uploads/avatars/u_25_4b1ee3c8.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(26, 'Oliver', 'Smith', 'female', 'oliver.smith@booknest.local', '09788010026', 'Naypyidaw', 44, 'uploads/avatars/u_26_8a0b449b.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:23:44'),
(27, 'Layla', 'Ahmed', 'male', 'layla.ahmed@booknest.local', '09788010027', 'Mawlamyine', 45, 'uploads/avatars/u_27_02c3e841.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(28, 'Daniel', 'Park', 'female', 'daniel.park@booknest.local', '09788010028', 'Bago', 46, 'uploads/avatars/u_28_d25527f5.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(29, 'Chloe', 'Martin', 'male', 'chloe.martin@booknest.local', '09788010029', 'Pathein', 47, 'uploads/avatars/u_29_253a3624.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:23:52'),
(30, 'Rafael', 'Souza', 'male', 'rafael.souza@booknest.local', '09788010030', 'Taunggyi', 48, 'uploads/avatars/u_30_74a34945.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(31, 'Noura', 'Mansour', 'female', 'noura.mansour@booknest.local', '09788010031', 'Pyin Oo Lwin', 49, 'uploads/avatars/u_31_301ee8b4.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(32, 'Leo', 'Dubois', 'male', 'leo.dubois@booknest.local', '09788010032', 'Monywa', 50, 'uploads/avatars/u_32_73c3955b.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:24:03'),
(33, 'Isabella', 'Marino', 'male', 'isabella.marino@booknest.local', '09788010033', 'Sittwe', 18, 'uploads/avatars/u_33_e2106dbb.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(34, 'Ethan', 'Wong', 'female', 'ethan.wong@booknest.local', '09788010034', 'Hpa-An', 19, 'uploads/avatars/u_34_4627bbd5.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(35, 'Priya', 'Sharma', '', 'priya.sharma@booknest.local', '09788010035', 'Dawei', 20, 'uploads/avatars/u_35_15b4ddfc.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(36, 'Marco', 'Santoro', 'male', 'marco.santoro@booknest.local', '09788010036', 'Yangon', 21, 'uploads/avatars/u_36_5ac0906e.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(37, 'Julia', 'Kowalska', 'female', 'julia.kowalska@booknest.local', '09788010037', 'Mandalay', 22, 'uploads/avatars/u_37_2b552aeb.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(38, 'Diego', 'Torres', '', 'diego.torres@booknest.local', '09788010038', 'Naypyidaw', 23, 'uploads/avatars/u_38_2371b06a.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(39, 'Aisha', 'Khan', 'male', 'aisha.khan@booknest.local', '09788010039', 'Mawlamyine', 24, 'uploads/avatars/u_39_33cac95e.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(40, 'Felix', 'Mueller', 'female', 'felix.mueller@booknest.local', '09788010040', 'Bago', 25, 'uploads/avatars/u_40_0f255552.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(41, 'Niko', 'Petrov', '', 'niko.petrov@booknest.local', '09788010041', 'Pathein', 26, 'uploads/avatars/u_41_5e3181b0.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(42, 'Mei', 'Lin', 'male', 'mei.lin@booknest.local', '09788010042', 'Taunggyi', 27, 'uploads/avatars/u_42_559b538f.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-14 16:33:03', 1, '2025-09-15 03:22:23'),
(44, 'Amelia', 'Nguyen', 'female', 'reader01@booknest.local', '09788010001', 'Yangon', 23, 'uploads/avatars/u_44_513e3331.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-15 17:44:34', 1, '2025-09-15 03:22:23'),
(45, 'Liam', 'Patel', 'male', 'reader02@booknest.local', '09788010002', 'Yangon', 26, 'uploads/avatars/u_45_fc0818b0.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-16 17:44:34', 1, '2025-09-15 03:22:23'),
(46, 'Noah', 'Kim', 'male', 'reader03@booknest.local', '09788010003', 'Yangon', 24, 'uploads/avatars/u_46_dd9b4ce1.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-17 17:44:34', 1, '2025-09-15 03:22:23'),
(47, 'Ava', 'Johnson', 'female', 'reader04@booknest.local', '09788010004', 'Yangon', 22, 'uploads/avatars/u_47_3a1bc41c.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-18 17:44:34', 1, '2025-09-15 03:22:23'),
(48, 'Oliver', 'Garcia', 'male', 'reader05@booknest.local', '09788010005', 'Yangon', 27, 'uploads/avatars/u_48_5656c7b8.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-19 17:44:34', 1, '2025-09-15 03:22:23'),
(49, 'Sophia', 'Rossi', 'female', 'reader06@booknest.local', '09788010006', 'Yangon', 25, 'uploads/avatars/u_49_777a9de7.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-20 17:44:34', 1, '2025-09-15 03:22:23'),
(50, 'Elijah', 'Chen', 'male', 'reader07@booknest.local', '09788010007', 'Yangon', 28, 'uploads/avatars/u_50_c804d65d.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-21 17:44:34', 1, '2025-09-15 03:22:23'),
(51, 'Isabella', 'Martin', 'female', 'reader08@booknest.local', '09788010008', 'Yangon', 21, 'uploads/avatars/u_51_2b85009b.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-22 17:44:34', 1, '2025-09-15 03:22:23'),
(52, 'Lucas', 'Ahmed', 'male', 'reader09@booknest.local', '09788010009', 'Yangon', 29, 'uploads/avatars/u_52_7c818868.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-23 17:44:34', 1, '2025-09-15 03:22:23'),
(53, 'Mia', 'Santos', 'female', 'reader10@booknest.local', '09788010010', 'Yangon', 24, 'uploads/avatars/u_53_2dfd657d.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-24 17:44:34', 1, '2025-09-15 03:22:23'),
(54, 'Ethan', 'Park', 'male', 'reader11@booknest.local', '09788010011', 'Yangon', 23, 'uploads/avatars/u_54_e08be806.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-25 17:44:34', 1, '2025-09-15 03:22:23'),
(55, 'Charlotte', 'Wilson', 'female', 'reader12@booknest.local', '09788010012', 'Yangon', 22, 'uploads/avatars/u_55_10ab29b7.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-26 17:44:34', 1, '2025-09-15 03:22:23'),
(56, 'Henry', 'Lopez', 'male', 'reader13@booknest.local', '09788010013', 'Yangon', 31, 'uploads/avatars/u_56_6075ca1a.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-27 17:44:34', 1, '2025-09-15 03:22:23'),
(57, 'Grace', 'Lee', 'female', 'reader14@booknest.local', '09788010014', 'Yangon', 30, 'uploads/avatars/u_57_d52e173d.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-28 17:44:34', 1, '2025-09-15 03:22:23'),
(58, 'Jack', 'Brown', 'male', 'reader15@booknest.local', '09788010015', 'Yangon', 27, 'uploads/avatars/u_58_bcb4556f.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-29 17:44:34', 1, '2025-09-15 03:22:23'),
(59, 'Chloe', 'Davis', 'female', 'reader16@booknest.local', '09788010016', 'Yangon', 26, 'uploads/avatars/u_59_18100120.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-30 17:44:34', 1, '2025-09-15 03:22:23'),
(60, 'Benjamin', 'Thomas', 'male', 'reader17@booknest.local', '09788010017', 'Yangon', 28, 'uploads/avatars/u_60_4a796226.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-08-31 17:44:34', 1, '2025-09-15 03:22:23'),
(61, 'Lily', 'Walker', 'female', 'reader18@booknest.local', '09788010018', 'Yangon', 25, 'uploads/avatars/u_61_f978f1f1.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-01 17:44:34', 1, '2025-09-15 03:22:23'),
(62, 'Daniel', 'Young', 'male', 'reader19@booknest.local', '09788010019', 'Yangon', 32, 'uploads/avatars/u_62_b06d9735.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-02 17:44:34', 1, '2025-09-15 03:22:23'),
(63, 'Emma', 'Hall', 'female', 'reader20@booknest.local', '09788010020', 'Yangon', 24, 'uploads/avatars/u_63_7f01ce53.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-03 17:44:34', 1, '2025-09-15 03:22:23'),
(64, 'Matthew', 'Allen', 'male', 'reader21@booknest.local', '09788010021', 'Yangon', 29, 'uploads/avatars/u_64_a0015499.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-04 17:44:34', 1, '2025-09-15 03:22:23'),
(65, 'Zoe', 'Clark', 'female', 'reader22@booknest.local', '09788010022', 'Yangon', 23, 'uploads/avatars/u_65_4dacddec.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-05 17:44:34', 1, '2025-09-15 03:22:23'),
(66, 'Samuel', 'Lewis', 'male', 'reader23@booknest.local', '09788010023', 'Yangon', 27, 'uploads/avatars/u_66_46c34c4f.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-06 17:44:34', 1, '2025-09-15 03:22:23'),
(67, 'Aria', 'King', 'female', 'reader24@booknest.local', '09788010024', 'Yangon', 22, 'uploads/avatars/u_67_1977bbf8.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-07 17:44:34', 1, '2025-09-15 03:22:23'),
(68, 'David', 'Wright', 'male', 'reader25@booknest.local', '09788010025', 'Yangon', 26, 'uploads/avatars/u_68_d9ae9ed4.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-08 17:44:34', 1, '2025-09-15 03:22:23'),
(69, 'Nora', 'Scott', 'female', 'reader26@booknest.local', '09788010026', 'Yangon', 28, 'uploads/avatars/u_69_f4efd923.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-09 17:44:34', 1, '2025-09-15 03:22:23'),
(70, 'Joseph', 'Green', 'male', 'reader27@booknest.local', '09788010027', 'Yangon', 31, 'uploads/avatars/u_70_93aa2b2e.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-10 17:44:34', 1, '2025-09-15 03:22:23'),
(71, 'Maya', 'Turner', 'female', 'reader28@booknest.local', '09788010028', 'Yangon', 25, 'uploads/avatars/u_71_b15e0c6a.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-11 17:44:34', 1, '2025-09-15 03:22:23'),
(72, 'Andrew', 'Baker', 'male', 'reader29@booknest.local', '09788010029', 'Yangon', 24, 'uploads/avatars/u_72_b0c1b4c9.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-12 17:44:34', 1, '2025-09-15 03:22:23'),
(73, 'Ella', 'Rivera', 'female', 'reader30@booknest.local', '09788010030', 'Yangon', 23, 'uploads/avatars/u_73_26e9f3cc.png', '$2y$10$Y9abbclNoH44FR/QWJ.WtuRWX/WsxrzfHl.kW/XfqCxmmNpiGGupO', '2025-09-13 17:44:34', 1, '2025-09-15 03:22:23'),
(74, 'San', 'Pwint', 'male', 'san@gmail.com', '09402030402', 'Yangon', 19, 'uploads/avatars/u_1757948073_e85aa784.png', '$2y$10$seqEzlOFJavqBuwCmsH0XuSSrX5hBifg3yUNbfCAdvqcoFdCMMHsK', '2025-09-15 14:54:33', 1, '2025-09-17 15:08:51');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`user_id`, `book_id`, `created_at`) VALUES
(10, 48, '2025-09-18 04:40:24'),
(10, 49, '2025-09-18 04:40:23'),
(10, 50, '2025-09-16 11:09:54'),
(11, 47, '2025-09-14 17:43:34'),
(11, 48, '2025-09-14 17:43:29'),
(11, 49, '2025-09-14 17:43:28'),
(11, 50, '2025-09-14 17:43:23'),
(74, 47, '2025-09-15 14:57:07'),
(74, 48, '2025-09-15 14:57:06'),
(74, 49, '2025-09-15 14:57:05'),
(74, 50, '2025-09-15 14:57:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `idx_books_author_id` (`author_id`);

--
-- Indexes for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD PRIMARY KEY (`book_id`,`category_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_bc_book_id` (`book_id`);

--
-- Indexes for table `book_discounts`
--
ALTER TABLE `book_discounts`
  ADD PRIMARY KEY (`book_discount_id`),
  ADD KEY `idx_book` (`book_id`),
  ADD KEY `idx_active_dates` (`is_active`,`start_date`,`end_date`),
  ADD KEY `idx_bd_book_id` (`book_id`),
  ADD KEY `fk_book_discounts_discount` (`discount_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `uq_user_book` (`user_id`,`book_id`),
  ADD KEY `idx_cart_book_id` (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`),
  ADD UNIQUE KEY `uniq_disc_code` (`discount_code`),
  ADD KEY `idx_disc_active_dates` (`is_active`,`start_date`,`end_date`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `fk_orders_shipping_method` (`shipping_method_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_oi_book_id` (`book_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `token_hash` (`token_hash`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`book_id`),
  ADD UNIQUE KEY `uq_reviews_user_book` (`user_id`,`book_id`),
  ADD KEY `idx_reviews_book_id` (`book_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`method_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role_id` (`role_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`user_id`,`book_id`),
  ADD UNIQUE KEY `uniq_user_book` (`user_id`,`book_id`),
  ADD KEY `idx_wl_book` (`book_id`),
  ADD KEY `idx_wish_book_id` (`book_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `book_discounts`
--
ALTER TABLE `book_discounts`
  MODIFY `book_discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2050;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`),
  ADD CONSTRAINT `fk_books_author_id` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD CONSTRAINT `book_categories_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_book_categories_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `book_discounts`
--
ALTER TABLE `book_discounts`
  ADD CONSTRAINT `fk_bd_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_book_discounts_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_book_discounts_discount` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_items_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_shipmethod` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`method_id`),
  ADD CONSTRAINT `fk_orders_shipping_method` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`method_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wishlists_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wl_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
