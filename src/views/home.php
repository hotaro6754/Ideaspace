<?php
/**
 * IdeaSync - Professional Landing Page
 * Collab Platform | Post Ideas + Find Teammates + Build Together
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IdeaSync - Connect Ideas with Talent</title>
    <meta name="description" content="Campus collaboration platform where visionaries meet builders. Post ideas, find teammates, build projects.">
    <meta name="theme-color" content="#1E293B">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body>
    <!-- NAVBAR -->
    <header class="navbar">
        <div class="container">
            <div class="flex-between">
                <a href="<?php echo BASE_URL; ?>/" class="navbar-brand">IdeaSync</a>
                <nav class="navbar-menu" id="navMenu">
                    <a href="#features">Features</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#faq">FAQ</a>
                </nav>
                <div class="flex gap-4" style="align-items: center;">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo BASE_URL; ?>/?page=dashboard" class="btn btn-primary btn-sm">Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/?page=login" class="btn btn-ghost btn-sm">Sign In</a>
                        <a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-primary btn-sm">Get Started</a>
                    <?php endif; ?>
                </div>
                <button class="navbar-hamburger" id="hamburger" style="display:none; background:none; border:none; font-size:1.5rem; cursor:pointer;">☰</button>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container">
            <div class="section-center">
                <h1 class="hero-title fade-in">Connect Ideas with Talent</h1>
                <p class="hero-subtitle fade-in">The campus collaboration platform where visionaries post ideas and builders find their perfect team</p>

                <div class="flex gap-4 flex-center mt-8" style="flex-wrap: wrap; justify-content: center;">
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-primary btn-lg">Start Free Today</a>
                        <a href="#features" class="btn btn-tertiary btn-lg">Learn More</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn btn-primary btn-lg">Post Your Idea</a>
                        <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn btn-tertiary btn-lg">Browse Ideas</a>
                    <?php endif; ?>
                </div>

                <!-- Social Proof -->
                <div class="mt-12 text-center fade-in" style="margin-top: 3rem;">
                    <p style="color: rgba(255,255,255,0.85); margin-bottom: 1.5rem; font-weight: 500;">Trusted by 500+ students across 5 colleges</p>
                    <div class="flex gap-8 flex-center flex-wrap" style="justify-content: center;">
                        <div><div style="font-size: 1.875rem; font-weight: 700;">500+</div><div style="font-size: 0.875rem; opacity: 0.8;">Ideas Posted</div></div>
                        <div><div style="font-size: 1.875rem; font-weight: 700;">200+</div><div style="font-size: 0.875rem; opacity: 0.8;">Active Teams</div></div>
                        <div><div style="font-size: 1.875rem; font-weight: 700;">1K+</div><div style="font-size: 0.875rem; opacity: 0.8;">Users</div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section id="features" class="py-20 px-4">
        <div class="container">
            <div class="section-header">
                <h2>Powerful Features for Collaboration</h2>
                <p>Everything you need to turn ideas into reality</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card"><div class="feature-icon">💡</div><h3 class="feature-title">Post Ideas</h3><p class="feature-description">Share innovative ideas. Define required skills and timeline. Let the right people find you.</p></div>
                <div class="feature-card"><div class="feature-icon">🔍</div><h3 class="feature-title">Find Teams</h3><p class="feature-description">Browse 500+ ideas. Filter by domain, skills, difficulty. Join projects you're passionate about.</p></div>
                <div class="feature-card"><div class="feature-icon">👥</div><h3 class="feature-title">Build Together</h3><p class="feature-description">Manage collaborations with roles, permissions, and communication tools. Stay aligned.</p></div>
                <div class="feature-card"><div class="feature-icon">💬</div><h3 class="feature-title">Team Channels</h3><p class="feature-description">Chat with teammates in organized channels. Share files and updates in one place.</p></div>
                <div class="feature-card"><div class="feature-icon">🎯</div><h3 class="feature-title">Events</h3><p class="feature-description">Host hack-a-thons and workshops. Track RSVPs and manage team events seamlessly.</p></div>
                <div class="feature-card"><div class="feature-icon">⭐</div><h3 class="feature-title">Builder Rank</h3><p class="feature-description">Climb ranks from INITIATE to LEGEND. Earn badges and build your portfolio.</p></div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section id="how-it-works" class="py-20 px-4" style="background-color: var(--color-bg-secondary);">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Get started in 3 simple steps</p>
            </div>
            <div style="max-width: 900px; margin: 3rem auto;">
                <div class="section-two-col" style="margin-bottom: 2rem;">
                    <div><div style="font-size: 3rem; font-weight: 900; color: var(--color-accent-600); margin-bottom: 1rem;">1</div><h3>Create Profile</h3><p style="color: var(--color-text-secondary); line-height: 1.6;">Sign up and tell us your skills, interests, and whether you're a visionary or builder.</p></div>
                    <div style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(8, 145, 178, 0.1)); border-radius: 1rem; padding: 3rem; display: flex; align-items: center; justify-content: center;"><div style="font-size: 4rem;">👤</div></div>
                </div>
                <div class="section-two-col" style="margin-bottom: 2rem;">
                    <div style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(8, 145, 178, 0.1)); border-radius: 1rem; padding: 3rem; display: flex; align-items: center; justify-content: center;"><div style="font-size: 4rem;">💼</div></div>
                    <div><div style="font-size: 3rem; font-weight: 900; color: var(--color-accent-600); margin-bottom: 1rem;">2</div><h3>Post or Apply</h3><p style="color: var(--color-text-secondary); line-height: 1.6;">Post your idea or browse and apply to projects that excite you.</p></div>
                </div>
                <div class="section-two-col">
                    <div><div style="font-size: 3rem; font-weight: 900; color: var(--color-accent-600); margin-bottom: 1rem;">3</div><h3>Build Together</h3><p style="color: var(--color-text-secondary); line-height: 1.6;">Form your team, create channels, track progress, and ship amazing projects.</p></div>
                    <div style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(8, 145, 178, 0.1)); border-radius: 1rem; padding: 3rem; display: flex; align-items: center; justify-content: center;"><div style="font-size: 4rem;">🚀</div></div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="py-20 px-4">
        <div class="container">
            <div class="section-header">
                <h2>What Users Say</h2>
                <p>Real stories from builders and visionaries</p>
            </div>
            <div class="feature-grid">
                <div class="card"><div class="card-body"><div style="margin-bottom: 0.5rem; font-size: 1rem;">⭐⭐⭐⭐⭐</div><p style="font-style: italic; margin-bottom: 1rem; color: var(--color-text-secondary);">"IdeaSync connected me with the perfect co-founder. We shipped an MVP in 3 months!"</p><div style="font-weight: 600;">Priya Sharma</div><div style="font-size: 0.875rem; color: var(--color-text-secondary);">Founder, TechVenture</div></div></div>
                <div class="card"><div class="card-body"><div style="margin-bottom: 0.5rem; font-size: 1rem;">⭐⭐⭐⭐⭐</div><p style="font-style: italic; margin-bottom: 1rem; color: var(--color-text-secondary);">"As someone new to building, IdeaSync helped me find mentors and a supportive community."</p><div style="font-weight: 600;">Arjun Patel</div><div style="font-size: 0.875rem; color: var(--color-text-secondary);">Builder & Developer</div></div></div>
                <div class="card"><div class="card-body"><div style="margin-bottom: 0.5rem; font-size: 1rem;">⭐⭐⭐⭐⭐</div><p style="font-style: italic; margin-bottom: 1rem; color: var(--color-text-secondary);">"IdeaSync is the perfect place to turn campus ideas into real projects. Love the community!"</p><div style="font-weight: 600;">Maya Singh</div><div style="font-size: 0.875rem; color: var(--color-text-secondary);">College Innovator</div></div></div>
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section id="pricing" class="py-20 px-4" style="background-color: var(--color-bg-secondary);">
        <div class="container">
            <div class="section-header">
                <h2>Simple, Transparent Pricing</h2>
                <p>Free for students. Upgrade for advanced features.</p>
            </div>
            <div class="feature-grid">
                <div class="card"><div class="card-body"><h3>Free</h3><div style="font-size: 2.25rem; font-weight: bold; margin: 1rem 0; color: var(--color-accent-600);">$0</div><p style="color: var(--color-text-secondary); margin-bottom: 1.5rem;">Perfect to get started</p><ul style="list-style: none; margin-bottom: 1.5rem;"><li style="padding: 0.375rem 0;">✓ Post up to 3 ideas</li><li style="padding: 0.375rem 0;">✓ Join unlimited teams</li><li style="padding: 0.375rem 0;">✓ Basic messaging</li><li style="padding: 0.375rem 0;">✓ Profile</li></ul><a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-secondary btn-block">Get Started</a></div></div>
                <div class="card card-accent" style="position: relative;"><div class="card-body"><div style="display: inline-block; background: var(--color-accent-600); color: white; padding: 0.25rem 0.625rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 600; margin-bottom: 0.5rem;">POPULAR</div><h3>Pro</h3><div style="font-size: 2.25rem; font-weight: bold; margin: 1rem 0; color: var(--color-accent-600);">$9<span style="font-size: 0.875rem; color: var(--color-text-secondary);">/mo</span></div><p style="color: var(--color-text-secondary); margin-bottom: 1.5rem;">For serious builders</p><ul style="list-style: none; margin-bottom: 1.5rem;"><li style="padding: 0.375rem 0;">✓ Unlimited ideas</li><li style="padding: 0.375rem 0;">✓ Advanced analytics</li><li style="padding: 0.375rem 0;">✓ Priority messaging</li><li style="padding: 0.375rem 0;">✓ File uploads (5GB)</li><li style="padding: 0.375rem 0;">✓ Enhanced visibility</li></ul><a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-primary btn-block">Start Trial</a></div></div>
                <div class="card"><div class="card-body"><h3>Enterprise</h3><div style="font-size: 2.25rem; font-weight: bold; margin: 1rem 0; color: var(--color-accent-600);">Custom</div><p style="color: var(--color-text-secondary); margin-bottom: 1.5rem;">For organizations</p><ul style="list-style: none; margin-bottom: 1.5rem;"><li style="padding: 0.375rem 0;">✓ Dedicated support</li><li style="padding: 0.375rem 0;">✓ Custom integrations</li><li style="padding: 0.375rem 0;">✓ Advanced security</li><li style="padding: 0.375rem 0;">✓ White-label options</li></ul><button class="btn btn-tertiary btn-block">Contact Sales</button></div></div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-20 px-4">
        <div class="container" style="max-width: 768px;">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Everything you need to know</p>
            </div>
            <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 1rem;">
                <details class="card" style="cursor: pointer;"><summary class="card-header" style="cursor: pointer; font-weight: 600; display: flex; justify-content: space-between;"><span>Is IdeaSync really free?</span><span>+</span></summary><div class="card-body">Yes! Free for students with Pro optional. Free includes posting ideas, joining teams, and basic messaging.</div></details>
                <details class="card" style="cursor: pointer;"><summary class="card-header" style="cursor: pointer; font-weight: 600; display: flex; justify-content: space-between;"><span>How do I post my first idea?</span><span>+</span></summary><div class="card-body">Sign up, complete your profile, click "Post Idea" in your dashboard. Takes about 5 minutes.</div></details>
                <details class="card" style="cursor: pointer;"><summary class="card-header" style="cursor: pointer; font-weight: 600; display: flex; justify-content: space-between;"><span>Can I use it for coursework?</span><span>+</span></summary><div class="card-body">Absolutely! Perfect for group projects, coursework, and hackathon teams.</div></details>
                <details class="card" style="cursor: pointer;"><summary class="card-header" style="cursor: pointer; font-weight: 600; display: flex; justify-content: space-between;"><span>How is my data safe?</span><span>+</span></summary><div class="card-body">We use encryption, secure hashing, and comply with GDPR and student privacy regulations.</div></details>
                <details class="card" style="cursor: pointer;"><summary class="card-header" style="cursor: pointer; font-weight: 600; display: flex; justify-content: space-between;"><span>What if I need help?</span><span>+</span></summary><div class="card-body">We have help center, email support, and active community forum. Pro users get 24-hour response time.</div></details>
            </div>
        </div>
    </section>

    <!-- FINAL CTA -->
    <section class="py-20 px-4" style="background: var(--gradient-primary); color: white; text-align: center;">
        <div class="container" style="max-width: 600px;">
            <h2 style="color: white; margin-bottom: 1rem;">Ready to start collaborating?</h2>
            <p style="margin-bottom: 2rem; opacity: 0.9;">Join 1000+ students building amazing projects</p>
            <div class="flex gap-4 flex-center" style="flex-wrap: wrap;">
                <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>/?page=register" class="btn" style="background: white; color: var(--color-primary-800); padding: 0.75rem 2rem; border-radius: var(--radius-lg); font-weight: 600;">Get Started Free</a>
                    <a href="<?php echo BASE_URL; ?>/?page=login" class="btn btn-tertiary" style="border-color: white; color: white;">Sign In</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/?page=dashboard" class="btn" style="background: white; color: var(--color-primary-800); padding: 0.75rem 2rem; border-radius: var(--radius-lg); font-weight: 600;">Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer style="background-color: var(--color-bg-secondary); border-top: 1px solid var(--color-border); padding: 3rem 1rem; margin-top: 4rem;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                <div><h4 style="margin-bottom: 1rem;">Product</h4><ul style="list-style: none;"><li><a href="#features">Features</a></li><li><a href="#pricing">Pricing</a></li><li><a href="#faq">FAQ</a></li></ul></div>
                <div><h4 style="margin-bottom: 1rem;">Community</h4><ul style="list-style: none;"><li><a href="#">Forum</a></li><li><a href="#">Discord</a></li><li><a href="#">Events</a></li></ul></div>
                <div><h4 style="margin-bottom: 1rem;">Company</h4><ul style="list-style: none;"><li><a href="#">About</a></li><li><a href="#">Privacy</a></li><li><a href="#">Terms</a></li></ul></div>
                <div><h4 style="margin-bottom: 1rem;">Support</h4><ul style="list-style: none;"><li><a href="#">Help</a></li><li><a href="#">Contact</a></li><li><a href="#">Report Bug</a></li></ul></div>
            </div>
            <div style="text-align: center; padding-top: 2rem; border-top: 1px solid var(--color-border); color: var(--color-text-secondary); font-size: 0.875rem;">
                <p>&copy; 2024 IdeaSync. All rights reserved. Built with ❤️ for campus builders.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile hamburger menu
        document.getElementById('hamburger')?.addEventListener('click', function() {
            document.getElementById('navMenu')?.classList.toggle('active');
        });
        document.querySelectorAll('.navbar-menu a').forEach(link => {
            link.addEventListener('click', () => document.getElementById('navMenu')?.classList.remove('active'));
        });

        // FAQ toggle
        document.querySelectorAll('details').forEach(detail => {
            detail.addEventListener('toggle', function() {
                const summary = this.querySelector('summary span:last-child');
                if(summary) summary.textContent = this.open ? '−' : '+';
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if(href !== '#') {
                    e.preventDefault();
                    document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
