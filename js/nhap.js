// =====================================================================
// nhap.js — Phòng luyện thi | Lịch sử 12  (UPDATED)
// =====================================================================

// ── DỮ LIỆU BÀI LUYỆN TẬP 10 CÂU / BÀI (giữ nguyên cho game) ──────
const testLesson6 = [
    ["Hội nghị lần thứ 8 BCH Trung ương Đảng (5/1941) đã xác định nhiệm vụ số 1 là:", ["Cải cách ruộng đất", "Giải phóng dân tộc", "Đấu tranh kinh tế", "Xây dựng văn hóa"], 1, "Hội nghị xác định giải phóng dân tộc là nhiệm vụ cấp bách nhất."],
    ["Mặt trận Việt Minh được thành lập vào thời gian nào?", ["19/05/1941", "22/12/1944", "15/08/1945", "02/09/1945"], 0, "Việt Minh là nơi đoàn kết mọi tầng lớp yêu nước từ năm 1941."],
    ["Chỉ thị 'Nhật - Pháp bắn nhau và hành động của chúng ta' xác định kẻ thù duy nhất là:", ["Thực dân Pháp", "Phát xít Nhật", "Đế quốc Mỹ", "Thực dân Anh"], 1, "Sau ngày 9/3/1945, Nhật đảo chính Pháp nên Nhật là kẻ thù chính."],
    ["Bốn tỉnh giành chính quyền sớm nhất (18/8/1945) gồm:", ["Bắc Giang, Hải Dương, Hà Tĩnh, Quảng Nam", "Hà Nội, Huế, Sài Gòn, Hải Phòng", "Thái Nguyên, Tuyên Quang, Cao Bằng, Lạng Sơn", "Cần Thơ, Tiền Giang, Đồng Tháp, Mỹ Tho"], 0, "Đây là những địa phương nổ súng giành thắng lợi sớm nhất cả nước."],
    ["Ý nghĩa lớn nhất của Cách mạng tháng Tám 1945 đối với dân tộc là:", ["Lật đổ chế độ phong kiến", "Mở ra kỷ nguyên độc lập, tự do", "Đánh bại thực dân Pháp", "Cổ vũ phong trào thế giới"], 1, "Đưa nhân dân từ thân phận nô lệ thành người làm chủ đất nước."],
    ["Thời cơ 'ngàn năm có một' kết thúc khi nào?", ["Khi Nhật đầu hàng", "Khi quân Đồng minh vào giải giáp quân Nhật", "Khi Bác Hồ về nước", "Khi thành lập quân đội"], 1, "Ta phải giành chính quyền trước khi quân Đồng minh vào."],
    ["Nghệ thuật quân sự đặc sắc của Cách mạng tháng Tám là:", ["Đấu tranh ngoại giao", "Kết hợp khởi nghĩa nông thôn và thành thị", "Chỉ đánh ở thành phố", "Chỉ dùng đấu tranh vũ trang"], 1, "Sự phối hợp nhịp nhàng giữa hai khu vực giúp thắng lợi nhanh chóng."],
    ["Tính chất của Cách mạng tháng Tám 1945 ở Việt Nam là:", ["Cách mạng tư sản", "Cách mạng xã hội chủ nghĩa", "Cách mạng giải phóng dân tộc", "Cách mạng văn hóa"], 2, "Nhiệm vụ hàng đầu là đánh đuổi ngoại xâm, giành độc lập."]
];

const testLesson7 = [
    ["Sự kiện mở đầu cuộc xâm lược Việt Nam lần thứ hai của Pháp (1945) diễn ra tại:", ["Hà Nội", "Đà Nẵng", "Sài Gòn", "Huế"], 2, "Đêm 22 rạng 23/9/1945, Pháp đánh úp trụ sở UBND Nam Bộ."],
    ["Chiến dịch nào đã làm phá sản chiến lược 'Đánh nhanh thắng nhanh' của Pháp?", ["Biên giới 1950", "Việt Bắc 1947", "Điện Biên Phủ 1954", "Hòa Bình 1952"], 1, "Chiến thắng Việt Bắc buộc Pháp phải chuyển sang đánh lâu dài."],
    ["Trong chiến dịch Biên giới 1950, ta chọn cứ điểm nào làm điểm đột phá mở màn?", ["Cao Bằng", "Đông Khê", "Thất Khê", "Lạng Sơn"], 1, "Đánh Đông Khê để chia cắt hệ thống phòng ngự của Pháp trên Đường số 4."],
    ["Đại hội II của Đảng (1951) quyết định đổi tên Đảng thành:", ["Đảng Cộng sản Việt Nam", "Đảng Lao động Việt Nam", "Mặt trận Việt Minh", "Đảng Dân chủ"], 1, "Đảng ra hoạt động công khai với tên Đảng Lao động Việt Nam."],
    ["Kế hoạch quân sự cuối cùng của Pháp có sự giúp đỡ của Mỹ tại Việt Nam là:", ["Kế hoạch Rơ-ve", "Kế hoạch Na-va", "Kế hoạch Đờ Lát", "Kế hoạch Bô-la-e"], 1, "Kế hoạch Na-va nhằm kết thúc chiến tranh trong danh dự."],
    ["Chiến thắng Điện Biên Phủ (1954) đã đập tan kế hoạch quân sự nào?", ["Kế hoạch Na-va", "Kế hoạch Rơ-ve", "Kế hoạch Đờ Lát", "Chiến tranh đặc biệt"], 0, "Thắng lợi này quyết định việc ký kết Hiệp định Giơ-ne-vơ."],
    ["Ý nghĩa quan trọng nhất của Hiệp định Giơ-ne-vơ 1954 là:", ["Pháp rút quân ngay", "Quốc tế công nhận độc lập, chủ quyền của Việt Nam", "Chia cắt đất nước", "Mỹ không can thiệp"], 1, "Lần đầu tiên các quyền dân tộc cơ bản của ta được pháp lý quốc tế thừa nhận."],
    ["Nguyên nhân quyết định thắng lợi của kháng chiến chống Pháp là:", ["Viện trợ quốc tế", "Địa hình rừng núi", "Sự lãnh đạo đúng đắn của Đảng", "Pháp tự suy yếu"], 2, "Đường lối kháng chiến toàn dân, toàn diện là nhân tố hàng đầu."]
];

const testLesson8 = [
    ["Phong trào 'Đồng khởi' (1959-1960) nổ ra mạnh mẽ nhất ở tỉnh nào?", ["Bến Tre", "Quảng Trị", "Tây Ninh", "Bình Định"], 0, "Bến Tre là quê hương đồng khởi, nơi khai sinh Đội quân tóc dài."],
    ["Chiến lược 'Chiến tranh đặc biệt' (1961-1965) của Mỹ dựa trên lực lượng nào?", ["Quân Mỹ trực tiếp", "Quân đội Sài Gòn và cố vấn Mỹ", "Quân đồng minh", "Quân Pháp"], 1, "Dùng người Việt đánh người Việt dưới sự chỉ huy của Mỹ."],
    ["Chiến thắng nào chứng minh ta có khả năng đánh thắng quân Mỹ trực tiếp (1965)?", ["Ấp Bắc", "Vạn Tường", "Pleime", "Khe Sanh"], 1, "Vạn Tường là trận đầu thắng Mỹ, ví như 'Ấp Bắc' đối với quân Mỹ."],
    ["Sự kiện nào buộc Mỹ phải tuyên bố 'Phi Mỹ hóa' chiến tranh (1968)?", ["Chiến dịch Đường 9", "Tổng tiến công Xuân Mậu Thân", "Trận Điện Biên Phủ trên không", "Hiệp định Paris"], 1, "Mậu Thân 1968 làm lung lay ý chí xâm lược của giới cầm quyền Mỹ."],
    ["Chiến thắng 'Điện Biên Phủ trên không' cuối năm 1972 diễn ra ở đâu?", ["Sài Gòn", "Hà Nội, Hải Phòng", "Huế, Đà Nẵng", "Tây Nguyên"], 1, "Đập tan cuộc tập kích chiến lược bằng B52 của Mỹ vào miền Bắc."],
    ["Chiến dịch mở màn cuộc Tổng tiến công và nổi dậy Xuân 1975 là:", ["Chiến dịch Hồ Chí Minh", "Chiến dịch Tây Nguyên", "Chiến dịch Huế - Đà Nẵng", "Chiến dịch Trị Thiên"], 1, "Bắt đầu bằng trận Buôn Ma Thuột, tạo bước ngoặt chuyển sang tổng tiến công."],
    ["Chiến dịch quyết chiến chiến lược cuối cùng giải phóng miền Nam là:", ["Chiến dịch Tây Nguyên", "Chiến dịch Hồ Chí Minh", "Chiến dịch Biên giới", "Chiến dịch Việt Bắc"], 1, "Diễn ra từ 26/4 đến 30/4/1975, kết thúc hoàn toàn cuộc kháng chiến."],
    ["Ngày 30/4/1975 ghi dấu mốc lịch sử vĩ đại nào?", ["Ký hiệp định Paris", "Mỹ rút quân", "Giải phóng hoàn toàn miền Nam, thống nhất đất nước", "Thành lập Đảng"], 2, "Kết thúc 21 năm kháng chiến chống Mỹ và 30 năm chiến tranh giải phóng."]
];

// ── DỮ LIỆU 40 CÂU / BÀI (dùng cho cả luyện tập 40 câu + đề kiểm tra) ──
const testLesson6 = [
    ["Hội nghị nào đã quyết định phát động Tổng khởi nghĩa giành chính quyền trong cả nước?", ["Hội nghị Trung ương 8 (5/1941)", "Hội nghị toàn quốc của Đảng tại Tân Trào (14-15/8/1945)", "Đại hội Quốc dân tại Tân Trào (16-17/8/1945)", "Hội nghị Quân sự cách mạng Bắc Kỳ (4/1945)"], 1, "Hội nghị toàn quốc của Đảng (14-15/8) là cơ quan đưa ra quyết định tối cao về việc Tổng khởi nghĩa."],
    ["Quân lệnh số 1 của Ủy ban khởi nghĩa toàn quốc được ban bố vào thời điểm nào?", ["Đêm 13/8/1945.", "Sáng 14/8/1945.", "Chiều 15/8/1945.", "Đêm 16/8/1945."], 0, "Ngay khi nhận được tin Nhật sắp đầu hàng, Ủy ban khởi nghĩa đã ban bố Quân lệnh số 1."],
    ["Bốn tỉnh giành được chính quyền sớm nhất trong cả nước (18/8/1945) là:", ["Hà Nội, Huế, Sài Gòn, Thái Nguyên.", "Bắc Giang, Hải Dương, Hà Tĩnh, Hà Nam.", "Bắc Giang, Hải Dương, Hà Tĩnh, Quảng Nam.", "Quảng Ninh, Hải Phòng, Hà Tĩnh, Quảng Nam."], 2, "Đây là kiến thức ghi nhớ thường xuyên xuất hiện trong đề thi."],
    ["Ai là người chỉ huy đơn vị Giải phóng quân tiến về giải phóng thị xã Thái Nguyên (16/8/1945)?", ["Hồ Chí Minh.", "Võ Nguyên Giáp.", "Phạm Văn Đồng.", "Trường Chinh."], 1, "Võ Nguyên Giáp trực tiếp chỉ huy một đơn vị từ Tân Trào tiến về giải phóng Thái Nguyên."],
    ["Đại hội Quốc dân tại Tân Trào (16-17/8/1945) đã bầu ra cơ quan nào?", ["Ủy ban khởi nghĩa toàn quốc.", "Chính phủ lâm thời nước Việt Nam.", "Ủy ban Dân tộc giải phóng Việt Nam.", "Ban chấp hành Trung ương Đảng."], 2, "Ủy ban Dân tộc giải phóng do Hồ Chí Minh làm Chủ tịch, sau này cải tổ thành Chính phủ lâm thời."],
    ["Ngày 19/8/1945 đánh dấu thắng lợi của cuộc khởi nghĩa tại địa phương nào?", ["Hà Nội.", "Huế.", "Sài Gòn.", "Bắc Giang."], 0, "Hà Nội giành chính quyền vào ngày 19/8 sau cuộc mít tinh tại Nhà hát Lớn."],
    ["Vị vua cuối cùng của chế độ phong kiến Việt Nam thoái vị vào ngày 30/8/1945 là ai?", ["Khải Định.", "Duy Tân.", "Bảo Đại.", "Hàm Nghi."], 2, "Vua Bảo Đại trao ấn kiếm cho phái đoàn Chính phủ tại Huế, chấm dứt chế độ phong kiến."],
    ["Chủ tịch Hồ Chí Minh đọc bản Tuyên ngôn Độc lập tại đâu?", ["Quảng trường Nhà hát Lớn Hà Nội.", "Quảng trường Ba Đình (Hà Nội).", "Đình Tân Trào (Tuyên Quang).", "Phủ Khâm sai (Hà Nội)."], 1, "Sự kiện diễn ra ngày 2/9/1945 tại Quảng trường Ba Đình lịch sử."],
    ["Nội dung nào sau đây là nhân tố khách quan dẫn đến thắng lợi của Cách mạng tháng Tám?", ["Sự lãnh đạo tài tình của Đảng Cộng sản.", "Khối liên minh công - nông vững chắc.", "Nhật Bản đầu hàng Đồng minh không điều kiện.", "Truyền thống yêu nước của dân tộc."], 2, "Nhân tố khách quan là tác động từ bên ngoài (Chiến tranh thế giới II kết thúc)."],
    ["Cách mạng tháng Tám 1945 đã lật đổ ách thống trị của thực dân Pháp trong khoảng bao nhiêu năm?", ["Hơn 60 năm.", "Hơn 80 năm.", "Gần 100 năm.", "50 năm."], 1, "Tính từ khi Pháp nổ súng tại Đà Nẵng (1858) đến 1945 là gần 87 năm."],
    ["Văn kiện nào đã xác định 10 chính sách lớn của Việt Minh?", ["Quân lệnh số 1.", "Nghị quyết Đại hội Quốc dân Tân Trào.", "Tuyên ngôn Độc lập.", "Chỉ thị Nhật - Pháp bắn nhau và hành động của chúng ta."], 1, "Đại hội Quốc dân thông qua 10 chính sách của Việt Minh."],
    ["Cuộc khởi nghĩa tại Sài Gòn giành thắng lợi vào ngày nào?", ["19/8/1945.", "23/8/1945.", "25/8/1945.", "28/8/1945."], 2, "Sài Gòn là một trong 3 trung tâm lớn nhất, giành chính quyền ngày 25/8."],
    ["Lực lượng nào giữ vai trò nòng cốt trong cuộc Tổng khởi nghĩa tháng Tám?", ["Lực lượng vũ trang.", "Lực lượng chính trị (quần chúng).", "Lực lượng quân Đồng minh.", "Lực lượng tư sản dân tộc."], 1, "Cách mạng tháng Tám là cuộc khởi nghĩa của toàn dân."],
    ["Hai địa phương cuối cùng giành được chính quyền trong cả nước (28/8/1945) là:", ["Hà Tiên và Cà Mau.", "Đồng Nai Thượng và Hà Tiên.", "Đồng Nai và Long An.", "Châu Đốc và Hà Tiên."], 3, "Theo thông tin SGK trang 33."],
    ["Ý nghĩa quan trọng nhất của Cách mạng tháng Tám đối với dân tộc Việt Nam là gì?", ["Mở ra kỷ nguyên độc lập, tự do cho dân tộc.", "Làm tan rã hệ thống thuộc địa của chủ nghĩa thực dân.", "Cổ vũ các dân tộc thuộc địa đứng lên đấu tranh.", "Thành lập được khối liên minh công nông."], 0, "Đây là bước ngoặt vĩ đại nhất, đưa dân ta từ nô lệ thành người làm chủ."],
    ["Kẻ thù trực tiếp của nhân dân Đông Dương trong giai đoạn từ tháng 3/1945 đến tháng 8/1945 là:", ["Thực dân Pháp.", "Phát xít Nhật.", "Đế quốc Mỹ.", "Thực dân Anh."], 1, "Sau ngày 9/3/1945, Nhật đảo chính Pháp, Nhật trở thành kẻ thù duy nhất."],
    ["Tại sao nói thời cơ trong Cách mạng tháng Tám là thời cơ khách quan thuận lợi?", ["Vì lực lượng vũ trang của ta đã lớn mạnh.", "Vì nhân dân đã sẵn sàng hy sinh.", "Vì quân Nhật ở Đông Dương rệu rã, Chính phủ Trần Trọng Kim hoang mang.", "Vì quân Đồng minh đã vào Đông Dương giúp đỡ ta."], 2, "Khách quan là những điều kiện thuận lợi đến từ sự sụp đổ của kẻ thù."],
    ["Điểm độc đáo trong việc sử dụng lực lượng của Cách mạng tháng Tám là gì?", ["Chỉ sử dụng lực lượng vũ trang tập trung.", "Chỉ sử dụng lực lượng chính trị quần chúng.", "Kết hợp giữa lực lượng chính trị và lực lượng vũ trang, trong đó lực lượng chính trị đóng vai trò quyết định.", "Sử dụng lực lượng quân đội Đồng minh làm nòng cốt."], 2, "Đây là nghệ thuật tổ chức lực lượng tài tình của Đảng."],
    ["Tính chất của Cách mạng tháng Tám 1945 ở Việt Nam là:", ["Cách mạng dân chủ tư sản kiểu cũ.", "Cách mạng xã hội chủ nghĩa.", "Cách mạng giải phóng dân tộc mang tính nhân dân sâu sắc.", "Cách mạng tư sản dân quyền."], 2, "Nhiệm vụ hàng đầu là giải phóng dân tộc."],
    ["Vì sao Cách mạng tháng Tám giành thắng lợi nhanh chóng và ít đổ máu?", ["Vì Nhật đã tự nguyện trao trả độc lập.", "Vì quân Đồng minh đánh đuổi Nhật giúp ta.", "Vì ta đã chuẩn bị chu đáo và chớp đúng thời cơ khi kẻ thù đã hoàn toàn gục ngã.", "Vì Pháp đã rút quân về nước từ trước."], 2, "Sự kết hợp giữa chuẩn bị lâu dài và nghệ thuật chớp thời cơ."],
    ["'Chớp thời cơ' trong Cách mạng tháng Tám được hiểu là hành động trong khoảng thời gian nào?", ["Từ khi Nhật đảo chính Pháp đến khi Nhật đầu hàng.", "Từ sau khi Nhật đầu hàng đến trước khi quân Đồng minh vào Đông Dương.", "Ngay sau khi Chiến tranh thế giới thứ hai bùng nổ.", "Sau khi quân Đồng minh đã vào giải giáp quân Nhật."], 1, "Khoảng vàng từ 15/8 đến đầu tháng 9 là lúc có khoảng trống quyền lực."],
    ["Vai trò của Mặt trận Việt Minh trong thắng lợi của Cách mạng tháng Tám là:", ["Là lực lượng trực tiếp cầm súng chiến đấu ở mọi mặt trận.", "Là nơi tập hợp và đoàn kết mọi tầng lớp nhân dân yêu nước.", "Là cơ quan lãnh đạo cao nhất của Đảng Cộng sản.", "Là đơn vị chỉ huy quân đội giải phóng."], 1, "Việt Minh là hình thức mặt trận dân tộc thống nhất rộng rãi nhất."],
    ["Sự kiện ngày 30/8/1945 tại Huế có ý nghĩa gì đối với lịch sử dân tộc?", ["Đánh dấu sự thành lập của Nhà nước mới.", "Đánh dấu sự sụp đổ hoàn toàn của chế độ phong kiến Việt Nam.", "Đuổi hoàn toàn thực dân Pháp ra khỏi bờ cõi.", "Thành lập Chính phủ liên hiệp lâm thời."], 1, "Vua Bảo Đại thoái vị chấm dứt hơn 1000 năm phong kiến."],
    ["Một trong những nguyên nhân chủ quan quan trọng nhất dẫn đến thắng lợi là:", ["Sự lãnh đạo đúng đắn, sáng tạo của Đảng và Chủ tịch Hồ Chí Minh.", "Nhật Bản bị Hồng quân Liên Xô đánh bại.", "Sự giúp đỡ của các nước xã hội chủ nghĩa.", "Địa hình đất nước có nhiều vùng căn cứ thuận lợi."], 0, "Lãnh đạo là nhân tố quyết định mọi thắng lợi."],
    ["Cách mạng tháng Tám 1945 đã 'chọc thủng khâu yếu nhất' của chủ nghĩa thực dân vì:", ["Việt Nam là nước có nền kinh tế yếu nhất.", "Lần đầu tiên một dân tộc thuộc địa nhỏ bé đã lật đổ được ách thống trị của thực dân, phát xít.", "Việt Nam nằm ở vị trí chiến lược của Đông Nam Á.", "Quân đội Nhật ở Việt Nam là yếu nhất."], 1, "Đây là ý nghĩa quốc tế to lớn của cuộc cách mạng."],
    ["Bài học kinh nghiệm về 'Khối đại đoàn kết dân tộc' được thể hiện rõ nhất qua:", ["Việc thành lập các đội tự vệ chiến đấu.", "Việc tập hợp quần chúng trong Mặt trận Việt Minh.", "Việc tổ chức các cuộc mít tinh tại Hà Nội.", "Việc soạn thảo bản Tuyên ngôn Độc lập."], 1, "Mặt trận Việt Minh là biểu tượng cho sức mạnh đoàn kết toàn dân."],
    ["Tính chất dân chủ của Cách mạng tháng Tám được thể hiện ở điểm nào?", ["Lật đổ được thực dân Pháp.", "Thiết lập nền dân chủ cộng hòa, nhân dân làm chủ.", "Đuổi được phát xít Nhật.", "Giành được quyền độc lập cho dân tộc."], 1, "'Dân chủ' nghĩa là quyền lực thuộc về nhân dân."],
    ["Nội dung nào sau đây phản ánh đúng hình thức giành chính quyền trong Cách mạng tháng Tám?", ["Đấu tranh ngoại giao kết hợp với kinh tế.", "Khởi nghĩa vũ trang ở thành thị tiến về nông thôn.", "Kết hợp giữa khởi nghĩa ở nông thôn và khởi nghĩa ở thành thị.", "Tổng tiến công quân sự trên quy mô cả nước."], 2, "Ta giành chính quyền nhịp nhàng ở cả hai khu vực."],
    ["Sự chuẩn bị chu đáo cho Cách mạng tháng Tám 1945 được thực hiện thông qua những cuộc tập dượt nào?", ["Phong trào 1930 - 1931.", "Phong trào 1936 - 1939.", "Cao trào kháng Nhật cứu nước 1945.", "Cả ba phong trào trên."], 3, "Thắng lợi của 1945 là kết quả của 15 năm chuẩn bị qua 3 cuộc tập dượt lớn."],
    ["Điểm khác biệt lớn nhất giữa Cách mạng tháng Tám 1945 và các phong trào yêu nước đầu thế kỷ XX là:", ["Mục tiêu giành độc lập dân tộc.", "Tinh thần hy sinh của quần chúng.", "Có sự lãnh đạo của Đảng Cộng sản với đường lối đúng đắn.", "Địa bàn diễn ra trên cả nước."], 2, "Các phong trào trước thất bại chủ yếu do thiếu một giai cấp lãnh đạo tiên tiến."],
    ["Bài học nào của Cách mạng tháng Tám 1945 vẫn còn nguyên giá trị trong công cuộc bảo vệ Tổ quốc hiện nay?", ["Chỉ sử dụng đấu tranh vũ trang để giải quyết tranh chấp.", "Chờ đợi sự giúp đỡ của các cường quốc.", "Phát huy sức mạnh khối đại đoàn kết toàn dân tộc.", "Tập trung phát triển kinh tế vùng căn cứ địa."], 2, "Đoàn kết dân tộc là nguồn lực nội sinh bền vững nhất."],
    ["Tại sao Đảng ta không phát động Tổng khởi nghĩa ngay khi Nhật đảo chính Pháp (9/3/1945)?", ["Vì lúc đó quân Nhật còn rất mạnh.", "Vì nhân dân chưa ủng hộ Đảng.", "Vì thời cơ chín muồi chưa đến (kẻ thù chưa suy yếu tột cùng).", "Vì Hồ Chí Minh chưa về nước."], 2, "Lúc đó Nhật mới thắng Pháp, quyền lực còn rất mạnh."],
    ["Nghệ thuật 'chớp thời cơ' trong Cách mạng tháng Tám đã thể hiện:", ["Sự may mắn của cách mạng Việt Nam.", "Sự nhạy bén về chính trị và khả năng dự báo tài tình của Đảng.", "Áp lực của quân Đồng minh đối với quân Nhật.", "Sự giúp đỡ vô điều kiện của Hồng quân Liên Xô."], 1, "Đảng đã dự báo từ trước và chuẩn bị sẵn tư thế sẵn sàng."],
    ["Sự kiện nào chứng tỏ Đảng ta đã có bước chuyển hướng chiến lược đúng đắn từ năm 1939 đến 1941?", ["Thành lập các đội Cứu quốc quân.", "Đưa nhiệm vụ giải phóng dân tộc lên hàng đầu.", "Quyết định khởi nghĩa từng phần.", "Thành lập Khu giải phóng Việt Bắc."], 1, "Các Hội nghị Trung ương 6, 7, 8 đã xác định giải phóng dân tộc là nhiệm vụ sống còn."],
    ["Tại sao nói thắng lợi của Cách mạng tháng Tám là thắng lợi của chủ nghĩa Mác - Lênin được vận dụng sáng tạo?", ["Vì ta đã rập khuôn hoàn toàn mô hình cách mạng Nga.", "Vì ta đã nhận được sự chỉ đạo trực tiếp từ Quốc tế Cộng sản.", "Vì Đảng đã kết hợp nhuần nhuyễn lý luận với thực tiễn đặc thù của Việt Nam.", "Vì ta đã sử dụng vũ khí hiện đại của Liên Xô."], 2, "Vận dụng linh hoạt các hình thức đấu tranh và chớp thời cơ."],
    ["Đặc điểm nổi bật về địa bàn giành chính quyền trong tháng 8/1945 là:", ["Chỉ diễn ra ở các thành phố lớn.", "Chỉ diễn ra ở các vùng nông thôn hẻo lánh.", "Diễn ra đồng loạt từ nông thôn đến thành thị, từ đồng bằng đến miền núi.", "Diễn ra ở miền Bắc trước, miền Nam sau."], 2, "Một cuộc tổng khởi nghĩa toàn dân trên quy mô toàn quốc."],
    ["Đóng góp lớn nhất của Cách mạng tháng Tám đối với kho tàng lý luận cách mạng thế giới là:", ["Xây dựng một quân đội chính quy hiện đại.", "Giải quyết vấn đề ruộng đất cho nông dân ngay lập tức.", "Cách mạng giải phóng dân tộc ở một nước thuộc địa có thể giành thắng lợi trước cách mạng vô sản ở chính quốc.", "Sử dụng ngoại giao để kết thúc chiến tranh."], 2, "Đây là sự phát triển sáng tạo lý luận của Lenin."],
    ["Điểm khác biệt về 'nghệ thuật khởi nghĩa' giữa Cách mạng tháng Tám và Cách mạng tháng Mười Nga là gì?", ["Cách mạng Nga dùng bạo lực, Việt Nam dùng hòa bình.", "Cách mạng Nga có Đảng lãnh đạo, Việt Nam thì không.", "Cách mạng Nga đi từ thành thị về nông thôn; Việt Nam kết hợp nông thôn - thành thị.", "Cách mạng Nga do tư sản lãnh đạo, Việt Nam do vô sản lãnh đạo."], 2, "Đây là điểm sáng tạo riêng biệt của cách mạng Việt Nam."],
    ["Nếu không có điều kiện khách quan thuận lợi, liệu Cách mạng tháng Tám có thành công?", ["Chắc chắn không thể thành công.", "Sẽ thành công nhưng diễn ra chậm hơn và đổ máu nhiều hơn.", "Thành công là tất yếu nhờ chuẩn bị chủ quan, nhưng khách quan giúp thắng lợi nhanh và ít tổn thất hơn.", "Sẽ thành công nhờ sự giúp đỡ của Mỹ."], 2, "Nhân tố chủ quan luôn đóng vai trò quyết định."],
    ["Nhận định đánh giá đúng nhất về vai trò của lực lượng vũ trang trong Cách mạng tháng Tám?", ["Là lực lượng duy nhất làm nên thắng lợi.", "Là lực lượng xung kích, hỗ trợ cho lực lượng chính trị giành chính quyền.", "Đóng vai trò quyết định trực tiếp ở mọi địa phương.", "Chỉ mang tính chất trang trí cho cuộc cách mạng."], 1, "Trong Cách mạng tháng Tám, lực lượng chính trị là chủ yếu, lực lượng vũ trang hỗ trợ."]
];

const testLesson7 = [
    ["Thực dân Pháp mở đầu cuộc xâm lược Việt Nam lần thứ hai tại Sài Gòn vào ngày nào?", ["02/09/1945", "23/09/1945", "19/12/1946", "06/03/1945"], 1, "Đêm 22 rạng 23-9-1945, Pháp đánh úp trụ sở UBND Nam Bộ tại Sài Gòn."],
    ["Lời kêu gọi toàn quốc kháng chiến của Chủ tịch Hồ Chí Minh được ban hành vào thời gian nào?", ["19/12/1945", "19/12/1946", "19/12/1947", "19/12/1954"], 1, "Tối 19-12-1946, Bác Hồ ra lời kêu gọi toàn quốc kháng chiến."],
    ["Mục tiêu của thực dân Pháp khi mở cuộc tấn công lên Việt Bắc mùa thu - đông năm 1947 là gì?", ["Khai thông biên giới Việt - Trung.", "Giành lại thế chủ động trên chiến trường.", "Tiêu diệt cơ quan đầu não và quân chủ lực của ta.", "Chuẩn bị ký Hiệp định Giơ-ne-vơ."], 2, "Pháp muốn đánh đòn quyết định vào bộ máy lãnh đạo để kết thúc nhanh chiến tranh."],
    ["Trong chiến dịch Biên giới thu - đông 1950, ta đã chọn cứ điểm nào để đánh mở màn?", ["Cao Bằng", "Thất Khê", "Đông Khê", "Lạng Sơn"], 2, "Đông Khê là vị trí xung yếu trên Đường số 4."],
    ["Đại hội đại biểu lần thứ II của Đảng (2-1951) đã quyết định đổi tên Đảng thành:", ["Đảng Cộng sản Đông Dương", "Đảng Lao động Việt Nam", "Đảng Cộng sản Việt Nam", "Mặt trận Việt Minh."], 1, "Đảng ra hoạt động công khai với tên Đảng Lao động Việt Nam."],
    ["Kế hoạch quân sự Pháp đề ra năm 1953 nhằm 'kết thúc chiến tranh trong danh dự' là:", ["Kế hoạch Bô-la-e.", "Kế hoạch Rơ-ve.", "Kế hoạch Đờ Lát đơ Tát-xi-nyi.", "Kế hoạch Na-va."], 3, "Kế hoạch Na-va là nỗ lực cuối cùng của thực dân Pháp."],
    ["Chiến dịch Điện Biên Phủ bắt đầu vào ngày nào?", ["13/03/1954", "07/05/1954", "19/12/1946", "20/11/1953"], 0, "Chiến dịch Điện Biên Phủ chính thức mở màn vào chiều ngày 13-3-1954."],
    ["Ai là người trực tiếp chỉ huy chiến dịch Điện Biên Phủ 1954?", ["Hồ Chí Minh.", "Võ Nguyên Giáp.", "Phạm Văn Đồng.", "Nguyễn Chí Thanh."], 1, "Đại tướng Võ Nguyên Giáp là Chỉ huy trưởng chiến dịch Điện Biên Phủ."],
    ["Hiệp định Giơ-ne-vơ về chấm dứt chiến tranh ở Đông Dương được ký kết vào ngày:", ["07/05/1954", "20/07/1954", "21/07/1954", "01/01/1955"], 2, "Hiệp định Giơ-ne-vơ chính thức được ký kết vào ngày 21-7-1954."],
    ["Đường lối kháng chiến chống Pháp của Đảng gồm các nội dung chính là:", ["Toàn dân, toàn diện, trường kỳ, tự lực cánh sinh.", "Đánh nhanh thắng nhanh.", "Đấu tranh ngoại giao là chính.", "Dựa hoàn toàn vào viện trợ nước ngoài."], 0, "Đây là 4 nội dung cốt lõi của đường lối kháng chiến."],
    ["Chiến dịch nào của ta đã làm thất bại âm mưu 'đánh nhanh thắng nhanh' của Pháp?", ["Chiến dịch Biên giới 1950.", "Chiến dịch Việt Bắc 1947.", "Chiến dịch Hòa Bình 1951.", "Chiến dịch Điện Biên Phủ 1954."], 1, "Chiến thắng Việt Bắc thu-đông 1947 buộc Pháp từ bỏ chiến lược đánh nhanh thắng nhanh."],
    ["Lực lượng nào giữ vai trò quyết định thắng lợi trong cuộc kháng chiến chống Pháp?", ["Quân tình nguyện Lào.", "Sự giúp đỡ của Liên Xô.", "Sức mạnh tổng hợp của toàn dân tộc.", "Chỉ riêng bộ đội chủ lực."], 2, "Cuộc kháng chiến chống Pháp là cuộc chiến tranh nhân dân."],
    ["Căn cứ địa nào được coi là 'Thủ đô kháng chiến' của nước ta?", ["Việt Bắc.", "Nam Bộ.", "Tây Nguyên.", "Khu 4."], 0, "Căn cứ địa Việt Bắc (gồm 6 tỉnh Cao-Bắc-Lạng-Hà-Tuyên-Thái) là nơi tập trung các cơ quan đầu não."],
    ["Sự kiện nào đánh dấu sự sụp đổ hoàn toàn kế hoạch Na-va của Pháp?", ["Chiến thắng Việt Bắc.", "Ký kết Hiệp định Giơ-ne-vơ.", "Chiến thắng Điện Biên Phủ.", "Chiến dịch Tây Bắc."], 2, "Khi Điện Biên Phủ thất thủ, kế hoạch Na-va chính thức bị phá sản hoàn toàn."],
    ["Đợt tấn công cuối cùng vào trung tâm Mường Thanh trong chiến dịch ĐBP bắt đầu từ ngày nào?", ["13/03/1954", "30/03/1954", "01/05/1954", "07/05/1954"], 2, "Đợt 3 (tấn công quyết định) diễn ra từ ngày 1-5 đến 7-5-1954."],
    ["Nhân tố chủ quan nào quan trọng nhất dẫn đến thắng lợi của kháng chiến chống Pháp?", ["Sự lãnh đạo của Đảng và Bác Hồ.", "Lực lượng vũ trang mạnh.", "Hậu phương rộng lớn.", "Truyền thống yêu nước."], 0, "Sự lãnh đạo sáng suốt của Đảng là nhân tố hàng đầu, mang tính quyết định."],
    ["Tại sao chiến dịch Biên giới 1950 được coi là bước ngoặt của cuộc kháng chiến?", ["Vì ta tiêu diệt được toàn bộ quân Pháp.", "Vì Mỹ ngừng can thiệp vào Đông Dương.", "Vì ta giành được thế chủ động trên chiến trường chính Bắc Bộ.", "Vì Pháp buộc phải ký hiệp định hòa bình."], 2, "Ta chuyển từ thế phòng ngự sang thế chủ động tấn công."],
    ["Nội dung nào phản ánh đúng tính chất 'Toàn diện' của kháng chiến chống Pháp?", ["Huy động mọi tầng lớp dân cư tham gia.", "Đánh giặc trên tất cả các mặt: quân sự, chính trị, kinh tế, văn hóa, ngoại giao.", "Đánh giặc ở mọi địa bàn từ rừng núi đến thành thị.", "Kết hợp đánh du kích và đánh chính quy."], 1, "Kháng chiến toàn diện nghĩa là không chỉ đánh bằng súng đạn."],
    ["Ý nghĩa quan trọng nhất của chiến dịch Việt Bắc thu - đông 1947 là gì?", ["Bảo vệ an toàn cơ quan đầu não, chuyển kháng chiến sang giai đoạn mới.", "Khai thông biên giới Việt - Trung để nhận viện trợ.", "Làm thất bại hoàn toàn kế hoạch Rơ-ve.", "Buộc Mỹ phải viện trợ cho Pháp."], 0, "Thắng lợi này giúp ta bảo toàn bộ máy lãnh đạo và lực lượng vũ trang."],
    ["Việc ký kết Hiệp định Sơ bộ (6-3-1946) và Tạm ước (14-9-1946) nhằm mục đích gì?", ["Đầu hàng thực dân Pháp để giữ hòa bình.", "Tránh cùng lúc đối đầu nhiều kẻ thù, có thời gian chuẩn bị lực lượng.", "Công nhận Việt Nam là một quốc gia độc lập hoàn toàn.", "Chờ đợi quân Đồng minh vào giúp đỡ."], 1, "Sách lược ngoại giao tài tình 'Hòa để tiến' của Đảng và Bác."],
    ["'Quyết tử để Tổ quốc quyết sinh' là lời biểu dương của Bác Hồ dành cho lực lượng nào?", ["Quân tình nguyện Việt - Lào.", "Dân công hỏa tuyến Điện Biên Phủ.", "Chiến sĩ Cảm tử quân Thủ đô.", "Bộ đội chủ lực chiến dịch Biên giới."], 2, "Lời thư khen ngợi tinh thần hy sinh dũng cảm của các chiến sĩ Trung đoàn Thủ đô."],
    ["Mâu thuẫn lớn nhất trong kế hoạch Na-va của Pháp là gì?", ["Giữa Pháp và Mỹ về quyền chỉ huy.", "Giữa quân đội Pháp và chính phủ bù nhìn.", "Giữa tập trung lực lượng và phân tán lực lượng.", "Giữa đánh nhanh và đánh lâu dài."], 2, "Muốn kiểm soát đất đai phải phân tán, muốn đánh bại quân chủ lực phải tập trung."],
    ["Khẩu hiệu 'Tất cả cho tiền tuyến, tất cả để chiến thắng' gắn liền với chiến dịch nào?", ["Việt Bắc 1947.", "Biên giới 1950.", "Tây Bắc 1952.", "Điện Biên Phủ 1954."], 3, "Trong chiến dịch Điện Biên Phủ, Đảng phát động tổng huy động chưa từng có."],
    ["Cuộc vận động 'Sản xuất và tiết kiệm' (1952) có ý nghĩa như thế nào?", ["Để xuất khẩu lấy ngoại tệ mua vũ khí.", "Xây dựng nền kinh tế kháng chiến, tự cung tự cấp.", "Giúp nhân dân giàu có ngay trong chiến tranh.", "Để chuẩn bị kết thúc chiến tranh."], 1, "Giúp ta thực hiện đúng phương châm 'tự lực cánh sinh'."],
    ["Vì sao Điện Biên Phủ được Pháp xây dựng thành tập đoàn cứ điểm mạnh nhất Đông Dương?", ["Vì nó gần biên giới Trung Quốc.", "Để bảo vệ vùng chiếm đóng ở miền Trung.", "Để biến nơi đây thành 'cái máy nghiền' quân chủ lực của ta.", "Vì đây là nơi duy nhất có sân bay quân sự."], 2, "Pháp tin rằng Điện Biên Phủ là pháo đài 'bất khả xâm phạm'."],
    ["Bản chất của Hiệp định Giơ-ne-vơ 1954 là:", ["Một hiệp định quân sự tạm thời.", "Một văn bản pháp lý quốc tế công nhận quyền dân tộc cơ bản của Việt Nam.", "Sự thỏa hiệp giữa các nước lớn để chia cắt Việt Nam lâu dài.", "Thắng lợi ngoại giao đơn thuần của Việt Nam."], 1, "Lần đầu tiên, các cường quốc thừa nhận độc lập, chủ quyền, thống nhất của Việt Nam."],
    ["Bài học 'Tranh thủ sự ủng hộ quốc tế' được thể hiện qua yếu tố nào?", ["Liên minh Việt - Miên - Lào.", "Sự ủng hộ của Liên Xô, Trung Quốc.", "Sự phản đối chiến tranh của nhân dân Pháp.", "Tất cả các yếu tố trên."], 3, "Ta kết hợp được sức mạnh đoàn kết 3 nước Đông Dương, phe XHCN và nhân dân tiến bộ."],
    ["Cuộc kháng chiến chống Pháp thắng lợi đã để lại bài học gì về xây dựng quân đội?", ["Chỉ cần tập trung phát triển vũ khí hiện đại.", "Xây dựng quân đội từ nhân dân, vì nhân dân, lấy chính trị làm gốc.", "Thuê chuyên gia nước ngoài huấn luyện.", "Quân đội chỉ cần tập trung ở thành phố lớn."], 1, "Quân đội xuất thân từ nhân dân, lấy giác ngộ chính trị làm nền tảng."],
    ["Cuộc tiến công chiến lược Đông Xuân 1953-1954 đạt được mục tiêu quan trọng nào?", ["Giải phóng hoàn toàn miền Bắc.", "Bước đầu làm phá sản kế hoạch Na-va, buộc địch phải phân tán quân.", "Tiêu diệt toàn bộ quân cơ động của Pháp.", "Kết thúc cuộc kháng chiến ngay lập tức."], 1, "Ta buộc khối quân cơ động của Na-va phải chia thành 5 mảnh phân tán."],
    ["Điểm khác biệt giữa chiến dịch Biên giới (1950) so với chiến dịch Việt Bắc (1947) là:", ["Ta bị động phòng ngự.", "Ta chủ động mở chiến dịch tấn công quy mô lớn.", "Ta chỉ dùng đấu tranh chính trị.", "Có sự giúp đỡ của Mỹ."], 1, "Biên giới là lần đầu tiên ta chủ động vạch kế hoạch và tấn công địch."],
    ["Nghệ thuật quân sự 'Lấy nhỏ thắng lớn, lấy yếu chống mạnh' trong kháng chiến chống Pháp thể hiện qua:", ["Sử dụng máy bay chiến đấu hiện đại.", "Chiến tranh nhân dân, kết hợp đánh du kích và đánh chính quy.", "Chờ địch hết đạn rồi mới đánh.", "Chỉ đánh vào ban đêm."], 1, "Dựa vào dân và địa hình hiểm trở để tiêu hao rồi tiêu diệt địch."],
    ["Hiệp định Giơ-ne-vơ (1954) phản ánh thực tế chiến trường như thế nào?", ["Pháp thắng ta ở miền Nam nên miền Nam vẫn thuộc Pháp.", "Ta thắng hoàn toàn nên Pháp phải rút quân ngay lập tức.", "Ta thắng lợi nhưng chưa đủ mạnh để giải phóng toàn bộ đất nước ngay.", "Mỹ đã thay chân Pháp cai trị miền Bắc."], 2, "Tương quan lực lượng và bối cảnh quốc tế phức tạp chưa cho phép giải phóng cả nước ngay."],
    ["Việc ta chuyển từ 'đánh nhanh thắng nhanh' sang 'đánh chắc tiến chắc' trong chiến dịch ĐBP thể hiện:", ["Sự sợ hãi trước sức mạnh của Pháp.", "Sự sáng suốt, bám sát thực tiễn chiến trường của chỉ huy.", "Sự bất đồng giữa Việt Nam và cố vấn nước ngoài.", "Ta thiếu vũ khí nên không dám đánh nhanh."], 1, "Quyết định khó khăn và thiên tài của Đại tướng Võ Nguyên Giáp."],
    ["Tại sao nói thắng lợi của kháng chiến chống Pháp đã giáng đòn nặng nề vào chủ nghĩa thực dân cũ?", ["Vì Pháp là đế quốc mạnh nhất thế giới.", "Vì lần đầu tiên một nước thuộc địa nhỏ bé đánh bại một cường quốc thực dân lâu đời.", "Vì sau đó Pháp mất hết thuộc địa ở châu Âu.", "Vì Pháp phải bồi thường chiến phí cho Việt Nam."], 1, "Thắng lợi này làm sụp đổ quan niệm về sự 'bất khả chiến bại' của thực dân."],
    ["Sự kiện nào chứng tỏ tình đoàn kết chiến đấu giữa 3 nước Việt - Miên - Lào?", ["Việc ký kết Hiệp định Giơ-ne-vơ.", "Liên minh nhân dân Việt - Miên - Lào được thành lập năm 1951.", "3 nước cùng chung một quân đội.", "3 nước dùng chung một đơn vị tiền tệ."], 1, "Liên minh này chính thức hóa sự hợp tác chiến đấu 3 nước."],
    ["Mối quan hệ giữa thắng lợi Điện Biên Phủ và Hiệp định Giơ-ne-vơ phản ánh chân lý nào?", ["Ngoại giao quan trọng hơn quân sự.", "Chỉ cần ngoại giao là giành được độc lập.", "Thắng lợi trên chiến trường quyết định thắng lợi trên bàn đàm phán.", "Các nước lớn luôn quyết định vận mệnh nước nhỏ."], 2, "Thực lực là cái chuông, ngoại giao là cái tiếng."],
    ["Nguyên nhân quyết định nhất đưa cuộc kháng chiến chống Pháp đến thắng lợi cuối cùng là:", ["Sự lãnh đạo đúng đắn của Đảng với đường lối kháng chiến sáng tạo.", "Tinh thần đoàn kết chiến đấu của nhân dân 3 nước Đông Dương.", "Sự giúp đỡ của Trung Quốc, Liên Xô và các nước dân chủ nhân dân.", "Truyền thống yêu nước chống ngoại xâm của nhân dân Việt Nam."], 0, "Đây là nhân tố bên trong, chủ quan và hàng đầu."],
    ["Nhận định 'Điện Biên Phủ là cái mốc bằng vàng của lịch sử' mang ý nghĩa:", ["Chiến dịch này thu được nhiều vàng bạc.", "Nó đánh dấu sự giàu có của đất nước sau chiến tranh.", "Nó đánh dấu sự sụp đổ của chủ nghĩa thực dân cũ, mở ra kỷ nguyên mới.", "Nó là chiến dịch tốn kém nhất lịch sử."], 2, "Là nguồn cổ vũ to lớn cho phong trào giải phóng dân tộc toàn cầu."],
    ["Bài học kinh nghiệm quý giá nhất từ kháng chiến chống Pháp để lại là:", ["Mua thật nhiều vũ khí hiện đại.", "Kết hợp sức mạnh dân tộc với sức mạnh thời đại, lấy sức mạnh nội sinh làm gốc.", "Luôn dựa vào sự bảo trợ của một cường quốc.", "Chỉ tập trung phát triển quân sự, không cần ngoại giao."], 1, "Phát huy nội lực là yếu tố sống còn, đồng thời tranh thủ ủng hộ quốc tế."]
];

const testLesson8 = [
    ["Sau Hiệp định Giơ-ne-vơ 1954, Mỹ thay chân Pháp dựng lên chính quyền tay sai đứng đầu là ai?", ["Bảo Đại.", "Dương Văn Minh.", "Ngô Đình Diệm.", "Nguyễn Văn Thiệu."], 2, "Mỹ đưa Ngô Đình Diệm về nước lập chính quyền tay sai, thực hiện chủ nghĩa thực dân kiểu mới."],
    ["Hội nghị nào của Đảng đã mở đường cho phong trào 'Đồng khởi' (1959 - 1960)?", ["Hội nghị Trung ương 12.", "Đại hội lần thứ III.", "Hội nghị Trung ương 15.", "Hội nghị Trung ương 21."], 2, "Hội nghị Trung ương 15 (1/1959) xác định con đường cơ bản của cách mạng miền Nam."],
    ["'Đội quân tóc dài' là tên gọi dành cho lực lượng nào trong phong trào 'Đồng khởi'?", ["Phụ nữ miền Nam.", "Thanh niên xung phong.", "Bộ đội chủ lực.", "Đội du kích địa phương."], 0, "Lực lượng đấu tranh chính trị trực diện của phụ nữ miền Nam, tiêu biểu là Bến Tre."],
    ["'Quốc sách' của chiến lược 'Chiến tranh đặc biệt' (1961 - 1965) là gì?", ["Tìm diệt.", "Ấp chiến lược.", "Bình định.", "Trực thăng vận."], 1, "Mỹ coi lập 'Ấp chiến lược' là quốc sách, nhằm tách dân ra khỏi cách mạng."],
    ["Hai chiến thuật mới được Mỹ sử dụng phổ biến trong 'Chiến tranh đặc biệt' là:", ["Thiết xa vận và Tìm diệt.", "Trực thăng vận và Thiết xa vận.", "Bình định và Trực thăng vận.", "Phi Mỹ hóa và Tìm diệt."], 1, "Mỹ dùng trực thăng (Trực thăng vận) và xe bọc thép M113 (Thiết xa vận)."],
    ["Chiến thắng quân sự nào mở đầu khả năng đánh bại chiến lược 'Chiến tranh đặc biệt'?", ["Ấp Bắc (Mỹ Tho).", "Vạn Tường (Quảng Ngãi).", "Bình Giã (Bà Rịa).", "Đồng Xoài (Bình Phước)."], 0, "Thắng lợi ở Ấp Bắc (2/1/1963) chứng minh ta có khả năng đánh bại chiến thuật hiện đại của Mỹ."],
    ["Chiến lược 'Chiến tranh cục bộ' (1965-1968) có điểm khác biệt lớn nhất so với 'Chiến tranh đặc biệt' là:", ["Dùng cố vấn Mỹ.", "Dùng quân đội Sài Gòn.", "Đưa quân Mỹ trực tiếp tham chiến.", "Sử dụng ấp chiến lược."], 2, "Ở 'Chiến tranh cục bộ', Mỹ đưa quân viễn chinh trực tiếp tham chiến, giữ vai trò chủ chốt."],
    ["Chiến thắng quân sự đầu tiên chống lại 'Chiến tranh cục bộ' là trận nào?", ["Núi Thành.", "Vạn Tường.", "Pleime.", "Khe Sanh."], 1, "Trận Vạn Tường (8/1965) chứng minh ta có thể đánh thắng quân viễn chinh Mỹ."],
    ["Ý nghĩa quan trọng nhất của cuộc Tổng tiến công và nổi dậy Xuân Mậu Thân 1968 là gì?", ["Buộc Mỹ phải tuyên bố 'phi Mỹ hóa' chiến tranh.", "Giải phóng hoàn toàn miền Nam.", "Buộc Mỹ ký ngay Hiệp định Pa-ri.", "Mỹ rút hết quân đội khỏi Đông Dương."], 0, "Thắng lợi này làm lung lay ý chí xâm lược của Mỹ."],
    ["Lực lượng đóng vai trò chủ yếu trong chiến lược 'Việt Nam hóa chiến tranh' là:", ["Quân Mỹ.", "Quân Đồng minh của Mỹ.", "Quân đội Sài Gòn.", "Quân giải phóng."], 2, "Mỹ rút dần quân Mỹ, dùng người Việt đánh người Việt."],
    ["Thắng lợi nào của ta được coi là trận 'Điện Biên Phủ trên không'?", ["Chiến dịch Tây Nguyên.", "Trận đánh bại cuộc tập kích B52 vào Hà Nội, Hải Phòng năm 1972.", "Chiến dịch Đường 9 - Nam Lào.", "Chiến dịch Hồ Chí Minh."], 1, "Thắng lợi đập tan cuộc tập kích B52 trong 12 ngày đêm cuối năm 1972."],
    ["Điều khoản cốt lõi của Hiệp định Pa-ri (27-1-1973) là gì?", ["Mỹ tiếp tục chi viện cho miền Nam.", "Mỹ cam kết chấm dứt chiến tranh, rút hết quân về nước.", "Thành lập chính phủ liên hiệp 3 thành phần.", "Việt Nam bị chia cắt vĩnh viễn."], 1, "Mỹ và đồng minh rút hết quân, còn quân ta ở lại miền Nam."],
    ["Chiến dịch nào mở đầu cuộc Tổng tiến công và nổi dậy Xuân 1975?", ["Chiến dịch Huế - Đà Nẵng.", "Chiến dịch Hồ Chí Minh.", "Chiến dịch Tây Nguyên.", "Chiến dịch Đường 14 - Phước Long."], 2, "Chiến dịch Tây Nguyên (bắt đầu bằng trận Buôn Ma Thuột 10/3) chuyển cuộc kháng chiến sang giai đoạn mới."],
    ["Chiến dịch cuối cùng kết thúc cuộc kháng chiến chống Mỹ là:", ["Chiến dịch Tây Nguyên.", "Chiến dịch Huế - Đà Nẵng.", "Chiến dịch Hồ Chí Minh.", "Chiến dịch biên giới."], 2, "Chiến dịch Hồ Chí Minh (26/4-30/4/1975) là chiến dịch quyết chiến lược cuối cùng."],
    ["Ngày 30-4-1975 ghi dấu sự kiện lịch sử nào?", ["Mỹ rút quân khỏi miền Nam.", "Ký kết Hiệp định Pa-ri.", "Chiến dịch Hồ Chí Minh toàn thắng, giải phóng hoàn toàn miền Nam.", "Tổng tuyển cử bầu quốc hội chung."], 2, "Cờ cách mạng tung bay trên Dinh Độc Lập lúc 11:30 ngày 30/4/1975."],
    ["Nhân tố quyết định nhất dẫn đến thắng lợi của kháng chiến chống Mỹ là gì?", ["Sự lãnh đạo đúng đắn của Đảng.", "Hậu phương miền Bắc vững mạnh.", "Sự giúp đỡ của Liên Xô, Trung Quốc.", "Tình đoàn kết 3 nước Đông Dương."], 0, "Đảng đề ra đường lối độc lập, sáng tạo, đoàn kết toàn dân và tranh thủ ủng hộ quốc tế."],
    ["Tại sao phong trào 'Đồng khởi' được coi là bước ngoặt của cách mạng miền Nam?", ["Vì ta tiêu diệt hết quân đội Sài Gòn.", "Vì Mỹ rút quân về nước.", "Chuyển cách mạng từ thế giữ gìn lực lượng sang thế tiến công.", "Chấm dứt sự can thiệp của Mỹ."], 2, "Sau Đồng khởi, ta xây dựng được lực lượng vũ trang và giải phóng nhiều vùng nông thôn."],
    ["Đại hội III (1960) xác định vai trò của cách mạng miền Bắc như thế nào?", ["Có vai trò quyết định nhất đối với sự phát triển của cách mạng cả nước.", "Có vai trò quyết định trực tiếp đối với sự nghiệp giải phóng miền Nam.", "Là hậu phương hỗ trợ ngoại giao.", "Không có liên hệ với cách mạng miền Nam."], 0, "Miền Bắc xây dựng CNXH là cơ sở vững chắc (quyết định nhất)."],
    ["Bản chất của chiến lược 'Việt Nam hóa chiến tranh' là gì?", ["Mỹ muốn giúp người Việt tự do hơn.", "Dùng người Việt đánh người Việt để giảm xương máu quân Mỹ.", "Mỹ muốn rút quân để kết thúc chiến tranh ngay lập tức.", "Tăng cường quân đội các nước đồng minh của Mỹ vào Việt Nam."], 1, "Đây là hình thức chiến tranh xâm lược thực dân mới, 'thay màu da trên xác chết'."],
    ["Ý nghĩa của chiến thắng Đường 14 - Phước Long (1/1975) là gì?", ["Là thắng lợi quân sự lớn nhất trong lịch sử dân tộc.", "Kết thúc hoàn toàn cuộc kháng chiến chống Mỹ cứu nước.", "Là đòn trinh sát chiến lược cho thấy Mỹ khó có khả năng can thiệp trở lại.", "Buộc thực dân Pháp phải rút quân hoàn toàn khỏi Đông Dương."], 2, "Giúp Đảng đánh giá chính xác sự suy yếu của địch và củng cố quyết tâm chiến lược."],
    ["Tại sao nói Hiệp định Pa-ri 1973 là thắng lợi của sự kết hợp quân sự - chính trị - ngoại giao?", ["Vì ta chỉ thắng trên mặt trận ngoại giao.", "Vì Mỹ tự nguyện rút quân.", "Vì ta thắng quân sự (ĐBP trên không) buộc địch ký hiệp định, công nhận quyền dân tộc.", "Vì Mỹ sợ Liên Xô nên rút quân."], 2, "Thắng lợi trên chiến trường tạo ra thực lực cho ta trên bàn đàm phán."],
    ["Vì sao miền Bắc được gọi là 'Hậu phương lớn' của miền Nam?", ["Vì miền Bắc giàu có và phát triển hơn miền Nam.", "Vì miền Bắc chi viện tối đa sức người, sức của cho tiền tuyến miền Nam.", "Vì miền Bắc không phải chịu ảnh hưởng của chiến tranh.", "Vì quân đội miền Bắc có vũ khí mạnh hơn quân Mỹ."], 1, "Miền Bắc là căn cứ địa cách mạng, thông qua đường mòn HCM chi viện cho miền Nam."],
    ["Điểm chung giữa chiến lược 'Chiến tranh cục bộ' và 'Việt Nam hóa chiến tranh' là gì?", ["Đều là loại hình chiến tranh xâm lược thực dân mới của Mỹ.", "Đều huy động số lượng lớn quân Mỹ tham chiến.", "Đều kết thúc bằng một hiệp định hòa bình.", "Đều sử dụng cố vấn Pháp."], 0, "Dù tên gọi hay cách thức thay đổi, mục tiêu của Mỹ vẫn là biến miền Nam thành căn cứ quân sự."],
    ["Cuộc Tổng tiến công Xuân 1975 phát triển theo phương châm nào?", ["Đánh lâu dài, chắc chắn từng bước một.", "Thần tốc, táo bạo, bất ngờ, chắc thắng.", "Đánh nhanh thắng nhanh ngay từ đầu.", "Vừa đánh quân sự vừa đàm phán ngoại giao."], 1, "Mệnh lệnh lịch sử của Đại tướng Võ Nguyên Giáp, chớp thời cơ đẩy tốc độ tấn công."],
    ["Sự sụp đổ của chính quyền Ngô Đình Diệm (1963) chứng tỏ điều gì?", ["Mỹ muốn thay đổi chế độ sang dân chủ thực sự.", "Sự thất bại của Mỹ trong việc dùng chính quyền độc tài để bình định miền Nam.", "Cách mạng miền Nam đã giành thắng lợi hoàn toàn.", "Thực dân Pháp quay trở lại xâm lược miền Nam."], 1, "Cuộc đảo chính 1/11/1963 cho thấy sự bế tắc của chiến lược 'Chiến tranh đặc biệt'."],
    ["Mục tiêu của chiến dịch Hồ Chí Minh là gì?", ["Giải phóng toàn bộ vùng Tây Nguyên hiểm yếu.", "Giải phóng hai thành phố lớn là Huế và Đà Nẵng.", "Đánh chiếm cơ quan đầu não địch tại Sài Gòn, kết thúc kháng chiến.", "Phá hủy hoàn toàn hệ thống đường mòn Hồ Chí Minh."], 2, "Chiến dịch quyết chiến chiến lược cuối cùng, tập trung 5 cánh quân đánh vào Sài Gòn."],
    ["Tính chất của cuộc kháng chiến chống Mỹ (1954-1975) là:", ["Một cuộc chiến tranh tôn giáo giữa các sắc tộc.", "Cuộc kháng chiến chính nghĩa, giải phóng dân tộc và bảo vệ Tổ quốc.", "Một cuộc nội chiến đơn thuần giữa hai miền Nam - Bắc.", "Một cuộc chiến tranh ủy nhiệm hoàn toàn của Liên Xô."], 1, "Cuộc chiến đấu của toàn dân nhằm đánh đuổi đế quốc xâm lược và thống nhất đất nước."],
    ["Thắng lợi của cuộc kháng chiến chống Mỹ đã chấm dứt sự chia cắt bao nhiêu năm?", ["10 năm", "15 năm", "21 năm", "30 năm"], 2, "Đất nước bị chia cắt từ 1954 đến 1975 là 21 năm."],
    ["So sánh lực lượng quân sự sau Hiệp định Pa-ri 1973 thay đổi như thế nào?", ["Có lợi cho quân đội Sài Gòn vì Mỹ để lại vũ khí.", "Có lợi cho quân Mỹ vì không phải trực tiếp đánh nhau.", "Có lợi hoàn toàn cho cách mạng vì quân Mỹ rút đi, quân ta vẫn ở lại miền Nam.", "Không có gì thay đổi."], 2, "Điểm thuận lợi nhất của Hiệp định Pa-ri: địch mất chỗ dựa trực tiếp là quân viễn chinh Mỹ."],
    ["Điểm độc đáo trong nghệ thuật chỉ đạo chiến tranh của Đảng giai đoạn 1954-1975 là gì?", ["Tiến hành đồng thời hai chiến lược cách mạng ở hai miền.", "Chỉ tập trung xây dựng miền Bắc.", "Chỉ tập trung đánh du kích ở miền Nam.", "Dựa hoàn toàn vào viện trợ của Trung Quốc."], 0, "Vừa xây dựng CNXH ở miền Bắc vừa tiến hành chiến tranh giải phóng ở miền Nam."],
    ["Tại sao chiến thắng Vạn Tường (1965) lại được ví như 'Ấp Bắc đối với quân Mỹ'?", ["Vì cả hai trận đánh đều diễn ra tại tỉnh Quảng Ngãi.", "Vì nó chứng minh ta có khả năng đánh thắng trực tiếp quân viễn chinh Mỹ.", "Vì cả hai trận đều sử dụng chiến thuật trực thăng vận.", "Vì quân Mỹ trong trận này đã thất bại thảm hại như quân đội Sài Gòn."], 1, "Vạn Tường mở đầu khả năng đánh thắng quân viễn chinh Mỹ."],
    ["Bài học kinh nghiệm từ kháng chiến chống Mỹ có thể áp dụng vào bảo vệ biển đảo hiện nay là:", ["Luôn ưu tiên sử dụng bạo lực vũ trang để giải quyết mọi tranh chấp.", "Kết hợp sức mạnh dân tộc với sức mạnh thời đại.", "Chờ đợi sự giúp đỡ hoàn toàn từ Liên hợp quốc.", "Chỉ cần tập trung phát triển kinh tế, không cần quan tâm đến quân sự."], 1, "Đoàn kết dân tộc (nội lực) kết hợp ủng hộ thế giới (ngoại lực) bảo vệ chủ quyền."],
    ["Sự kiện nào chứng tỏ Mỹ đã thất bại hoàn toàn trong âm mưu chia cắt lâu dài Việt Nam?", ["Mỹ ký kết Hiệp định Pa-ri đầu năm 1973.", "Quân Mỹ rút hết quân đội về nước năm 1973.", "Thắng lợi của Chiến dịch Hồ Chí Minh lịch sử năm 1975.", "Đại hội Đảng lần thứ III họp tại miền Bắc."], 2, "Khi miền Nam giải phóng hoàn toàn, mục tiêu chia cắt Việt Nam của Mỹ tan thành mây khói."],
    ["Việc Mỹ chuyển hướng từ 'Tìm diệt' sang 'Việt Nam hóa' chiến tranh phản ánh:", ["Mỹ muốn Việt Nam phát triển kinh tế mạnh mẽ hơn.", "Sự sa lầy và thất bại về chiến lược của Mỹ trên chiến trường miền Nam.", "Sự giàu có và lớn mạnh vượt bậc của quân đội Sài Gòn.", "Hòa bình thế giới đã hoàn toàn lập lại."], 1, "Mỹ buộc phải chuyển hướng vì không chịu đựng được tổn thất và phản đối trong nước."],
    ["Tại sao nói cách mạng miền Bắc và miền Nam có mối quan hệ 'khăng khít, tác động thúc đẩy lẫn nhau'?", ["Vì thành tựu của miền Bắc chi viện cho miền Nam thắng lợi, miền Nam thắng lợi bảo vệ được miền Bắc.", "Vì cả hai miền đều đang nằm dưới sự cai trị trực tiếp của Mỹ.", "Vì hai miền có chung một đội quân duy nhất ngay từ đầu.", "Vì hai miền thường xuyên trao đổi hàng hóa và hợp tác kinh tế."], 0, "Miền Bắc là nền tảng, miền Nam là mục tiêu trực tiếp."],
    ["Ý nghĩa thời đại sâu sắc của thắng lợi kháng chiến chống Mỹ là gì?", ["Làm tan rã hoàn toàn Liên hợp quốc.", "Góp phần làm sụp đổ chủ nghĩa thực dân mới, cổ vũ phong trào giải phóng dân tộc trên thế giới.", "Giúp Việt Nam trở thành cường quốc quân sự số 1.", "Mỹ phá sản hoàn toàn về kinh tế."], 1, "Việt Nam đánh bại siêu cường mạnh nhất thế giới, cổ vũ các nước nhỏ tự quyết định vận mệnh mình."],
    ["Nghệ thuật quân sự 'Kết hợp tiến công và nổi dậy' trong mùa Xuân 1975 có điểm đặc sắc là:", ["Kết hợp đòn đánh của quân chủ lực và sự nổi dậy giành quyền làm chủ của quần chúng.", "Chỉ dùng quân đội bí mật.", "Chỉ dựa vào biểu tình chính trị.", "Dùng ngoại giao để buộc địch đầu hàng."], 0, "Quân chủ lực đánh tan tổ chức địch, quần chúng nổi dậy đập tan bộ máy cai trị ở cơ sở."],
    ["Nguyên nhân khách quan nào giúp cuộc kháng chiến chống Mỹ đi đến thắng lợi?", ["Sự suy yếu của kinh tế Mỹ.", "Tình đoàn kết chiến đấu của nhân dân 3 nước Đông Dương và sự ủng hộ của thế giới.", "Mỹ bị khủng hoảng chính trị trong nước.", "Liên hợp quốc cấm Mỹ xâm lược."], 1, "Sức mạnh thời đại: Liên Xô, Trung Quốc và nhân dân yêu chuộng hòa bình cô lập Mỹ."]
];

// ── HÀM TRỘN MẢNG (Fisher-Yates) ───────────────────────────────────
function shuffleArray(arr) {
    const a = arr.slice();
    for (let i = a.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
}

// ── TẠO BỘ 40 CÂU LUYỆN TẬP TỪ testLesson (có giải thích) ─────────
function buildPractice40(rawData) {
    return rawData.map(([prompt, options, answer, explain]) => ({
        kind: "mcq", prompt, options, answer, explain: explain || ""
    }));
}

// ── TẠO BỘ TRỘN 3 BÀI: 40 CÂU NGẪU NHIÊN TỪ 120 CÂU ──────────────
function buildMixed40(raw6, raw7, raw8) {
    const all = [...raw6, ...raw7, ...raw8];
    const shuffled = shuffleArray(all).slice(0, 40);
    return shuffled.map(([prompt, options, answer, explain]) => ({
        kind: "mcq", prompt, options, answer, explain: explain || ""
    }));
}

// ── HELPERS ─────────────────────────────────────────────────────────
function mcq(prompt, options, answer) {
    return { kind: "mcq", prompt, options, answer };
}
function buildQuizActivity(title, badge, description, rawQuestions) {
    return {
        type: "quiz", title, badge, description,
        items: rawQuestions.map(([prompt, options, answer]) => mcq(prompt, options, answer))
    };
}
function buildTestActivity(title, badge, description, items) {
    return { type: "test", title, badge, description, items };
}
function buildPracticeActivity40(title, badge, description, rawData) {
    return {
        type: "practice40", title, badge, description,
        get items() { return buildPractice40(rawData); }  // fresh each time
    };
}
function buildMixedActivity(title, badge, description) {
    return {
        type: "test", title, badge, description,
        get items() { return buildMixed40(testLesson6, testLesson7, testLesson8); }
    };
}

// ── EXPERIENCES MAP ─────────────────────────────────────────────────
const experiences = {
    // Luyện tập 10 câu (giữ nguyên cho game)
    "practice-lesson6": buildQuizActivity("Luyện tập nhanh - Bài 6", "Bài luyện tập", "Bộ 10 câu luyện tập nhanh cho Bài 6.", practiceLesson6),
    "practice-lesson7": buildQuizActivity("Luyện tập nhanh - Bài 7", "Bài luyện tập", "Bộ 10 câu luyện tập nhanh cho Bài 7.", practiceLesson7),
    "practice-lesson8": buildQuizActivity("Luyện tập nhanh - Bài 8", "Bài luyện tập", "Bộ 10 câu luyện tập nhanh cho Bài 8.", practiceLesson8),

    // === MỚI: Luyện tập 40 câu (từng bài) ===
    "practice40-lesson6": buildPracticeActivity40("Luyện tập đầy đủ - Bài 6", "Luyện tập 40 câu", "40 câu hỏi có giải thích cho Bài 6: Cách mạng tháng Tám 1945.", testLesson6),
    "practice40-lesson7": buildPracticeActivity40("Luyện tập đầy đủ - Bài 7", "Luyện tập 40 câu", "40 câu hỏi có giải thích cho Bài 7: Kháng chiến chống Pháp.", testLesson7),
    "practice40-lesson8": buildPracticeActivity40("Luyện tập đầy đủ - Bài 8", "Luyện tập 40 câu", "40 câu hỏi có giải thích cho Bài 8: Kháng chiến chống Mỹ.", testLesson8),

    // === MỚI: Luyện tập trộn 3 bài ===
    "practice40-mixed": buildMixedActivity("Luyện tập tổng hợp — Trộn 3 bài", "Luyện tập tổng hợp", "40 câu ngẫu nhiên từ cả 3 bài 6, 7, 8 (xáo trộn mỗi lần làm)."),

    // Đề kiểm tra từng bài
    "test-lesson6": buildTestActivity("Đề kiểm tra - Bài 6: Cách mạng tháng Tám 1945", "Đề kiểm tra", "40 câu trắc nghiệm tổng kết Bài 6.", buildPractice40(testLesson6)),
    "test-lesson7": buildTestActivity("Đề kiểm tra - Bài 7: Kháng chiến chống thực dân Pháp", "Đề kiểm tra", "40 câu trắc nghiệm tổng kết Bài 7.", buildPractice40(testLesson7)),
    "test-lesson8": buildTestActivity("Đề kiểm tra - Bài 8: Kháng chiến chống Mỹ cứu nước", "Đề kiểm tra", "40 câu trắc nghiệm tổng kết Bài 8.", buildPractice40(testLesson8)),

    // === MỚI: Đề kiểm tra trộn 3 bài ===
    "test-mixed": buildMixedActivity("Đề kiểm tra tổng hợp — Trộn 3 bài", "Đề kiểm tra tổng hợp", "40 câu ngẫu nhiên từ cả 3 bài 6, 7, 8 (xáo trộn mỗi lần thi)."),

    // Game
    "game-lesson6": buildQuizActivity("Game nhanh - Bài 6", "Trò chơi", "Chế độ chơi nhanh 10 câu cho Bài 6.", practiceLesson6),
    "game-lesson7": buildQuizActivity("Game nhanh - Bài 7", "Trò chơi", "Chế độ chơi nhanh 10 câu cho Bài 7.", practiceLesson7),
    "game-lesson8": buildQuizActivity("Game nhanh - Bài 8", "Trò chơi", "Chế độ chơi nhanh 10 câu cho Bài 8.", practiceLesson8)
};

// ═══════════════════════════════════════════════════════════════════
// DOM REFS
// ═══════════════════════════════════════════════════════════════════
const actionButtons    = document.querySelectorAll(".action-btn");
const modeButtons      = document.querySelectorAll(".section-btn");
const contentPanels    = document.querySelectorAll(".content-panel");
const mainView         = document.getElementById("mainView");
const metricTitle      = document.getElementById("metricTitle");
const metricFormat     = document.getElementById("metricFormat");
const metricLevel      = document.getElementById("metricLevel");
const workspaceLabel   = document.getElementById("workspaceLabel");
const workspaceHeading = document.getElementById("workspaceHeading");
const experienceShell  = document.getElementById("experienceShell");
const activityBoard    = document.getElementById("activityBoard");
const activityBadge    = document.getElementById("activityBadge");
const activityProgress = document.getElementById("activityProgress");
const activityScore    = document.getElementById("activityScore");
const activityNumber   = document.getElementById("activityNumber");
const questionLabel    = document.getElementById("questionLabel");
const questionTitle    = document.getElementById("questionTitle");
const questionBody     = document.getElementById("questionBody");
const prevBtn          = document.getElementById("prevBtn");
const nextBtn          = document.getElementById("nextBtn");
const submitBtn        = document.getElementById("submitBtn");
const restartBtn       = document.getElementById("restartBtn");
const backBtn          = document.getElementById("backBtn");
const resultPanel      = document.getElementById("resultPanel");
const resultTitle      = document.getElementById("resultTitle");
const resultText       = document.getElementById("resultText");
const answeredCount    = document.getElementById("answeredCount");
const totalCount       = document.getElementById("totalCount");
const summaryFill      = document.getElementById("summaryFill");

let currentActivityKey = null;
let currentActivity    = null;
let currentIndex       = 0;
let answers            = [];
let tabSwitchCount     = 0;
const MAX_TAB_SWITCH   = 3;

// ── TIMER ─────────────────────────────────────────────────────────
const TEST_DURATION_SEC = 50 * 60;
let timerInterval  = null;
let timerRemaining = 0;

function startTimer() {
    clearInterval(timerInterval);
    timerRemaining = TEST_DURATION_SEC;
    renderTimer();
    timerInterval = setInterval(() => {
        timerRemaining--;
        renderTimer();
        if (timerRemaining <= 0) { clearInterval(timerInterval); autoSubmitTest(); }
    }, 1000);
}
function stopTimer() {
    clearInterval(timerInterval);
    timerInterval = null;
    const el = document.getElementById('testTimer');
    if (el) el.hidden = true;
}
function renderTimer() {
    const el = document.getElementById('testTimer');
    if (!el) return;
    el.hidden = false;
    const m = Math.floor(timerRemaining / 60);
    const s = timerRemaining % 60;
    el.textContent = '⏱ ' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
    if (timerRemaining <= 300)      { el.style.background = '#c0392b'; el.style.animation = 'pulse 1s infinite'; }
    else if (timerRemaining <= 600) { el.style.background = '#e67e22'; el.style.animation = ''; }
    else                            { el.style.background = '#8b1a2e'; el.style.animation = ''; }
}
function autoSubmitTest() {
    if (!currentActivity || (currentActivity.type !== 'test' && currentActivity.type !== 'practice40')) return;
    submitCurrent(true);
}

// ── BẢNG ĐIỀU HƯỚNG CÂU HỎI ─────────────────────────────────────
function buildQuestionNav() {
    const nav = document.getElementById('questionNav');
    if (!nav || !currentActivity) return;
    const isTestLike = currentActivity.type === 'test' || currentActivity.type === 'practice40';
    if (!isTestLike) { nav.hidden = true; return; }
    nav.hidden = false;
    const total = currentActivity.items.length;
    let html = '<div class="qnav-title">Danh sách câu</div><div class="qnav-grid">';
    for (let i = 0; i < total; i++) {
        const isDone = answers[i] !== null && answers[i] !== undefined;
        const isCur  = i === currentIndex;
        let cls = 'qnav-btn';
        if (isCur)       cls += ' qnav-current';
        else if (isDone) cls += ' qnav-done';
        html += `<button class="${cls}" type="button" data-index="${i}">${i + 1}</button>`;
    }
    html += '</div>';
    html += `<div class="qnav-legend">
        <span class="qnav-leg-dot qnav-done"></span><span>Đã làm</span>
        <span class="qnav-leg-dot qnav-current"></span><span>Đang làm</span>
    </div>`;
    nav.innerHTML = html;
    nav.querySelectorAll('.qnav-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            currentIndex = Number(btn.dataset.index);
            const target = document.getElementById('tq-' + currentIndex);
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            buildQuestionNav();
        });
    });
}

// ── RENDER TEST / PRACTICE40 (hiện tất cả cùng lúc) ──────────────
function renderTestView() {
    if (!currentActivity) return;
    const isPractice40 = currentActivity.type === 'practice40';
    const items = currentActivity.items;
    const total = items.length;

    openShell();
    activityBoard.hidden = false;
    resultPanel.hidden   = true;
    workspaceLabel.textContent   = isPractice40 ? 'LỊCH SỬ 12 · LUYỆN TẬP 40 CÂU' : 'LỊCH SỬ 12 · ĐỀ KIỂM TRA';
    workspaceHeading.textContent = currentActivity.title;
    activityBadge.textContent    = currentActivity.badge;
    activityProgress.textContent = `${answers.filter(a => a !== null).length}/${total} câu đã làm`;
    activityNumber.textContent   = '';
    activityScore.textContent    = '';
    prevBtn.hidden    = true;
    nextBtn.hidden    = true;
    submitBtn.hidden  = false;
    restartBtn.hidden = true;

    let html = '<div class="test-all-questions">';
    for (let i = 0; i < total; i++) {
        const item   = items[i];
        const chosen = answers[i];
        html += `<div class="test-question-block" id="tq-${i}" data-qidx="${i}">`;
        html += `<p class="test-q-label"><strong>Câu ${i + 1}:</strong> ${escHtml(item.prompt)}</p>`;
        html += '<div class="test-options">';
        for (let j = 0; j < item.options.length; j++) {
            const letter = String.fromCharCode(65 + j);
            const sel    = chosen === j;
            html += `<label class="test-opt ${sel ? 'selected' : ''}">
                <input type="radio" name="tq-${i}" value="${j}" ${sel ? 'checked' : ''}>
                <span class="test-opt-key">${letter}</span>
                <span class="test-opt-text">${escHtml(item.options[j])}</span>
            </label>`;
        }
        html += '</div></div>';
    }
    html += '</div>';

    questionLabel.textContent = '';
    questionTitle.textContent = '';
    questionBody.innerHTML    = html;

    for (let i = 0; i < total; i++) {
        questionBody.querySelectorAll(`input[name="tq-${i}"]`).forEach(inp => {
            inp.addEventListener('change', e => {
                answers[i] = Number(e.target.value);
                currentIndex = i;
                const block = document.getElementById('tq-' + i);
                if (block) block.querySelectorAll('.test-opt').forEach((lbl, j) => {
                    lbl.classList.toggle('selected', j === answers[i]);
                });
                updateSummary();
                buildQuestionNav();
                activityProgress.textContent = `${answers.filter(a => a !== null).length}/${total} câu đã làm`;
            });
        });
    }
    updateSummary();
    buildQuestionNav();
}

// ── REVIEW SAU KHI NỘP ──────────────────────────────────────────
function renderTestReview() {
    if (!currentActivity) return;
    const isPractice40 = currentActivity.type === 'practice40';
    const items = currentActivity.items;
    const total = items.length;
    let correct = 0;

    let html = '<div class="test-all-questions test-reviewed">';
    for (let i = 0; i < total; i++) {
        const item      = items[i];
        const chosen    = answers[i];
        const isCorrect = (chosen === item.answer);
        if (isCorrect) correct++;
        html += `<div class="test-question-block ${chosen === null ? 'unanswered' : (isCorrect ? 'q-correct' : 'q-wrong')}" id="tq-${i}">`;
        html += `<p class="test-q-label"><strong>Câu ${i + 1}:</strong> ${escHtml(item.prompt)}</p>`;
        html += '<div class="test-options">';
        for (let j = 0; j < item.options.length; j++) {
            const letter = String.fromCharCode(65 + j);
            const isAns  = j === item.answer;
            const isSel  = j === chosen;
            let cls = 'test-opt reviewed';
            if (isAns)      cls += ' opt-correct';
            else if (isSel) cls += ' opt-wrong';
            html += `<label class="${cls}">
                <input type="radio" disabled ${isSel ? 'checked' : ''}>
                <span class="test-opt-key">${letter}</span>
                <span class="test-opt-text">${escHtml(item.options[j])}</span>
                ${isAns ? '<span class="opt-tag correct-tag">✓ Đúng</span>' : (isSel ? '<span class="opt-tag wrong-tag">✗ Sai</span>' : '')}
            </label>`;
        }
        html += '</div>';
        if (item.explain) {
            html += `<p class="test-explain">💡 ${escHtml(item.explain)}</p>`;
        }
        html += '</div>';
    }
    html += '</div>';

    questionLabel.textContent = '';
    questionTitle.textContent = '';
    questionBody.innerHTML    = html;

    resultPanel.hidden      = false;
    resultTitle.textContent = isPractice40 ? '✓ Hoàn thành luyện tập' : '✓ Đã nộp bài';

    const wrong = total - correct;
    const pct   = Math.round(correct / total * 100);
    const score = (correct / total * 10).toFixed(1);
    resultText.innerHTML = `
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:10px;">
        <div style="padding:12px;border-radius:10px;background:#e8f5e9;text-align:center;">
            <div style="font-size:13px;color:#2e7d32;">Đúng</div>
            <div style="font-size:20px;font-weight:700;color:#1b5e20;">${correct}/${total}</div>
        </div>
        <div style="padding:12px;border-radius:10px;background:#ffebee;text-align:center;">
            <div style="font-size:13px;color:#c62828;">Sai</div>
            <div style="font-size:20px;font-weight:700;color:#b71c1c;">${wrong}</div>
        </div>
        <div style="padding:12px;border-radius:10px;background:#fff8e1;text-align:center;">
            <div style="font-size:13px;color:#f9a825;">Tỷ lệ</div>
            <div style="font-size:20px;font-weight:700;color:#f57f17;">${pct}%</div>
        </div>
        <div style="padding:12px;border-radius:10px;background:#e3f2fd;text-align:center;">
            <div style="font-size:13px;color:#1565c0;">Điểm</div>
            <div style="font-size:20px;font-weight:700;color:#0d47a1;">${score}/10</div>
        </div>
    </div>
    <div style="margin-top:12px;text-align:center;font-weight:600;font-size:14px;">
    ${pct >= 80 ? 'Xuất sắc 🏆' : pct >= 65 ? 'Khá 👍' : pct >= 50 ? 'Trung bình 📖' : 'Cần ôn thêm 💪'}
    </div>`;

    submitBtn.hidden  = true;
    restartBtn.hidden = false;
    buildQuestionNav();

    if (currentActivity.type === 'test') {
        stopTimer();
        const lessonId = getLessonId(currentActivityKey);
        if (lessonId) {
            saveScoreToServer(lessonId, correct, total, function(err, data) {
                if (!err && data && !data.error) showSaveIndicator('✓ Đã lưu điểm.', true);
                else showSaveIndicator('⚠ Không lưu được điểm.', false);
            });
        }
    }
}

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── RENDER BÀI LUYỆN TẬP THÔNG THƯỜNG (10 câu từng câu) ──────────
function renderMcq(item, index) {
    questionLabel.textContent = "Câu hỏi trắc nghiệm";
    questionTitle.textContent = item.prompt;
    questionBody.innerHTML = `
        <div class="options-list">
            ${item.options.map((option, optionIndex) => `
                <label class="option-item ${answers[index] === optionIndex ? 'selected' : ''}">
                    <input type="radio" name="question-option" value="${optionIndex}" ${answers[index] === optionIndex ? 'checked' : ''}>
                    <span class="option-dot"></span>
                    <span>${String.fromCharCode(65 + optionIndex)}. ${option}</span>
                </label>
            `).join("")}
        </div>
    `;
    questionBody.querySelectorAll('input[name="question-option"]').forEach((input) => {
        input.addEventListener("change", (e) => {
            answers[index] = Number(e.target.value);
            renderCurrentItem();
        });
    });
}

function renderCurrentItem() {
    if (!currentActivity) return;
    const isTestLike = currentActivity.type === 'test' || currentActivity.type === 'practice40';
    if (isTestLike) { renderTestView(); return; }

    const item  = currentActivity.items[currentIndex];
    const total = currentActivity.items.length;
    const score = scoreCurrentActivity();

    openShell();
    activityBoard.hidden = false;
    resultPanel.hidden   = true;
    workspaceLabel.textContent   = `LỊCH SỬ 12 · ${currentActivity.badge.toUpperCase()}`;
    workspaceHeading.textContent = currentActivity.title;
    activityBadge.textContent    = currentActivity.badge;
    activityNumber.textContent   = String(currentIndex + 1);
    activityProgress.textContent = `Câu ${currentIndex + 1} / ${total}`;
    activityScore.textContent    = `Điểm hiện tại: ${score}`;

    renderMcq(item, currentIndex);

    prevBtn.hidden    = currentIndex === 0;
    nextBtn.hidden    = currentIndex === total - 1;
    submitBtn.hidden  = currentIndex !== total - 1;
    restartBtn.hidden = false;
    updateSummary();
}

// ── OPEN / CLOSE ──────────────────────────────────────────────────
function openActivity(key) {
    const actDef = experiences[key];
    if (!actDef) return;
    resetTabCheck();
    currentActivityKey = key;
    // Nếu là getter (mixed), gọi lại để lấy items mới xáo
    currentActivity = {
        type: actDef.type,
        title: actDef.title,
        badge: actDef.badge,
        description: actDef.description,
        items: actDef.items  // getter tự gọi lại
    };
    currentIndex = 0;
    answers = new Array(currentActivity.items.length).fill(null);

    if (key.startsWith("practice40")) setSection("practice");
    else if (key.startsWith("practice"))     setSection("practice");
    else if (key.startsWith("test"))    setSection("test");
    else                                setSection("games");

    openShell();
    const isTestLike = currentActivity.type === 'test' || currentActivity.type === 'practice40';
    if (isTestLike) {
        renderTestView();
        if (currentActivity.type === 'test') startTimer();
        else stopTimer();
    } else {
        stopTimer();
        renderCurrentItem();
    }
}

function openShell() {
    experienceShell.classList.add("open");
    mainView.hidden = true;
    document.body.style.overflow = "hidden";
}
function closeShell() {
    experienceShell.classList.remove("open");
    mainView.hidden = false;
    document.body.style.overflow = "";
    stopTimer();
}

// ── NAV ───────────────────────────────────────────────────────────
function goPrev() {
    if (!currentActivity || currentActivity.type === 'test' || currentActivity.type === 'practice40') return;
    if (currentIndex > 0) { currentIndex -= 1; renderCurrentItem(); }
}
function goNext() {
    if (!currentActivity || currentActivity.type === 'test' || currentActivity.type === 'practice40') return;
    const item = currentActivity.items[currentIndex];
    if (!isFilled(answers[currentIndex])) {
        questionLabel.textContent = item.kind === "mcq" ? "Bạn chưa chọn đáp án" : "Bạn chưa nhập câu trả lời";
        return;
    }
    if (currentIndex < currentActivity.items.length - 1) { currentIndex += 1; renderCurrentItem(); }
}

// ── SUBMIT ────────────────────────────────────────────────────────
function submitCurrent(forceSubmit) {
    if (!currentActivity) return;
    const isTestLike = currentActivity.type === 'test' || currentActivity.type === 'practice40';
    if (isTestLike) {
        if (!forceSubmit) {
            const unanswered = answers.filter(a => a === null).length;
            if (unanswered > 0) {
                const ok = confirm(`Bạn còn ${unanswered} câu chưa trả lời. Bạn có chắc muốn nộp bài không?`);
                if (!ok) return;
            }
        }
        renderTestReview();
        return;
    }
    // Practice 10 câu
    const total    = currentActivity.items.length;
    const answered = answeredCountValue();
    const mcqC     = countMcqCorrect();
    const mcqT     = countMcqTotal();
    if (answered < total) {
        resultPanel.hidden = false;
        resultTitle.textContent = "Bạn chưa hoàn thành toàn bộ bài";
        resultText.textContent  = `Hiện bạn mới làm ${answered}/${total} câu.`;
        return;
    }
    resultPanel.hidden = false;
    resultTitle.textContent = "Bạn đã hoàn thành bài";
    resultText.textContent  = `Kết quả: ${mcqC}/${mcqT} câu đúng.`;

    if (!document.getElementById('saveIndicator')) {
        const ind = document.createElement('p');
        ind.id = 'saveIndicator';
        ind.style.cssText = 'margin-top:10px;font-size:13px;font-weight:700;display:none;';
        resultPanel.appendChild(ind);
    }
    const lessonId = getLessonId(currentActivityKey);
    if (lessonId && !currentActivityKey.startsWith('game')) {
        showSaveIndicator('Đang lưu điểm...', true);
        saveScoreToServer(lessonId, mcqC, mcqT, function(err, data) {
            if (err || !data || data.error) showSaveIndicator('⚠ Lưu điểm thất bại.', false);
            else showSaveIndicator('✓ Đã lưu điểm vào hệ thống.', true);
        });
    }
}

// ── UTILS ─────────────────────────────────────────────────────────
function getLessonId(key) {
    if (!key) return 0;
    const m = key.match(/lesson(\d+)/);
    return m ? parseInt(m[1]) : 0;
}
function countMcqCorrect() {
    if (!currentActivity) return 0;
    return currentActivity.items.reduce((sum, item, idx) => {
        return item.kind === 'mcq' ? sum + (answers[idx] === item.answer ? 1 : 0) : sum;
    }, 0);
}
function countMcqTotal() {
    if (!currentActivity) return 0;
    return currentActivity.items.filter(item => item.kind === 'mcq').length;
}
function saveScoreToServer(lessonId, score, totalQ, callback) {
    fetch('../../php/quiz_score.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ lesson_id: lessonId, score, total_q: totalQ })
    }).then(r => r.json()).then(data => callback(null, data)).catch(err => callback(err, null));
}
function showSaveIndicator(text, ok) {
    const el = document.getElementById('saveIndicator');
    if (!el) return;
    el.style.display = 'block';
    el.style.color   = ok ? '#2e7d32' : '#c0392b';
    el.textContent   = text;
}
function isFilled(value) {
    return typeof value === "string" ? value.trim().length > 0 : value !== null && value !== undefined;
}
function scoreCurrentActivity() {
    if (!currentActivity) return 0;
    return currentActivity.items.reduce((sum, item, index) => {
        return sum + (isFilled(answers[index]) ? (item.kind === "mcq" && answers[index] === item.answer ? 1 : 0) : 0);
    }, 0);
}
function answeredCountValue() {
    return answers.filter(v => isFilled(v)).length;
}
function updateSummary() {
    const answered = answeredCountValue();
    const total    = currentActivity ? currentActivity.items.length : 0;
    answeredCount.textContent = String(answered);
    totalCount.textContent    = String(total);
    summaryFill.style.width   = total ? `${(answered / total) * 100}%` : "0%";
}
function setSection(sectionId) {
    modeButtons.forEach(btn => btn.classList.toggle("active", btn.dataset.section === sectionId));
    contentPanels.forEach(panel => { panel.hidden = panel.id !== sectionId; });
    if (sectionId === "practice") {
        metricTitle.textContent  = "Bài luyện tập";
        metricFormat.textContent = "10 câu nhanh hoặc 40 câu đầy đủ";
        metricLevel.textContent  = "Luyện ngay sau bài học";
    } else if (sectionId === "test") {
        metricTitle.textContent  = "Đề kiểm tra";
        metricFormat.textContent = "40 câu trắc nghiệm — 50 phút";
        metricLevel.textContent  = "Tổng kết toàn bài";
    } else {
        metricTitle.textContent  = "Trò chơi";
        metricFormat.textContent = "Game nhanh theo từng bài";
        metricLevel.textContent  = "Vui, nhanh, dễ ôn";
    }
}

// ── EVENT LISTENERS ───────────────────────────────────────────────
prevBtn.addEventListener("click", goPrev);
nextBtn.addEventListener("click", goNext);
submitBtn.addEventListener("click", () => submitCurrent(false));
restartBtn.addEventListener("click", () => { if (currentActivityKey) openActivity(currentActivityKey); });
backBtn.addEventListener("click", closeShell);
modeButtons.forEach(btn => btn.addEventListener("click", () => setSection(btn.dataset.section)));
actionButtons.forEach(btn => btn.addEventListener("click", () => openActivity(btn.dataset.activity)));

setSection("practice");

// ── ANTI-CHEAT (chỉ cho đề kiểm tra) ─────────────────────────────
document.addEventListener("visibilitychange", function() {
    const isTesting = experienceShell.classList.contains("open") &&
                      currentActivity && currentActivity.type === 'test';
    if (isTesting && resultPanel.hidden) {
        if (document.visibilityState === 'hidden') {
            tabSwitchCount++;
            if (tabSwitchCount < MAX_TAB_SWITCH) {
                alert(`⚠️ CẢNH BÁO: Bạn không được rời khỏi màn hình làm bài!\nLần vi phạm ${tabSwitchCount}/${MAX_TAB_SWITCH}.`);
            } else {
                alert("❌ VI PHẠM NGHIÊM TRỌNG: Hệ thống sẽ tự động nộp bài ngay lập tức!");
                autoSubmitTest();
            }
        }
    }
});
function resetTabCheck() { tabSwitchCount = 0; }