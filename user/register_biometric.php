<?php 
include('layout/header.php'); 

$user_id = $_SESSION['user_id'] ?? null; 
?>

<div class="max-w-4xl mx-auto pb-24 px-6 text-center">
    
    <div class="mb-12">
        <div class="w-20 h-20 bg-golf-green/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-fingerprint text-4xl text-golf-green"></i>
        </div>
        <h2 class="text-3xl font-black font-['Syne'] text-white uppercase tracking-tighter italic">Biometric<span class="text-golf-green">Enrollment</span></h2>
        <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.4em] mt-2 max-w-sm mx-auto">
            Link your device's hardware security to your AuchiCity account for passwordless access.
        </p>
    </div>

    <div class="bg-surface border border-white/5 rounded-[3rem] p-12 shadow-2xl relative overflow-hidden">
        <!-- Visual background element -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-golf-green/5 rounded-full blur-3xl"></div>

        <div id="registration-ui">
            <div class="space-y-8">
                <div class="bg-deep-dark p-8 rounded-[2rem] border border-white/5">
                    <ul class="text-left space-y-4">
                        <li class="flex items-start gap-4">
                            <i class="fa-solid fa-circle-check text-golf-green mt-1 text-xs"></i>
                            <p class="text-[11px] text-gray-400 uppercase font-bold tracking-tight">Enable lightning-fast logins using FaceID, TouchID, or Windows Hello.</p>
                        </li>
                        <li class="flex items-start gap-4">
                            <i class="fa-solid fa-circle-check text-golf-green mt-1 text-xs"></i>
                            <p class="text-[11px] text-gray-400 uppercase font-bold tracking-tight">Protect your vault with phishing-resistant hardware encryption.</p>
                        </li>
                    </ul>
                </div>

                <button id="btn-register" class="w-full bg-white text-deep-dark font-black py-5 rounded-2xl uppercase text-[11px] tracking-[0.4em] hover:bg-golf-green transition-all shadow-xl flex items-center justify-center gap-3">
                    <i class="fa-solid fa-shield-halved"></i> Initialize Passkey
                </button>
                
                <a href="profile.php" class="block text-[9px] font-black text-gray-600 uppercase tracking-widest hover:text-white transition-colors">
                    Maybe Later
                </a>
            </div>
        </div>

        <!-- Success State (Hidden initially) -->
        <div id="success-ui" class="hidden">
            <div class="py-10">
                <i class="fa-solid fa-circle-check text-6xl text-golf-green mb-6"></i>
                <h3 class="text-xl font-black text-white uppercase italic">Device Registered</h3>
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mt-2">Your hardware key is now active.</p>
                <a href="profile.php" class="mt-8 inline-block bg-white/5 border border-white/10 text-white px-10 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-black transition-all">Return to Settings</a>
            </div>
        </div>
    </div>
</div>

<!-- WebAuthn Logic -->
<script>
document.getElementById('btn-register').addEventListener('click', async () => {
    const btn = document.getElementById('btn-register');
    btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Communicating with Device...';
    btn.disabled = true;

    try {
        // 1. Fetch registration options from your backend (the "Challenge")
        const response = await fetch('handlers/passkey_registration_options.php');
        const options = await response.json();

        // Convert base64 strings from server back to buffer for WebAuthn API
        options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));
        options.user.id = Uint8Array.from(atob(options.user.id), c => c.charCodeAt(0));

        // 2. Trigger the hardware prompt (FaceID/TouchID)
        const credential = await navigator.credentials.create({ publicKey: options });

        // 3. Send the credential back to server for verification and storage
        const attestationResponse = {
            id: credential.id,
            rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))),
            type: credential.type,
            response: {
                attestationObject: btoa(String.fromCharCode(...new Uint8Array(credential.response.attestationObject))),
                clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
            },
        };

        const saveResponse = await fetch('handlers/passkey_save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(attestationResponse)
        });

        const result = await saveResponse.json();

        if (result.success) {
            document.getElementById('registration-ui').classList.add('hidden');
            document.getElementById('success-ui').classList.remove('hidden');
        } else {
            alert('Registration failed: ' + result.error);
            location.reload();
        }

    } catch (err) {
        console.error(err);
        alert('Biometric registration cancelled or not supported on this browser.');
        btn.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Initialize Passkey';
        btn.disabled = false;
    }
});
</script>

<?php include('layout/footer.php'); ?>