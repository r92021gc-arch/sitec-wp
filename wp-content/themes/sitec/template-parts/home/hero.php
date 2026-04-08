<?php
$kpis = [];
if ( function_exists('have_rows') && function_exists('get_field') && have_rows('kpis', get_option('page_on_front')) ) {
	while ( have_rows('kpis', get_option('page_on_front')) ) { the_row();
		$label = trim((string) get_sub_field('label'));
		if ($label !== '') { $kpis[] = $label; }
	}
}
if (empty($kpis)) { $kpis = ['60% ahorro', '99.9% uptime', '+500 proyectos']; }

$front_id = (int) get_option('page_on_front');
$hero_title = '';
$hero_text = '';
$cta1_label = '';
$cta1_url = '';
$cta2_label = '';
$cta2_url = '';
if ( function_exists('get_field') ) {
	$hero_title   = trim((string) get_field('hero_title', $front_id));
	$hero_text    = trim((string) get_field('hero_text', $front_id));
	$cta1_label   = trim((string) get_field('hero_cta_primary_label', $front_id));
	$cta1_url     = trim((string) get_field('hero_cta_primary_url', $front_id));
	$cta2_label   = trim((string) get_field('hero_cta_secondary_label', $front_id));
	$cta2_url     = trim((string) get_field('hero_cta_secondary_url', $front_id));
}

$hero_image = null;
if ( function_exists('get_field') ) {
	$img = get_field('hero_image', $front_id);
	if ( is_array($img) && !empty($img['url']) ) { $hero_image = $img; }
}

$overlay_color   = '#0f172a';
$overlay_opacity = 0.90;
if ( function_exists('get_field') ) {
	$color   = trim((string) get_field('hero_overlay_color', $front_id));
	$opacity = get_field('hero_overlay_opacity', $front_id);
	if ($color !== '') { $overlay_color = $color; }
	if (is_numeric($opacity)) { $overlay_opacity = max(0, min(100, (float)$opacity)) / 100.0; }
}

function sitec_hex_to_rgba($hex, $alpha) {
	$hex = str_replace('#', '', $hex);
	if (strlen($hex) === 3) {
		$r = hexdec(str_repeat(substr($hex,0,1),2));
		$g = hexdec(str_repeat(substr($hex,1,1),2));
		$b = hexdec(str_repeat(substr($hex,2,1),2));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$alpha = is_numeric($alpha) ? $alpha : 0.90;
	return 'rgba('.$r.','.$g.','.$b.','.$alpha.')';
}
$overlay_rgba = sitec_hex_to_rgba($overlay_color, $overlay_opacity);

$alignment    = 'left';
$height       = 'normal';
$img_fit      = 'cover';
$img_position = 'center';
if ( function_exists('get_field') ) {
	$al  = trim((string) get_field('hero_alignment', $front_id));
	$ht  = trim((string) get_field('hero_height', $front_id));
	$fit = trim((string) get_field('hero_img_fit', $front_id));
	$pos = trim((string) get_field('hero_img_position', $front_id));
	if ($al  !== '') { $alignment    = $al; }
	if ($ht  !== '') { $height       = $ht; }
	if ($fit !== '') { $img_fit      = $fit; }
	if ($pos !== '') { $img_position = $pos; }
}

$padding_y = 'py-24 md:py-32';
if ($height === 'compact') { $padding_y = 'py-14 md:py-20'; }
elseif ($height === 'wide') { $padding_y = 'py-32 md:py-44'; }

$text_align     = $alignment === 'center' ? 'text-center' : '';
$badge_center   = $alignment === 'center' ? 'justify-center' : '';

$default_cta1_label = 'Consultoría Gratuita';
$default_cta1_url   = (string) ( get_permalink( get_page_by_path('contacto') ) ?: home_url('/contacto') );
$default_cta2_label = 'Ver Proyectos Destacados';
$default_cta2_url   = (string) ( get_post_type_archive_link('case_study') ?: home_url('/cases') );
$cta1_label_out = $cta1_label !== '' ? $cta1_label : $default_cta1_label;
$cta1_url_out   = $cta1_url   !== '' ? $cta1_url   : $default_cta1_url;
$cta2_label_out = $cta2_label !== '' ? $cta2_label : $default_cta2_label;
$cta2_url_out   = $cta2_url   !== '' ? $cta2_url   : $default_cta2_url;
?>

<section class="sitec-hero relative overflow-hidden text-white">
	<!-- Fondo animado -->
	<div class="sitec-hero-bg absolute inset-0"></div>
	<!-- Overlay -->
	<div class="absolute inset-0" style="background: linear-gradient(135deg, <?php echo esc_attr($overlay_rgba); ?> 0%, rgba(0,0,0,0.55) 55%, rgba(16,185,129,0.10) 100%);"></div>

	<div class="relative mx-auto max-w-7xl px-4 <?php echo esc_attr($padding_y); ?> pb-20 md:pb-28">

		<!-- Badge -->
		<div class="mb-8 flex <?php echo esc_attr($badge_center); ?>">
			<span class="sitec-badge inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-4 py-1.5 text-sm font-medium text-emerald-300">
				<span class="sitec-dot h-2 w-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
				+20 años de experiencia comprobada
			</span>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-12 gap-10 items-center">

			<!-- Texto principal -->
			<div class="md:col-span-7 <?php echo esc_attr($text_align); ?>">
				<h1 class="sitec-reveal text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight">
					<?php echo esc_html($hero_title !== '' ? $hero_title : 'Ingeniería que Conecta y Protege a México'); ?>
				</h1>
				<p class="sitec-reveal sitec-reveal-d1 mt-6 text-slate-300 text-lg md:text-xl leading-relaxed max-w-2xl">
					<?php echo esc_html($hero_text !== '' ? $hero_text : 'Más de 20 años transformando infraestructuras críticas con tecnología de vanguardia. Soluciones integrales en seguridad, telecomunicaciones y energía respaldadas por proyectos con Guardia Nacional, SEDENA e INE.'); ?>
				</p>
				<div class="sitec-reveal sitec-reveal-d2 mt-8 flex flex-col sm:flex-row gap-4">
					<a href="<?php echo esc_url($cta1_url_out); ?>" class="sitec-btn-primary inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-6 py-3.5 font-semibold text-white hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-900/30">
						<?php echo esc_html($cta1_label_out); ?>
						<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
					</a>
					<a href="<?php echo esc_url($cta2_url_out); ?>" class="sitec-btn-secondary inline-flex items-center justify-center rounded-xl border border-white/25 bg-white/10 px-6 py-3.5 font-semibold text-white hover:bg-white/20 transition-all backdrop-blur-sm">
						<?php echo esc_html($cta2_label_out); ?>
					</a>
				</div>

				<!-- Franja de logos de socios -->
				<?php
				$si = 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/';
				$hero_partners = [
					['name' => 'AXIS',      'logo' => $si . 'axis.svg'],
					['name' => 'Cisco',     'logo' => $si . 'cisco.svg'],
					['name' => 'Ubiquiti',  'logo' => $si . 'ubiquiti.svg'],
					['name' => 'Hikvision', 'logo' => $si . 'hikvision.svg'],
					['name' => 'Bosch',     'logo' => $si . 'bosch.svg'],
					['name' => 'MikroTik',  'logo' => $si . 'mikrotik.svg'],
				];
				?>
				<div class="sitec-reveal sitec-reveal-d3 mt-10 pt-8 border-t border-white/10 relative" style="z-index:2">
					<p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-4">Tecnología de clase mundial</p>
					<div class="flex flex-wrap items-center gap-4">
						<?php foreach ($hero_partners as $hp): ?>
						<div class="sitec-hero-partner-chip">
							<img
								src="<?php echo esc_url($hp['logo']); ?>"
								alt="<?php echo esc_attr($hp['name']); ?>"
								class="sitec-hero-partner-img"
								onerror="this.style.display='none';this.nextElementSibling.style.display='inline';"
							/><span class="sitec-hero-partner-fallback" style="display:none"><?php echo esc_html($hp['name']); ?></span>
						</div>
						<?php endforeach; ?>
					</div>
				</div>

			</div>

			<!-- KPIs o imagen -->
			<div class="md:col-span-5">
				<?php if ($hero_image): ?>
					<img src="<?php echo esc_url($hero_image['url']); ?>"
					     alt="<?php echo esc_attr($hero_image['alt'] ?? ''); ?>"
					     class="w-full h-56 md:h-80 rounded-2xl shadow-2xl object-<?php echo esc_attr($img_fit); ?> object-<?php echo esc_attr($img_position); ?>" />
				<?php else: ?>
					<div class="grid grid-cols-3 gap-4">
						<?php foreach ($kpis as $kpi): ?>
						<div class="sitec-kpi-card sitec-reveal rounded-2xl border border-white/15 bg-white/10 backdrop-blur-sm p-5 text-center">
							<span class="block text-2xl md:text-3xl font-extrabold text-emerald-300"><?php echo esc_html($kpi); ?></span>
						</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

		</div>
	</div>

	<!-- Ola inferior -->
	<div class="absolute bottom-0 left-0 right-0 leading-none" style="z-index:1;pointer-events:none">
		<svg viewBox="0 0 1440 56" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full h-10 md:h-14">
			<path d="M0 56 C360 0 1080 56 1440 0 L1440 56 Z" fill="white"/>
		</svg>
	</div>
</section>
