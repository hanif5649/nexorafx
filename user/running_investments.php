<?php 
include('layout/header.php'); 

$user_id = $_SESSION['user_id'] ?? null; 

// Fetch Running and Completed Investments
// Fetch Running and Completed Investments
// Changed p.name to p.plan_name to match your CREATE TABLE schema
$stmt = $pdo->prepare("
    SELECT i.*, p.plan_name, p.roi_percentage, p.duration_days 
    FROM investments i 
    JOIN inv_plans p ON i.plan_id = p.id 
    WHERE i.user_id = ? 
    ORDER BY i.status DESC, i.created_at DESC
");
$stmt->execute([$user_id]);
$investments = $stmt->fetchAll();


// Function to calculate progress percentage
function getProgress($start, $end) {
    $now = time();
    $startDate = strtotime($start);
    $endDate = strtotime($end);
    
    if ($now >= $endDate) return 100;
    if ($now <= $startDate) return 0;
    
    $total = $endDate - $startDate;
    $passed = $now - $startDate;
    return round(($passed / $total) * 100, 2);
}
?>

<div class="max-w-7xl mx-auto pb-24 px-6">

    <!-- Header Section -->
    <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6">
        <div>
            <h2 class="text-3xl font-black font-['Syne'] text-white uppercase tracking-tighter italic">Active<span class="text-golf-green">Portfolios</span></h2>
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.4em] mt-2">Real-time Asset Growth Tracking</p>
        </div>
        <a href="plans.php" class="bg-golf-green text-deep-dark font-black px-8 py-4 rounded-2xl uppercase text-[10px] tracking-[0.2em] hover:bg-white transition-all shadow-lg flex items-center gap-3">
            <i class="fa-solid fa-plus text-xs"></i> Start New Investment
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Market Overview (TradingView Widget) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-surface border border-white/5 rounded-[2.5rem] p-4 overflow-hidden h-[500px]">
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4 ml-4 mt-2">Market Pulse</p>
                <!-- TradingView Widget BEGIN -->
                <div class="tradingview-widget-container" style="height: calc(100% - 40px);">
                    <div class="tradingview-widget-container__widget"></div>
                    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js" async>
                    {
                    "colorTheme": "dark",
                    "dateRange": "12M",
                    "showChart": true,
                    "locale": "en",
                    "largeChartUrl": "",
                    "isTransparent": true,
                    "showSymbolLogo": true,
                    "showFloatingTooltip": false,
                    "width": "100%",
                    "height": "100%",
                    "plotLineColorGrowing": "rgba(0, 255, 145, 1)",
                    "plotLineColorFalling": "rgba(255, 0, 0, 1)",
                    "gridLineColor": "rgba(240, 243, 250, 0)",
                    "scaleFontColor": "rgba(209, 212, 220, 1)",
                    "belowLineFillColorGrowing": "rgba(41, 98, 255, 0.12)",
                    "belowLineFillColorFalling": "rgba(41, 98, 255, 0.12)",
                    "belowLineFillColorGrowingBottom": "rgba(41, 98, 255, 0)",
                    "belowLineFillColorFallingBottom": "rgba(41, 98, 255, 0)",
                    "symbolActiveColor": "rgba(41, 98, 255, 0.12)",
                    "tabs": [
                        {
                        "title": "Indices",
                        "symbols": [
                            { "s": "BINANCE:BTCUSDT" },
                            { "s": "BINANCE:ETHUSDT" },
                            { "s": "FOREXCOM:SPX500" }
                        ]
                        }
                    ]
                    }
                    </script>
                </div>
                <!-- TradingView Widget END -->
            </div>
            
            <div class="bg-golf-green/10 border border-golf-green/20 p-8 rounded-[2rem]">
                <p class="text-white font-black text-xs uppercase tracking-widest mb-2 italic">Compound Growth</p>
                <p class="text-[11px] text-golf-green/80 leading-relaxed uppercase font-bold tracking-tighter">
                    Your assets are secured with multi-layer encryption. Profits are calculated daily and settled upon contract completion.
                </p>
            </div>
        </div>

        <!-- Right: Investment Cards -->
        <div class="lg:col-span-8 space-y-6">
            <?php foreach($investments as $inv): ?>
                <?php 
                    $progress = getProgress($inv['start_date'], $inv['end_date']);
                    $isEnded = ($progress >= 100);
                ?>
                <div class="bg-surface border border-white/5 rounded-[2.5rem] p-8 md:p-10 shadow-xl relative overflow-hidden group">
                    <!-- Status Badge -->
                    <div class="absolute top-0 right-0 px-8 py-2 rounded-bl-3xl text-[9px] font-black uppercase tracking-widest <?= $inv['status'] == 'running' ? 'bg-golf-green text-deep-dark' : 'bg-white/10 text-white' ?>">
                        <?= $inv['status'] ?>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between gap-8 mb-10">
                        <div>
                            <p class="text-[10px] text-gray-500 font-black uppercase tracking-[0.3em] mb-2">Strategy Plan</p>
                           
<h3 class="text-2xl font-black text-white uppercase italic"><?= $inv['plan_name'] ?></h3>
                            <p class="text-golf-green font-mono text-sm mt-1 font-bold">+<?= $inv['roi_percentage'] ?>% ROI Potential</p>
                        </div>
                        <div class="grid grid-cols-2 gap-8 text-right">
                            <div>
                                <p class="text-[9px] text-gray-500 font-black uppercase tracking-widest">Principal</p>
                                <p class="text-xl font-black text-white font-mono">$<?= number_format($inv['amount'], 2) ?></p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-500 font-black uppercase tracking-widest">Expected Return</p>
                                <p class="text-xl font-black text-white font-mono">$<?= number_format($inv['amount'] + ($inv['amount'] * $inv['roi_percentage'] / 100), 2) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Section -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-end">
                            <p class="text-[10px] text-white font-black uppercase tracking-widest">
                                <?= $isEnded ? 'Maturity Reached' : 'Growth Progress' ?>
                            </p>
                            <p class="text-xl font-black text-white font-mono"><?= $progress ?>%</p>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full h-3 bg-deep-dark rounded-full overflow-hidden border border-white/5 p-0.5">
                            <div class="h-full bg-golf-green rounded-full shadow-[0_0_15px_rgba(0,255,145,0.4)] transition-all duration-1000" style="width: <?= $progress ?>%"></div>
                        </div>

                        <div class="flex justify-between text-[9px] text-gray-500 font-black uppercase tracking-widest">
                            <span>Started: <?= date('M d, Y', strtotime($inv['start_date'])) ?></span>
                            <span>Ends: <?= date('M d, Y', strtotime($inv['end_date'])) ?></span>
                        </div>
                    </div>

                    <!-- Post-Maturity Notice -->
                    <?php if($isEnded && $inv['status'] == 'running'): ?>
                    <div class="mt-8 p-6 bg-white/5 border border-dashed border-white/20 rounded-2xl flex items-center gap-4">
                        <i class="fa-solid fa-circle-check text-golf-green text-xl animate-pulse"></i>
                        <div>
                            <p class="text-[10px] text-white font-black uppercase tracking-widest">Contract Maturity Notice</p>
                            <p class="text-[11px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">Plan cycle ended. Full profits and principal will be credited to your main balance shortly after final audit.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if(empty($investments)): ?>
                <div class="bg-surface border border-dashed border-white/10 rounded-[3rem] py-24 text-center">
                    <i class="fa-solid fa-chart-line text-5xl text-gray-800 mb-6"></i>
                    <p class="text-gray-500 text-sm font-black uppercase tracking-[0.4em]">No active positions found</p>
                    <a href="invest.php" class="text-golf-green text-[10px] font-black uppercase tracking-widest mt-4 block underline">Browse investment plans</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>