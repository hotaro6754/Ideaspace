<?php
/**
 * IdeaSync - Post Idea
 */
require_once __DIR__ . '/../../config/Database.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/?page=login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Idea | IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-primary text-primary">
    <header class="navbar">
        <div class="container navbar-inner">
            <a href="/" class="logo">IDEASYNC</a>
            <a href="/?page=feed" class="text-secondary text-sm">Cancel</a>
        </div>
    </header>

    <main class="container py-20">
        <div style="max-width: 600px; margin: 0 auto;">
            <h1 class="mb-2">What's the vision?</h1>
            <p class="text-secondary mb-12">Your idea will be visible to all builders in Lendi. Make it count.</p>

            <form action="/src/controllers/ideas.php" method="POST">
                <input type="hidden" name="action" value="create">

                <div class="input-group">
                    <label>Idea Title</label>
                    <input type="text" name="title" class="form-input" placeholder="e.g. AI-Powered Smart Canteen System" required>
                </div>

                <div class="input-group">
                    <label>Domain</label>
                    <select name="domain" class="form-select" required>
                        <option value="AI/ML">AI & Machine Learning</option>
                        <option value="Web Development">Web Development</option>
                        <option value="Mobile Development">App Development</option>
                        <option value="IoT">IoT & Hardware</option>
                        <option value="Cybersecurity">Cybersecurity</option>
                        <option value="Blockchain">Blockchain & Web3</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Explain the problem and your proposed solution..." style="min-height: 200px;" required></textarea>
                </div>

                <div class="input-group">
                    <label>Skills Needed (Press comma to add)</label>
                    <input type="text" name="skills_input" class="form-input" placeholder="e.g. React, Python, Figma">
                    <input type="hidden" name="skills_needed" id="skills_hidden" value="[]">
                    <div id="skills_container" class="flex gap-2 mt-2 flex-wrap"></div>
                </div>

                <button type="submit" class="btn btn-primary w-full py-4 mt-8">Post Idea +10 Points</button>
            </form>
        </div>
    </main>

    <script>
        lucide.createIcons();

        const skillsInput = document.querySelector('input[name="skills_input"]');
        const skillsContainer = document.querySelector('#skills_container');
        const skillsHidden = document.querySelector('#skills_hidden');
        let skills = [];

        skillsInput.addEventListener('keydown', (e) => {
            if (e.key === ',' || e.key === 'Enter') {
                e.preventDefault();
                const val = skillsInput.value.trim().replace(',', '');
                if (val && !skills.includes(val)) {
                    skills.push(val);
                    renderSkills();
                }
                skillsInput.value = '';
            }
        });

        function renderSkills() {
            skillsContainer.innerHTML = skills.map(s => `<span class="badge badge-accent">${s}</span>`).join('');
            skillsHidden.value = JSON.stringify(skills);
        }
    </script>
</body>
</html>
