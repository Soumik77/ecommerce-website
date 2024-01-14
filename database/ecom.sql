-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 14, 2024 at 05:49 AM
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
-- Database: `ecom`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin1');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `categories` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `categories`, `status`) VALUES
(18, 'Men', 1),
(19, 'Women', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(75) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `comment` text NOT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(250) NOT NULL,
  `city` varchar(50) NOT NULL,
  `pincode` int(11) NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `total_price` float NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `order_status` int(11) NOT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `user_id`, `address`, `city`, `pincode`, `payment_type`, `total_price`, `payment_status`, `order_status`, `added_on`) VALUES
(7, 2, 'House-65 Banagram R/A,MANIKGANJ sadar', 'Manikganj', 1800, 'COD', 35, 'pending', 1, '2024-01-13 05:08:54');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id`, `order_id`, `product_id`, `qty`, `price`) VALUES
(9, 7, 25, 1, 35);

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`id`, `name`) VALUES
(1, 'Pending'),
(2, 'Processing'),
(3, 'Shipped'),
(4, 'Canceled'),
(5, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mrp` float NOT NULL,
  `price` float NOT NULL,
  `qty` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `short_desc` varchar(2100) NOT NULL,
  `description` text NOT NULL,
  `meta_title` varchar(2000) NOT NULL,
  `meta_desc` varchar(2000) NOT NULL,
  `meta_keyword` varchar(2000) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `best_seller` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `categories_id`, `name`, `mrp`, `price`, `qty`, `image`, `short_desc`, `description`, `meta_title`, `meta_desc`, `meta_keyword`, `status`, `best_seller`) VALUES
(23, 1, 'a', 12, 12, 1, '923287145_7.png', 'g', 'g', 'g', 'g', 'g', 1, 0),
(24, 15, 'Men\'s Sweater', 143, 100, 3, '174934394_dfba11e6-a98e-4184-ba13-5e932e603a55.jpeg', 'Men\'s Sweater collection', 'Discover cozy comfort and timeless style with our exclusive collection of men\'s sweaters. From classic crewnecks to sophisticated cardigans, embrace the warmth of premium fabrics and elevate your winter wardrobe. Explore versatile designs that effortlessly blend fashion and function, perfect for any occasion. Stay snug in style with our curated selection of men\'s sweaters', 'Winter Best Sweater', '', 'Sweater', 1, 1),
(25, 18, 'BreezyFlex Shorts', 50, 35, 4, '300000291_half-pent-men-up.jpeg', 'BreezyFlex Shorts: Effortless comfort meets modern style. Lightweight and versatile, these shorts redefine casual chic for every occasion.\r\n\r\n', 'Introducing our \"BreezyFlex Shorts\" – where comfort meets style in perfect harmony. Designed for those who appreciate effortless coolness, these shorts are a must-have addition to your wardrobe.\r\n\r\nCrafted from a lightweight and breathable fabric, the \"BreezyFlex Shorts\" provide all-day comfort whether you\'re on the move or taking it easy. The flexible design ensures unrestricted movement, making them ideal for various activities – from casual outings to active pursuits.\r\n\r\nFeaturing a modern and versatile style, these shorts effortlessly transition from laid-back weekends to spontaneous adventures. The sleek yet relaxed silhouette, coupled with thoughtful details like [mention any unique features, e.g., an adjustable drawstring or innovative pocket design], sets the \"BreezyFlex Shorts\" apart as a go-to choice for those who appreciate both comfort and trendiness.\r\n\r\n', 'BreezyFlex Shorts For Men', 'Discover unmatched comfort and style with our BreezyFlex Shorts. Lightweight and versatile, these shorts redefine casual fashion for a look that effortlessly transitions from day to night.', 'BreezyFlex Shorts, lightweight shorts, versatile fashion, casual chic, comfortable clothing', 1, 1),
(26, 18, '2-Pack Woven Men Shorts ', 75, 60, 12, '293983681_Pant.jpeg', 'Elevate your style with our 2-Pack Woven Men Shorts. Versatile, comfortable, and effortlessly fashionable for any occasion.', 'Upgrade your casual wardrobe with our 2-Pack Woven Men Shorts, a versatile duo of comfort and style. Crafted with precision and designed for everyday ease, these shorts offer a perfect fit and a touch of sophistication. Ideal for any occasion, experience fashion-forward comfort with this essential set.', 'Shop Stylish Comfort: 2-Pack Woven Men Shorts for Versatile Casual Wear ', 'Elevate your wardrobe with our 2-Pack Woven Men Shorts – the epitome of comfort and style. Versatile, fashion-forward, and perfect for every occasion. Shop now at Delight Fashion', '2-pack woven men shorts, men\'s fashion, casual wear, comfortable shorts, versatile shorts, fashion-forward clothing', 1, 0),
(27, 18, 'Delight-boxer\'s Men', 45, 30, 12, '886180444_delight-boxer.jpeg', 'Indulge in supreme comfort with our Delight Boxer. Impeccably crafted for a perfect fit, these boxers offer a luxurious blend of softness and style, ensuring everyday confidence and ease.', 'Experience unparalleled comfort with our Delight Boxer. Meticulously crafted for a perfect fit, these boxers redefine everyday luxury. Embrace a blend of softness and style, designed to keep you comfortable and confident throughout the day. Elevate your essentials with Delight Boxer – where comfort meets sophistication.', 'Delight Boxer: Embrace Everyday Luxury and Comfort', 'Discover ultimate comfort with our Delight Boxer. Meticulously crafted for a perfect fit, experience luxurious softness and style in every moment. Elevate your essentials with Delight Boxer – where comfort meets sophistication. Shop now', 'Delight Boxer, men\'s underwear, comfort, luxury, softness, style, everyday essentials, fashion, confidence', 1, 1),
(28, 18, 'Boxer Men', 20, 18, 3, '385105034_boxer-men.jpeg', 'Indulge in the perfect blend of comfort and style with our men\'s boxers. Crafted for an impeccable fit, our boxers offer softness and confidence for every occasion.', 'Elevate your daily essentials with our men\'s boxer collection. Crafted for optimum comfort and style, our boxers boast a perfect fit, soft fabrics, and a touch of sophistication. Whether for everyday wear or special occasions, embrace unparalleled comfort and confidence with our meticulously designed men\'s boxers.', 'Discover Comfort and Style with Our Men\'s Boxers Collection', 'Explore ultimate comfort and style with our premium men\'s boxers collection. Meticulously crafted for a perfect fit, our boxers redefine everyday essentials. Elevate your wardrobe with confidence and comfort.', 'men\'s boxers, comfort, style, premium underwear, confidence, everyday essentials, fashion.', 1, 0),
(29, 19, 'Girl\'s latest soft panty', 12, 10, 3, '506884321_girls-panty.jpeg', 'Elevate her everyday with our girls\' panties collection. Crafted for comfort and adorned with delightful designs, each pair combines snug fit and charm. ', 'Introducing our collection of girls\' panties, where comfort and style meet in perfect harmony. Carefully crafted with soft fabrics and delightful designs, our panties ensure a snug fit and a touch of charm. Embrace confidence and individuality with every pair, making a statement in comfort and fashion for girls of all ages.\r\n\r\n\r\n\r\n\r\n\r\n', 'Charm and Comfort: Explore Our Girls\' Panties Collection', 'Discover comfort and style with our girls\' panties collection. Thoughtfully crafted for a perfect fit and adorned with delightful designs, these panties blend charm and confidence seamlessly.', 'Girls\' panties, comfort, style, delightful designs, snug fit, fashion, confidence,', 1, 0),
(30, 19, 'Green women\'s full shorts', 40, 30, 3, '663530764_girls-payjama-green.jpeg', 'Step into style and comfort with our Green Women\'s Full Shorts. Meticulously crafted for the perfect fit, these shorts bring a touch of vibrancy to your wardrobe. ', 'ntroducing our Green Women\'s Full Shorts – a fusion of style and comfort. Crafted with meticulous attention, these shorts offer a perfect fit and a touch of vibrancy to your wardrobe. Elevate your casual ensemble with these full shorts designed for the modern woman who embraces both fashion and ease.', 'Chic Comfort: Green Women\'s Full Shorts for Stylish Ease | ', 'Elevate your wardrobe with our Green Women\'s Full Shorts – a perfect blend of style and comfort. Meticulously crafted for the modern woman, embrace fashion effortlessly.', 'Green Women\'s Full Shorts, women\'s fashion, comfortable shorts, chic style, vibrant wardrobe.', 1, 1),
(31, 19, 'BlossomLoom Girls\' Underwear Collection', 40, 20, 12, '209203848_girls-underwear.jpeg', 'Elevate comfort and joy with BlossomLoom Girls\' Underwear Collection. Designed for snug fit and adorned with delightful details, these panties blend practicality with charm for a daily dose of style and confidence.', 'Introducing the BlossomLoom Girls\' Underwear Collection, where comfort meets charm. Crafted with soft fabrics and delightful designs, these panties offer a snug fit and a touch of whimsy. Elevate her essentials with this collection, ensuring every day is adorned with comfort, confidence, and a dash of youthful style.\r\n\r\n\r\n\r\n\r\n\r\n', 'BlossomLoom Girls\' Underwear: Comfortable, Adorable, and Always in Style', 'Discover the BlossomLoom Girls\' Underwear Collection – where comfort and charm harmonize. Crafted for a snug fit and adorned with delightful details, our panties bring daily joy and style. Explore comfort and confidence for your little one.', 'BlossomLoom, girls\' underwear, comfortable panties, charming designs, kids\' fashion, confidence, everyday essentials', 1, 1),
(32, 19, 'EleganceCozy Lady Sweater', 50, 40, 12, '784492328_lady-sweater.jpeg', 'EleganceCozy Lady Sweater: Wrap yourself in comfort and style. Crafted with soft knit fabric, this timeless sweater combines warmth with sophistication, making it a must-have for chilly days.', 'Introducing the \"EleganceCozy Lady Sweater,\" where warmth meets sophistication. Crafted with soft knit fabric and a timeless design, this sweater offers both comfort and style. Embrace the chilly seasons with elegance, as each thread weaves a story of cozy luxury and fashion-forward charm.\r\n\r\n\r\n\r\n\r\n\r\n', 'EleganceCozy Lady Sweater – Embrace Comfort and Style in Every Thread', 'Discover warmth and sophistication with the EleganceCozy Lady Sweater. Crafted from soft knit fabric, this timeless piece combines comfort and style for chilly days. Elevate your winter wardrobe with elegance and coziness.', 'EleganceCozy, lady sweater, knitwear, winter fashion, comfort and style, cozy luxury, fashion-forward charm', 1, 0),
(33, 19, 'Pink Payjama', 20, 15, 12, '160133606_payjama-girl.jpeg', '\r\n\"Unwind in style with our Pink Pajama – a cozy companion for serene nights. Crafted from soft, breathable fabric, this pajama promises comfort and a touch of femininity. ', 'Introducing our Pink Pajama, where comfort meets charm. Crafted from soft, breathable fabric, this pajama offers a relaxed fit and a touch of femininity to your sleepwear collection. Embrace cozy nights with a hint of style, making bedtime a delightful experience in this soothing shade of pink.', 'Cozy Elegance: Pink Pajama for Serene Nights', 'Indulge in comfort and style with our Pink Pajama. Crafted from soft, breathable fabric, this pajama brings a touch of femininity to bedtime. ', 'Pink Pajama, sleepwear, cozy nights, comfort and style, feminine loungewear, bedtime fashion,', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `added_on` datetime NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `added_on`, `password`) VALUES
(2, 'Soumik Datta', 'soumikdattta229@gmail.com', '8801315087698', '2024-01-06 05:42:20', 'soumik12345?'),
(3, 'Soumik Datta', 'songjuktadatta@gmail.com', '8801315087698', '2024-01-06 07:06:41', ''),
(5, 'Maria Chowdhury', 'mariachowdhury899@gmail.com', '8801315087698', '2024-01-06 01:03:02', 'maria12345?'),
(6, 'Tapu', 'tapu@gmail.com', '09797778371', '2024-01-06 03:37:34', 'tapu12345?'),
(7, 'Tapu Chowdhury', 'maha@gmail.com', '8801315087698', '2024-01-06 03:38:40', 'tapu12345?'),
(8, 'Mahbuba', 'ma@gmail.com', '09797778371', '2024-01-06 03:39:06', 'sou12345?');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
