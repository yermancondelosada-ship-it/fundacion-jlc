<?php include_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-brand-900 py-24 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-6">Nuestros Pilares Estratégicos</h1>
        <p class="text-xl text-green-200 max-w-3xl mx-auto">Los ejes que guían nuestra labor diaria y definen nuestro impacto en la comunidad y el territorio.</p>
    </div>
</section>

<!-- Pillars Grid -->
<section class="py-24 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Tecnología e Innovación -->
            <div class="bg-white rounded-[3rem] p-10 shadow-xl border border-gray-100 flex flex-col h-full hover:shadow-2xl transition-all group">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-8 group-hover:rotate-12 transition-transform">
                    <i class="fas fa-microchip"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Tecnología e Innovación</h3>
                <p class="text-gray-600 leading-relaxed flex-grow">
                    Impulsamos la adopción de herramientas digitales y el desarrollo de software local para resolver problemas reales del Caquetá.
                </p>
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <span class="text-brand-700 font-bold text-sm uppercase tracking-widest">Eje de Desarrollo</span>
                </div>
            </div>

            <!-- Capacitación para el Trabajo -->
            <div class="bg-brand-700 rounded-[3rem] p-10 shadow-2xl flex flex-col h-full text-white relative overflow-hidden group">
                <div class="absolute -top-4 -right-4 opacity-10">
                    <i class="fas fa-graduation-cap text-9xl"></i>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-2xl mb-8">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Capacitación para el Trabajo</h3>
                <p class="text-green-50 leading-relaxed flex-grow">
                    Nuestro sistema educativo integral diseñado para dotar a los estudiantes de habilidades prácticas y certificadas.
                </p>
                <div class="mt-8">
                    <a href="aula-virtual.php" class="block w-full bg-white text-brand-700 text-center py-4 rounded-2xl font-bold shadow-lg hover:bg-green-50 transition-all transform hover:scale-105">
                        INGRESAR AL AULA
                    </a>
                </div>
            </div>

            <!-- Turismo Sostenible -->
            <div class="bg-white rounded-[3rem] p-10 shadow-xl border border-gray-100 flex flex-col h-full hover:shadow-2xl transition-all group">
                <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mb-8 group-hover:rotate-12 transition-transform">
                    <i class="fas fa-binoculars"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Turismo Sostenible</h3>
                <p class="text-gray-600 leading-relaxed flex-grow">
                    Promovemos la riqueza natural y cultural de nuestra región a través de un turismo responsable que cuide nuestra biodiversidad.
                </p>
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <span class="text-brand-700 font-bold text-sm uppercase tracking-widest">Eje Económico</span>
                </div>
            </div>

            <!-- Gestión Socio-Ambiental -->
            <div class="bg-white rounded-[3rem] p-10 shadow-xl border border-gray-100 flex flex-col h-full hover:shadow-2xl transition-all group">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl mb-8 group-hover:rotate-12 transition-transform">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Gestión Socio-Ambiental</h3>
                <p class="text-gray-600 leading-relaxed flex-grow">
                    Compromiso total con el equilibrio ecológico, la reforestación y el fortalecimiento del tejido social en nuestras comunidades.
                </p>
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <span class="text-brand-700 font-bold text-sm uppercase tracking-widest">Eje de Vida</span>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
