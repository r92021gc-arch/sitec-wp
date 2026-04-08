<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<header id="sitec-header">

	<!-- ── Barra principal ── -->
	<div class="sitec-header-inner">

		<!-- Logo -->
		<div class="sitec-header-logo">
			<?php if ( function_exists('the_custom_logo') && has_custom_logo() ) {
				the_custom_logo();
			} else { ?>
				<a href="<?php echo esc_url(home_url('/')); ?>" class="sitec-logo-text">
					<?php bloginfo('name'); ?>
				</a>
			<?php } ?>
		</div>

		<!-- Navegación desktop -->
		<nav class="sitec-header-nav" id="sitec-desktop-nav">
			<?php wp_nav_menu([
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'sitec-nav-list',
			]); ?>
		</nav>

		<!-- Acciones derecha -->
		<div class="sitec-header-actions">
			<a class="sitec-phone-link" href="tel:+527471683256">
				<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="currentColor">
					<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
				</svg>
				747-168-3256
			</a>
			<a class="sitec-cta-btn" href="<?php echo esc_url( get_permalink( get_page_by_path('contacto') ) ?: home_url('/#contacto') ); ?>">
				Cotización 
			</a>
			<!-- Hamburger -->
			<button id="sitec-menu-btn" class="sitec-hamburger-btn" aria-label="Abrir menú" aria-expanded="false">
				<span class="sitec-bar"></span>
				<span class="sitec-bar"></span>
				<span class="sitec-bar"></span>
			</button>
		</div>

	</div>

	<!-- ── Barra de socios ── -->
	<div class="sitec-partners-bar">
		<div class="sitec-partners-bar-inner">
			<span class="sitec-partners-label">Socios</span>
			<div class="sitec-partners-divider"></div>
			<?php
			$partner_logos = [];
			if ( post_type_exists('partner') ) {
				$pq = new WP_Query([
					'post_type'      => 'partner',
					'posts_per_page' => 8,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'no_found_rows'  => true,
				]);
				if ( $pq->have_posts() ) {
					while ( $pq->have_posts() ) { $pq->the_post();
						$logo = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
						$name = get_the_title();
						$url  = function_exists('get_field') ? trim((string) get_field('website_url', get_the_ID())) : '';
						$partner_logos[] = [ 'name' => $name, 'logo' => (string) $logo, 'url' => $url ];
					}
					wp_reset_postdata();
				}
			}
			if ( empty($partner_logos) ) {
				$si = 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/';
				$partner_logos = [
					['name' => 'AXIS',      'logo' => $si . 'axis.svg',            'url' => 'https://www.axis.com/',         'color' => '#AEB0AF'],
					['name' => 'Cisco',     'logo' => $si . 'cisco.svg',           'url' => 'https://www.cisco.com/',        'color' => '#1BA0D7'],
					['name' => 'Ubiquiti',  'logo' => $si . 'ubiquiti.svg',        'url' => 'https://www.ui.com/',           'color' => '#0559C9'],
					['name' => 'Hikvision', 'logo' => $si . 'hikvision.svg',       'url' => 'https://www.hikvision.com/',    'color' => '#E31E24'],
					['name' => 'Panduit',   'logo' => '',                           'url' => 'https://www.panduit.com/',      'color' => '#E47920'],
					['name' => 'MikroTik',  'logo' => $si . 'mikrotik.svg',        'url' => 'https://mikrotik.com/',         'color' => '#419BD2'],
					['name' => 'Bosch',     'logo' => $si . 'bosch.svg',           'url' => 'https://www.boschsecurity.com/','color' => '#E20015'],
					['name' => 'Dahua',     'logo' => $si . 'dahua.svg',           'url' => 'https://www.dahuasecurity.com/','color' => '#0067B1'],
				];
			}
			foreach ( $partner_logos as $p ) :
				$tag   = !empty($p['url']) ? 'a' : 'span';
				$attrs = !empty($p['url']) ? ' href="' . esc_url($p['url']) . '" target="_blank" rel="noopener noreferrer"' : '';
				$color = !empty($p['color']) ? $p['color'] : '#64748b';
			?>
			<<?php echo $tag; ?><?php echo $attrs; ?> class="sitec-partner-item">
				<?php if ( !empty($p['logo']) ): ?>
					<img
						src="<?php echo esc_url($p['logo']); ?>"
						alt="<?php echo esc_attr($p['name']); ?>"
						style="height:16px;width:auto;object-fit:contain;filter:invert(0);"
						onerror="this.style.display='none';this.nextElementSibling.style.display='inline';"
					/><span style="display:none;color:<?php echo esc_attr($color); ?>"><?php echo esc_html($p['name']); ?></span>
				<?php else: ?>
					<span style="color:<?php echo esc_attr($color); ?>"><?php echo esc_html($p['name']); ?></span>
				<?php endif; ?>
			</<?php echo $tag; ?>>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- ── Menú móvil ── -->
	<div id="sitec-mobile-menu" class="sitec-mobile-menu hidden">
		<?php wp_nav_menu([
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'sitec-mobile-nav',
			'menu_id'        => 'mobile-nav',
		]); ?>
		<div class="sitec-mobile-footer">
			<a href="tel:+525512345678" class="sitec-mobile-phone">
				<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="currentColor">
					<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
				</svg>
				(55) 1234-5678
			</a>
			<a href="<?php echo esc_url( get_permalink( get_page_by_path('contacto') ) ?: home_url('/#contacto') ); ?>" class="sitec-cta-btn w-full text-center">
				Diagnóstico Gratis
			</a>
		</div>
	</div>

</header>
