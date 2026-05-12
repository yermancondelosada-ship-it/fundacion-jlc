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
            <?php 
            $colors = [
                1 => ['bg' => 'bg-white hover:shadow-2xl', 'icon_bg' => 'bg-blue-100 text-blue-600', 'text' => 'text-gray-900', 'desc' => 'text-gray-600', 'eje' => 'Eje de Desarrollo', 'border' => 'border border-gray-100'],
                2 => ['bg' => 'bg-brand-700 text-white relative overflow-hidden', 'icon_bg' => 'bg-white/20', 'text' => 'text-white', 'desc' => 'text-green-50', 'btn' => true, 'border' => ''],
                3 => ['bg' => 'bg-white hover:shadow-2xl', 'icon_bg' => 'bg-orange-100 text-orange-600', 'text' => 'text-gray-900', 'desc' => 'text-gray-600', 'eje' => 'Eje Económico', 'border' => 'border border-gray-100'],
                4 => ['bg' => 'bg-white hover:shadow-2xl', 'icon_bg' => 'bg-green-100 text-green-600', 'text' => 'text-gray-900', 'desc' => 'text-gray-600', 'eje' => 'Eje de Vida', 'border' => 'border border-gray-100']
            ];
            $default_icons = ['fas fa-microchip', 'fas fa-book-reader', 'fas fa-globe-americas', 'fas fa-seedling'];
            $default_titles = ['Tecnología e Innovación', 'Educación Multinivel', 'Turismo Sostenible', 'Gestión Socio-Ambiental'];
            
            for ($i = 1; $i <= 4; $i++): 
                $icono = getSiteConfig("pilar_{$i}_icono", $default_icons[$i-1]);
                $titulo = getSiteConfig("pilar_{$i}_titulo", $default_titles[$i-1]);
                $desc = getSiteConfig("pilar_{$i}_desc", 'Descripción no disponible.');
                $c = $colors[$i];
            ?>
            <div class="<?php echo $c['bg']; ?> rounded-[3rem] p-10 shadow-xl flex flex-col h-full transition-all group <?php echo $c['border']; ?>">
                <?php if(isset($c['btn'])): ?>
                    <div class="absolute -top-4 -right-4 opacity-10">
                        <i class="<?php echo $icono; ?> text-9xl"></i>
                    </div>
                <?php endif; ?>
                <div class="w-16 h-16 <?php echo $c['icon_bg']; ?> rounded-2xl flex items-center justify-center text-2xl mb-8 group-hover:rotate-12 transition-transform">
                    <i class="<?php echo $icono; ?>"></i>
                </div>
                <h3 class="text-2xl font-bold <?php echo $c['text']; ?> mb-4"><?php echo $titulo; ?></h3>
                <p class="<?php echo $c['desc']; ?> leading-relaxed flex-grow">
                    <?php echo $desc; ?>
                </p>
                <?php if(isset($c['btn'])): ?>
                    <div class="mt-8">
                        <a href="aula-virtual.php" class="block w-full bg-white text-brand-700 text-center py-4 rounded-2xl font-bold shadow-lg hover:bg-green-50 transition-all transform hover:scale-105">
                            INGRESAR AL AULA
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <span class="text-brand-700 font-bold text-sm uppercase tracking-widest"><?php echo $c['eje']; ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
