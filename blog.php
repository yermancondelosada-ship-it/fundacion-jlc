<?php 
include_once 'includes/header.php'; 

// Fetch blog posts from DB
$db = Database::getInstance();
$blog_posts = $db->query("SELECT * FROM blog_posts WHERE estado = 'publicado' ORDER BY fecha_publicacion DESC")->fetchAll();
?>

<!-- Hero Section -->
<section class="bg-brand-900 py-20 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-4"><?php echo getSiteConfig('blog_hero_titulo', 'Blog Académico'); ?></h1>
        <p class="text-xl text-green-200 max-w-2xl mx-auto"><?php echo getSiteConfig('blog_hero_subtitulo', 'Explora las últimas tendencias en educación, tecnología y sostenibilidad en la región.'); ?></p>
    </div>
</section>

<!-- Blog Grid -->
<section class="py-24 bg-gray-50">
    <div class="container mx-auto px-4">
        
        <!-- Search & Categories -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-16 gap-6">
            <div class="flex flex-wrap gap-3">
                <button class="bg-brand-700 text-white px-6 py-2 rounded-full font-bold shadow-md">Todos</button>
                <button class="bg-white text-gray-600 px-6 py-2 rounded-full font-bold hover:bg-brand-100 transition-colors">Tecnología</button>
                <button class="bg-white text-gray-600 px-6 py-2 rounded-full font-bold hover:bg-brand-100 transition-colors">Educación</button>
                <button class="bg-white text-gray-600 px-6 py-2 rounded-full font-bold hover:bg-brand-100 transition-colors">Ambiental</button>
            </div>
            <div class="relative w-full md:w-80">
                <input type="text" placeholder="Buscar artículos..." class="w-full px-6 py-3 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 shadow-sm">
                <i class="fas fa-search absolute right-6 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            
            <?php if (empty($blog_posts)): ?>
                <div class="col-span-full text-center py-20 bg-white rounded-[3rem] shadow-xl">
                    <i class="fas fa-newspaper text-gray-200 text-7xl mb-6"></i>
                    <h3 class="text-2xl font-bold text-gray-400">Aún no hay artículos publicados.</h3>
                    <p class="text-gray-400">Vuelve pronto para ver nuestras novedades.</p>
                </div>
            <?php else: ?>
                <?php foreach ($blog_posts as $post): ?>
                <article class="bg-white rounded-[2.5rem] overflow-hidden shadow-xl hover:shadow-2xl transition-all group">
                    <div class="relative h-64 overflow-hidden">
                        <?php if($post->imagen_destacada): ?>
                            <img src="uploads/img/<?php echo $post->imagen_destacada; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="<?php echo $post->titulo; ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Post">
                        <?php endif; ?>
                        <span class="absolute top-6 left-6 bg-brand-700 text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-lg shadow-lg"><?php echo $post->categoria; ?></span>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center text-xs text-gray-400 mb-4 space-x-4">
                            <span><i class="far fa-calendar-alt mr-2"></i> <?php echo date('d M, Y', strtotime($post->fecha_publicacion)); ?></span>
                            <span><i class="far fa-clock mr-2"></i> 5 min lectura</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-brand-700 transition-colors line-clamp-2"><?php echo $post->titulo; ?></h2>
                        <p class="text-gray-500 mb-8 leading-relaxed line-clamp-3"><?php echo $post->resumen; ?></p>
                        <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full mr-3 shadow-md bg-brand-50 flex items-center justify-center text-brand-700 font-bold text-xs">
                                    <?php echo substr($post->autor, 0, 1); ?>
                                </div>
                                <span class="text-sm font-bold text-gray-700"><?php echo $post->autor; ?></span>
                            </div>
                            <a href="blog-detalle.php?slug=<?php echo $post->slug; ?>" class="text-brand-700 font-bold hover:translate-x-1 transition-transform"><i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>

<?php include_once 'includes/footer.php'; ?>
