<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Ormawa - Universitas Negeri Padang</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&family=Kalam:wght@300;400;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Custom Properties - Modern Academia Theme */
        :root {
            --primary-blue: #0D47A1;
            --accent-orange: #FF9800;
            --text-dark: #212121;
            --text-secondary: #616161;
            --bg-main: #FAFAFA;
            --border-color: #EEEEEE;
            --success-green: #4CAF50;
            --error-red: #D32F2F;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Typography System */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Lora', serif;
            font-weight: 600;
            line-height: 1.2;
        }

        h1 { font-size: 2.5rem; }
        h2 { font-size: 2rem; }
        h3 { font-size: 1.5rem; }
        h4 { font-size: 1.25rem; }
        h5 { font-size: 1.125rem; }
        h6 { font-size: 1rem; }

        .font-kalam {
            font-family: 'Kalam', cursive;
        }

        /* Container System */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (min-width: 640px) {
            .container { padding: 0 1.5rem; }
        }

        @media (min-width: 1024px) {
            .container { padding: 0 2rem; }
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #1565C0 100%);
            color: white;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-md);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-family: 'Lora', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--accent-orange);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent-orange);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 4rem 0;
        }

        .hero-content {
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1565C0;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background-color: white;
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
        }

        .btn-secondary:hover {
            background-color: var(--primary-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Decorative Elements */
        .hero-decoration {
            position: absolute;
            border-radius: 50%;
            filter: blur(3rem);
            opacity: 0.3;
        }

        .hero-decoration-1 {
            top: -5rem;
            right: -5rem;
            width: 20rem;
            height: 20rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-orange));
        }

        .hero-decoration-2 {
            bottom: -5rem;
            left: -5rem;
            width: 15rem;
            height: 15rem;
            background: linear-gradient(135deg, var(--accent-orange), var(--primary-blue));
        }

        /* Filter Bar */
        .filter-bar {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-md);
            margin: 2rem 0;
            border: 1px solid var(--border-color);
        }

        .category-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .category-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            border: 1px solid var(--border-color);
            background: white;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-btn.active {
            background-color: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        .category-btn:hover:not(.active) {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .filters-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .filter-select {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            background: white;
            color: var(--text-dark);
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.1);
        }

        /* Organization Cards Grid */
        .organizations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        /* Organization Card Component */
        .org-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
        }

        .org-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .org-image {
            height: 12rem;
            background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
            position: relative;
            overflow: hidden;
        }

        .org-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .org-content {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .org-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .org-title {
            font-family: 'Lora', serif;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary-blue);
            line-height: 1.3;
            flex: 1;
            margin-right: 0.5rem;
        }

        .org-category {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            white-space: nowrap;
        }

        .category-bem {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .category-ukm {
            background-color: #dcfce7;
            color: #166534;
        }

        .category-hima {
            background-color: #fef3c7;
            color: #92400e;
        }

        .org-faculty {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
        }

        .org-description {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .org-footer {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .org-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            overflow: hidden;
        }

        .org-logo {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            object-fit: cover;
        }

        .org-acronym {
            font-weight: 600;
            font-size: 0.875rem;
            color: #374151;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .org-detail-link {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: gap 0.3s ease;
        }

        .org-detail-link:hover {
            gap: 0.5rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--primary-blue);
                flex-direction: column;
                padding: 1rem;
                gap: 1rem;
            }

            .nav-menu.active {
                display: flex;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .organizations-grid {
                grid-template-columns: 1fr;
            }

            .filters-row {
                grid-template-columns: 1fr;
            }
        }

        /* Loading State */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus Styles */
        *:focus-visible {
            outline: 2px solid var(--accent-orange);
            outline-offset: 2px;
        }

        /* Skip to main content */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--primary-blue);
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 100;
        }

        .skip-link:focus {
            top: 6px;
        }

        /* Section Titles */
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 3rem 0 2rem;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-orange));
            border-radius: 2px;
        }

        /* Carousel Styles */
        .featured-section {
            margin: 3rem 0;
        }

        .carousel-container {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: white;
            box-shadow: var(--shadow-lg);
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease;
            animation: carouselScroll 60s linear infinite;
        }

        .carousel-track:hover {
            animation-play-state: paused;
        }

        @keyframes carouselScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .carousel-item {
            min-width: 300px;
            margin: 1rem;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .carousel-item:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .carousel-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .carousel-content {
            padding: 1rem;
        }

        .carousel-title {
            font-family: 'Lora', serif;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .carousel-description {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            box-shadow: var(--shadow-md);
        }

        .carousel-btn:hover {
            background: white;
            box-shadow: var(--shadow-lg);
        }

        .carousel-prev {
            left: 1rem;
        }

        .carousel-next {
            right: 1rem;
        }

        /* Bulletin Board Styles */
        .bulletin-section {
            margin: 3rem 0;
        }

        .bulletin-board {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #8B4513 100%);
            background-image: 
                repeating-linear-gradient(90deg, transparent, transparent 10px, rgba(0,0,0,.1) 10px, rgba(0,0,0,.1) 20px),
                repeating-linear-gradient(0deg, transparent, transparent 10px, rgba(0,0,0,.1) 10px, rgba(0,0,0,.1) 20px),
                linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #8B4513 100%);
            border-radius: 1rem;
            padding: 2rem;
            min-height: 400px;
            position: relative;
            box-shadow: var(--shadow-xl);
        }

        .bulletin-board::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none" stroke="rgba(0,0,0,0.1)" stroke-width="0.5"/></svg>');
            opacity: 0.3;
            border-radius: 1rem;
        }

        .bulletin-item {
            position: relative;
            background: linear-gradient(135deg, #fff9e6 0%, #fffef9 100%);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin: 1rem;
            box-shadow: 
                0 4px 6px rgba(0, 0, 0, 0.1),
                0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            max-width: 280px;
            z-index: 1;
        }

        .bulletin-item:hover {
            transform: scale(1.05) rotate(0deg) !important;
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
            z-index: 2;
        }

        .bulletin-pin {
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 16px;
            height: 16px;
            background: radial-gradient(circle, #ff4444 0%, #cc0000 100%);
            border-radius: 50%;
            box-shadow: 
                0 2px 4px rgba(0, 0, 0, 0.3),
                inset 0 -1px 2px rgba(0, 0, 0, 0.2);
            z-index: 3;
        }

        .bulletin-pin::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
        }

        .bulletin-content h3 {
            font-family: 'Lora', serif;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .bulletin-date {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--accent-orange);
            margin-bottom: 0.75rem;
        }

        .bulletin-content p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.4;
            margin-bottom: 1rem;
        }

        .bulletin-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .bulletin-link:hover {
            color: var(--accent-orange);
        }

        /* Responsive adjustments for carousel and bulletin */
        @media (max-width: 768px) {
            .carousel-item {
                min-width: 250px;
            }
            
            .bulletin-board {
                padding: 1rem;
            }
            
            .bulletin-item {
                max-width: 100%;
                margin: 0.5rem 0;
            }
            
            .carousel-btn {
                width: 2.5rem;
                height: 2.5rem;
            }
            
            .carousel-prev {
                left: 0.5rem;
            }
            
            .carousel-next {
                right: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Header -->
    <header class="header" role="banner">
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    Ormawa UNP
                </a>
                
                <nav role="navigation" aria-label="Main navigation">
                    <ul class="nav-menu" id="navMenu">
                        <li><a href="#" class="nav-link">Beranda</a></li>
                        <li><a href="#" class="nav-link">Pengumuman</a></li>
                        <li><a href="#" class="nav-link">Berita</a></li>
                        <li><a href="#" class="nav-link">Ormawa</a></li>
                        <li><a href="#" class="nav-link">Kalender</a></li>
                        <li>
                            <button class="btn btn-secondary">
                                <i class="fas fa-user"></i>
                                Masuk
                            </button>
                        </li>
                    </ul>
                    
                    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation menu">
                        <i class="fas fa-bars"></i>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" role="banner">
        <div class="hero-decoration hero-decoration-1"></div>
        <div class="hero-decoration hero-decoration-2"></div>
        
        <div class="container">
            <div class="hero-content">
                <h1>
                    Selamat Datang di<br>
                    <span class="text-gradient font-kalam">Portal Ormawa</span>
                </h1>
                <p>
                    Platform terpadu untuk organisasi mahasiswa Universitas Negeri Padang
                </p>
                <div class="cta-buttons">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Jelajahi Ormawa
                    </a>
                    <a href="#" class="btn btn-secondary">
                        <i class="fas fa-info-circle"></i>
                        Tentang Platform
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main id="main-content" class="container" role="main">
        <!-- Filter Bar -->
        <section class="filter-bar" aria-label="Filter organizations">
            <div class="category-buttons" role="group" aria-label="Filter by category">
                <button class="category-btn active" data-category="all">Semua</button>
                <button class="category-btn" data-category="bem">BEM</button>
                <button class="category-btn" data-category="ukm">UKM</button>
                <button class="category-btn" data-category="hima">HIMA</button>
            </div>
            
            <div class="filters-row">
                <div class="filter-group">
                    <label for="faculty-filter" class="filter-label">Fakultas</label>
                    <select id="faculty-filter" class="filter-select">
                        <option value="">Semua Fakultas</option>
                        <option value="fip">FIP</option>
                        <option value="fbs">FBS</option>
                        <option value="fis">FIS</option>
                        <option value="fmipa">FMIPA</option>
                        <option value="ft">FT</option>
                        <option value="fikep">FIKEP</option>
                        <option value="fpp">FPP</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sort-filter" class="filter-label">Urutkan</label>
                    <select id="sort-filter" class="filter-select">
                        <option value="name">Nama A-Z</option>
                        <option value="name-desc">Nama Z-A</option>
                        <option value="newest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <button class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                        Reset
                    </button>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <input type="text" class="search-input" placeholder="Cari di nama, deskripsi, visi, misi, divisi..." aria-label="Search organizations">
            </div>
        </section>

        <!-- Featured Organizations Carousel -->
        <section class="featured-section" aria-label="Featured organizations">
            <h2 class="section-title">Ormawa Unggulan</h2>
            <div class="carousel-container">
                <div class="carousel-track" id="carouselTrack">
                    <!-- Carousel items will be dynamically generated -->
                </div>
                <button class="carousel-btn carousel-prev" id="carouselPrev" aria-label="Previous slide">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn carousel-next" id="carouselNext" aria-label="Next slide">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </section>

        <!-- Announcements Bulletin Board -->
        <section class="bulletin-section" aria-label="Announcements">
            <h2 class="section-title">Papan Pengumuman</h2>
            <div class="bulletin-board">
                <div class="bulletin-item" style="transform: rotate(-2deg);">
                    <div class="bulletin-pin"></div>
                    <div class="bulletin-content">
                        <h3>Penerimaan Anggota Baru UKM</h3>
                        <p class="bulletin-date">15 November 2024</p>
                        <p>Dibuka pendaftaran anggota baru untuk semua Unit Kegiatan Mahasiswa. Daftar sekarang!</p>
                        <a href="#" class="bulletin-link">Selengkapnya →</a>
                    </div>
                </div>
                
                <div class="bulletin-item" style="transform: rotate(1deg);">
                    <div class="bulletin-pin"></div>
                    <div class="bulletin-content">
                        <h3>Lomba Karya Tulis Ilmiah</h3>
                        <p class="bulletin-date">20 November 2024</p>
                        <p>BEM UNP menyelenggarakan lomba karya tulis ilmiah tingkat universitas dengan total hadiah jutaan rupiah.</p>
                        <a href="#" class="bulletin-link">Selengkapnya →</a>
                    </div>
                </div>
                
                <div class="bulletin-item" style="transform: rotate(-1deg);">
                    <div class="bulletin-pin"></div>
                    <div class="bulletin-content">
                        <h3>Pelatihan Kepemimpinan</h3>
                        <p class="bulletin-date">25 November 2024</p>
                        <p>Ikuti pelatihan kepemimpinan untuk pengurus ormawa tingkat fakultas dan universitas.</p>
                        <a href="#" class="bulletin-link">Selengkapnya →</a>
                    </div>
                </div>
                
                <div class="bulletin-item" style="transform: rotate(2deg);">
                    <div class="bulletin-pin"></div>
                    <div class="bulletin-content">
                        <h3>Seminar Nasional</h3>
                        <p class="bulletin-date">30 November 2024</p>
                        <p>HIMA FIS menyelenggarakan seminar nasional dengan tema "Transformasi Digital di Era Society 5.0".</p>
                        <a href="#" class="bulletin-link">Selengkapnya →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Organizations Grid -->
        <section aria-label="Organizations list">
            <h2 class="section-title">Semua Ormawa</h2>
            <div class="organizations-grid" id="organizationsGrid">
                <!-- Sample Organization Cards -->
                <article class="org-card">
                    <div class="org-image">
                        <img src="https://via.placeholder.com/300x200/0D47A1/FFFFFF?text=BEM+UNP" alt="BEM UNP Logo">
                    </div>
                    <div class="org-content">
                        <div class="org-header">
                            <h3 class="org-title">Badan Eksekutif Mahasiswa Universitas Negeri Padang</h3>
                            <span class="org-category category-bem">BEM</span>
                        </div>
                        <p class="org-faculty">Universitas</p>
                        <p class="org-description">
                            Badan Eksekutif Mahasiswa adalah lembaga eksekutif tertinggi di tingkat universitas yang mewakili seluruh mahasiswa UNP dalam menjalankan program kerja dan aspirasi kemahasiswaan.
                        </p>
                        <div class="org-footer">
                            <div class="org-info">
                                <img src="https://via.placeholder.com/32x32/0D47A1/FFFFFF?text=B" alt="BEM" class="org-logo">
                                <span class="org-acronym">BEM UNP</span>
                            </div>
                            <a href="#" class="org-detail-link">
                                Detail
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <article class="org-card">
                    <div class="org-image">
                        <img src="https://via.placeholder.com/300x200/4CAF50/FFFFFF?text=UKM+Paduan+Suara" alt="UKM Paduan Suara Logo">
                    </div>
                    <div class="org-content">
                        <div class="org-header">
                            <h3 class="org-title">Unit Kegiatan Mahasiswa Paduan Suara Symphony</h3>
                            <span class="org-category category-ukm">UKM</span>
                        </div>
                        <p class="org-faculty">Universitas</p>
                        <p class="org-description">
                            UKM Paduan Suara Symphony adalah wadah bagi mahasiswa yang memiliki minat dan bakat dalam bidang vokal untuk mengembangkan potensi dan menyalurkan hobi dalam bernyanyi.
                        </p>
                        <div class="org-footer">
                            <div class="org-info">
                                <img src="https://via.placeholder.com/32x32/4CAF50/FFFFFF?text=S" alt="Symphony" class="org-logo">
                                <span class="org-acronym">Symphony</span>
                            </div>
                            <a href="#" class="org-detail-link">
                                Detail
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <article class="org-card">
                    <div class="org-image">
                        <img src="https://via.placeholder.com/300x200/FF9800/FFFFFF?text=HIMA+Matematika" alt="HIMA Matematika Logo">
                    </div>
                    <div class="org-content">
                        <div class="org-header">
                            <h3 class="org-title">Himpunan Mahasiswa Matematika</h3>
                            <span class="org-category category-hima">HIMA</span>
                        </div>
                        <p class="org-faculty">FMIPA</p>
                        <p class="org-description">
                            Himpunan Mahasiswa Matematika adalah organisasi kemahasiswaan yang menaungi seluruh mahasiswa Program Studi Matematika FMIPA UNP.
                        </p>
                        <div class="org-footer">
                            <div class="org-info">
                                <img src="https://via.placeholder.com/32x32/FF9800/FFFFFF?text=M" alt="Matematika" class="org-logo">
                                <span class="org-acronym">HIMA Mat</span>
                            </div>
                            <a href="#" class="org-detail-link">
                                Detail
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <article class="org-card">
                    <div class="org-image">
                        <img src="https://via.placeholder.com/300x200/0D47A1/FFFFFF?text=UKM+Basket" alt="UKM Basket Logo">
                    </div>
                    <div class="org-content">
                        <div class="org-header">
                            <h3 class="org-title">Unit Kegiatan Mahasiswa Basket</h3>
                            <span class="org-category category-ukm">UKM</span>
                        </div>
                        <p class="org-faculty">Universitas</p>
                        <p class="org-description">
                            UKM Basket adalah wadah bagi mahasiswa pecinta olahraga basket untuk berlatih, berkompetisi, dan mengembangkan kemampuan dalam permainan basket.
                        </p>
                        <div class="org-footer">
                            <div class="org-info">
                                <img src="https://via.placeholder.com/32x32/0D47A1/FFFFFF?text=B" alt="Basket" class="org-logo">
                                <span class="org-acronym">UKM Basket</span>
                            </div>
                            <a href="#" class="org-detail-link">
                                Detail
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="container" style="margin-top: 4rem; padding: 2rem 0; border-top: 1px solid var(--border-color); text-align: center; color: var(--text-secondary);" role="contentinfo">
        <p>&copy; 2024 Portal Ormawa Universitas Negeri Padang. All rights reserved.</p>
    </footer>

    <script>
        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const navMenu = document.getElementById('navMenu');

        mobileMenuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            const icon = mobileMenuToggle.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Category Filter
        const categoryButtons = document.querySelectorAll('.category-btn');
        
        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                button.classList.add('active');
                
                // Here you would typically filter the organizations
                const category = button.dataset.category;
                console.log('Filter by category:', category);
            });
        });

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value;
                console.log('Search for:', searchTerm);
                // Implement search logic here
            }, 300);
        });

        // Faculty and Sort filters
        const facultyFilter = document.getElementById('faculty-filter');
        const sortFilter = document.getElementById('sort-filter');

        facultyFilter.addEventListener('change', (e) => {
            console.log('Faculty filter:', e.target.value);
            // Implement faculty filter logic here
        });

        sortFilter.addEventListener('change', (e) => {
            console.log('Sort by:', e.target.value);
            // Implement sort logic here
        });

        // Organization card click handlers
        const orgCards = document.querySelectorAll('.org-card');
        
        orgCards.forEach(card => {
            card.addEventListener('click', (e) => {
                // Prevent navigation if clicking on the detail link
                if (e.target.closest('.org-detail-link')) {
                    return;
                }
                
                // Navigate to organization detail page
                console.log('Navigate to organization details');
                // window.location.href = '/organization-details';
            });
        });

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading animation for images
        const images = document.querySelectorAll('.org-image img');
        images.forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '0';
                setTimeout(() => {
                    this.style.transition = 'opacity 0.3s ease';
                    this.style.opacity = '1';
                }, 100);
            });
        });

        // Initialize Carousel
        const carouselTrack = document.getElementById('carouselTrack');
        const carouselPrev = document.getElementById('carouselPrev');
        const carouselNext = document.getElementById('carouselNext');
        
        // Sample carousel data (in real app, this would come from backend)
        const carouselData = [
            {
                title: "UKM Paduan Suara Symphony",
                description: "Mengembangkan bakat vokal dan musik mahasiswa UNP",
                image: "https://via.placeholder.com/300x160/4CAF50/FFFFFF?text=Paduan+Suara"
            },
            {
                title: "BEM FMIPA",
                description: "Badan Eksekutif Mahasiswa Fakultas MIPA",
                image: "https://via.placeholder.com/300x160/0D47A1/FFFFFF?text=BEM+FMIPA"
            },
            {
                title: "UKM Basket",
                description: "Wadah penggemar olahraga basket UNP",
                image: "https://via.placeholder.com/300x160/FF9800/FFFFFF?text=UKM+Basket"
            },
            {
                title: "HIMA Matematika",
                description: "Himpunan Mahasiswa Program Studi Matematika",
                image: "https://via.placeholder.com/300x160/9C27B0/FFFFFF?text=HIMA+Matematika"
            },
            {
                title: "UKM Teater",
                description: "Menyalurkan bakat seni peran mahasiswa",
                image: "https://via.placeholder.com/300x160/E91E63/FFFFFF?text=UKM+Teater"
            }
        ];

        // Generate carousel items (duplicate for infinite scroll)
        function generateCarouselItems() {
            const itemsHTML = carouselData.map(item => `
                <div class="carousel-item">
                    <img src="${item.image}" alt="${item.title}" loading="lazy">
                    <div class="carousel-content">
                        <h3 class="carousel-title">${item.title}</h3>
                        <p class="carousel-description">${item.description}</p>
                    </div>
                </div>
            `).join('');
            
            // Duplicate items for infinite scroll effect
            carouselTrack.innerHTML = itemsHTML + itemsHTML;
        }

        // Carousel controls
        let currentTransform = 0;
        const itemWidth = 332; // 300px + margins

        carouselPrev.addEventListener('click', () => {
            currentTransform += itemWidth;
            carouselTrack.style.transform = `translateX(${currentTransform}px)`;
            carouselTrack.style.animation = 'none';
            
            // Reset animation after manual control
            setTimeout(() => {
                carouselTrack.style.animation = 'carouselScroll 60s linear infinite';
                currentTransform = 0;
            }, 500);
        });

        carouselNext.addEventListener('click', () => {
            currentTransform -= itemWidth;
            carouselTrack.style.transform = `translateX(${currentTransform}px)`;
            carouselTrack.style.animation = 'none';
            
            // Reset animation after manual control
            setTimeout(() => {
                carouselTrack.style.animation = 'carouselScroll 60s linear infinite';
                currentTransform = 0;
            }, 500);
        });

        // Bulletin board interactions
        const bulletinItems = document.querySelectorAll('.bulletin-item');
        
        bulletinItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Prevent navigation if clicking on the link
                if (e.target.closest('.bulletin-link')) {
                    return;
                }
                
                // Add a subtle animation
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
                
                console.log('Bulletin item clicked');
                // Navigate to announcement details
            });
        });

        // Initialize carousel on page load
        generateCarouselItems();
    </script>
</body>
</html>