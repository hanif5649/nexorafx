<?php include('layout/header.php'); ?>

<!-- HERO -->
<section class="relative min-h-[65vh] bg-white flex items-center pt-20 overflow-hidden">

    <div class="absolute top-0 right-0 w-1/2 h-full bg-slate-light -skew-x-12 translate-x-20"></div>

    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center relative z-10">

        <!-- Left -->
        <div>

            <div class="inline-flex items-center gap-3 bg-golf-green/10 px-4 py-2 rounded-full mb-8">
                <span class="w-2 h-2 bg-golf-green rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-golf-green">
                    Contact Our Advisory Team
                </span>
            </div>

            <h1 class="font-syne text-6xl font-bold tracking-tighter leading-tight text-navy-dark mb-6">
                Let’s Talk <br>
                <span class="text-golf-green italic">About Growth</span>
            </h1>

            <p class="text-slate-500 text-lg leading-relaxed max-w-xl">
                Whether you are an individual investor, institution, or family office,
                our advisors are ready to help you build and preserve wealth.
            </p>

        </div>

        <!-- Right -->
        <div>

            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&q=80&w=1000"
                 class="rounded-[3rem] h-[550px] w-full object-cover shadow-2xl">

        </div>

    </div>

</section>



<!-- CONTACT INFO -->
<section class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid md:grid-cols-3 gap-8">

            <!-- Phone -->
            <div class="bg-slate-light p-10 rounded-3xl text-center">

                <i class="fa-solid fa-phone text-4xl text-golf-green mb-6"></i>

                <h3 class="text-xl font-bold text-navy-dark mb-3">
                    Call Us
                </h3>

                <p class="text-slate-500">
                    +234 903 021 1894
                </p>

            </div>


            <!-- Email -->
            <div class="bg-slate-light p-10 rounded-3xl text-center">

                <i class="fa-solid fa-envelope text-4xl text-golf-green mb-6"></i>

                <h3 class="text-xl font-bold text-navy-dark mb-3">
                    Email Us
                </h3>

                <p class="text-slate-500">
                    hanifatijani@gmail.com
                </p>

            </div>


            <!-- Address -->
            <div class="bg-slate-light p-10 rounded-3xl text-center">

                <i class="fa-solid fa-location-dot text-4xl text-golf-green mb-6"></i>

                <h3 class="text-xl font-bold text-navy-dark mb-3">
                    Visit Office
                </h3>

                <p class="text-slate-500">
                    245 Madison Avenue,<br>
                    New York, NY 10016
                </p>

            </div>

        </div>

    </div>

</section>



<!-- FORM + HOURS -->
<section class="py-32 bg-slate-light">

    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-start">

        <!-- FORM -->
        <div class="bg-white rounded-[2rem] p-10 shadow-xl">

            <h2 class="font-syne text-4xl font-bold text-navy-dark mb-8">
                Send Us A Message
            </h2>

            <form action="process-contact.php" method="POST" class="space-y-6">

                <div>
                    <input type="text"
                           name="name"
                           placeholder="Full Name"
                           class="w-full border border-slate-200 rounded-xl px-5 py-4 focus:outline-none">
                </div>

                <div>
                    <input type="email"
                           name="email"
                           placeholder="Email Address"
                           class="w-full border border-slate-200 rounded-xl px-5 py-4 focus:outline-none">
                </div>

                <div>
                    <input type="text"
                           name="subject"
                           placeholder="Subject"
                           class="w-full border border-slate-200 rounded-xl px-5 py-4 focus:outline-none">
                </div>

                <div>
                    <textarea name="message"
                              rows="6"
                              placeholder="Tell us about your investment goals..."
                              class="w-full border border-slate-200 rounded-xl px-5 py-4 focus:outline-none"></textarea>
                </div>

                <button type="submit"
                        class="bg-navy-dark text-white px-8 py-4 rounded-xl font-bold uppercase text-sm tracking-widest hover:bg-golf-green transition">
                    Send Message
                </button>

            </form>

        </div>



        <!-- HOURS -->
        <div>

            <h2 class="font-syne text-4xl font-bold text-navy-dark mb-8">
                Office Hours
            </h2>

            <div class="bg-white rounded-[2rem] p-10 shadow-xl mb-8">

                <div class="space-y-5">

                    <div class="flex justify-between border-b pb-4">
                        <span class="font-medium">Monday - Friday</span>
                        <span class="text-golf-green font-bold">8:00 AM - 6:00 PM</span>
                    </div>

                    <div class="flex justify-between border-b pb-4">
                        <span class="font-medium">Saturday</span>
                        <span class="text-golf-green font-bold">9:00 AM - 2:00 PM</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">Sunday</span>
                        <span class="text-red-500 font-bold">Closed</span>
                    </div>

                </div>

            </div>



            <!-- Quick Note -->
            <div class="bg-navy-dark rounded-[2rem] p-10 text-white">

                <h3 class="text-2xl font-bold mb-4">
                    Priority Advisory Support
                </h3>

                <p class="text-slate-300 leading-relaxed">
                    Institutional clients and family offices receive dedicated advisory support
                    and personalized portfolio consultations.
                </p>

            </div>

        </div>

    </div>

</section>



<!-- MAP -->
<section class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-6">

        <div class="rounded-[2rem] overflow-hidden shadow-xl">

            <iframe
                src="https://maps.google.com/maps?q=new%20york&t=&z=13&ie=UTF8&iwloc=&output=embed"
                width="100%"
                height="500"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>

        </div>

    </div>

</section>



<!-- CTA -->
<section class="py-24 bg-golf-green text-center">

    <div class="max-w-4xl mx-auto px-6">

        <h2 class="font-syne text-5xl font-bold text-navy-dark mb-6">
            Ready To Build Long-Term Wealth?
        </h2>

        <a href="register.php"
           class="inline-block bg-navy-dark text-white px-10 py-5 rounded-xl font-bold uppercase text-sm tracking-widest">
            Open Investment Account
        </a>

    </div>

</section>

<?php include('layout/footer.php'); ?>