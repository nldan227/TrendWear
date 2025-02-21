<?php 
    include '../layout/header.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrendWear</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <style>
        .swiper {
            width: 70%;
            margin: auto;
        }

        .swiper-wrapper {
            display: flex;
            align-items: center;
        }

        .swiper-slide {
            text-align: center;
        }

        .swiper-slide img {
            width: 90%;
            border-radius: 5px;
        }

        .swiper-button-next, .swiper-button-prev {
            color: black;
        }

        .clothes-female, .clothes-male, .clothes-kids {
            margin-top: 2%;
        }
        .category-title {
            display: block; /* Để có thể căn giữa như thẻ <h2> */
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: black;
            margin: 10px 0;
        }

        .category-title:hover {
            color: #0057ae; /* Màu khi hover */
        }
    </style>
</head>
<body>
    <div class="products">
        <div class="clothes-female">
            <a href="women" class="category-title">Thời Trang Nữ</a>
            <div class="swiper listProducts">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m1nopjnnbqz7f4.webp"><p>Áo</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-ljz34sdh4jr6fb.webp"><p>Giày, dép</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lmgw3ws6q21rd2.webp"><p>Quần</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m1nzwk41x3dfec.webp"><p>Váy</p></div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>

        <div class="clothes-male">
            <a href="men" class="category-title">Thời Trang Nam</a>
            <div class="swiper listProducts">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m3purjcx2ca0a7.webp"><p>Áo mùa đông</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lyekgm8ef1cx0e.webp"><p>Quần</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m2ca4p0u50okf3.webp"><p>Tất</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m33dbwuadoyj19.webp"><p>Áo khoác</p></div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>

        <div class="clothes-kids">
         <a href="kids" class="category-title">Thời Trang Trẻ Em</a>
            <div class="swiper listProducts">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-ly3hwlwb0e6bad.webp"><p>Áo thun</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m1w6xrhdfg43ae.webp"><p>Phụ kiện</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lzikr0aypxsda2.webp"><p>Áo mùa đông</p></div>
                    <div class="swiper-slide"><img src="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lzijlomhpy8xe1.webp"><p>Quần</p></div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
    <script>
   
    var swipers = document.querySelectorAll('.listProducts'); 
    swipers.forEach((swiperContainer, index) => {
        new Swiper(swiperContainer, {
            slidesPerView: 3,  
            spaceBetween: 20,  
            loop: true,  // Cho phép lặp lại slider
            navigation: {
                nextEl: swiperContainer.querySelector(".swiper-button-next"),
                prevEl: swiperContainer.querySelector(".swiper-button-prev"),
            },
            pagination: {
                el: swiperContainer.querySelector(".swiper-pagination"),
                clickable: true,
            },
            breakpoints: {  
                1024: {
                    slidesPerView: 3 
                },
                768: {
                    slidesPerView: 3
                },
                480: {
                    slidesPerView: 2
                }
            }
        });
    });

    </script>
</body>
</html>