<?php
/**
 * EPOS Dashboard Shortcode - Prototype Exact Mount
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_dashboard_prototype_exact_render')) {
    function epos_dashboard_prototype_exact_render() {
        ob_start();
        ?>
        <div id="epos-dashboard-prototype-exact-wrapper"></div>
        <script>
        (function () {
            var root = document.getElementById('epos-dashboard-prototype-exact-wrapper');
            if (!root) return;

            if (!document.getElementById('epos-tailwind-cdn')) {
                var tailwindScript = document.createElement('script');
                tailwindScript.id = 'epos-tailwind-cdn';
                tailwindScript.src = 'https://cdn.tailwindcss.com';
                document.head.appendChild(tailwindScript);
            }

            if (!document.getElementById('epos-fontawesome-cdn')) {
                var fontAwesomeLink = document.createElement('link');
                fontAwesomeLink.id = 'epos-fontawesome-cdn';
                fontAwesomeLink.rel = 'stylesheet';
                fontAwesomeLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
                document.head.appendChild(fontAwesomeLink);
            }

            function mountDashboard() {
                if (typeof tailwind === 'undefined') {
                    setTimeout(mountDashboard, 100);
                    return;
                }

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

                if (!document.getElementById('epos-dashboard-prototype-exact-styles')) {
                    var style = document.createElement('style');
                    style.id = 'epos-dashboard-prototype-exact-styles';
                    style.textContent = "\
#epos-dashboard-prototype-exact-wrapper ::-webkit-scrollbar { width: 6px; height: 6px; }\
#epos-dashboard-prototype-exact-wrapper ::-webkit-scrollbar-track { background: transparent; }\
#epos-dashboard-prototype-exact-wrapper ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }\
#epos-dashboard-prototype-exact-wrapper ::-webkit-scrollbar-thumb:hover { background: #52525b; }\
@keyframes eposShine { to { background-position: 200% center; } }\
#epos-dashboard-prototype-exact-wrapper .chrome-gold-text { background: linear-gradient(to right, #BF953F 0%, #FCF6BA 25%, #B38728 50%, #FBF5B7 75%, #BF953F 100%); background-size: 200% auto; animation: eposShine 5s linear infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }\
#epos-dashboard-prototype-exact-wrapper .chrome-gold-bg-anim { background: linear-gradient(to right, #BF953F 0%, #FCF6BA 25%, #B38728 50%, #FBF5B7 75%, #BF953F 100%); background-size: 200% auto; animation: eposShine 5s linear infinite; }\
#epos-dashboard-prototype-exact-wrapper .btn-evo { position: relative; overflow: hidden; background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #BF953F); background-size: 200% auto; color: #000000 !important; z-index: 1; transition: all 0.4s ease !important; cursor: pointer; border: none; }\
#epos-dashboard-prototype-exact-wrapper .btn-evo::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to right, #00c6ff, #0072ff, #4facfe); z-index: -1; transform: translateX(-101%); transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }\
#epos-dashboard-prototype-exact-wrapper .btn-evo:hover::before { transform: translateX(0); }\
#epos-dashboard-prototype-exact-wrapper .btn-evo:hover { color: #ffffff !important; box-shadow: 0 0 20px rgba(0, 198, 255, 0.5) !important; transform: translateY(-2px); }\
#epos-dashboard-prototype-exact-wrapper .evo-table th { letter-spacing: 0.1em; }\
#epos-dashboard-prototype-exact-wrapper .evo-table tr { transition: background-color 0.2s ease; }";
                    document.head.appendChild(style);
                }

                root.innerHTML = `
<div class="dark">
<body class="font-sans antialiased overflow-hidden flex h-screen selection:bg-[#BF953F] selection:text-black bg-zinc-50 dark:bg-[#0a0a0a] text-zinc-900 dark:text-fafafa transition-colors duration-300">
    <div class="absolute top-0 left-0 w-full z-[200]">
        <div class="w-full flex flex-col gap-[1px]">
            <div class="w-full h-[2px] chrome-gold-bg-anim"></div>
        </div>
    </div>
    <aside class="w-64 bg-white dark:bg-black border-r border-zinc-200 dark:border-zinc-800/80 flex flex-col h-full transition-colors duration-300 z-20 relative pt-1">
        <div class="h-20 flex items-center justify-center border-b border-zinc-200 dark:border-zinc-800/80 transition-colors px-4">
            <img src="https://evolutionpower.com/wp-content/uploads/2025/10/New_EP_Logo_Black_300x81.jpg" alt="Evolution Power OS" class="w-48 h-auto block dark:hidden object-contain">
            <img src="https://evolutionpower.com/wp-content/uploads/2025/08/New_EP_Logo_White.webp" alt="Evolution Power OS" class="w-48 h-auto hidden dark:block object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]">
        </div>
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <p class="px-2 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3 mt-2">Main Menu</p>
            <a href="#" class="flex items-center px-3 py-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl group relative overflow-hidden transition-colors">
                <div class="absolute left-0 top-0 bottom-0 w-1 chrome-gold-bg-anim"></div>
                <i class="fa-solid fa-chart-pie w-6 text-center mr-2 chrome-gold-text"></i>
                <span class="font-bold tracking-wide text-zinc-900 dark:text-white">Dashboard</span>
            </a>
            <a href="#" class="flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-white rounded-xl group transition-all duration-200">
                <i class="fa-solid fa-users w-6 text-center mr-2 group-hover:text-[#00c6ff] transition-colors"></i>
                <span class="font-medium tracking-wide">CRM</span>
            </a>
            <a href="#" class="flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-white rounded-xl group transition-all duration-200">
                <i class="fa-solid fa-kanban w-6 text-center mr-2 group-hover:text-[#00c6ff] transition-colors"></i>
                <span class="font-medium tracking-wide">Pipelines</span>
            </a>
            <a href="#" class="flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-white rounded-xl group transition-all duration-200">
                <i class="fa-solid fa-helmet-safety w-6 text-center mr-2 group-hover:text-[#00c6ff] transition-colors"></i>
                <span class="font-medium tracking-wide">Projects</span>
            </a>
            <p class="px-2 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3 mt-8">Financials</p>
            <a href="#" class="flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-white rounded-xl group transition-all duration-200">
                <i class="fa-solid fa-calculator w-6 text-center mr-2 group-hover:text-[#BF953F] transition-colors"></i>
                <span class="font-medium tracking-wide">Estimators</span>
            </a>
            <a href="#" class="flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-white rounded-xl group transition-all duration-200">
                <i class="fa-solid fa-file-signature w-6 text-center mr-2 group-hover:text-[#BF953F] transition-colors"></i>
                <span class="font-medium tracking-wide">Contracts</span>
            </a>
            <a href="#" class="flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-white rounded-xl group transition-all duration-200">
                <i class="fa-solid fa-boxes-stacked w-6 text-center mr-2 group-hover:text-[#BF953F] transition-colors"></i>
                <span class="font-medium tracking-wide">Bundles</span>
            </a>
        </nav>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-800/80 bg-white dark:bg-black transition-colors">
            <a href="#" class="flex items-center px-3 py-3 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-zinc-900 dark:hover:text-white rounded-xl group transition-all duration-200">
                <i class="fa-solid fa-gear w-6 text-center mr-2 group-hover:text-zinc-900 dark:group-hover:text-white"></i>
                <span class="font-medium tracking-wide">Settings</span>
            </a>
        </div>
    </aside>
    <div class="flex-1 flex flex-col h-full overflow-hidden relative pt-1 bg-zinc-50 dark:bg-[#0a0a0a] transition-colors duration-300">
        <header class="h-20 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between px-8 z-10 transition-colors duration-300">
            <div class="flex-1 max-w-xl flex items-center">
                <div class="relative w-full group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-zinc-400 dark:text-zinc-500 group-focus-within:text-[#BF953F] transition-colors"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" placeholder="Search clients, projects, or proposals..." class="w-full pl-11 pr-4 py-3 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:border-[#BF953F]/50 focus:bg-white dark:focus:bg-black focus:ring-1 focus:ring-[#BF953F]/50 transition-all shadow-inner">
                </div>
            </div>
            <div class="flex items-center space-x-6">
                <button id="epos-theme-toggle-exact" class="text-zinc-400 hover:text-[#BF953F] dark:text-zinc-500 dark:hover:text-[#BF953F] transition-colors focus:outline-none" title="Toggle Light/Dark Mode">
                    <i class="fa-solid fa-moon text-xl hidden dark:block"></i>
                    <i class="fa-solid fa-sun text-xl block dark:hidden"></i>
                </button>
                <button class="btn-evo flex items-center gap-2 px-6 py-2.5 text-xs font-bold uppercase tracking-widest shadow-[0_4px_15px_rgba(191,149,63,0.15)] rounded-xl">
                    <i class="fa-solid fa-plus"></i>
                    <span class="relative z-10">Quick Add</span>
                </button>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 transition-colors"></div>
                <button class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-900 dark:hover:text-white relative transition-colors">
                    <i class="fa-regular fa-bell text-xl"></i>
                    <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#00c6ff] opacity-75"></span>
                        <span id="epos-exact-notification-count" class="relative inline-flex rounded-full h-4 w-4 bg-[#00c6ff] text-[9px] text-black justify-center items-center font-black">3</span>
                    </span>
                </button>
                <button class="flex items-center space-x-3 focus:outline-none pl-2 border-l border-transparent hover:border-zinc-200 dark:hover:border-zinc-800 transition-colors">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=BF953F&color=000&bold=true" alt="User Avatar" class="h-9 w-9 rounded-full border border-zinc-200 dark:border-zinc-700">
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-bold text-zinc-900 dark:text-white leading-tight tracking-wide">Admin User</p>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-widest leading-tight mt-0.5">SuperAdmin</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-zinc-400 dark:text-zinc-600"></i>
                </button>
            </div>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 relative">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#BF953F] opacity-[0.05] dark:opacity-[0.03] blur-[100px] rounded-full pointer-events-none transition-opacity"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#00c6ff] opacity-[0.05] dark:opacity-[0.03] blur-[100px] rounded-full pointer-events-none transition-opacity"></div>
            <div class="mb-8 relative z-10">
                <p class="chrome-gold-text uppercase tracking-widest text-xs font-bold mb-2 font-mono">01 // OVERVIEW</p>
                <h1 class="text-3xl md:text-4xl font-black tracking-tighter text-zinc-900 dark:text-white transition-colors">Dashboard <span class="italic font-light text-zinc-500 dark:text-zinc-400">Activity</span></h1>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 relative z-10">
                <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-xl relative overflow-hidden group transition-colors duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#BF953F]/10 dark:from-[#BF953F]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10"><div class="flex items-center justify-between mb-4"><p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Total Revenue (MTD)</p><div class="p-2.5 bg-[#BF953F]/10 border border-[#BF953F]/20 text-[#BF953F] rounded-xl"><i class="fa-solid fa-dollar-sign w-4 h-4 flex items-center justify-center"></i></div></div><div><h3 id="epos-exact-kpi-revenue" class="text-3xl md:text-4xl font-black chrome-gold-text tracking-tighter pb-1">$1.24M</h3><p class="text-xs text-[#0092bc] dark:text-[#00c6ff] mt-2 flex items-center font-bold tracking-wide"><i class="fa-solid fa-arrow-trend-up mr-1.5"></i> +12.5% <span class="text-zinc-400 dark:text-zinc-500 ml-2 font-medium">vs last month</span></p></div></div>
                </div>
                <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-xl relative overflow-hidden group transition-colors duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-zinc-200/50 dark:from-zinc-800/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10"><div class="flex items-center justify-between mb-4"><p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Active Projects</p><div class="p-2.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-white rounded-xl transition-colors"><i class="fa-solid fa-helmet-safety w-4 h-4 flex items-center justify-center"></i></div></div><div><h3 id="epos-exact-kpi-projects" class="text-3xl md:text-4xl font-black text-zinc-900 dark:text-white tracking-tighter pb-1 transition-colors">42</h3><div class="flex gap-2 mt-2"><span class="text-[10px] font-bold px-2.5 py-1 bg-[#BF953F]/10 text-[#BF953F] border border-[#BF953F]/20 rounded-lg uppercase tracking-wider">15 Solar</span><span class="text-[10px] font-bold px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-lg uppercase tracking-wider transition-colors">12 Roofing</span></div></div></div>
                </div>
                <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-xl relative overflow-hidden group transition-colors duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#00c6ff]/10 dark:from-[#00c6ff]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10"><div class="flex items-center justify-between mb-4"><p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Pending Signatures</p><div class="p-2.5 bg-[#00c6ff]/10 border border-[#00c6ff]/20 text-[#0092bc] dark:text-[#00c6ff] rounded-xl"><i class="fa-solid fa-file-signature w-4 h-4 flex items-center justify-center"></i></div></div><div><h3 id="epos-exact-kpi-signatures" class="text-3xl md:text-4xl font-black text-zinc-900 dark:text-white tracking-tighter pb-1 transition-colors">8</h3><p class="text-xs text-zinc-500 mt-2 font-medium tracking-wide">Requires immediate follow-up</p></div></div>
                </div>
                <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-xl relative overflow-hidden group transition-colors duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-zinc-200/50 dark:from-zinc-800/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10"><div class="flex items-center justify-between mb-4"><p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">New Leads (Today)</p><div class="p-2.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-white rounded-xl transition-colors"><i class="fa-solid fa-user-plus w-4 h-4 flex items-center justify-center"></i></div></div><div><h3 id="epos-exact-kpi-leads" class="text-3xl md:text-4xl font-black text-zinc-900 dark:text-white tracking-tighter pb-1 transition-colors">14</h3><p class="text-xs text-zinc-500 mt-2 font-medium tracking-wide">Unassigned: <span class="text-zinc-900 dark:text-white font-bold transition-colors">3</span></p></div></div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-10">
                <div class="lg:col-span-2 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl flex flex-col overflow-hidden transition-colors duration-300">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-900/50 transition-colors">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Pipeline Snapshot</h2>
                        <button class="text-[11px] uppercase tracking-widest text-[#0092bc] dark:text-[#00c6ff] font-bold hover:text-[#007ba0] dark:hover:text-white transition-colors flex items-center gap-1">View Kanban <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left border-collapse evo-table">
                            <thead>
                                <tr class="bg-zinc-100/50 dark:bg-black/40 text-zinc-500 dark:text-zinc-500 text-[10px] uppercase font-bold border-b border-zinc-200 dark:border-zinc-800 transition-colors">
                                    <th class="p-5">Client / Project</th>
                                    <th class="p-5">Business Unit</th>
                                    <th class="p-5">Stage</th>
                                    <th class="p-5">Value</th>
                                    <th class="p-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="epos-exact-pipeline-body" class="text-sm divide-y divide-zinc-200 dark:divide-zinc-800/50 transition-colors"></tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl p-6 flex flex-col transition-colors duration-300">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight mb-6 transition-colors">My Tasks</h2>
                    <div id="epos-exact-task-list" class="space-y-5 flex-1"></div>
                    <button class="w-full mt-6 py-3 border border-zinc-200 dark:border-zinc-700 rounded-xl text-[11px] uppercase tracking-widest font-bold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all">View All Tasks</button>
                </div>
            </div>
        </main>
    </div>
</body>
</div>`;

                var themeToggleBtn = document.getElementById('epos-theme-toggle-exact');
                var htmlContainer = root.querySelector('.dark');
                if (localStorage.getItem('theme') === 'light') {
                    htmlContainer.classList.remove('dark');
                } else {
                    htmlContainer.classList.add('dark');
                }
                if (themeToggleBtn) {
                    themeToggleBtn.addEventListener('click', function () {
                        htmlContainer.classList.toggle('dark');
                        if (htmlContainer.classList.contains('dark')) {
                            localStorage.setItem('theme', 'dark');
                        } else {
                            localStorage.setItem('theme', 'light');
                        }
                    });
                }

                loadLiveData();
            }

            function esc(text) {
                return String(text == null ? '' : text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            async function getJson(url) {
                var response = await fetch(url, {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (!response.ok) throw new Error('Request failed: ' + response.status);
                return response.json();
            }

            function items(result) {
                return result && result.data && result.data.items ? result.data.items : [];
            }

            function total(result) {
                return result && result.data && result.data.pagination ? result.data.pagination.total : 0;
            }

            async function loadLiveData() {
                try {
                    var data = await Promise.all([
                        getJson('/wp-json/ep/v1/projects'),
                        getJson('/wp-json/ep/v1/leads'),
                        getJson('/wp-json/ep/v1/document-signatures'),
                        getJson('/wp-json/ep/v1/opportunities'),
                        getJson('/wp-json/ep/v1/project-tasks'),
                        getJson('/wp-json/ep/v1/notifications')
                    ]);

                    var projects = data[0], leads = data[1], signatures = data[2], opportunities = data[3], tasks = data[4], notifications = data[5];
                    var projectCount = total(projects);
                    var leadCount = total(leads);
                    var signatureCount = total(signatures);
                    var notificationCount = total(notifications);

                    var projectsEl = document.getElementById('epos-exact-kpi-projects');
                    var leadsEl = document.getElementById('epos-exact-kpi-leads');
                    var signaturesEl = document.getElementById('epos-exact-kpi-signatures');
                    var notifEl = document.getElementById('epos-exact-notification-count');

                    if (projectsEl) projectsEl.textContent = String(projectCount);
                    if (leadsEl) leadsEl.textContent = String(leadCount);
                    if (signaturesEl) signaturesEl.textContent = String(signatureCount);
                    if (notifEl) notifEl.textContent = String(notificationCount > 99 ? 99 : notificationCount);

                    var pipelineBody = document.getElementById('epos-exact-pipeline-body');
                    var opportunityItems = items(opportunities).slice(0, 5);
                    if (pipelineBody) {
                        if (!opportunityItems.length) {
                            pipelineBody.innerHTML = '<tr><td class="p-5" colspan="5">No records found.</td></tr>';
                        } else {
                            pipelineBody.innerHTML = opportunityItems.map(function (item) {
                                return '<tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">'
                                    + '<td class="p-5"><p class="font-bold text-zinc-900 dark:text-white tracking-wide">' + esc(item.title || 'Untitled') + '</p><p class="text-xs text-zinc-500 mt-0.5">ID #' + esc(item.id || '') + '</p></td>'
                                    + '<td class="p-5"><span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-[#BF953F]/30 bg-[#BF953F]/10 text-[#a37b2d] dark:text-[#BF953F]">' + esc(item.business_id || 'N/A') + '</span></td>'
                                    + '<td class="p-5"><span class="flex items-center text-zinc-600 dark:text-zinc-300 font-medium text-xs tracking-wide transition-colors"><span class="h-2 w-2 rounded-full bg-[#00c6ff] mr-2 shadow-[0_0_8px_rgba(0,198,255,0.5)]"></span>' + esc(item.pipeline_stage || item.status || 'n/a') + '</span></td>'
                                    + '<td class="p-5 font-bold text-zinc-900 dark:text-white">$' + esc(item.estimated_value || '0.00') + '</td>'
                                    + '<td class="p-5 text-right"><button class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors"><i class="fa-solid fa-ellipsis-vertical px-2"></i></button></td>'
                                    + '</tr>';
                            }).join('');
                        }
                    }

                    var taskList = document.getElementById('epos-exact-task-list');
                    var taskItems = items(tasks).slice(0, 5);
                    if (taskList) {
                        if (!taskItems.length) {
                            taskList.innerHTML = '<div class="text-sm text-zinc-500">No tasks found.</div>';
                        } else {
                            taskList.innerHTML = taskItems.map(function (item) {
                                return '<div class="flex items-start group">'
                                    + '<div class="flex-shrink-0 mt-0.5"><input type="checkbox" class="h-4 w-4 appearance-none rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800"></div>'
                                    + '<div class="ml-3"><p class="text-sm font-bold text-zinc-900 dark:text-white tracking-wide group-hover:text-[#BF953F] dark:group-hover:text-[#BF953F] transition-colors">' + esc(item.title || 'Untitled') + '</p><p class="text-xs text-zinc-500 mt-0.5">' + esc(item.description || item.status || '') + '</p></div>'
                                    + '</div>';
                            }).join('');
                        }
                    }
                } catch (error) {
                    var pipelineBodyError = document.getElementById('epos-exact-pipeline-body');
                    var taskListError = document.getElementById('epos-exact-task-list');
                    if (pipelineBodyError) pipelineBodyError.innerHTML = '<tr><td class="p-5" colspan="5">Error loading records.</td></tr>';
                    if (taskListError) taskListError.innerHTML = '<div class="text-sm text-red-400">' + esc(error.message || 'Unknown error') + '</div>';
                }
            }

            mountDashboard();
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
