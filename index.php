<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <link href="Storage/Index/Logo.png" rel="shortcut icon">

  <title>Welcome to NEXORA - Global Premium Trading Plateform</title>

  <link rel="stylesheet" href="style.css" />

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <!-- <script src="script.js"></script> -->

</head>
<body>

  <!-- NAVBAR -->
  <header class="navbar">
    <div class="flex-shrink-0 flex items-center">
        <a href="index.html" class="flex items-center">
            <img src="Storage/Index/Logo.png" 
                alt="NEXORA Global Trading" 
                class="h-12 w-auto"
                height="40px"
                width="60px">
        </a>
    </div>

  <nav class="nav-menu">

    <div class="nav-item">
      <button class="nav-btn">Trading ▾</button>
      <div class="dropdown">
        <a href="#">Forex</a>
        <a href="#">Cryptocurrencies</a>
        <a href="#">Stocks</a>
        <a href="#">Indices</a>
        <a href="#">ETFs</a>
      </div>
    </div>

    <div class="nav-item">
      <button class="nav-btn">System ▾</button>
      <div class="dropdown">
        <a href="#">How It Works</a>
        <a href="#">Demo Account</a>
        <a href="#">Trading Rules</a>
      </div>
    </div>

    <div class="nav-item">
      <button class="nav-btn">Company ▾</button>
      <div class="dropdown">
        <a href="#">About Us</a>
        <a href="#">Careers</a>
        <a href="#">Partners</a>
      </div>
    </div>

    <div class="nav-item">
      <button class="nav-btn">Education ▾</button>
      <div class="dropdown">
        <a href="#">Trading Basics</a>
        <a href="#">Market Analysis</a>
        <a href="#">Tutorials</a>
      </div>
    </div>

    <a href="contact.php" class="nav-link">Contact</a>
  </nav>

    <div class="nav-actions">
      <a href="user/login.php" class="login">Log in</a>
      <a href="user/register.php" class="signup">Sign up</a>
    </div>
  </header>

  <!-- Main Content Area -->
<main id="main-content" class="flex-grow">

  <!-- MARKET TICKER -->
    <div class="bg-dark-300 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-1">
                <div class="crypto-ticker-wrapper">
                    <iframe src="https://widget.coinlib.io/widget?type=horizontal_v2&theme=dark&pref_coin_id=1505&invert_hover=no" width="100%" height="36px" scrolling="auto" marginwidth="0" marginheight="0" frameborder="0" class="w-full">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- HERO CONTENT -->
  <section class="hero">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <span class="hero-badge">INNOVATIVE TRADING PLATFORM</span>

        <h1>Trade Global Markets<br><span>With Confidence</span></h1>
      </div>

    <p>
      Access advanced trading tools for Forex, Cryptocurrencies,
      Stocks, Indices, and <br> more with competitive spreads and
      lightning-fast execution.
    </p>

    <div class="hero-buttons">
      <a href="user/register.php" class="btn-primary" style="text-decoration: none;">Sign Up</a>
      <a href="user/login.php" class="btn-secondary" style="text-decoration: none;">Login</a>
    </div>
    </div>
  </section>

  <!-- WHY TRADE WITH US -->
<section class="why-trade">

  <h2>Why Trade With Nexora</h2>
  <p class="subtitle">Everything you need for successful trading</p>

  <div class="features">

    <div class="feature-card">
      <div class="icon blue">
        📈
      </div>
      <h3>Trading Tools</h3>
      <p>
        Plan your trades effectively with our wide range of free
        professional trading tools.
      </p>
    </div>

    <div class="feature-card">
      <div class="icon green">
        📚
      </div>
      <h3>Trading Products</h3>
      <p>
        Diverse opportunities to optimize your trading portfolio
        across multiple markets.
      </p>
    </div>

    <div class="feature-card">
      <div class="icon blue">
        🖥️
      </div>
      <h3>Trading Platforms</h3>
      <p>
        Powerful platforms to suit all trading style.
      </p>
    </div>
</section>

<!-- Platform Features Section -->
<section class="platform-features">
    <div>
        <div class="ptfeatures-header">
            <span class="ptfeatures-badge">
                PLATFORM FEATURES
            </span>
            <h2>Advanced Trading Tools</h2>
            <p>Our platform provides everything you need for successful trading in one powerful interface</p>
        </div>

        <div class="ptfeatures-grid">
            <!-- Feature 1 -->
            <div class="ptfeatures-card">
                <div class="ptfeatures-icon1">
                    <i class="fas fa-bolt text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Reliable <span style="color: rgb(45, 45, 159);">Execution</span></h3>
                <p class="text-gray-300">
                    Featuring the market's sharpest execution, Nexora cTrader fills your orders in milliseconds without any requotes or price manipulation.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="ptfeatures-card">
                <div class="ptfeatures-icon2">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Intelligent <span style="color: #0e11d4;">Analysis</span></h3>
                <p class="text-gray-300">
                    Make informed decisions with smart market analysis tools, Live Sentiment data and in-platform market insights from Trading Central.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="ptfeatures-card">
                <div class="ptfeatures-icon3">
                    <i class="fas fa-search-dollar text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Transparent <span style="color: rgb(39, 116, 211);">Reporting</span></h3>
                <p class="text-gray-300">
                    Access transaction statistics, equity charts and detailed history of your deals for a crystal clear understanding of your performance.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="ptfeatures-card">
                <div class="ptfeatures-icon4">
                    <i class="fas fa-desktop text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Intuitive <span style="color: limegreen;">Interface</span></h3>
                <p class="text-gray-300">
                    Easy to use and navigate, Nexora cTrader was built with real traders' needs in mind. Trade with Nexora cTrader and experience its distinct advantage.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Market Analysis Section -->
<section class="market-analysis">
    <div class="mb-12 text-center">
        <span class="real-time">
                Real-Time Intelligence
        </span>
        <h2>Market Analysis & Insights</h2>
        <p id= "market" class="mt-3 text-gray-300 max-w-2xl mx-auto">Stay ahead with real-time market data, AI-powered insights, and expert analysis.</p>
    </div>

    <div class="market-grid">
            <!-- Market Overview Chart -->
        <div class="market-card-1">
            <h3>Live Market Overview</h3>

                <!-- TradingView Widget -->
            <div class="tradingview-widget-container" style="width: 100%; height: 570px;">
                <iframe scrolling="no" allowtransparency="true" frameborder="0" src="https://www.tradingview-widget.com/embed-widget/market-overview/?locale=en#%7B%22colorTheme%22%3A%22dark%22%2C%22dateRange%22%3A%2212M%22%2C%22showChart%22%3Atrue%2C%22largeChartUrl%22%3A%22%22%2C%22isTransparent%22%3Afalse%2C%22showSymbolLogo%22%3Atrue%2C%22showFloatingTooltip%22%3Afalse%2C%22width%22%3A%22100%25%22%2C%22height%22%3A500%2C%22plotLineColorGrowing%22%3A%22rgba(0%2C%20255%2C%200%2C%201)%22%2C%22plotLineColorFalling%22%3A%22rgba(255%2C%200%2C%200%2C%201)%22%2C%22gridLineColor%22%3A%22rgba(42%2C%2046%2C%2057%2C%200.5)%22%2C%22scaleFontColor%22%3A%22rgba(106%2C%20109%2C%20120%2C%201)%22%2C%22belowLineFillColorGrowing%22%3A%22rgba(41%2C%2098%2C%20255%2C%200.12)%22%2C%22belowLineFillColorFalling%22%3A%22rgba(41%2C%2098%2C%20255%2C%200.12)%22%2C%22belowLineFillColorGrowingBottom%22%3A%22rgba(41%2C%2098%2C%20255%2C%200)%22%2C%22belowLineFillColorFallingBottom%22%3A%22rgba(41%2C%2098%2C%20255%2C%200)%22%2C%22symbolActiveColor%22%3A%22rgba(41%2C%2098%2C%20255%2C%200.12)%22%2C%22tabs%22%3A%5B%7B%22title%22%3A%22Indices%22%2C%22symbols%22%3A%5B%7B%22s%22%3A%22FOREXCOM%3ASPXUSD%22%2C%22d%22%3A%22S%26P%20500%22%7D%2C%7B%22s%22%3A%22FOREXCOM%3ANSXUSD%22%2C%22d%22%3A%22US%20100%22%7D%2C%7B%22s%22%3A%22FOREXCOM%3ADJI%22%2C%22d%22%3A%22Dow%2030%22%7D%2C%7B%22s%22%3A%22INDEX%3ANKY%22%2C%22d%22%3A%22Nikkei%20225%22%7D%2C%7B%22s%22%3A%22INDEX%3ADEU40%22%2C%22d%22%3A%22DAX%20Index%22%7D%2C%7B%22s%22%3A%22FOREXCOM%3AUKXGBP%22%2C%22d%22%3A%22UK%20100%22%7D%5D%2C%22originalTitle%22%3A%22Indices%22%7D%2C%7B%22title%22%3A%22Futures%22%2C%22symbols%22%3A%5B%7B%22s%22%3A%22CME_MINI%3AES1!%22%2C%22d%22%3A%22S%26P%20500%22%7D%2C%7B%22s%22%3A%22CME%3A6E1!%22%2C%22d%22%3A%22Euro%22%7D%2C%7B%22s%22%3A%22COMEX%3AGC1!%22%2C%22d%22%3A%22Gold%22%7D%2C%7B%22s%22%3A%22NYMEX%3ACL1!%22%2C%22d%22%3A%22Crude%20Oil%22%7D%2C%7B%22s%22%3A%22NYMEX%3ANG1!%22%2C%22d%22%3A%22Natural%20Gas%22%7D%2C%7B%22s%22%3A%22CBOT%3AZC1!%22%2C%22d%22%3A%22Corn%22%7D%5D%2C%22originalTitle%22%3A%22Futures%22%7D%2C%7B%22title%22%3A%22Bonds%22%2C%22symbols%22%3A%5B%7B%22s%22%3A%22CME%3AGE1!%22%2C%22d%22%3A%22Eurodollar%22%7D%2C%7B%22s%22%3A%22CBOT%3AZB1!%22%2C%22d%22%3A%22T-Bond%22%7D%2C%7B%22s%22%3A%22CBOT%3AUB1!%22%2C%22d%22%3A%22Ultra%20T-Bond%22%7D%2C%7B%22s%22%3A%22EUREX%3AFGBL1!%22%2C%22d%22%3A%22Euro%20Bund%22%7D%2C%7B%22s%22%3A%22EUREX%3AFBTP1!%22%2C%22d%22%3A%22Euro%20BTP%22%7D%2C%7B%22s%22%3A%22EUREX%3AFGBM1!%22%2C%22d%22%3A%22Euro%20BOBL%22%7D%5D%2C%22originalTitle%22%3A%22Bonds%22%7D%2C%7B%22title%22%3A%22Forex%22%2C%22symbols%22%3A%5B%7B%22s%22%3A%22FX%3AEURUSD%22%2C%22d%22%3A%22EUR%2FUSD%22%7D%2C%7B%22s%22%3A%22FX%3AGBPUSD%22%2C%22d%22%3A%22GBP%2FUSD%22%7D%2C%7B%22s%22%3A%22FX%3AUSDJPY%22%2C%22d%22%3A%22USD%2FJPY%22%7D%2C%7B%22s%22%3A%22FX%3AUSDCHF%22%2C%22d%22%3A%22USD%2FCHF%22%7D%2C%7B%22s%22%3A%22FX%3AAUDUSD%22%2C%22d%22%3A%22AUD%2FUSD%22%7D%2C%7B%22s%22%3A%22FX%3AUSDCAD%22%2C%22d%22%3A%22USD%2FCAD%22%7D%5D%2C%22originalTitle%22%3A%22Forex%22%7D%5D%2C%22utm_source%22%3A%22webbo.online%22%2C%22utm_medium%22%3A%22widget%22%2C%22utm_campaign%22%3A%22market-overview%22%2C%22page-uri%22%3A%22webbo.online%2F%22%7D" title="market overview TradingView widget" lang="en" style="user-select: none; box-sizing: border-box; display: block; height: 100%; width: 100%;"></iframe>
            </div>
        </div>

                <!-- Expert Analysis Content -->
        <div class="market-card-2">
                <h3 class="analysis-title">Expert Market Analysis</h3>
                <div class="space-y-6">

                        <!-- Analysis Card 1 -->
                    <div class="analysis-item" x-data="{ expanded: false }">
                        <div class="analysis-icon">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-600 to-blue-700 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-medium text-white">Daily Market <span class="text-emerald-400">Updates</span></h4>
                            <p class="mt-2 text-gray-300 line-clamp-2" :class="expanded ? '' : 'line-clamp-2'">
                                    Receive daily market analysis directly to your mail. Our team of expert analysts provide actionable insights on market trends, price movements, and trading opportunities across all major asset classes.
                            </p>
                            <a href="#">
                                <button class="analysis-read-more">
                                    Read more
                                </button>
                            </a>
                        </div>
                    </div>

                        <!-- Analysis Card 2 -->
                    <div class="analysis-item" x-data="{ expanded: false }">
                        <div class="analysis-icon">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-600 to-blue-700 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-tools text-white"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-medium text-white">Premium Trading <span class="text-blue-400">Tools</span></h4>
                            <p class="mt-2 text-gray-300 line-clamp-2" :class="expanded ? '' : 'line-clamp-2'">
                                    Access advanced trading tools designed for all experience levels. Our platform offers customizable solutions to meet diverse trading needs and styles, with multi-language support for international traders.
                            </p>
                            <a href="#">
                                <button class="analysis-read-more">
                                    Read more
                                </button>
                            </a>
                        </div>
                    </div>

                        <!-- Analysis Card 3 -->
                    <div class="analysis-item" x-data="{ expanded: false }">
                        <div class="analysis-icon">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-shield-alt text-white"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-medium text-white">Funds <span class="text-blue-400">Protection</span></h4>
                            <p class="mt-2 text-gray-300 line-clamp-2" :class="expanded ? '' : 'line-clamp-2'">
                                    Your security is our priority. We provide industry-leading insurance protection for client funds up to $1,000,000, ensuring your investments are protected against unforeseen circumstances.
                            </p>
                            <a href="#">
                                <button class="analysis-read-more">
                                    Read more
                                </button>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="analysis-learn-more">
                    <a href="user/login.php" class="analysis-learn-more-btn">
                            Learn more about our services
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
        </div>
    </div>
</section>


<!-- SUPERIOR TRADING SECTION -->
<section class="superior-trading">
        <div class="mb-12 text-center">
            <span class="real-time">
                Superior Trading
            </span>
            <h2 class="mt-2 text-3xl font-bold text-white">Tighter Spreads. Faster Execution.</h2>
            <p class="mt-2 text-gray-400 max-w-2xl mx-auto">Experience institutional-grade trading conditions designed for professional traders.</p>
        </div>

        <div class="trading-grid">

            <!-- Left Side: Trading Advantages -->
                <div class="trading-card-1">
                    <h3 class="text-2xl font-bold text-white mb-6">Premium Trading Conditions</h3>

                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <span class="trading-card-1-check">✓</span>
                            <p class="ml-3 text-gray-300">
                                <span class="font-semibold text-white">Ultra-low spreads</span> from 0.01 pips on major pairs.
                            </p>
                        </li>

                        <li class="flex items-start">
                            <span class="trading-card-1-check">✓</span>
                            <p class="ml-3 text-gray-300">
                                <span class="font-semibold text-white">Lightning-fast execution</span> from NY4 server facility with minimal slippage.
                            </p>
                        </li>

                        <li class="flex items-start">
                            <span class="trading-card-1-check">✓</span>
                            <p class="ml-3 text-gray-300">
                                <span class="font-semibold text-white">Top-tier liquidity</span> and market-leading pricing 24/5.
                            </p>
                        </li>

                        <li class="flex items-start">
                            <span class="trading-card-1-check">✓</span>
                            <p class="ml-3 text-gray-300">
                                <span class="font-semibold text-white">No dealing desk</span> and no requotes, ever.
                            </p>
                        </li>
                    </ul>

                    <div class="superior-trading-conditions">
                        <a href="user/login.php" class="superior-trading-btn">
                            View detailed conditions
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

            <!-- Right Side: Trading Widget -->
            <div class="trading-card-2">
                <h3 class="text-xl font-semibold text-white mb-4">Live Market Overview</h3>

                <div class="superior-widget-container" style="width: 100%; height: 400px;">
                    <iframe scrolling="no" allowtransparency="true" frameborder="0" src="https://www.tradingview-widget.com/embed-widget/forex-cross-rates/?locale=en#%7B%22width%22%3A%22100%25%22%2C%22height%22%3A400%2C%22currencies%22%3A%5B%22EUR%22%2C%22USD%22%2C%22JPY%22%2C%22GBP%22%2C%22CHF%22%2C%22AUD%22%2C%22CAD%22%2C%22NZD%22%5D%2C%22colorTheme%22%3A%22dark%22%2C%22utm_source%22%3A%22webbo.online%22%2C%22utm_medium%22%3A%22widget%22%2C%22utm_campaign%22%3A%22forex-cross-rates%22%2C%22page-uri%22%3A%22webbo.online%2F%22%7D" title="forex cross-rates TradingView widget" lang="en" style="user-select: none; box-sizing: border-box; display: block; height: 100%; width: 100%;"></iframe>
                    
                </div>
            </div>

</section>


<!-- Copy Trading Section -->
<section class="copy-trading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center" style="margin-bottom: 8px;">
            <span class="copy-trading-badge">
                Professional Trading
            </span>
            <h2 class="mt-2 text-3xl font-bold text-white">Copy Professional Traders</h2>
            <p class="mt-2 text-gray-400 max-w-2xl mx-auto">Let experienced traders do the work for you with our advanced copy trading system</p>
        </div>

        <div class="copy-trading-grid">

            <!-- Strategy 1 -->
            <div class="copy-trading-card">
                <div class="copy-trading-icon">
                    <i class="fa-solid fa-copy"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Copy 400+ Strategies</h3>
                <p class="text-gray-400">Access hundreds of strategies for more than 1000 instruments across all asset classes.</p>
            </div>

            <!-- Strategy 2 -->
            <div class="copy-trading-card">
                <div class="copy-trading-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Select Top Performers</h3>
                <p class="text-gray-400">Use our reporting tools to rank strategies according to performance and select the most suitable for your circumstances.</p>
            </div>

            <!-- Strategy 3 -->
            <div class="copy-trading-card">
                <div class="copy-trading-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Stay Protected</h3>
                <p class="text-gray-400">The system uses sophisticated calculations to keep your exposure at an optimal level for your account.</p>
            </div>

            <!-- Strategy 4 -->
            <div class="copy-trading-card">
                <div class="copy-trading-icon">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Combine Methods</h3>
                <p class="text-gray-400">Our integrated platform allows you to combine copying with manual and automated trading, depending on your preferences.</p>
            </div>
        </div>
    </div>
</section>


<!-- Education Section -->
<section class="education">
        <div class="education-grid">

            <!-- Video Column -->
            <div class="bitcoin-video">
                <div class="bg-dark-300 h-[400px]">
                    <iframe src="https://www.youtube.com/embed/Gc2en3nHxA4" allow="autoplay; encrypted-media" allowfullscreen="" class="w-full h-full object-cover" title="Learn about Bitcoin">
                    </iframe>
                </div>
            </div>

            <!-- Text Column -->
            <div class="education-text-column">
                <div class="education-center">
                    <span>EDUCATION CENTER</span>
                    <h2 class="mt-2 text-3xl font-bold text-white">Learn From Market Experts</h2>
                </div>

                <p class="text-gray-300 text-lg">
                    Discover everything you need to know about cryptocurrency trading, from fundamentals to advanced strategies.
                </p>

                <div class="education-about">
                    <h3>About Bitcoin</h3>
                    <p>
                        Bitcoin ₿ is a decentralized digital currency that operates without a central authority. Transactions are verified by network nodes through cryptography and recorded on a public distributed ledger called a blockchain. Created in 2008 by an unknown person or group using the name Satoshi Nakamoto, Bitcoin pioneered the concept of cryptocurrencies.
                    </p>
                </div>

                <div class="education-btn">
                    <a href="#" class="education-btn-1">
                        Explore learning resources
                        <i class="fas fa-graduation-cap ml-2"></i>
                    </a>
                    <a href="user/login.php" class="education-btn-2">
                        Join free webinars
                        <i class="fas fa-video ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
</section>


<!-- About Us Section -->
<section class="about-section">

    <div class="">
        <div class="about-us-header">
            <span class="about-us-badge">
                Our Story
            </span>
            <h2 class="mt-2 text-3xl font-bold text-white">About Us</h2>
            <div class="underline"></div>
        </div>

        <div class="about-card">
            <p class="intro-text">
                Nexora has become one of the most reputable brokers in the industry, offering traders CFDs across Forex, Equities, Commodities and Futures. Trading on the Forex market is a legitimate and straightforward way of generating income.
            </p>

            <p class="intro-text">
                The good news is that you don't have to be a professional trader in order to make money. All you need is the right personality and the right skill set and you can make money trading on foreign exchanges. WebBo lets you trade in the way that best suits you.
            </p>

            <div class="about-features">
                <div class="about-feature">
                    <h3 class="text-white font-semibold flex items-center">
                        <i class="fas fa-check-circle text-primary mr-2" style="color: #1848CC;"></i>
                        Personalized Trading
                    </h3>
                    <p class="text-gray-400 mt-2">
                        Do you want to risk a little or a lot? Do you want gains in the short term or are you playing a longer game? Are you a day trader, a swing trader or a scalper?
                    </p>
                </div>

                <div class="about-feature">
                    <h3 class="text-white font-semibold flex items-center">
                        <i class="fas fa-check-circle text-primary mr-2" style="color: #1848CC;"></i>
                        Complete Control
                    </h3>
                    <p class="text-gray-400 mt-2">
                        With the right tools, information and access to all the world's currencies, WebBo puts you in control of the trades you make.
                    </p>
                </div>
            </div>

            <div class="about-btn-wrap">
                <a href="about" class="about-btn">
                    Learn more about us
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Trusted Brand Section -->
<section class="trust-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="trust-header">
            <span class="trust-badge">
                Global Trust
            </span>
            <h2 class="mt-3 text-3xl font-bold text-white">Why <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600">Nexora</span> Is One of the World's Most Trusted Brands</h2>
            <p class="mt-3 text-gray-300 max-w-2xl mx-auto">Experience the reliability and security that our global clients have come to trust</p>
        </div>

        <div class="trust-grid">
            <!-- TradingView Widget Container -->
            <div class="trust-container">
                <div class="p-4 bg-gray-800 border-b border-gray-700">
                    <h3 class="text-xl font-semibold text-white">Real-Time Market Analysis</h3>
                </div>

                <div id="forex_heat_map" class="w-full h-96 bg-gray-800"></div>
                <div class="trust-widget-link">
                    <a href="https://www.tradingview.com/markets/currencies/forex-heat-map/" rel="noopener" target="_blank" class="hover:underline" style="text-decoration: none;">Forex Heat Map by TradingView</a>
                </div>

            <div style="width: 100%; height: 400px;"><style>
	.tradingview-widget-copyright {
		font-size: 13px !important;
		line-height: 32px !important;
		text-align: center !important;
		vertical-align: middle !important;
		/* @mixin sf-pro-display-font; */
		font-family: -apple-system, BlinkMacSystemFont, 'Trebuchet MS', Roboto, Ubuntu, sans-serif !important;
		color: #B2B5BE !important;
	}

	.tradingview-widget-copyright .blue-text {
		color: #2962FF !important;
	}

	.tradingview-widget-copyright a {
		text-decoration: none !important;
		color: #B2B5BE !important;
	}

	.tradingview-widget-copyright a:visited {
		color: #B2B5BE !important;
	}

	.tradingview-widget-copyright a:hover .blue-text {
		color: #1E53E5 !important;
	}

	.tradingview-widget-copyright a:active .blue-text {
		color: #1848CC !important;
	}

	.tradingview-widget-copyright a:visited .blue-text {
		color: #2962FF !important;
	}
	</style><iframe scrolling="no" allowtransparency="true" frameborder="0" src="https://www.tradingview-widget.com/embed-widget/forex-heat-map/#%7B%22width%22%3A770%2C%22height%22%3A400%2C%22isTransparent%22%3Afalse%2C%22currencies%22%3A%5B%22EUR%22%2C%22USD%22%2C%22JPY%22%2C%22GBP%22%2C%22CHF%22%2C%22AUD%22%2C%22CAD%22%2C%22NZD%22%2C%22CNY%22%5D%2C%22frameElementId%22%3Anull%2C%22utm_source%22%3A%22webbo.online%22%2C%22utm_medium%22%3A%22widget%22%2C%22utm_campaign%22%3A%22forex-heat-map%22%2C%22page-uri%22%3A%22webbo.online%2F%22%7D" title="forex heat-map TradingView widget" lang="en" style="user-select: none; box-sizing: border-box; display: block; height: 100%; width: 100%;"></iframe></div>
            </div>

            <!-- Trust Features -->
            <div class="trust-card">
                <div class="p-6">
                    <h3>Our Trusted Reputation</h3>
                    <ul class="space-y-5">
                        <li class="trust-item">
                            <div class="flex-shrink-0 mt-1">
                                <span class="icon">
                                    <i class="fas fa-check text-blue-400"></i>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">Globally Regulated</h4>
                                <p class="text-gray-400 text-sm mt-1">Operating under strict financial regulations to ensure maximum security for your assets</p>
                            </div>
                        </li>
                        <li class="trust-item">
                            <div class="flex-shrink-0 mt-1">
                                <span class="icon">
                                    <i class="fas fa-trophy text-blue-400"></i>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">40+ International Awards</h4>
                                <p class="text-gray-400 text-sm mt-1">Recognition for excellence in trading services, platform technology and customer support</p>
                            </div>
                        </li>
                        <li class="trust-item">
                            <div class="flex-shrink-0 mt-1">
                                <span class="icon">
                                    <i class="fas fa-headset text-blue-400"></i>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">24/7 Multilingual Support</h4>
                                <p class="text-gray-400 text-sm mt-1">Expert assistance available around the clock in multiple languages</p>
                            </div>
                        </li>
                        <li class="trust-item">
                            <div class="flex-shrink-0 mt-1">
                                <span class="icon">
                                    <i class="fas fa-shield-alt text-blue-400"></i>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">Segregated Client Funds</h4>
                                <p class="text-gray-400 text-sm mt-1">Your investments are kept in separate accounts for maximum security</p>
                            </div>
                        </li>
                        <li class="trust-item">
                            <div class="flex-shrink-0 mt-1">
                                <span class="icon">
                                    <i class="fas fa-user-tie text-blue-400"></i>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">Personal Account Managers</h4>
                                <p class="text-gray-400 text-sm mt-1">Dedicated professionals to guide your trading journey</p>
                            </div>
                        </li>
                    </ul>
                    <div class="trust-section-btn">
                        <a href="user/login.php">
                            Learn More
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- How It Works Section -->
<section class="how-it-works">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="process-header">
            <span class="badge">
                SIMPLE PROCESS
            </span>
            <h2 class="mt-3 text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600">How It Works</h2>
            <p class="mt-3 text-gray-300 max-w-2xl mx-auto">Get started with trading in three simple steps</p>
        </div>

        <div class="process-container">
            <!-- Step 1 -->
            <div class="relative-group">

                    <div class="process-number">1</div>
                <div class="process-card">
                    <h3>Deposit</h3>
                    <p class="text-gray-300 text-center mb-8">
                        Open real account and add funds. We work with more than 20 payment systems for your convenience.
                    </p>
                </div>
                    <div class="process-card-btn">
                        <a href="user/register.php">
                            Get Started
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
            </div>

            <!-- Step 2 -->
            <div class="relative-group">
                
                <div class="process-number">2</div>
            <div class="process-card">
                <h3>Trade</h3>
                <p class="text-gray-300 text-center mb-8">
                    Trade any of 100 assets and stocks. Use technical analysis and trade the news for better results.
                </p>
            </div>
                    <div class="process-card-btn">
                        <a href="user/login.php">
                            Explore Markets
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
            </div>

            <!-- Step 3 -->
            <div class="relative-group">
    
                    <div class="process-number">3</div>
                <div class="process-card">
                    <h3>Withdraw</h3>
                    <p>
                        Get funds easily to your bank card or e-wallet with our fast and secure withdrawal process.
                    </p>
                </div>
                    <div class="process-card-btn">
                        <a href="user/login.php">
                            Learn More
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
            </div>
        </div>
    </div>
</section>



<!-- Premium Trading Experience Section -->
<section class="freedom-to-trade">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="freedom-header">
            <span class="badge">
                FREEDOM TO TRADE
            </span>
            <h2>Trade What You Want, When You Want</h2>
            <p>Experience unlimited trading possibilities with our comprehensive platform</p>
        </div>

        <div class="freedom-grid">
            <!-- Left: Image Column -->
            <!-- <div class="relative group overflow-hidden rounded-xl shadow-2xl opacity-100" x-data="{}" x-init="setTimeout(() =&gt; { $el.classList.add('opacity-100'); $el.classList.remove('opacity-0'); }, 100)">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-blue-900/20 rounded-xl transform rotate-1 group-hover:rotate-0 transition-all duration-300 opacity-70"></div>
                  <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-gray-900 p-4">
                    <p class="text-sm text-blue-300 text-center">Competitive commission structure for all traders</p>
                </div>
            </div> -->

            <!-- Right: Content Column -->
            <div class="freedom-right-grid">
                <h3>Premium Trading Experience</h3>
                <p>
                    One of the primary goals of NEXORA is to provide the best product in the market. Our relationships with leading tier one financial institutions mean deep liquidity and tighter spreads for Forex traders.
                </p>
                <div class="freedom-card">
                    <div class="freedom-card-items">
                        <p>
                        <i class="fas fa-check text-blue-400 text-sm"></i>
                        Trade Forex, Indices, Shares & Commodities.</p>
                    </div>
                    <div class="freedom-card-items">
                        <p>
                        <i class="fas fa-check text-blue-400 text-sm"></i>
                        Access global markets 24 hours / 7 days.</p>
                    </div>
                    <div class="freedom-card-items">
                        <p>
                        <i class="fas fa-check text-blue-400 text-sm"></i>
                        Multilingual customer support.</p>
                    </div>
                    <div class="freedom-card-items">
                        <p>
                        <i class="fas fa-check text-blue-400 text-sm"></i>
                        Trade on the go on our mobile apps.</p>
                    </div>
                </div>
                <div class="freedom-learn-more">
                    <a href="login/login.html">
                        Learn About Our Commissions
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Trading Services Section -->
<section class="trading-services-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="trading-service-tag">
            <span class="badge">
                COMPREHENSIVE SERVICES
            </span>
            <h2>Start Trading with NEXORA</h2>
            <p>Everything you need for successful trading in one platform</p>
        </div>

        <div class="trading-service-grid">
            <!-- Market Analysis Card -->
            <div class="trading-service-card">
                <i class="fas fa-chart-line text-blue-400 text-xl"></i>
                <h3>Market Analysis</h3>
                <ul>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Daily Market Analysis</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Weekly Live Webinars</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Live Q & A Sessions</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Trading Strategy Support</span>
                    </li>
                </ul>
            </div>

            <!-- Award-Winning Broker Card -->
            <div class="trading-service-card">
                <i class="fas fa-trophy text-blue-400 text-xl"></i>
                <h3>Award-Winning Broker</h3>
                <ul>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>42+ Industry Awards</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Top 100 Companies</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Best Client Funds Security Global</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Best Forex News & Analysis Provider</span>
                    </li>
                </ul>
            </div>

            <!-- Investment Options Card -->
            <div class="trading-service-card">
                <i class="fas fa-hand-holding-usd text-blue-400 text-xl"></i>
                <h3>Investment Options</h3>
                <ul>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>BA Copy - How it Works</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Become a Follower</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>PAMM Ranking</span>
                    </li>
                    <li class="flex items-start">
                        <span class="badge">â€¢</span>
                        <span>Become an Investor</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="trading-service-btn">
            <a href="user/register.php">
                Start Trading Now
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>


<!-- Cryptocurrency Logos Section -->
<section class="crypto-logos">
  <div class="logo-grid">
        <!-- Bitcoin -->
        <div class="logo-card">
            <img src="Storage/Index/btc.png" alt="Bitcoin">
        </div>

        <!-- Ethereum -->
        <div class="logo-card">
            <img src="Storage/Index/eth.png" alt="Ethereum">
        </div>

        <!-- Dogecoin -->
        <div class="logo-card">
            <img src="Storage/Index/doge.png" alt="Dogecoin">
        </div>

        <!-- Bitcoin Cash -->
        <div class="logo-card">
            <img src="Storage/Index/bch.png" alt="Bitcoin Cash"">
        </div>

        <!-- Tether -->
        <div class="logo-card">
            <img src="Storage/Index/usdt.png" alt="Tether">
        </div>

        <!-- Binance Coin -->
        <div class="logo-card">
            <img src="Storage/Index/bnb.png" alt="Binance Coin">
        </div>

        <!-- Litecoin -->
        <div class="logo-card">
            <img src="Storage/Index/ltc.png" alt="Litecoin">
        </div>
    </div>
</section>

<script type="text/javascript"> var host = 'h51.p.ctrader.com';</script><script src="temp/custom/js/spreads-home.js" type="module"></script>

</main>


<!-- Footer -->
<footer class="footer">
    <!-- Upper Footer -->
    <div class="footer-top">
                    <!-- Company Info -->
                    <div class="footer-brand">
                        <img src="Storage/Index/Logo.png" alt="Nexora Logo">
                        <p class="text-sm text-gray-400 mb-6">
                            Nexora offers CFD trading on stocks, forex, indices, commodities, and cryptocurrencies with competitive spreads and advanced trading tools.
                        </p>
                        <div class="social-icons">
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="footer-links">
                        <h3>Quick Links</h3>
                        <a href="about">About Us</a>
                        <a href="why-us">Why Choose Us</a>
                        <a href="for-traders">Education</a>
                        <a href="contacts">Contact</a>
                    </div>

                    <!-- Trading -->
                    <div class="footer-links">
                        <h3>Trading</h3>
                        <a href="cryptocurrencies">Cryptocurrencies</a>
                        <a href="forex">Forex</a>
                        <a href="shares">Shares</a>
                        <a href="indices">Indices</a>
                    </div>

                    <!-- Account -->
                    <div class="footer-links">
                        <h3>YOUR ACCOUNT</h3>
                        <a href="user/login.php">Log In</a>
                        <a href="user/register.php">Create Account</a>
                        <a href="#">Demo Account</a>
                        <a href="contact.php">Help Center</a>
                    </div>
    </div>

    <!-- Platform Availability -->
    <div class="footer-platforms">
        <h4>Available On</h4>
        <div class="platform-icons">
            <span>Web</span>
            <span>Windows</span>
            <span>Android</span>
            <span>iOS</span>
        </div>
    </div>

    <!-- Disclaimer & Legal -->
    <div class="footer-warning">
        <p>
            <strong>RISK WARNING:</strong>
            The Financial Products offered by the company include Contracts for Difference ('CFDs') and other complex financial products. Trading CFDs carries a high level of risk since leverage can work both to your advantage and disadvantage. As a result, CFDs may not be suitable for all investors because it is possible to lose all of your invested capital. You should never invest money that you cannot afford to lose. Before trading in the complex financial products offered, please ensure you understand the risks involved.
        </p>
    </div>

    <div class="footer-bottom">
        <div class="footer-policy">
            <a href="#">Terms & Conditions</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Legal Documents</a>
        </div>

        <p>© <script>document.write(new Date().getFullYear())</script> Nexora. All Rights Reserved.</p>
    </div>
    
    </footer>


<script src="script.js"></script>


    <!-- JavaScript utilities -->
    <!-- <script> -->
        <!-- // Utility function for mobile menu
        document.addEventListener('alpine:init', () => {
            Alpine.store('navigation', {
                open: false,
                toggle() {
                    this.open = !this.open;
                },
                close() {
                    this.open = false;
                }
            }); -->
<!-- 
            // Theme toggler
            Alpine.store('darkMode', {
                on: true,
                toggle() {
                    this.on = !this.on;
                    if (this.on) {
                        document.documentElement.classList.add('dark');
                        document.body.classList.add('bg-gray-900');
                        document.body.classList.remove('bg-gray-100');
                    } else {
                        document.documentElement.classList.remove('dark');
                        document.body.classList.remove('bg-gray-900');
                        document.body.classList.add('bg-gray-100');
                    }
                    localStorage.setItem('darkMode', this.on ? 'dark' : 'light');
                }
            });
        }); -->

        <!-- // Initialize theme from local storage
        document.addEventListener('DOMContentLoaded', () => {
            const theme = localStorage.getItem('darkMode');
            if (theme === 'light') {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('bg-gray-900');
                document.body.classList.add('bg-gray-100');
                if (window.Alpine) {
                    Alpine.store('darkMode').on = false;
                }
            }
        }); -->
    <!-- </script> -->





        <!-- GetButton.io widget -->
<!--<script type="text/javascript">-->
<!--    (function () {-->
<!--        var options = {-->
            <!--whatsapp: "447346357710", // WhatsApp number-->
            <!--call_to_action: "Message us", // Call to action-->
            <!--button_color: "#FF6550", // Color of button-->
            <!--position: "right", // Position may be 'right' or 'left'-->
<!--        };-->
<!--        var proto = 'https:', host = "getbutton.io", url = proto + '//static.' + host;-->
<!--        var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';-->
<!--        s.onload = function () { WhWidgetSendButton.init(host, proto, options); };-->
<!--        var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);-->
<!--    })();-->
<!--</script>-->
<!-- Additional Scripts -->
<script>
    // Live Chat Function
    function openLiveChat(e) {
        e.preventDefault();
        console.log('Opening live chat...');
        // Implement your live chat functionality here
    }

    // Theme toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any custom components
        console.log('DOM loaded, initializing components...');
    });

    // Analytics (preserved from original)
    function bindGAEvents(eventCategory, callback, iter) {
        if (typeof iter === "undefined") { iter = 1; }
        if(window.dataLayer) {
            if (typeof callback === "function") {
                callback();
            }
        } else if(iter > 2 && typeof callback === "function") {
            callback();
        } else {
            setTimeout(function(){
                bindGAEvents(eventCategory, callback, iter + 1);
            }, 500);
        }
    }

    $(document).ready(function() {
        if('' == '1') {
            bindGAEvents('ex_new_user');
        }

        $(document).on('click', '.start-trading, .open-live-account, .open-demo-account', function (event) {
            var redirectLink = $(this).attr('href');
            bindGAEvents('ex_button', function(){
                window.location.href = redirectLink;
            });
            event.preventDefault();
        });
    });
</script>


</body>
</html>
