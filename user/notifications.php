<?php 
include('layout/header.php'); 

$user_id = $_SESSION['user_id'] ?? null; 
$message_status = "";

// --- SEND MESSAGE TO ADMIN HANDLER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['message_content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        if ($stmt->execute([$user_id, "SUPPORT: " . $title, $content])) {
            $message_status = "Message dispatched to administrative desk.";
        }
    }
}

// --- MARK ALL AS READ HANDLER ---
if (isset($_GET['mark_read'])) {
    $update = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $update->execute([$user_id]);
}

// Fetch Notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto pb-24 px-6">

    <!-- Header Section -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h2 class="text-3xl font-black font-['Syne'] text-white uppercase tracking-tighter italic">Message<span class="text-golf-green">Center</span></h2>
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.4em] mt-2">Secure Correspondence Protocol</p>
        </div>
        <a href="?mark_read=1" class="text-[9px] font-black text-gray-400 uppercase tracking-widest hover:text-white transition-colors flex items-center gap-2 border border-white/10 px-4 py-2 rounded-lg">
            <i class="fa-solid fa-check-double"></i> Mark all as read
        </a>
    </div>

    <?= $message_status ? "<div class='bg-golf-green/10 border border-golf-green/20 p-4 rounded-xl mb-8 text-center text-[10px] font-black uppercase tracking-widest text-golf-green'>$message_status</div>" : "" ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Contact Admin Form -->
        <div class="lg:col-span-4">
            <div class="bg-surface border border-white/5 rounded-[2.5rem] p-8 shadow-2xl sticky top-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-golf-green/20 rounded-full flex items-center justify-center text-golf-green">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-black text-xs uppercase tracking-widest">Support Ticket</h4>
                        <p class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter">Response time: ~24hrs</p>
                    </div>
                </div>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Subject / Title</label>
                        <input type="text" name="title" placeholder="e.g., Deposit Inquiry" required
                               class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-5 py-4 text-white font-bold text-xs outline-none focus:border-golf-green/50 transition-all">
                    </div>

                    <div>
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Your Message</label>
                        <textarea name="message_content" rows="6" placeholder="Describe your request in detail..." required
                                  class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-5 py-4 text-white font-medium text-xs outline-none focus:border-golf-green/50 transition-all resize-none"></textarea>
                    </div>

                    <button type="submit" name="send_message" 
                            class="w-full bg-white text-deep-dark font-black py-5 rounded-2xl uppercase text-[10px] tracking-[0.4em] hover:bg-golf-green transition-all shadow-xl">
                        Dispatch Message
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Notification Feed -->
        <div class="lg:col-span-8 space-y-4">
            <?php foreach($notifications as $note): ?>
                <div class="bg-surface border border-white/5 p-6 md:p-8 rounded-[2rem] relative overflow-hidden transition-all hover:bg-white/[0.03]">
                    <!-- Read/Unread Indicator -->
                    <?php if(!$note['is_read']): ?>
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-golf-green"></div>
                    <?php endif; ?>

                    <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center <?= str_contains($note['title'], 'SUPPORT') ? 'bg-blue-500/10 text-blue-400' : 'bg-golf-green/10 text-golf-green' ?>">
                                <i class="fa-solid <?= str_contains($note['title'], 'SUPPORT') ? 'fa-headset' : 'fa-bell' ?>"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-white uppercase tracking-tight"><?= htmlspecialchars($note['title']) ?></h3>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-1">
                                    <?= date('M d, Y • h:i A', strtotime($note['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                        <?php if(!$note['is_read']): ?>
                            <span class="bg-golf-green/20 text-golf-green text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest">New</span>
                        <?php endif; ?>
                    </div>

                    <div class="bg-black/20 p-5 rounded-2xl border border-white/5">
                        <p class="text-[11px] text-gray-400 leading-relaxed font-medium">
                            <?= nl2br(htmlspecialchars($note['message'])) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if(empty($notifications)): ?>
                <div class="bg-surface border border-dashed border-white/10 rounded-[3rem] py-32 text-center">
                    <i class="fa-solid fa-envelope-open text-5xl text-gray-800 mb-6"></i>
                    <p class="text-gray-500 text-xs font-black uppercase tracking-[0.4em]">Your inbox is empty</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>