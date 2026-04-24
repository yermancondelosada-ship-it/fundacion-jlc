<?php 
$page_title = "Mensajes de Contacto";
include_once 'includes/admin_header.php'; 

// Fetch messages (mocking since there's no table for messages in setup.sql, I should create it if needed or just show a message)
// I'll create the table if it doesn't exist to be functional.
try {
    $db->query("CREATE TABLE IF NOT EXISTS mensajes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150),
        email VARCHAR(100),
        asunto VARCHAR(150),
        mensaje TEXT,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        estado ENUM('leido', 'pendiente') DEFAULT 'pendiente'
    )");
} catch (Exception $e) {}

$mensajes = $db->query("SELECT * FROM mensajes ORDER BY fecha DESC")->fetchAll();
?>

<div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
    <div class="p-8 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-900">Bandeja de Entrada</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($mensajes as $msg): ?>
                <tr class="hover:bg-gray-50 transition-colors cursor-pointer <?php echo $msg->estado == 'pendiente' ? 'bg-brand-50/30' : ''; ?>">
                    <td class="px-8 py-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-2 h-2 rounded-full <?php echo $msg->estado == 'pendiente' ? 'bg-brand-500 shadow-[0_0_10px_#22c55e]' : 'bg-gray-200'; ?>"></div>
                            <div>
                                <p class="font-bold text-gray-900"><?php echo $msg->nombre; ?></p>
                                <p class="text-xs text-gray-400"><?php echo $msg->email; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-gray-800 text-sm mb-1"><?php echo $msg->asunto; ?></p>
                        <p class="text-xs text-gray-500 line-clamp-1"><?php echo $msg->mensaje; ?></p>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <p class="text-xs text-gray-400"><?php echo date('H:i d/m/y', strtotime($msg->fecha)); ?></p>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($mensajes)): ?>
                <tr>
                    <td class="px-8 py-20 text-center text-gray-400 italic">No hay mensajes nuevos en la bandeja de entrada.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once 'includes/admin_footer.php'; ?>
