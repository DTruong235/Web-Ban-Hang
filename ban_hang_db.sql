-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th3 30, 2026 lúc 02:58 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ban_hang_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `role_id`, `status`) VALUES
(1, 'admin_master', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(1, 'Orion'),
(2, 'Mirinda'),
(3, 'Heejin'),
(4, 'Pew Store'),
(5, 'Unilever');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `status`) VALUES
(1, 'Rau Củ', 'rau-cu', NULL, 1),
(2, 'Bánh Mì & Bánh Ngọt', 'banh-mi', NULL, 1),
(3, 'Rượu', 'ruou', NULL, 1),
(4, 'Sữa - Trứng', 'sua-trung', 'Sữa tươi là sữa của các loại động vật như bò, dê, cừu,... được vắt trực tiếp. Sữa tươi chưa được tiệt trùng hay khử trùng bởi các thiết bị xử lý nhiệt. Ở Việt Nam, loại sữa tươi phổ biến nhất là sữa bò, được thu hoạch tại các trang trại lớn.\r\n\r\nĐặc điểm và phân loại sữa tươi\r\n\r\nSữa tươi hiện nay được phân thành 4 loại cơ bản mà bạn có thể dễ dàng tìm kiếm, mua và sử dụng ở bất cứ nơi đâu, bao gồm: Sữa tươi thanh trùng, sữa tươi tiệt trùng, sữa tươi nguyên kem và sữa tươi tách béo, ít béo.\r\n\r\nSữa tươi thanh trùng được xử lý ở nhiệt độ khoảng 90 độ C trong 30 giây, sữa được đun nóng thật nhanh rồi làm lạnh đột ngột để tiêu diệt các vi khuẩn có khả năng gây bệnh và tăng thời hạn sử dụng cho sữa. Do được xử lý ở nhiệt độ vừa phải nên sữa thanh trùng giữ được gần như toàn bộ các vitamin, khoáng chất và hương vị thơm ngon đặc trưng.\r\n\r\nSữa tươi tiệt trùng được xử lý với công nghệ tiệt trùng hiện đại UHT ở nhiệt độ cao từ 140-143 độ C trong khoảng 3-4 giây rồi làm lạnh đột ngột để loại bỏ vi khuẩn, nấm có hại. Sữa tươi tiệt trùng có thể bảo quản ở nhiệt độ bình thường và có hạn sử dụng dài từ 6 tháng - 1 năm nếu bảo quản ở nơi thoáng mát, tránh nhiệt độ cao.\r\n\r\nSữa tươi nguyên kem không qua xử lý nhiệt và vẫn giữ nguyên 100% sữa nguyên chất, không thêm hay bớt thành phần nào trong sữa khi tới tay người tiêu dùng. Sữa tươi nguyên kem được giữ nguyên dinh dưỡng, lớp váng sữa và chất béo nên khi sử dụng sữa bạn sẽ cảm nhận được vị béo, thơm ngon từ sữa tươi.\r\n\r\nSữa tách béo, ít béo là sữa tươi nguyên chất được tách béo, do được tách đi lớp béo nên sữa giảm hàm lượng chất béo và calo trong sữa. Sữa tách béo, ít béo thích hợp với những người muốn ăn kiêng, người muốn giảm cân.\r\n\r\nCác thương hiệu sữa tươi ngon nổi tiếng đang bán tại Bách hoá XANH\r\n\r\nHiện nay, trên thị trường đang có rất nhiều sản phẩm sữa tươi ngon, nhiều hương vị như sữa tươi hương dâu, sữa tươi hương socola,... đến từ nhiều thương hiệu khác nhau, đủ các sản phẩm sữa trong nước và nhập khẩu cho các bạn thoải mái lựa chọn tại Bách hoá XANH như: Sữa tươi Vinamilk, sữa tươi Pure Milk, sữa tươi Dutch Lady, sữa tươi TH True Milk, sữa tươi Australia\'s Own, sữa tươi Meadow Fresh,...\r\n\r\nNhững lưu ý khi sử dụng và bảo quản sữa tươi\r\n\r\nĐể sử dụng và bảo quản sữa tươi đúng cách, bạn nên lưu ý một số điều sau:\r\n\r\nLưu ý: Đối với sữa tươi thanh trùng, bạn nên lắc đều trước khi dùng để đảm bảo các thành phần trong sữa được hòa quyện đều1.\r\n\r\nSữa tươi nên được sử dụng ngay sau khi mở nắp để đảm bảo chất lượng và hương vị tốt nhất.\r\n\r\nSữa tươi cần được bảo quản ở nhiệt độ từ 3-5°C. Đối với sữa tươi thanh trùng, bạn nên bảo quản trong tủ lạnh ngay cả khi chưa mở nắp.\r\n\r\nĐể sữa tươi ở nơi khô ráo, thoáng mát và tránh ánh nắng trực tiếp để ngăn ngừa sự phát triển của vi khuẩn.\r\n\r\nSau khi sử dụng, hãy đậy kín nắp chai để ngăn không khí và vi khuẩn xâm nhập, giữ cho sữa tươi lâu hơn.\r\n\r\nKhông để sữa ở nhiệt độ phòng quá lâu vì sẽ dễ bị hỏng, nhất là trong những ngày trời nắng nóng.\r\n\r\n \r\n\r\nUống sữa tươi có tác dụng gì cho sức khỏe?\r\n\r\nSữa tươi có thể nói là một loại thực phẩm bổ dưỡng, cần thiết nên được bổ sung vào trong thực đơn của chúng ta. Sữa tươi vừa thơm ngon, dễ uống lại vừa có nhiều công dụng tốt cho sức khoẻ, cụ thể là:\r\n\r\nSữa tươi cung cấp nhiều vitamin và khoáng chất cho cơ thể. Trong sữa tươi cực kỳ giàu vitamin B12, vitamin B2 (hay còn gọi là Riboflavin), vitamin D và canxi hỗ trợ phát triển chiều cao, phốt pho,... Do đó, sữa tươi sẽ giúp duy trì sự phát triển và tăng trưởng ổn định cho cơ thể.\r\n\r\nSữa tươi giúp duy trì và phát triển cơ bắp nhờ chúng có đạm whey chiếm 20% hàm lượng protein trong sữa. Đạm whey cực kỳ giàu axit amin tốt cho cải thiện, phục hồi và phát triển cơ bắp. \r\n\r\nNhờ hàm lượng canxi và vitamin D dồi dào có trong sữa mà sữa giúp hỗ trợ phát triển xương, chiều cao ở trẻ em và giúp làm giảm nguy cơ loãng xương ở những người lớn tuổi. Ngoài ra trong sữa tươi còn chứa carbohydrate dồi dào, giúp làm giảm huyết áp và tăng cường hoạt động của hệ tim mạch và mạch máu. \r\n\r\nCó thể nói, sữa tươi mang lại nhiều công dụng tuyệt vời cho sức khoẻ, do đó, chúng ta nên duy trì thói quen uống sữa tươi mỗi ngày để cho cơ thể luôn tràn đầy sức sống và khỏe mạnh hơn.', 1),
(5, 'Thịt - Gia Cầm', 'thit-gia-cam', 'Bên cạnh thịt heo, thịt bò là một trong những loại thịt mang đến nguồn dinh dưỡng cao và luôn có mặt trong các bữa ăn trong gia đình ở khắp thế giới. Trong thịt bò có chứa rất nhiều protein và chất sắt,… đây là những chất dinh dưỡng rất tốt cho sức khỏe và sắc đẹp. Đặc biệt, đây cũng là một loại thực phẩm rất tốt dành cho người muốn giảm cân, tăng cơ bắp rất hiệu quả.\r\n\r\nThành phần dinh dưỡng có trong thịt bò\r\nTrong 100g thịt bò có chứa khoảng 28g protein, 10g lipid, cung cấp 280 kcal, nhiều gấp đôi so với cá và nhiều loại thịt động vật khác. Ngoài ra, thịt bò cũng có nhiều công dụng tốt cho sức khoẻ như cung cấp năng lượng, bổ sung máu, tăng cường cơ bắp, khả năng miễn dịch,...\r\n\r\nLợi ích của thịt bò đối với sức khỏe:\r\nThịt bò là một loại thực phẩm rất tốt cho sức khỏe, giàu giá trị dinh dưỡng, mang lại nhiều lợi ích như:\r\n\r\nTăng cường cơ bắp.\r\n\r\nTăng cường khả năng miễn dịch, phục hồi cơ thể.\r\n\r\nChống oxy các mô bị tổn thương.\r\n\r\nGiúp giảm béo, bổ sung máu.\r\n\r\nCung cấp năng lượng cho cơ thể\r\n\r\nMặc dù thịt bò có nhiều tác dụng tốt đối với cơ thể nhưng bạn hãy mua thịt tại nơi uy tín, ăn khi thịt đã được chế biến kỹ và đặc biệt kiêng thịt khi đang điều trị bệnh nhé.\r\n\r\nCác loại thịt bò và cách phân biệt\r\n\r\nTheo phần thịt bò:\r\n\r\nBắp bò: bao gồm phần đó thịt bắp chân trước và sau, thích hợp dùng để hầm, luộc, hấp, kho hay nấu canh để giữ độ ngon có sẵn của thịt bắp.\r\n\r\nThăn bò: là phần thịt mềm nhất của con bò, có sớ thịt nhỏ, mềm, ít mỡ, chỉ cần chế biến cơ bản, đơn giản cũng đã toát ra hương vị thơm ngon đặc biệt.\r\n\r\nBa chỉ bò: phần thịt nằm ở bụng bò, có thịt nạc và mỡ xen kẽ, ăn mềm, béo hay dùng để nướng, xào, nhúng lẩu.\r\n\r\nNạc vai bò: phần thịt ở lưng bò, thịt mềm, ngọt và một trong những phần thịt ngon nhất của con bò, dùng để nấu phở, bít tết, lẩu, xào. Nếu thịt có xen lẫn mỡ, gân thì dùng làm bò hầm, bò viên, lagu…\r\n\r\nĐùi bò: Phần thịt ở phía sau của bò, ít mỡ và dai hơn so với các phần thịt khác, rất phù hợp dùng để chế biến những món nướng lâu, hầm, hoặc xay làm thịt băm.\r\n\r\nSườn bò: Với những ân mỡ hấp dẫn, sườn bò thường được dùng để nướng.\r\n\r\nLõi vai bò: Còn gọi là thịt thăn vai, phần thịt nằm ở vùng vai trước của con bò, giáp với cổ và sườn, thịt có các thớ thịt xen kẽ với vân mỡ, tạo nên độ mềm mại.\r\n\r\nNên mua phần nào của thịt bò thì ngon nhất? Để nấu được một món ăn ngon, chúng ta nên chọn nguyên liệu phù hợp với món ăn chứ không phải nguyên liệu ngon, đắt tiền nhất.\r\n\r\nTheo nguồn gốc, xuất xứ:\r\nThị trường thịt bò ngày càng trở nên nhộn nhịp. Bên cạnh thịt bò Việt Nam, những loại thịt bò nhập khẩu từ nhiều thị trường cũng được ưa chuộng. Vậy làm thế nào để biết được đâu là thịt bò Việt Nam và đâu là thịt bò nhập khẩu, hãy cùng Bách hóa XANH tìm hiểu nhé:\r\n\r\nThịt bò Việt Nam: Ở Việt Nam, các hộ chăn nuôi bò khá tự do. Bò ngoài nuôi lấy thịt còn có thể nuôi lấy sức kéo làm nông. Vì vậy, thịt bò Việt Nam có thể sẽ dai và chắc hơn so với thịt bò nhập khẩu, cùng với đó là được giết mổ và tiêu thụ ngay nên thịt bò Việt Nam thường có màu tươi hơn các loại thịt bò nhập khẩu.\r\n\r\nThịt bò nhập khẩu: là những loại thịt bò nhập khẩu từ nhiều quốc gia khác nhau hoặc các loại bò giống Châu Âu, được nhập và nuôi tại Việt Nam bằng những phương pháp và công nghệ hiện đại. Thịt bò nhập khẩu được bán với dạng thịt bò tươi hoặc thịt bò đông lạnh tại nhiều nơi nhưng nhìn chung đặc tính của các loại bò này là nhiều mỡ và thịt mềm hơn so với thịt bò Việt Nam.\r\n \r\n\r\nThịt bò nhập khẩu và thịt bò Việt Nam đã quá quen thuộc với những món như bít tết, những món nướng, lẩu,... và đều và những loại thịt bò được ưa chuộng bởi người Việt.\r\n\r\nCác thương hiệu bán thịt bò sạch, tươi ngon nổi tiếng\r\nNgày nay, nhu cầu sử dụng thịt bò của người tiêu dùng ngày càng tăng cao, không chỉ dừng lại ở các loại thịt bò trong nước mà họ còn quan tâm đến những loại thịt bò nhập khẩu từ nhiều nước như Úc, Mỹ,... mang đến hương vị và đặc trưng khác nhau trong từng miếng thịt bò. Một số thương hiệu thịt bò sạch uy tín, tươi ngon với giá tốt như:\r\n\r\nThịt bò Ace Foods\r\n\r\nThịt bò Bách hóa XANH\r\n\r\n Thịt bò GoFood\r\n\r\nThịt bò Kiwifood\r\n\r\nThịt bò Ori Food\r\n\r\n\r\nCách chọn mua thịt bò tươi ngon\r\nThịt bò tươi ngon, chất lượng sẽ có những đặc điểm như sau:\r\n\r\nThịt có màu đỏ tươi, mỡ màu trắng hoặc hơi ngả vàng, thớ thịt nhỏ. Nếu thớ thịt càng nhỏ và mỡ càng trắng thì đó là thịt của bò tơ, thịt mềm.\r\n\r\nBề mặt thịt bò tươi hơi khô và se. Dùng tay nhấn vào phần thịt, nếu thấy thịt mềm, khá dẻo, ít tính đàn hồi thì thịt còn tươi \r\n\r\nKhông chọn những miếng bề ngoài thịt hơi nhớt, có mùi máu tanh khó chịu. Đặc biệt, không chọn thịt bò giả với đặc điểm màu thịt bên ngoài và bên trong không đồng nhất, khi dùng tay chạm vào thì bị dính một ít màu lên tay\r\n \r\n\r\nThịt bò làm món gì ngon\r\nThịt bò góp mặt trong rất nhiều đồ ăn ngon của người Việt với những công thức chế biến độc đáo, mang đến hương vị thơm ngon, độc đáo khó cưỡng như:\r\n\r\nPhở trộn thịt bò\r\n\r\nThịt bò xào tỏi\r\n\r\nBò kho\r\n\r\nThịt bò hầm rau củ\r\n\r\nBò cuốn lá lốt\r\n\r\nMiến trộn thịt bò\r\n\r\nBò xào hành tây\r\n\r\nBò nướng lá lốt\r\n\r\nBún bò Huế\r\n \r\n\r\nCách ướp thịt bò ngon, mềm, đậm đà như ngoài hàng\r\nThịt bò nướng là món ăn ngon, tuy nhiên ướp thịt bò làm sao để không ảnh hưởng tới vị nguyên bản của thịt bò, cùng Bách hóa XANH tìm hiểu nhé:\r\n\r\nBước 1: Sơ chế nguyên liệu: Trộn tất cả các nguyên liệu gồm: Hành, tỏi, sả, dầu hào, dầu điều, mật ong, bột quế, tiêu xay. Tùy vào lượng thịt bò định ướp mà bạn cho thêm muối, tiêu, đường, nước mắm vào chung với hỗn hợp.\r\nBước 2: Ướp thịt bò: Lấy phần nguyên liệu ướp đã chuẩn bị, trộn đều và bóp thịt bò để thấm gia vị. Để ngăn mát tủ lạnh 2-3 tiếng để thịt thấm đều gia vị hơn.\r\n\r\nCách bảo quản thịt bò đúng cách\r\nThịt bò sau khi mua về cần cho vào túi nilon, hộp đậy kín hoặc quấn màng bọc thực phẩm để ngăn mùi và vi khuẩn rồi đặt trong ngăn đá tủ lạnh ngay mà không cần rửa nước. Ngoài ra, bạn cũng có thể bảo quản thịt bò trong ngăn mát tủ lạnh trong khoảng 2-4 độ C.\r\n\r\nNhững lưu ý khi mua thịt bò:\r\n\r\nThịt bò có bao nhiêu calo?\r\nThịt bò thuộc nhóm thịt đỏ, thường chứa lượng chất sắt nhiều hơn so với các loại thịt trắng và cá. Thậm chí, mỗi bộ phận trên cơ thể con bò sẽ có hàm lượng giá trị dinh dưỡng khác nhau. Thịt bò là loại thực phẩm giàu và trung bình 100gr thịt bò cung cấp khoảng 250 calo. Trong đó:\r\n\r\nChất đạm: 26.1gr\r\n\r\nChất béo: 11.8gr\r\n\r\nNước: 61%\r\n\r\nTuy thịt bò có hàm lượng axit béo omega 3 cao, nhưng thịt bò không làm tăng cân, mà ngược lại còn giúp săn chắc cơ bắp nếu bạn có một chế độ ăn uống hợp lý. Lượng calo trong 100 – 200g thịt bò chỉ khoảng 250 – 576 calo rất phù hợp để bổ sung vào các bữa ăn giảm cân. Ngoài ra, thịt bò còn chứa nhiều hàm lượng dinh dưỡng giúp cân bằng thể trạng trong suốt quá trình giảm cân. Bởi vậy, bạn nên bổ sung món thịt bò giảm cân vào kế hoạch ăn kiêng của mình.\r\n\r\nCách phân biệt thịt bò già và thịt bò tơ?\r\nChúng ta có thể phân biệt thịt bò già và thịt bò tơ thông qua các đặc điểm sau:\r\n\r\nBề mặt thịt: Bò tơ có thớ thịt nhỏ, mềm, mịn, có màu đỏ tươi trong khi thớ thịt bò già to, màu đỏ thẫm.\r\n\r\nLớp da: Thịt bò tơ có da mềm, mỏng còn da thịt bò già rất dày, có lỗ chân lông to, có mùi hôi.\r\n\r\nLớp mỡ: Lớp mỡ của thịt bò tơ có màu trắng và của thịt bò già là màu xanh xám.\r\n \r\n\r\nMẹo khử mùi hôi cho thịt bò?\r\nThịt bò tuy được rất nhiều người yêu thích, nhưng lại có mùi rất đặc trưng, nhưng chúng ta hoàn toàn có thể khử mùi hôi thịt bò dễ dàng, chỉ bằng một số những nguyên liệu có sẵn trong nhà bếp như: Gừng, rượu, chanh và giấm, muối, hành tím và tỏi.\r\n\r\nMua thịt bò sạch tươi ngon giá tốt ở đâu?\r\nNgày nay, thịt bò được tẩm màu để làm tăng độ bắt mắt hoặc các loại thịt heo giả bò được bán tràn lan trên thị trường. Để tránh mua nhầm những loại thực phẩm bẩn này, bạn nên đến các siêu thị, cửa hàng thực phẩm lớn hoặc đặt mua online nhanh chóng tại bachhoaxanh.com để nhận ngay những miếng thịt bò tươi ngon tự nhiên, đậm đà và an toàn cho sức khỏe với giá phải chăng cùng dịch vụ giao hàng nhanh chóng tận nơi.\r\n\r\nNơi nhập hàng và quy trình sơ chế của Bách hóa XANH\r\nBách hoá XANH luôn có hai mặt hàng thịt bò bán song song, từ hàng thịt nóng đến hàng thịt đông lạnh. Đối với hàng thịt bò đông lạnh sẽ được nhập từ các nhà cung cấp uy tín vào mỗi ngày và được bán ngay cho người tiêu dùng. Ngoài ra, thịt bò Việt Nam mỗi ngày sẽ được nhập từ các nguồn cung thịt tươi ngon, cùng với quy trình bảo quản, đóng gói nghiêm ngặt, mang đến người dùng những miếng thịt tươi nhất.\r\n\r\nThông tin liên quan về thịt bò\r\n\r\nThịt bò mua phần nào ngon nhất?\r\n\r\nTổng hợp các loại thịt bò ngon và được ưa chuộng nhất ở Việt Nam\r\n\r\nMách bạn cách chọn thịt bò tươi ngon cho từng món ăn\r\n\r\nƯớp thịt bò với những thứ này, miếng thịt bò già, dai cũng hóa mềm ngay lập tức!\r\n\r\nBí quyết nhỏ giúp thịt bò dù già vẫn chín mềm thơm ngon', 1),
(6, 'Nước Ngọt', 'nuoc-ngot', NULL, 1),
(7, 'Đồ Gia Dụng', 'do-gia-dung', NULL, 1),
(8, 'Mỹ Phẩm', 'my-pham', NULL, 1),
(9, 'Mỳ - Miến - Cháo - Phở', 'my-mien-chao-pho', 'Mì ăn liền là gì?\r\nMì ăn liền hay còn gọi là mì tôm, là một sản phẩm đồ ăn liền, dạng khô, được đóng gói cùng gói bột súp, dầu gia vị, nguyên liệu sấy khô,... Gia vị thường được đóng thành từng gói riêng hoặc được rót sẵn chung với vắt mì. Cách ăn vô cùng đơn giản chỉ cần một ít nước sôi cùng vài phút là có thể ăn ngay.\r\n\r\nThành phần dinh dưỡng của mì ăn liền\r\n\r\nVắt mì: Bột lúa mì, dầu thực vật, thành phần tạo màu,...\r\n\r\nGói rau sấy: hành lá, baro, bắp, cà rốt, mùi thơm, nấm….\r\n\r\nGói súp: gia vị muối, bột ngọt, tiêu ớt, bột tôm, bột thịt gà, thịt heo, thịt bò, nõn tôm…\r\n\r\nGói dầu gia vị: Dầu tinh luyện, các loại rau củ ( hành, tỏi, ngò rí, ngò gai, …)\r\n\r\nThành phần dinh dưỡng của một gói mì tôm bao gồm: chất đạm, chất béo, carbohydrate là 3 thành phần cung cấp năng lượng cho cơ thể.\r\n\r\n\r\nCông dụng của mì ăn liền\r\n\r\nĐem lại sự tiện lợi nhờ cách chế biến nhanh, đơn giản mà ai cũng làm được, chỉ với vài phút nhanh chóng. Với sự cải tiến của mì ăn liền, ngày nay còn có thêm mì ly, mì tô, mì hộp được đóng gói kỹ càng, bạn có thể dễ dàng cho mì vào balo, túi xách mang theo và ăn bất cứ đâu chỉ cần với một ít nước sôi.\r\n\r\nĐi kèm sự tiện lợi mì ăn liền cũng giúp tiết kiệm được nhiều thời gian hơn dành cho bữa ăn, rất phù hợp với những người thường xuyên bận rộn.\r\n\r\nGiá thành rẻ giúp tiết kiệm được chi phí.\r\n\r\nCung cấp được nguồn năng lượng nhất định đủ để cơ thể hoạt động trong một buổi chỉ với một tô mì gói, kéo dài đến bữa ăn tiếp theo.\r\n\r\nThời gian bảo quản lâu tầm 6 tháng đến 1 năm, có thể dùng làm lương thực dự trữ trong gia đình, hoặc làm quà tặng, quà cứu trợ,...\r\n\r\n\r\nCác loại mì ăn liền\r\nHiện nay, trên thị trường phân ra nhiều loại mì ăn liền nổi tiếng khác nhau. Có nhiều cách để phân loại mì ăn liền dựa trên phương pháp khác nhau.\r\n\r\nTheo cách chế biến có mì nấu với nước, mì trộn, mì xào, mì sợi Hàn Quốc, mì Udon ăn liền mì khoai tây.\r\n\r\nTheo quy cách đóng gói có thể phân thành như sau: thùng mì, mì gói ăn liền, mì tô, ly, khay, lốc mì ăn liền..\r\n\r\n\r\nCác thương hiệu mì ăn liền nổi tiếng hiện nay\r\nMì ăn liền là phẩm không xa lạ gì đối với người dân Việt Nam, món ăn chế biến nhanh và vô cùng tiện lợi, giá thành lại rẻ nên được ưa chuộng. Ngày nay, ngày càng xuất hiện thêm những thương hiệu mì ăn liền được nhiều người ưa thích được sản xuất trong nước đến nhập khẩu, nổi bật có mì Hảo Hảo, mì 3 Miền, mì Nongshim, mì Sangyang, mì Ottogi, mì Indomie, mì Mama, mì Koreno, mì Omachi,...\r\n\r\nCác sản phẩm mì ăn liền được ưa chuộng nhất hiện nay\r\n\r\nMì ăn liền Kang Shi Fu vị bò hầm hộp 110g: được sản xuất tại Hàn Quốc, hương vị thơm ngon hấp dẫn, súp bò hầm thanh ngọt, phù hợp cho nhiều đối tượng. Giá bán khoảng 26.000 đồng.\r\n\r\nMì ăn liền Mini Handy Hảo Hảo vị tôm chua cay ly 47g hương vị thơm ngon, dạng ly tiện dụng, cung cấp đến 1/3 nhu cầu canxi mỗi ngày cho người trưởng thành. Giá bán khoảng 7.600 đồng.\r\n\r\nMì tôm A-One gói 85g: mì vàng dai, thơm ngon nước súp đậm đà hấp dẫn, cung cấp năng lượng cho cơ thể. Giá bán khoảng 6.000 đồng\r\n\r\nMì ăn liền Kang Shi Fu vị gà nấm hương hộp 104g; dạng hộp tiện lợi, hương vị thơm ngon hấp dẫn. Giá bán khoảng 26.000 đồng.\r\n\r\n\r\nCác món ăn ngon với mì ăn liền\r\n​​Nếu đã nhàm chán với cách ăn mì truyền thống thì bạn cũng có thể thỏa thích sáng tạo, biến tấu thành những món ngon, hấp dẫn, lạ miệng lại đơn giản và dễ làm như làm pizza từ mì ăn liền, làm mì trứng chiên giòn, hamburger mì gói, mì gói que,...\r\n\r\nCách bảo quản, lưu ý khi sử dụng mì ăn liền\r\n\r\nKhi bảo quản tại nhà hoặc tại nơi văn làm việc bạn cũng nên chú ý đến nơi cất giữ sản phẩm và điều kiện môi trường xung quanh. Để nơi khô thoáng, tránh ẩm thấp, tránh ánh nắng trực tiếp, tránh sự tấn công của chuột, kiến hay gián, để sản phẩm cách xa các sản phẩm có mùi, đặc biệt là các loại hóa mỹ phẩm như xà phòng, nước rửa chén, nước xả quần áo,…\r\n\r\nLuôn kiểm tra hạn sử dụng và tình trạng bao bì trước khi sử dụng.\r\n\r\n\r\nCác câu hỏi thường gặp đối với mì ăn liền\r\nMua mì ăn liền giá bao nhiêu tiền?\r\nMì ăn liền là loại sản phẩm ăn liền ngon, tiện lợi và tiết kiệm, với giá bán tốt và nhiều chủng loại. Giá mì ăn liền trên thị trường giao động từ 3.000 - 15.000 đồng, các loại mì nhập khẩu Hàn Quốc, Nhật Bản,...thì có giá cao hơn khoảng từ 15.000 - 30.000 đồng.\r\n\r\nMì ăn liền mua chính hãng ở đâu giá tốt nhất?\r\nNếu bạn đang băn khoăn tìm một địa chỉ mua hàng uy tín, có nhiều sự lựa chọn, đa dạng các chủng loại, hương vị mì ăn liền, chất lượng, thơm ngon thì đừng chần chừ mà hãy lựa chọn ngay Bách Hóa XANH có phân phối tất cả các thể loại mì ăn liền chiều chuộng nhu cầu của tất cả khách hàng, luôn cam kết chính hãng, thơm ngon và an toàn. Ngoài ra bạn cũng có thể đổi khẩu vị cho gia đình với các sản phẩm ăn liền khác tại Bách Hóa XANH như hủ tiếu, miến, bánh canh ăn liền, phở bún ăn liền, cháo gói, cháo tươi,...\r\n\r\nMì ăn liền có hại cho sức khỏe không?\r\nMặc dù mang hương vị thơm ngon, nhanh chóng, tiện lợi, song nếu quá lạm dụng, sử dụng với tần suất không hợp lý thì có thể gây ra một số ảnh hưởng cho cơ thể. Bạn cần cung cấp thêm các chất từ những thực phẩm khác. Vì thế, bạn nên ăn mì với tuần suất vừa phải, khoảng 2 lần/tuần, khi nhàm chán cũng có thể dùng mì kết hợp với những thực phẩm dinh dưỡng hơn, để bảo vệ sức khoẻ cho bản thân nhé.\r\n\r\nCác thông tin liên quan tới mì ăn liền\r\n\r\nTổng hợp 6 loại mì gói có thịt thật cực ngon mà bạn không thể bỏ qua\r\n\r\nNấu mì ăn như thế nào là đúng cách?\r\n\r\nCác loại mì tôm được ưa chuộng nhất Việt Nam\r\n\r\nĂn mì tôm có tốt không?', 1),
(10, 'Đồ hộp', 'do-hop', 'Cá hộp là gì?\r\n\r\nCá hộp là một trong những thực phẩm đóng hộp tiện lợi, thơm ngon. Cá hộp sau khi chế biến, không bị tanh, gia vị thấm đậm đà, ngon miệng. Các sản phẩm từ cá hộp rất đa dạng, được chế biến từ nhiều loại cá khác nhau, mang đến sự lựa chọn đa dạng cho người dùng, cá hộp là loại cá được chế biến sẵn, tẩm ướp gia vị và nấu chín lên, sau đó được bảo quản trong những hộp thiếc, từ đó giúp giữ được hương vị thơm ngon cũng như bảo quản được lâu hơn.\r\n\r\nThành phần dinh dưỡng của cá hộp\r\n\r\nTrong cá hộp chứa nhiều protein, ngoài ra còn chứa nhiều vitamin D, vitamin B12, vitamin E, vitamin K và các khoáng chất khác như sắt, kẽm, kali,...tốt cho cơ thể. \r\n\r\nCông dụng của cá hộp\r\n \r\n\r\nDễ sử dụng, chỉ cần mở nắp hộp là có thể thưởng thức, hoặc chế biến thành những món yêu thích.\r\n\r\nTiện lợi, có thể mang theo bất kì đâu cho những buổi đi chơi cả gia đình.\r\n\r\nXương trong cá hộp thường được nấu mềm, giúp bổ sung lượng canxi cho cơ thể. \r\n\r\nBảo quản được rất lâu, giá trị dinh dưỡng của đồ hộp không giảm theo thời gian như các loại hàng tươi sống.\r\n\r\n\r\nCác loại cá hộp\r\n\r\nCá hộp đang có bán tại Bách Hóa XANH được nhiều người ưa chuộng hiện nay như cá Sardines đóng hộp\r\n cá mòi đóng hộp, cá ngừ đóng hộp, cá nục đóng hộp, cá thu đóng hộp, cá trích đóng hộp, cá sapa sốt cà,....\r\n\r\nCác thương hiệu cá hộp nổi tiếng hiện nay\r\n\r\nNhững thương hiệu cá hộp được ưa chuộng hiện nay có thể kể đến như cá hộp Tuna Việt Nam, cá hộp 3 Cô Gái, cá hộp Sea Crown, cá hộp Ligo, cá hộp Vissan,...\r\n\r\nCác sản phẩm cá hộp được ưa chuộng nhất hiện nay\r\n\r\nCác sản phẩm cá hộp thơm ngon, chất lượng với mức giá hợp lý được ưa chuộng trên thị trường đang được bày bán tại Bách Hóa XANH:\r\n\r\nCá trích sốt cà Lilly hộp 155g: là cá trích được sốt cà thơm ngon, đậm đà, cực kỳ chất lượng và hấp dẫn.\r\n\r\nCá nục sốt cà nắp giật Lilly hộp 155g: cá nục tươi ngon, được đóng hộp, sốt cà với gia vị đậm đà, thơm ngon hấp dẫn\r\n\r\nCá kho thịt 3 Bông mai Vissan hộp 150g: cá tươi ngon, kho cùng thịt, hương vị hấp dẫn, đặc biệt, tiện lợi, dễ dàng chế biến giúp tiết kiệm thời gian của người dùng.\r\n\r\nCá nục sốt ớt chua ngọt Sea Crown hộp 155g: cá nục được sốt ớt với hương vị cay the thơm ngon, hấp dẫn, tiện lợi và tiết kiệm thời gian.\r\n\r\nCá nục sốt cà vị cay Sea Crown hộp 155g: cá nục được sốt cà truyền thống có vị the cay bắt vị, hấp dẫn, được nhiều người tin dùng.\r\n\r\n\r\nCác món ăn ngon với cá hộp\r\n\r\nCá hộp sốt cà chua, bún cá hộp, cá hộp kho trứng, cá hộp kho sườn heo, cà tím nấu cá hộp, gà nấu cá hộp, cá hộp xào bầu, cà đắng nấu cá hộp,...là một trong những món ăn ngon, phổ biến được chế biến từ cá hộp các loại.\r\n\r\nCách bảo quản, lưu ý khi sử dụng cá hộp\r\n\r\nCá hộp có thể được sử dụng trực tiếp với cơm, bánh mì hoặc cũng có thể thành nhiều món ăn thơm ngon, hấp dẫn.\r\n\r\nBảo quản cá hộp nơi khô ráo, thoáng mát, tránh ánh nắng trực tiếp của mặt trời.\r\n\r\nNếu sử dụng không hết, nên bảo quản lạnh để tránh hư hỏng.\r\n\r\n\r\nCác câu hỏi thường gặp đối với cá hộp\r\n\r\nMua cá hộp giá bao nhiêu tiền?\r\n\r\nCác loại cá hộp tại Bách Hóa XANH đang bán có mức giá rất phải chăng, giao động chỉ khoảng từ 11.000đ đến 75.000đ là bạn đã có ngay một món cá hộp thơm ngon, tiện lợi.\r\n\r\nCá hộp mua chính hãng ở đâu giá tốt nhất?\r\n\r\nNếu bạn đang muốn tìm nơi cung cấp cá hộp chất lượng, đa dạng chủng loại, hãy ghé ngay Bách hóa XANH, với đầy đủ các thương hiệu nổi tiếng và loại cá mà bạn có thể chọn lựa, cùng với cam kết bán hàng đầy đủ nguồn gốc xuất xứ, cùng nhiều chương trình khuyến mãi hấp dẫn cho khách mua hàng tại Bách Hóa XANH.\r\n\r\nCá hộp có bao nhiêu calo?\r\n\r\nTrong 100g cá hộp có chứa khoảng 70 đến 300 calo. Tùy vào những loại cá khác nhau và những gia vị bên trong, mà lượng calo trong mỗi hộp cá cũng khác nhau.\r\n\r\nCác thông tin liên quan tới cá hộp\r\n \r\n\r\nTìm hiểu về cá hộp và các thương hiệu cá hộp ngon trên thị trường\r\n\r\nTop 5 loại cá hộp thương hiệu Việt Nam ngon bổ rẻ\r\n\r\nTổng hợp 8 món ăn chế biến từ cá hộp dễ làm nhưng siêu \'bắt cơm\'', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `account_id`, `full_name`, `phone_number`, `address`) VALUES
(1, NULL, 'Võ Văn TỶ', '0123654789', 'Số 252, Phường Long Xuyên, An Giang'),
(2, NULL, 'Võ Minh Anh', '0123654788', 'Ấp Hòa Bình 3, Phú Tân, An Giang'),
(3, NULL, 'Nguyễn Văn A', '0123654789', 'An Giang');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_money` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `total_money`, `payment_method`, `status`) VALUES
(1, 1, '2026-03-12 00:52:31', 2596000, 'Tiền mặt', 3),
(2, 2, '2026-03-13 22:29:15', 460000, 'Tiền mặt', 0),
(3, 3, '2026-03-18 16:42:54', 729000, 'Tiền mặt', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`order_id`, `product_id`, `price`, `quantity`) VALUES
(1, 2, 50000, 2),
(1, 3, 199000, 4),
(1, 9, 200000, 2),
(1, 10, 200000, 2),
(1, 11, 300000, 3),
(2, 12, 115000, 4),
(3, 3, 199000, 1),
(3, 8, 30000, 1),
(3, 9, 200000, 2),
(3, 10, 100000, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `discount_price` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `sub_category_id`, `brand_id`, `name`, `price`, `discount_price`, `stock_quantity`, `description`, `cover_image`, `status`) VALUES
(1, 8, NULL, 5, 'Tinh chất dưỡng da cao cấp', 350000, 280000, 100, NULL, 'uploads/1773244738_serum.webp', 1),
(2, 3, NULL, 3, 'Rượu soju Heejin vị việt quất 12% chai 360ml', 65000, 50000, 49, NULL, 'uploads/1773244721_237746_202411261322540446.jpg', 1),
(3, 6, 5, 2, 'Thùng 24 lon Mirinda Cam 330ml', 250000, 199000, 200, NULL, 'uploads/1773244683_mirinda-cam-330ml-th24-sleek-lon_202602231040140594.jpg', 1),
(4, 2, NULL, 1, 'Bánh gạo nướng vị tảo biển Orion', 45000, 35000, 150, NULL, 'uploads/1773244702_banh-gao-nuong-vi-tao-bien-orion-an-goi-1113g_202504091006528394.jpg', 1),
(5, 1, NULL, 4, 'Bắp Cải Đà Lạt Chuẩn VietGAP', 25000, NULL, 50, NULL, 'uploads/1773244757_cai-be-xanh_202601141630158498.jpg', 1),
(6, 4, NULL, 4, 'Thùng 48 hợp sửa tươi ít đường Dutch Lady', 170000, NULL, 80, NULL, 'uploads/1773244789_thung-48-hop-sua-tuoi-tiet-trung-it-duong-dutch-lady-180ml_202504231354344799.jpg', 1),
(7, 5, NULL, 4, 'Thịt bò Úc nhập khẩu 500g', 180000, 90000, 30, NULL, 'uploads/1773244839_bap-bo-1kg_202511041111594354.jpg', 1),
(8, 7, NULL, 5, 'Nước lau sàn diệt khuẩn 1L', 45000, 30000, 120, NULL, 'uploads/1773244666_frame-3476173_202503191352406465.png', 1),
(9, 6, 5, 2, 'Thùng 24 lon Mirinda Green Soda 330ml', 200000, NULL, 100, NULL, 'uploads/1773245701_mirinda-cream-soda-330ml-sleek-lon-th24_202602231030330371.jpg', 1),
(10, 6, 5, 2, 'Thùng 24 lon Mirinda Xá Xị 330ml', 200000, 100000, 200, NULL, 'uploads/1773245746_mirinda-xa-xi-sleek-lon-330ml-th24_202602231047401947.jpg', 1),
(11, 6, 5, 2, 'Thùng 24 lon nước ngọt Pepsi không calo', 300000, 200000, 200, NULL, 'uploads/1773245795_thung-24-lon-nuoc-ngot-pepsi-khong-calo-330ml_202601231358143569.jpg', 1),
(12, 9, 1, 5, 'Thùng 30 gói mì Kokomi 90 tôm chua cay 90g', 128000, 115000, 68, NULL, 'uploads/1773415508_thung-30-goi-mi-kokomi-90-tom-chua-cay-90g_202504011326552988.jpg', 1),
(13, 9, 1, 5, 'Thùng 30 gói mì chay Vifon lẩu Thái 65g', 120000, 77000, 100, NULL, 'uploads/1773415585_thung-30-goi-mi-chay-vifon-lau-thai-65g-202306101107333616.png', 1),
(14, 9, 1, 5, 'Thùng 30 gói mì Hảo Hảo chay rau nấm 74g', 96000, 20000, 50, NULL, 'uploads/1773415655_thung-30-goi-mi-3-mien-tom-chua-cay-65g-202402280906169619.jpg', 1),
(15, 9, 1, 4, '2 gói mì hải sản SiuKay 128g và 2 gói mì bò Siukay 128g', 52800, 50000, 65, NULL, 'uploads/1773415955_2-goi-mi-hai-san-siukay-128g-va-2-goi-mi-bo-siukay-128g_202603041348415225.jpg', 1),
(16, 9, 1, 4, '2 hộp mì trộn Cung Đình Kool vị sườn nướng tô 99g', 31200, 25000, 50, NULL, 'uploads/1773828737_2-goi-mi-tron-cung-dinh-kool-vi-suon-nuong-to-99g_202603041330473454.jpg', 1),
(17, 9, 2, 1, 'Thùng 24 gói miến Phú Hương thịt bằm 55g', 256000, NULL, 50, NULL, 'uploads/1773840365_thung-24-goi-mien-phu-huong-thit-bam-55g-202306171515401680.jpg', 1),
(18, 9, 2, 3, 'Thùng 24 gói miến Phú Hương sườn heo 55g', 256000, NULL, 25, NULL, 'uploads/1773840979_thung-24-goi-mien-phu-huong-suon-heo-55g-202309141037099051.jpg', 1),
(19, 4, 3, 3, 'Thùng 48 hộp sữa tươi tiệt trùng ít đường TH true MILK 180ml', 430000, 420000, 100, NULL, 'uploads/1773841337_sua-th-true-milk.jpg', 1),
(20, 4, 3, 2, 'Thùng 12 hộp sữa tươi tiệt trùng không đường Vinamilk Sữa tươi 100% 1 lít', 395000, 390000, 65, NULL, 'uploads/1773841518_sttt-vinamilk-dan-bo-kd-1l-thung_202509260919032233.jpg', 1),
(21, 4, 4, 4, 'Thùng 48 hộp Sữa lúa mạch vị socola Ovaltine bổ sung canxi 180ml', 369000, 325000, 100, NULL, 'uploads/1773841837_sua-vatilinjpg.jpg', 1),
(22, 6, 6, 3, 'Thùng 48 hộp Sữa trái cây Kun hương cam có thạch 170ml', 380000, 330000, 200000, NULL, 'uploads/1773843107_sua-trai-cay.jpg', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Customer');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `image`) VALUES
(1, 9, 'Mỳ ăn liền', 'uploads/1773840104_sub_mi-an-lien_202511010301464949.png'),
(2, 9, 'Miến - Hủ tiếu', 'uploads/1773840525_sub_hutieu.png'),
(3, 4, 'Sửa Tươi', 'uploads/1773841407_sub_frame-sua-tuoi.png'),
(4, 4, 'Sửa Cacao', 'uploads/1773841871_sub_frame-sua-cacao.png'),
(5, 6, 'Nước ngọt', 'uploads/1773842881_sub_frame-nuocngotpng.png'),
(6, 6, 'Sửa trái cây', 'uploads/1773843014_sub_sua-trai-cay_202508291623515799.png');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
