<!DOCTYPE html>
<html lang="en" data-theme="business">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../assets/images/tasks-solid.svg" type="image/x-icon" />
</head>
<body class="bg-base-100">
    <!-- Navigation -->
    <nav class="navbar bg-base-200 shadow-md">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-tasks text-primary text-3xl mr-3"></i>
                <span class="text-2xl font-bold text-primary">TaskFlow</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-success">Create Account</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero min-h-screen bg-base-200">
        <div class="hero-content flex-col lg:flex-row-reverse">
            <div class="mockup-window border bg-base-300 max-w-2xl">
                <div class="flex justify-center bg-base-200">
                    <img src="../assets/images/TaskFlow.png">
                </div>
            </div>
            <div>
                <h1 class="text-5xl font-bold text-primary">Manage Your Tasks Efficiently</h1>
                <p class="py-6 text-xl">With TaskFlow, you can easily manage and organize your tasks. Add tasks, set due dates, and track your progress.</p>
                <div class="flex space-x-4">
                    <a href="register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket mr-2"></i>
                        Get Started
                    </a>
                    <a href="#features" class="btn btn-outline btn-lg">
                        <i class="fas fa-info-circle mr-2"></i>
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-base-100">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12 text-primary">Why Choose TaskFlow?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card bg-base-200 shadow-xl">
                    <figure class="px-10 pt-10">
                        <img src="../assets/images/colab.jpg" alt="Collaboration" class="rounded-xl h-48 w-full object-cover">
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title">Real-time Collaboration</h3>
                        <p>Work seamlessly with your team, regardless of location.</p>
                    </div>
                </div>
                <div class="card bg-base-200 shadow-xl">
                    <figure class="px-10 pt-10">
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wxMjA3fDB8MXxzZWFyY2h8M3x8cHJvZHVjdGl2aXR5fGVufDB8fHx8MTcwNzA3MjQ3Nnww&ixlib=rb-4.0.3&q=80&w=1080" alt="Productivity" class="rounded-xl h-48 w-full object-cover">
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title">Boost Productivity</h3>
                        <p>Organize tasks, set priorities, and track progress with intuitive tools.</p>
                    </div>
                </div>
                <div class="card bg-base-200 shadow-xl">
                    <figure class="px-10 pt-10">
                        <img src="https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wxMjA3fDB8MXxzZWFyY2h8Mnx8aW50ZWdyYXRpb258ZW58MHx8fHwxNzA3MDcyNDk0fDA&ixlib=rb-4.0.3&q=80&w=1080" alt="Integration" class="rounded-xl h-48 w-full object-cover">
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title">Seamless Integration</h3>
                        <p>Connect effortlessly with your favorite tools and platforms.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="bg-primary text-primary-content py-20">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Ready to Transform Your Workflow?</h2>
            <p class="text-xl mb-8">Join thousands of teams who have accelerated their productivity with TaskFlow.</p>
            <a href="/register" class="btn btn-secondary btn-lg">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Create Your Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer p-10 bg-base-200 text-base-content">
        <div>
            <i class="fas fa-tasks text-primary text-4xl"></i>
            <p class="font-bold">TaskFlow © 2024<br>Empowering Teams, Simplifying Work</p>
        </div>
        <div>
            <span class="footer-title">Quick Links</span>
            <a href="#" class="link link-hover">Home</a>
            <a href="#features" class="link link-hover">Features</a>
        </div>
        <div>
            <span class="footer-title">USE NOW</span>

            <a href="../login.php" class="link link-hover">Login</a>
            <a href="../register.php" class="link link-hover">Register</a>
        </div>
        <div>
            <span class="footer-title">Social</span>
            <div class="grid grid-flow-col gap-4">
                <a href="https://github.com/nigel772" class="text-2xl hover:text-primary"><i class="fab fa-github"></i></a>
                <a href="https://www.linkedin.com/in/nigel-becholtz/" class="text-2xl hover:text-primary"><i class="fab fa-linkedin"></i></a>
                <a href="https://becholtz.com/" class="text-2xl hover:text-primary"><i class="fas fa-user-circle"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>
