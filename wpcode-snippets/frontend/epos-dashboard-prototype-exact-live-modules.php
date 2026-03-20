<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_dashboard_prototype_exact_render')) {
    function epos_dashboard_prototype_exact_render() {
        $rest_nonce = wp_create_nonce('wp_rest');
        $rest_base  = esc_url_raw(rest_url('ep/v1/'));

        ob_start();
        ?>
        <div id="epos-dashboard-prototype-exact-wrapper"
             data-rest-base="<?php echo esc_attr($rest_base); ?>"
             data-rest-nonce="<?php echo esc_attr($rest_nonce); ?>">
        </div>

        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        zinc: {
                            50: '#fafafa',
                            100: '#f4f4f5',
                            200: '#e4e4e7',
                            300: '#d4d4d8',
                            400: '#a1a1aa',
                            500: '#71717a',
                            600: '#52525b',
                            700: '#3f3f46',
                            800: '#27272a',
                            900: '#18181b',
                            950: '#09090b'
                        }
                    }
                }
            }
        };
        </script>

        <style>
            #epos-dashboard-prototype-exact-wrapper * { box-sizing: border-box; }
            #epos-dashboard-prototype-exact-wrapper { width:100%; min-height:100vh; background:#0a0a0a; }
            #epos-dashboard-prototype-exact-wrapper ::-webkit-scrollbar { width: 6px; height: 6px; }
            #epos-dashboard-prototype-exact-wrapper ::-webkit-scrollbar-track { background: transparent; }
            #epos-dashboard-prototype-exact-wrapper ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }

            @keyframes eposShine { to { background-position: 200% center; } }

            #epos-dashboard-prototype-exact-wrapper .chrome-gold-text {
                background: linear-gradient(to right, #BF953F 0%, #FCF6BA 25%, #B38728 50%, #FBF5B7 75%, #BF953F 100%);
                background-size: 200% auto;
                animation: eposShine 5s linear infinite;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            #epos-dashboard-prototype-exact-wrapper .chrome-gold-bg-anim {
                background: linear-gradient(to right, #BF953F 0%, #FCF6BA 25%, #B38728 50%, #FBF5B7 75%, #BF953F 100%);
                background-size: 200% auto;
                animation: eposShine 5s linear infinite;
            }

            #epos-dashboard-prototype-exact-wrapper .btn-evo {
                position: relative;
                overflow: hidden;
                background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #BF953F);
                background-size: 200% auto;
                color: #000 !important;
                z-index: 1;
                transition: all 0.4s ease !important;
                cursor: pointer;
                border: none;
            }

            #epos-dashboard-prototype-exact-wrapper .btn-evo::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: linear-gradient(to right, #00c6ff, #0072ff, #4facfe);
                z-index: -1;
                transform: translateX(-101%);
                transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }

            #epos-dashboard-prototype-exact-wrapper .btn-evo:hover::before { transform: translateX(0); }
            #epos-dashboard-prototype-exact-wrapper .btn-evo:hover {
                color: #fff !important;
                box-shadow: 0 0 20px rgba(0, 198, 255, 0.5) !important;
                transform: translateY(-2px);
            }

            #epos-dashboard-prototype-exact-wrapper .epos-nav-link.active {
                background: #18181b;
                border: 1px solid #27272a;
                color: #fff;
                position: relative;
                overflow: hidden;
            }

            #epos-dashboard-prototype-exact-wrapper .epos-nav-link.active::before {
                content: "";
                position: absolute;
                left: 0; top: 0; bottom: 0;
                width: 4px;
                background: linear-gradient(to right, #BF953F 0%, #FCF6BA 25%, #B38728 50%, #FBF5B7 75%, #BF953F 100%);
                background-size: 200% auto;
                animation: eposShine 5s linear infinite;
            }
        </style>

        <script>
        (function () {
            function esc(text) {
                return String(text == null ? '' : text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function mount() {
                var wrapper = document.getElementById('epos-dashboard-prototype-exact-wrapper');
                if (!wrapper) return;

                var restBase = wrapper.getAttribute('data-rest-base');
                var restNonce = wrapper.getAttribute('data-rest-nonce');

                wrapper.innerHTML = `
                <div id="epos-app" class="dark font-sans antialiased overflow-hidden flex h-screen selection:bg-[#BF953F] selection:text-black bg-zinc-50 dark:bg-[#0a0a0a] text-zinc-900 dark:text-zinc-50 transition-colors duration-300">
                    <div class="absolute top-0 left-0 w-full z-[200]">
                        <div class="w-full flex flex-col gap-[1px]"><div class="w-full h-[2px] chrome-gold-bg-anim"></div></div>
                    </div>

                    <aside class="w-64 bg-white dark:bg-black border-r border-zinc-200 dark:border-zinc-800/80 flex flex-col h-full transition-colors duration-300 z-20 relative pt-1 shrink-0">
                        <div class="h-20 flex items-center justify-center border-b border-zinc-200 dark:border-zinc-800/80 transition-colors px-4">
                            <img src="https://evolutionpower.com/wp-content/uploads/2025/10/New_EP_Logo_Black_300x81.jpg" alt="Evolution Power OS" class="w-48 h-auto block dark:hidden object-contain">
                            <img src="https://evolutionpower.com/wp-content/uploads/2025/08/New_EP_Logo_White.webp" alt="Evolution Power OS" class="w-48 h-auto hidden dark:block object-contain">
                        </div>

                        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                            <p class="px-2 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3 mt-2">Main Menu</p>
                            <button type="button" data-view="dashboard" class="epos-nav-link active w-full text-left flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 rounded-xl transition-all duration-200"><i class="fa-solid fa-chart-pie w-6 text-center mr-2"></i><span class="font-medium tracking-wide">Dashboard</span></button>
                            <button type="button" data-view="crm" class="epos-nav-link w-full text-left flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 rounded-xl transition-all duration-200"><i class="fa-solid fa-users w-6 text-center mr-2"></i><span class="font-medium tracking-wide">CRM</span></button>
                            <button type="button" data-view="pipelines" class="epos-nav-link w-full text-left flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 rounded-xl transition-all duration-200"><i class="fa-solid fa-kanban w-6 text-center mr-2"></i><span class="font-medium tracking-wide">Pipelines</span></button>
                            <button type="button" data-view="projects" class="epos-nav-link w-full text-left flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 rounded-xl transition-all duration-200"><i class="fa-solid fa-helmet-safety w-6 text-center mr-2"></i><span class="font-medium tracking-wide">Projects</span></button>
                            <p class="px-2 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3 mt-8">Financials</p>
                            <button type="button" data-view="estimators" class="epos-nav-link w-full text-left flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 rounded-xl transition-all duration-200"><i class="fa-solid fa-calculator w-6 text-center mr-2"></i><span class="font-medium tracking-wide">Estimators</span></button>
                            <button type="button" data-view="contracts" class="epos-nav-link w-full text-left flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 rounded-xl transition-all duration-200"><i class="fa-solid fa-file-signature w-6 text-center mr-2"></i><span class="font-medium tracking-wide">Contracts</span></button>
                            <button type="button" data-view="bundles" class="epos-nav-link w-full text-left flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 rounded-xl transition-all duration-200"><i class="fa-solid fa-boxes-stacked w-6 text-center mr-2"></i><span class="font-medium tracking-wide">Bundles</span></button>
                        </nav>

                        <div class="p-4 border-t border-zinc-200 dark:border-zinc-800/80 bg-white dark:bg-black transition-colors">
                            <button type="button" data-view="settings" class="epos-nav-link w-full text-left flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 rounded-xl transition-all duration-200"><i class="fa-solid fa-gear w-6 text-center mr-2"></i><span class="font-medium tracking-wide">Settings</span></button>
                        </div>
                    </aside>

                    <div class="flex-1 flex flex-col h-full overflow-hidden relative pt-1 bg-zinc-50 dark:bg-[#0a0a0a] transition-colors duration-300">
                        <header class="h-20 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between px-8 z-10 transition-colors duration-300">
                            <div class="flex-1 max-w-xl flex items-center"><div class="relative w-full group"><span class="absolute inset-y-0 left-0 pl-4 flex items-center text-zinc-400 dark:text-zinc-500"><i class="fa-solid fa-magnifying-glass"></i></span><input type="text" placeholder="Search clients, projects, or proposals..." class="w-full pl-11 pr-4 py-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none shadow-inner"></div></div>
                            <div class="flex items-center space-x-6"><button id="epos-theme-toggle-exact" class="text-zinc-400 hover:text-[#BF953F] dark:text-zinc-500 dark:hover:text-[#BF953F] transition-colors focus:outline-none"><i class="fa-solid fa-moon text-xl hidden dark:block"></i><i class="fa-solid fa-sun text-xl block dark:hidden"></i></button><button class="btn-evo flex items-center gap-2 px-6 py-2.5 text-xs font-bold uppercase tracking-widest rounded-xl"><i class="fa-solid fa-plus"></i><span class="relative z-10">Quick Add</span></button><div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 transition-colors"></div><button class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-900 dark:hover:text-white relative transition-colors"><i class="fa-regular fa-bell text-xl"></i><span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-4 w-4"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#00c6ff] opacity-75"></span><span id="epos-exact-notification-count" class="relative inline-flex rounded-full h-4 w-4 bg-[#00c6ff] text-[9px] text-black justify-center items-center font-black">0</span></span></button></div>
                        </header>
                        <main id="epos-main-content" class="flex-1 overflow-x-hidden overflow-y-auto p-8 relative"></main>
                    </div>
                </div>`;

                var app = document.getElementById('epos-app');
                var main = document.getElementById('epos-main-content');
                var navLinks = wrapper.querySelectorAll('.epos-nav-link');
                var toggle = document.getElementById('epos-theme-toggle-exact');

                if (localStorage.getItem('theme') === 'light') {
                    app.classList.remove('dark');
                } else {
                    app.classList.add('dark');
                }

                if (toggle) {
                    toggle.addEventListener('click', function () {
                        app.classList.toggle('dark');
                        localStorage.setItem('theme', app.classList.contains('dark') ? 'dark' : 'light');
                    });
                }

                async function getJson(endpoint) {
                    var response = await fetch(restBase + endpoint, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restNonce
                        }
                    });
                    if (!response.ok) throw new Error('Request failed: ' + response.status);
                    return response.json();
                }

                function getItems(result) {
                    return result && result.data && Array.isArray(result.data.items) ? result.data.items : [];
                }

                function getTotal(result) {
                    return result && result.data && result.data.pagination ? parseInt(result.data.pagination.total || 0, 10) : 0;
                }

                function setActive(view) {
                    navLinks.forEach(function (btn) {
                        btn.classList.remove('active');
                        if (btn.getAttribute('data-view') === view) btn.classList.add('active');
                    });
                }

                function renderShell(title, body) {
                    main.innerHTML = `
                        <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#BF953F] opacity-[0.05] dark:opacity-[0.03] blur-[100px] rounded-full pointer-events-none"></div>
                        <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#00c6ff] opacity-[0.05] dark:opacity-[0.03] blur-[100px] rounded-full pointer-events-none"></div>
                        <div class="mb-8 relative z-10"><p class="chrome-gold-text uppercase tracking-widest text-xs font-bold mb-2 font-mono">01 // MODULE</p><h1 class="text-3xl md:text-4xl font-black tracking-tighter text-zinc-900 dark:text-white">${esc(title)}</h1></div>
                        ${body}`;
                }

                async function renderDashboard() {
                    renderShell('Dashboard', `
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 relative z-10">
                            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-xl"><p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mb-4">Total Revenue (MTD)</p><h3 class="text-3xl md:text-4xl font-black chrome-gold-text tracking-tighter pb-1">$1.24M</h3></div>
                            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-xl"><p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mb-4">Active Projects</p><h3 id="kpi-projects" class="text-3xl md:text-4xl font-black text-zinc-900 dark:text-white tracking-tighter pb-1">0</h3></div>
                            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-xl"><p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mb-4">Pending Signatures</p><h3 id="kpi-signatures" class="text-3xl md:text-4xl font-black text-zinc-900 dark:text-white tracking-tighter pb-1">0</h3></div>
                            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-xl"><p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mb-4">New Leads</p><h3 id="kpi-leads" class="text-3xl md:text-4xl font-black text-zinc-900 dark:text-white tracking-tighter pb-1">0</h3></div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-10">
                            <div class="lg:col-span-2 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl p-6"><h2 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight mb-6">Pipeline Snapshot</h2><div id="pipeline-list" class="space-y-3 text-zinc-500 dark:text-zinc-400">Loading...</div></div>
                            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl p-6"><h2 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight mb-6">My Tasks</h2><div id="task-list" class="space-y-3 text-zinc-500 dark:text-zinc-400">Loading...</div></div>
                        </div>`);

                    try {
                        var results = await Promise.all([getJson('projects'), getJson('leads'), getJson('document-signatures'), getJson('opportunities'), getJson('project-tasks'), getJson('notifications')]);
                        document.getElementById('kpi-projects').textContent = String(getTotal(results[0]));
                        document.getElementById('kpi-leads').textContent = String(getTotal(results[1]));
                        document.getElementById('kpi-signatures').textContent = String(getTotal(results[2]));
                        document.getElementById('epos-exact-notification-count').textContent = String(Math.min(getTotal(results[5]), 99));

                        var opps = getItems(results[3]).slice(0, 5);
                        var tasks = getItems(results[4]).slice(0, 5);
                        document.getElementById('pipeline-list').innerHTML = opps.length ? opps.map(function (item) { return '<div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800"><div class="font-bold text-zinc-900 dark:text-white">' + esc(item.title || 'Untitled') + '</div><div class="text-sm">' + esc(item.pipeline_stage || item.status || '') + ' · $' + esc(item.estimated_value || '0.00') + '</div></div>'; }).join('') : 'No records found.';
                        document.getElementById('task-list').innerHTML = tasks.length ? tasks.map(function (item) { return '<div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800"><div class="font-bold text-zinc-900 dark:text-white">' + esc(item.title || 'Untitled') + '</div><div class="text-sm">' + esc(item.description || item.status || '') + '</div></div>'; }).join('') : 'No tasks found.';
                    } catch (e) {
                        document.getElementById('pipeline-list').innerHTML = '<div class="text-red-400">' + esc(e.message) + '</div>';
                        document.getElementById('task-list').innerHTML = '<div class="text-red-400">' + esc(e.message) + '</div>';
                    }
                }

                async function renderList(title, endpoint, mapper) {
                    renderShell(title, '<div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl p-6"><h2 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight mb-6">' + esc(title) + '</h2><div id="module-list" class="space-y-3 text-zinc-500 dark:text-zinc-400">Loading...</div></div>');
                    try {
                        var result = await getJson(endpoint);
                        var rows = getItems(result).slice(0, 20);
                        document.getElementById('module-list').innerHTML = rows.length ? rows.map(mapper).join('') : 'No records found.';
                    } catch (e) {
                        document.getElementById('module-list').innerHTML = '<div class="text-red-400">' + esc(e.message) + '</div>';
                    }
                }

                function route(view) {
                    setActive(view);
                    if (view === 'dashboard') return renderDashboard();
                    if (view === 'crm') return renderList('CRM', 'clients', function (item) { return '<div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800"><div class="font-bold text-zinc-900 dark:text-white">' + esc(item.full_name || 'Client') + '</div><div class="text-sm">' + esc(item.email || '') + ' · ' + esc(item.status || '') + '</div></div>'; });
                    if (view === 'pipelines') return renderList('Pipelines', 'opportunities', function (item) { return '<div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800"><div class="font-bold text-zinc-900 dark:text-white">' + esc(item.title || 'Untitled') + '</div><div class="text-sm">' + esc(item.pipeline_stage || item.status || '') + ' · $' + esc(item.estimated_value || '0.00') + '</div></div>'; });
                    if (view === 'projects') return renderList('Projects', 'projects', function (item) { return '<div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800"><div class="font-bold text-zinc-900 dark:text-white">' + esc(item.title || 'Untitled') + '</div><div class="text-sm">' + esc(item.status || '') + '</div></div>'; });
                    if (view === 'estimators') return renderList('Estimators', 'estimators', function (item) { return '<div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800"><div class="font-bold text-zinc-900 dark:text-white">' + esc(item.address || 'Estimator') + '</div><div class="text-sm">$' + esc(item.total_estimated_price || '0.00') + '</div></div>'; });
                    if (view === 'contracts') return renderList('Contracts', 'contracts', function (item) { return '<div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800"><div class="font-bold text-zinc-900 dark:text-white">' + esc(item.contract_number || 'Contract') + '</div><div class="text-sm">' + esc(item.status || '') + ' · $' + esc(item.contract_amount || '0.00') + '</div></div>'; });
                    renderShell(view, '<div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl p-6 text-zinc-500 dark:text-zinc-400">Module shell ready.</div>');
                }

                navLinks.forEach(function (btn) {
                    btn.addEventListener('click', function () { route(btn.getAttribute('data-view')); });
                });

                route('dashboard');
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', mount);
            } else {
                mount();
            }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('epos_dashboard_prototype_exact_register')) {
    function epos_dashboard_prototype_exact_register() {
        add_shortcode('epos_dashboard_prototype_exact', 'epos_dashboard_prototype_exact_render');
    }
}
add_action('init', 'epos_dashboard_prototype_exact_register');
