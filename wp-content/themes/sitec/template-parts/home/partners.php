<?php
$front_id = (int) get_option('page_on_front');
$partners = [];

// 1) CPT 'partner'
if ( post_type_exists('partner') ) {
	$count = 12; $orderby = 'menu_order'; $order = 'ASC';
	if ( function_exists('get_field') ) {
		$c = (int) get_field('count_partners', $front_id); if ($c > 0) { $count = $c; }
		$ob = trim((string) get_field('partners_orderby', $front_id)); if ($ob !== '') { $orderby = $ob; }
		$od = trim((string) get_field('partners_order', $front_id)); if (in_array($od, ['ASC','DESC'], true)) { $order = $od; }
	}
	$q = new WP_Query([
		'post_type'          => 'partner',
		'posts_per_page'     => $count,
		'orderby'            => $orderby,
		'order'              => $order,
		'no_found_rows'      => true,
		'ignore_sticky_posts'=> true,
	]);
	if ($q->have_posts()) {
		while ($q->have_posts()) { $q->the_post();
			$logo = get_the_post_thumbnail_url(get_the_ID(), 'medium');
			$name = get_the_title();
			$url  = function_exists('get_field') ? trim((string) get_field('website_url', get_the_ID())) : '';
			if ($logo || $name) { $partners[] = ['name' => $name, 'url' => $url, 'logo' => (string) $logo, 'color' => '']; }
		}
		wp_reset_postdata();
	}
}

// 2) ACF lista manual
if ( empty($partners) && function_exists('have_rows') && function_exists('get_field') && $front_id && have_rows('partners', $front_id) ) {
	while ( have_rows('partners', $front_id) ) { the_row();
		$name     = trim((string) get_sub_field('name'));
		$url      = trim((string) get_sub_field('url'));
		$logo     = get_sub_field('logo');
		$logo_url = is_array($logo) && !empty($logo['url']) ? $logo['url'] : '';
		if ($logo_url !== '' || $name !== '') {
			$partners[] = ['name' => $name, 'url' => $url, 'logo' => $logo_url, 'color' => ''];
		}
	}
}

// 3) Fallback con URLs confiables (jsDelivr simple-icons CDN + colores de marca)
if (empty($partners)) {
	$si = 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/';
	$partners = [
		['name' => 'AXIS Communications', 'url' => 'https://www.axis.com/',          'logo' => $si . 'axis.svg',            'color' => '#AEB0AF'],
		['name' => 'Cisco',               'url' => 'https://www.cisco.com/',          'logo' => $si . 'cisco.svg',           'color' => '#1BA0D7'],
		['name' => 'Ubiquiti',            'url' => 'https://www.ui.com/',             'logo' => $si . 'ubiquiti.svg',        'color' => '#0559C9'],
		['name' => 'Hikvision',           'url' => 'https://www.hikvision.com/',      'logo' => $si . 'hikvision.svg',       'color' => '#E31E24'],
		['name' => 'Bosch',               'url' => 'https://www.boschsecurity.com/',  'logo' => $si . 'bosch.svg',           'color' => '#E20015'],
		['name' => 'MikroTik',            'url' => 'https://mikrotik.com/',           'logo' => $si . 'mikrotik.svg',        'color' => '#419BD2'],
		['name' => 'Panduit',             'url' => 'https://www.panduit.com/',        'logo' => '',                          'color' => '#E47920'],
		['name' => 'Dahua',               'url' => 'https://www.dahuasecurity.com/',  'logo' => $si . 'dahua.svg',           'color' => '#0067B1'],
		['name' => 'APC / Schneider',     'url' => 'https://www.apc.com/',            'logo' => $si . 'schneiderelectric.svg','color' => '#3DCD58'],
		['name' => 'Eaton',               'url' => 'https://www.eaton.com/',          'logo' => $si . 'eaton.svg',           'color' => '#FFB81C'],
		['name' => 'Fluke Networks',      'url' => 'https://www.flukenetworks.com/',  'logo' => '',                          'color' => '#E31837'],
		['name' => 'UNV / Uniview',       'url' => '',                                'logo' => '',                          'color' => '#005BAA'],
		['name' => 'Hytera',              'url' => '',                                'logo' => '',                          'color' => '#003087'],
		['name' => 'EPCOM',               'url' => '',                                'logo' => '',                          'color' => '#FF6600'],
	];
}
?>
<?php if (!empty($partners)): ?>
<section class="py-10 md:py-14 bg-slate-50">
	<div class="mx-auto max-w-7xl px-4">

		<?php
		$heading = 'Marcas y Socios Tecnológicos';
		if ( function_exists('get_field') ) {
			$custom = trim((string) get_field('partners_heading', $front_id));
			if ($custom !== '') { $heading = $custom; }
		}
		?>
		<p class="text-center text-xs font-bold uppercase tracking-widest text-slate-400 mb-8">
			<?php echo esc_html($heading); ?>
		</p>

		<!-- Wrapper flex: [← botón] [logos] [→ botón] -->
		<div class="sitec-partners-slider flex items-center gap-3" data-autoplay="true" data-interval="3000">

			<!-- Flecha izquierda — fuera del área de logos -->
			<button class="sitec-partners-prev flex-shrink-0 p-2 rounded-full bg-white shadow border border-slate-200 hidden sm:inline-flex hover:bg-slate-50 transition-colors" aria-label="Anterior">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L8.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
			</button>

			<!-- Área de logos con overflow oculto -->
			<div class="overflow-hidden flex-1">
				<div class="sitec-partners-track flex gap-6 items-center snap-x snap-mandatory scroll-smooth overflow-x-auto no-scrollbar py-2" tabindex="0" role="list">
					<?php foreach ($partners as $p):
						$has_logo   = !empty($p['logo']);
						$has_url    = !empty($p['url']);
						$color      = !empty($p['color']) ? $p['color'] : '#64748b';
						$wrap_tag   = $has_url ? 'a' : 'div';
						$wrap_attrs = $has_url ? ' href="' . esc_url($p['url']) . '" target="_blank" rel="noopener noreferrer"' : '';
					?>
					<div class="sitec-partners-slide flex-none w-1/3 sm:w-1/4 md:w-1/6 snap-center" role="listitem">
						<<?php echo $wrap_tag; ?><?php echo $wrap_attrs; ?> class="sitec-partner-card">
							<?php if ($has_logo): ?>
								<img
									src="<?php echo esc_url($p['logo']); ?>"
									alt="<?php echo esc_attr($p['name']); ?>"
									class="sitec-partner-img"
									style="--partner-color:<?php echo esc_attr($color); ?>"
									onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
								/>
								<span class="sitec-partner-text" style="display:none;color:<?php echo esc_attr($color); ?>">
									<?php echo esc_html($p['name']); ?>
								</span>
							<?php else: ?>
								<span class="sitec-partner-text" style="color:<?php echo esc_attr($color); ?>">
									<?php echo esc_html($p['name']); ?>
								</span>
							<?php endif; ?>
						</<?php echo $wrap_tag; ?>>
					</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Flecha derecha — fuera del área de logos -->
			<button class="sitec-partners-next flex-shrink-0 p-2 rounded-full bg-white shadow border border-slate-200 hidden sm:inline-flex hover:bg-slate-50 transition-colors" aria-label="Siguiente">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L13.707 9.293a1 1 0 010 1.414L8.707 15.707a1 1 0 01-1.414-1.414L11.586 10 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
			</button>

		</div>

	</div>
</section>
<?php endif; ?>
