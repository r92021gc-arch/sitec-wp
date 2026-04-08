<?php
$items = [];
if ( function_exists('have_rows') && function_exists('get_field') && have_rows('differentiators', get_option('page_on_front')) ) {
	while ( have_rows('differentiators', get_option('page_on_front')) ) { the_row();
		$title    = trim((string) get_sub_field('title'));
		$text     = trim((string) get_sub_field('text'));
		$icon     = get_sub_field('icon');
		$icon_url = is_array($icon) && !empty($icon['url']) ? $icon['url'] : '';
		if ($title !== '' || $text !== '' || $icon_url !== '') {
			$items[] = [ 'title' => $title, 'text' => $text, 'icon' => $icon_url ];
		}
	}
}
if (empty($items)) {
	$items = [
		[
			'title' => 'Expertise en Proyectos Nacionales',
			'text'  => 'Implementaciones con Guardia Nacional, SEDENA e INE. +500 proyectos y 98% satisfacción comprobada.',
			'icon'  => '',
		],
		[
			'title' => 'Soluciones Integrales',
			'text'  => 'Seguridad + telecom + energía con un solo proveedor. Hasta 40% menos costos y 30% menor tiempo.',
			'icon'  => '',
		],
		[
			'title' => 'Tecnología de Clase Mundial',
			'text'  => 'Partner Oficial AXIS. Equipamiento certificado, garantía extendida y sistemas 5G-ready escalables.',
			'icon'  => '',
		],
		[
			'title' => 'Soporte 24/7 y SLA Garantizado',
			'text'  => 'Centro de monitoreo 365 días, atención <4h en sitio y SLA 99.5% uptime garantizado.',
			'icon'  => '',
		],
	];
}

$icons_default = ['🏛️', '🔗', '🌐', '🛡️'];
$accents = ['from-emerald-500 to-teal-600', 'from-blue-500 to-indigo-600', 'from-violet-500 to-purple-600', 'from-orange-500 to-rose-600'];
?>
<section class="py-20 md:py-24 bg-slate-50">
	<div class="mx-auto max-w-7xl px-4">

		<?php
		$heading = 'Por Qué Elegir SITEC';
		$subheading = 'La ventaja competitiva que transforma infraestructuras críticas';
		if ( function_exists('get_field') ) {
			$custom = trim((string) get_field('differentiators_heading', get_option('page_on_front')));
			if ($custom !== '') { $heading = $custom; }
		}
		?>
		<div class="text-center mb-12">
			<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?php echo esc_html($heading); ?></h2>
			<p class="mt-3 text-slate-500 text-lg"><?php echo esc_html($subheading); ?></p>
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
			<?php foreach ($items as $i => $item): ?>
			<div class="sitec-diff-card sitec-reveal group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
				<!-- Número de acento -->
				<span class="sitec-diff-num absolute top-4 right-4 text-5xl font-extrabold text-slate-100 group-hover:text-slate-200 transition-colors leading-none select-none"><?php echo sprintf('%02d', $i + 1); ?></span>
				<!-- Ícono o emoji -->
				<div class="mb-4">
					<?php if (!empty($item['icon'])): ?>
					<img src="<?php echo esc_url($item['icon']); ?>" alt="" class="h-10 w-10 object-contain" />
					<?php else: ?>
					<div class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br <?php echo esc_attr($accents[$i % count($accents)]); ?> text-white text-xl shadow-sm">
						<?php echo $icons_default[$i % count($icons_default)]; ?>
					</div>
					<?php endif; ?>
				</div>
				<h3 class="font-bold text-slate-900 text-base leading-snug"><?php echo esc_html($item['title']); ?></h3>
				<p class="mt-2 text-slate-500 text-sm leading-relaxed"><?php echo esc_html($item['text']); ?></p>
				<!-- Borde inferior de color al hover -->
				<div class="sitec-diff-line absolute bottom-0 left-0 right-0 h-0.5 rounded-b-2xl bg-gradient-to-r <?php echo esc_attr($accents[$i % count($accents)]); ?> scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
			</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
