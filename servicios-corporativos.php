<?php include_once 'includes/header.php'; ?>

<?php 
// Cargar servicios desde la base de datos
$servicios_db = $db->query("SELECT * FROM servicios ORDER BY id ASC")->fetchAll();
?>

<!-- Hero Section -->
<section class="bg-brand-800 py-20 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-4"><?php echo getSiteConfig('servicios_hero_titulo', 'Servicios Corporativos'); ?></h1>
        <p class="text-xl text-green-100 max-w-2xl mx-auto"><?php echo getSiteConfig('servicios_hero_subtitulo', 'Soluciones integrales para organizaciones que buscan innovar y generar impacto positivo.'); ?></p>
    </div>
</section>

<!-- Services Section -->
<section class="py-24">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            
            <?php if(empty($servicios_db)): ?>
                <div class="col-span-full text-center py-20 bg-white rounded-[3rem] shadow-xl">
                    <i class="fas fa-briefcase text-gray-200 text-7xl mb-6"></i>
                    <h3 class="text-2xl font-bold text-gray-400">Próximamente más servicios.</h3>
                </div>
            <?php else: ?>
                <?php foreach($servicios_db as $srv): ?>
                <div class="bg-white rounded-[3rem] p-12 shadow-2xl border border-gray-100 hover:scale-[1.02] transition-transform group flex flex-col h-full">
                    <div class="w-20 h-20 bg-brand-100 text-brand-700 rounded-3xl flex items-center justify-center text-4xl mb-8 overflow-hidden p-4">
                        <?php if($srv->icono): ?>
                            <img src="uploads/img/<?php echo $srv->icono; ?>" class="w-full h-full object-contain">
                        <?php else: ?>
                            <i class="fas fa-concierge-bell"></i>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-6"><?php echo $srv->titulo; ?></h3>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed flex-grow">
                        <?php echo $srv->descripcion; ?>
                    </p>
                    <button onclick="openModal('<?php echo addslashes($srv->titulo); ?>')" class="bg-brand-700 text-white px-8 py-4 rounded-2xl font-bold hover:bg-brand-800 transition-colors shadow-lg w-full">
                        Saber más
                    </button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Contact Modal -->
<div id="serviceModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full p-10 relative">
        <button onclick="closeModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 text-2xl">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="text-2xl font-bold text-gray-900 mb-2" id="modalTitle">Solicitar Información</h3>
        <p class="text-gray-500 mb-8">Déjanos tus datos y un experto se pondrá en contacto contigo.</p>
        
        <form action="contacto.php" method="POST" class="space-y-4">
            <input type="hidden" name="servicio" id="modalInputServicio">
            <input type="text" placeholder="Nombre de la empresa" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            <input type="email" placeholder="Correo corporativo" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            <textarea placeholder="¿En qué podemos ayudarte?" rows="4" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"></textarea>
            <button type="submit" class="w-full bg-brand-700 text-white font-bold py-4 rounded-2xl hover:bg-brand-800 transition-all shadow-xl">
                Enviar Solicitud
            </button>
        </form>
    </div>
</div>

<script>
    function openModal(service) {
        document.getElementById('modalTitle').innerText = 'Información: ' + service;
        document.getElementById('modalInputServicio').value = service;
        document.getElementById('serviceModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('serviceModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

<?php include_once 'includes/footer.php'; ?>
