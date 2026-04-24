<?php 
include_once 'includes/header.php'; 

$slug = $_GET['slug'] ?? '';
$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND estado = 'publicado'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    echo "<script>window.location.href='blog.php';</script>";
    exit;
}
?>

<article class="py-24 bg-white">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-400 mb-8 space-x-2">
            <a href="index.php" class="hover:text-brand-700">Inicio</a>
            <span>/</span>
            <a href="blog.php" class="hover:text-brand-700">Blog</a>
            <span>/</span>
            <span class="text-gray-900 font-bold"><?php echo $post->titulo; ?></span>
        </nav>

        <header class="mb-12">
            <span class="bg-brand-50 text-brand-700 text-xs font-black uppercase tracking-widest px-4 py-2 rounded-lg mb-6 inline-block">
                <?php echo $post->categoria; ?>
            </span>
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 leading-tight mb-8">
                <?php echo $post->titulo; ?>
            </h1>
            <div class="flex items-center space-x-6 border-y border-gray-100 py-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-brand-700 text-white flex items-center justify-center font-bold mr-4">
                        <?php echo substr($post->autor, 0, 1); ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900"><?php echo $post->autor; ?></p>
                        <p class="text-xs text-gray-400">Autor del artículo</p>
                    </div>
                </div>
                <div class="h-8 w-px bg-gray-100"></div>
                <div class="text-xs text-gray-400">
                    <p class="font-bold text-gray-900 uppercase tracking-widest mb-1">Publicado</p>
                    <p><?php echo date('d M, Y', strtotime($post->fecha_publicacion)); ?></p>
                </div>
            </div>
        </header>

        <?php if($post->imagen_destacada): ?>
            <img src="uploads/img/<?php echo $post->imagen_destacada; ?>" class="w-full h-auto rounded-[3rem] shadow-2xl mb-16" alt="Post Hero">
        <?php endif; ?>

        <div class="prose prose-lg prose-brand max-w-none text-gray-600 leading-relaxed">
            <?php echo nl2br($post->contenido); ?>
        </div>

        <!-- Social Share Placeholder -->
        <div class="mt-16 pt-8 border-t border-gray-100 flex justify-between items-center">
            <p class="font-bold text-gray-900">¿Te gustó este artículo? Compártelo:</p>
            <div class="flex space-x-4">
                <a href="#" class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="w-10 h-10 bg-green-50 text-green-600 rounded-full flex items-center justify-center hover:bg-green-600 hover:text-white transition-all"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>
</article>

<!-- Newsletter / CTA -->
<section class="py-24 bg-brand-50">
    <div class="container mx-auto px-4 text-center max-w-2xl">
        <h3 class="text-3xl font-bold text-gray-900 mb-6">Suscríbete a nuestro boletín</h3>
        <p class="text-gray-600 mb-8">Recibe los mejores artículos y noticias de la Fundación JLC directamente en tu correo.</p>
        <form class="flex gap-4">
            <input type="email" placeholder="Tu correo electrónico" class="flex-grow px-8 py-4 rounded-2xl border-none shadow-lg focus:ring-2 focus:ring-brand-500">
            <button class="bg-brand-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl hover:bg-brand-800 transition-all">Unirse</button>
        </form>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
