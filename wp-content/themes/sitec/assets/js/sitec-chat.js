(function () {
    'use strict';

    /* ── Configuración del bot ─────────────────────────────────────────────── */
    var BOT_NAME   = 'Asistente SITEC';
    var BOT_AVATAR = '🤖';
    var AJAX_URL   = (window.sitecChat && window.sitecChat.ajaxUrl) || '/wp-admin/admin-ajax.php';
    var NONCE      = (window.sitecChat && window.sitecChat.nonce)   || '';

    /* ── Árbol de conversación ─────────────────────────────────────────────── */
    var TREE = {
        welcome: {
            text: '¡Hola! 👋 Soy el asistente virtual de <strong>SITEC</strong>. ¿En qué puedo ayudarte hoy?',
            replies: [
                { label: '🔧 Servicios',          next: 'servicios'  },
                { label: '📁 Casos de éxito',      next: 'casos'      },
                { label: '💰 Precios',              next: 'precios'    },
                { label: '📞 Hablar con un asesor', next: 'lead_form'  },
            ],
        },
        servicios: {
            text: 'Ofrecemos soluciones integrales en:\n\n✅ Videovigilancia y seguridad electrónica\n✅ Telecomunicaciones y redes\n✅ Energía UPS y reguladores\n✅ Cableado estructurado\n✅ Radiocomunicación\n\n¿Sobre cuál quieres saber más?',
            replies: [
                { label: '📷 Videovigilancia',  next: 'videovigi'  },
                { label: '🌐 Redes y Telecom',  next: 'redes'      },
                { label: '⚡ Energía',          next: 'energia'    },
                { label: '📞 Quiero cotizar',   next: 'lead_form'  },
                { label: '← Volver',            next: 'welcome'    },
            ],
        },
        videovigi: {
            text: '📷 <strong>Videovigilancia:</strong>\n\nInstalamos sistemas CCTV con cámaras AXIS, Hikvision, Dahua y Bosch. Trabajamos con tecnología HD, 4K, térmica y análisis de video por IA.\n\nClientes: Guardia Nacional, SEDENA, INE y más de 500 proyectos.',
            replies: [
                { label: '📞 Solicitar diagnóstico', next: 'lead_form' },
                { label: '← Más servicios',          next: 'servicios' },
            ],
        },
        redes: {
            text: '🌐 <strong>Redes y Telecomunicaciones:</strong>\n\nDiseñamos e instalamos redes LAN/WAN, Wi-Fi empresarial, fibra óptica, MPLS y soluciones 5G. Partners certificados de Cisco, Ubiquiti y MikroTik.',
            replies: [
                { label: '📞 Solicitar diagnóstico', next: 'lead_form' },
                { label: '← Más servicios',          next: 'servicios' },
            ],
        },
        energia: {
            text: '⚡ <strong>Energía:</strong>\n\nInstalamos UPS, reguladores, tableros de distribución y sistemas de respaldo para infraestructuras críticas. Marcas: APC by Schneider, Eaton.',
            replies: [
                { label: '📞 Solicitar diagnóstico', next: 'lead_form' },
                { label: '← Más servicios',          next: 'servicios' },
            ],
        },
        casos: {
            text: '📁 <strong>Casos de éxito:</strong>\n\nHemos completado <strong>+500 proyectos</strong>, incluyendo:\n\n🏛️ Guardia Nacional — Videovigilancia nacional\n⚖️ SEDENA — Infraestructura de red\n🗳️ INE — Telecomunicaciones\n\n98% de satisfacción comprobada.',
            replies: [
                { label: '🔍 Ver más proyectos',    next: 'casos_mas'  },
                { label: '📞 Quiero algo similar',  next: 'lead_form'  },
                { label: '← Volver',               next: 'welcome'    },
            ],
        },
        casos_mas: {
            text: 'Puedes ver todos nuestros casos de éxito en la sección de proyectos del sitio. ¿Te gustaría que un asesor te presente un portafolio completo personalizado?',
            replies: [
                { label: '✅ Sí, que me contacten', next: 'lead_form' },
                { label: '← Volver',               next: 'welcome'   },
            ],
        },
        precios: {
            text: '💰 <strong>Precios:</strong>\n\nCada proyecto es único y el costo depende del alcance. Por eso ofrecemos un <strong>diagnóstico técnico GRATUITO</strong> sin compromiso.\n\nUn asesor visita tu instalación o hace videollamada, analiza tus necesidades y te entrega una propuesta detallada.',
            replies: [
                { label: '🎯 Quiero mi diagnóstico gratis', next: 'lead_form' },
                { label: '← Volver',                       next: 'welcome'   },
            ],
        },
        lead_form: {
            text: '¡Perfecto! 🎉 Déjame tus datos y un asesor SITEC te contactará en <strong>menos de 24 horas</strong>.',
            form: true,
        },
        gracias: {
            text: '✅ <strong>¡Mensaje enviado!</strong>\n\nTe contactaremos muy pronto. También recibirás una confirmación en tu correo.\n\n¿Hay algo más en lo que pueda ayudarte?',
            replies: [
                { label: '← Menú principal', next: 'welcome' },
            ],
        },
        error: {
            text: '❌ No se pudo enviar tu mensaje. Por favor intenta de nuevo o escríbenos a <strong>contacto@sitec.com.mx</strong>.',
            replies: [
                { label: '🔄 Intentar de nuevo', next: 'lead_form' },
                { label: '← Menú principal',     next: 'welcome'   },
            ],
        },
    };

    /* ── Estado ────────────────────────────────────────────────────────────── */
    var isOpen     = false;
    var isTyping   = false;
    var hasGreeted = false;

    /* ── DOM ───────────────────────────────────────────────────────────────── */
    function buildWidget() {
        // Botón flotante
        var btn = document.createElement('button');
        btn.id = 'sitec-chat-btn';
        btn.setAttribute('aria-label', 'Abrir chat');
        btn.innerHTML = [
            '<span class="chat-icon-open">',
            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">',
            '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
            '</svg></span>',
            '<span class="chat-icon-close">',
            '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
            '</span>',
            '<span class="sitec-chat-pulse"></span>',
        ].join('');

        // Ventana del chat
        var win = document.createElement('div');
        win.id = 'sitec-chat-window';
        win.className = 'chat-hidden';
        win.innerHTML = [
            '<div class="sitec-chat-header">',
            '  <div class="sitec-chat-avatar">' + BOT_AVATAR + '</div>',
            '  <div class="sitec-chat-header-info">',
            '    <div class="sitec-chat-header-name">' + BOT_NAME + '</div>',
            '    <div class="sitec-chat-header-status">En línea ahora</div>',
            '  </div>',
            '  <button class="sitec-chat-close" aria-label="Cerrar">',
            '    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
            '  </button>',
            '</div>',
            '<div class="sitec-chat-messages" id="sitec-chat-msgs"></div>',
            '<div class="sitec-chat-footer">',
            '  <input class="sitec-chat-input" id="sitec-chat-text" type="text" placeholder="Escribe tu pregunta..." autocomplete="off" />',
            '  <button class="sitec-chat-send" id="sitec-chat-send" aria-label="Enviar">',
            '    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>',
            '  </button>',
            '</div>',
        ].join('');

        document.body.appendChild(btn);
        document.body.appendChild(win);

        // Eventos
        btn.addEventListener('click', toggleChat);
        win.querySelector('.sitec-chat-close').addEventListener('click', closeChat);
        win.querySelector('#sitec-chat-send').addEventListener('click', onTextSend);
        win.querySelector('#sitec-chat-text').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); onTextSend(); }
        });
    }

    /* ── Abrir / cerrar ────────────────────────────────────────────────────── */
    function toggleChat() {
        isOpen ? closeChat() : openChat();
    }
    function openChat() {
        isOpen = true;
        document.getElementById('sitec-chat-window').classList.remove('chat-hidden');
        document.getElementById('sitec-chat-btn').classList.add('is-open');
        // Ocultar pulso
        var pulse = document.querySelector('.sitec-chat-pulse');
        if (pulse) { pulse.style.display = 'none'; }
        // Saludo automático una vez
        if (!hasGreeted) {
            hasGreeted = true;
            setTimeout(function() { showNode('welcome'); }, 400);
        }
        scrollToBottom();
    }
    function closeChat() {
        isOpen = false;
        document.getElementById('sitec-chat-window').classList.add('chat-hidden');
        document.getElementById('sitec-chat-btn').classList.remove('is-open');
    }

    /* ── Renderizado de mensajes ───────────────────────────────────────────── */
    function getContainer() { return document.getElementById('sitec-chat-msgs'); }

    function addBotMessage(html) {
        var c = getContainer();
        var wrap = document.createElement('div');
        wrap.className = 'chat-msg chat-msg-bot';
        wrap.innerHTML = [
            '<div class="chat-msg-bot-avatar">' + BOT_AVATAR + '</div>',
            '<div class="chat-bubble">' + formatText(html) + '</div>',
        ].join('');
        c.appendChild(wrap);
        scrollToBottom();
    }

    function addUserMessage(text) {
        var c = getContainer();
        var wrap = document.createElement('div');
        wrap.className = 'chat-msg chat-msg-user';
        wrap.innerHTML = '<div class="chat-bubble">' + escHtml(text) + '</div>';
        c.appendChild(wrap);
        scrollToBottom();
    }

    function showTyping() {
        var c = getContainer();
        var wrap = document.createElement('div');
        wrap.className = 'chat-msg chat-msg-bot chat-typing';
        wrap.id = 'chat-typing-indicator';
        wrap.innerHTML = [
            '<div class="chat-msg-bot-avatar">' + BOT_AVATAR + '</div>',
            '<div class="chat-bubble"><span class="chat-dot"></span><span class="chat-dot"></span><span class="chat-dot"></span></div>',
        ].join('');
        c.appendChild(wrap);
        scrollToBottom();
    }
    function hideTyping() {
        var el = document.getElementById('chat-typing-indicator');
        if (el) { el.remove(); }
    }

    function addQuickReplies(replies) {
        var c = getContainer();
        var wrap = document.createElement('div');
        wrap.className = 'chat-quick-replies';
        replies.forEach(function(r) {
            var btn = document.createElement('button');
            btn.className = 'chat-quick-reply';
            btn.textContent = r.label;
            btn.addEventListener('click', function() {
                // Eliminar todos los quick replies al seleccionar uno
                var qr = c.querySelector('.chat-quick-replies');
                if (qr) { qr.remove(); }
                addUserMessage(r.label);
                setTimeout(function() { showNode(r.next); }, 300);
            });
            wrap.appendChild(btn);
        });
        c.appendChild(wrap);
        scrollToBottom();
    }

    /* ── Nodo del árbol de conversación ────────────────────────────────────── */
    function showNode(nodeKey) {
        if (isTyping) { return; }
        var node = TREE[nodeKey];
        if (!node) { return; }

        isTyping = true;
        showTyping();

        var delay = 700 + Math.min(node.text.length * 12, 900);
        setTimeout(function() {
            hideTyping();
            isTyping = false;
            addBotMessage(node.text);

            if (node.form) {
                setTimeout(function() { addLeadForm(); }, 200);
            } else if (node.replies) {
                setTimeout(function() { addQuickReplies(node.replies); }, 200);
            }
        }, delay);
    }

    /* ── Formulario de captura de lead ─────────────────────────────────────── */
    function addLeadForm() {
        var c = getContainer();
        var wrap = document.createElement('div');
        wrap.className = 'chat-msg chat-msg-bot';
        wrap.innerHTML = '<div class="chat-msg-bot-avatar">' + BOT_AVATAR + '</div><div class="chat-bubble" style="max-width:90%;padding:12px;"><div class="sitec-chat-form" id="sitec-lead-form"></div></div>';
        c.appendChild(wrap);

        var form = document.getElementById('sitec-lead-form');
        form.innerHTML = [
            '<input type="text"  id="cl-nombre"   placeholder="Tu nombre *"    />',
            '<input type="email" id="cl-email"    placeholder="Tu email *"      />',
            '<input type="tel"   id="cl-telefono" placeholder="Teléfono (opcional)" />',
            '<textarea           id="cl-mensaje"  placeholder="¿Sobre qué necesitas ayuda? (opcional)"></textarea>',
            '<button class="sitec-chat-form-submit" id="cl-submit">Enviar mensaje →</button>',
            '<div class="sitec-chat-form-error" id="cl-error" style="display:none"></div>',
        ].join('');

        document.getElementById('cl-submit').addEventListener('click', submitLead);
        scrollToBottom();
    }

    function submitLead() {
        var nombre   = (document.getElementById('cl-nombre').value   || '').trim();
        var email    = (document.getElementById('cl-email').value    || '').trim();
        var telefono = (document.getElementById('cl-telefono').value || '').trim();
        var mensaje  = (document.getElementById('cl-mensaje').value  || '').trim();
        var errEl    = document.getElementById('cl-error');
        var submitEl = document.getElementById('cl-submit');

        if (!nombre || !email) {
            errEl.textContent = 'Por favor completa nombre y email.';
            errEl.style.display = 'block';
            return;
        }
        errEl.style.display = 'none';
        submitEl.disabled = true;
        submitEl.textContent = 'Enviando…';

        var data = new FormData();
        data.append('action',   'sitec_chat_lead');
        data.append('nonce',    NONCE);
        data.append('nombre',   nombre);
        data.append('email',    email);
        data.append('telefono', telefono);
        data.append('mensaje',  mensaje);
        data.append('page_url', window.location.href);

        fetch(AJAX_URL, { method: 'POST', body: data })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                // Eliminar formulario
                var formWrap = document.getElementById('sitec-lead-form');
                if (formWrap && formWrap.closest('.chat-msg')) {
                    formWrap.closest('.chat-msg').remove();
                }
                addUserMessage(nombre + ' — ' + email);
                showNode(res.success ? 'gracias' : 'error');
            })
            .catch(function() {
                showNode('error');
            });
    }

    /* ── Texto libre del footer ────────────────────────────────────────────── */
    var KEYWORD_MAP = [
        { keys: ['servicio','seguridad','camara','red','telecom','wifi','energia','ups','cable'],  next: 'servicios'  },
        { keys: ['caso','proyecto','cliente','exito','guardia','sedena','ine'],                    next: 'casos'      },
        { keys: ['precio','costo','cuanto','presupuesto','cobran','tarifa'],                        next: 'precios'    },
        { keys: ['contacto','asesor','hablar','llamar','persona','humano','agente'],               next: 'lead_form'  },
        { keys: ['hola','buenos','saludos','buenas'],                                              next: 'welcome'    },
    ];

    function onTextSend() {
        var input = document.getElementById('sitec-chat-text');
        var text  = (input.value || '').trim();
        if (!text) { return; }
        input.value = '';

        addUserMessage(text);

        var lower   = text.toLowerCase();
        var matched = null;
        for (var i = 0; i < KEYWORD_MAP.length; i++) {
            for (var j = 0; j < KEYWORD_MAP[i].keys.length; j++) {
                if (lower.indexOf(KEYWORD_MAP[i].keys[j]) !== -1) {
                    matched = KEYWORD_MAP[i].next;
                    break;
                }
            }
            if (matched) { break; }
        }

        if (matched) {
            setTimeout(function() { showNode(matched); }, 300);
        } else {
            // Respuesta genérica
            setTimeout(function() {
                addBotMessage('No encontré una respuesta exacta, pero puedo conectarte con un asesor que resolverá tu duda. 😊');
                setTimeout(function() {
                    addQuickReplies([
                        { label: '📞 Hablar con asesor', next: 'lead_form' },
                        { label: '← Menú principal',     next: 'welcome'   },
                    ]);
                }, 200);
            }, 600);
        }
    }

    /* ── Utilidades ────────────────────────────────────────────────────────── */
    function scrollToBottom() {
        var c = getContainer();
        if (c) { c.scrollTop = c.scrollHeight; }
    }
    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    function formatText(str) {
        // Convierte \n en <br> y respeta <strong>
        return str.replace(/\n/g, '<br>');
    }

    /* ── Saludo proactivo después de 5s ─────────────────────────────────────── */
    function proactiveGreet() {
        if (hasGreeted) { return; }
        var pulse = document.querySelector('.sitec-chat-pulse');
        if (pulse) { pulse.style.display = 'block'; }
    }

    /* ── Init ───────────────────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function() {
        buildWidget();
        setTimeout(proactiveGreet, 5000);
    });

})();
