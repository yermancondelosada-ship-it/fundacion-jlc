    <!-- Footer Section -->
    <footer class="bg-brand-900 text-gray-300 pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <!-- Col 1: Brand -->
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="bg-white p-2 rounded-lg flex items-center justify-center">
                            <?php if(getSiteConfig('logo')): ?>
                                <img src="uploads/img/<?php echo getSiteConfig('logo'); ?>" class="h-6 w-auto" alt="Logo">
                            <?php else: ?>
                                <i class="fas fa-leaf text-brand-700 text-xl"></i>
                            <?php endif; ?>
                        </div>
                        <span class="text-white text-xl font-bold uppercase"><?php echo getSiteConfig('site_name_short', 'PLATAFORMA JLC'); ?></span>
                    </div>
                    <p class="text-sm leading-relaxed mb-6">
                        Impulsando el desarrollo tecnológico, ambiental y educativo en la región del Caquetá. Comprometidos con el futuro de nuestra comunidad.
                    </p>
                    <div class="flex space-x-4">
                        <?php if(getSiteConfig('social_fb')): ?>
                            <a href="<?php echo getSiteConfig('social_fb'); ?>" target="_blank" class="w-10 h-10 bg-brand-800 rounded-full flex items-center justify-center hover:bg-brand-600 transition-colors"><i class="fab fa-facebook-f text-white"></i></a>
                        <?php endif; ?>
                        <?php if(getSiteConfig('social_ig')): ?>
                            <a href="<?php echo getSiteConfig('social_ig'); ?>" target="_blank" class="w-10 h-10 bg-brand-800 rounded-full flex items-center justify-center hover:bg-brand-600 transition-colors"><i class="fab fa-instagram text-white"></i></a>
                        <?php endif; ?>
                        <a href="<?php echo getSiteConfig('social_wa', 'https://wa.me/'.WHATSAPP_NUMBER); ?>" target="_blank" class="w-10 h-10 bg-brand-800 rounded-full flex items-center justify-center hover:bg-brand-600 transition-colors"><i class="fab fa-whatsapp text-white"></i></a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Enlaces Rápidos</h4>
                    <ul class="space-y-4">
                        <li><a href="index.php" class="hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="pilares.php" class="hover:text-white transition-colors">Nuestros Pilares</a></li>
                        <li><a href="servicios-corporativos.php" class="hover:text-white transition-colors">Servicios</a></li>
                        <li><a href="aula-virtual.php" class="hover:text-white transition-colors">Aula Virtual</a></li>
                    </ul>
                </div>

                <!-- Col 3: Support -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Soporte</h4>
                    <ul class="space-y-4">
                        <li><a href="contacto.php" class="hover:text-white transition-colors">Contacto</a></li>
                        <li><a href="blog.php" class="hover:text-white transition-colors">Blog Académico</a></li>
                        <li><a href="propuesta-de-valor.php" class="hover:text-white transition-colors">Propuesta de Valor</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacidad</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Ubicación</h4>
                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt mt-1 text-brand-500"></i>
                            <span><?php echo getSiteConfig('contact_address', 'Florencia, Caquetá, Colombia'); ?></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone-alt text-brand-500"></i>
                            <span><?php echo getSiteConfig('contact_phone', CONTACT_PHONE); ?></span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-brand-500"></i>
                            <span><?php echo getSiteConfig('contact_email', CONTACT_EMAIL); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-brand-800 pt-8 flex flex-col md:row justify-between items-center text-sm">
                <p>&copy; <?php echo date('Y'); ?> <?php echo getSiteConfig('site_name', 'Fundación JLC'); ?>. Todos los derechos reservados.</p>
                <p class="mt-2 md:mt-0">Diseñado con <i class="fas fa-heart text-red-500"></i> para el Caquetá.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <?php $wa_link = getSiteConfig('social_wa', 'https://wa.me/'.WHATSAPP_NUMBER); ?>
    <a href="<?php echo $wa_link; ?>" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-16 h-16 rounded-full shadow-2xl flex items-center justify-center text-3xl hover:bg-green-600 transition-all transform hover:scale-110 z-50 animate-bounce">
        <i class="fab fa-whatsapp"></i>
        <span class="absolute -top-1 -right-1 flex h-5 w-5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-5 w-5 bg-green-500 border-2 border-white"></span>
        </span>
    </a>

    <!-- Floating "Escríbenos" Sidebar (Desktop) -->
    <div class="fixed right-0 top-1/2 -translate-y-1/2 hidden lg:flex flex-col space-y-2 z-40">
        <a href="contacto.php" class="bg-brand-700 text-white py-3 px-4 rounded-l-xl flex items-center space-x-2 shadow-lg transform hover:-translate-x-2 transition-transform">
            <i class="fas fa-paper-plane"></i>
            <span class="font-bold text-sm tracking-wider uppercase" style="writing-mode: vertical-rl;">Escríbenos</span>
        </a>
    </div>

    <!-- SDK de Facebook -->
    <div id="fb-root"></div>
    <script async defer src="https://connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v19.0"></script>
</body>
</html>
