        </main>

        <!-- Footer -->
        <footer class="bg-dark text-white py-12">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="md:col-span-2">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-book-open text-3xl text-accent mr-3"></i>
                            <span class="text-2xl font-bold"><?php echo SITE_NAME; ?></span>
                        </div>
                        <p class="text-gray-300 mb-6">Discover your next favorite book and track your reading journey with our vibrant community.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="<?php echo SITE_URL; ?>/index.php" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/pages/search.php" class="text-gray-300 hover:text-white transition-colors">Search Books</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Popular Books</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">New Releases</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold mb-4">Account</h3>
                        <ul class="space-y-2">
                            <?php if(isLoggedIn()): ?>
                                <li><a href="<?php echo SITE_URL; ?>/pages/my_books.php" class="text-gray-300 hover:text-white transition-colors">My Books</a></li>
                                <li><a href="<?php echo SITE_URL; ?>/pages/profile.php" class="text-gray-300 hover:text-white transition-colors">Profile</a></li>
                            <?php else: ?>
                                <li><a href="<?php echo SITE_URL; ?>/pages/login.php" class="text-gray-300 hover:text-white transition-colors">Login</a></li>
                                <li><a href="<?php echo SITE_URL; ?>/pages/register.php" class="text-gray-300 hover:text-white transition-colors">Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold mb-4">Newsletter</h3>
                        <p class="text-gray-300 mb-4">Subscribe for book recommendations and updates</p>
                        <form class="flex">
                            <input type="email" placeholder="Your email" class="px-4 py-2 rounded-l-lg focus:outline-none text-dark w-full">
                            <button type="submit" class="bg-accent hover:bg-yellow-600 px-4 py-2 rounded-r-lg transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Mobile Menu (hidden by default) -->
        <div id="mobile-menu" class="fixed inset-0 bg-dark bg-opacity-95 z-50 hidden">
            <div class="container mx-auto px-4 py-8">
                <div class="flex justify-between items-center mb-8">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="flex items-center space-x-2">
                        <i class="fas fa-book-open text-2xl text-accent"></i>
                        <span class="text-2xl font-bold"><?php echo SITE_NAME; ?></span>
                    </a>
                    <button id="close-menu" class="text-3xl focus:outline-none">&times;</button>
                </div>
                
                <nav class="space-y-4">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="block text-xl py-2 border-b border-gray-700">
                        <i class="fas fa-home mr-3"></i>Home
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/search.php" class="block text-xl py-2 border-b border-gray-700">
                        <i class="fas fa-search mr-3"></i>Search
                    </a>
                    <?php if(isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/my_books.php" class="block text-xl py-2 border-b border-gray-700">
                        <i class="fas fa-bookmark mr-3"></i>My Books
                    </a>
                    <?php endif; ?>
                    
                    <div class="pt-4">
                        <?php if(isLoggedIn()): ?>
                            <a href="<?php echo SITE_URL; ?>/pages/profile.php" class="btn-primary inline-block w-full text-center mb-3">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="<?php echo SITE_URL; ?>/pages/logout.php" class="btn-outline inline-block w-full text-center">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/pages/login.php" class="btn-primary inline-block w-full text-center mb-3">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn-outline inline-block w-full text-center">
                                Register
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </div>

        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
        <script>
            // Mobile menu toggle
            document.querySelector('header button').addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.remove('hidden');
            });
            
            document.getElementById('close-menu').addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        </script>
    </body>
</html>