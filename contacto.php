<?php include_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-brand-700 py-20 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-4">Contáctanos</h1>
        <p class="text-xl opacity-90 max-w-2xl mx-auto">Estamos aquí para escucharte. Envíanos un mensaje y te responderemos lo antes posible.</p>
    </div>
</section>

<!-- Contact Form & Info -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            
            <!-- Contact Info -->
            <div class="space-y-12">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-8">Información de <span class="text-brand-700">Contacto</span></h2>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Visítanos en nuestra sede principal o comunícate con nosotros a través de nuestros canales digitales.
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start space-x-6 p-6 bg-gray-50 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-brand-700 text-white rounded-2xl flex items-center justify-center text-xl flex-shrink-0 shadow-lg">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-1">Ubicación</h4>
                            <p class="text-gray-600">Florencia, Caquetá, Colombia</p>
                            <p class="text-gray-500 text-sm italic">Puerta de Oro de la Amazonía</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-6 p-6 bg-gray-50 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-brand-700 text-white rounded-2xl flex items-center justify-center text-xl flex-shrink-0 shadow-lg">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-1">Teléfono</h4>
                            <p class="text-gray-600"><?php echo CONTACT_PHONE; ?></p>
                            <p class="text-gray-600">Atención Lunes a Viernes</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-6 p-6 bg-gray-50 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-brand-700 text-white rounded-2xl flex items-center justify-center text-xl flex-shrink-0 shadow-lg">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-1">Email</h4>
                            <p class="text-gray-600"><?php echo CONTACT_EMAIL; ?></p>
                            <p class="text-gray-500 text-sm">Respondemos en menos de 24h</p>
                        </div>
                    </div>
                </div>

                <!-- Google Map Placeholder -->
                <div class="h-64 bg-gray-200 rounded-[2.5rem] overflow-hidden shadow-inner border border-gray-100 relative group">
                    <img src="https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover opacity-60 grayscale group-hover:grayscale-0 transition-all duration-700" alt="Mapa Florencia">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <a href="https://www.google.com/maps/search/Florencia,+Caquet%C3%A1" target="_blank" class="bg-white text-brand-700 px-6 py-3 rounded-full font-bold shadow-2xl hover:bg-brand-50 transition-all transform hover:scale-110">
                            VER MAPA COMPLETO
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-gray-50 p-12 rounded-[3rem] shadow-2xl border border-gray-100">
                <h3 class="text-3xl font-bold text-gray-900 mb-8">Envíanos un Mensaje</h3>
                <form action="#" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 pl-2">Nombre Completo</label>
                            <input type="text" name="nombre" required placeholder="Tu nombre..." class="w-full px-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-2 focus:ring-brand-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 pl-2">Correo Electrónico</label>
                            <input type="email" name="email" required placeholder="tu@email.com" class="w-full px-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-2 focus:ring-brand-500 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 pl-2">Asunto</label>
                        <select name="asunto" class="w-full px-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                            <option>Información de Cursos</option>
                            <option>Servicios Corporativos</option>
                            <option>Propuesta de Alianza</option>
                            <option>Otro Asunto</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 pl-2">Mensaje</label>
                        <textarea name="mensaje" rows="6" required placeholder="¿Cómo podemos ayudarte?" class="w-full px-6 py-4 rounded-2xl border-none bg-white shadow-sm focus:ring-2 focus:ring-brand-500 transition-all"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-brand-700 text-white font-bold py-5 rounded-2xl shadow-xl hover:bg-brand-800 transition-all transform hover:-translate-y-1">
                        ENVIAR MENSAJE AHORA
                    </button>
                    <p class="text-center text-xs text-gray-400">Al enviar este formulario, aceptas nuestra política de privacidad y tratamiento de datos.</p>
                </form>
            </div>

        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
