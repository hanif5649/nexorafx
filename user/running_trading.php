<?php 
include('layout/header.php'); 

$user_id = $_SESSION['user_id'] ?? null; 
$message = "";

// --- TRADE EXECUTION HANDLER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['execute_trade'])) {
    $asset_id = (int)$_POST['asset_id'];
    $units = filter_var($_POST['units'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    // Fetch asset data for the price
    $asset_stmt = $pdo->prepare("SELECT * FROM tradable_assets WHERE id = ? AND is_active = 1");
    $asset_stmt->execute([$asset_id]);
    $asset = $asset_stmt->fetch();

    if ($asset) {
        $price = (float)$asset['current_price'];
$units = (float)$units;

$total_cost = $units * $price;

        // Check user balance
        $user_stmt = $pdo->prepare("SELECT account_balance FROM users WHERE id = ?");
        $user_stmt->execute([$user_id]);
        $balance = $user_stmt->fetchColumn();

        if ($balance >= $total_cost && $units > 0) {
            $pdo->beginTransaction();
            try {
                // Deduct balance
                $deduct = $pdo->prepare("UPDATE users SET account_balance = account_balance - ? WHERE id = ?");
                $deduct->execute([$total_cost, $user_id]);

                // Record investment
                $trade = $pdo->prepare("INSERT INTO asset_investments (user_id, asset_id, units, purchase_price, total_cost, trade_type, status, created_at) VALUES (?, ?, ?, ?, ?, 'buy', 'open', NOW())");
                $trade->execute([$user_id, $asset_id, $units, $price, $total_cost]);

                $pdo->commit();
                $message = "TRADE_EXECUTED: Position opened successfully.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "SYSTEM_ERROR: Execution failed.";
            }
        } else {
            $message = "INSUFFICIENT_LIQUIDITY: Top up your account.";
        }
    }
}

// Fetch Data for UI
$assets = $pdo->query("SELECT * FROM tradable_assets WHERE is_active = 1 ORDER BY asset_type ASC")->fetchAll();

$my_trades = $pdo->prepare("
    SELECT ai.*, ta.symbol, ta.asset_name, ta.current_price as market_price 
    FROM asset_investments ai 
    JOIN tradable_assets ta ON ai.asset_id = ta.id 
    WHERE ai.user_id = ? AND ai.status = 'open' 
    ORDER BY ai.created_at DESC
");
$my_trades->execute([$user_id]);
$running_positions = $my_trades->fetchAll();
?>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="max-w-7xl mx-auto pb-24 px-6" x-data="{ selectedSymbol: 'BINANCE:BTCUSDT' }">

    <!-- Terminal Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4 border-b border-white/5 pb-6">
        <div>
            <h2 class="text-2xl font-black font-['Syne'] text-white uppercase tracking-tighter italic">Asset<span class="text-golf-green">Terminal</span></h2>
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.4em] mt-1">Real-time Spot Execution</p>
        </div>
        <div class="flex gap-4">
            <div class="bg-surface border border-white/5 px-6 py-3 rounded-xl">
                <p class="text-[8px] text-gray-500 font-black uppercase tracking-widest mb-1 text-right">Market Status</p>
                <p class="text-xs font-black text-golf-green uppercase tracking-widest flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-golf-green rounded-full animate-ping"></span> Live
                </p>
            </div>
        </div>
    </div>

    <?= $message ? "<div class='bg-golf-green/10 border border-golf-green/20 p-4 rounded-xl mb-8 text-center text-[10px] font-black uppercase tracking-widest text-golf-green'>$message</div>" : "" ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Chart & Execution -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Advanced Real-Time Chart -->
            <div class="bg-surface border border-white/5 rounded-[2rem] p-2 h-[500px] shadow-2xl relative overflow-hidden">
                <div class="tradingview-widget-container" style="height: 100%; width: 100%;">
                    <div id="tradingview_adv" style="height: 100%; width: 100%;"></div>
                    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                    <script type="text/javascript">
                    function loadChart(symbol) {
                        new TradingView.widget({
                            "autosize": true,
                            "symbol": symbol,
                            "interval": "D",
                            "timezone": "Etc/UTC",
                            "theme": "dark",
                            "style": "1",
                            "locale": "en",
                            "toolbar_bg": "#f1f3f6",
                            "enable_publishing": false,
                            "hide_top_toolbar": false,
                            "save_image": false,
                            "container_id": "tradingview_adv",
                            "backgroundColor": "rgba(13, 13, 13, 1)",
                            "gridColor": "rgba(42, 46, 57, 0.06)"
                        });
                    }
                    loadChart('BINANCE:BTCUSDT');
                    </script>
                </div>
            </div>

            <!-- Execution Form -->
            <div class="bg-surface border border-white/5 rounded-[2rem] p-8 shadow-2xl" 
     x-data="{ 
        units: 0, 
        currentUnitPrice: 0,
        updateAsset(el) {
            this.currentUnitPrice = el.options[el.selectedIndex].getAttribute('data-price') || 0;
            loadChart(el.options[el.selectedIndex].getAttribute('data-symbol'));
        }
     }">
    
    <form action="" method="POST" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <!-- Asset Selection -->
            <div>
                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Target Asset</label>
                <select name="asset_id" required @change="updateAsset($event.target)"
                        class="w-full mt-2 bg-deep-dark border border-white/10 rounded-xl px-4 py-3.5 text-white font-bold text-xs uppercase tracking-widest outline-none focus:border-golf-green/50 transition-all">
                    <option value="">Select Asset</option>
                    <?php foreach($assets as $a): ?>
                        <option value="<?= $a['id'] ?>" 
                                data-symbol="<?= htmlspecialchars($a['symbol']) ?>" 
                                data-price="<?= $a['current_price'] ?>">
                            <?= $a['asset_name'] ?> (<?= $a['symbol'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Volume Input -->
            <div>
                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Trade Volume (Units)</label>
                <input type="number" name="units" step="0.00000001" placeholder="0.00" required x-model="units"
                       class="w-full mt-2 bg-deep-dark border border-white/10 rounded-xl px-4 py-3.5 text-white font-black text-sm outline-none focus:border-golf-green/50 transition-all">
            </div>

            <!-- Execution Button -->
            <button type="submit" name="execute_trade" 
                    class="bg-golf-green text-deep-dark font-black py-4 rounded-xl uppercase text-[10px] tracking-[0.3em] hover:bg-white transition-all shadow-lg active:scale-95">
                Open Position
            </button>
        </div>

        <!-- Real-time Cost Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-white/5 pt-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/5 rounded-lg">
                    <p class="text-[8px] text-gray-500 font-black uppercase tracking-widest">Price per Unit</p>
                    <p class="text-sm font-mono font-black text-white">$<span x-text="parseFloat(currentUnitPrice).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 8})">0.00</span></p>
                </div>
                <div class="text-gray-600 text-xs">×</div>
                <div class="p-2 bg-white/5 rounded-lg">
                    <p class="text-[8px] text-gray-500 font-black uppercase tracking-widest">Quantity</p>
                    <p class="text-sm font-mono font-black text-white" x-text="units || 0">0</p>
                </div>
            </div>

            <div class="flex md:justify-end items-center">
                <div class="text-right px-6 border-l border-white/5">
                    <p class="text-[9px] text-golf-green font-black uppercase tracking-widest">Estimated Total Cost</p>
                    <p class="text-2xl font-black text-white font-mono tracking-tighter">
                        $<span x-text="(units * currentUnitPrice).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})">0.00</span>
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
        </div>

        <!-- Right: Portfolio Ledger -->
        <div class="lg:col-span-4">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.4em] mb-4 ml-2">Running Positions</p>
            <div class="space-y-4 max-h-[750px] overflow-y-auto pr-2 custom-scrollbar">
                
                <?php foreach($running_positions as $p): ?>
                <div class="bg-surface border border-white/5 p-6 rounded-3xl relative overflow-hidden group">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-golf-green"></div>
                    
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-black text-white uppercase italic"><?= $p['asset_name'] ?></p>
                            <p class="text-[8px] text-gray-500 font-bold uppercase tracking-widest"><?= $p['symbol'] ?> / SPOT</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-white font-mono"><?= number_format($p['units'], 8) ?> Units</p>
                            <p class="text-[8px] text-golf-green font-black uppercase tracking-tighter">Status: Open</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                        <div>
                            <p class="text-[8px] text-gray-500 font-black uppercase tracking-widest mb-1">Entry Price</p>
                            <p class="text-xs font-black text-white font-mono">$<?= number_format($p['purchase_price'], 2) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] text-gray-500 font-black uppercase tracking-widest mb-1">Market Price</p>
                            <p class="text-xs font-black text-white font-mono">$<?= number_format($p['market_price'], 2) ?></p>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-[8px] text-gray-600 font-bold uppercase tracking-widest"><?= date('y-m-d H:i', strtotime($p['created_at'])) ?></span>
                        <div class="text-right">
                            <p class="text-[8px] text-gray-500 uppercase font-black">Investment</p>
                            <p class="text-sm font-black text-white font-mono">$<?= number_format($p['total_cost'], 2) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(empty($running_positions)): ?>
                <div class="text-center py-20 border border-dashed border-white/10 rounded-3xl">
                    <i class="fa-solid fa-layer-group text-3xl text-gray-800 mb-4"></i>
                    <p class="text-[10px] text-gray-600 font-black uppercase tracking-widest italic">No Active Positions</p>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>

<?php include('layout/footer.php'); ?>