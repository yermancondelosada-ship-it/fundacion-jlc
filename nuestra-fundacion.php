<?php 
include_once 'includes/header.php'; 

// Cargar equipo de forma segura
try {
    $equipo = $db->query("SELECT * FROM equipo ORDER BY orden ASC")->fetchAll();
} catch (Exception $e) {
    $equipo = []; // Si la tabla no existe en producción, evitamos el error crítico
}
?>

<!-- Hero Section -->
<section class="bg-brand-900 py-20 text-white relative overflow-hidden">
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-5xl font-bold mb-4"><?php echo getSiteConfig('titulo_institucion', 'Nuestra Institución'); ?></h1>
        <p class="text-xl text-green-200 max-w-2xl mx-auto"><?php echo getSiteConfig('subtitulo_institucion', 'Construyendo un legado de conocimiento y sostenibilidad en el corazón de la Amazonía colombiana.'); ?></p>
    </div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-brand-700/20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
</section>

<!-- Content Section -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <!-- Historia -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-32">
            <div class="max-w-full overflow-hidden">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 border-l-8 border-brand-700 pl-6">Nuestra Historia</h2>
                <div class="text-gray-600 leading-relaxed space-y-4 break-words whitespace-normal">
                    <?php echo nl2br(getSiteConfig('historia', 'La Fundación JLC nació con la convicción de que la educación y la tecnología son los motores de cambio más poderosos para las regiones en desarrollo...')); ?>
                </div>
            </div>
            <div class="relative">
                <img src="<?php echo getSiteConfig('historia_img') ? 'uploads/img/'.getSiteConfig('historia_img') : 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80'; ?>" class="rounded-[3rem] shadow-2xl w-full h-[400px] object-cover" alt="Historia JLC">
                <div class="absolute -bottom-6 -right-6 bg-yellow-400 p-8 rounded-3xl shadow-xl hidden md:block">
                    <p class="text-brand-900 font-black text-4xl">10+</p>
                    <p class="text-brand-800 font-bold text-sm">Años de Impacto</p>
                </div>
            </div>
        </div>

        <!-- Misión y Visión -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-32">
            <!-- Misión -->
            <div class="bg-gray-50 p-12 rounded-[2.5rem] shadow-xl border border-gray-100 flex items-start space-x-6 max-w-full overflow-hidden">
                <div class="bg-yellow-400 w-16 h-16 rounded-2xl flex-shrink-0 flex items-center justify-center text-brand-900 text-2xl shadow-lg">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="flex-grow min-w-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Nuestra Misión</h3>
                    <div class="text-gray-600 leading-relaxed break-words whitespace-normal">
                        <?php echo nl2br(getSiteConfig('mision', 'Brindar herramientas educativas y tecnológicas de vanguardia...')); ?>
                    </div>
                </div>
            </div>

            <!-- Visión -->
            <div class="bg-gray-50 p-12 rounded-[2.5rem] shadow-xl border border-gray-100 flex items-start space-x-6 max-w-full overflow-hidden">
                <div class="bg-yellow-400 w-16 h-16 rounded-2xl flex-shrink-0 flex items-center justify-center text-brand-900 text-2xl shadow-lg">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="flex-grow min-w-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Nuestra Visión</h3>
                    <div class="text-gray-600 leading-relaxed break-words whitespace-normal">
                        <?php echo nl2br(getSiteConfig('vision', 'Ser reconocidos para el 2030 como la institución líder en innovación...')); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nuestra Esencia -->
        <div class="mb-32">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-16">Nuestra Esencia</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Valores -->
                <div class="bg-white p-10 rounded-[3rem] shadow-md border border-gray-100 hover:shadow-2xl transition-all duration-500 group">
                    <div class="flex items-center mb-8">
                        <?php if(getSiteConfig('valores_img')): ?>
                            <img src="uploads/institucion/<?php echo getSiteConfig('valores_img'); ?>" class="w-20 h-20 rounded-2xl object-cover mr-6 shadow-lg group-hover:scale-110 transition-transform">
                        <?php else: ?>
                            <div class="w-20 h-20 bg-brand-100 text-brand-700 rounded-2xl flex items-center justify-center text-3xl mr-6 shadow-lg">
                                <i class="fas fa-heart"></i>
                            </div>
                        <?php endif; ?>
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo getSiteConfig('valores_titulo', 'Nuestros Valores'); ?></h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        <?php echo nl2br(getSiteConfig('valores_desc')); ?>
                    </p>
                </div>

                <!-- Filosofía -->
                <div class="bg-white p-10 rounded-[3rem] shadow-md border border-gray-100 hover:shadow-2xl transition-all duration-500 group">
                    <div class="flex items-center mb-8">
                        <?php if(getSiteConfig('filosofia_img')): ?>
                            <img src="uploads/institucion/<?php echo getSiteConfig('filosofia_img'); ?>" class="w-20 h-20 rounded-2xl object-cover mr-6 shadow-lg group-hover:scale-110 transition-transform">
                        <?php else: ?>
                            <div class="w-20 h-20 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center text-3xl mr-6 shadow-lg">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                        <?php endif; ?>
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo getSiteConfig('filosofia_titulo', 'Nuestra Filosofía'); ?></h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        <?php echo nl2br(getSiteConfig('filosofia_desc')); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Perfiles Profesionales -->
        <div id="equipo">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Perfiles Profesionales</h2>
                <p class="text-gray-500 max-w-2xl mx-auto font-medium">Nuestro equipo de liderazgo académico y administrativo dedicado a tu crecimiento.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
                <?php foreach($equipo as $integrante): ?>
                <div class="bg-white rounded-[2.5rem] p-8 shadow-md border border-gray-50 text-center hover:shadow-xl transition-all duration-500 hover:-translate-y-2 group">
                    <div class="relative inline-block mb-6">
                        <div class="absolute inset-0 bg-brand-500 rounded-full scale-110 opacity-0 group-hover:opacity-20 transition-opacity"></div>
                        <img src="uploads/institucion/<?php echo $integrante->foto; ?>" class="w-40 h-40 rounded-full object-cover border-8 border-gray-50 shadow-inner mx-auto relative z-10" alt="<?php echo $integrante->nombre; ?>">
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-1"><?php echo $integrante->nombre; ?></h4>
                    <p class="text-brand-600 font-bold text-sm uppercase tracking-widest mb-4"><?php echo $integrante->cargo; ?></p>
                    <div class="h-px w-12 bg-gray-100 mx-auto mb-4"></div>
                    <p class="text-gray-500 italic text-sm leading-relaxed break-words">
                        "<?php echo nl2br($integrante->frase); ?>"
                    </p>
                </div>
                <?php endforeach; ?>
                
                <?php if(empty($equipo)): ?>
                    <div class="col-span-full py-20 text-center">
                        <div class="bg-gray-50 inline-block p-10 rounded-[3rem] border border-dashed border-gray-200">
                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-400 font-medium italic">Próximamente conocerás a nuestro equipo.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
