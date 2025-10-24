-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 02:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `solar_company`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Residential', '2025-10-15 18:17:57'),
(2, 'Commercial', '2025-10-15 18:18:14'),
(3, 'Industrial', '2025-10-15 18:18:35');

-- --------------------------------------------------------

--
-- Table structure for table `color_palettes`
--

CREATE TABLE `color_palettes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `primary_color` varchar(7) NOT NULL,
  `secondary_color` varchar(7) NOT NULL,
  `accent_color` varchar(7) NOT NULL,
  `background_color` varchar(7) NOT NULL,
  `text_color` varchar(7) NOT NULL,
  `light_color` varchar(7) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `color_palettes`
--

INSERT INTO `color_palettes` (`id`, `name`, `primary_color`, `secondary_color`, `accent_color`, `background_color`, `text_color`, `light_color`, `is_active`, `created_at`) VALUES
(1, 'Solar Sunrise', '#FF6B35', '#2E86AB', '#F7C59F', '#FFFFFF', '#333333', '#F8F9FA', 0, '2025-10-10 15:49:11'),
(2, 'Eco Green', '#27AE60', '#2D9CDB', '#F2C94C', '#FFFFFF', '#333333', '#F8F9FA', 0, '2025-10-10 15:49:11'),
(3, 'Professional Blue', '#2E86AB', '#FF6B35', '#A8DADC', '#FFFFFF', '#1D3557', '#F1FAEE', 0, '2025-10-10 15:49:11'),
(4, 'Modern Gradient', '#FF6B35', '#4ECDC4', '#45B7D1', '#FFFFFF', '#2C3E50', '#F7F9FC', 0, '2025-10-10 15:49:11'),
(5, 'Premium Gold', '#D4AF37', '#2C3E50', '#E67E22', '#FFFFFF', '#2C3E50', '#FDF6E3', 0, '2025-10-10 15:49:11'),
(6, 'Vibrant Modern', '#FF6B35', '#6A4C93', '#1982C4', '#FFFFFF', '#2B2D42', '#F8F9FA', 0, '2025-10-10 15:49:11'),
(7, 'Earth Tones', '#E76F51', '#2A9D8F', '#E9C46A', '#FFFFFF', '#264653', '#F4F1DE', 1, '2025-10-10 15:49:11'),
(14, 'Earth Tones', '#E76F51', '#2A9D8F', '#E9C46A', '#FFFFFF', '#264653', '#F4F1DE', 0, '2025-10-10 15:57:06');

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `button_text` varchar(100) DEFAULT 'Get Started',
  `button_link` varchar(255) DEFAULT '#',
  `slide_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `image_path`, `button_text`, `button_link`, `slide_order`, `is_active`, `created_at`) VALUES
(1, 'Power Your Future with Solar Energy', 'Sustainable, Efficient, and Affordable Solar Solutions for Your Home and Business', 'assets/uploads/1760353182_ChatGPT Image Oct 4, 2025, 10_49_05 AM.png', 'Get Started', '#', 1, 1, '2025-10-09 10:28:31'),
(2, 'Save Money with Solar Power', 'Reduce your electricity bills by up to 80% with our premium solar systems', 'assets/uploads/1760416225_supermarket-worker-measuring-selling-meat-customer.jpg', 'Get Started', '#', 2, 1, '2025-10-09 10:28:31'),
(3, 'Clean Energy for a Better Tomorrow', 'Join the renewable energy revolution and make a positive environmental impact', 'assets/uploads/1760416254_devin-avery-bx1G9db3FjA-unsplash.jpg', 'Get Started', '#', 3, 1, '2025-10-09 10:28:31');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `image_path`, `price`, `features`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Residential Solar System', 'Complete home solar solution with battery storage', 'assets/uploads/1760161764_unnamed.png', 15000.00, '25-year warranty|Smart monitoring|Battery backup', 1, '2025-10-09 10:02:39', '2025-10-11 05:49:24'),
(2, 'Commercial Solar Array', 'Scalable solar solutions for businesses', NULL, 50000.00, 'High efficiency panels|Remote monitoring|Maintenance included', 1, '2025-10-09 10:02:39', '2025-10-09 10:02:39'),
(3, 'Solar Water Heater', 'Energy efficient solar water heating system', NULL, 4500.00, '80% energy savings|Durable construction|Easy installation', 1, '2025-10-09 10:02:39', '2025-10-09 10:02:39'),
(4, 'Solar Panel Cleaning', 'Professional solar panel maintenance service', NULL, 299.00, 'Increases efficiency|Safe cleaning|Quarterly service', 1, '2025-10-09 10:02:39', '2025-10-09 10:02:39'),
(5, 'Solar Battery Storage', 'Advanced battery storage for solar energy', NULL, 8000.00, '24/7 power backup|10-year warranty|Smart integration', 1, '2025-10-09 10:02:39', '2025-10-09 10:02:39'),
(6, 'Solar Consultation', 'Expert solar energy consultation service', NULL, 199.00, 'Free assessment|Custom solutions|Financing advice', 1, '2025-10-09 10:02:39', '2025-10-09 10:02:39');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `gallery_url` varchar(1000) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `system_size` varchar(100) DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `project_order` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `image_path`, `gallery_url`, `location`, `system_size`, `completion_date`, `is_active`, `project_order`, `category_id`, `created_at`) VALUES
(1, 'Residential Solar Installation', 'Complete home solar system with battery backup for a family home in suburban area.', '', '', 'Los Angeles, CA', '8.5 kW', '2023-06-15', 1, 0, 2, '2025-10-09 12:15:54'),
(2, 'Commercial Office Building', 'Large-scale solar installation for corporate office building with 24/7 monitoring.', '', '', 'San Francisco, CA', '250 kW', '2023-08-22', 1, 0, 3, '2025-10-09 12:15:54'),
(3, 'Solar Farm Project', 'Community solar farm providing clean energy for 200+ households.', '', '', 'Phoenix, AZ', '5 MW', '2023-11-30', 1, 0, 2, '2025-10-09 12:15:54'),
(4, 'School Solar Initiative', 'Solar power system for local school district reducing energy costs by 60%.', '', '', 'San Diego, CA', '150 kW', '2023-05-10', 1, 0, 1, '2025-10-09 12:15:54'),
(5, 'Hospital Backup System', 'Critical solar backup system for hospital emergency power needs.', '', '', 'Houston, TX', '500 kW', '2023-09-18', 1, 0, 3, '2025-10-09 12:15:54'),
(6, 'Apartment Complex', 'Multi-unit residential solar installation with shared battery storage.', NULL, '', 'Miami, FL', '1.2 MW', '2023-12-05', 1, 0, 1, '2025-10-09 12:15:54'),
(8, 'Commercial Rooftop Solar', 'Large-scale solar installation for office building with energy monitoring system.', '', 'gallery/commercial1', 'New York, NY', '250 kW', '2023-08-20', 1, 1, 1, '2025-10-09 12:16:00'),
(9, 'Agricultural Solar Farm', 'Ground-mounted solar array powering irrigation systems and farm operations.', '', 'gallery/agricultural1', 'Fresno, CA', '500 kW', '2023-11-10', 1, 2, 3, '2025-10-09 12:16:05'),
(10, 'Suburban Townhouse Complex', 'Multi-unit residential solar installation with shared battery storage.', '', 'gallery/residential2', 'San Diego, CA', '45 kW', '2023-09-05', 1, 3, 2, '2025-10-09 12:16:10'),
(11, 'Shopping Center Solar Canopy', 'Parking lot solar canopy providing shade and clean energy for retail complex.', '', 'gallery/commercial2', 'Miami, FL', '180 kW', '2023-07-30', 1, 4, 1, '2025-10-09 12:16:15'),
(12, 'Off-Grid Cabin System', 'Complete off-grid solar setup with battery backup for remote mountain cabin.', '', 'gallery/residential3', 'Aspen, CO', '3.2 kW', '2023-12-15', 1, 5, 2, '2025-10-09 12:16:20'),
(13, 'Industrial Warehouse Solar', 'Rooftop solar installation on large industrial facility with smart inverters.', '', 'gallery/industrial1', 'Houston, TX', '800 kW', '2023-10-22', 1, 6, 1, '2025-10-09 12:16:25'),
(14, 'Community Solar Garden', 'Shared solar array serving multiple households in urban community.', '', 'gallery/community1', 'Portland, OR', '1.2 MW', '2024-01-18', 1, 7, 3, '2025-10-09 12:16:30'),
(15, 'Luxury Villa Solar', 'High-end residential solar system with smart home integration.', '', 'gallery/residential4', 'Beverly Hills, CA', '15 kW', '2023-08-12', 1, 8, 2, '2025-10-09 12:16:35'),
(16, 'School District Solar', 'Multiple school rooftop installations across district with educational displays.', '', 'gallery/educational1', 'Chicago, IL', '350 kW', '2024-02-28', 1, 9, 1, '2025-10-09 12:16:40'),
(17, 'Floating Solar Farm', 'Innovative floating solar installation on reservoir with water conservation benefits.', '', 'gallery/innovative1', 'Phoenix, AZ', '2.5 MW', '2023-11-30', 1, 10, 3, '2025-10-09 12:16:45'),
(18, 'Apartment Building Retrofit', 'Solar installation on multi-story apartment complex with tenant billing system.', '', 'gallery/residential5', 'Seattle, WA', '120 kW', '2023-09-25', 1, 11, 2, '2025-10-09 12:16:50'),
(19, 'Hospital Emergency Backup', 'Critical care facility solar with battery backup for emergency power.', '', 'gallery/healthcare1', 'Boston, MA', '600 kW', '2024-03-15', 1, 12, 1, '2025-10-09 12:16:55'),
(20, 'Vineyard Solar Installation', 'Solar power for winery operations and irrigation systems in Napa Valley.', '', 'gallery/agricultural2', 'Napa, CA', '85 kW', '2023-10-05', 1, 13, 3, '2025-10-09 12:17:00'),
(21, 'Municipal Building Solar', 'City hall solar installation demonstrating municipal commitment to renewables.', '', 'gallery/government1', 'Denver, CO', '200 kW', '2024-01-08', 1, 14, 1, '2025-10-09 12:17:05'),
(22, 'Tiny Home Solar Setup', 'Compact solar system for tiny home living with efficient energy management.', '', 'gallery/residential6', 'Austin, TX', '2.5 kW', '2023-12-20', 1, 15, 2, '2025-10-09 12:17:10'),
(23, 'Car Dealership Solar', 'Showroom and service center solar installation with EV charging integration.', '', 'gallery/commercial3', 'Atlanta, GA', '300 kW', '2023-11-15', 1, 16, 1, '2025-10-09 12:17:15'),
(24, 'Rural Farmhouse Solar', 'Standalone solar system for remote farmhouse with livestock water pumping.', '', 'gallery/agricultural3', 'Boise, ID', '12 kW', '2024-02-10', 1, 17, 3, '2025-10-09 12:17:20'),
(25, 'University Campus Solar', 'Multiple building installation across university campus with research components.', '', 'gallery/educational2', 'Madison, WI', '1.5 MW', '2023-10-30', 1, 18, 1, '2025-10-09 12:17:25'),
(26, 'Beach House Solar', 'Coastal property solar installation with corrosion-resistant components.', '', 'gallery/residential7', 'Malibu, CA', '7.2 kW', '2023-07-18', 1, 19, 2, '2025-10-09 12:17:30'),
(27, 'Manufacturing Plant Solar', 'Industrial facility with solar power for production line operations.', '', 'gallery/industrial2', 'Detroit, MI', '1 MW', '2024-03-22', 1, 20, 1, '2025-10-09 12:17:35'),
(28, 'Mountain Resort Solar', 'Luxury resort solar installation with backup for guest amenities.', '', 'gallery/commercial4', 'Park City, UT', '400 kW', '2023-09-12', 1, 21, 1, '2025-10-09 12:17:40'),
(29, 'Subdivision Solar Community', 'New construction neighborhood with solar pre-installed on all homes.', '', 'gallery/residential8', 'Raleigh, NC', '650 kW', '2024-01-25', 1, 22, 2, '2025-10-09 12:17:45'),
(30, 'Water Treatment Plant Solar', 'Municipal water facility solar installation reducing operational costs.', '', 'gallery/government2', 'San Antonio, TX', '750 kW', '2023-12-05', 1, 23, 1, '2025-10-09 12:17:50'),
(31, 'Historic Home Solar', 'Carefully integrated solar system preserving architectural integrity of historic home.', '', 'gallery/residential9', 'Charleston, SC', '6.8 kW', '2023-08-28', 1, 24, 2, '2025-10-09 12:17:55'),
(32, 'Data Center Solar', 'High-density computing facility with solar power contribution.', '', 'gallery/industrial3', 'Ashburn, VA', '2 MW', '2024-02-14', 1, 25, 1, '2025-10-09 12:18:00'),
(33, 'RV Park Solar', 'Recreational vehicle park with solar-powered amenities and individual hookups.', '', 'gallery/commercial5', 'Las Vegas, NV', '180 kW', '2023-11-25', 1, 26, 1, '2025-10-09 12:18:05'),
(34, 'Greenhouse Solar', 'Agricultural greenhouse with solar power for climate control systems.', '', 'gallery/agricultural4', 'Salinas, CA', '65 kW', '2024-03-08', 1, 27, 3, '2025-10-09 12:18:10'),
(35, 'Fire Station Solar', 'Emergency services facility with solar backup for critical operations.', '', 'gallery/government3', 'Dallas, TX', '90 kW', '2023-10-18', 1, 28, 1, '2025-10-09 12:18:15'),
(36, 'Modern Condo Solar', 'High-rise condominium with shared solar system for common areas.', '', 'gallery/residential10', 'San Francisco, CA', '95 kW', '2023-07-22', 1, 29, 2, '2025-10-09 12:18:20'),
(37, 'Brewery Solar Installation', 'Craft brewery using solar power for brewing operations and tasting room.', '', 'gallery/commercial6', 'Portland, ME', '110 kW', '2024-01-12', 1, 30, 1, '2025-10-09 12:18:25'),
(38, 'Desert Ranch Solar', 'Remote desert property with solar power for well pumping and residence.', '', 'gallery/agricultural5', 'Tucson, AZ', '18 kW', '2023-12-28', 1, 31, 3, '2025-10-09 12:18:30'),
(39, 'Library Solar Project', 'Public library solar installation with educational kiosk for visitors.', '', 'gallery/educational3', 'Minneapolis, MN', '150 kW', '2024-02-20', 1, 32, 1, '2025-10-09 12:18:35'),
(40, 'Lake House Solar', 'Waterfront property solar with battery backup for boat lift and dock lighting.', '', 'gallery/residential11', 'Lake Tahoe, CA', '9.5 kW', '2023-08-05', 1, 33, 2, '2025-10-09 12:18:40'),
(41, 'Airport Solar Canopy', 'Airport parking garage solar installation powering terminal operations.', '', 'gallery/transportation1', 'Orlando, FL', '1.8 MW', '2024-03-30', 1, 34, 1, '2025-10-09 12:18:45'),
(42, 'Church Solar Initiative', 'Place of worship solar installation reducing operational expenses.', '', 'gallery/nonprofit1', 'Kansas City, MO', '75 kW', '2023-11-08', 1, 35, 1, '2025-10-09 12:18:50'),
(43, 'Mountain Cabin Solar', 'Remote alpine cabin with solar power for heating and essential systems.', '', 'gallery/residential12', 'Jackson Hole, WY', '4.2 kW', '2023-10-12', 1, 36, 2, '2025-10-09 12:18:55'),
(44, 'Retail Store Solar', 'Big-box retail store rooftop solar installation.', '', 'gallery/commercial7', 'Columbus, OH', '450 kW', '2024-01-30', 1, 37, 1, '2025-10-09 12:19:00'),
(45, 'Orchard Solar Power', 'Fruit orchard solar system for irrigation and cold storage.', '', 'gallery/agricultural6', 'Yakima, WA', '120 kW', '2023-09-18', 1, 38, 3, '2025-10-09 12:19:05'),
(46, 'Research Facility Solar', 'Scientific research center with solar power for laboratory equipment.', '', 'gallery/educational4', 'Cambridge, MA', '280 kW', '2024-02-25', 1, 39, 1, '2025-10-09 12:19:10'),
(47, 'Townhome Community Solar', 'Attached townhome development with individual solar systems.', '', 'gallery/residential13', 'Nashville, TN', '210 kW', '2023-12-12', 1, 40, 2, '2025-10-09 12:19:15'),
(48, 'Hotel Solar Array', 'Luxury hotel solar installation powering guest rooms and amenities.', '', 'gallery/hospitality1', 'Miami Beach, FL', '550 kW', '2024-03-12', 1, 41, 1, '2025-10-09 12:19:20'),
(49, 'Dairy Farm Solar', 'Dairy operation solar power for milking equipment and refrigeration.', '', 'gallery/agricultural7', 'Madera, CA', '160 kW', '2023-11-20', 1, 42, 3, '2025-10-09 12:19:25'),
(50, 'Office Park Solar', 'Multi-tenant office complex with shared solar infrastructure.', '', 'gallery/commercial8', 'Charlotte, NC', '320 kW', '2024-01-18', 1, 43, 1, '2025-10-09 12:19:30'),
(51, 'Eco-Home Solar', 'Net-zero energy home with integrated solar and energy efficiency features.', '', 'gallery/residential14', 'Boulder, CO', '10.5 kW', '2023-10-25', 1, 44, 2, '2025-10-09 12:19:35'),
(52, 'Warehouse Distribution Solar', 'Logistics center solar installation for warehouse operations.', '', 'gallery/industrial4', 'Memphis, TN', '900 kW', '2024-02-08', 1, 45, 1, '2025-10-09 12:19:40'),
(53, 'Ranch Property Solar', 'Large ranch estate solar system for main house and outbuildings.', '', 'gallery/agricultural8', 'Santa Fe, NM', '25 kW', '2023-09-30', 1, 46, 3, '2025-10-09 12:19:45'),
(54, 'Museum Solar Project', 'Cultural institution solar installation with preservation considerations.', '', 'gallery/nonprofit2', 'Washington, DC', '190 kW', '2024-03-18', 1, 47, 1, '2025-10-09 12:19:50'),
(55, 'Duplex Solar Installation', 'Two-unit residential property with separate solar metering.', '', 'gallery/residential15', 'Salt Lake City, UT', '13.6 kW', '2023-12-22', 1, 48, 2, '2025-10-09 12:19:55'),
(56, 'Golf Course Solar', 'Country club solar installation powering clubhouse and maintenance facilities.', '', 'gallery/commercial9', 'Scottsdale, AZ', '220 kW', '2024-01-05', 1, 49, 1, '2025-10-09 12:20:00'),
(57, 'Mobile Home Park Solar', 'Manufactured home community with central solar power system.', '', 'gallery/community2', 'Riverside, CA', '180 kW', '2023-11-05', 1, 50, 3, '2025-10-09 12:20:05');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(11) DEFAULT 5,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `customer_name`, `review_text`, `rating`, `image_path`, `is_active`, `created_at`) VALUES
(1, 'John Smith', 'Excellent service! Our energy bills reduced by 80% after installing their solar system. Highly recommended!', 5, NULL, 1, '2025-10-09 10:02:39'),
(2, 'Sarah Johnson', 'Professional team from start to finish. The installation was quick and the results are amazing.', 5, NULL, 1, '2025-10-09 10:02:39'),
(3, 'Mike Wilson', 'Great quality products and outstanding customer service. The solar panels are working perfectly.', 4, NULL, 1, '2025-10-09 10:02:39'),
(4, 'Emily Davis', 'The team was knowledgeable and helped us choose the perfect system for our home. Very happy with the results!', 5, NULL, 1, '2025-10-09 10:02:39'),
(5, 'David Brown', 'Fast installation, competitive pricing, and excellent after-sales support. Five stars!', 5, NULL, 1, '2025-10-09 10:02:39'),
(6, 'Lisa Anderson', 'Our business saved thousands on electricity costs. The commercial solar system was a great investment.', 5, NULL, 1, '2025-10-09 10:02:39'),
(7, 'Chaturanga', 'good service', 5, 'assets/uploads/1760164291_20250905_2356_Stick-Figure Profile Humor_simple_compose_01k4dhz3ehe63tn4qmbjs0nv8g(1).png', 1, '2025-10-11 06:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$kFKzudewunquyXIhN/sbQ.SU6.CxrCs.LfnFaMwhKtZFyumFyNrHK', '2025-10-09 09:40:13');

-- --------------------------------------------------------

--
-- Table structure for table `website_content`
--

CREATE TABLE `website_content` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `website_content`
--

INSERT INTO `website_content` (`id`, `section_name`, `title`, `content`, `image_path`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'about', 'About Us', 'We are a leading solar energy company dedicated to providing sustainable energy solutions. With years of experience, we help homeowners and businesses transition to clean, renewable energy.', 'assets/uploads/1760348700_68ecca1ca336c_unnamed.png', 1, '2025-10-13 09:26:28', '2025-10-13 10:56:57'),
(2, 'why_choose', 'Why Choose Us', 'We offer comprehensive solar solutions with industry-leading warranties, maximum efficiency panels, and round-the-clock customer support.', NULL, 1, '2025-10-13 09:26:28', '2025-10-13 10:58:33'),
(3, 'contact', 'Contact Us', 'Get in touch with our solar experts to discuss your energy needs and schedule a free consultation.', NULL, 1, '2025-10-13 09:26:28', '2025-10-13 10:58:52'),
(4, 'products_title', 'Our Products & Services', 'Discover our range of high-quality solar products and professional installation services.', NULL, 1, '2025-10-13 09:26:28', '2025-10-13 09:26:28'),
(5, 'projects', 'Our Projects', 'Latest Projects', NULL, 1, '2025-10-13 09:49:26', '2025-10-13 10:57:41'),
(6, 'products', 'Our Products & Services', 'List of our products & services', NULL, 1, '2025-10-13 09:49:30', '2025-10-13 10:58:07'),
(7, 'Review', 'What Our Customers Says', '', NULL, 1, '2025-10-14 04:14:23', '2025-10-14 04:33:23');

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE `website_settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `website_settings`
--

INSERT INTO `website_settings` (`id`, `setting_name`, `setting_value`, `updated_at`) VALUES
(1, 'website_logo', NULL, '2025-10-24 12:58:14'),
(2, 'header_background', '', '2025-10-10 16:25:32'),
(3, 'header_text_color', '', '2025-10-10 16:25:34'),
(7, 'google_analytics_id', '', '2025-10-10 12:47:34'),
(8, 'google_analytics_status', 'disabled', '2025-10-10 12:47:34'),
(9, 'google_analytics_anonymize_ip', '0', '2025-10-10 12:47:34'),
(10, 'google_analytics_enhanced_link_attribution', '0', '2025-10-10 12:47:34'),
(11, 'google_analytics_cross_domain_tracking', '0', '2025-10-10 11:59:23'),
(12, 'google_analytics_remarketing', '0', '2025-10-10 11:59:23'),
(18, 'privacy_policy_content', '<p><strong>Privacy Policy</strong></p>\r\n\r\n<p><strong>Effective Date:</strong> <em>01/10/2025</em></p>\r\n<p><strong>Company Name:</strong> <em>Solar Energy Solutions</em></p>\r\n<p><strong>Website:</strong> <em>www.solarcompany.com</em></p>\r\n\r\n<h2>Introduction</h2>\r\n<p>At <strong>[Your Company Name]</strong>, we respect your privacy and are committed to protecting the personal information you share with us. This Privacy Policy explains what information we collect, how we use it, and your choices regarding that information when you visit our website, contact us, or use our solar energy products and services.</p>\r\n\r\n<h2>1. Information We Collect</h2>\r\n\r\n<h3>a. Personal Information</h3>\r\n<ul>\r\n  <li><p>We may collect information you provide when you contact us, request a quote, create an account, or purchase services. This may include: <strong>full name</strong>, <strong>email address</strong>, <strong>phone number</strong>, <strong>billing/shipping address</strong>, and <strong>payment details</strong>.</p></li>\r\n</ul>\r\n\r\n<h3>b. Technical Information</h3>\r\n<ul>\r\n  <li><p>When you visit our website we may automatically collect technical data such as your <strong>IP address</strong>, <strong>browser type/version</strong>, <strong>device information</strong>, <strong>pages visited</strong>, and <strong>cookies/usage data</strong>.</p></li>\r\n</ul>\r\n\r\n<h3>c. Solar System & Device Data</h3>\r\n<ul>\r\n  <li><p>If you use our monitoring systems or services, we may collect <strong>system performance</strong> and <strong>energy production/consumption</strong> data, device identifiers, and related telemetry necessary to run and maintain your solar system.</p></li>\r\n</ul>\r\n\r\n<h2>2. How We Use Your Information</h2>\r\n<ul>\r\n  <li><p>To provide, operate, and improve our products and services.</p></li>\r\n  <li><p>To process orders, quotations, installations, and payments.</p></li>\r\n  <li><p>To respond to inquiries, provide customer support, and send service notifications.</p></li>\r\n  <li><p>To send marketing communications where you have consented, and to analyze and improve our website and offerings.</p></li>\r\n  <li><p>To comply with legal obligations and protect our legal rights.</p></li>\r\n</ul>\r\n\r\n<h2>3. How We Share Your Information</h2>\r\n<p>We do <strong>not</strong> sell or rent your personal information. We may share information with:</p>\r\n<ul>\r\n  <li><p><strong>Service providers</strong> (payment processors, hosting providers, installers) who help deliver our services.</p></li>\r\n  <li><p><strong>Business partners</strong> involved in installation, maintenance, or financing of solar systems.</p></li>\r\n  <li><p><strong>Legal or regulatory authorities</strong> when required by law or to protect our rights.</p></li>\r\n</ul>\r\n<p>All third parties are required to protect your information and use it only for the purposes we authorize.</p>\r\n\r\n<h2>4. Data Retention</h2>\r\n<p>We retain personal data only as long as necessary to fulfill the purposes described in this policy, comply with legal requirements, resolve disputes, and enforce agreements. When information is no longer needed, we will securely delete or anonymize it.</p>\r\n\r\n<h2>5. Cookies and Tracking Technologies</h2>\r\n<p>We use cookies and similar technologies to improve site functionality, analyze usage, and deliver relevant content. You can manage or disable cookies through your browser settings, but some parts of the site may not function correctly if cookies are blocked.</p>\r\n\r\n<h2>6. Data Security</h2>\r\n<p>We implement reasonable technical and organizational measures to protect your personal data against accidental or unlawful destruction, loss, alteration, unauthorized disclosure, or access. However, no internet transmission or storage system is completely secure; we cannot guarantee absolute security.</p>\r\n\r\n<h2>7. Your Rights</h2>\r\n<p>Depending on your jurisdiction, you may have rights including:</p>\r\n<ul>\r\n  <li><p><strong>Access</strong> — request a copy of personal data we hold about you.</p></li>\r\n  <li><p><strong>Correction</strong> — request correction of inaccurate or incomplete data.</p></li>\r\n  <li><p><strong>Deletion</strong> — request erasure of your personal data, subject to legal exceptions.</p></li>\r\n  <li><p><strong>Object or restrict processing</strong> — where applicable, and to withdraw consent to marketing communications.</p></li>\r\n</ul>\r\n<p>To exercise these rights, contact us at the address below. We may need to verify your identity before fulfilling certain requests.</p>\r\n\r\n<h2>8. Third-Party Links</h2>\r\n<p>Our website may contain links to third-party sites. We are not responsible for the privacy practices or content of those sites. Please review their privacy policies before sharing personal information.</p>\r\n\r\n<h2>9. International Transfers</h2>\r\n<p>If we transfer your data to other countries for processing, we will ensure appropriate safeguards are in place to protect your personal information consistent with applicable law.</p>\r\n\r\n<h2>10. Changes to This Policy</h2>\r\n<p>We may update this Privacy Policy from time to time. We will post the updated policy on our website with a revised <strong>Effective Date</strong>. Please check this page periodically for changes.</p>\r\n\r\n<h2>11. Contact Us</h2>\r\n<p>If you have questions, requests, or concerns about this Privacy Policy or our data practices, please contact:</p>\r\n<ul>\r\n  <li><p><strong>Solar Energy Solutions</strong></p></li>\r\n  <li><p><strong>Address:</strong> <em>123 Solar Street, Energy City, EC 12345</em></p></li>\r\n  <li><p><strong>Email:</strong> <em>info@solarcompany.com</em></p></li>\r\n  <li><p><strong>Phone:</strong> <em>+1 (555) 123-456733</em></p></li>\r\n</ul>\r\n\r\n<p><em>Note:</em> Replace placeholder text (e.g., company name, contact details, effective date) with your company\'s information. If you’d like, I can adapt this policy to comply specifically with Sri Lanka’s Personal Data Protection Act or translate it into Sinhala or Tamil.</p>\r\n', '2025-10-16 08:57:51'),
(19, 'terms_of_service_content', '<h2>Terms of Service</h2><p>Welcome to our website...</p>', '2025-10-10 13:10:26'),
(20, 'privacy_last_updated', '2025-10-16 10:57:51', '2025-10-16 08:57:51'),
(21, 'terms_last_updated', '', '2025-10-10 13:10:26'),
(36, 'company_name', 'Solar Energy Solutions', '2025-10-15 20:08:54'),
(37, 'company_email', 'info@solarcompany.com', '2025-10-10 13:26:58'),
(38, 'company_phone', '+1 (555) 123-456733', '2025-10-11 06:38:53'),
(39, 'company_address', '123 Solar Street, Energy City, EC 12345', '2025-10-11 06:29:46'),
(40, 'company_working_hours', 'Mon-Fri: 9AM-6PM', '2025-10-10 13:26:58'),
(41, 'social_facebook', 'https://www.facebook.com/', '2025-10-15 20:09:18'),
(42, 'social_x', 'https://x.com/', '2025-10-15 20:15:03'),
(43, 'social_instagram', 'https://www.instagram.com/', '2025-10-15 20:22:30'),
(44, 'social_linkedin', 'https://www.linkedin.com/', '2025-10-15 20:22:30'),
(45, 'social_youtube', 'https://www.youtube.com/', '2025-10-15 20:22:30'),
(46, 'social_whatsapp', '(555) 123-456733', '2025-10-16 09:15:22'),
(47, 'website_favicon', '', '2025-10-10 13:26:58'),
(48, 'contact_map_embed', '', '2025-10-10 13:26:58'),
(49, 'contact_form_email', 'info@solarcompany.com', '2025-10-10 13:26:58'),
(50, 'current_color_palette', '7', '2025-10-15 20:51:55'),
(52, 'projects_count', '50+', '2025-10-15 20:06:24'),
(53, 'clients_count', '149+', '2025-10-13 10:59:26'),
(54, 'satisfaction_rate', '100%', '2025-10-13 09:04:10'),
(55, 'years_of_experience', '12+', '2025-10-13 09:12:14'),
(56, 'smtp_host', 'smtp.gmail.com', '2025-10-13 12:26:41'),
(57, 'smtp_port', '587', '2025-10-13 12:26:41'),
(58, 'smtp_username', 'chaturanga900@gmail.com', '2025-10-13 12:28:01'),
(59, 'smtp_password', 'chaturanga900@gmail.com', '2025-10-13 12:28:01'),
(60, 'smtp_encryption', 'ssl', '2025-10-13 12:28:07'),
(61, 'from_email', 'chaturanga900@gmail.com', '2025-10-13 12:30:02'),
(62, 'from_name', 'Solar Energy Solutions - test', '2025-10-13 12:28:01'),
(63, 'admin_notification_email', 'chaturanga900@gmail.com', '2025-10-13 12:30:02'),
(64, 'whatsapp_number', '(555) 123-456733', '2025-10-16 09:15:34'),
(65, 'whatsapp_welcome_message', 'Hello', '2025-10-13 12:54:07'),
(66, 'whatsapp_button_position', 'bottom-right', '2025-10-13 13:24:51'),
(67, 'whatsapp_button_style', 'floating', '2025-10-13 13:23:29'),
(68, 'whatsapp_enabled', '1', '2025-10-13 12:55:36'),
(69, 'whatsapp_show_desktop', '1', '2025-10-13 12:48:59'),
(70, 'whatsapp_show_mobile', '1', '2025-10-13 12:48:59'),
(71, 'privacy_meta_title', 'Privacy Policy - Solar Energy Solutions', '2025-10-16 08:50:03'),
(72, 'privacy_meta_description', 'Read our Privacy Policy to understand how we collect, use, and protect your personal information.', '2025-10-15 21:23:07');

-- --------------------------------------------------------

--
-- Table structure for table `why_choose_features`
--

CREATE TABLE `why_choose_features` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon_class` varchar(100) NOT NULL DEFAULT 'fas fa-star',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `why_choose_features`
--

INSERT INTO `why_choose_features` (`id`, `title`, `description`, `icon_class`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '25-Year Warranty', 'Comprehensive warranty on all our solar installations', 'fas fa-shield-alt', 1, 1, '2025-10-13 11:08:12', '2025-10-13 11:08:12'),
(2, 'Maximum Efficiency', 'High-efficiency panels that generate more power', 'fas fa-shield-alt', 2, 1, '2025-10-13 11:08:12', '2025-10-13 11:36:25'),
(3, '24/7 Support', 'Round-the-clock customer support and monitoring', 'fas fa-headset', 3, 1, '2025-10-13 11:08:12', '2025-10-13 11:08:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `color_palettes`
--
ALTER TABLE `color_palettes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `website_content`
--
ALTER TABLE `website_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_name` (`section_name`);

--
-- Indexes for table `website_settings`
--
ALTER TABLE `website_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indexes for table `why_choose_features`
--
ALTER TABLE `why_choose_features`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `color_palettes`
--
ALTER TABLE `color_palettes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `website_content`
--
ALTER TABLE `website_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `website_settings`
--
ALTER TABLE `website_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `why_choose_features`
--
ALTER TABLE `why_choose_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
