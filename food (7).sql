-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 09:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '123');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'Anshul', 'ahdave1573@gmail.com', 'food', 'offers', '2025-08-08 09:40:56'),
(7, 'Anshul', 'ahdave1573@gmail.com', 'food', 'hello', '2025-11-27 10:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = In Stock, 0 = Out of Stock'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category`, `name`, `price`, `rating`, `image`, `is_available`) VALUES
(3, 'Starters', 'Paneer Tikka', 180.00, 4.5, 'uploads/1757500062_paneer_tikka.jpg', 1),
(4, 'Starters', 'Hara Bhara Kabab', 150.00, 4.8, 'uploads/1757518664_Hara Bhara Kabab.jpg', 1),
(5, 'Starters', 'Veg Spring Rolls', 140.00, 4.1, 'uploads/1757518788_spring_rolls.jpg', 1),
(6, 'Starters', 'Corn & Cheese Balls', 150.00, 4.5, 'uploads/1757518865_Corn & Cheese Balls.jpg', 1),
(7, 'Starters', 'Samosa', 90.00, 5.0, 'uploads/1757518948_samosa.jpg', 1),
(8, 'Starters', 'Aloo Tikki', 100.00, 4.8, 'uploads/1757519039_Aloo Tikki.jpg', 1),
(9, 'Starters', 'Chilli Panee', 200.00, 5.0, 'uploads/1757519123_Chilli Paneer.jpg', 1),
(10, 'Starters', 'Tandoori Mushroom', 199.00, 5.0, 'uploads/1757519201_Tandoori Mushroom.jpg', 1),
(11, 'Starters', 'Stuffed Baby Potatoes', 169.00, 4.7, 'uploads/1757519307_Stuffed Baby Potatoes.jpg', 1),
(12, 'Starters', 'French Fries', 99.00, 5.0, 'uploads/1757519461_French Fries.jpg', 1),
(13, 'Starters', 'Pav bhaji', 150.00, 5.0, 'uploads/1757519540_pavbhaji.jpg', 1),
(14, 'Soups', 'Tomato Shorba', 150.00, 5.0, 'uploads/1757521750_Tomato Shorba.jpg', 1),
(15, 'Soups', 'Sweet Corn Soup', 160.00, 4.9, 'uploads/1757521817_Sweet Corn Soup.jpg', 1),
(16, 'Soups', 'Hot & Sour Soup', 150.00, 4.5, 'uploads/1757521877_Hot & Sour Soup.jpg', 1),
(17, 'Soups', 'Veg Manchow Soup', 180.00, 4.4, 'uploads/1757521981_Veg Manchow Soup.jpeg', 1),
(18, 'Soups', 'Cream of Mushroom Soup', 200.00, 5.0, 'uploads/1757522033_Cream of Mushroom Soup.jpeg', 1),
(19, 'Soups', 'Spinach Soup', 180.00, 5.0, 'uploads/1757522078_Spinach Soup.jpg', 1),
(20, 'Soups', 'Lemon Coriander Soup', 160.00, 5.0, 'uploads/1757522127_Lemon Coriander Soup.jpg', 1),
(21, 'South Indian', 'Masala Dosa', 180.00, 5.0, 'uploads/1757523169_Masala Dosa.jpg', 1),
(22, 'South Indian', 'Plain Dosa', 120.00, 4.8, 'uploads/1757523264_Plain Dosa.jpg', 1),
(23, 'South Indian', 'Rava Dosa', 190.00, 4.8, 'uploads/1757523325_Rava Dosa.jpg', 1),
(24, 'South Indian', 'Onion Uttapam', 180.00, 4.7, 'uploads/1757523379_Onion Uttapam.jpg', 1),
(25, 'South Indian', 'Idli Sambhar', 190.00, 4.9, 'uploads/1757523430_Idli Sambhar.jpg', 1),
(26, 'South Indian', 'Medu Vada', 190.00, 4.9, 'uploads/1757523479_Medu Vada.jpg', 1),
(27, 'South Indian', 'Pesarattu', 230.00, 5.0, 'uploads/1757523583_Pesarattu.jpeg', 1),
(28, 'South Indian', 'Upma', 180.00, 4.8, 'uploads/1757523601_Upma.jpg', 1),
(29, 'Chinese', 'Veg Manchurian', 180.00, 4.8, 'uploads/1757525294_Veg Manchurian.jpg', 1),
(30, 'Chinese', 'Veg Hakka Noodles', 180.00, 4.9, 'uploads/1757525313_Veg Hakka Noodles.jpg', 1),
(31, 'Chinese', 'Schezwan Noodles', 200.00, 4.9, 'uploads/1757525333_Schezwan Noodles.jpg', 1),
(32, 'Chinese', 'Veg Fried Rice', 180.00, 4.5, 'uploads/1757525351_Veg Fried Rice.jpg', 1),
(33, 'Chinese', 'Paneer Chilli', 200.00, 4.5, 'uploads/1757525369_Paneer Chilli.jpg', 1),
(34, 'Chinese', 'Corn Chilli', 200.00, 4.9, 'uploads/1757525385_Corn Chilli.jpg', 1),
(35, 'Chinese', 'Baby Corn Manchurian', 220.00, 4.9, 'uploads/1757525401_Baby Corn Manchurian.jpg', 1),
(36, 'Chinese', 'Spring Rolls', 150.00, 4.5, 'uploads/1757525417_Chinese Spring Rolls.jpg', 1),
(37, 'Desserts', 'Gulab Jamun', 90.00, 4.8, 'uploads/1757526616_gulab_jamun.jpg', 1),
(38, 'Desserts', 'Rasgulla', 90.00, 4.5, 'uploads/1757526630_rasgulla.jpg', 1),
(39, 'Desserts', 'Kheer (Rice Pudding)', 150.00, 5.0, 'uploads/1757526645_Kheer.jpg', 1),
(40, 'Desserts', 'Jalebi', 90.00, 4.5, 'uploads/1757526662_Jalebi.jpg', 1),
(41, 'Desserts', 'Gajar Halwa', 150.00, 5.0, 'uploads/1757526679_Gajar Halwa.jpg', 1),
(42, 'Desserts', 'Rabdi', 150.00, 4.6, 'uploads/1757526694_Rabri.jpg', 1),
(43, 'Desserts', 'Ice Cream', 110.00, 4.4, 'uploads/1757526712_ice_cream00.jpg', 1),
(44, 'Desserts', 'Falooda', 180.00, 4.6, 'uploads/1757526727_Falooda.jpg', 1),
(45, 'Desserts', 'Chocolate Brownie', 90.00, 5.0, 'uploads/1757526740_chocolate_brownie.jpg', 1),
(46, 'Desserts', 'Kaju Katri', 150.00, 5.0, 'uploads/1757557498_Kaju katri.jpg', 1),
(47, 'Beverages', 'Masala Chai', 40.00, 4.8, 'admin/uploads/1757559003_Masala Chai.png', 1),
(48, 'Beverages', 'Ginger Lemon Tea', 100.00, 4.7, 'admin/uploads/1757559258_Ginger Lemon Tea.jpg', 1),
(49, 'Beverages', 'Filter Coffee', 80.00, 4.3, 'admin/uploads/1757559298_Filter Coffee.jpg', 1),
(50, 'Beverages', 'Mango Lassi', 120.00, 4.7, 'admin/uploads/1757559362_Mango Lassi.jpg', 1),
(51, 'Beverages', 'Sweet Lassi', 60.00, 4.6, 'admin/uploads/1757559413_Sweet Lassi.jpg', 1),
(52, 'Beverages', 'Buttermilk', 60.00, 4.0, 'uploads/1757573432_Buttermilk.jpg', 0),
(53, 'Beverages', 'Fresh Lime Soda', 70.00, 4.8, 'admin/uploads/1757559558_Fresh Lime Soda.jpg', 1),
(54, 'Beverages', 'Cold Coffee', 80.00, 4.8, 'admin/uploads/1757559610_Cold Coffee.jpg', 1),
(55, 'Salads', 'Garden Fresh Salad', 110.00, 4.8, 'admin/uploads/1757560846_Garden Fresh Salad.jpg', 1),
(56, 'Salads', 'Greek Salad', 130.00, 4.9, 'admin/uploads/1757560962_Greek Salad.jpg', 1),
(57, 'Salads', 'Sprouts Salad', 210.00, 5.0, 'uploads/1757561353_Sprouts Salad.jpg', 1),
(58, 'Salads', 'Cucumber & Tomato Salad', 120.00, 4.3, 'uploads/1757561327_Cucumber & Tomato Salad.jpg', 1),
(59, 'Salads', 'Chickpea Salad', 140.00, 4.1, 'uploads/1757561308_Chickpea Salad.jpg', 1),
(60, 'Salads', 'Fruit Salad', 160.00, 4.8, 'uploads/1757561290_Fruit Salad.jpg', 1),
(61, 'Salads', 'Corn & Cheese Salad', 160.00, 5.0, 'uploads/1757561264_Corn & Cheese Salad.jpg', 1),
(62, 'Pizzas', 'Margherita (Cheese, Tomato, Basil)', 180.00, 4.1, 'admin/uploads/1757574520_Margherita Pizza (Cheese, Tomato, Basil).jpg', 1),
(63, 'Pizzas', 'Veggie Supreme  (Onions, Capsicum, Olives, Mushrooms, Tomatoes)', 300.00, 4.6, 'admin/uploads/1757574638_Veggie Supreme Pizza (Onions, Capsicum, Olives, Mushrooms, Tomatoes).png', 1),
(64, 'Pizzas', 'Paneer Tikka Pizza', 320.00, 4.1, 'admin/uploads/1757574765_Paneer Tikka Pizza.jpg', 1),
(65, 'Pizzas', 'Mexican Green Wave (Jalapeños, Capsicum, Olives)', 300.00, 4.9, 'admin/uploads/1757574837_Mexican Green Wave (Jalapeños, Capsicum, Olives).png', 1),
(66, 'Pizzas', 'Farmhouse Pizza (Mushrooms, Capsicum, Tomatoes, Onions)', 320.00, 4.5, 'admin/uploads/1757574898_Farmhouse Pizza (Mushrooms, Capsicum, Tomatoes, Onions).png', 1),
(67, 'Pizzas', 'Cheese Burst Pizza', 380.00, 4.6, 'admin/uploads/1757575006_Cheese Burst Pizza.jpg', 1),
(68, 'Pizzas', 'Garlic Bread Pizza', 250.00, 4.7, 'admin/uploads/1757575117_Garlic Bread Pizza.jpg', 1),
(69, 'Pizzas', 'Spicy Veggie Delight (Chilies, Jalapeños, Capsicum)', 290.00, 4.0, 'admin/uploads/1757575178_Spicy Veggie Delight (Chilies, Jalapeños, Capsicum).jpg', 1),
(70, 'Pizzas', 'Corn & Cheese Pizza', 280.00, 4.5, 'admin/uploads/1757575253_Corn & Cheese Pizza.jpg', 1),
(71, 'Pizzas', 'Four Cheese Pizza (Mozzarella, Cheddar, Parmesan, Feta)', 450.00, 4.9, 'admin/uploads/1757575330_Four Cheese Pizza (Mozzarella, Cheddar, Parmesan, Feta).png', 1),
(72, 'Pizzas', 'BBQ Paneer Pizza', 380.00, 4.0, 'uploads/1757591086_BBQ Paneer Pizza.png', 1),
(73, 'Pizzas', 'Mediterranean Veg Pizza (Olives, Feta, Red Onion, Bell Peppers)', 400.00, 4.8, 'admin/uploads/1757575497_Mediterranean Veg Pizza (Olives, Feta, Red Onion, Bell Peppers).png', 1),
(74, 'Pizzas', 'Veggie Extravaganza (Loaded with all veggies)', 580.00, 5.0, 'admin/uploads/1757575548_Veggie Extravaganza (Loaded with all veggies).png', 1),
(75, 'Pizzas', 'Tandoori Paneer Pizza', 580.00, 5.0, 'uploads/1757575620_Tandoori Paneer Pizza.png', 1),
(76, 'Pizzas', 'Capsicum & Black Olive Pizza', 310.00, 5.0, 'admin/uploads/1757575691_Capsicum & Black Olive Pizza.png', 1),
(77, 'Pizzas', 'White Sauce Pizza (Creamy base with veggies)', 480.00, 5.0, 'admin/uploads/1757575772_White Sauce Pizza (Creamy base with veggies).png', 1),
(78, 'Indian Curries', 'Paneer Butter Masala', 200.00, 4.8, 'admin/uploads/1758770593_Paneer Butter Masala.png', 1),
(79, 'Indian Curries', 'Shahi Paneer', 210.00, 4.8, 'admin/uploads/1758770686_Shahi Paneer.png', 1),
(80, 'Indian Curries', 'Matar Paneer', 210.00, 5.0, 'admin/uploads/1758770732_Matar Paneer.png', 1),
(81, 'Indian Curries', 'Kadai Paneer', 210.00, 5.0, 'admin/uploads/1758770818_Kadai Paneer.png', 1),
(82, 'Indian Curries', 'Dal Makhani  ', 180.00, 4.5, 'admin/uploads/1758771143_Dal Makhani.png', 1),
(83, 'Indian Curries', 'Yellow Dal Tadka', 120.00, 5.0, 'admin/uploads/1758771182_Yellow Dal Tadka.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `offer_name` varchar(255) NOT NULL,
  `discount_percent` float NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `item_id`, `offer_name`, `discount_percent`, `start_date`, `end_date`) VALUES
(4, 65, 'offers', 45, '2025-09-25', '2025-12-13'),
(5, 69, 'Navratri', 30, '2025-09-25', '2025-10-02'),
(6, 72, 'Navratri', 38, '2025-09-25', '2025-10-02'),
(7, 10, 'Navratri', 40, '2025-09-25', '2025-10-26'),
(8, 3, 'offers', 45, '2025-09-25', '2025-11-30'),
(9, 30, 'Navratri', 45, '2025-09-25', '2025-10-04'),
(12, 72, 'Pizza Dhamaka', 25, '2025-10-15', '2025-11-30'),
(13, 81, 'offers Shahi ', 20, '2025-10-15', '2025-11-30'),
(14, 10, 'Festive Starter Deal', 35, '2025-10-15', '2025-11-30'),
(15, 67, 'Diwali Cheese Dhamaka', 30, '2025-10-15', '2025-11-02'),
(16, 82, 'Royal Diwali Dinner', 33, '2025-10-10', '2025-11-02'),
(17, 3, 'Tikka Festival offers', 35, '2025-10-15', '2025-11-07'),
(18, 72, 'Pizza Dhamaka', 30, '2025-12-06', '2025-12-25');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `offer_name` varchar(255) DEFAULT NULL,
  `order_status` varchar(50) DEFAULT 'Pending Payment',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `username`, `grand_total`, `discount_amount`, `offer_name`, `order_status`, `payment_method`, `created_at`) VALUES
(1, 'Anshul', 778.00, 0.00, '', 'Completed', 'cod', '2025-10-16 09:08:12'),
(2, 'Anshul', 778.00, 0.00, '', 'Pending Order', 'upi', '2025-10-16 09:11:03'),
(3, 'Anshul', 778.00, 0.00, '', 'Cancelled', NULL, '2025-10-16 09:14:04'),
(4, 'Anshul', 180.00, 0.00, '', 'Completed', NULL, '2025-10-16 09:17:30'),
(5, 'Anshul', 180.00, 0.00, '', 'Completed', NULL, '2025-10-16 09:18:13'),
(6, 'Anshul', 117.00, 63.00, 'Tikka Festival Treat', 'Completed', NULL, '2025-10-16 09:23:15'),
(7, 'Anshul', 117.00, 63.00, 'Tikka Festival Treat', 'Pending Order', 'cod', '2025-10-16 09:25:34'),
(8, 'Anshul', 200.60, 59.40, 'Royal Diwali Dinner', 'Completed', NULL, '2025-10-16 09:29:06'),
(9, 'Anshul', 200.60, 59.40, 'Royal Diwali Dinner', 'Completed', 'upi', '2025-10-16 09:32:12'),
(10, 'Anshul', 200.60, 59.40, 'Royal Diwali Dinner', 'Completed', 'card', '2025-10-16 09:34:45'),
(11, 'Anshul', 129.35, 69.65, 'Festive Starter Deal', 'Completed', 'upi', '2025-10-16 12:40:36'),
(12, 'Anshul', 70.00, 0.00, '', 'Completed', 'cod', '2025-10-16 14:06:40'),
(13, 'Anshul', 360.60, 59.40, 'Royal Diwali Dinner', 'Completed', NULL, '2025-10-17 06:24:49'),
(14, 'Anshul', 360.60, 59.40, 'Royal Diwali Dinner', 'Cancelled', NULL, '2025-10-17 06:24:52'),
(15, 'Anshul', 360.60, 59.40, 'Royal Diwali Dinner', 'Completed', NULL, '2025-10-17 06:24:59'),
(16, 'Anshul', 117.00, 63.00, 'Tikka Festival Treat', 'Completed', 'upi', '2025-10-17 06:51:37'),
(21, 'Anshul', 80.00, 0.00, '', 'Pending Order', 'upi', '2025-10-18 14:56:04'),
(22, 'Anshul', 150.00, 0.00, '', 'Pending Order', 'upi', '2025-11-27 04:16:41'),
(23, 'Anshul', 80.00, 0.00, '', 'Pending Order', 'cod', '2025-11-27 04:18:30'),
(24, 'ANSHUL', 80.00, 0.00, '', 'Completed', NULL, '2025-11-27 04:37:18'),
(25, 'Anshul', 500.00, 0.00, '', 'Completed', 'upi', '2025-11-27 05:46:19'),
(26, 'Anshul', 782.00, 158.00, 'Diwali Pizza Dhamaka', 'Pending Order', 'upi', '2025-11-27 10:11:17'),
(27, 'Anshul', 80.00, 0.00, '', 'Pending Order', 'upi', '2025-11-28 06:31:57'),
(28, 'Anshul', 285.00, 95.00, 'Diwali Pizza Dhamaka', 'Pending Order', 'upi', '2025-11-28 06:37:14'),
(29, 'Anshul', 285.00, 95.00, 'Pizza Dhamaka', 'Completed', 'upi', '2025-11-28 07:39:11'),
(30, 'Anshul', 336.00, 84.00, 'offers Shahi ', 'Completed', 'upi', '2025-11-29 05:34:21'),
(31, 'Anshul', 165.00, 135.00, 'offers', 'Pending Order', 'card', '2025-11-29 06:04:18'),
(32, 'Anshul', 90.00, 0.00, '', 'Completed', 'cod', '2025-11-29 11:41:40'),
(33, 'Anshul', 150.00, 0.00, '', 'Completed', 'card', '2025-11-29 13:27:45'),
(34, 'Anshul', 160.00, 0.00, '', 'Completed', 'card', '2025-11-29 13:34:30'),
(35, 'Anshul', 180.00, 0.00, '', 'Completed', 'cod', '2025-11-29 18:12:29'),
(36, 'Anshul', 510.00, 0.00, '', 'Completed', 'upi', '2025-11-29 18:42:14'),
(38, 'Anshul', 199.00, 0.00, '', 'Completed', 'upi', '2025-12-06 05:55:57'),
(39, 'Anshul', 165.00, 135.00, 'offers', 'Pending Order', 'card', '2025-12-06 06:04:10'),
(40, 'Anshul', 70.00, 0.00, '', 'Pending Order', 'cod', '2025-12-06 06:07:35'),
(41, 'Anshul', 165.00, 135.00, 'offers', 'Processing', 'cod', '2025-12-06 06:35:08'),
(42, 'Anshul', 70.00, 0.00, '', 'Processing', 'upi', '2025-12-06 06:55:17');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `price`, `quantity`) VALUES
(1, 1, 'Tandoori Mushroom', 199.00, 2),
(2, 1, 'Cheese Burst Pizza', 380.00, 1),
(3, 2, 'Tandoori Mushroom', 199.00, 2),
(4, 2, 'Cheese Burst Pizza', 380.00, 1),
(5, 3, 'Tandoori Mushroom', 199.00, 2),
(6, 3, 'Cheese Burst Pizza', 380.00, 1),
(7, 4, 'Dal Makhani  ', 180.00, 1),
(8, 5, 'Paneer Tikka', 180.00, 1),
(9, 6, 'Paneer Tikka', 180.00, 1),
(10, 7, 'Paneer Tikka', 117.00, 1),
(11, 8, 'Filter Coffee', 80.00, 1),
(12, 8, 'Dal Makhani  ', 120.60, 1),
(13, 9, 'Filter Coffee', 80.00, 1),
(14, 9, 'Dal Makhani  ', 120.60, 1),
(15, 10, 'Filter Coffee', 80.00, 1),
(16, 10, 'Dal Makhani  ', 120.60, 1),
(17, 11, 'Tandoori Mushroom', 129.35, 1),
(18, 12, 'Fresh Lime Soda', 70.00, 1),
(19, 13, 'Paneer Butter Masala', 200.00, 1),
(20, 13, 'Masala Chai', 40.00, 1),
(21, 13, 'Dal Makhani  ', 120.60, 1),
(22, 14, 'Paneer Butter Masala', 200.00, 1),
(23, 14, 'Masala Chai', 40.00, 1),
(24, 14, 'Dal Makhani  ', 120.60, 1),
(25, 15, 'Paneer Butter Masala', 200.00, 1),
(26, 15, 'Masala Chai', 40.00, 1),
(27, 15, 'Dal Makhani  ', 120.60, 1),
(28, 16, 'Paneer Tikka', 117.00, 1),
(33, 21, 'Filter Coffee', 80.00, 1),
(34, 22, 'Kaju Katri', 150.00, 1),
(35, 23, 'Cold Coffee', 80.00, 1),
(36, 24, 'Filter Coffee', 80.00, 1),
(37, 25, 'Garlic Bread Pizza', 250.00, 2),
(38, 26, 'Cheese Burst Pizza', 380.00, 1),
(39, 26, 'BBQ Paneer Pizza', 285.00, 1),
(40, 26, 'Paneer Tikka', 117.00, 1),
(41, 27, 'Cold Coffee', 80.00, 1),
(42, 28, 'BBQ Paneer Pizza', 285.00, 1),
(43, 29, 'BBQ Paneer Pizza', 285.00, 1),
(44, 30, 'Kadai Paneer', 168.00, 2),
(45, 31, 'Mexican Green Wave (Jalapeños, Capsicum, Olives)', 165.00, 1),
(46, 32, 'Samosa', 90.00, 1),
(47, 33, 'Pav bhaji', 150.00, 1),
(48, 34, 'Cold Coffee', 80.00, 2),
(49, 35, 'Chocolate Brownie', 90.00, 2),
(50, 36, 'Mexican Green Wave (Jalapeños, Capsicum, Olives)', 165.00, 1),
(51, 36, 'Kadai Paneer', 168.00, 1),
(53, 38, 'Tandoori Mushroom', 199.00, 1),
(54, 39, 'Mexican Green Wave (Jalapeños, Capsicum, Olives)', 165.00, 1),
(55, 40, 'Fresh Lime Soda', 70.00, 1),
(56, 41, 'Mexican Green Wave (Jalapeños, Capsicum, Olives)', 165.00, 1),
(57, 42, 'Fresh Lime Soda', 70.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`) VALUES
(6, 'Anshul', 'ahdave13@gmail.com', '8849919418', '123'),
(8, 'Jay', 'ahdave1573@gmail.com', '8884991944', '$2y$10$PlExy2AEgm83zmUn1ij2POiR2isZnkPXDBPejTVl8XfgV2dGrJFkK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
