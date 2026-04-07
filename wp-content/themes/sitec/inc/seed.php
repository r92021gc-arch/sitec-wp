<?php
// Endpoint protegido por nonce para crear contenido demo
add_action('admin_init', function(){
	if (!current_user_can('manage_options')) return;
	if (!isset($_GET['sitec_seed'])) return;
	check_admin_referer('sitec_seed_action');

	// Helper: actualizar o crear por título
	$upsert = function($post_type, $title, $content = '', $excerpt = '', $slug = ''){
		$exists = get_page_by_title($title, OBJECT, $post_type);
		$args = [
			'post_title'   => $title,
			'post_type'    => $post_type,
			'post_status'  => 'publish',
			'post_content' => $content,
			'post_excerpt' => $excerpt ?: wp_trim_words(wp_strip_all_tags($content), 40, '…')
		];
		if (!empty($slug)) { $args['post_name'] = sanitize_title($slug); }
		if ($exists && !is_wp_error($exists)) {
			$args['ID'] = $exists->ID;
			return wp_update_post($args);
		}
		return wp_insert_post($args);
	};

	// Crear páginas base si no existen
	$pages = [
		['title' => 'Inicio', 'template' => 'front-page.php', 'option' => 'page_on_front'],
		['title' => 'Blog', 'slug' => 'blog', 'option' => 'page_for_posts'],
		['title' => 'Aviso de Privacidad', 'slug' => 'privacy-policy', 'content' => 'Esta es la página de aviso de privacidad. Actualice el contenido conforme a sus políticas.'],
		['title' => 'Términos de Servicio', 'slug' => 'terms', 'content' => 'Esta es la página de términos de servicio. Actualice el contenido con sus condiciones.'],
		['title' => 'Nosotros', 'template' => 'page-about.php', 'content' =>
			"<h2>Más de 20 Años Construyendo el México Tecnológico del Futuro</h2>".
			"<p>SITEC S.A. de C.V. es una empresa mexicana líder en soluciones integrales de ingeniería, seguridad electrónica y telecomunicaciones. Desde 2003, hemos sido el socio tecnológico de confianza para instituciones gubernamentales y empresas privadas que requieren infraestructuras robustas y de clase mundial.</p>".
			"<h3>Misión</h3><p>Transformar desafíos tecnológicos en ventajas competitivas sostenibles mediante innovación, servicio excepcional y excelencia operativa.</p>".
			"<h3>Visión</h3><p>Ser el integrador tecnológico más confiable de México, reconocido por proyectos que establecen nuevos estándares de calidad, seguridad e innovación.</p>".
			"<h3>Valores</h3><ul><li>Excelencia Técnica</li><li>Integridad</li><li>Innovación</li><li>Compromiso</li><li>Responsabilidad Social</li></ul>".
			"<h3>Hitos</h3><ul>".
			"<li><strong>2003</strong>: Fundación en Chilpancingo, primeros proyectos eléctricos.</li>".
			"<li><strong>2008</strong>: Expansión a telecomunicaciones, certificación ISO 9001.</li>".
			"<li><strong>2012</strong>: Primeros proyectos gubernamentales; oficina CDMX.</li>".
			"<li><strong>2015</strong>: AXIS Solution Partner; proyectos a gran escala.</li>".
			"<li><strong>2018</strong>: Contrato SEDENA; centro de soporte 24/7.</li>".
			"<li><strong>2020</strong>: Soluciones con IA y data centers modulares.</li>".
			"<li><strong>2022–2023</strong>: Guardia Nacional (4 estados), INE (infraestructura electoral), AXIS Gold Partner.</li>".
			"<li><strong>2024–2025</strong>: 500+ proyectos, energía solar, IoT y ciudades inteligentes.</li></ul>".
			"<h3>Certificaciones y Membresías</h3><ul>".
			"<li>ISO 9001:2015, ISO 27001:2013</li><li>AXIS Solution Partner Gold, Cisco Registered</li><li>Panduit Certified Installer, Ubiquiti Elite</li><li>Fluke Networks CCTT, FIDE Instalador Certificado</li><li>CANIETI, CANITEC, Clúster de Seguridad EDOMEX</li></ul>".
			"<h3>Presencia Nacional</h3><p>15+ estados: Guerrero, CDMX, EDOMEX, Hidalgo, Jalisco, Zacatecas, Morelos, Michoacán, Puebla, Oaxaca, Veracruz, Querétaro, Guanajuato, Nuevo León, Baja California.</p>".
			"<h3>Métricas</h3><ul><li>500+ proyectos</li><li>100+ clientes activos</li><li>100+ colaboradores</li><li>98% satisfacción</li><li>99.5% SLA en mantenimiento</li><li>$450M+ MXN (2020–2024)</li></ul>"],
		['title' => 'Contacto', 'template' => 'page-contact.php', 'content' => "Conéctese con Nuestros Expertos: Su Proyecto Comienza Aquí\n\nRespuesta garantizada en menos de 2 horas hábiles. Consultoría inicial sin costo."],
	];
	foreach ($pages as $p) {
		$exists = get_page_by_title($p['title']);
		if (!$exists) {
			$id = wp_insert_post([
				'post_title' => $p['title'], 'post_type' => 'page', 'post_status' => 'publish', 'post_content' => ($p['content'] ?? ''),
				'post_name'  => !empty($p['slug']) ? sanitize_title($p['slug']) : sanitize_title($p['title'])
			]);
			if (!is_wp_error($id)) {
				if (!empty($p['template'])) update_post_meta($id, '_wp_page_template', $p['template']);
				if (!empty($p['option']) && $p['option'] === 'page_on_front') { update_option('page_on_front', $id); update_option('show_on_front', 'page'); }
				if (!empty($p['option']) && $p['option'] === 'page_for_posts') { update_option('page_for_posts', $id); }
			}
		} else {
			if (!empty($p['content'])) {
				wp_update_post(['ID' => $exists->ID, 'post_content' => $p['content']]);
			}
			if (!empty($p['template'])) update_post_meta($exists->ID, '_wp_page_template', $p['template']);
			if (!empty($p['slug'])) { wp_update_post(['ID' => $exists->ID, 'post_name' => sanitize_title($p['slug'])]); }
			if (!empty($p['option']) && $p['option'] === 'page_on_front') { update_option('page_on_front', $exists->ID); update_option('show_on_front', 'page'); }
			if (!empty($p['option']) && $p['option'] === 'page_for_posts') { update_option('page_for_posts', $exists->ID); }
		}
	}

	// Sembrar bloques en "Inicio" si no tiene contenido (para edición visual inmediata)
	$front_id_seed = (int) get_option('page_on_front');
	if ($front_id_seed) {
		$current = (string) get_post_field('post_content', $front_id_seed);
		if (trim(wp_strip_all_tags($current)) === '') {
			$blocks = '<!-- wp:cover {"dimRatio":40,"minHeight":420,"minHeightUnit":"px"} -->\n'
				. '<div class="wp-block-cover__inner-container">'
				. '<!-- wp:heading {"level":1} --><h1 class="wp-block-heading">Portada editable</h1><!-- /wp:heading -->'
				. '<!-- wp:paragraph --><p>Edita estos bloques para ver los cambios inmediatos en la página de inicio.</p><!-- /wp:paragraph -->'
				. '<!-- wp:buttons --><div class="wp-block-buttons">'
				. '<!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link" href="' . esc_url( home_url('/contacto') ) . '">Contactar</a></div><!-- /wp:button -->'
				. '<!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link" href="#servicios">Ver servicios</a></div><!-- /wp:button -->'
				. '</div><!-- /wp:buttons -->'
				. '</div>'
				. '<!-- /wp:cover -->';
			wp_update_post(['ID' => $front_id_seed, 'post_content' => $blocks]);
		}
	}

	// Servicios con contenido extenso y títulos optimizados
	$service_entries = [
		[
			'old' => 'Seguridad Avanzada',
			'title' => 'Seguridad Inteligente con IA: Protección Proactiva 24/7',
			'content' =>
				"<p>Transformamos la seguridad reactiva en inteligencia predictiva. Sistemas basados en IA que analizan comportamientos, detectan anomalías y alertan antes de incidentes críticos. Tecnología AXIS + plataformas VMS de última generación.</p>".
				"<h3>Soluciones Detalladas</h3>".
				"<h4>1) Centros de Comando C4</h4><ul><li>Videowall 4K con múltiples fuentes</li><li>VMS profesional integrado</li><li>Analítica de video en tiempo real</li><li>Integración con sistemas de emergencia</li><li><strong>Desde:</strong> $850,000 MXN — <strong>Tiempo:</strong> 4–8 semanas</li></ul>".
				"<h4>2) Videovigilancia Inteligente</h4><ul><li>Cámaras AXIS con visión térmica (-40°C)</li><li>LPR 98% precisión</li><li>Intrusión perimetral</li><li>Almacenamiento híbrido (90 días)</li><li><strong>Desde:</strong> $3,500 MXN por cámara — <strong>Tiempo:</strong> 1–3 semanas</li></ul>".
				"<h4>3) Control de Acceso Biométrico</h4><ul><li>Facial touchless y huella ultrasónica</li><li>Integración con RH/nómina</li><li>Reportes asistencia</li><li><strong>Desde:</strong> $18,000 MXN por punto — <strong>Tiempo:</strong> 3–7 días</li></ul>".
				"<h4>4) Protección Perimetral</h4><ul><li>Sensores doble tecnología</li><li>Cerca eléctrica monitorizada</li><li>Integración con iluminación/sirenas</li><li><strong>Desde:</strong> $450 MXN por metro — <strong>Tiempo:</strong> 2–4 semanas</li></ul>".
				"<h4>5) Drones de Vigilancia IoT</h4><ul><li>Rutas programables</li><li>Cámara 4K nocturna</li><li>Detección térmica</li><li><strong>Desde:</strong> $180,000 MXN — <strong>Capacitación:</strong> 5 días</li></ul>".
				"<h4>6) Automatización de Edificios</h4><ul><li>Iluminación, HVAC, persianas</li><li>Sensores de ocupación</li><li>Integración Alexa/Google</li><li><strong>Desde:</strong> $85,000 MXN por piso — <strong>Tiempo:</strong> 2–3 semanas</li></ul>".
				"<h3>Certificaciones y Cumplimiento</h3><ul><li>ISO 9001:2015</li><li>AXIS Solution Partner Gold</li><li>NOM-001-SEDE</li></ul>",
			'cta' => ['Solicite su Cotización Personalizada', '/contacto']
		],
		[
			'old' => 'Telecomunicaciones y Redes',
			'title' => 'Conectividad de Nueva Generación: Infraestructura 5G-Ready',
			'content' =>
				"<p>Diseñamos e implementamos infraestructuras de red que soportan la transformación digital: del cableado certificado a enlaces inalámbricos de alta capacidad.</p>".
				"<h3>Soluciones Detalladas</h3>".
				"<h4>1) Cableado Estructurado</h4><ul><li>Cat 6A/7 hasta 10Gbps</li><li>Fibra MM/SM</li><li>Certificación Fluke</li><li>Garantía 25 años</li><li><strong>Desde:</strong> $280 MXN por punto</li></ul>".
				"<h4>2) Radiocomunicación Profesional</h4><ul><li>DMR/P25 analógico/digital</li><li>Encriptación</li><li><strong>Desde:</strong> $8,500 MXN por radio — <strong>Red:</strong> $450,000 MXN</li></ul>".
				"<h4>3) Redes WiFi Empresariales</h4><ul><li>APs Ubiquiti/Cisco alta densidad</li><li>Indoor/outdoor</li><li>Gestión en nube</li><li>Portal cautivo</li><li><strong>Desde:</strong> $12,000 MXN por AP — <strong>Tiempo:</strong> 1–2 semanas</li></ul>".
				"<h4>4) Enlaces Inalámbricos</h4><ul><li>PtP hasta 100km / PtMP</li><li>Mesh</li><li>Hasta 1.7Gbps</li><li><strong>Desde:</strong> $45,000 MXN — <strong>Tiempo:</strong> 3–5 días</li></ul>".
				"<h4>5) Rastreo de Flotillas</h4><ul><li>Monitoreo 24/7</li><li>Geocercas</li><li>Alertas</li><li><strong>Desde:</strong> $3,200 MXN + $450/mes</li></ul>".
				"<h4>6) Data Centers Modulares</h4><ul><li>Racks con PDU</li><li>Climatización precisión</li><li>UPS autonomía</li><li>Detección/supresión incendios</li><li><strong>Desde:</strong> $380,000 MXN (10 racks) — <strong>Tiempo:</strong> 4–6 semanas</li></ul>".
				"<h3>Certificaciones</h3><ul><li>Fluke Networks CCTT</li><li>Ubiquiti Elite</li><li>Panduit Certified</li></ul>",
			'cta' => ['Optimice su Red Hoy', '/contacto']
		],
		[
			'old' => 'Energía Sostenible',
			'title' => 'Energía Inteligente: Eficiencia que Reduce Costos Hasta 60%',
			'content' =>
				"<p>Proyectos de electrificación bajo NOM vigentes con enfoque en sostenibilidad y ahorro. Desde instalaciones industriales hasta sistemas solares con ROI garantizado.</p>".
				"<h3>Soluciones Detalladas</h3>".
				"<h4>1) Instalaciones Eléctricas Certificadas</h4><ul><li>NOM-001-SEDE-2012</li><li>Acometidas MT/BT</li><li>Tableros con protecciones</li><li>Tierra física certificada</li><li><strong>Desde:</strong> $850 MXN por salida</li></ul>".
				"<h4>2) Iluminación LED Inteligente</h4><ul><li>Retrofit a LED</li><li>Sensores y fotoceldas</li><li>Control DMX</li><li>Integración IoT</li><li><strong>Ahorro:</strong> hasta 75% — <strong>ROI:</strong> 18–24 meses — <strong>Desde:</strong> $1,800 por luminaria</li></ul>".
				"<h4>3) Iluminación Arquitectónica</h4><ul><li>RGB dinámico</li><li>Jardines/fuentes</li><li>Control DMX</li><li><strong>Desde:</strong> $650 por metro — <strong>Diseño:</strong> 1–2 semanas</li></ul>".
				"<h4>4) Sistemas Solares Fotovoltaicos</h4><ul><li>Paneles 400W+</li><li>Inversores híbridos</li><li>Estructuras certificadas</li><li>Monitoreo tiempo real</li><li><strong>Desde:</strong> $18,500 MXN/kWp — <strong>ROI:</strong> 4–6 años — <strong>Ahorro:</strong> 90–95%</li><li><strong>Garantías:</strong> 25 años paneles / 10 años inversores</li></ul>".
				"<h3>Beneficios Fiscales</h3><ul><li>Deducción inmediata 100% (solar)</li><li>Depreciación acelerada</li></ul>",
			'cta' => ['Calcule su Ahorro Solar', '/contacto']
		],
		[
			'old' => 'Consultoría Estratégica',
			'title' => 'Asesoría Estratégica: Su Socio en Transformación Tecnológica',
			'content' =>
				"<p>Planificamos, diseñamos y supervisamos proyectos tecnológicos complejos para maximizar retorno operativo y estratégico.</p>".
				"<h3>Servicios</h3>".
				"<h4>1) Auditoría y Diagnóstico</h4><ul><li>Evaluación de infraestructura</li><li>Vulnerabilidades</li><li>Roadmap priorizado</li><li><strong>Inversión:</strong> $35,000 MXN — <strong>Entregable:</strong> reporte ejecutivo</li></ul>".
				"<h4>2) Proyectos Llave en Mano</h4><ul><li>Ingeniería de detalle</li><li>Especificaciones</li><li>Presupuesto</li><li><strong>Inversión:</strong> 8–12% valor del proyecto — <strong>Tiempo:</strong> 2–4 semanas</li></ul>".
				"<h4>3) Gestión de Proyectos (PMO)</h4><ul><li>PM dedicado</li><li>Seguimiento semanal</li><li>Control de costos/tiempos</li><li>Gestión de cambios — <strong>Inversión:</strong> 10–15% valor</li></ul>".
				"<h4>4) Certificaciones y Cumplimiento</h4><ul><li>CFE, dictámenes, RETIE (si aplica)</li><li>Manuales O&M</li><li><strong>Desde:</strong> $25,000 MXN</li></ul>".
				"<h4>5) Capacitación Técnica</h4><ul><li>Operación y mantenimiento</li><li>Respuesta a incidentes</li><li>Certificaciones (AXIS, Cisco)</li><li><strong>Desde:</strong> $15,000 MXN (10 personas) — <strong>2–5 días</strong></li></ul>",
			'cta' => ['Programe su Consulta Gratuita de 45 Minutos', '/contacto']
		],
	];

	// Upsert servicios: intenta actualizar por título antiguo; si no existe, crea por el nuevo
	foreach ($service_entries as $svc) {
		$exists_old = get_page_by_title($svc['old'], OBJECT, 'service');
		$exists_new = get_page_by_title($svc['title'], OBJECT, 'service');
		if ($exists_old && !is_wp_error($exists_old)) {
			$id = wp_update_post(['ID'=>$exists_old->ID,'post_title'=>$svc['title'],'post_content'=>$svc['content'],'post_excerpt'=>wp_trim_words(wp_strip_all_tags($svc['content']), 40, '…')]);
		} elseif ($exists_new && !is_wp_error($exists_new)) {
			$id = wp_update_post(['ID'=>$exists_new->ID,'post_content'=>$svc['content'],'post_excerpt'=>wp_trim_words(wp_strip_all_tags($svc['content']), 40, '…')]);
		} else {
			$id = $upsert('service', $svc['title'], $svc['content']);
		}
		if (function_exists('update_field') && $id && !is_wp_error($id)) {
			@update_field('cta_label', $svc['cta'][0], $id);
			@update_field('cta_url', home_url($svc['cta'][1]), $id);
		}
	}

	// Casos de éxito reales
	$case_entries = [
		[
			'title' => 'UAGro — Sistema CCTV Campus Universitario',
			'aliases' => ['UAGro','Universidad Autónoma de Guerrero'],
			'content' =>
				"<h2>Instalación Sistema CCTV en Instalaciones Universitarias</h2>".
				"<p><strong>Cliente:</strong> Universidad Autónoma de Guerrero (UAGro) — <strong>Sector:</strong> Educación Pública — <strong>Período:</strong> Nov 2015 / May 2016</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Suministro e instalación de cámaras de seguridad en campus universitario</li>".
				"<li>Cableado estructurado Cat 6 para red de videovigilancia</li>".
				"<li>Instalación de grabadoras de video en red (NVR)</li>".
				"<li>Configuración y puesta en marcha del sistema CCTV</li>".
				"<li>Instalación de monitores de supervisión</li>".
				"<li>Capacitación al personal operativo</li>".
				"</ul>",
		],
		[
			'title' => 'Extracciones y Triturados Mineros — CCTV Operaciones',
			'aliases' => ['Extracciones y Triturados Mineros'],
			'content' =>
				"<h2>Instalación de Cámaras IP para Supervisión de Operaciones Mineras</h2>".
				"<p><strong>Cliente:</strong> Extracciones y Triturados Mineros — <strong>Sector:</strong> Minero Privado — <strong>Período:</strong> Oct 2018 / Feb 2019</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Suministro e instalación de cámaras IP para exteriores industriales</li>".
				"<li>Instalación en zonas de extracción y trituración</li>".
				"<li>Cableado estructurado y canalizaciones en ambiente industrial</li>".
				"<li>Configuración de sistema de grabación y monitoreo remoto</li>".
				"<li>Puesta en operación y pruebas funcionales</li>".
				"</ul>",
		],
		[
			'title' => 'Miguel Ángel Adame Quaas — Sistema IP Privado',
			'aliases' => ['Miguel Ángel Adame Quaas'],
			'content' =>
				"<h2>Instalación de Cámaras IP en Instalaciones Privadas</h2>".
				"<p><strong>Cliente:</strong> Miguel Ángel Adame Quaas — <strong>Sector:</strong> Privado — <strong>Período:</strong> Nov 2020 / May 2021</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Suministro e instalación de cámaras IP de seguridad</li>".
				"<li>Diseño y tendido de red de datos para videovigilancia</li>".
				"<li>Instalación de central de grabación y monitoreo</li>".
				"<li>Configuración de acceso remoto vía dispositivos móviles</li>".
				"<li>Capacitación al cliente final</li>".
				"</ul>",
		],
		[
			'title' => 'SEDENA — Zacatecas Frente 1, Sistema de Comunicaciones',
			'aliases' => ['SEDENA Zacatecas Frente 1','SEDENA – Zacatecas Frente 1'],
			'content' =>
				"<h2>Construcción de Instalaciones para Apoyo en Tareas de Seguridad Pública — Zacatecas Frente 1</h2>".
				"<p><strong>Cliente:</strong> SEDENA – DG Ingenieros — <strong>Sector:</strong> Seguridad Nacional — <strong>Período:</strong> 07 Jun 2021 / 10 Jul 2021</p>".
				"<p>Adquisición, suministro, instalación, configuración y puesta en operación del sistema de comunicaciones. 2 predios: \"La Escondida\" (Av. Universidad de Zacatecas S/N) y \"Los Indios\" (Km 54.5, Río Grande).</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Nodos dobles de datos (internet sala OPS) con cableado Cat 6A FTP blindado</li>".
				"<li>Gabinetes de comunicaciones empotrados de 12 unidades de rack</li>".
				"<li>Nodos sencillos de videovigilancia con cableado Cat 6A LSZH conectado a cámaras</li>".
				"<li>Nodos sencillos de voz con cableado UTP Cat 6A 100 ohms LSZH</li>".
				"<li>Equipo de cómputo (desktop Intel Core i7, 16GB RAM, 1TB, pantalla 15\")</li>".
				"<li>Escáner cama plana resolución 4800×9600 DPI</li>".
				"<li>Impresora monocromática láser 52 rpm, 1200×1200 DPI</li>".
				"<li>UPS 1400 VAC, 60Hz con 6 tomacorrientes Nema 5-15r</li>".
				"<li>Estación terrena remota satelital iDirect/IQ5+ hasta 100 Mbps — antena 1.8m</li>".
				"<li>Switch 28 puertos Gigabit + 2 SFP, administrable, capa 3, PoE (24 puertos)</li>".
				"<li>Teléfonos IP 6 líneas, 2 puertos RJ-45, PoE — instalación en servidor Asterisk</li>".
				"<li>Antena aérea para TV HD con booster, tubos, soportes, tornillería, cable coaxial, sellado</li>".
				"<li>Pantallas LED 55\" Smart 4K UHD con puertos LAN, HDMI y USB</li>".
				"<li>Torre de telecomunicaciones 15m, sección triangular 29-30cm, kit obstrucción aviación</li>".
				"<li>Cámaras de videovigilancia Axis M2025-le exterior, 1080p 2.1MP, PoE, IP66/IP4x</li>".
				"<li>Laptop con software Axis Camera Station para punto de monitoreo CCTV</li>".
				"<li>Grabador de video en red (NVR) Axis S2212, 6TB, 12ch/ptos PoE, 2 puertos LAN</li>".
				"<li>Pantalla Smart TV 4K LED 43\" con control remoto y cables</li>".
				"<li>Instalación, configuración e integración de todo el equipamiento</li>".
				"<li>Puesta en operación y pruebas funcionales en sitio</li>".
				"<li>Garantía de calidad por 12 meses, reemplazo en 48 hrs ante falla</li>".
				"</ul>",
		],
		[
			'title' => 'SEDENA — Guerrero Frente 1, Sistema de Comunicaciones',
			'aliases' => ['SEDENA Guerrero Frente 1','SEDENA – Guerrero Frente 1'],
			'content' =>
				"<h2>Suministro e Instalación de Sistema de Comunicaciones — Guerrero Frente 1</h2>".
				"<p><strong>Cliente:</strong> SEDENA – DG Ingenieros — <strong>Sector:</strong> Seguridad Nacional — <strong>Período:</strong> Oct 2022 / Nov 2022</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Suministro e instalación de sistema de comunicaciones para instalaciones militares</li>".
				"<li>Cableado estructurado Cat 6A para red de voz, datos y videovigilancia</li>".
				"<li>Instalación de gabinetes de comunicaciones y switches administrables</li>".
				"<li>Sistemas de CCTV con cámaras IP para perímetro e interiores</li>".
				"<li>Sistemas de telefonía IP y servidor de comunicaciones</li>".
				"<li>Torres de telecomunicaciones y antenas</li>".
				"<li>UPS y sistemas de respaldo eléctrico</li>".
				"</ul>",
		],
		[
			'title' => 'SEDENA — Hidalgo Frente 1, Sistema de Comunicaciones',
			'aliases' => ['SEDENA Hidalgo Frente 1','SEDENA – Hidalgo Frente 1'],
			'content' =>
				"<h2>Sistema de Comunicaciones para Instalaciones de Seguridad — Hidalgo Frente 1</h2>".
				"<p><strong>Cliente:</strong> SEDENA – DG Ingenieros — <strong>Sector:</strong> Seguridad Nacional — <strong>Período:</strong> Jun 2021 / Ago 2021</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Suministro de sistema de comunicaciones para instalación de seguridad pública</li>".
				"<li>Instalación de infraestructura de red de datos y voz</li>".
				"<li>Sistema de videovigilancia CCTV IP con grabación centralizada</li>".
				"<li>Telefonía IP con servidor PBX Asterisk</li>".
				"<li>Switches administrables y equipamiento activo de red</li>".
				"<li>Sistema de respaldo eléctrico (UPS) y reguladores</li>".
				"<li>Instalación de torres de comunicaciones y antenas</li>".
				"<li>Puesta en operación y capacitación</li>".
				"</ul>",
		],
		[
			'title' => 'SEDENA — Guerrero Frente 2 Chilapa, Sistema de Comunicaciones',
			'aliases' => ['SEDENA Guerrero Frente 2 Chilapa','SEDENA – Guerrero Frente 2 Chilapa'],
			'content' =>
				"<h2>Sistema de Comunicaciones para Instalaciones de Seguridad — Guerrero Frente 2 Chilapa</h2>".
				"<p><strong>Cliente:</strong> SEDENA – DG Ingenieros — <strong>Sector:</strong> Seguridad Nacional — <strong>Período:</strong> Ago 2021 / Oct 2021</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Suministro e instalación de equipo de comunicaciones en instalación militar</li>".
				"<li>Infraestructura de cableado estructurado Cat 6A</li>".
				"<li>Sistema de CCTV con grabación en red</li>".
				"<li>Equipamiento de red: switches, gabinetes, patch panels</li>".
				"<li>Nodos de datos, voz y videovigilancia</li>".
				"<li>Configuración e integración de todos los sistemas</li>".
				"</ul>",
		],
		[
			'title' => 'SEDENA — Guerrero Frente 2 Iguala, Comunicaciones Voz y Datos',
			'aliases' => ['SEDENA Guerrero Frente 2 Iguala','SEDENA – Guerrero Frente 2 Iguala'],
			'content' =>
				"<h2>Suministro de Equipo de Comunicaciones de Voz y Datos — Guerrero Frente 2 Iguala</h2>".
				"<p><strong>Cliente:</strong> SEDENA – DG Ingenieros — <strong>Sector:</strong> Seguridad Nacional — <strong>Período:</strong> Ene 2024</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Suministro de equipamiento de comunicaciones de voz y datos</li>".
				"<li>Switches administrables de alta capacidad con PoE</li>".
				"<li>Infraestructura de fibra óptica multimodo e interconexión</li>".
				"<li>Sistema CCTV IP con cámaras exteriores e interiores</li>".
				"<li>Gabinetes de telecomunicaciones y nodos de conexión</li>".
				"<li>Grabadores de video en red con alta capacidad de almacenamiento</li>".
				"<li>Telefonía IP con servidor SIP</li>".
				"<li>Sistema de audio y sonorización de instalaciones</li>".
				"<li>UPS y sistemas de protección eléctrica</li>".
				"</ul>",
		],
		[
			'title' => 'SEDENA — Guerrero Frente 4 Acapulco, Equipamiento Integral',
			'aliases' => ['SEDENA Guerrero Frente 4 Acapulco','SEDENA – Guerrero Frente 4 Acapulco'],
			'content' =>
				"<h2>Suministro e Instalación de Equipo de Comunicaciones — Guerrero Frente 4, 2024</h2>".
				"<p><strong>Cliente:</strong> SEDENA – DG Ingenieros — <strong>Sector:</strong> Seguridad Nacional — <strong>Período:</strong> 03 May 2024 / 31 Jul 2024</p>".
				"<p>Proyecto: Construcción y Equipamiento Coordinaciones Estatales, Batallones y Comandancias — Guerrero Frente 4, 2024. Predios San Agustín (Seguridad Veladero) y San Marcos (Alberca Costa Dorada).</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Gabinete sala de comunicaciones pared 24U × 2 piezas/predio — puerta frontal transparente, extractores, barra tierra, iluminación, organizadores, charolas, patch panel</li>".
				"<li>Nodos dobles de datos: 6 piezas/predio — placa convexa 2 puertos RJ-45, cable FTP Cat 6A 100Ω LSZH</li>".
				"<li>Nodos sencillos de videovigilancia: 23 piezas/predio — cable UTP Cat 6A LSZH conectado directamente a cámara MPTL</li>".
				"<li>Nodos sencillos de voz: 5 piezas/predio — placa convexa 1 puerto RJ-45, cable UTP Cat 6A 100Ω LSZH</li>".
				"<li>Switches administrables 48 puertos PoE 10/100/1000 1440W con 4 puertos fibra óptica × 2 piezas/predio</li>".
				"<li>UPS 2.2 KVA para gabinete de comunicaciones en rack 42U con regulador × 2 piezas/predio</li>".
				"<li>Pantallas LED 55\" Smart 4K UHD con soporte pared/techo × 1 pieza/predio</li>".
				"<li>Torres de telecomunicaciones HF/UHF 15m, sección triangular 29-30cm × 1 pieza/predio</li>".
				"<li>Sistema alarma sísmica VHF 162.400–162.550 MHz, PoE, bocinas exterior 30W × 1 pieza/predio</li>".
				"<li>Sistema de videoconferencia codec H.323/SIP, cámara Full HD 1080p 88° × 1 pieza/predio</li>".
				"<li>Cámaras CCTV exterior 2.1MP 30fps, PoE, IP66 IK10: 11 piezas/predio</li>".
				"<li>Cámaras CCTV interior 2.1MP 30fps, PoE, IP66 IK10: 12 piezas/predio</li>".
				"<li>Grabador NVR rack server 24TB, licencias para todas las cámaras × 1 pieza/predio</li>".
				"<li>Sonorización Patio de Honor: rack anti-shock, mezcladora 16ch, amplificador 800W RMS, 4 bocinas 600W, micrófonos inalámbricos</li>".
				"<li>Telefonía SIP con servidor Asterisk: teléfonos IP 2 RJ-45 PoE, 16 cuentas</li>".
				"<li>Instalación, configuración, integración y puesta en marcha en sitio</li>".
				"<li>Garantía de calidad 12 meses — reparación/sustitución en máx. 3 días naturales sin costo para SEDENA</li>".
				"<li>4 facturas CFDI (524, 525, 551, 552) — pagadas por transferencia bancaria</li>".
				"</ul>",
		],
		[
			'title' => 'SEDENA — Guerrero Frente 3, Sistema Integral de Comunicaciones',
			'aliases' => ['SEDENA Guerrero Frente 3','SEDENA – Guerrero Frente 3'],
			'content' =>
				"<h2>Adquisición de Sistema de Comunicación — Guerrero Frente 3, 2024</h2>".
				"<p><strong>Cliente:</strong> SEDENA – DG Ingenieros — <strong>Sector:</strong> Seguridad Nacional — <strong>Período:</strong> 24 May 2024 / 14 Sep 2024</p>".
				"<p>Construcción y Equipamiento Coordinaciones Estatales, Batallones y Comandancias, Estado de Guerrero, Frente 3, 2024. Total: $11,399,878.20 MXN — 6 facturas CFDI emitidas y pagadas mediante SPEI.</p>".
				"<h3>Actividades y Servicios</h3><ul>".
				"<li>Fibra óptica multimodo 6 hilos armada — 1,800 piezas (900m por enlace × 2 predios), escaneada y certificada</li>".
				"<li>Gabinetes profundos 42U (2 piezas) para sala de comunicaciones con UPS, extractores, iluminación, patch panels</li>".
				"<li>Gabinetes de pared 12U (4 piezas) para comunicaciones de área</li>".
				"<li>Nodos dobles de datos (50 piezas) con Cat 6A FTP 100 ohms LSZH y conectores RJ-45 blindados</li>".
				"<li>Nodos sencillos de videovigilancia (134 piezas) con cable MPTL Cat 6A directamente a cámaras</li>".
				"<li>Nodos sencillos de voz (40 piezas) con Cat 6A LSZH estándar ANSI/TIA/EIA 568</li>".
				"<li>Nodos sencillos de videoconferencia (4 piezas) y nodos de control ACC (8 piezas)</li>".
				"<li>Switches administrables 48 puertos PoE 10/100/1000 1440W (10 piezas)</li>".
				"<li>UPS 2.2 KVA para gabinete (2 piezas) y UPS 1.5 KVA de torre (6 piezas)</li>".
				"<li>Laptops para proyección: Intel/AMD 4 núcleos, 8GB RAM, 1TB SSD, Win10 Pro (2 piezas)</li>".
				"<li>Pantallas LED 55\" Smart 4K UHD con soporte pared/techo (12 piezas)</li>".
				"<li>Kits de seguridad para depósito de armas: panel alarma, sirena 120dB, sensores inalámbricos (8 piezas)</li>".
				"<li>Equipo de sonido profesional: rack anti-shock, amplificador 2ch 800W, mezcladora 16ch, bocinas 600W (2 piezas)</li>".
				"<li>Servidor SIP con Asterisk, IVR, ACD, interfaz Issabel 4.0 (2 piezas)</li>".
				"<li>Teléfonos IP ejecutivos (8 piezas) y teléfonos IP comunes (32 piezas)</li>".
				"<li>Torres de telecomunicaciones 15m con lámparas obstrucción aviación y pararrayos (2 piezas)</li>".
				"<li>Sistemas de alarma sísmica radio receptor VHF/UHF, CEA2009B y SAME (2 piezas)</li>".
				"<li>Unidades de videoconferencia codec H.323/SIP, cámara Full HD 88° con cancelación de eco (4 piezas)</li>".
				"<li>Cámaras CCTV exterior (80 piezas) y cámaras CCTV interior (42 piezas) 2.1MP 30fps PoE IP66/IK10</li>".
				"<li>Cámaras IP domo 360° 4K 3840×2160 PoE interior/exterior H.265 (12 piezas)</li>".
				"<li>Extensores de alimentación PoE compatibles con cámaras exteriores (30 piezas)</li>".
				"<li>Grabadores de video en red rack-server 24TB con licencias para todas las cámaras (2 piezas)</li>".
				"<li>Estación de monitoreo CCTV con Axis Camera Station, CPU Intel/AMD 4 núcleos, SSD 1TB (2 piezas)</li>".
				"<li>Equipo de sonido para sala de juntas: amplificador 2ch 500W, mezcladora 12ch, bocinas de plafón 30W (2 piezas)</li>".
				"<li>Sistema de audio Home Theater 5.1 con Blu-Ray y HDMI (2 piezas)</li>".
				"<li>Instalación, configuración, integración y puesta en operación en 2 predios</li>".
				"<li>Garantía de calidad 12 meses, reemplazo en 48 hrs, sin costo para SEDENA</li>".
				"</ul>",
		],
	];

	foreach ($case_entries as $ce) {
		$found_id = 0;
		if (!empty($ce['aliases'])) {
			foreach ($ce['aliases'] as $al) {
				$ex = get_page_by_title($al, OBJECT, 'case_study');
				if ($ex && !is_wp_error($ex)) { $found_id = (int)$ex->ID; break; }
			}
		}
		if (!$found_id) {
			$ex2 = get_page_by_title($ce['title'], OBJECT, 'case_study');
			if ($ex2 && !is_wp_error($ex2)) { $found_id = (int)$ex2->ID; }
		}
		if ($found_id) {
			wp_update_post(['ID'=>$found_id,'post_title'=>$ce['title'],'post_content'=>$ce['content'],'post_excerpt'=>wp_trim_words(wp_strip_all_tags($ce['content']), 40, '…')]);
		} else {
			$upsert('case_study', $ce['title'], $ce['content']);
		}
	}

	// Testimonios demo
	$tests = [
		['Gerente de Operaciones, Hotel Cancún', '"El equipo de SITEC no solo implementó tecnología, sino que mejoró la experiencia de nuestros huéspedes. La conectividad y seguridad superaron expectativas."'],
		['Administrador, Condominio CDMX', '"Gracias a SITEC, nuestro condominio redujo en un 35% los costos operativos y los residentes se sienten más seguros."'],
		['Director, Plaza Milenium', '"Con SITEC logramos reducir incidentes en un 45% y mejorar la satisfacción de los clientes."'],
	];
	foreach ($tests as $t) { $upsert('testimonial', $t[0], $t[1], $t[1]); }

	// Blog/Resources
	$blog_posts = [
		['Tendencias en Smart Buildings', 'Un resumen de las principales tendencias en edificios inteligentes para desarrolladores y hotelería.'],
		['Checklist de Energía Sostenible en Residenciales', 'Lista práctica para evaluar y mejorar la eficiencia energética en desarrollos residenciales.'],
	];
	foreach ($blog_posts as $bp) { $upsert('post', $bp[0], $bp[1], $bp[1]); }

    // KPIs Home (ACF repeater)
    if (function_exists('update_field')) {
        $front_id = (int) get_option('page_on_front');
        if ($front_id) {
            $kpis = [
                ['label' => '60% ahorro'],
                ['label' => '99.9% uptime'],
                ['label' => '+500 proyectos'],
            ];
            @update_field('kpis', $kpis, $front_id);
			// Partners demo (logotipos y enlaces de muestra)
			$partners_demo = [
				// Primero: UNV y Bosch
				['logo' => ['url' => 'https://via.placeholder.com/200x60?text=UNV'], 'name' => 'UNV (Uniview)', 'url' => ''],
				['logo' => ['url' => 'https://via.placeholder.com/200x60?text=Bosch'], 'name' => 'Bosch', 'url' => ''],
				// Marcas principales
				['logo' => ['url' => 'https://upload.wikimedia.org/wikipedia/commons/3/33/Axis_Communications_logo.svg'], 'name' => 'AXIS Communications', 'url' => 'https://www.axis.com/'],
				['logo' => ['url' => 'https://upload.wikimedia.org/wikipedia/commons/6/64/Cisco_logo_blue_2016.svg'], 'name' => 'Cisco', 'url' => 'https://www.cisco.com/'],
				['logo' => ['url' => 'https://upload.wikimedia.org/wikipedia/commons/8/8a/Ubiquiti_Networks_logo_2015.svg'], 'name' => 'Ubiquiti', 'url' => 'https://www.ui.com/'],
				['logo' => ['url' => 'https://upload.wikimedia.org/wikipedia/commons/a/a1/Panduit_logo.svg'], 'name' => 'Panduit', 'url' => 'https://www.panduit.com/'],
				['logo' => ['url' => 'https://upload.wikimedia.org/wikipedia/commons/8/8d/Fluke_logo.svg'], 'name' => 'Fluke Networks', 'url' => 'https://www.flukenetworks.com/'],
				['logo' => ['url' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Hikvision_logo.svg'], 'name' => 'Hikvision', 'url' => 'https://www.hikvision.com/'],
				// Al final: placeholders restantes
				['logo' => ['url' => 'https://via.placeholder.com/200x60?text=Hytera'], 'name' => 'Hytera', 'url' => ''],
				['logo' => ['url' => 'https://via.placeholder.com/200x60?text=EPCOM'], 'name' => 'EPCOM', 'url' => ''],
			];
			@update_field('partners', $partners_demo, $front_id);
        }
    }

	// Menús (principal y footer)
	$locations = get_theme_mod('nav_menu_locations');
	if (!is_array($locations)) { $locations = []; }

	// Crear o tomar menús
	$primary_menu_id = 0; $footer_menu_id = 0;
	$primary_menu = wp_get_nav_menu_object('Principal');
	if (!$primary_menu) { $primary_menu_id = wp_create_nav_menu('Principal'); } else { $primary_menu_id = (int) $primary_menu->term_id; }
	$footer_menu = wp_get_nav_menu_object('Footer');
	if (!$footer_menu) { $footer_menu_id = wp_create_nav_menu('Footer'); } else { $footer_menu_id = (int) $footer_menu->term_id; }

	// Asignar ubicaciones si no están
	$locs = get_nav_menu_locations();
	if ( empty($locs['primary']) ) { $locs['primary'] = $primary_menu_id; }
	if ( empty($locs['footer']) )  { $locs['footer']  = $footer_menu_id; }
	set_theme_mod('nav_menu_locations', $locs);

	// Agregar ítems comunes
	function sitec_normalize_url($u){ $u = trailingslashit($u); return strtolower($u); }
	function sitec_seed_menu_item($menu_id, $title, $url) {
		if (!$menu_id) return;
		$items = wp_get_nav_menu_items($menu_id) ?: [];
		$u_new = sitec_normalize_url($url);
		foreach ($items as $it) {
			$u_old = sitec_normalize_url($it->url ?: '');
			// Si coincide por título, actualizar URL si difiere
			if (trim(wp_strip_all_tags($it->title)) === trim($title)) {
				if ($u_old !== $u_new) {
					wp_update_nav_menu_item($menu_id, $it->ID, [
						'menu-item-title'  => $title,
						'menu-item-url'    => $url,
						'menu-item-status' => 'publish'
					]);
				}
				return;
			}
			// Si coincide por URL normalizada, no hacer nada
			if ($u_old === $u_new) { return; }
		}
		wp_update_nav_menu_item($menu_id, 0, [
			'menu-item-title'  => $title,
			'menu-item-url'    => $url,
			'menu-item-status' => 'publish'
		]);
	}

	function sitec_dedupe_menu($menu_id){
		$items = wp_get_nav_menu_items($menu_id) ?: [];
		$seen = [];
		foreach ($items as $it) {
			$key = sitec_normalize_url($it->url ?: '') . '|' . trim(wp_strip_all_tags($it->title));
			if (isset($seen[$key])) {
				// eliminar duplicado
				wp_delete_post($it->ID, true);
			} else {
				$seen[$key] = true;
			}
		}
	}

	$home_id   = (int) get_option('page_on_front');
	$posts_id  = (int) get_option('page_for_posts');
	$about     = get_page_by_title('Nosotros');
	$contact   = get_page_by_title('Contacto');
	$privacy   = get_page_by_path('privacy-policy') ?: get_page_by_title('Aviso de Privacidad');
	$terms     = get_page_by_path('terms') ?: get_page_by_title('Términos de Servicio');
	$home_url  = $home_id ? get_permalink($home_id) : home_url('/');
	$blog_url  = $posts_id ? get_permalink($posts_id) : home_url('/blog');
	$about_url = $about ? get_permalink($about) : home_url('/nosotros');
	$contact_url = $contact ? get_permalink($contact) : home_url('/contacto');
	$privacy_url = $privacy ? get_permalink($privacy) : home_url('/privacy-policy');
	$terms_url   = $terms ? get_permalink($terms) : home_url('/terms');

	// Ítems
	sitec_seed_menu_item($primary_menu_id, 'Inicio', $home_url);
	sitec_seed_menu_item($primary_menu_id, 'Servicios', home_url('/services'));
	sitec_seed_menu_item($primary_menu_id, 'Casos de Éxito', home_url('/cases'));
	sitec_seed_menu_item($primary_menu_id, 'Blog', $blog_url);
	sitec_seed_menu_item($primary_menu_id, 'Nosotros', $about_url);
	sitec_seed_menu_item($primary_menu_id, 'Contacto', $contact_url);

	// Footer links
	sitec_seed_menu_item($footer_menu_id, 'Aviso de Privacidad', $privacy_url);
	sitec_seed_menu_item($footer_menu_id, 'Términos de Servicio', $terms_url);
	sitec_seed_menu_item($footer_menu_id, 'Contacto', $contact_url);

	// Deduplicar por si el seed se ejecutó previamente
	sitec_dedupe_menu($primary_menu_id);
	sitec_dedupe_menu($footer_menu_id);

	wp_safe_redirect( admin_url('index.php?sitec_seed_done=1') );
	exit;
});

// Helper para sacar URL del seed con nonce
function sitec_get_seed_url(){
	return wp_nonce_url( admin_url('index.php?sitec_seed=1'), 'sitec_seed_action');
}

// Aviso en admin para ejecutar seeding si falta contenido
add_action('admin_notices', function(){
    if (!current_user_can('manage_options')) return;
    $url = esc_url( sitec_get_seed_url() );
    echo '<div class="notice notice-info"><p>Actualizar contenido real (Home, Servicios, Casos, Testimonios, Blog). <a class="button button-primary" href="'.$url.'">Aplicar contenido</a></p></div>';
});


// Botón para limpiar el contenido del editor de la página de Inicio (no afecta lo que muestra el sitio)
add_action('admin_notices', function(){
    if (!current_user_can('manage_options')) return;
    $front_id = (int) get_option('page_on_front');
    if (!$front_id) return;
    $content_raw = (string) get_post_field('post_content', $front_id);
    if (trim(wp_strip_all_tags($content_raw)) === '') return; // nada que limpiar
    $url = wp_nonce_url( admin_url('index.php?sitec_clean_front=1'), 'sitec_clean_front_action' );
    echo '<div class="notice notice-warning"><p>La página de Inicio tiene contenido en el editor que no se usa en el sitio. <a class="button" href="'.esc_url($url).'">Limpiar contenido de Inicio</a></p></div>';
});

add_action('admin_init', function(){
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['sitec_clean_front'])) return;
    check_admin_referer('sitec_clean_front_action');
    $front_id = (int) get_option('page_on_front');
    if ($front_id) {
        wp_update_post(['ID' => $front_id, 'post_content' => '' ]);
    }
    wp_safe_redirect( admin_url('index.php?sitec_clean_front_done=1') );
    exit;
});

add_action('admin_notices', function(){
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['sitec_clean_front_done'])) return;
    echo '<div class="notice notice-success is-dismissible"><p>Contenido del editor en "Inicio" limpiado. La portada seguirá usando las secciones del tema.</p></div>';
});

// Metabox en la edición de Página para sembrar bloques solo en esa página (principalmente "Inicio")
add_action('add_meta_boxes', function(){
    add_meta_box('sitec_seed_page_blocks', __('Sembrar bloques de portada','sitec'), function($post){
        if (!current_user_can('manage_options')) return;
        $front_id = (int) get_option('page_on_front');
        if ((int)$post->ID !== $front_id) {
            echo '<p>' . esc_html__('Este botón está disponible solo para la página establecida como "Inicio".','sitec') . '</p>';
            return;
        }
        $url = add_query_arg(['sitec_seed_page' => (int)$post->ID], admin_url('post.php?post='.(int)$post->ID.'&action=edit'));
        $url = wp_nonce_url($url, 'sitec_seed_page_action');
        echo '<p>' . esc_html__('Reemplaza el contenido actual por bloques de portada preconfigurados.','sitec') . '</p>';
        echo '<a class="button button-primary" href="'.esc_url($url).'">' . esc_html__('Sembrar bloques','sitec') . '</a>';
    }, 'page', 'side', 'high');
});

// Acción para procesar el sembrado desde el metabox
add_action('admin_init', function(){
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['sitec_seed_page'])) return;
    check_admin_referer('sitec_seed_page_action');
    $page_id = (int) $_GET['sitec_seed_page'];
    if ($page_id <= 0) return;

    // Leer valores de ACF del Hero si existen
    $front_id = (int) get_option('page_on_front');
    $hero_title = function_exists('get_field') ? trim((string) get_field('hero_title', $front_id)) : '';
    $hero_text  = function_exists('get_field') ? trim((string) get_field('hero_text', $front_id)) : '';
    $cta1_label = function_exists('get_field') ? trim((string) get_field('hero_cta_primary_label', $front_id)) : '';
    $cta1_url   = function_exists('get_field') ? trim((string) get_field('hero_cta_primary_url', $front_id)) : '';
    $cta2_label = function_exists('get_field') ? trim((string) get_field('hero_cta_secondary_label', $front_id)) : '';
    $cta2_url   = function_exists('get_field') ? trim((string) get_field('hero_cta_secondary_url', $front_id)) : '';
    if ($hero_title === '') { $hero_title = 'Ingeniería que Conecta y Protege a México'; }
    if ($hero_text === '') { $hero_text = 'Más de 20 años transformando infraestructuras críticas con tecnología de vanguardia.'; }
    if ($cta1_label === '') { $cta1_label = 'Consultoría Gratuita'; }
    if ($cta1_url === '') { $cta1_url = (string) ( get_permalink( get_page_by_path('contacto') ) ?: home_url('/contacto') ); }
    if ($cta2_label === '') { $cta2_label = 'Ver Proyectos Destacados'; }
    if ($cta2_url === '') { $cta2_url = '#servicios'; }

    $blocks = '<!-- wp:cover {"dimRatio":40,"minHeight":420,"minHeightUnit":"px"} -->\n'
        . '<div class="wp-block-cover__inner-container">'
        . '<!-- wp:heading {"level":1} --><h1 class="wp-block-heading">' . esc_html($hero_title) . '</h1><!-- /wp:heading -->'
        . '<!-- wp:paragraph --><p>' . esc_html($hero_text) . '</p><!-- /wp:paragraph -->'
        . '<!-- wp:buttons --><div class="wp-block-buttons">'
        . '<!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link" href="' . esc_url( $cta1_url ) . '">' . esc_html($cta1_label) . '</a></div><!-- /wp:button -->'
        . '<!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link" href="' . esc_url( $cta2_url ) . '">' . esc_html($cta2_label) . '</a></div><!-- /wp:button -->'
        . '</div><!-- /wp:buttons -->'
        . '</div>'
        . '<!-- /wp:cover -->';

    wp_update_post(['ID' => $page_id, 'post_content' => $blocks]);
    wp_safe_redirect( admin_url('post.php?post='.$page_id.'&action=edit&sitec_seed_done=1') );
    exit;
});

// Aviso de éxito al volver al editor de la página
add_action('admin_notices', function(){
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['sitec_seed_done'])) return;
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'page') return;
    echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__('Bloques de portada insertados correctamente. Guarda para conservar cambios.','sitec').'</p></div>';
});


// Metabox: Restablecer Hero a valores por defecto en la página Inicio
add_action('add_meta_boxes', function(){
    add_meta_box('sitec_reset_hero', __('Restablecer Hero','sitec'), function($post){
        if (!current_user_can('manage_options')) return;
        $front_id = (int) get_option('page_on_front');
        if ((int)$post->ID !== $front_id) return;
        $url = add_query_arg(['sitec_reset_hero' => (int)$post->ID], admin_url('post.php?post='.(int)$post->ID.'&action=edit'));
        $url = wp_nonce_url($url, 'sitec_reset_hero_action');
        echo '<p>'.esc_html__('Restaura el Hero a los textos y estilos por defecto del tema. No afecta otras secciones.','sitec').'</p>';
        echo '<a class="button" href="'.esc_url($url).'">'.esc_html__('Restablecer Hero','sitec').'</a>';
    }, 'page', 'side', 'low');
});

// Acción: procesar restablecimiento del Hero
add_action('admin_init', function(){
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['sitec_reset_hero'])) return;
    check_admin_referer('sitec_reset_hero_action');
    $page_id = (int) $_GET['sitec_reset_hero'];
    if ($page_id <= 0) return;

    // Valores por defecto tomados de la plantilla
    $defaults = [
        'hero_title' => 'Ingeniería que Conecta y Protege a México',
        'hero_text'  => 'Más de 20 años transformando infraestructuras críticas con tecnología de vanguardia. Soluciones integrales en seguridad, telecomunicaciones y energía respaldadas por proyectos con Guardia Nacional, SEDENA e INE.',
        'hero_cta_primary_label' => 'Consultoría Gratuita',
        'hero_cta_primary_url'   => (string) ( get_permalink( get_page_by_path('contacto') ) ?: home_url('/contacto') ),
        'hero_cta_secondary_label' => 'Ver Proyectos Destacados',
        'hero_cta_secondary_url'   => (string) ( get_post_type_archive_link('case_study') ?: home_url('/cases') ),
        'hero_overlay_color'   => '#0f172a',
        'hero_overlay_opacity' => 85,
        'hero_alignment' => 'left',
        'hero_height'    => 'normal',
        'hero_img_fit'   => 'cover',
        'hero_img_position' => 'center',
    ];

    if ( function_exists('update_field') ) {
        foreach ($defaults as $k=>$v) { @update_field($k, $v, $page_id); }
        // Vaciar imagen
        @update_field('hero_image', null, $page_id);
    } else {
        foreach ($defaults as $k=>$v) { update_post_meta($page_id, $k, $v); }
        delete_post_meta($page_id, 'hero_image');
    }

    wp_safe_redirect( admin_url('post.php?post='.$page_id.'&action=edit&sitec_reset_hero_done=1') );
    exit;
});

// Aviso de éxito al restablecer Hero
add_action('admin_notices', function(){
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['sitec_reset_hero_done'])) return;
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'page') return;
    echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__('Hero restablecido a valores por defecto.','sitec').'</p></div>';
});


// Sincronización diaria de páginas legales y menús (idempotente)
add_action('admin_init', function(){
    if (!current_user_can('manage_options')) return;
    if (get_transient('sitec_daily_sync_done')) return;

    $ensure_page = function($title, $slug, $content = '', $opt = ''){
        $existing = get_page_by_path($slug) ?: get_page_by_title($title);
        if ($existing && !is_wp_error($existing)) {
            wp_update_post(['ID' => $existing->ID, 'post_name' => sanitize_title($slug)]);
            $page_id = (int) $existing->ID;
        } else {
            $page_id = (int) wp_insert_post([
                'post_title' => $title,
                'post_name'  => sanitize_title($slug),
                'post_type'  => 'page',
                'post_status'=> 'publish',
                'post_content'=> $content,
            ]);
        }
        if ($page_id && !is_wp_error($page_id)) {
            if ($opt === 'page_for_posts') { update_option('page_for_posts', $page_id); }
            if ($opt === 'page_on_front') { update_option('page_on_front', $page_id); update_option('show_on_front', 'page'); }
        }
        return $page_id;
    };

    $blog_id    = $ensure_page('Blog', 'blog', '', 'page_for_posts');
    $privacy_id = $ensure_page('Aviso de Privacidad', 'privacy-policy', 'Esta es la página de aviso de privacidad. Actualice el contenido conforme a sus políticas.');
    $terms_id   = $ensure_page('Términos de Servicio', 'terms', 'Esta es la página de términos de servicio. Actualice el contenido con sus condiciones.');

    $home_id    = (int) get_option('page_on_front');
    $about      = get_page_by_title('Nosotros');
    $contact    = get_page_by_title('Contacto');
    $home_url   = $home_id ? get_permalink($home_id) : home_url('/');
    $blog_url   = $blog_id ? get_permalink($blog_id) : home_url('/blog');
    $about_url  = $about ? get_permalink($about) : home_url('/nosotros');
    $contact_url= $contact ? get_permalink($contact) : home_url('/contacto');
    $privacy_url= $privacy_id ? get_permalink($privacy_id) : home_url('/privacy-policy');
    $terms_url  = $terms_id ? get_permalink($terms_id) : home_url('/terms');

    $primary_menu = wp_get_nav_menu_object('Principal');
    if (!$primary_menu) { $primary_menu_id = (int) wp_create_nav_menu('Principal'); } else { $primary_menu_id = (int) $primary_menu->term_id; }
    $footer_menu = wp_get_nav_menu_object('Footer');
    if (!$footer_menu) { $footer_menu_id = (int) wp_create_nav_menu('Footer'); } else { $footer_menu_id = (int) $footer_menu->term_id; }

    $normalize = function($u){ return trailingslashit(strtolower((string)$u)); };
    $ensure_menu_item = function($menu_id, $title, $url) use ($normalize){
        if (!$menu_id) return;
        $items = wp_get_nav_menu_items($menu_id) ?: [];
        $new_u = $normalize($url);
        foreach ($items as $it) {
            $old_u = $normalize($it->url ?: '');
            if (trim(wp_strip_all_tags($it->title)) === trim($title)) {
                if ($old_u !== $new_u) {
                    wp_update_nav_menu_item($menu_id, $it->ID, [
                        'menu-item-title' => $title,
                        'menu-item-url'   => $url,
                        'menu-item-status'=> 'publish',
                    ]);
                }
                return;
            }
            if ($old_u === $new_u) { return; }
        }
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => $title,
            'menu-item-url'   => $url,
            'menu-item-status'=> 'publish',
        ]);
    };

    $ensure_menu_item($primary_menu_id, 'Inicio', $home_url);
    $ensure_menu_item($primary_menu_id, 'Servicios', home_url('/services'));
    $ensure_menu_item($primary_menu_id, 'Casos de Éxito', home_url('/cases'));
    $ensure_menu_item($primary_menu_id, 'Blog', $blog_url);
    $ensure_menu_item($primary_menu_id, 'Nosotros', $about_url);
    $ensure_menu_item($primary_menu_id, 'Contacto', $contact_url);

    $ensure_menu_item($footer_menu_id, 'Aviso de Privacidad', $privacy_url);
    $ensure_menu_item($footer_menu_id, 'Términos de Servicio', $terms_url);
    $ensure_menu_item($footer_menu_id, 'Contacto', $contact_url);

    set_transient('sitec_daily_sync_done', 1, DAY_IN_SECONDS);
});

// Aviso en admin: migrar partners ACF -> CPT
add_action('admin_notices', function(){
    if (!current_user_can('manage_options')) return;
    $front_id = (int) get_option('page_on_front');
    if (!$front_id) return;
    if (!function_exists('have_rows') || !have_rows('partners', $front_id)) return;
    $url = wp_nonce_url( admin_url('index.php?sitec_migrate_partners=1'), 'sitec_migrate_partners_action' );
    echo '<div class="notice notice-info"><p>¿Desea migrar los logos del repeater ACF a "Socios/Marcas" para administrarlos como entradas? <a class="button" href="'.esc_url($url).'">Migrar Partners (ACF → CPT)</a></p></div>';
});

// Acción: procesar migración de partners ACF -> CPT
add_action('admin_init', function(){
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['sitec_migrate_partners'])) return;
    check_admin_referer('sitec_migrate_partners_action');

    $front_id = (int) get_option('page_on_front');
    if (!$front_id) {
        wp_safe_redirect( admin_url('index.php?sitec_migrate_partners_done=1&created=0&updated=0') );
        exit;
    }

    $created = 0; $updated = 0; $order = 0;
    if ( function_exists('have_rows') && have_rows('partners', $front_id) ) {
        // Cargar helpers de media para sideload si se ocupan
        if (!function_exists('media_sideload_image')) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        while ( have_rows('partners', $front_id) ) { the_row();
            $name = trim((string) get_sub_field('name'));
            $url  = trim((string) get_sub_field('url'));
            $logo = get_sub_field('logo');
            $logo_id = 0;
            $logo_url = '';
            if (is_array($logo)) {
                if (!empty($logo['id'])) { $logo_id = (int) $logo['id']; }
                if (!$logo_id && !empty($logo['ID'])) { $logo_id = (int) $logo['ID']; }
                if (!empty($logo['url'])) { $logo_url = (string) $logo['url']; }
            }

            if ($name === '' && $logo_id === 0 && $logo_url === '') { continue; }

            $post_title = $name !== '' ? $name : ('Socio ' . ($order + 1));
            $existing = get_page_by_title($post_title, OBJECT, 'partner');
            $args = [
                'post_type' => 'partner',
                'post_status' => 'publish',
                'menu_order' => $order,
            ];
            $partner_id = 0;
            if ($existing && !is_wp_error($existing)) {
                $args['ID'] = (int) $existing->ID;
                $partner_id = (int) wp_update_post($args);
                if (!is_wp_error($partner_id)) { $updated++; }
            } else {
                $args['post_title'] = $post_title;
                $partner_id = (int) wp_insert_post($args);
                if (!is_wp_error($partner_id) && $partner_id) { $created++; }
            }
            if ($partner_id && !is_wp_error($partner_id)) {
                if ( function_exists('update_field') ) { @update_field('website_url', $url, $partner_id); }
                // Asignar logo
                if ($logo_id > 0) {
                    set_post_thumbnail($partner_id, $logo_id);
                } elseif ($logo_url !== '' && function_exists('media_sideload_image')) {
                    $att_id = @media_sideload_image($logo_url, $partner_id, $post_title, 'id');
                    if (!is_wp_error($att_id) && (int)$att_id > 0) { set_post_thumbnail($partner_id, (int)$att_id); }
                }
            }
            $order++;
        }
    }

    wp_safe_redirect( admin_url('index.php?sitec_migrate_partners_done=1&created='.(int)$created.'&updated='.(int)$updated) );
    exit;
});

// Aviso de éxito de migración
add_action('admin_notices', function(){
    if (!current_user_can('manage_options')) return;
    if (empty($_GET['sitec_migrate_partners_done'])) return;
    $created = isset($_GET['created']) ? (int) $_GET['created'] : 0;
    $updated = isset($_GET['updated']) ? (int) $_GET['updated'] : 0;
    $link = esc_url( admin_url('edit.php?post_type=partner') );
    echo '<div class="notice notice-success is-dismissible"><p>Partners migrados. Creados: <strong>'.(int)$created.'</strong>, Actualizados: <strong>'.(int)$updated.'</strong>. <a href="'.$link.'">Ver Socios/Marcas</a></p></div>';
});

