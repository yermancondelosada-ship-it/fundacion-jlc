<?php include_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-brand-700 py-20 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-4">Nuestra Propuesta de Valor</h1>
        <p class="text-xl opacity-90 max-w-2xl mx-auto">Lo que nos hace únicos y por qué somos el aliado ideal para tu crecimiento.</p>
    </div>
</section>

<?php 
// Cargar propuestas desde la base de datos
$propuestas_db = $db->query("SELECT * FROM propuestas_valor ORDER BY id ASC")->fetchAll();
?>

<!-- Values List -->
<section class="py-24 bg-white overflow-hidden">
    <div class="container mx-auto px-4">
        
        <?php if(empty($propuestas_db)): ?>
            <div class="text-center py-20">
                <i class="fas fa-gem text-gray-100 text-9xl mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-300">Nuestra propuesta se está actualizando...</h3>
            </div>
        <?php else: ?>
            <?php foreach($propuestas_db as $index => $prop): ?>
            <div class="flex flex-col <?php echo ($index % 2 == 0) ? 'lg:flex-row' : 'lg:flex-row-reverse'; ?> items-center gap-16 mb-32">
                <div class="lg:w-1/2">
                    <div class="relative">
                        <?php if($prop->imagen): ?>
                            <img src="uploads/img/<?php echo $prop->imagen; ?>" class="rounded-[4rem] shadow-2xl relative z-10 w-full object-cover aspect-video" alt="Propuesta">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1524178232363-1fb280714553?auto=format&fit=crop&w=800&q=80" class="rounded-[4rem] shadow-2xl relative z-10 w-full" alt="Propuesta">
                        <?php endif; ?>
                        <div class="absolute -top-8 -left-8 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl"></div>
                    </div>
                </div>
                <div class="lg:w-1/2">
                    <span class="text-brand-700 font-bold uppercase tracking-[0.3em] text-sm mb-4 block">Eje 0<?php echo $index + 1; ?></span>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6"><?php echo $prop->titulo; ?></h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-8">
                        <?php echo $prop->descripcion; ?>
                    </p>
                    <a href="aula-virtual.php" class="inline-flex items-center text-brand-700 font-black hover:translate-x-4 transition-transform">
                        EXPLORAR PROGRAMAS <i class="fas fa-arrow-right ml-3"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
